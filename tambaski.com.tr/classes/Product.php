<?php
/**
 * Ürün, Matbaa Varyantları, Fiyat Matrisi ve AI Entegrasyon Sınıfı
 */
class Product {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->ensureDekotaCategoryAndProduct();
    }

    /**
     * Tüm Aktif Ürünleri Getirir
     */
    public function getAll($limit = null, $categoryId = null, $onlyFeatured = false, $onlyUrgent = false) {
        $sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug, c.pricing_model AS category_pricing_model 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 1";
        $params = [];

        if ($categoryId) {
            $sql .= " AND (p.category_id = ? OR c.parent_id = ?)";
            $params[] = $categoryId;
            $params[] = $categoryId;
        }

        if ($onlyFeatured) {
            $sql .= " AND p.is_featured = 1";
        }

        if ($onlyUrgent) {
            $sql .= " AND p.is_urgent = 1";
        }

        $sql .= " ORDER BY p.sort_order ASC, p.id DESC";

        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll();

        foreach ($products as &$prod) {
            $prod['starting_price'] = $this->getStartingPrice($prod);
            $prod['base_price'] = $prod['starting_price'];
        }

        return $products;
    }

    /**
     * Ürünün Vitrin Başlangıç Fiyatını (1000 Adet veya Min Tiraj) Döndürür
     */
    public function getStartingPrice($product) {
        if (is_numeric($product)) {
            $product = $this->getById((int)$product);
        }
        if (!$product) return 0.00;

        // 1. Manuel özel vitrin başlangıç fiyatı verilmişse (custom_starting_price)
        if (!empty($product['custom_starting_price']) && (float)$product['custom_starting_price'] > 0) {
            return (float)$product['custom_starting_price'];
        }

        // 2. Minimum adet kademesini bul
        $tiers = $this->getQuantityTiers($product['id']);
        $minQty = 1000;
        if (!empty($tiers) && isset($tiers[0]['quantity'])) {
            $minQty = (int)$tiers[0]['quantity'];
        }

        $lowestPrice = null;

        // 3. Tüm aktif paketler arasından en uygun fiyatı bul
        $presets = !empty($product['package_presets']) ? json_decode($product['package_presets'], true) : [];
        if (!empty($presets) && is_array($presets)) {
            foreach ($presets as $pkgKey => $pkgData) {
                if (!isset($pkgData['active']) || !empty($pkgData['active'])) {
                    $calc = $this->calculatePrice($product['id'], $minQty, [], 0, 0, false, $pkgKey);
                    if (!empty($calc['success']) && isset($calc['subtotal']) && $calc['subtotal'] > 0) {
                        if ($lowestPrice === null || (float)$calc['subtotal'] < $lowestPrice) {
                            $lowestPrice = (float)$calc['subtotal'];
                        }
                    }
                }
            }
        } else {
            // Fallback ekonomik ve standart paket
            $calcEko = $this->calculatePrice($product['id'], $minQty, [], 0, 0, false, 'ekonomik');
            if (!empty($calcEko['success']) && isset($calcEko['subtotal']) && $calcEko['subtotal'] > 0) {
                $lowestPrice = (float)$calcEko['subtotal'];
            }
            $calcStd = $this->calculatePrice($product['id'], $minQty, [], 0, 0, false, 'standart');
            if (!empty($calcStd['success']) && isset($calcStd['subtotal']) && $calcStd['subtotal'] > 0) {
                if ($lowestPrice === null || (float)$calcStd['subtotal'] < $lowestPrice) {
                    $lowestPrice = (float)$calcStd['subtotal'];
                }
            }
        }

        if ($lowestPrice !== null && $lowestPrice > 0) {
            return $lowestPrice;
        }

        $basePrice = (float)($product['base_price'] ?? 0);
        return $basePrice > 0 ? $basePrice : 0.00;
    }

    /**
     * Slug ile Ürün ve Detaylarını Getirir
     */
    public function getBySlug($slug) {
        $stmt = $this->db->prepare("SELECT p.*, c.name AS category_name, c.slug AS category_slug, c.pricing_model AS category_pricing_model 
                                    FROM products p 
                                    LEFT JOIN categories c ON p.category_id = c.id 
                                    WHERE p.slug = ? AND p.status = 1");
        $stmt->execute([$slug]);
        $product = $stmt->fetch();

        if ($product) {
            $product['attributes'] = $this->getAttributes($product['id']);
            $product['quantity_tiers'] = $this->getQuantityTiers($product['id']);
            $product['templates'] = $this->getTemplates($product['id'], $product);
            $product['variant_groups'] = $this->getVariantGroups($product['id'], $product);
            $product['allowed_papers_list'] = $this->getAllowedPapers($product['id'], $product);
            $product['gallery_array'] = !empty($product['gallery']) ? json_decode($product['gallery'], true) : [];
        }

        return $product;
    }

    /**
     * ID ile Ürün Getirir
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT p.*, c.name AS category_name, c.slug AS category_slug, c.pricing_model AS category_pricing_model 
                                    FROM products p 
                                    LEFT JOIN categories c ON p.category_id = c.id 
                                    WHERE p.id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        if ($product) {
            $product['attributes'] = $this->getAttributes($product['id']);
            $product['quantity_tiers'] = $this->getQuantityTiers($product['id']);
            $product['templates'] = $this->getTemplates($product['id'], $product);
            $product['variant_groups'] = $this->getVariantGroups($product['id'], $product);
            $product['allowed_papers_list'] = $this->getAllowedPapers($product['id'], $product);
            $product['gallery_array'] = !empty($product['gallery']) ? json_decode($product['gallery'], true) : [];
        }

        return $product;
    }

    /**
     * Ürüne Ait İzinli Kağıt Türlerini Getirir
     */
    public function getAllowedPapers($productId, $productRow = null) {
        if ($productRow === null) {
            $stmt = $this->db->prepare("SELECT allowed_papers FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $productRow = $stmt->fetch();
        }
        $allowed = !empty($productRow['allowed_papers']) ? json_decode($productRow['allowed_papers'], true) : [];
        if (!empty($allowed) && is_array($allowed)) {
            $inClause = implode(',', array_map('intval', $allowed));
            if (!empty($inClause)) {
                $stmt = $this->db->query("SELECT * FROM paper_types WHERE id IN ($inClause) AND status = 1 ORDER BY gsm ASC");
                $papers = $stmt ? $stmt->fetchAll() : [];
                if (!empty($papers)) return $papers;
            }
        }
        return $this->db->query("SELECT * FROM paper_types WHERE status = 1 ORDER BY gsm ASC")->fetchAll();
    }

    /**
     * Ürüne Ait Özellikleri ve Değerlerini Getirir
     */
    public function getAttributes($productId) {
        $stmt = $this->db->prepare("SELECT * FROM product_attributes WHERE product_id = ? ORDER BY sort_order ASC, id ASC");
        $stmt->execute([$productId]);
        $attributes = $stmt->fetchAll();

        foreach ($attributes as &$attr) {
            $valStmt = $this->db->prepare("SELECT * FROM product_attribute_values WHERE attribute_id = ? ORDER BY sort_order ASC, id ASC");
            $valStmt->execute([$attr['id']]);
            $attr['values'] = $valStmt->fetchAll();
        }

        return $attributes;
    }

    /**
     * Ürüne Ait Adet Kademelerini Getirir
     */
    public function getQuantityTiers($productId) {
        $stmt = $this->db->prepare("SELECT * FROM product_quantity_tiers WHERE product_id = ? ORDER BY quantity ASC");
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    /**
     * Tüm Sektörleri Getirir
     */
    public function getIndustries() {
        return $this->db->query("SELECT * FROM industries WHERE status = 1 ORDER BY sort_order ASC, id ASC")->fetchAll();
    }

    /**
     * Ürüne Ait Hazır Vektörel Şablonları Getirir (Sektör Bilgileriyle)
     */
    public function getTemplates($productId, $productRow = null) {
        if ($productRow === null) {
            $stmt = $this->db->prepare("SELECT p.*, c.slug AS category_slug FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
            $stmt->execute([$productId]);
            $productRow = $stmt->fetch();
        }

        if (!$productRow || empty($productRow['allow_online_editor'])) {
            return [];
        }

        // Ürün Türünü Belirle (Kartvizit, El İlanı / Broşür, Cepli Dosya, Kaşe)
        $catSlug = $productRow['category_slug'] ?? '';
        $prodSlug = $productRow['slug'] ?? '';
        $prodName = mb_strtolower($productRow['name'] ?? '', 'UTF-8');

        $detectedType = 'kartvizit';
        if (str_contains($catSlug, 'brosur') || str_contains($catSlug, 'ilan') || str_contains($prodSlug, 'brosur') || str_contains($prodSlug, 'ilan') || str_contains($prodName, 'broşür') || str_contains($prodName, 'el ilanı')) {
            $detectedType = 'brosur';
        } elseif (str_contains($catSlug, 'dosya') || str_contains($prodSlug, 'dosya') || str_contains($prodName, 'dosya')) {
            $detectedType = 'cepli-dosya';
        } elseif (str_contains($catSlug, 'kase') || str_contains($prodSlug, 'kase') || str_contains($prodName, 'kaşe')) {
            $detectedType = 'kase';
        }

        // 1. Ürüne özel seçilmiş şablon ID listesi varsa
        $allowedTplIds = !empty($productRow['allowed_templates']) ? json_decode($productRow['allowed_templates'], true) : [];
        if (!empty($allowedTplIds) && is_array($allowedTplIds)) {
            $inClause = implode(',', array_map('intval', $allowedTplIds));
            if (!empty($inClause)) {
                $sql = "SELECT dt.*, i.name AS industry_name, i.icon AS industry_icon 
                        FROM design_templates dt 
                        LEFT JOIN industries i ON dt.industry_slug = i.slug 
                        WHERE dt.id IN ($inClause) AND dt.status = 1 
                        ORDER BY i.sort_order ASC, dt.id ASC";
                $stmt = $this->db->query($sql);
                $templates = $stmt ? $stmt->fetchAll() : [];
                if (!empty($templates)) {
                    foreach ($templates as &$tpl) {
                        $tpl['fields'] = !empty($tpl['template_data']) ? json_decode($tpl['template_data'], true) : null;
                    }
                    return $templates;
                }
            }
        }

        // 2. Ürüne veya Ürün Türüne (kartvizit, brosur, cepli-dosya vb.) göre uyumlu şablonları getir
        $sql = "SELECT dt.*, i.name AS industry_name, i.icon AS industry_icon 
                FROM design_templates dt 
                LEFT JOIN industries i ON dt.industry_slug = i.slug 
                WHERE dt.status = 1 
                  AND (dt.product_id = ? OR dt.product_type = ? OR (dt.product_type IS NULL AND ? = 'kartvizit'))
                ORDER BY i.sort_order ASC, dt.id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int)$productId, $detectedType, $detectedType]);
        $templates = $stmt ? $stmt->fetchAll() : [];

        // Eğer türe göre bulunamazsa fallback olarak genel aktif şablonları getir
        if (empty($templates)) {
            $sql = "SELECT dt.*, i.name AS industry_name, i.icon AS industry_icon 
                    FROM design_templates dt 
                    LEFT JOIN industries i ON dt.industry_slug = i.slug 
                    WHERE dt.status = 1 
                    ORDER BY i.sort_order ASC, dt.id ASC";
            $stmt = $this->db->query($sql);
            $templates = $stmt ? $stmt->fetchAll() : [];
        }

        foreach ($templates as &$tpl) {
            $tpl['fields'] = !empty($tpl['template_data']) ? json_decode($tpl['template_data'], true) : null;
        }
        return $templates;
    }

    /**
     * Sektöre Göre Önerilen Tamamlayıcı Ürünleri Getirir
     */
    public function getRecommendedProductsByIndustry($industrySlug, $excludeProductId = null, $limit = 4) {
        if (empty($industrySlug)) {
            $industrySlug = 'genel-kurumsal';
        }

        $sql = "SELECT p.*, c.name AS category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 1";
        $params = [];

        if ($excludeProductId) {
            $sql .= " AND p.id != ?";
            $params[] = (int)$excludeProductId;
        }

        $sql .= " AND (p.target_industries LIKE ? OR p.target_industries IS NULL OR p.target_industries = '' OR p.target_industries = '[]')";
        $params[] = '%' . $industrySlug . '%';

        $sql .= " ORDER BY p.is_featured DESC, p.sort_order ASC, p.id DESC LIMIT " . (int)$limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll();

        // Eğer sektör ürünü azsa diğer popüler ürünlerle tamamla
        if (count($products) < $limit) {
            $existingIds = array_column($products, 'id');
            if ($excludeProductId) $existingIds[] = (int)$excludeProductId;
            $notIn = !empty($existingIds) ? "AND p.id NOT IN (" . implode(',', $existingIds) . ")" : "";
            
            $remainLimit = $limit - count($products);
            $stmtExtra = $this->db->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.status = 1 $notIn ORDER BY p.is_featured DESC, p.id DESC LIMIT $remainLimit");
            if ($stmtExtra) {
                $extraProds = $stmtExtra->fetchAll();
                $products = array_merge($products, $extraProds);
            }
        }

        return $products;
    }

    /**
     * Ürüne Ait Aktif Varyant Gruplarını ve Seçeneklerini Getirir
     */
    public function getVariantGroups($productId = null, $productRow = null) {
        $allowedGroups = [];
        $allowedOptions = [];
        if ($productId) {
            if ($productRow === null) {
                $stmt = $this->db->prepare("SELECT allowed_variant_groups, allowed_variant_options FROM products WHERE id = ?");
                $stmt->execute([$productId]);
                $productRow = $stmt->fetch();
            }
            if (!empty($productRow['allowed_variant_groups'])) {
                $allowedGroups = json_decode($productRow['allowed_variant_groups'], true);
            }
            if (!empty($productRow['allowed_variant_options'])) {
                $allowedOptions = json_decode($productRow['allowed_variant_options'], true);
            }
        }

        $sql = "SELECT * FROM variant_groups WHERE status = 1";
        if (!empty($allowedGroups) && is_array($allowedGroups)) {
            $inClause = implode(',', array_map('intval', $allowedGroups));
            if (!empty($inClause)) {
                $sql .= " AND id IN ($inClause)";
            }
        }
        $sql .= " ORDER BY sort_order ASC, id ASC";

        $stmt = $this->db->query($sql);
        $groups = $stmt ? $stmt->fetchAll() : [];
        $finalGroups = [];

        foreach ($groups as $grp) {
            $optSql = "SELECT * FROM variant_options WHERE group_id = ? AND status = 1";
            if (!empty($allowedOptions) && is_array($allowedOptions)) {
                $inOptClause = implode(',', array_map('intval', $allowedOptions));
                if (!empty($inOptClause)) {
                    $optSql .= " AND id IN ($inOptClause)";
                }
            }
            $optSql .= " ORDER BY sort_order ASC, id ASC";

            $optStmt = $this->db->prepare($optSql);
            $optStmt->execute([$grp['id']]);
            $opts = $optStmt->fetchAll();

            if (!empty($opts)) {
                $grp['options'] = $opts;
                $finalGroups[] = $grp;
            }
        }

        return $finalGroups;
    }

    /**
     * Tiraj Çarpanını Hesaplar (Adet arttıkça birim maliyetin düşüş eğrisi)
    /**
     * Tiraj Çarpanını Hesaplar (Adet arttıkça birim maliyetin düşüş eğrisi - 1.000 Adet = 1.00 Baz)
     */
    public function getTierMultiplier($quantity, $productId = null) {
        if ($productId) {
            $tiers = $this->getQuantityTiers($productId);
            if (!empty($tiers)) {
                foreach ($tiers as $t) {
                    if ((int)$t['quantity'] === (int)$quantity && isset($t['multiplier'])) {
                        $m = (float)$t['multiplier'];
                        if ($m > 0) {
                            // Eğer tier çarpanı birim indirim olarak (< 1.0) girildiyse toplam çarpana çevir
                            if ($quantity > 1000 && $m < 1.0) {
                                return ($quantity / 1000) * $m;
                            }
                            return $m;
                        }
                    }
                }
            }
        }

        // Standart Matbaa Ofset Tiraj Eğrisi (1.000 Adet = 1.00 Baz)
        if ($quantity == 1000) return 1.00;
        if ($quantity == 2000) return 1.70;
        if ($quantity == 3000) return 2.40;
        if ($quantity == 5000) return 3.65;
        if ($quantity == 10000) return 6.80;
        if ($quantity == 500) return 0.75;
        if ($quantity == 250) return 0.60;
        if ($quantity == 100) return 0.50;

        if ($quantity < 1000) {
            return max(0.40, 0.50 + (($quantity - 100) / 900) * 0.50);
        } else {
            return pow($quantity / 1000, 0.82);
        }
    }

    /**
     * 1. Faz: Sadeleştirilmiş Doğrudan Paket & Tiraj Fiyat Hesaplama Motoru (100x70 Tabaka Hesabı Kaldırıldı)
     */
    public function calculatePrice($productId, $quantity, $selectedOptions = [], $customWidth = 0, $customHeight = 0, $includeDesignService = false, $selectedPackage = 'standart', $customPaperId = 0) {
        $product = $this->getById($productId);
        if (!$product) {
            return ['success' => false, 'error' => 'Ürün bulunamadı.'];
        }

        $quantity = max(1, (int)$quantity);
        $basePrice = (float)($product['base_price'] ?? 0);
        if ($basePrice <= 0) {
            $basePrice = (float)($product['manual_base_price'] ?? 750.00);
        }

        $presets = !empty($product['package_presets']) ? json_decode($product['package_presets'], true) : [];

        // 0. Sert Zemin & Levha Modeli (rigid_board - Dekota Uyarı Levhaları vb.) Fiyat Hesaplama
        if (!empty($product['category_pricing_model']) && $product['category_pricing_model'] === 'rigid_board') {
            $isCustomSize = ($selectedPackage === 'ozel' || ($customWidth > 0 && $customHeight > 0));
            $unitBasePrice = 95.00;

            if ($isCustomSize && $customWidth > 0 && $customHeight > 0) {
                // Dinamik m2 hesabı (USD / TRY Kuru ile ve yukarı yuvarlamalı)
                $areaM2 = ($customWidth * $customHeight) / 10000;
                $m2Usd = (float)($product['m2_usd_price'] ?? 0);
                if ($m2Usd > 0) {
                    $usdRate = Helper::getUsdRate();
                    $rawPriceTry = $areaM2 * $m2Usd * $usdRate;
                    $unitBasePrice = max(45.00, ceil($rawPriceTry)); // Örn: 47,52 TL -> 48 TL
                } else {
                    $unitBasePrice = max(45.00, ceil($areaM2 * 550.00));
                }
            } else {
                if (!empty($presets[$selectedPackage]['price'])) {
                    $unitBasePrice = (float)$presets[$selectedPackage]['price'];
                }
            }

            // Kalınlık Seçimi (5mm ise +%25)
            $thickness = $selectedOptions['thickness'] ?? '3mm';
            if ($thickness === '5mm') {
                $unitBasePrice = ceil($unitBasePrice * 1.25);
            }

            // Montaj Seçeneği
            $mounting = $selectedOptions['mounting'] ?? 'none';
            if ($mounting === 'tape') {
                $unitBasePrice += 15.00; // Çift taraflı köpük bant
            } elseif ($mounting === 'holes') {
                $unitBasePrice += 10.00; // 4 Köşeden delikli
            }

            // Kademeli Toplu Adet İndirimi (Veritabanındaki tanımlı kademelerden çekilir)
            $tiers = $this->getQuantityTiers($productId);
            $discountPercent = 0.0;
            if (!empty($tiers)) {
                foreach ($tiers as $t) {
                    if ($quantity >= (int)$t['quantity']) {
                        $discountPercent = (float)$t['discount_percent'];
                    }
                }
            } else {
                if ($quantity >= 100) $discountPercent = 40.0;
                elseif ($quantity >= 50) $discountPercent = 30.0;
                elseif ($quantity >= 25) $discountPercent = 20.0;
                elseif ($quantity >= 10) $discountPercent = 10.0;
            }

            $unitPrice = ceil($unitBasePrice * (1 - ($discountPercent / 100)));
            $calculatedSubtotal = $unitPrice * $quantity;

            if ($includeDesignService && !empty($product['allow_design_service'])) {
                $calculatedSubtotal += (float)($product['design_service_price'] ?? 150.0);
            }

            $dealerDiscountRate = Auth::getDiscountRate();
            if ($dealerDiscountRate > 0) {
                $calculatedSubtotal -= ($calculatedSubtotal * ($dealerDiscountRate / 100));
            }

            $taxRate = (float)($product['tax_rate'] ?? 20.0);
            $taxAmount = $calculatedSubtotal * ($taxRate / 100);
            $totalWithTax = $calculatedSubtotal + $taxAmount;

            return [
                'success'               => true,
                'product_id'            => (int)$productId,
                'quantity'              => $quantity,
                'package'               => $selectedPackage,
                'unit_price'            => $unitPrice,
                'unit_base_price'       => $unitBasePrice,
                'discount_percent'      => $discountPercent,
                'subtotal'              => $calculatedSubtotal,
                'tax_rate'              => $taxRate,
                'tax_amount'            => round($taxAmount, 2),
                'total'                 => round($totalWithTax, 2),
                'formatted_total'       => Helper::formatPrice($totalWithTax),
                'formatted_subtotal'    => Helper::formatPrice($calculatedSubtotal),
                'formatted_unit_price'  => Helper::formatPrice($unitPrice),
                'currency'              => 'TRY',
                'pricing_model'         => 'rigid_board'
            ];
        }

        // 1. Standart Paket Fiyatı / Çarpanı Tespiti (Kartvizit vb.)
        $packagePrice = $basePrice;

        if (!empty($presets[$selectedPackage])) {
            $preset = $presets[$selectedPackage];
            if (isset($preset['price']) && (float)$preset['price'] > 0) {
                $packagePrice = (float)$preset['price'];
            } elseif (isset($preset['supplier_cost_1000']) && (float)$preset['supplier_cost_1000'] > 0) {
                // Kar marjı eklenmiş doğrudan paket fiyatı
                $margin = !empty($product['profit_margin_percent']) ? (float)$product['profit_margin_percent'] : 50.0;
                $packagePrice = (float)$preset['supplier_cost_1000'] * (1 + ($margin / 100));
            } else {
                $multipliers = [
                    'ekonomik' => 0.85,
                    'standart' => 1.00,
                    'premium'  => 1.45,
                    'vip'      => 1.85
                ];
                $packagePrice = $basePrice * ($multipliers[$selectedPackage] ?? 1.0);
            }
        } else {
            $multipliers = [
                'ekonomik' => 0.85,
                'standart' => 1.00,
                'premium'  => 1.45,
                'vip'      => 1.85
            ];
            $packagePrice = $basePrice * ($multipliers[$selectedPackage] ?? 1.0);
        }

        // 2. Tiraj / Adet İndirimi Tespiti
        $discountPercent = 0.0;
        if (!empty($product['quantity_tiers'])) {
            foreach ($product['quantity_tiers'] as $tier) {
                if ((int)$tier['quantity'] === $quantity) {
                    $discountPercent = (float)($tier['discount_percent'] ?? 0);
                    break;
                }
            }
        }
        
        // Eğer veritabanında eşleşen tier yoksa standart matbaa kademesi
        if ($discountPercent == 0.0 && $quantity > 1000) {
            if ($quantity >= 10000) $discountPercent = 38.0;
            elseif ($quantity >= 5000) $discountPercent = 30.0;
            elseif ($quantity >= 3000) $discountPercent = 22.0;
            elseif ($quantity >= 2000) $discountPercent = 15.0;
        }

        // 3. Birim Fiyat ve Ara Toplam (1.000 Adet Fiyatı Üzerinden)
        $discountFactor = max(0.2, (1 - ($discountPercent / 100)));
        $unitPrice = ($packagePrice / 1000) * $discountFactor;
        $calculatedSubtotal = round($unitPrice * $quantity, 2);

        // 4. Tasarım Desteği Eklentisi
        if ($includeDesignService && !empty($product['allow_design_service'])) {
            $calculatedSubtotal += (float)($product['design_service_price'] ?? 150.0);
        }

        // 5. B2B / Bayi İskontosu
        $dealerDiscountRate = Auth::getDiscountRate();
        if ($dealerDiscountRate > 0) {
            $calculatedSubtotal -= ($calculatedSubtotal * ($dealerDiscountRate / 100));
        }

        // 6. KDV Hesaplama
        $taxRate = (float)($product['tax_rate'] ?? 20.0);
        $taxAmount = $calculatedSubtotal * ($taxRate / 100);
        $totalWithTax = $calculatedSubtotal + $taxAmount;

        return [
            'success'               => true,
            'product_id'            => (int)$productId,
            'quantity'              => $quantity,
            'usd_rate'              => 0,
            'sheets_needed'         => 1,
            'items_per_sheet'       => 1,
            'total_produced'        => $quantity,
            'upsell'                => null,
            'waste_percent'         => 0,
            'is_custom_size'        => false,
            'pricing_mode'          => 'simple_package',
            'profit_margin_percent' => 0,
            'raw_cost_try'          => round($calculatedSubtotal * 0.5, 2),
            'subtotal'              => round($calculatedSubtotal, 2),
            'tax_rate'              => $taxRate,
            'tax_amount'            => round($taxAmount, 2),
            'grand_total'           => round($totalWithTax, 2),
            'unit_price'            => round($unitPrice, 4),
            'formatted_subtotal'    => Helper::formatPrice($calculatedSubtotal),
            'formatted_total'       => Helper::formatPrice($totalWithTax),
            'formatted_unit_price'  => Helper::formatPrice($unitPrice, ' ₺ / adet')
        ];
    }

    /**
     * AI Araçları & REST API için Komple Ürün, Varyant ve Şablon Oluşturucu
     */
    public function createFullProductFromAI($data) {
        try {
            $this->db->beginTransaction();

            // 1. Kategori Kontrolü veya Otomatik Oluşturma
            $categoryId = $data['category_id'] ?? null;
            if (!$categoryId && !empty($data['category_name'])) {
                $catSlug = Helper::slugify($data['category_name']);
                $catStmt = $this->db->prepare("SELECT id FROM categories WHERE slug = ?");
                $catStmt->execute([$catSlug]);
                $catRow = $catStmt->fetch();
                if ($catRow) {
                    $categoryId = $catRow['id'];
                } else {
                    $insCat = $this->db->prepare("INSERT INTO categories (name, slug, description, icon) VALUES (?, ?, ?, ?)");
                    $insCat->execute([$data['category_name'], $catSlug, $data['category_description'] ?? '', $data['category_icon'] ?? 'bi bi-box-seam']);
                    $categoryId = $this->db->lastInsertId();
                }
            }

            if (!$categoryId) {
                $categoryId = 1; // Varsayılan
            }

            // 2. Ürün Ana Kaydı
            $slug = !empty($data['slug']) ? Helper::slugify($data['slug']) : Helper::slugify($data['name']);
            // Benzersiz slug kontrolü
            $slugCheck = $this->db->prepare("SELECT id FROM products WHERE slug = ?");
            $slugCheck->execute([$slug]);
            if ($slugCheck->fetch()) {
                $slug .= '-' . bin2hex(random_bytes(2));
            }

            $prodStmt = $this->db->prepare("INSERT INTO products (
                category_id, name, slug, sku, short_description, full_description, 
                base_price, tax_rate, is_custom_size, min_width, max_width, min_height, max_height, price_per_sqm,
                allow_design_upload, allow_online_editor, allow_design_service, design_service_price,
                featured_image, gallery, is_featured, is_urgent, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $prodStmt->execute([
                $categoryId,
                $data['name'],
                $slug,
                $data['sku'] ?? 'AI-' . strtoupper(bin2hex(random_bytes(3))),
                $data['short_description'] ?? '',
                $data['full_description'] ?? '',
                $data['base_price'] ?? 100.00,
                $data['tax_rate'] ?? 20.00,
                !empty($data['is_custom_size']) ? 1 : 0,
                $data['min_width'] ?? 0,
                $data['max_width'] ?? 0,
                $data['min_height'] ?? 0,
                $data['max_height'] ?? 0,
                $data['price_per_sqm'] ?? 0,
                isset($data['allow_design_upload']) ? (int)$data['allow_design_upload'] : 1,
                isset($data['allow_online_editor']) ? (int)$data['allow_online_editor'] : 1,
                isset($data['allow_design_service']) ? (int)$data['allow_design_service'] : 1,
                $data['design_service_price'] ?? 150.00,
                $data['featured_image'] ?? null,
                !empty($data['gallery']) ? json_encode($data['gallery']) : null,
                !empty($data['is_featured']) ? 1 : 0,
                !empty($data['is_urgent']) ? 1 : 0,
                isset($data['status']) ? (int)$data['status'] : 1
            ]);

            $productId = $this->db->lastInsertId();

            // 3. Özellikler & Değerler (Kağıt, Selefon, Ebat vb.)
            if (!empty($data['attributes']) && is_array($data['attributes'])) {
                foreach ($data['attributes'] as $sort => $attr) {
                    $attrStmt = $this->db->prepare("INSERT INTO product_attributes (product_id, name, type, is_required, sort_order) VALUES (?, ?, ?, ?, ?)");
                    $attrStmt->execute([
                        $productId,
                        $attr['name'],
                        $attr['type'] ?? 'radio',
                        isset($attr['is_required']) ? (int)$attr['is_required'] : 1,
                        $sort + 1
                    ]);
                    $attrId = $this->db->lastInsertId();

                    if (!empty($attr['values']) && is_array($attr['values'])) {
                        foreach ($attr['values'] as $vSort => $val) {
                            $valStmt = $this->db->prepare("INSERT INTO product_attribute_values (attribute_id, title, price_extra, price_multiplier, is_default, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
                            $valStmt->execute([
                                $attrId,
                                $val['title'],
                                $val['price_extra'] ?? 0.00,
                                $val['price_multiplier'] ?? 1.00,
                                !empty($val['is_default']) ? 1 : 0,
                                $vSort + 1
                            ]);
                        }
                    }
                }
            }

            // 4. Adet Kademeleri
            if (!empty($data['quantity_tiers']) && is_array($data['quantity_tiers'])) {
                foreach ($data['quantity_tiers'] as $tSort => $tier) {
                    $tierStmt = $this->db->prepare("INSERT INTO product_quantity_tiers (product_id, quantity, multiplier, fixed_price, discount_percent, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
                    $tierStmt->execute([
                        $productId,
                        (int)$tier['quantity'],
                        $tier['multiplier'] ?? 1.0000,
                        $tier['fixed_price'] ?? null,
                        $tier['discount_percent'] ?? 0.00,
                        $tSort + 1
                    ]);
                }
            }

            // 5. Hazır Vektörel Şablonlar (SVG / Online Editor)
            if (!empty($data['templates']) && is_array($data['templates'])) {
                foreach ($data['templates'] as $tplSort => $tpl) {
                    $tplSlug = Helper::slugify($tpl['title']);
                    $tplStmt = $this->db->prepare("INSERT INTO design_templates (product_id, title, slug, thumbnail, category, canvas_width, canvas_height, default_svg, template_data, status, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $tplStmt->execute([
                        $productId,
                        $tpl['title'],
                        $tplSlug,
                        $tpl['thumbnail'] ?? null,
                        $tpl['category'] ?? 'Kurumsal',
                        $tpl['canvas_width'] ?? 850,
                        $tpl['canvas_height'] ?? 500,
                        $tpl['default_svg'] ?? '',
                        !empty($tpl['fields']) ? json_encode(['fields' => $tpl['fields']]) : null,
                        1,
                        $tplSort + 1
                    ]);
                }
            }

            $this->db->commit();
            return [
                'success'    => true,
                'product_id' => $productId,
                'slug'       => $slug,
                'url'        => SITE_URL . '/product.php?slug=' . $slug
            ];

        } catch (Exception $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'error'   => 'Ürün eklenirken hata: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Tüm Aktif Tedarikçileri Getirir
     */
    public function getSuppliers($onlyActive = true) {
        $sql = "SELECT * FROM suppliers";
        if ($onlyActive) {
            $sql .= " WHERE status = 1";
        }
        $sql .= " ORDER BY sort_order ASC, id ASC";
        try {
            $stmt = $this->db->query($sql);
            return $stmt ? $stmt->fetchAll() : [];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * ID ile Tedarikçi Getirir
     */
    public function getSupplierById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM suppliers WHERE id = ?");
            $stmt->execute([(int)$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Tedarikçi Fiyat Listesi Kalemlerini Getirir
     */
    public function getSupplierItems($supplierId = null, $category = null) {
        $sql = "SELECT i.*, s.name as supplier_name FROM supplier_price_items i LEFT JOIN suppliers s ON i.supplier_id = s.id WHERE i.status = 1";
        $params = [];
        if ($supplierId) {
            $sql .= " AND i.supplier_id = ?";
            $params[] = (int)$supplierId;
        }
        if ($category) {
            $sql .= " AND i.category = ?";
            $params[] = $category;
        }
        $sql .= " ORDER BY i.category ASC, i.id ASC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt ? $stmt->fetchAll() : [];
        } catch (Exception $e) {
            return [];
        }
    }

    public function ensureDekotaCategoryAndProduct() {
        if (!$this->db) return;
        try {
            // 1. Dekota Uyarı Levhaları Kategorisi
            $catStmt = $this->db->prepare("SELECT id FROM categories WHERE slug = 'dekota-uyari-levhalari' LIMIT 1");
            $catStmt->execute();
            $catId = $catStmt->fetchColumn();

            if (!$catId) {
                $insCat = $this->db->prepare("INSERT INTO categories (name, slug, icon, pricing_model, sort_order, status) VALUES (?, ?, ?, ?, ?, ?)");
                $insCat->execute(['Dekota Uyarı Levhaları', 'dekota-uyari-levhalari', 'bi bi-exclamation-triangle-fill', 'rigid_board', 2, 1]);
                $catId = $this->db->lastInsertId();
            } else {
                $this->db->prepare("UPDATE categories SET pricing_model = 'rigid_board', icon = 'bi bi-exclamation-triangle-fill' WHERE id = ?")->execute([$catId]);
            }

            // 2. Dekota Ürünü
            $prodStmt = $this->db->prepare("SELECT id FROM products WHERE slug = 'dekota-isg-guvenlik-uyari-levhasi' LIMIT 1");
            $prodStmt->execute();
            $prodId = $prodStmt->fetchColumn();

            $dekotaPackages = [
                'kucuk' => [
                    'name'   => 'Küçük Boy (25x35 cm)',
                    'active' => 1,
                    'price'  => 95.00,
                    'desc'   => 'Kapı üstü, pano yanı, ofis ve atölye içi kullanım',
                    'badge'  => 'Kompakt (25x35 cm)',
                    'width'  => 25,
                    'height' => 35
                ],
                'orta' => [
                    'name'   => 'Orta Boy (35x50 cm)',
                    'active' => 1,
                    'price'  => 145.00,
                    'desc'   => 'Koridorlar, üretim hatları ve elektrik panoları',
                    'badge'  => 'Standart (35x50 cm)',
                    'width'  => 35,
                    'height' => 50
                ],
                'buyuk' => [
                    'name'   => 'Büyük Boy (50x70 cm)',
                    'active' => 1,
                    'price'  => 240.00,
                    'desc'   => 'Fabrika girişleri, şantiyeler ve geniş depo alanları',
                    'badge'  => 'Çok Satan (50x70 cm)',
                    'width'  => 50,
                    'height' => 70
                ],
                'mega' => [
                    'name'   => 'Mega Boy (70x100 cm)',
                    'active' => 1,
                    'price'  => 420.00,
                    'desc'   => 'Dış cephe, nizamiyeler ve otopark yönlendirme',
                    'badge'  => 'Dev Ebat (70x100 cm)',
                    'width'  => 70,
                    'height' => 100
                ]
            ];
            $pkgJson = json_encode($dekotaPackages, JSON_UNESCAPED_UNICODE);
            $galleryJson = json_encode([
                'uploads/mockups/tambaski_dekota_mockup.jpg',
                'uploads/mockups/tambaski_dekota_collection.jpg'
            ], JSON_UNESCAPED_SLASHES);

            try {
                $this->db->exec("ALTER TABLE products ADD COLUMN m2_usd_price DECIMAL(10,2) DEFAULT 0.00");
            } catch (Exception $e) {}

            if (!$prodId) {
                $insProd = $this->db->prepare("INSERT INTO products 
                    (category_id, name, slug, sku, short_description, full_description, base_price, m2_usd_price, package_presets, featured_image, gallery, allow_online_editor, allow_design_upload, is_featured, is_urgent, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $insProd->execute([
                    $catId,
                    'Dekota İSG & Güvenlik Uyarı Levhası',
                    'dekota-isg-guvenlik-uyari-levhasi',
                    'TB-LEVH-01',
                    '3mm / 5mm Sert Dekota (Forex) zemin üzerine yüksek çözünürlüklü UV baskılı İSG, fabrika ve tesis güvenlik uyarı levhaları.',
                    '3mm veya 5mm Sert Dekota (Forex) zemin üzerine direkt UV baskı teknolojisiyle üretilen yüksek dayanımlı uyarı levhaları. Solmaz, neme, suya ve güneşe tam dayanıklıdır.',
                    95.00,
                    14.50,
                    $pkgJson,
                    'uploads/mockups/tambaski_dekota_mockup.jpg',
                    $galleryJson,
                    1, 1, 1, 1, 1
                ]);
                $prodId = $this->db->lastInsertId();

                // Kademeli Adet İskontoları
                $tiers = [
                    ['quantity' => 1, 'multiplier' => 1.0, 'discount_percent' => 0],
                    ['quantity' => 5, 'multiplier' => 1.0, 'discount_percent' => 0],
                    ['quantity' => 10, 'multiplier' => 0.90, 'discount_percent' => 10],
                    ['quantity' => 25, 'multiplier' => 0.80, 'discount_percent' => 20],
                    ['quantity' => 50, 'multiplier' => 0.70, 'discount_percent' => 30],
                    ['quantity' => 100, 'multiplier' => 0.60, 'discount_percent' => 40],
                ];
                $insTier = $this->db->prepare("INSERT INTO product_quantity_tiers (product_id, quantity, multiplier, discount_percent) VALUES (?, ?, ?, ?)");
                foreach ($tiers as $t) {
                    $insTier->execute([$prodId, $t['quantity'], $t['multiplier'], $t['discount_percent']]);
                }
            } else {
                $this->db->prepare("UPDATE products SET category_id = ?, package_presets = ?, m2_usd_price = 14.50, featured_image = ?, gallery = ?, allow_online_editor = 1, status = 1 WHERE id = ?")
                         ->execute([$catId, $pkgJson, 'uploads/mockups/tambaski_dekota_mockup.jpg', $galleryJson, $prodId]);
            }
        } catch (Exception $e) {}
    }
}

