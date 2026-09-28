<?php
/**
 * Sipariş, Vektörel Baskı Dosyaları ve Kargo Yönetimi Sınıfı
 */
class Order {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Sepetteki Ürünleri Siparişe Dönüştürür
     */
    public function createFromCart($customerData) {
        $cart = new Cart();
        $summary = $cart->getSummary();

        if (empty($summary['items'])) {
            return ['success' => false, 'error' => 'Sepetiniz boş.'];
        }

        try {
            $this->db->beginTransaction();

            $orderNumber = Helper::generateOrderNumber();
            $userId = Auth::id();

            $stmt = $this->db->prepare("INSERT INTO orders (
                order_number, user_id, customer_name, customer_email, customer_phone,
                shipping_address, shipping_city, shipping_district,
                billing_type, billing_company, tax_number, tax_office,
                payment_method, payment_status, order_status,
                subtotal, tax_amount, discount_amount, shipping_fee, total_amount,
                coupon_code, cargo_company, notes
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $paymentMethod = $customerData['payment_method'] ?? 'paytr';
            $paymentStatus = ($paymentMethod === 'bank_transfer' || $paymentMethod === 'door_payment') ? 'pending' : 'pending';
            $orderStatus = 'pending_payment';

            $stmt->execute([
                $orderNumber,
                $userId,
                $customerData['customer_name'],
                $customerData['customer_email'],
                $customerData['customer_phone'],
                $customerData['shipping_address'],
                $customerData['shipping_city'] ?? '',
                $customerData['shipping_district'] ?? '',
                $customerData['billing_type'] ?? 'individual',
                $customerData['billing_company'] ?? null,
                $customerData['tax_number'] ?? null,
                $customerData['tax_office'] ?? null,
                $paymentMethod,
                $paymentStatus,
                $orderStatus,
                $summary['subtotal'],
                $summary['tax_amount'],
                $summary['discount_amount'],
                $summary['shipping_fee'],
                $summary['grand_total'],
                $summary['coupon']['code'] ?? null,
                Helper::getSetting('cargo_default_company', 'Yurtiçi Kargo'),
                $customerData['notes'] ?? null
            ]);

            $orderId = $this->db->lastInsertId();

            // Sipariş Kalemlerini Ekle
            $itemStmt = $this->db->prepare("INSERT INTO order_items (
                order_id, product_id, product_name, quantity, unit_price, total_price,
                selected_options, custom_size, design_type, design_file, design_svg, design_preview, design_notes
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            foreach ($summary['items'] as $item) {
                $itemStmt->execute([
                    $orderId,
                    $item['product_id'],
                    $item['product_name'],
                    $item['quantity'],
                    $item['unit_price'],
                    $item['total_price'],
                    !empty($item['options_labels']) ? json_encode($item['options_labels'], JSON_UNESCAPED_UNICODE) : null,
                    !empty($item['custom_size']) ? json_encode($item['custom_size']) : null,
                    $item['design_type'],
                    $item['design_file'],
                    $item['design_svg'],
                    $item['design_preview'],
                    $item['design_notes']
                ]);
            }

            // Kupon Kullanım Sayısını Arttır
            if (!empty($summary['coupon']['id'])) {
                $cUp = $this->db->prepare("UPDATE campaigns SET usage_count = usage_count + 1 WHERE id = ?");
                $cUp->execute([$summary['coupon']['id']]);
            }

            // Sepeti Temizle
            $cart->clear();
            $cart->removeCoupon();

            $this->db->commit();

            return [
                'success'      => true,
                'order_id'     => $orderId,
                'order_number' => $orderNumber,
                'total_amount' => $summary['grand_total'],
                'items'        => $summary['items']
            ];

        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'error' => 'Sipariş oluşturulamadı: ' . $e->getMessage()];
        }
    }

    /**
     * ID ile Sipariş Getirir
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        $order = $stmt->fetch();
        if ($order) {
            $order['items'] = $this->getOrderItems($order['id']);
        }
        return $order;
    }

    /**
     * Sipariş Numarası ile Getirir
     */
    public function getByOrderNumber($orderNumber) {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE order_number = ?");
        $stmt->execute([$orderNumber]);
        $order = $stmt->fetch();
        if ($order) {
            $order['items'] = $this->getOrderItems($order['id']);
        }
        return $order;
    }

    /**
     * Siparişe Ait Kalemleri Getirir
     */
    public function getOrderItems($orderId) {
        $stmt = $this->db->prepare("SELECT oi.*, p.slug AS product_slug, p.featured_image 
                                    FROM order_items oi 
                                    LEFT JOIN products p ON oi.product_id = p.id 
                                    WHERE oi.order_id = ?");
        $stmt->execute([$orderId]);
        $items = $stmt->fetchAll();
        foreach ($items as &$item) {
            $item['options_array'] = !empty($item['selected_options']) ? json_decode($item['selected_options'], true) : [];
            $item['custom_size_array'] = !empty($item['custom_size']) ? json_decode($item['custom_size'], true) : null;
        }
        return $items;
    }

    /**
     * Sipariş Durumunu Güncelle
     */
    public function updateStatus($orderId, $status) {
        $stmt = $this->db->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
        return $stmt->execute([$status, $orderId]);
    }

    /**
     * Ödeme Durumunu Güncelle
     */
    public function updatePaymentStatus($orderId, $paymentStatus, $transactionId = null) {
        $sql = "UPDATE orders SET payment_status = ?";
        $params = [$paymentStatus];

        if ($transactionId) {
            $sql .= ", payment_transaction_id = ?";
            $params[] = $transactionId;
        }

        if ($paymentStatus === 'paid') {
            $sql .= ", order_status = 'payment_received'";
        }

        $sql .= " WHERE id = ?";
        $params[] = $orderId;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Kargo Takip Bilgisini Güncelle
     */
    public function updateCargo($orderId, $cargoCompany, $trackingCode) {
        $stmt = $this->db->prepare("UPDATE orders SET cargo_company = ?, cargo_tracking_code = ?, order_status = 'shipped' WHERE id = ?");
        return $stmt->execute([$cargoCompany, $trackingCode, $orderId]);
    }

    /**
     * Tüm Siparişleri Listele (Admin Paneli)
     */
    public function getAll($limit = 50, $status = null, $search = null) {
        $sql = "SELECT * FROM orders WHERE 1=1";
        $params = [];

        if ($status) {
            $sql .= " AND order_status = ?";
            $params[] = $status;
        }

        if ($search) {
            $sql .= " AND (order_number LIKE ? OR customer_name LIKE ? OR customer_phone LIKE ? OR customer_email LIKE ?)";
            $term = "%{$search}%";
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $sql .= " ORDER BY id DESC LIMIT " . (int)$limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
