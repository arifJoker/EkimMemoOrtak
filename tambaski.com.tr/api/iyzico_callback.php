<?php
/**
 * iyzico 3D Secure / Checkout Form Dönüş Uç Noktası
 */
require_once __DIR__ . '/../config/config.php';

$token = $_POST['token'] ?? null;
if (!$token) {
    header("Location: " . SITE_URL . "/checkout.php?status=failed&error=" . urlencode("iyzico ödeme belirteci (token) eksik."));
    exit;
}

$iyzico = new Iyzico();
$result = $iyzico->getPaymentResult($token);

if ($result['success']) {
    $orderModel = new Order();
    $order = $orderModel->getByOrderNumber($result['order_number']);
    if ($order) {
        $orderModel->updatePaymentStatus($order['id'], 'paid', $result['payment_id'] ?? null);
    }
    header("Location: " . SITE_URL . "/success.php?order_number=" . urlencode($result['order_number']));
    exit;
} else {
    header("Location: " . SITE_URL . "/checkout.php?status=failed&error=" . urlencode($result['error'] ?? 'Ödeme tamamlanamadı.'));
    exit;
}
