<?php
/**
 * TAMBASKI.COM.TR - Kategori & Ürün Listeleme Sayfası
 */
require_once __DIR__ . '/includes/functions.php';

$cat_slug = $_GET['slug'] ?? null;
$search_query = trim($_GET['q'] ?? '');
$is_urgent = !empty($_GET['urgent']) || ($cat_slug === 'acil-baski');

$category = $cat_slug ? get_category_by_slug($cat_slug) : null;
$page_title = $category ? $category['name'] . " Fiyatları & Modelleri – TamBaskı" : ($search_query ? "Arama: {$search_query} – TamBaskı" : "Tüm Baskı ve Kesim Ürünleri – TamBaskı");

require_once __DIR__ . '/includes/header.php';

$products = get_all_products($cat_slug, false, $is_urgent);

// Arama Filtresi
if (!empty($search_query)) {
    $products = array_filter($products, function($p) use ($search_query) {
        return stripos($p['name'], $search_query) !== false || stripos($p['short_desc'], $search_query) !== false;
    });
}
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="index.php">Anasayfa</a></li>
            <?php if ($category): ?>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($category['name']) ?></li>
            <?php elseif ($search_query): ?>
                <li class="breadcrumb-item active">Arama: "<?= htmlspecialchars($search_query) ?>"</li>
            <?php else: ?>
                <li class="breadcrumb-item active">Tüm Ürünler</li>
            <?php endif; ?>
        </ol>
    </nav>

    <!-- Kategori Başlığı & Açıklaması -->
    <div class="p-4 bg-white rounded-4 border mb-4 shadow-sm">
        <div class="d-flex align-items-center gap-3">
            <?php if ($category): ?>
                <div class="bg-light p-3 rounded-circle text-primary">
                    <i class="bi <?= htmlspecialchars($category['icon']) ?> fs-2"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1"><?= htmlspecialchars($category['name']) ?></h2>
                    <p class="text-muted small mb-0"><?= htmlspecialchars($category['description']) ?></p>
                </div>
            <?php else: ?>
                <div>
                    <h2 class="fw-bold mb-1"><?= $search_query ? 'Arama Sonuçları' : 'Tüm Baskı, Reklam & Kesim Ürünleri' ?></h2>
                    <p class="text-muted small mb-0"><?= count($products) ?> ürün listeleniyor</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Ürün Grid -->
    <?php if (empty($products)): ?>
        <div class="text-center py-5 bg-white rounded-4 border">
            <i class="bi bi-search text-muted fs-1 mb-2 d-block"></i>
            <h5 class="fw-bold">Ürün Bulunamadı</h5>
            <p class="text-muted small">Aradığınız kriterlere uygun ürün bulunamadı. Lütfen başka bir arama yapın veya tüm ürünleri inceleyin.</p>
            <a href="category.php" class="btn btn-apple btn-apple-orange">Tüm Ürünleri Gör</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($products as $p): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="apple-card product-card">
                        <div class="product-img-wrapper">
                            <i class="bi <?= ($p['pricing_type'] === 'sqm_calculator' ? 'bi-layers' : 'bi-box-seam') ?> text-primary" style="font-size: 64px;"></i>
                        </div>
                        <div class="product-body">
                            <span class="text-muted small mb-1"><?= ucfirst(str_replace('-', ' ', $p['category_slug'])) ?></span>
                            <a href="product.php?slug=<?= urlencode($p['slug']) ?>" class="product-title"><?= htmlspecialchars($p['name']) ?></a>
                            <p class="product-desc"><?= htmlspecialchars($p['short_desc']) ?></p>
                            
                            <div class="product-price-row">
                                <div>
                                    <?php if ($p['pricing_type'] === 'sqm_calculator'): ?>
                                        <span class="product-price"><?= format_price($p['base_sqm_price']) ?></span>
                                        <span class="product-price-sub">/ m²'den başlayan</span>
                                    <?php elseif (!empty($p['packages'])): ?>
                                        <span class="product-price"><?= format_price($p['packages'][0]['price']) ?></span>
                                        <span class="product-price-sub"><?= $p['packages'][0]['quantity'] ?> Adet Paket</span>
                                    <?php else: ?>
                                        <span class="product-price"><?= format_price($p['base_setup_fee']) ?></span>
                                        <span class="product-price-sub">'den başlayan</span>
                                    <?php endif; ?>
                                </div>
                                <a href="product.php?slug=<?= urlencode($p['slug']) ?>" class="btn btn-sm btn-apple btn-apple-orange">
                                    Seç & Hesapla <i class="bi bi-chevron-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
