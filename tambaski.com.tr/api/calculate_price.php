<?php
/**
 * Ajax Canlı Fiyat Hesaplama Uç Noktası
 */
require_once __DIR__ . '/../config/config.php';

$rawInput = file_get_contents('php://input');
$jsonBody = !empty($rawInput) ? json_decode($rawInput, true) : null;
$inputData = is_array($jsonBody) ? array_merge($_REQUEST, $jsonBody) : $_REQUEST;

$productId = (int)($inputData['product_id'] ?? 0);
$quantity = (int)($inputData['quantity'] ?? 100);
$options = $inputData['options'] ?? [];
if (!is_array($options)) {
    $options = [];
}
if (!empty($inputData['thickness'])) {
    $options['thickness'] = $inputData['thickness'];
}
if (!empty($inputData['mounting'])) {
    $options['mounting'] = $inputData['mounting'];
}

$customWidth = (float)($inputData['custom_width'] ?? $inputData['dekota_width_cm'] ?? $inputData['width_cm'] ?? 0);
$customHeight = (float)($inputData['custom_height'] ?? $inputData['dekota_height_cm'] ?? $inputData['height_cm'] ?? 0);
$includeDesignService = !empty($inputData['design_service']);
$selectedPackage = trim($inputData['selected_package'] ?? $inputData['package_id'] ?? $inputData['package'] ?? 'standart');
$customPaperId = (int)($inputData['custom_paper_id'] ?? 0);

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


