<?php
/**
 * TAMBASKI.COM.TR - Kategori & Ürün Listeleme Sayfası (Precision Studio Print)
 */
require_once __DIR__ . '/includes/functions.php';

$cat_slug = $_GET['slug'] ?? null;
$search_query = trim($_GET['q'] ?? '');
$is_urgent = !empty($_GET['urgent']) || ($cat_slug === 'acil-baski');

$category = $cat_slug ? get_category_by_slug($cat_slug) : null;
$page_title = $category ? $category['name'] . " Fiyatları & Modelleri | TAM BASKI STUDIO" : ($search_query ? "Arama: {$search_query} | TAM BASKI STUDIO" : "Tüm Baskı & Kesim Ürünleri | TAM BASKI STUDIO");

require_once __DIR__ . '/includes/header.php';

$products = get_all_products($cat_slug, false, $is_urgent);

// Arama Filtresi
if (!empty($search_query)) {
    $products = array_filter($products, function($p) use ($search_query) {
        return stripos($p['name'], $search_query) !== false || stripos($p['short_desc'], $search_query) !== false;
    });
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 flex-1 w-full">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs font-medium text-on-surface-variant mb-6">
        <a class="hover:text-primary transition-colors" href="index.php">Anasayfa</a>
        <span class="text-outline text-xs">/</span>
        <?php if ($category): ?>
            <span class="text-primary font-semibold"><?= htmlspecialchars($category['name']) ?></span>
        <?php elseif ($search_query): ?>
            <span class="text-primary font-semibold">Arama: "<?= htmlspecialchars($search_query) ?>"</span>
        <?php else: ?>
            <span class="text-primary font-semibold">Tüm Ürünler</span>
        <?php endif; ?>
    </nav>

    <!-- Kategori Başlığı & Açıklaması -->
    <div class="p-6 bg-surface-container-lowest rounded-2xl border border-outline-variant mb-8 shadow-xs">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-surface-container-low flex items-center justify-center text-secondary border border-outline-variant/60">
                <span class="material-symbols-outlined text-2xl">layers</span>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-primary">
                    <?= $category ? htmlspecialchars($category['name']) : ($search_query ? 'Arama Sonuçları' : 'Tüm Baskı ve Kesim Ürünleri') ?>
                </h1>
                <p class="text-xs sm:text-sm text-on-surface-variant mt-0.5">
                    <?= $category ? htmlspecialchars($category['description']) : count($products) . ' farklı ürün listeleniyor' ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Ürün Grid -->
    <?php if (empty($products)): ?>
        <div class="text-center py-12 bg-surface-container-lowest rounded-2xl border border-outline-variant">
            <span class="material-symbols-outlined text-5xl text-outline mb-2">search_off</span>
            <h3 class="text-base font-bold text-primary">Ürün Bulunamadı</h3>
            <p class="text-xs text-on-surface-variant mt-1 mb-4">Aradığınız kriterlere uygun ürün bulunamadı. Lütfen başka bir arama yapın veya tüm ürünleri inceleyin.</p>
            <a href="category.php" class="px-4 py-2 bg-primary text-white rounded-xl text-xs font-semibold hover:bg-slate-800 transition-all">
                Tüm Ürünleri Gör
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($products as $p): ?>
                <div class="group rounded-2xl border border-outline-variant/80 bg-surface-container-lowest overflow-hidden hover:shadow-xl hover:border-secondary transition-all duration-200 flex flex-col">
                    <div class="relative aspect-[4/3] bg-surface-container-low overflow-hidden flex items-center justify-center">
                        <span class="material-symbols-outlined text-6xl text-secondary/70">
                            <?= ($p['pricing_type'] === 'sqm_calculator' ? 'view_quilt' : 'badge') ?>
                        </span>
                        <div class="absolute top-3 left-3 bg-surface-container-lowest/90 backdrop-blur-md px-2.5 py-1 rounded text-primary font-label-numeric text-[11px] font-semibold">
                            <?= ucfirst(str_replace('-', ' ', $p['category_slug'] ?? 'Baskı')) ?>
                        </div>
                    </div>
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="text-base font-bold text-primary mb-1 group-hover:text-secondary transition-colors">
                                <?= htmlspecialchars($p['name']) ?>
                            </h3>
                            <p class="text-xs text-on-surface-variant mb-4 leading-relaxed line-clamp-2">
                                <?= htmlspecialchars($p['short_desc']) ?>
                            </p>
                        </div>
                        <div class="pt-4 flex items-center justify-between border-t border-outline-variant/40 mt-auto">
                            <div>
                                <span class="text-[10px] text-on-surface-variant font-label-numeric uppercase block">Fiyat</span>
                                <span class="text-base font-bold font-label-numeric text-primary">
                                    <?php if ($p['pricing_type'] === 'sqm_calculator'): ?>
                                        <?= format_price($p['base_sqm_price']) ?> / m²
                                    <?php elseif (!empty($p['packages'])): ?>
                                        <?= format_price($p['packages'][0]['price']) ?>
                                    <?php else: ?>
                                        <?= format_price($p['base_setup_fee'] ?? 450) ?>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <a class="px-3 py-1.5 bg-surface-container hover:bg-secondary hover:text-white rounded-xl text-xs font-semibold transition-colors" href="product.php?slug=<?= urlencode($p['slug']) ?>">
                                İncele
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
