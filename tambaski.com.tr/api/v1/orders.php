<?php
/**
 * REST API v1 - Siparişler & Vektörel Tasarım İndirme
 */
require_once __DIR__ . '/auth_check.php';

$orderModel = new Order();

if (!empty($_GET['order_number'])) {
    $order = $orderModel->getByOrderNumber($_GET['order_number']);
    if ($order) {
        Helper::jsonResponse(['success' => true, 'order' => $order]);
    } else {
        Helper::jsonResponse(['success' => false, 'error' => 'Sipariş bulunamadı.'], 404);
    }
} else {
    $status = $_GET['status'] ?? null;
    $limit = (int)($_GET['limit'] ?? 50);
    $orders = $orderModel->getAll($limit, $status);
    Helper::jsonResponse(['success' => true, 'count' => count($orders), 'orders' => $orders]);
}
