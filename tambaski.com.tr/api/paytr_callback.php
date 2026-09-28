<?php
/**
 * PayTR Bildirim / Callback Webhook Uç Noktası
 */
require_once __DIR__ . '/../config/config.php';

$paytr = new PayTR();
$verify = $paytr->verifyCallback($_POST);

if ($verify['valid']) {
    $orderModel = new Order();
    $order = $orderModel->getByOrderNumber($verify['order_number']);

    if ($order) {
        if ($verify['status'] === 'success') {
            $orderModel->updatePaymentStatus($order['id'], 'paid', $_POST['merchant_oid'] ?? null);
        } else {
            $orderModel->updatePaymentStatus($order['id'], 'failed', $_POST['failed_reason_code'] ?? null);
        }
    }

    // PayTR'a işlemin başarıyla alındığını bildirmek için zorunlu metin
    echo "OK";
    exit;
} else {
    http_response_code(400);
    echo "PAYTR_ERROR: " . ($verify['error'] ?? 'Hash mismatch');
    exit;
}
