<?php
/**
 * Ajax Canlı Fiyat Hesaplama Uç Noktası
 */
require_once __DIR__ . '/../config/config.php';

$productId = (int)($_REQUEST['product_id'] ?? 0);
$quantity = (int)($_REQUEST['quantity'] ?? 100);
$options = $_REQUEST['options'] ?? [];
$customWidth = (float)($_REQUEST['custom_width'] ?? 0);
$customHeight = (float)($_REQUEST['custom_height'] ?? 0);
$includeDesignService = !empty($_REQUEST['design_service']);
$selectedPackage = trim($_REQUEST['selected_package'] ?? $_REQUEST['package_id'] ?? $_REQUEST['package'] ?? 'standart');
$customPaperId = (int)($_REQUEST['custom_paper_id'] ?? 0);

if (!$productId) {
    Helper::jsonResponse(['success' => false, 'error' => 'Ürün ID belirtilmedi.'], 400);
}

$productModel = new Product();
$result = $productModel->calculatePrice($productId, $quantity, $options, $customWidth, $customHeight, $includeDesignService, $selectedPackage, $customPaperId);

Helper::jsonResponse($result);

