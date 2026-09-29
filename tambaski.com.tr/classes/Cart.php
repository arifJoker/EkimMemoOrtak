<?php
/**
 * Sepet Yönetimi ve Matbaa Sipariş Tasarım Bağlayıcı Sınıfı
 */
class Cart {
    private $db;
    private $sessionId;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->sessionId = session_id();
    }

    /**
     * Sepete Ürün & Tasarım Ekle
     */
    public function add($productId, $quantity, $options = [], $customSize = null, $designData = [], $selectedPackage = 'standart', $customPaperId = 0) {
        $productModel = new Product();
        $calc = $productModel->calculatePrice(
            $productId, 
            $quantity, 
            $options, 
            $customSize['width'] ?? 0, 
            $customSize['height'] ?? 0, 
            ($designData['type'] ?? '') === 'design_request',
            $selectedPackage,
            $customPaperId
        );

        if (!$calc['success']) {
            return $calc;
        }

        $userId = Auth::id();
        $designType = $designData['type'] ?? 'none';
        $designFile = $designData['file'] ?? null;
        $designSvg = $designData['svg'] ?? null;
        $designPreview = $designData['preview'] ?? null;
        $designNotes = $designData['notes'] ?? null;
        $totalPrice = $calc['grand_total'] ?? $calc['total'] ?? $calc['total_amount'] ?? (($calc['unit_price'] ?? 0) * ($calc['quantity'] ?? 1));

        $stmt = $this->db->prepare("INSERT INTO cart_items (
            session_id, user_id, product_id, quantity, selected_options, custom_size,
            design_type, design_file, design_svg, design_preview, design_notes,
            unit_price, total_price
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $success = $stmt->execute([
            $this->sessionId,
            $userId,
            $productId,
            $calc['quantity'],
            !empty($options) ? json_encode($options) : null,
            !empty($customSize) ? json_encode($customSize) : null,
            $designType,
            $designFile,
            $designSvg,
            $designPreview,
            $designNotes,
            $calc['unit_price'],
            $totalPrice
        ]);

        if ($success) {
            return ['success' => true, 'cart_item_id' => $this->db->lastInsertId(), 'cart_count' => $this->count()];
        }

        return ['success' => false, 'error' => 'Ürün sepete eklenemedi.'];
    }

    /**
     * Sepet Kaleminin Adedini Güncelle
     */
    public function updateQuantity($itemId, $newQuantity) {
        $stmt = $this->db->prepare("SELECT * FROM cart_items WHERE id = ? AND (session_id = ? OR (user_id IS NOT NULL AND user_id = ?))");
        $stmt->execute([$itemId, $this->sessionId, Auth::id()]);
        $item = $stmt->fetch();
        if (!$item) return false;

        $options = !empty($item['selected_options']) ? json_decode($item['selected_options'], true) : [];
        $customSize = !empty($item['custom_size']) ? json_decode($item['custom_size'], true) : null;
        $selectedPkg = $options['selected_package'] ?? 'standart';

        $productModel = new Product();
        $calc = $productModel->calculatePrice(
            $item['product_id'],
            $newQuantity,
            $options,
            $customSize['width'] ?? 0,
            $customSize['height'] ?? 0,
            $item['design_type'] === 'design_request',
            $selectedPkg
        );

        if (!$calc['success']) return false;

        $itemTotal = $calc['grand_total'] ?? $calc['total'] ?? $calc['total_amount'] ?? ($calc['unit_price'] * $calc['quantity']);

        $upStmt = $this->db->prepare("UPDATE cart_items SET quantity = ?, unit_price = ?, total_price = ? WHERE id = ?");
        return $upStmt->execute([
            $calc['quantity'],
            $calc['unit_price'],
            $itemTotal,
            $itemId
        ]);
    }

    /**
     * Sepetten Ürün Sil
     */
    public function remove($itemId) {
        $stmt = $this->db->prepare("DELETE FROM cart_items WHERE id = ? AND (session_id = ? OR (user_id IS NOT NULL AND user_id = ?))");
        return $stmt->execute([$itemId, $this->sessionId, Auth::id()]);
    }

    /**
     * Sepeti Tamamen Temizle
     */
    public function clear() {
        $stmt = $this->db->prepare("DELETE FROM cart_items WHERE session_id = ? OR (user_id IS NOT NULL AND user_id = ?)");
        return $stmt->execute([$this->sessionId, Auth::id()]);
    }

    /**
     * Sepetteki Ürün Sayısı
     */
    public function count() {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM cart_items WHERE session_id = ? OR (user_id IS NOT NULL AND user_id = ?)");
        $stmt->execute([$this->sessionId, Auth::id()]);
        $res = $stmt->fetch();
        return (int)($res['total'] ?? 0);
    }

    /**
     * Sepet Kalemlerini ve Toplam Hesaplamaları Getirir
     */
    public function getSummary() {
        $stmt = $this->db->prepare("SELECT ci.*, p.name AS product_name, p.slug AS product_slug, p.featured_image, p.tax_rate 
                                    FROM cart_items ci 
                                    JOIN products p ON ci.product_id = p.id 
                                    WHERE ci.session_id = ? OR (ci.user_id IS NOT NULL AND ci.user_id = ?) 
                                    ORDER BY ci.id DESC");
        $stmt->execute([$this->sessionId, Auth::id()]);
        $rawItems = $stmt->fetchAll();

        $items = [];
        $subtotal = 0.00;
        $totalTax = 0.00;
        $productModel = new Product();

        foreach ($rawItems as $item) {
            $options = !empty($item['selected_options']) ? json_decode($item['selected_options'], true) : [];
            $customSize = !empty($item['custom_size']) ? json_decode($item['custom_size'], true) : null;

            // Seçenek isimlerini çözümle (Hem ID bazlı matbaa özellikleri hem de paket / kalınlık / montaj)
            $optionLabels = [];
            if (!empty($options)) {
                foreach ($options as $attrId => $valId) {
                    if (is_numeric($attrId)) {
                        $optStmt = $this->db->prepare("SELECT pav.title, pa.name AS attr_name FROM product_attribute_values pav JOIN product_attributes pa ON pav.attribute_id = pa.id WHERE pav.id = ?");
                        $optStmt->execute([$valId]);
                        $opt = $optStmt->fetch();
                        if ($opt) {
                            $optionLabels[] = $opt['attr_name'] . ': ' . $opt['title'];
                        }
                    } else {
                        if ($attrId === 'selected_package' || $attrId === 'package') {
                            $pkgNames = [
                                'kucuk' => 'Küçük Boy (25x35 cm)',
                                'orta' => 'Orta Boy (35x50 cm)',
                                'buyuk' => 'Büyük Boy (50x70 cm)',
                                'mega' => 'Mega Boy (70x100 cm)',
                                'ozel' => 'Özel Ölçü',
                                'ekonomik' => 'Ekonomik Paket',
                                'standart' => 'Standart Paket',
                                'premium' => 'Premium Paket',
                                'vip' => 'VIP Prestij Paket'
                            ];
                            $optionLabels[] = 'Paket: ' . ($pkgNames[$valId] ?? ucfirst($valId));
                        } elseif ($attrId === 'thickness') {
                            $thicknessNames = ['3mm' => '3 mm Sert Dekota', '5mm' => '5 mm Sert Dekota', '9mm' => '9 mm Sert Dekota'];
                            $optionLabels[] = 'Kalınlık: ' . ($thicknessNames[$valId] ?? $valId);
                        } elseif ($attrId === 'mounting') {
                            $mountingNames = ['none' => 'Montajsız', 'tape' => 'Çift Taraflı Köpük Bantlı', 'holes' => '4 Köşeden Delikli'];
                            $optionLabels[] = 'Montaj: ' . ($mountingNames[$valId] ?? $valId);
                        } elseif (is_string($valId) && !empty($valId)) {
                            $optionLabels[] = ucfirst($attrId) . ': ' . $valId;
                        }
                    }
                }
            }

            if (!empty($customSize) && !empty($customSize['width']) && !empty($customSize['height'])) {
                $optionLabels[] = 'Özel Boyut: ' . $customSize['width'] . ' x ' . $customSize['height'] . ' cm';
            }

            // Sepet kalemi için bir üst kademe fırsat kontrolü
            $itemUpsell = null;
            $itemPkg = $options['selected_package'] ?? 'standart';
            $calcUpsell = $productModel->calculatePrice(
                $item['product_id'],
                (int)$item['quantity'],
                $options,
                $customSize['width'] ?? 0,
                $customSize['height'] ?? 0,
                $item['design_type'] === 'design_request',
                $itemPkg
            );
            if (!empty($calcUpsell['upsell']) && !empty($calcUpsell['upsell']['active'])) {
                $itemUpsell = $calcUpsell['upsell'];
            }

            // Eğer bir hata sonucu total_price 0 kaydedilmişse birim fiyat x adet ile anında otomatik düzelt
            $itemTotalPrice = (float)$item['total_price'];
            if ($itemTotalPrice <= 0 && (float)$item['unit_price'] > 0) {
                $itemTotalPrice = (float)$item['unit_price'] * (int)$item['quantity'];
                try {
                    $this->db->prepare("UPDATE cart_items SET total_price = ? WHERE id = ?")->execute([$itemTotalPrice, $item['id']]);
                } catch (Exception $e) {}
            }

            $itemSubtotal = $itemTotalPrice / (1 + ((float)$item['tax_rate'] / 100));
            $itemTax = $itemTotalPrice - $itemSubtotal;

            $subtotal += $itemSubtotal;
            $totalTax += $itemTax;

            $items[] = [
                'id'                => $item['id'],
                'product_id'        => $item['product_id'],
                'product_name'      => $item['product_name'],
                'product_slug'      => $item['product_slug'],
                'featured_image'    => $item['featured_image'],
                'quantity'          => (int)$item['quantity'],
                'unit_price'        => (float)$item['unit_price'],
                'total_price'       => (float)$item['total_price'],
                'options_labels'    => $optionLabels,
                'custom_size'       => $customSize,
                'design_type'       => $item['design_type'],
                'design_file'       => $item['design_file'],
                'design_svg'        => $item['design_svg'],
                'design_preview'    => $item['design_preview'],
                'design_notes'      => $item['design_notes'],
                'upsell'            => $itemUpsell,
                'formatted_price'   => Helper::formatPrice($item['total_price'])
            ];
        }

        // Kupon & Kampanya İndirimi
        $couponCode = $_SESSION['applied_coupon'] ?? null;
        $discountAmount = 0.00;
        $couponData = null;

        if ($couponCode) {
            $cStmt = $this->db->prepare("SELECT * FROM campaigns WHERE code = ? AND status = 1 AND (start_date IS NULL OR start_date <= CURDATE()) AND (end_date IS NULL OR end_date >= CURDATE())");
            $cStmt->execute([$couponCode]);
            $coupon = $cStmt->fetch();

            if ($coupon && $subtotal >= (float)$coupon['min_cart_amount']) {
                if ($coupon['type'] === 'percent') {
                    $discountAmount = ($subtotal + $totalTax) * ((float)$coupon['value'] / 100);
                    if ((float)$coupon['max_discount_amount'] > 0) {
                        $discountAmount = min($discountAmount, (float)$coupon['max_discount_amount']);
                    }
                } elseif ($coupon['type'] === 'fixed') {
                    $discountAmount = (float)$coupon['value'];
                }
                $couponData = $coupon;
            } else {
                unset($_SESSION['applied_coupon']);
            }
        }

        // Kargo Hesaplama
        $freeShippingLimit = (float)Helper::getSetting('free_shipping_limit', '750.00');
        $defaultShippingFee = (float)Helper::getSetting('default_shipping_fee', '79.90');
        $cartTotalForShipping = ($subtotal + $totalTax) - $discountAmount;

        $shippingFee = 0.00;
        if (count($items) > 0) {
            if ($couponData && $couponData['type'] === 'free_shipping') {
                $shippingFee = 0.00;
            } elseif ($cartTotalForShipping < $freeShippingLimit) {
                $shippingFee = $defaultShippingFee;
            } else {
                $shippingFee = 0.00; // Ücretsiz Kargo
            }
        }

        $grandTotal = max(0, ($subtotal + $totalTax) - $discountAmount + $shippingFee);

        return [
            'items'                 => $items,
            'count'                 => count($items),
            'subtotal'              => round($subtotal, 2),
            'tax_amount'            => round($totalTax, 2),
            'discount_amount'       => round($discountAmount, 2),
            'shipping_fee'          => round($shippingFee, 2),
            'grand_total'           => round($grandTotal, 2),
            'coupon'                => $couponData,
            'is_free_shipping'      => ($shippingFee === 0.00 && count($items) > 0),
            'free_shipping_limit'   => $freeShippingLimit,
            'formatted_subtotal'    => Helper::formatPrice($subtotal),
            'formatted_tax'         => Helper::formatPrice($totalTax),
            'formatted_discount'    => Helper::formatPrice($discountAmount),
            'formatted_shipping'    => $shippingFee > 0 ? Helper::formatPrice($shippingFee) : '<span class="text-success fw-bold">ÜCRETSİZ</span>',
            'formatted_grand_total' => Helper::formatPrice($grandTotal)
        ];
    }

    /**
     * Kupon Kodu Uygula
     */
    public function applyCoupon($code) {
        $stmt = $this->db->prepare("SELECT * FROM campaigns WHERE code = ? AND status = 1 AND (start_date IS NULL OR start_date <= CURDATE()) AND (end_date IS NULL OR end_date >= CURDATE())");
        $stmt->execute([$code]);
        $coupon = $stmt->fetch();

        if (!$coupon) {
            return ['success' => false, 'error' => 'Geçersiz veya süresi dolmuş kupon kodu.'];
        }

        $summary = $this->getSummary();
        if ($summary['subtotal'] < (float)$coupon['min_cart_amount']) {
            return ['success' => false, 'error' => 'Bu kuponu kullanabilmek için sepet tutarınız en az ' . Helper::formatPrice($coupon['min_cart_amount']) . ' olmalıdır.'];
        }

        $_SESSION['applied_coupon'] = $coupon['code'];
        return ['success' => true, 'coupon' => $coupon];
    }

    /**
     * Kuponu Kaldır
     */
    public function removeCoupon() {
        unset($_SESSION['applied_coupon']);
        return ['success' => true];
    }
}
