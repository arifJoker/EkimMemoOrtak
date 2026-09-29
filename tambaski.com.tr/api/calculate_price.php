<?php
/**
 * Ajax Canlı Fiyat Hesaplama Uç Noktası
 */
require_once __DIR__ . '/../config/config.php';

$productId = (int)($_REQUEST['product_id'] ?? 0);
$quantity = (int)($_REQUEST['quantity'] ?? 100);
$options = $_REQUEST['options'] ?? [];
if (!is_array($options)) {
    $options = [];
}
if (!empty($_REQUEST['thickness'])) {
    $options['thickness'] = $_REQUEST['thickness'];
}
if (!empty($_REQUEST['mounting'])) {
    $options['mounting'] = $_REQUEST['mounting'];
}

$customWidth = (float)($_REQUEST['custom_width'] ?? $_REQUEST['dekota_width_cm'] ?? 0);
$customHeight = (float)($_REQUEST['custom_height'] ?? $_REQUEST['dekota_height_cm'] ?? 0);
$includeDesignService = !empty($_REQUEST['design_service']);
$selectedPackage = trim($_REQUEST['selected_package'] ?? $_REQUEST['package_id'] ?? $_REQUEST['package'] ?? 'standart');
$customPaperId = (int)($_REQUEST['custom_paper_id'] ?? 0);

if (!$productId) {
    Helper::jsonResponse(['success' => false, 'error' => 'Ürün ID belirtilmedi.'], 400);
}

$productModel = new Product();
$result = $productModel->calculatePrice($productId, $quantity, $options, $customWidth, $customHeight, $includeDesignService, $selectedPackage, $customPaperId);

// Format helper fields if missing
if (!empty($result['success'])) {
    if (empty($result['formatted_total']) && isset($result['total'])) {
        $result['formatted_total'] = Helper::formatPrice($result['total']);
    }
    if (empty($result['formatted_subtotal']) && isset($result['subtotal'])) {
        $result['formatted_subtotal'] = Helper::formatPrice($result['subtotal']);
    }
    if (empty($result['formatted_unit_price']) && isset($result['unit_price'])) {
        $result['formatted_unit_price'] = Helper::formatPrice($result['unit_price']);
    }
}

Helper::jsonResponse($result);


