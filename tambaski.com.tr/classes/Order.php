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
        $clean = preg_replace('/[^a-zA-Z0-9]/', '', (string)$orderNumber);
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE order_number = ? OR REPLACE(order_number, '-', '') = ? LIMIT 1");
        $stmt->execute([$orderNumber, $clean]);
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
     * Siparişi ve Kalemlerini Tamamen Sil
     */
    public function delete($orderId) {
        try {
            $this->db->beginTransaction();
            $delItems = $this->db->prepare("DELETE FROM order_items WHERE order_id = ?");
            $delItems->execute([(int)$orderId]);
            $delOrder = $this->db->prepare("DELETE FROM orders WHERE id = ?");
            $res = $delOrder->execute([(int)$orderId]);
            $this->db->commit();
            return $res;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Sipariş İstatistikleri ve KPI Özetleri
     */
    public function getStats() {
        try {
            $totalOrders = (int)$this->db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
            $paidSum = (float)$this->db->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE payment_status = 'paid'")->fetchColumn();
            $paidOrders = (int)$this->db->query("SELECT COUNT(*) FROM orders WHERE payment_status = 'paid'")->fetchColumn();
            $pendingPayment = (int)$this->db->query("SELECT COUNT(*) FROM orders WHERE payment_status = 'pending'")->fetchColumn();
            $inProduction = (int)$this->db->query("SELECT COUNT(*) FROM orders WHERE order_status IN ('in_production', 'design_approval', 'payment_received', 'preparing')")->fetchColumn();
            $shipped = (int)$this->db->query("SELECT COUNT(*) FROM orders WHERE order_status = 'shipped'")->fetchColumn();
            $delivered = (int)$this->db->query("SELECT COUNT(*) FROM orders WHERE order_status = 'delivered'")->fetchColumn();
            $cancelled = (int)$this->db->query("SELECT COUNT(*) FROM orders WHERE order_status IN ('cancelled', 'refunded') OR payment_status = 'refunded'")->fetchColumn();

            return [
                'total_orders'    => $totalOrders,
                'paid_sum'        => $paidSum,
                'paid_orders'     => $paidOrders,
                'pending_payment' => $pendingPayment,
                'in_production'   => $inProduction,
                'shipped'         => $shipped,
                'delivered'       => $delivered,
                'cancelled'       => $cancelled
            ];
        } catch (Exception $e) {
            return [
                'total_orders'    => 0,
                'paid_sum'        => 0,
                'paid_orders'     => 0,
                'pending_payment' => 0,
                'in_production'   => 0,
                'shipped'         => 0,
                'delivered'       => 0,
                'cancelled'       => 0
            ];
        }
    }

    /**
     * Tüm Siparişleri Listele (Admin Paneli Gelişmiş Filtreleme)
     */
    public function getAll($limit = 100, $status = null, $paymentStatus = null, $search = null) {
        $sql = "SELECT * FROM orders WHERE 1=1";
        $params = [];

        if (!empty($status)) {
            if ($status === 'in_production_group') {
                $sql .= " AND order_status IN ('payment_received', 'design_approval', 'in_production', 'preparing', 'packaged')";
            } elseif ($status === 'cancelled_group') {
                $sql .= " AND (order_status IN ('cancelled', 'refunded') OR payment_status = 'refunded')";
            } else {
                $sql .= " AND order_status = ?";
                $params[] = $status;
            }
        }

        if (!empty($paymentStatus)) {
            $sql .= " AND payment_status = ?";
            $params[] = $paymentStatus;
        }

        if (!empty($search)) {
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
