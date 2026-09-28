<?php
require_once __DIR__ . '/config/config.php';

$productModel = new Product();
$dbConn = Database::getInstance()->getConnection();

$slug = $_GET['slug'] ?? null;
$search = $_GET['q'] ?? null;
$onlyUrgent = !empty($_GET['urgent']);

$currentCategory = null;
$categoryId = null;

if ($slug) {
    $cStmt = $dbConn->prepare("SELECT * FROM categories WHERE slug = ?");
    $cStmt->execute([$slug]);
    $currentCategory = $cStmt->fetch();
    if ($currentCategory) {
        $categoryId = $currentCategory['id'];
    }
}

$allCategories = $dbConn ? $dbConn->query("SELECT * FROM categories WHERE status = 1 ORDER BY sort_order ASC")->fetchAll() : [];

// Filtreye göre ürünleri çek
$sql = "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.status = 1";
$params = [];

if ($categoryId) {
    $sql .= " AND (p.category_id = ? OR c.parent_id = ?)";
    $params[] = $categoryId;
    $params[] = $categoryId;
}

if ($onlyUrgent) {
    $sql .= " AND p.is_urgent = 1";
}

if ($search) {
    $sql .= " AND (p.name LIKE ? OR p.short_description LIKE ? OR p.full_description LIKE ?)";
    $term = "%{$search}%";
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
}

$sql .= " ORDER BY p.sort_order ASC, p.id DESC";
$stmt = $dbConn->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

foreach ($products as &$prod) {
    $prod['base_price'] = $productModel->getStartingPrice($prod);
    $prod['starting_price'] = $prod['base_price'];
}

$pageTitle = $currentCategory ? $currentCategory['name'] . ' Baskı Modelleri & Fiyatları' : ($onlyUrgent ? '24 Saatte Acil Baskı Ürünleri' : 'Tüm Matbaa ve Baskı Ürünleri');
$pageDesc = $currentCategory ? $currentCategory['description'] : 'En kaliteli matbaa ve dijital baskı ürünlerini inceleyin.';

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-4">
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/" class="text-decoration-none text-muted">Ana Sayfa</a></li>
            <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/category.php" class="text-decoration-none text-muted">Ürünler</a></li>
            <?php if ($currentCategory): ?>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($currentCategory['name']) ?></li>
            <?php elseif ($onlyUrgent): ?>
                <li class="breadcrumb-item active text-danger" aria-current="page">Acil Baskı</li>
            <?php elseif ($search): ?>
                <li class="breadcrumb-item active" aria-current="page">"<?= htmlspecialchars($search) ?>" Arama Sonuçları</li>
            <?php else: ?>
                <li class="breadcrumb-item active" aria-current="page">Tüm Ürünler</li>
            <?php endif; ?>
        </ol>
    </nav>

    <div class="row g-4">
        
        <!-- Sol Filtreleme / Kategori Listesi -->
        <div class="col-lg-3">
            <div class="apple-card p-3 mb-4">
                <h6 class="fw-bold mb-3 pb-2 border-bottom"><i class="bi bi-funnel me-1 text-primary"></i> Kategoriler</h6>
                <div class="list-group list-group-flush small">
                    <a href="<?= SITE_URL ?>/category.php" class="list-group-item list-group-item-action border-0 rounded-3 mb-1 <?= (!$slug && !$onlyUrgent) ? 'active bg-primary text-white' : '' ?>">
                        Tüm Ürünler
                    </a>
                    <a href="<?= SITE_URL ?>/category.php?urgent=1" class="list-group-item list-group-item-action border-0 rounded-3 mb-1 text-danger fw-semibold <?= $onlyUrgent ? 'active bg-danger text-white' : '' ?>">
                        <i class="bi bi-lightning-fill"></i> 24 Saatte Acil Baskı
                    </a>
                    <?php foreach ($allCategories as $cat): ?>
                        <a href="<?= SITE_URL ?>/category.php?slug=<?= $cat['slug'] ?>" class="list-group-item list-group-item-action border-0 rounded-3 mb-1 <?= ($slug === $cat['slug']) ? 'active bg-primary text-white' : '' ?>">
                            <i class="<?= $cat['icon'] ?> me-2"></i> <?= htmlspecialchars($cat['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- E-Bayi Kutusu -->
            <div class="p-3 bg-light rounded-4 border text-center">
                <i class="bi bi-briefcase-fill text-warning fs-3 mb-2 d-block"></i>
                <h6 class="fw-bold mb-1">Ajans veya Matbaacı mısınız?</h6>
                <p class="text-muted" style="font-size: 11px;">Bayilik başvurusu yaparak toptan iskonto oranlarından faydalanın.</p>
                <a href="<?= SITE_URL ?>/dealer_apply.php" class="btn btn-sm btn-dark w-100 rounded-pill">Bayi Başvurusu</a>
            </div>
        </div>

        <!-- Sağ Ürün Listesi -->
        <div class="col-lg-9">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold mb-0">
                    <?= htmlspecialchars($currentCategory['name'] ?? ($onlyUrgent ? 'Acil Baskı Ürünleri' : ($search ? 'Arama: ' . $search : 'Tüm Baskı Ürünleri'))) ?>
                </h4>
                <span class="text-muted small"><?= count($products) ?> ürün bulundu</span>
            </div>

            <?php if (empty($products)): ?>
                <div class="apple-card p-5 text-center my-4">
                    <i class="bi bi-search text-muted" style="font-size: 48px;"></i>
                    <h5 class="fw-bold mt-3">Aradığınız kriterlere uygun ürün bulunamadı.</h5>
                    <p class="text-muted small">Lütfen başka bir kategori seçin veya arama kelimenizi kontrol edin.</p>
                    <a href="<?= SITE_URL ?>/category.php" class="btn btn-apple">Tüm Ürünleri Görüntüle</a>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($products as $prod): ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="apple-card product-card">
                                
                                <div class="product-badges">
                                    <?php if ($prod['is_urgent']): ?>
                                        <span class="badge-urgent"><i class="bi bi-lightning-fill"></i> Acil</span>
                                    <?php endif; ?>
                                    <?php if ($prod['allow_online_editor']): ?>
                                        <span class="badge-vector"><i class="bi bi-palette-fill"></i> Şablonlu</span>
                                    <?php endif; ?>
                                </div>

                                <div class="product-img-wrapper">
                                    <?php if (!empty($prod['featured_image'])): ?>
                                        <img src="<?= SITE_URL . '/' . htmlspecialchars($prod['featured_image']) ?>" alt="<?= htmlspecialchars($prod['name']) ?>">
                                    <?php else: ?>
                                        <i class="bi bi-printer text-muted" style="font-size: 54px; opacity: 0.3;"></i>
                                    <?php endif; ?>
                                </div>

                                <div class="product-body">
                                    <span class="text-muted small mb-1"><?= htmlspecialchars($prod['category_name'] ?? 'Matbaa') ?></span>
                                    <a href="<?= SITE_URL ?>/product.php?slug=<?= $prod['slug'] ?>" class="product-title"><?= htmlspecialchars($prod['name']) ?></a>
                                    <p class="product-desc"><?= htmlspecialchars($prod['short_description'] ?? '') ?></p>
                                    
                                    <div class="product-price-row">
                                        <div>
                                            <span class="product-price"><?= Helper::formatPrice($prod['base_price']) ?></span>
                                            <span class="product-price-sub">'den itibaren <small class="text-muted fw-normal" style="font-size:11px;">(+KDV)</small></span>
                                        </div>
                                        <a href="<?= SITE_URL ?>/product.php?slug=<?= $prod['slug'] ?>" class="btn btn-sm btn-apple">
                                            Seç <i class="bi bi-chevron-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>

    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
