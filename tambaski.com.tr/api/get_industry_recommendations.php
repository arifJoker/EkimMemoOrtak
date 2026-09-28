<?php
/**
 * TAMBASKI.COM.TR - Sektörel Çapraz Satış Önerileri API
 */
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/functions.php';

$industry = $_GET['industry'] ?? 'all';
$products = get_all_products();

$html = '';
$count = 0;
foreach ($products as $prod) {
    if ($count >= 4) break;
    $html .= '
    <div class="col-6 col-md-3">
        <div class="card h-100 border-0 shadow-xs rounded-3 p-2 text-center bg-light">
            <div class="fw-bold small text-dark mb-1 text-truncate">' . htmlspecialchars($prod['name']) . '</div>
            <div class="text-primary fw-bolder small mb-2">' . number_format($prod['base_price'], 2, ',', '.') . ' ₺</div>
            <a href="product.php?slug=' . urlencode($prod['slug']) . '" class="btn btn-sm btn-outline-primary rounded-pill py-0" style="font-size: 11px;">İncele</a>
        </div>
    </div>';
    $count++;
}

echo json_encode([
    'success' => true,
    'html' => $html
]);
