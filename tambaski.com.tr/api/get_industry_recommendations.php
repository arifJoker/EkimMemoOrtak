<?php
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json; charset=utf-8');

$industrySlug = trim($_GET['industry'] ?? 'genel-kurumsal');
$excludeProductId = (int)($_GET['exclude_product_id'] ?? 0);

$productModel = new Product();
$recommended = $productModel->getRecommendedProductsByIndustry($industrySlug, $excludeProductId, 4);

$db = Database::getInstance()->getConnection();
$indStmt = $db->prepare("SELECT * FROM industries WHERE slug = ?");
$indStmt->execute([$industrySlug]);
$industryInfo = $indStmt->fetch();

$productsHtml = '';
foreach ($recommended as $p) {
    $img = !empty($p['featured_image']) ? SITE_URL . '/' . $p['featured_image'] : 'https://placehold.co/400x300/f1f5f9/0071e3?text=' . urlencode($p['name']);
    $url = SITE_URL . '/product.php?slug=' . $p['slug'];
    $priceStarting = '250,00 ₺';
    
    $productsHtml .= '
    <div class="col-md-3 col-6">
        <div class="apple-card p-2 text-center h-100 d-flex flex-column border hover-shadow" style="transition: all 0.2s ease;">
            <a href="' . $url . '" class="text-decoration-none">
                <img src="' . $img . '" class="img-fluid rounded-3 mb-2" style="height: 110px; width: 100%; object-fit: cover;" alt="' . htmlspecialchars($p['name']) . '">
                <div class="fw-bold text-dark small text-truncate" title="' . htmlspecialchars($p['name']) . '">' . htmlspecialchars($p['name']) . '</div>
                <div class="text-muted" style="font-size: 11px;">' . htmlspecialchars($p['category_name'] ?? 'Matbaa') . '</div>
            </a>
            <div class="mt-auto pt-2">
                <a href="' . $url . '" class="btn btn-sm btn-outline-primary w-100 rounded-pill py-1 fw-semibold" style="font-size: 11px;">
                    <i class="bi bi-arrow-right-circle me-1"></i> İncele & Sipariş
                </a>
            </div>
        </div>
    </div>';
}

echo json_encode([
    'success' => true,
    'industry' => $industryInfo ?: ['name' => 'Kurumsal', 'slug' => 'genel-kurumsal', 'icon' => 'bi-briefcase'],
    'html' => $productsHtml,
    'count' => count($recommended)
], JSON_UNESCAPED_UNICODE);
