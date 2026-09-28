<?php
require_once __DIR__ . '/../config/config.php';
Auth::requireAdmin();

$productModel = new Product();
$db = Database::getInstance()->getConnection();
$action = $_GET['action'] ?? 'list';

// -----------------------------------------------------------------------------
// 1. Silme İşlemi
// -----------------------------------------------------------------------------
if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    Helper::setFlash('success', 'Ürün başarıyla silindi.');
    header("Location: " . SITE_URL . "/admin/products.php");
    exit;
}

// -----------------------------------------------------------------------------
// 2. Form Gönderimi (Ekle / Güncelle)
// -----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = (int)($_POST['product_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 1);
    $sku = trim($_POST['sku'] ?? '');
    $taxRate = (float)($_POST['tax_rate'] ?? 20);
    $shortDesc = trim($_POST['short_description'] ?? '');
    $fullDesc = $_POST['full_description'] ?? '';

    // Ölçüler
    $standardWidth = (float)str_replace(',', '.', $_POST['standard_width'] ?? 8.40);
    $standardHeight = (float)str_replace(',', '.', $_POST['standard_height'] ?? 5.20);
    $isCustomSize = !empty($_POST['is_custom_size']) ? 1 : 0;
    $minWidth = (float)($_POST['min_width'] ?? 0);
    $maxWidth = (float)($_POST['max_width'] ?? 0);
    $minHeight = (float)($_POST['min_height'] ?? 0);
    $maxHeight = (float)($_POST['max_height'] ?? 0);

    // Ürün Bazlı Ek Maliyetler
    $extraFixedFee = (float)str_replace(',', '.', $_POST['extra_fixed_fee'] ?? 0.00);
    $extraPercentFee = (float)str_replace(',', '.', $_POST['extra_percent_fee'] ?? 0.00);

    // İzin Verilen Kağıtlar
    $allowedPapers = isset($_POST['allowed_papers']) && is_array($_POST['allowed_papers']) ? json_encode(array_map('intval', $_POST['allowed_papers'])) : json_encode([]);
    $allowedFinishings = isset($_POST['allowed_finishings']) && is_array($_POST['allowed_finishings']) ? json_encode(array_map('intval', $_POST['allowed_finishings'])) : json_encode([]);

    // İzin Verilen Şablonlar, Varyant Grupları ve Seçenekleri (JSON)
    $allowedTemplates = isset($_POST['allowed_templates']) && is_array($_POST['allowed_templates']) ? json_encode(array_map('intval', $_POST['allowed_templates'])) : null;
    $targetIndustries = isset($_POST['target_industries']) && is_array($_POST['target_industries']) ? json_encode(array_values(array_filter($_POST['target_industries'])), JSON_UNESCAPED_UNICODE) : json_encode([]);
    $allowedVariantGroups = isset($_POST['allowed_variant_groups']) && is_array($_POST['allowed_variant_groups']) ? json_encode(array_map('intval', $_POST['allowed_variant_groups'])) : json_encode([]);
    $allowedVariantOptions = isset($_POST['allowed_variant_options']) && is_array($_POST['allowed_variant_options']) ? json_encode(array_map('intval', $_POST['allowed_variant_options'])) : json_encode([]);

    // 4 Paket Önayarları (JSON)
    $packagePresets = json_encode([
        'ekonomik' => [
            'active'             => !empty($_POST['pkg_ekonomik_active']) ? 1 : 0,
            'supplier_cost_1000' => (float)str_replace(',', '.', $_POST['pkg_ekonomik_cost'] ?? 220.00),
            'paper_id'           => (int)($_POST['pkg_ekonomik_paper'] ?? 0),
            'fin_id'             => (int)($_POST['pkg_ekonomik_fin'] ?? 0),
            'desc'               => trim($_POST['pkg_ekonomik_desc'] ?? '250gr Bristol, Tek Yön Düz Baskı')
        ],
        'standart' => [
            'active'             => !empty($_POST['pkg_standart_active']) ? 1 : 0,
            'supplier_cost_1000' => (float)str_replace(',', '.', $_POST['pkg_standart_cost'] ?? 400.00),
            'paper_id'           => (int)($_POST['pkg_standart_paper'] ?? 0),
            'fin_id'             => (int)($_POST['pkg_standart_fin'] ?? 0),
            'desc'               => trim($_POST['pkg_standart_desc'] ?? '350gr Kuşe, Çift Taraf Mat Selefon')
        ],
        'premium'  => [
            'active'             => !empty($_POST['pkg_premium_active']) ? 1 : 0,
            'supplier_cost_1000' => (float)str_replace(',', '.', $_POST['pkg_premium_cost'] ?? 650.00),
            'paper_id'           => (int)($_POST['pkg_premium_paper'] ?? 0),
            'fin_id'             => (int)($_POST['pkg_premium_fin'] ?? 0),
            'desc'               => trim($_POST['pkg_premium_desc'] ?? 'Soft-Touch Kadife Selefon & Kabartma Lak')
        ],
        'vip'      => [
            'active'             => !empty($_POST['pkg_vip_active']) ? 1 : 0,
            'supplier_cost_1000' => (float)str_replace(',', '.', $_POST['pkg_vip_cost'] ?? 950.00),
            'paper_id'           => (int)($_POST['pkg_vip_paper'] ?? 0),
            'fin_id'             => (int)($_POST['pkg_vip_fin'] ?? 0),
            'desc'               => trim($_POST['pkg_vip_desc'] ?? 'Tuale Fantezi / 24K Altın Varak Yaldız')
        ],
    ], JSON_UNESCAPED_UNICODE);

    $allowDesignUpload = !empty($_POST['allow_design_upload']) ? 1 : 0;
    $allowOnlineEditor = !empty($_POST['allow_online_editor']) ? 1 : 0;
    $allowDesignService = !empty($_POST['allow_design_service']) ? 1 : 0;
    $designServicePrice = (float)($_POST['design_service_price'] ?? 150);
    $isFeatured = !empty($_POST['is_featured']) ? 1 : 0;
    $isUrgent = !empty($_POST['is_urgent']) ? 1 : 0;
    $status = !empty($_POST['status']) ? 1 : 0;

    $slug = !empty($_POST['slug']) ? Helper::slugify($_POST['slug']) : Helper::slugify($name);

    // -------------------------------------------------------------------------
    // 📸 Görsel Galerisi & Otomatik WebP İşleme
    // -------------------------------------------------------------------------
    $featuredImage = trim($_POST['featured_image'] ?? '');
    $galleryItems = isset($_POST['gallery_images']) && is_array($_POST['gallery_images']) ? array_values(array_filter($_POST['gallery_images'])) : [];

    // Yeni dosya yüklendiyse (Geleneksel fallback veya ilk görsel)
    if (!empty($_FILES['image']['name'])) {
        $up = Helper::uploadImageAsWebp($_FILES['image'], 'products', 85);
        if ($up['success']) {
            $featuredImage = $up['file_path'];
            if (!in_array($up['file_path'], $galleryItems)) {
                array_unshift($galleryItems, $up['file_path']);
            }
        }
    }

    // Eğer kapak görseli seçilmediyse galerinin ilk görselini kapak yap
    if (empty($featuredImage) && !empty($galleryItems)) {
        $featuredImage = $galleryItems[0];
    }

    $galleryJson = !empty($galleryItems) ? json_encode($galleryItems, JSON_UNESCAPED_UNICODE) : null;

    // -------------------------------------------------------------------------
    // 🎥 Video Dosyası & URL İşleme
    // -------------------------------------------------------------------------
    $videoPath = trim($_POST['video_path'] ?? '');
    if (!empty($_FILES['video_file']['name'])) {
        $vUp = Helper::uploadVideo($_FILES['video_file'], 'products/videos', 100);
        if ($vUp['success']) {
            $videoPath = $vUp['file_path'];
        }
    }

    $pricingMode = in_array($_POST['pricing_mode'] ?? '', ['auto_m2', 'manual']) ? $_POST['pricing_mode'] : 'auto_m2';
    $supplierId = (int)($_POST['supplier_id'] ?? 1);
    $profitMarginPercent = (float)str_replace(',', '.', $_POST['profit_margin_percent'] ?? 50.0);
    $supplierCost1000 = (float)str_replace(',', '.', $_POST['supplier_cost_1000'] ?? 0);
    $manualBasePrice = (float)str_replace(',', '.', $_POST['manual_base_price'] ?? 0);
    $basePrice = ($pricingMode === 'manual' && $manualBasePrice > 0) ? $manualBasePrice : (float)str_replace(',', '.', $_POST['base_price'] ?? 0);

    try {
        if ($productId > 0) {
            // Güncelle
            $stmt = $db->prepare("UPDATE products SET 
                category_id = ?, name = ?, slug = ?, sku = ?, short_description = ?, full_description = ?,
                pricing_mode = ?, supplier_id = ?, profit_margin_percent = ?, supplier_cost_1000 = ?, manual_base_price = ?, base_price = ?,
                tax_rate = ?, standard_width = ?, standard_height = ?, is_custom_size = ?, min_width = ?, max_width = ?, min_height = ?, max_height = ?,
                extra_fixed_fee = ?, extra_percent_fee = ?, allowed_papers = ?, allowed_finishings = ?, package_presets = ?, allowed_templates = ?, target_industries = ?, allowed_variant_groups = ?, allowed_variant_options = ?,
                allow_design_upload = ?, allow_online_editor = ?, allow_design_service = ?, design_service_price = ?,
                featured_image = ?, gallery = ?, video_path = ?, is_featured = ?, is_urgent = ?, status = ?
                WHERE id = ?");
            
            $stmt->execute([
                $categoryId, $name, $slug, $sku, $shortDesc, $fullDesc,
                $pricingMode, $supplierId, $profitMarginPercent, $supplierCost1000, $manualBasePrice, $basePrice,
                $taxRate, $standardWidth, $standardHeight, $isCustomSize, $minWidth, $maxWidth, $minHeight, $maxHeight,
                $extraFixedFee, $extraPercentFee, $allowedPapers, $allowedFinishings, $packagePresets, $allowedTemplates, $targetIndustries, $allowedVariantGroups, $allowedVariantOptions,
                $allowDesignUpload, $allowOnlineEditor, $allowDesignService, $designServicePrice,
                $featuredImage, $galleryJson, $videoPath, $isFeatured, $isUrgent, $status, $productId
            ]);

            $productModel->getStartingPrice($productId);
            Helper::setFlash('success', 'Ürün, medya galerisi, fiyatlandırma modu ve varyant ayarları başarıyla güncellendi.');
        } else {
            // Yeni Ekle
            $stmt = $db->prepare("INSERT INTO products (
                category_id, name, slug, sku, short_description, full_description,
                pricing_mode, supplier_id, profit_margin_percent, supplier_cost_1000, manual_base_price, base_price,
                tax_rate, standard_width, standard_height, is_custom_size, min_width, max_width, min_height, max_height,
                extra_fixed_fee, extra_percent_fee, allowed_papers, allowed_finishings, package_presets, allowed_templates, target_industries, allowed_variant_groups, allowed_variant_options,
                allow_design_upload, allow_online_editor, allow_design_service, design_service_price,
                featured_image, gallery, video_path, is_featured, is_urgent, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([
                $categoryId, $name, $slug, $sku, $shortDesc, $fullDesc,
                $pricingMode, $supplierId, $profitMarginPercent, $supplierCost1000, $manualBasePrice, $basePrice,
                $taxRate, $standardWidth, $standardHeight, $isCustomSize, $minWidth, $maxWidth, $minHeight, $maxHeight,
                $extraFixedFee, $extraPercentFee, $allowedPapers, $allowedFinishings, $packagePresets, $allowedTemplates, $targetIndustries, $allowedVariantGroups, $allowedVariantOptions,
                $allowDesignUpload, $allowOnlineEditor, $allowDesignService, $designServicePrice,
                $featuredImage, $galleryJson, $videoPath, $isFeatured, $isUrgent, $status
            ]);

            $productId = $db->lastInsertId();

            // Varsayılan Adet Kademeleri Ekle
            $tiers = [
                ['q' => 1000, 'm' => 1.00, 'd' => 0],
                ['q' => 2000, 'm' => 0.85, 'd' => 15],
                ['q' => 3000, 'm' => 0.78, 'd' => 22],
                ['q' => 5000, 'm' => 0.70, 'd' => 30],
                ['q' => 10000, 'm' => 0.62, 'd' => 38]
            ];
            foreach ($tiers as $t) {
                $tStmt = $db->prepare("INSERT INTO product_quantity_tiers (product_id, quantity, multiplier, discount_percent) VALUES (?, ?, ?, ?)");
                $tStmt->execute([$productId, $t['q'], $t['m'], $t['d']]);
            }

            // Otomatik başlangıç fiyatı hesapla ve kaydet
            if ($productId > 0) {
                $productModel->getStartingPrice($productId);
            }
        }
    } catch (Exception $e) {
        Helper::setFlash('danger', 'Kayıt sırasında veritabanı hatası oluştu: ' . $e->getMessage());
    }

    header("Location: " . SITE_URL . "/admin/products.php");
    exit;
}

// -----------------------------------------------------------------------------
// 3. Veri Hazırlığı
// -----------------------------------------------------------------------------
$categories = $db->query("SELECT * FROM categories ORDER BY id ASC, name ASC")->fetchAll();
$papers = $db->query("SELECT * FROM paper_types WHERE status = 1 ORDER BY gsm ASC")->fetchAll();
$finishings = $db->query("SELECT * FROM finishing_options WHERE status = 1 ORDER BY id ASC")->fetchAll();
$allIndustries = $productModel->getIndustries();

// Tüm Varyant Gruplarını ve Seçeneklerini Çek
$allVariantGroups = $db->query("SELECT * FROM variant_groups WHERE status = 1 ORDER BY sort_order ASC, id ASC")->fetchAll();
foreach ($allVariantGroups as &$vg) {
    $vOptStmt = $db->prepare("SELECT * FROM variant_options WHERE group_id = ? AND status = 1 ORDER BY sort_order ASC, id ASC");
    $vOptStmt->execute([$vg['id']]);
    $vg['options'] = $vOptStmt->fetchAll();
}

$product = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $product = $productModel->getById((int)$_GET['id']);
}

$selectedPapers = !empty($product['allowed_papers']) ? json_decode($product['allowed_papers'], true) : array_column($papers, 'id');
$selectedFinishings = !empty($product['allowed_finishings']) ? json_decode($product['allowed_finishings'], true) : [];
$selectedVariantGroups = !empty($product['allowed_variant_groups']) ? json_decode($product['allowed_variant_groups'], true) : array_column($allVariantGroups, 'id');
$selectedVariantOptions = !empty($product['allowed_variant_options']) ? json_decode($product['allowed_variant_options'], true) : [];
$selectedIndustries = !empty($product['target_industries']) ? json_decode($product['target_industries'], true) : array_column($allIndustries, 'slug');
$galleryImages = !empty($product['gallery']) ? json_decode($product['gallery'], true) : [];

$presets = !empty($product['package_presets']) ? json_decode($product['package_presets'], true) : [];

$pageTitle = ($action === 'edit') ? 'Ürün Düzenle: ' . htmlspecialchars($product['name'] ?? '') : 'Ürün Yönetimi';
require_once __DIR__ . '/header.php';
?>

<style>
/* 🌈 Canlı, Renkli ve Yüksek Kontrastlı Modern Admin Tasarımı */
:root {
    --color-blue: #0284c7;
    --color-purple: #7c3aed;
    --color-pink: #db2777;
    --color-amber: #d97706;
    --color-emerald: #059669;
}

.colorful-header-card {
    border-radius: 16px;
    border: 1px solid rgba(0,0,0,0.06);
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    overflow: hidden;
    background: #ffffff;
    transition: transform 0.2s, box-shadow 0.2s;
}

.card-top-accent {
    padding: 14px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-weight: 700;
    font-size: 15px;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

/* 5 Canlı Tema Kartı */
.card-theme-blue .card-top-accent {
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
    color: #0369a1;
    border-left: 6px solid #0284c7;
}
.card-theme-purple .card-top-accent {
    background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
    color: #6b21a8;
    border-left: 6px solid #7c3aed;
}
.card-theme-emerald .card-top-accent {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
    border-left: 6px solid #059669;
}
.card-theme-amber .card-top-accent {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
    border-left: 6px solid #d97706;
}
.card-theme-pink .card-top-accent {
    background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%);
    color: #9d174d;
    border-left: 6px solid #db2777;
}

/* Renkli İkon Kutuları */
.icon-box-colorful {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    margin-right: 8px;
}
.card-theme-blue .icon-box-colorful { background: #0284c7; color: #fff; }
.card-theme-purple .icon-box-colorful { background: #7c3aed; color: #fff; }
.card-theme-emerald .icon-box-colorful { background: #059669; color: #fff; }
.card-theme-amber .icon-box-colorful { background: #d97706; color: #fff; }
.card-theme-pink .icon-box-colorful { background: #db2777; color: #fff; }

/* 🌟 Renkli Sekme Çubuğu (Color Coded Tabs) */
.nav-pills-colorful {
    gap: 8px;
    background: #f1f5f9;
    padding: 8px;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
}
.nav-pills-colorful .nav-link {
    border-radius: 12px;
    padding: 10px 18px;
    font-size: 13.5px;
    font-weight: 700;
    color: #475569;
    background: transparent;
    border: 2px solid transparent;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.nav-pills-colorful .nav-link:hover {
    background: #ffffff;
    color: #0f172a;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

/* Aktif Sekme Renkleri */
.nav-pills-colorful .nav-link.tab-c-blue.active {
    background: #0284c7;
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(2,132,199,0.35);
}
.nav-pills-colorful .nav-link.tab-c-pink.active {
    background: #db2777;
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(219,39,119,0.35);
}
.nav-pills-colorful .nav-link.tab-c-amber.active {
    background: #d97706;
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(217,119,6,0.35);
}
.nav-pills-colorful .nav-link.tab-c-purple.active {
    background: #7c3aed;
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(124,58,237,0.35);
}
.nav-pills-colorful .nav-link.tab-c-emerald.active {
    background: #059669;
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(5,150,105,0.35);
}

/* Form Input Güzelleştirmeleri */
.form-control, .form-select {
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    padding: 8px 12px;
    font-size: 13.5px;
    transition: all 0.15s ease;
}
.form-control:focus, .form-select:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 3px rgba(2,132,199,0.15);
}
.form-label {
    margin-bottom: 5px;
    color: #1e293b;
}

/* Sürükle Bırak Alanı */
.media-dropzone {
    border: 2px dashed #f472b6;
    border-radius: 16px;
    background: linear-gradient(135deg, #fdf2f8 0%, #fff 100%);
    padding: 35px 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
}
.media-dropzone:hover, .media-dropzone.dragover {
    border-color: #db2777;
    background: #fce7f3;
    transform: scale(1.005);
}

.gallery-thumb-card {
    position: relative;
    border-radius: 14px;
    overflow: hidden;
    background: #fff;
    border: 2px solid #e2e8f0;
    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    transition: all 0.2s;
}
.gallery-thumb-card:hover {
    border-color: #0284c7;
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}
.gallery-thumb-card img {
    width: 100%;
    height: 140px;
    object-fit: cover;
}

.sticky-admin-actions {
    position: sticky;
    bottom: 20px;
    z-index: 99;
    background: rgba(15, 23, 42, 0.92);
    backdrop-filter: blur(12px);
    border-radius: 18px;
    padding: 14px 28px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.25);
    border: 1px solid rgba(255,255,255,0.15);
    color: #fff;
}
</style>

<?php if ($action === 'list'): ?>
    <!-- ========================================================================= -->
    <!-- 📋 ÜRÜN LİSTE GÖRÜNÜMÜ -->
    <!-- ========================================================================= -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-box-seam text-primary me-2"></i>Matbaa Ürün Yönetimi</h4>
            <p class="text-muted small mb-0">Ürünlerinizi, 4'lü paketleri, çoklu WebP medya galerilerini ve sektör çapraz satışlarını yönetin.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= SITE_URL ?>/admin/variants.php" class="btn btn-outline-secondary rounded-pill px-3 shadow-xs">
                <i class="bi bi-diagram-3 me-1"></i> Varyant Havuzu
            </a>
            <a href="<?= SITE_URL ?>/admin/products.php?action=create" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-plus-lg me-1"></i> Yeni Ürün Ekle
            </a>
        </div>
    </div>

    <!-- Ürün İstatistikleri -->
    <?php
    $totalProds = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $activeProds = $db->query("SELECT COUNT(*) FROM products WHERE status = 1")->fetchColumn();
    $editorProds = $db->query("SELECT COUNT(*) FROM products WHERE allow_online_editor = 1")->fetchColumn();
    ?>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-xs rounded-4 p-3 bg-white border-start border-4 border-primary">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Toplam Kayıtlı Ürün</div>
                        <div class="fs-4 fw-bold text-dark"><?= $totalProds ?> Adet</div>
                    </div>
                    <div class="rounded-circle bg-primary-subtle p-3 text-primary"><i class="bi bi-boxes fs-4"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-xs rounded-4 p-3 bg-white border-start border-4 border-success">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Yayındaki Aktif Ürünler</div>
                        <div class="fs-4 fw-bold text-success"><?= $activeProds ?> Ürün</div>
                    </div>
                    <div class="rounded-circle bg-success-subtle p-3 text-success"><i class="bi bi-check2-circle fs-4"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-xs rounded-4 p-3 bg-white border-start border-4 border-info">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">75+ Vektörel Şablon & Editör Aktif</div>
                        <div class="fs-4 fw-bold text-info"><?= $editorProds ?> Ürün</div>
                    </div>
                    <div class="rounded-circle bg-info-subtle p-3 text-info"><i class="bi bi-vector-pen fs-4"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ürün Listesi Tablosu -->
    <div class="card border-0 shadow-xs rounded-4 overflow-hidden bg-white">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-muted text-uppercase" style="font-size: 11px;">
                            <th class="ps-4">Görsel</th>
                            <th>Ürün Adı & SKU</th>
                            <th>Kategori</th>
                            <th>Standart Ebat</th>
                            <th>Özellikler & Medya</th>
                            <th>Durum</th>
                            <th class="text-end pe-4">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $prods = $db->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC")->fetchAll();
                        if (empty($prods)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-box2 text-secondary fs-1 d-block mb-2"></i>
                                    Henüz ürün eklenmemiş. Hemen sağ üstten yeni ürün ekleyin.
                                </td>
                            </tr>
                        <?php else: 
                            foreach ($prods as $p): 
                                $gCount = !empty($p['gallery']) ? count(json_decode($p['gallery'], true) ?: []) : 0;
                            ?>
                                <tr>
                                    <td class="ps-4" style="width: 70px;">
                                        <?php if (!empty($p['featured_image'])): ?>
                                            <img src="<?= SITE_URL ?>/<?= $p['featured_image'] ?>" alt="" class="rounded-3 border" style="width: 52px; height: 52px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-3 bg-light d-flex align-items-center justify-content-center text-muted border" style="width: 52px; height: 52px;">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($p['name']) ?></div>
                                        <div class="small text-muted font-monospace" style="font-size: 11px;">SKU: <?= htmlspecialchars($p['sku'] ?: '—') ?> | slug: /<?= $p['slug'] ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($p['category_name'] ?: 'Genel') ?></span>
                                    </td>
                                    <td>
                                        <span class="small fw-semibold"><?= $p['standard_width'] ?> x <?= $p['standard_height'] ?> cm</span>
                                        <?php if ($p['is_custom_size']): ?>
                                            <span class="badge bg-info-subtle text-info d-block mt-1" style="font-size: 10px;">Özel Ebat Açık</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <?php if ($p['allow_online_editor']): ?>
                                                <span class="badge bg-purple text-white" style="background-color: #8b5cf6; font-size:10px;"><i class="bi bi-vector-pen me-1"></i> Editör Aktif</span>
                                            <?php endif; ?>
                                            <?php if ($gCount > 0): ?>
                                                <span class="badge bg-success-subtle text-success" style="font-size:10px;"><i class="bi bi-images me-1"></i> <?= $gCount ?> Fotoğraf (WebP)</span>
                                            <?php endif; ?>
                                            <?php if (!empty($p['video_path'])): ?>
                                                <span class="badge bg-danger-subtle text-danger" style="font-size:10px;"><i class="bi bi-camera-video-fill me-1"></i> Video</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($p['status']): ?>
                                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i> Yayında</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1">Taslak</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= SITE_URL ?>/product.php?slug=<?= $p['slug'] ?>" target="_blank" class="btn btn-outline-secondary" title="Sitede İncele">
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                            <a href="<?= SITE_URL ?>/admin/products.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-outline-primary" title="Düzenle">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a href="<?= SITE_URL ?>/admin/products.php?action=delete&id=<?= $p['id'] ?>" class="btn btn-outline-danger" title="Sil" onclick="return confirm('Bu ürünü silmek istediğinize emin misiniz?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; 
                        endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- ========================================================================= -->
    <!-- ✏️ ÜRÜN EKLEME / DÜZENLEME SEKMELİ CANLI RENKLİ FORMU -->
    <!-- ========================================================================= -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <a href="<?= SITE_URL ?>/admin/products.php" class="text-muted small text-decoration-none d-inline-flex align-items-center mb-1">
                <i class="bi bi-arrow-left me-1"></i> Ürün Listesine Dön
            </a>
            <h4 class="fw-bold mb-0">
                <?= ($action === 'edit') ? 'Ürün Düzenle: <span class="text-primary">' . htmlspecialchars($product['name']) . '</span>' : 'Yeni Matbaa Ürünü Ekle' ?>
            </h4>
        </div>
        <?php if ($action === 'edit'): ?>
            <a href="<?= SITE_URL ?>/product.php?slug=<?= $product['slug'] ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-xs">
                <i class="bi bi-eye me-1"></i> Ürünü Sitede Gör
            </a>
        <?php endif; ?>
    </div>

    <form action="<?= SITE_URL ?>/admin/products.php" method="POST" enctype="multipart/form-data" id="adminProductForm">
        <input type="hidden" name="product_id" value="<?= $product['id'] ?? 0 ?>">
        <input type="hidden" name="featured_image" id="inputFeaturedImage" value="<?= htmlspecialchars($product['featured_image'] ?? '') ?>">
        <input type="hidden" name="video_path" id="inputVideoPath" value="<?= htmlspecialchars($product['video_path'] ?? '') ?>">

        <!-- 🌟 Renkli Adım Sekmeleri -->
        <ul class="nav nav-pills nav-pills-colorful mb-4" id="productTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link tab-c-blue active" id="tab-general-btn" data-bs-toggle="tab" data-bs-target="#tab-general" type="button" role="tab">
                    <i class="bi bi-pencil-square fs-6"></i> 1. Genel Bilgiler &amp; Ebatlar
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link tab-c-pink" id="tab-media-btn" data-bs-toggle="tab" data-bs-target="#tab-media" type="button" role="tab">
                    <i class="bi bi-images fs-6"></i> 2. Fotoğraf &amp; Video Galerisi <span class="badge bg-white text-dark ms-1 shadow-2xs" style="font-size:10px;">WebP</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link tab-c-amber" id="tab-packages-btn" data-bs-toggle="tab" data-bs-target="#tab-packages" type="button" role="tab">
                    <i class="bi bi-boxes fs-6"></i> 3. 4'lü Akıllı Paketler
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link tab-c-purple" id="tab-variants-btn" data-bs-toggle="tab" data-bs-target="#tab-variants" type="button" role="tab">
                    <i class="bi bi-diagram-3-fill fs-6"></i> 4. Varyantlar &amp; Kağıtlar
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link tab-c-emerald" id="tab-industries-btn" data-bs-toggle="tab" data-bs-target="#tab-industries" type="button" role="tab">
                    <i class="bi bi-funnel-fill fs-6"></i> 5. Hedef Sektörler &amp; Şablonlar
                </button>
            </li>
        </ul>

        <!-- Sekme İçerikleri -->
        <div class="tab-content" id="productTabsContent">
            
            <!-- ========================================================================= -->
            <!-- 📌 SEKME 1: GENEL BİLGİLER & EBATLAR -->
            <!-- ========================================================================= -->
            <div class="tab-pane fade show active" id="tab-general" role="tabpanel">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <!-- Mavi Temalı Kart: Temel Bilgiler -->
                        <div class="colorful-header-card card-theme-blue mb-4">
                            <div class="card-top-accent">
                                <div><span class="icon-box-colorful"><i class="bi bi-card-text"></i></span> Temel Ürün Bilgileri</div>
                                <span class="badge bg-white text-primary fw-bold">Zorunlu Alanlar</span>
                            </div>
                            <div class="p-4">
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label class="form-label small fw-bold">Ürün Adı <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control fw-semibold" required value="<?= htmlspecialchars($product['name'] ?? '') ?>" placeholder="Örn: Ekonomik Mat Kuşe Kartvizit">
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label class="form-label small fw-bold mb-0">Kategori <span class="text-danger">*</span></label>
                                            <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 rounded-pill" style="font-size:11px;" data-bs-toggle="modal" data-bs-target="#modalQuickAddCategory">
                                                <i class="bi bi-plus-circle me-1"></i> Yeni Kategori
                                            </button>
                                        </div>
                                        <select name="category_id" id="productCategorySelect" class="form-select fw-semibold" required>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?= $cat['id'] ?>" <?= ($product['category_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($cat['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Özel URL (Slug)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-muted small">/product.php?slug=</span>
                                            <input type="text" name="slug" class="form-control font-monospace" value="<?= htmlspecialchars($product['slug'] ?? '') ?>" placeholder="test-kasa">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Stok Kodu (SKU)</label>
                                        <input type="text" name="sku" class="form-control font-monospace" value="<?= htmlspecialchars($product['sku'] ?? '') ?>" placeholder="KVZ-350-MAT">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">KDV Oranı (%)</label>
                                        <div class="input-group">
                                            <input type="number" name="tax_rate" class="form-control" value="<?= $product['tax_rate'] ?? 20 ?>" min="0" max="100">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Kısa Açıklama (Özet Vurgu)</label>
                                        <textarea name="short_description" class="form-control" rows="2" placeholder="Ürün sayfasında başlığın hemen altında çıkan 1-2 cümlelik vurucu açıklama..."><?= htmlspecialchars($product['short_description'] ?? '') ?></textarea>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Detaylı Ürün Açıklaması &amp; Teknik Özellikler</label>
                                        <textarea name="full_description" class="form-control" rows="5" placeholder="Kağıt özellikleri, selefon türü, teslimat süresi ve teknik detaylar..."><?= htmlspecialchars($product['full_description'] ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mor Temalı Kart: Ebat & Yerleşim -->
                        <div class="colorful-header-card card-theme-purple">
                            <div class="card-top-accent">
                                <div><span class="icon-box-colorful"><i class="bi bi-aspect-ratio"></i></span> Ebat &amp; 70x100 Tabaka Yerleşim Ölçüleri</div>
                                <span class="badge bg-white text-purple fw-bold" style="color: #7c3aed;">Fiyatlandırma Motoru</span>
                            </div>
                            <div class="p-4">
                                <div class="row g-3 align-items-center">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Standart Genişlik / En (cm)</label>
                                        <div class="input-group">
                                            <input type="text" name="standard_width" class="form-control fw-bold" value="<?= $product['standard_width'] ?? '8.40' ?>" required>
                                            <span class="input-group-text bg-light fw-bold">cm</span>
                                        </div>
                                        <div class="text-muted small mt-1" style="font-size: 11px;">Kartvizit için standart 8.4 cm</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Standart Yükseklik / Boy (cm)</label>
                                        <div class="input-group">
                                            <input type="text" name="standard_height" class="form-control fw-bold" value="<?= $product['standard_height'] ?? '5.20' ?>" required>
                                            <span class="input-group-text bg-light fw-bold">cm</span>
                                        </div>
                                        <div class="text-muted small mt-1" style="font-size: 11px;">Kartvizit için standart 5.2 cm</div>
                                    </div>

                                    <div class="col-12 pt-3 border-top mt-3">
                                        <div class="form-check form-switch p-2 bg-light rounded-3 border mb-2">
                                            <input class="form-check-input ms-0 me-2" type="checkbox" name="is_custom_size" id="isCustomSizeSwitch" value="1" <?= !empty($product['is_custom_size']) ? 'checked' : '' ?> onchange="document.getElementById('customLimitsBox').style.display = this.checked ? 'flex' : 'none'">
                                            <label class="form-check-label fw-bold text-dark" for="isCustomSizeSwitch">
                                                <i class="bi bi-sliders text-purple me-1"></i> Müşteri Özel Ölçü (En x Boy) Belirleyebilsin
                                            </label>
                                        </div>
                                        <div class="row g-2 p-3 bg-purple-subtle rounded-3" id="customLimitsBox" style="display: <?= !empty($product['is_custom_size']) ? 'flex' : 'none' ?>; background: #faf5ff;">
                                            <div class="col-3">
                                                <label class="small text-muted mb-1">Min En (cm)</label>
                                                <input type="number" step="0.1" name="min_width" class="form-control form-control-sm" placeholder="Min En" value="<?= $product['min_width'] ?? 4 ?>">
                                            </div>
                                            <div class="col-3">
                                                <label class="small text-muted mb-1">Maks En (cm)</label>
                                                <input type="number" step="0.1" name="max_width" class="form-control form-control-sm" placeholder="Maks En" value="<?= $product['max_width'] ?? 100 ?>">
                                            </div>
                                            <div class="col-3">
                                                <label class="small text-muted mb-1">Min Boy (cm)</label>
                                                <input type="number" step="0.1" name="min_height" class="form-control form-control-sm" placeholder="Min Boy" value="<?= $product['min_height'] ?? 4 ?>">
                                            </div>
                                            <div class="col-3">
                                                <label class="small text-muted mb-1">Maks Boy (cm)</label>
                                                <input type="number" step="0.1" name="max_height" class="form-control form-control-sm" placeholder="Maks Boy" value="<?= $product['max_height'] ?? 100 ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Yeşil Temalı Kart: Yayın Durumu -->
                        <div class="colorful-header-card card-theme-emerald mb-4">
                            <div class="card-top-accent">
                                <div><span class="icon-box-colorful"><i class="bi bi-toggle-on"></i></span> Yayın &amp; Vitrin Durumu</div>
                            </div>
                            <div class="p-3">
                                <div class="form-check form-switch p-2 bg-light rounded-3 border mb-2">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="status" id="statusSwitch" value="1" <?= (!isset($product['status']) || $product['status']) ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-bold text-success" for="statusSwitch">
                                        <i class="bi bi-check-circle-fill me-1"></i> Ürünü Yayına Al (Aktif)
                                    </label>
                                </div>

                                <div class="form-check form-switch p-2 bg-light rounded-3 border mb-2">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="is_featured" id="featuredSwitch" value="1" <?= !empty($product['is_featured']) ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-semibold text-dark" for="featuredSwitch">
                                        <i class="bi bi-star-fill text-warning me-1"></i> Anasayfa Vitrininde Göster
                                    </label>
                                </div>

                                <div class="form-check form-switch p-2 bg-light rounded-3 border">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="is_urgent" id="urgentSwitch" value="1" <?= !empty($product['is_urgent']) ? 'checked' : '' ?>>
                                    <label class="form-check-label text-danger fw-bold" for="urgentSwitch">
                                        <i class="bi bi-lightning-charge-fill me-1"></i> 24 Saatte Acil Baskı Rozeti
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Amber Temalı Kart: Fiyatlandırma Modu & Kâr Marjı -->
                        <div class="colorful-header-card card-theme-amber">
                            <div class="card-top-accent">
                                <div><span class="icon-box-colorful"><i class="bi bi-cash-stack"></i></span> Fiyatlandırma Modu &amp; Kâr Marjı</div>
                            </div>
                            <div class="p-3">
                                <!-- Fiyatlandırma Modu Seçimi -->
                                <div class="mb-3">
                                    <label class="form-label small fw-bold d-block">Hesaplama Yöntemi</label>
                                    <?php 
                                        $currPMode = $product['pricing_mode'] ?? 'auto_m2';
                                    ?>
                                    <div class="btn-group w-100 mb-2" role="group">
                                        <input type="radio" class="btn-check" name="pricing_mode" id="pm_auto" value="auto_m2" <?= $currPMode === 'auto_m2' ? 'checked' : '' ?> onchange="togglePricingModeInputs()">
                                        <label class="btn btn-outline-primary btn-sm fw-semibold" for="pm_auto">
                                            <i class="bi bi-lightning-charge-fill me-1"></i> ⚡ Otomatik m²
                                        </label>

                                        <input type="radio" class="btn-check" name="pricing_mode" id="pm_manual" value="manual" <?= $currPMode === 'manual' ? 'checked' : '' ?> onchange="togglePricingModeInputs()">
                                        <label class="btn btn-outline-secondary btn-sm fw-semibold" for="pm_manual">
                                            <i class="bi bi-pencil-square me-1"></i> ✍️ Manuel Sabit
                                        </label>
                                    </div>
                                    <div class="text-muted" style="font-size: 10.5px;">
                                        <strong>Otomatik m²:</strong> Tabaka dizilimi ve hammadde maliyetine kâr marjı ekler.<br>
                                        <strong>Manuel:</strong> Aşağıdaki sabit tutarı baz alır.
                                    </div>
                                </div>

                                <!-- Otomatik Mod Alanları -->
                                <div id="autoPricingInputs" style="display: <?= $currPMode === 'auto_m2' ? 'block' : 'none' ?>;">
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Tedarikçi Firma</label>
                                        <select name="supplier_id" id="prodSupplierSelect" class="form-select form-select-sm">
                                            <?php 
                                            $prodSuppliers = $productModel->getSuppliers(false);
                                            $currSuppId = $product['supplier_id'] ?? 1;
                                            foreach ($prodSuppliers as $ps): 
                                            ?>
                                                <option value="<?= $ps['id'] ?>" <?= $currSuppId == $ps['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($ps['name']) ?> (%<?= (int)$ps['default_margin'] ?> Kâr)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Hedef Kâr Marjı (%)</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">%</span>
                                            <input type="number" step="1" name="profit_margin_percent" class="form-control" value="<?= $product['profit_margin_percent'] ?? '100.00' ?>">
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Tedarikçi Alış Fiyatı (1.000 Adet ₺)</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">₺</span>
                                            <input type="number" step="0.5" name="supplier_cost_1000" class="form-control" value="<?= $product['supplier_cost_1000'] ?? '450.00' ?>">
                                        </div>
                                        <div class="text-muted" style="font-size: 10.5px;">Türmatsan veya toptancı 1000 adet ham maliyeti</div>
                                    </div>
                                </div>

                                <!-- Manuel Mod Alanları -->
                                <div id="manualPricingInputs" style="display: <?= $currPMode === 'manual' ? 'block' : 'none' ?>;" class="mb-3">
                                    <label class="form-label small fw-bold">Manuel Taban Satış Fiyatı (₺)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.01" name="manual_base_price" class="form-control" value="<?= $product['manual_base_price'] ?? $product['base_price'] ?? '0.00' ?>">
                                        <span class="input-group-text">₺</span>
                                    </div>
                                    <div class="text-muted" style="font-size: 10.5px;">1.000 adet veya başlangıç sabit satış fiyatı</div>
                                </div>

                                <hr class="my-2 opacity-25">

                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Sabit Kalıp / Montaj Bedeli (₺)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="extra_fixed_fee" class="form-control" value="<?= $product['extra_fixed_fee'] ?? '0.00' ?>">
                                        <span class="input-group-text">₺</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label small fw-bold">Ekstra Yüzde Farkı (%)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="extra_percent_fee" class="form-control" value="<?= $product['extra_percent_fee'] ?? '0.00' ?>">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                        function togglePricingModeInputs() {
                            const isAuto = document.getElementById('pm_auto').checked;
                            const autoBox = document.getElementById('autoPricingInputs');
                            const manualBox = document.getElementById('manualPricingInputs');
                            if (autoBox) autoBox.style.display = isAuto ? 'block' : 'none';
                            if (manualBox) manualBox.style.display = isAuto ? 'none' : 'block';
                        }
                        </script>
                    </div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- 📸 SEKME 2: FOTOĞRAF & VİDEO GALERİSİ (Otomatik WebP) -->
            <!-- ========================================================================= -->
            <div class="tab-pane fade" id="tab-media" role="tabpanel">
                <div class="colorful-header-card card-theme-pink mb-4">
                    <div class="card-top-accent">
                        <div><span class="icon-box-colorful"><i class="bi bi-images"></i></span> Çoklu Fotoğraf Galerisi (Otomatik WebP Sıkıştırma)</div>
                        <span class="badge bg-success text-white py-1 px-3 rounded-pill fw-bold">
                            <i class="bi bi-lightning-charge-fill me-1"></i> Auto-WebP Aktif
                        </span>
                    </div>
                    <div class="p-4">
                        <div class="media-dropzone mb-4" id="photoDropzone">
                            <i class="bi bi-cloud-arrow-up-fill text-danger" style="font-size: 46px;"></i>
                            <div class="fw-bold fs-6 mt-2 text-dark">Fotoğrafları Buraya Sürükleyip Bırakın veya Seçmek İçin Tıklayın</div>
                            <div class="text-muted small mt-1">Tek seferde birden fazla görsel seçebilirsiniz (JPG, PNG otomatik olarak <strong>WebP</strong> formatına dönüştürülür).</div>
                            <input type="file" id="multiImageInput" class="d-none" multiple accept="image/jpeg,image/png,image/webp,image/gif">
                        </div>

                        <!-- Yükleme Durumu & Spinner -->
                        <div id="mediaUploadSpinner" class="alert alert-info py-2 px-3 small align-items-center gap-2 mb-3" style="display: none;">
                            <span class="spinner-border spinner-border-sm text-primary"></span>
                            <span id="mediaUploadProgressText">Görseller yükleniyor ve WebP formatına dönüştürülüyor...</span>
                        </div>

                        <!-- Galeri Önizleme Kartları Grid -->
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="fw-bold small text-dark mb-0"><i class="bi bi-collection-play me-1 text-primary"></i> Yüklü Fotoğraflar:</label>
                            <span class="text-muted small" style="font-size:11px;">Yıldız simgesine basarak kapak görseli belirleyebilirsiniz.</span>
                        </div>
                        <div class="row g-3" id="galleryGrid">
                            <?php if (empty($galleryImages) && !empty($product['featured_image'])): 
                                $galleryImages = [$product['featured_image']];
                            endif; ?>

                            <?php foreach ($galleryImages as $idx => $img): 
                                $isFeatured = ($product['featured_image'] ?? '') === $img || $idx === 0;
                            ?>
                                <div class="col-md-3 col-sm-4 col-6 gallery-item-wrapper" data-path="<?= htmlspecialchars($img) ?>">
                                    <div class="gallery-thumb-card">
                                        <img src="<?= SITE_URL ?>/<?= htmlspecialchars($img) ?>" alt="Ürün Görseli">
                                        <div class="card-actions">
                                            <button type="button" class="btn btn-sm btn-dark btn-set-featured p-1 px-2 rounded-circle shadow-xs" title="Kapak Görseli Yap" onclick="setAsFeatured('<?= htmlspecialchars($img) ?>', this)">
                                                <i class="bi bi-star-fill text-warning"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger btn-del-image p-1 px-2 rounded-circle shadow-xs" title="Sil" onclick="removeGalleryItem(this)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                        <span class="badge bg-primary badge-featured <?= $isFeatured ? '' : 'd-none' ?>">
                                            <i class="bi bi-star-fill me-1"></i> Kapak Görseli
                                        </span>
                                        <input type="hidden" name="gallery_images[]" value="<?= htmlspecialchars($img) ?>">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Video Yönetimi -->
                <div class="colorful-header-card card-theme-purple">
                    <div class="card-top-accent">
                        <div><span class="icon-box-colorful"><i class="bi bi-camera-video-fill"></i></span> Ürün Tanıtım Videosu (MP4, WebM)</div>
                    </div>
                    <div class="p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Video Dosyası Yükle (Maks 100 MB)</label>
                                <div class="input-group">
                                    <input type="file" id="videoFileInput" class="form-control" accept="video/mp4,video/webm,video/quicktime">
                                    <button type="button" class="btn btn-danger px-3 fw-bold" id="btnUploadVideo">
                                        <i class="bi bi-upload me-1"></i> Videoyu Yükle
                                    </button>
                                </div>
                                <div id="videoUploadStatus" class="mt-2 small" style="display: none;"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Veya Video Dosya Yolu / URL</label>
                                <input type="text" id="videoUrlManual" class="form-control font-monospace" placeholder="uploads/products/videos/tanitim.mp4" value="<?= htmlspecialchars($product['video_path'] ?? '') ?>" onchange="document.getElementById('inputVideoPath').value = this.value; updateVideoPreview();">
                            </div>

                            <div class="col-12" id="videoPreviewContainer" style="<?= empty($product['video_path']) ? 'display: none;' : '' ?>">
                                <label class="fw-bold small text-dark mb-2 d-block">Video Önizleme:</label>
                                <div class="rounded-4 overflow-hidden border bg-dark d-inline-block shadow-sm" style="max-width: 480px;">
                                    <video id="adminVideoPlayer" src="<?= !empty($product['video_path']) ? (str_starts_with($product['video_path'], 'http') ? $product['video_path'] : SITE_URL . '/' . $product['video_path']) : '' ?>" controls style="max-width: 100%; height: auto; max-height: 260px; display: block;"></video>
                                </div>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="clearVideo()">
                                        <i class="bi bi-trash me-1"></i> Videoyu Kaldır
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- 📦 SEKME 3: 4'LÜ AKILLI PAKETLER -->
            <!-- ========================================================================= -->
            <div class="tab-pane fade" id="tab-packages" role="tabpanel">
                <div class="colorful-header-card card-theme-amber mb-4">
                    <div class="card-top-accent">
                        <div><span class="icon-box-colorful"><i class="bi bi-boxes"></i></span> 4'lü Akıllı Hazır Paket Yapılandırması</div>
                        <span class="badge bg-white text-dark fw-bold">Müşteri Vitrin Kartları</span>
                    </div>
                    <div class="p-4">
                        <div class="row g-3">
                            <?php
                            $pkgsConfig = [
                                'ekonomik' => ['title' => '1. Ekonomik Paket', 'default_desc' => '350gr Kuşe, Mat Selefon, Düz Kesim', 'badge' => 'En Uygun Fiyat', 'border_color' => '#64748b', 'bg' => '#f8fafc', 'badge_class' => 'bg-secondary'],
                                'standart' => ['title' => '2. Standart Paket', 'default_desc' => '350gr Kuşe, Çift Taraf Mat, Oval Köşe', 'badge' => 'En Çok Satan', 'border_color' => '#0284c7', 'bg' => '#f0f9ff', 'badge_class' => 'bg-primary'],
                                'premium'  => ['title' => '3. Premium Paket',  'default_desc' => 'Soft-Touch Kadife Selefon & Kabartma Lak', 'badge' => 'Lüks Doku', 'border_color' => '#7c3aed', 'bg' => '#faf5ff', 'badge_class' => 'bg-purple', 'style' => 'background-color:#7c3aed;color:#fff;'],
                                'vip'      => ['title' => '4. VIP Prestij Paket', 'default_desc' => 'Tuale Fantezi / 24K Altın Varak Yaldız', 'badge' => 'Maksimum Prestij', 'border_color' => '#d97706', 'bg' => '#fffbeb', 'badge_class' => 'bg-warning text-dark']
                            ];

                            foreach ($pkgsConfig as $pKey => $pCfg):
                                $pData = $presets[$pKey] ?? [];
                                $isActive = isset($pData['active']) ? $pData['active'] : 1;
                            ?>
                                <div class="col-md-6">
                                    <div class="card rounded-4 p-3 h-100 shadow-2xs" style="border: 2px solid <?= $pCfg['border_color'] ?>; background: <?= $pCfg['bg'] ?>;">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="form-check form-switch m-0">
                                                    <input class="form-check-input" type="checkbox" name="pkg_<?= $pKey ?>_active" id="pkg_<?= $pKey ?>_active" value="1" <?= $isActive ? 'checked' : '' ?>>
                                                </div>
                                                <label class="fw-bold text-dark mb-0 cursor-pointer fs-6" for="pkg_<?= $pKey ?>_active">
                                                    <?= $pCfg['title'] ?>
                                                </label>
                                            </div>
                                            <span class="badge <?= $pCfg['badge_class'] ?>" style="<?= $pCfg['style'] ?? '' ?>"><?= $pCfg['badge'] ?></span>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Kart Açıklaması</label>
                                            <input type="text" name="pkg_<?= $pKey ?>_desc" class="form-control form-control-sm bg-white" value="<?= htmlspecialchars($pData['desc'] ?? $pCfg['default_desc']) ?>">
                                        </div>

                                        <div class="mb-3 p-2 rounded-3 bg-white border">
                                            <label class="form-label small fw-bold text-primary mb-1"><i class="bi bi-lightning-charge-fill me-1"></i>Paket Toptancı Maliyeti (1.000 Adet ₺)</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">₺</span>
                                                <input type="number" step="0.5" name="pkg_<?= $pKey ?>_cost" class="form-control form-control-sm fw-bold" value="<?= $pData['supplier_cost_1000'] ?? ($pKey === 'ekonomik' ? '220.00' : ($pKey === 'standart' ? '400.00' : ($pKey === 'premium' ? '650.00' : '950.00'))) ?>">
                                            </div>
                                            <small class="text-muted" style="font-size:10px;">Otomatik modda bu paketin baz toptancı maliyeti kullanılır.</small>
                                        </div>

                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="form-label small fw-semibold">Eşleşen Kağıt</label>
                                                <select name="pkg_<?= $pKey ?>_paper" class="form-select form-select-sm bg-white">
                                                    <option value="0">Varsayılan Kağıt</option>
                                                    <?php foreach ($papers as $p): ?>
                                                        <option value="<?= $p['id'] ?>" <?= ($pData['paper_id'] ?? 0) == $p['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($p['name']) ?> (<?= $p['gsm'] ?> gr)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-semibold">Eşleşen İşçilik / Lak</label>
                                                <select name="pkg_<?= $pKey ?>_fin" class="form-select form-select-sm bg-white">
                                                    <option value="0">İşçilik Yok (Düz)</option>
                                                    <?php foreach ($finishings as $f): ?>
                                                        <option value="<?= $f['id'] ?>" <?= ($pData['fin_id'] ?? 0) == $f['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($f['name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- ⚙️ SEKME 4: VARYANTLAR & MALZEMELER -->
            <!-- ========================================================================= -->
            <div class="tab-pane fade" id="tab-variants" role="tabpanel">
                <div class="colorful-header-card card-theme-purple mb-4">
                    <div class="card-top-accent">
                        <div><span class="icon-box-colorful"><i class="bi bi-diagram-3-fill"></i></span> Dinamik Matbaa Baskı Varyantları</div>
                        <button type="button" class="btn btn-sm btn-light text-primary rounded-pill px-3 shadow-xs fw-bold" data-bs-toggle="modal" data-bs-target="#modalQuickAddGroup">
                            <i class="bi bi-plus-circle me-1"></i> + Hızlı Yeni Varyant Grubu Ekle
                        </button>
                    </div>
                    <div class="p-4">
                        <div class="row g-3" id="variantGroupsContainer">
                            <?php foreach ($allVariantGroups as $grp): 
                                $isGrpActive = in_array($grp['id'], $selectedVariantGroups);
                            ?>
                                <div class="col-12" id="grp_card_<?= $grp['id'] ?>">
                                    <div class="card border rounded-4 overflow-hidden shadow-2xs">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3 border-start border-4 border-primary">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="form-check form-switch m-0">
                                                    <input class="form-check-input group-master-switch" type="checkbox" name="allowed_variant_groups[]" value="<?= $grp['id'] ?>" id="grp_<?= $grp['id'] ?>" <?= $isGrpActive ? 'checked' : '' ?> onchange="toggleGroupOptions(<?= $grp['id'] ?>, this.checked)">
                                                </div>
                                                <label class="fw-bold text-dark mb-0 cursor-pointer" for="grp_<?= $grp['id'] ?>">
                                                    <?= htmlspecialchars($grp['name']) ?>
                                                    <?php if (!empty($grp['description'])): ?>
                                                        <span class="text-muted fw-normal small ms-1">(<?= htmlspecialchars($grp['description']) ?>)</span>
                                                    <?php endif; ?>
                                                </label>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 rounded-pill" style="font-size:11px;" onclick="openAddOptionModal(<?= $grp['id'] ?>, '<?= htmlspecialchars($grp['name'], ENT_QUOTES) ?>')">
                                                    <i class="bi bi-plus"></i> Seçenek Ekle
                                                </button>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size:11px;" onclick="selectAllGroupOptions(<?= $grp['id'] ?>, true)">Tümü</button>
                                                    <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size:11px;" onclick="selectAllGroupOptions(<?= $grp['id'] ?>, false)">Temizle</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-3" id="grp_body_<?= $grp['id'] ?>" style="<?= $isGrpActive ? '' : 'opacity: 0.5;' ?>">
                                            <div class="row g-2" id="grp_options_row_<?= $grp['id'] ?>">
                                                <?php foreach ($grp['options'] as $opt): 
                                                    $badge = '';
                                                    if ($opt['calc_type'] === 'percent' && (float)$opt['percent_fee'] > 0) $badge = '+%' . (float)$opt['percent_fee'];
                                                    elseif ($opt['calc_type'] === 'per_unit_try' && (float)$opt['per_unit_fee_try'] > 0) $badge = '+' . Helper::formatPrice($opt['per_unit_fee_try']) . '/ad';
                                                    elseif ($opt['calc_type'] === 'sheet_usd' && (float)$opt['price_usd_70x100'] > 0) $badge = '+$' . number_format($opt['price_usd_70x100'], 2);
                                                    elseif ($opt['calc_type'] === 'fixed_try' && (float)$opt['fixed_fee_try'] > 0) $badge = '+' . Helper::formatPrice($opt['fixed_fee_try']);
                                                    elseif ($opt['calc_type'] === 'fixed_usd' && (float)$opt['fixed_fee_usd'] > 0) $badge = '+$' . number_format($opt['fixed_fee_usd'], 2);
                                                    
                                                    $isOptChecked = empty($selectedVariantOptions) ? $isGrpActive : in_array($opt['id'], $selectedVariantOptions);
                                                ?>
                                                    <div class="col-md-4 col-sm-6">
                                                        <div class="form-check p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between h-100">
                                                            <div class="d-flex align-items-center text-truncate">
                                                                <input class="form-check-input ms-1 grp-opt-<?= $grp['id'] ?>" type="checkbox" name="allowed_variant_options[]" value="<?= $opt['id'] ?>" id="opt_chk_<?= $opt['id'] ?>" <?= $isOptChecked ? 'checked' : '' ?>>
                                                                <label class="form-check-label small ms-2 fw-semibold text-dark text-truncate" for="opt_chk_<?= $opt['id'] ?>" title="<?= htmlspecialchars($opt['name']) ?>">
                                                                    <?= htmlspecialchars($opt['name']) ?>
                                                                </label>
                                                            </div>
                                                            <?php if (!empty($badge)): ?>
                                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-1 text-nowrap"><?= $badge ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Kağıt Türleri Kartı -->
                <div class="colorful-header-card card-theme-blue mb-4">
                    <div class="card-top-accent">
                        <div><span class="icon-box-colorful"><i class="bi bi-layers"></i></span> Müşterinin Seçebileceği Kağıt Türleri &amp; Gramajlar</div>
                        <button type="button" class="btn btn-sm btn-light text-primary rounded-pill px-3 shadow-xs fw-bold" data-bs-toggle="modal" data-bs-target="#modalQuickAddPaper">
                            <i class="bi bi-plus-circle me-1"></i> + Hızlı Kağıt Türü Ekle
                        </button>
                    </div>
                    <div class="p-4">
                        <div class="row g-2" id="papersContainerRow">
                            <?php foreach ($papers as $p): ?>
                                <div class="col-md-3 col-sm-6" id="paper_item_<?= $p['id'] ?>">
                                    <div class="form-check p-2 border rounded-3 bg-light d-flex justify-content-between align-items-center h-100">
                                        <div class="d-flex align-items-center text-truncate">
                                            <input class="form-check-input ms-1" type="checkbox" name="allowed_papers[]" value="<?= $p['id'] ?>" id="paper_<?= $p['id'] ?>" <?= in_array($p['id'], $selectedPapers) ? 'checked' : '' ?>>
                                            <label class="form-check-label small ms-2 fw-bold text-dark text-truncate" for="paper_<?= $p['id'] ?>" title="<?= htmlspecialchars($p['name']) ?>">
                                                <?= htmlspecialchars($p['name']) ?> (<?= $p['gsm'] ?> gr)
                                            </label>
                                        </div>
                                        <span class="badge bg-success-subtle text-success fw-bold ms-1 text-nowrap">$<?= $p['price_usd_70x100'] ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Baskı İşçilikleri & Selefon / Lak Kartı -->
                <div class="colorful-header-card card-theme-emerald">
                    <div class="card-top-accent">
                        <div><span class="icon-box-colorful"><i class="bi bi-gem"></i></span> Ekstra Baskı İşçilikleri (Selefon, Lak, Yaldız, Kesim)</div>
                        <button type="button" class="btn btn-sm btn-light text-success rounded-pill px-3 shadow-xs fw-bold" data-bs-toggle="modal" data-bs-target="#modalQuickAddFinishing">
                            <i class="bi bi-plus-circle me-1"></i> + Hızlı İşçilik / Selefon Ekle
                        </button>
                    </div>
                    <div class="p-4">
                        <div class="row g-2" id="finishingsContainerRow">
                            <?php foreach ($finishings as $f): ?>
                                <div class="col-md-3 col-sm-6" id="finishing_item_<?= $f['id'] ?>">
                                    <div class="form-check p-2 border rounded-3 bg-light d-flex justify-content-between align-items-center h-100">
                                        <div class="d-flex align-items-center text-truncate">
                                            <input class="form-check-input ms-1" type="checkbox" name="allowed_finishings[]" value="<?= $f['id'] ?>" id="fin_<?= $f['id'] ?>" <?= in_array($f['id'], $selectedFinishings) ? 'checked' : '' ?>>
                                            <label class="form-check-label small ms-2 fw-bold text-dark text-truncate" for="fin_<?= $f['id'] ?>" title="<?= htmlspecialchars($f['name']) ?>">
                                                <?= htmlspecialchars($f['name']) ?>
                                            </label>
                                        </div>
                                        <span class="badge bg-primary-subtle text-primary fw-bold ms-1 text-nowrap">+$<?= $f['price_per_unit_usd'] ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- 🎯 SEKME 5: HEDEF SEKTÖRLER & ŞABLON AYARLARI -->
            <!-- ========================================================================= -->
            <div class="tab-pane fade" id="tab-industries" role="tabpanel">
                <div class="colorful-header-card card-theme-emerald mb-4">
                    <div class="card-top-accent">
                        <div><span class="icon-box-colorful"><i class="bi bi-funnel-fill"></i></span> 15 Hedef Sektör &amp; Akıllı Çapraz Satış</div>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-sm btn-outline-success rounded-start-pill px-3" onclick="toggleAllIndustries(true)">Tümünü Seç</button>
                            <button type="button" class="btn btn-sm btn-outline-success rounded-end-pill px-3" onclick="toggleAllIndustries(false)">Temizle</button>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="row g-2">
                            <?php foreach ($allIndustries as $ind): 
                                $isIndChecked = empty($selectedIndustries) || in_array($ind['slug'], $selectedIndustries);
                            ?>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-check p-2 border rounded-3 bg-light d-flex align-items-center h-100 hover-shadow" style="transition: all 0.2s;">
                                        <input class="form-check-input ms-1 industry-check-input" type="checkbox" name="target_industries[]" value="<?= $ind['slug'] ?>" id="ind_<?= $ind['id'] ?>" <?= $isIndChecked ? 'checked' : '' ?>>
                                        <label class="form-check-label small ms-2 fw-semibold text-dark cursor-pointer" for="ind_<?= $ind['id'] ?>">
                                            <i class="bi <?= $ind['icon'] ?> text-emerald me-1" style="color: #059669;"></i> <?= htmlspecialchars($ind['name']) ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Online Vektörel Editör & Grafik Desteği -->
                <div class="colorful-header-card card-theme-blue">
                    <div class="card-top-accent">
                        <div><span class="icon-box-colorful"><i class="bi bi-vector-pen"></i></span> Vektörel Tasarım Editörü &amp; Grafik Hizmeti</div>
                    </div>
                    <div class="p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch p-3 border rounded-4 bg-light h-100">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="allow_online_editor" id="allowOnlineEditorSwitch" value="1" <?= (!isset($product['allow_online_editor']) || $product['allow_online_editor']) ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-bold text-dark fs-6" for="allowOnlineEditorSwitch">
                                        <i class="bi bi-vector-pen text-primary me-1"></i> Vektörel Şablonlar &amp; Online Editör Aktif
                                    </label>
                                    <div class="text-muted small mt-1">Müşteriler 15 sektöre ait hazır şablonları tarayıcıda canlı düzenleyebilsin.</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch p-3 border rounded-4 bg-light h-100">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="allow_design_upload" id="allowDesignUploadSwitch" value="1" <?= (!isset($product['allow_design_upload']) || $product['allow_design_upload']) ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-bold text-dark fs-6" for="allowDesignUploadSwitch">
                                        <i class="bi bi-cloud-arrow-up text-success me-1"></i> Dosya Yükleme (PDF, AI, PSD, ZIP) Aktif
                                    </label>
                                    <div class="text-muted small mt-1">Müşteriler hazır tasarımlarını sürükle-bırak yöntemiyle yükleyebilsin.</div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="p-3 border rounded-4 bg-light">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="allow_design_service" id="allowDesignServiceSwitch" value="1" <?= !empty($product['allow_design_service']) ? 'checked' : '' ?> onchange="document.getElementById('designPriceRow').style.display = this.checked ? 'block' : 'none'">
                                        <label class="form-check-label fw-bold text-dark fs-6" for="allowDesignServiceSwitch">
                                            <i class="bi bi-magic text-warning me-1"></i> Profesyonel Grafik Tasarım Desteği Seçeneği Sunulsun
                                        </label>
                                    </div>
                                    <div id="designPriceRow" style="display: <?= !empty($product['allow_design_service']) ? 'block' : 'none' ?>;" class="mt-2 pt-2 border-top">
                                        <div class="row align-items-center">
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold mb-0">Tasarım Hizmet Bedeli (₺)</label>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group input-group-sm">
                                                    <input type="number" step="1" name="design_service_price" class="form-control" value="<?= $product['design_service_price'] ?? 150 ?>">
                                                    <span class="input-group-text">₺</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- 💾 SABİT KAYDETME & AKSİYON BARI (Sticky Bottom) -->
        <!-- ========================================================================= -->
        <div class="sticky-admin-actions d-flex justify-content-between align-items-center mt-4">
            <a href="<?= SITE_URL ?>/admin/products.php" class="btn btn-outline-light rounded-pill px-4">
                <i class="bi bi-x-lg me-1"></i> İptal
            </a>
            <div class="d-flex align-items-center gap-2">
                <span class="text-light opacity-75 small d-none d-md-inline">Tüm sekmelerdeki değişiklikler tek seferde kaydedilir.</span>
                <button type="submit" class="btn btn-success btn-lg rounded-pill px-5 fw-bold shadow-lg" style="background: #10b981; border-color: #059669;">
                    <i class="bi bi-check-lg me-1"></i> Ürünü &amp; Ayarları Kaydet
                </button>
            </div>
        </div>

    </form>

    <!-- ========================================================================= -->
    <!-- ⚡ MODAL 1: HIZLI YENİ VARYANT GRUBU EKLE (Sayfadan Ayrılmadan) -->
    <!-- ========================================================================= -->
    <div class="modal fade" id="modalQuickAddGroup" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-primary me-2"></i>Yeni Varyant Grubu Oluştur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Grup Adı <span class="text-danger">*</span></label>
                        <input type="text" id="quickGroupName" class="form-control" placeholder="Örn: Laminasyon / Selefon">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Açıklama / İpucu</label>
                        <input type="text" id="quickGroupDesc" class="form-control" placeholder="Örn: Kartvizitin dış kaplamasını seçin">
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Seçim Türü</label>
                            <select id="quickGroupInputType" class="form-select">
                                <option value="radio">Tekli Seçim (Radio)</option>
                                <option value="checkbox">Çoklu Seçim (Checkbox)</option>
                                <option value="select">Açılır Liste (Dropdown)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Zorunlu mu?</label>
                            <select id="quickGroupRequired" class="form-select">
                                <option value="1">Evet, Zorunlu</option>
                                <option value="0">Hayır, İsteğe Bağlı</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" id="btnSubmitQuickGroup" onclick="submitQuickGroup()">
                        <i class="bi bi-check-lg me-1"></i> Grubu Oluştur
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- ⚡ MODAL 2: GRUBA HIZLI SEÇENEK EKLE -->
    <!-- ========================================================================= -->
    <div class="modal fade" id="modalQuickAddOption" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-primary me-2"></i><span id="targetGroupNameSpan">Gruba</span> Seçenek Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <input type="hidden" id="quickOptGroupId" value="0">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Seçenek Adı <span class="text-danger">*</span></label>
                        <input type="text" id="quickOptName" class="form-control" placeholder="Örn: 24K Altın Varak Yaldız">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Fiyatlandırma Türü</label>
                            <select id="quickOptCalcType" class="form-select form-select-sm" onchange="adjustQuickOptFeeInputs(this.value)">
                                <option value="per_unit_try">Adet Başı Sabit ₺ (+0.15 ₺/ad)</option>
                                <option value="percent">Yüzdelik Artış (+%20)</option>
                                <option value="sheet_usd">70x100 Tabaka $ (+0.05 $)</option>
                                <option value="fixed_try">Sabit Sipariş Bedeli ₺ (+50 ₺)</option>
                                <option value="fixed_usd">Sabit Bedel $ (+$5)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold" id="quickOptFeeLabel">Adet Başı Ek Ücret (₺)</label>
                            <input type="number" step="0.01" id="quickOptFeeValue" class="form-control form-control-sm" placeholder="0.00">
                        </div>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="quickOptIsDefault">
                        <label class="form-check-label small fw-semibold" for="quickOptIsDefault">Bu seçeneği grupta varsayılan yap</label>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" id="btnSubmitQuickOption" onclick="submitQuickOption()">
                        <i class="bi bi-check-lg me-1"></i> Seçeneği Ekle
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- ⚡ MODAL 3: HIZLI KATEGORİ EKLE -->
    <!-- ========================================================================= -->
    <div class="modal fade" id="modalQuickAddCategory" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-grid text-primary me-2"></i>Yeni Kategori Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kategori Adı <span class="text-danger">*</span></label>
                        <input type="text" id="quickCatName" class="form-control" placeholder="Örn: Promosyon Ürünleri, Etiket & Sticker">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">İkon Sınıfı (Bootstrap Icon)</label>
                        <input type="text" id="quickCatIcon" class="form-control" value="bi bi-grid" placeholder="bi bi-card-text">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" onclick="submitQuickCategory()">
                        <i class="bi bi-check-lg me-1"></i> Kategoriyi Oluştur
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- ⚡ MODAL 4: HIZLI KAĞIT TÜRÜ EKLE -->
    <!-- ========================================================================= -->
    <div class="modal fade" id="modalQuickAddPaper" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-layers text-primary me-2"></i>Yeni Kağıt Türü (70x100) Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kağıt Adı / Cinsi <span class="text-danger">*</span></label>
                        <input type="text" id="quickPaperName" class="form-control" placeholder="Örn: 400gr Amerikan Bristol">
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Gramaj (GSM)</label>
                            <input type="number" id="quickPaperGsm" class="form-control" value="350" placeholder="350">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">70x100 Tabaka Fiyatı ($)</label>
                            <input type="number" step="0.01" id="quickPaperPriceUsd" class="form-control" value="7.00" placeholder="7.00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" onclick="submitQuickPaper()">
                        <i class="bi bi-check-lg me-1"></i> Kağıdı Ekle
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- ⚡ MODAL 5: HIZLI BASKI İŞÇİLİĞİ / SELEFON EKLE -->
    <!-- ========================================================================= -->
    <div class="modal fade" id="modalQuickAddFinishing" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-gem text-success me-2"></i>Yeni Baskı İşçiliği / Selefon Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">İşlem Adı <span class="text-danger">*</span></label>
                        <input type="text" id="quickFinName" class="form-control" placeholder="Örn: Parlak Selefon, Kabartma Lak, Hologram Varak">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">İşlem Türü</label>
                        <select id="quickFinType" class="form-select">
                            <option value="lamination">Selefon / Laminasyon</option>
                            <option value="spot_uv">Kısmi / Kabartma Lak</option>
                            <option value="foil">Varak Yaldız</option>
                            <option value="die_cut">Özel Bıçak / Kesim</option>
                            <option value="other">Diğer Özel İşçilik</option>
                        </select>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Adet Başı Ek ($)</label>
                            <input type="number" step="0.001" id="quickFinPriceUnit" class="form-control" value="0.05" placeholder="0.05">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Sabit Ayar Bedeli ($)</label>
                            <input type="number" step="0.01" id="quickFinBaseSetup" class="form-control" value="10.00" placeholder="10.00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" onclick="submitQuickFinishing()">
                        <i class="bi bi-check-lg me-1"></i> İşçiliği Ekle
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 🚀 JS İŞLEMLERİ (WebP Yükleme, Sürükle-Bırak, Video, Hızlı Varyantlar) -->
    <!-- ========================================================================= -->
    <script>
    const SITE_URL = '<?= SITE_URL ?>';

    // 1. Çoklu Resim Yükleme (Otomatik WebP)
    const photoDropzone = document.getElementById('photoDropzone');
    const multiImageInput = document.getElementById('multiImageInput');
    const galleryGrid = document.getElementById('galleryGrid');
    const uploadSpinner = document.getElementById('mediaUploadSpinner');
    const inputFeaturedImage = document.getElementById('inputFeaturedImage');

    if (photoDropzone && multiImageInput) {
        photoDropzone.addEventListener('click', () => multiImageInput.click());
        
        photoDropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            photoDropzone.classList.add('dragover');
        });
        photoDropzone.addEventListener('dragleave', () => photoDropzone.classList.remove('dragover'));
        photoDropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            photoDropzone.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                uploadImages(e.dataTransfer.files);
            }
        });

        multiImageInput.addEventListener('change', () => {
            if (multiImageInput.files.length) {
                uploadImages(multiImageInput.files);
            }
        });
    }

    function uploadImages(files) {
        if (!files || !files.length) return;

        uploadSpinner.style.display = 'flex';
        const formData = new FormData();
        formData.append('type', 'image');
        for (let i = 0; i < files.length; i++) {
            formData.append('images[]', files[i]);
        }

        fetch(SITE_URL + '/admin/api_upload_media.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            uploadSpinner.style.display = 'none';
            if (data.success && data.files) {
                data.files.forEach(f => {
                    addGalleryCard(f.file_path, f.full_url);
                });
            } else {
                alert('Görsel yükleme hatası: ' + (data.error || 'Bilinmeyen hata'));
            }
        })
        .catch(err => {
            uploadSpinner.style.display = 'none';
            alert('Sunucuya bağlanırken bir hata oluştu.');
        });
    }

    function addGalleryCard(filePath, fullUrl) {
        const isFirst = galleryGrid.querySelectorAll('.gallery-item-wrapper').length === 0;
        if (isFirst || !inputFeaturedImage.value) {
            inputFeaturedImage.value = filePath;
        }

        const col = document.createElement('div');
        col.className = 'col-md-3 col-sm-4 col-6 gallery-item-wrapper';
        col.dataset.path = filePath;
        col.innerHTML = `
            <div class="gallery-thumb-card">
                <img src="${fullUrl}" alt="Ürün Görseli">
                <div class="card-actions">
                    <button type="button" class="btn btn-sm btn-dark btn-set-featured p-1 px-2 rounded-circle" title="Kapak Görseli Yap" onclick="setAsFeatured('${filePath}', this)">
                        <i class="bi bi-star-fill text-warning"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger btn-del-image p-1 px-2 rounded-circle" title="Sil" onclick="removeGalleryItem(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <span class="badge bg-primary badge-featured ${isFirst ? '' : 'd-none'}">
                    <i class="bi bi-star-fill me-1"></i> Kapak Görseli
                </span>
                <input type="hidden" name="gallery_images[]" value="${filePath}">
            </div>
        `;
        galleryGrid.appendChild(col);
    }

    function setAsFeatured(filePath, btn) {
        inputFeaturedImage.value = filePath;
        document.querySelectorAll('.badge-featured').forEach(b => b.classList.add('d-none'));
        const card = btn.closest('.gallery-thumb-card');
        if (card) {
            const badge = card.querySelector('.badge-featured');
            if (badge) badge.classList.remove('d-none');
        }
    }

    function removeGalleryItem(btn) {
        const wrapper = btn.closest('.gallery-item-wrapper');
        const path = wrapper.dataset.path;
        wrapper.remove();
        if (inputFeaturedImage.value === path) {
            const firstRemaining = galleryGrid.querySelector('.gallery-item-wrapper');
            if (firstRemaining) {
                setAsFeatured(firstRemaining.dataset.path, firstRemaining.querySelector('.btn-set-featured'));
            } else {
                inputFeaturedImage.value = '';
            }
        }
    }

    // 2. Video Yükleme
    const btnUploadVideo = document.getElementById('btnUploadVideo');
    const videoFileInput = document.getElementById('videoFileInput');
    const videoUploadStatus = document.getElementById('videoUploadStatus');
    const inputVideoPath = document.getElementById('inputVideoPath');
    const videoUrlManual = document.getElementById('videoUrlManual');
    const videoPreviewContainer = document.getElementById('videoPreviewContainer');
    const adminVideoPlayer = document.getElementById('adminVideoPlayer');

    if (btnUploadVideo && videoFileInput) {
        btnUploadVideo.addEventListener('click', () => {
            if (!videoFileInput.files.length) {
                alert('Lütfen bir video dosyası seçin.');
                return;
            }
            const file = videoFileInput.files[0];
            const formData = new FormData();
            formData.append('type', 'video');
            formData.append('video_file', file);

            videoUploadStatus.style.display = 'block';
            videoUploadStatus.className = 'mt-2 small text-primary fw-bold';
            videoUploadStatus.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Video yükleniyor...';

            fetch(SITE_URL + '/admin/api_upload_media.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    videoUploadStatus.className = 'mt-2 small text-success fw-bold';
                    videoUploadStatus.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Video başarıyla yüklendi: ' + data.file_name;
                    inputVideoPath.value = data.file_path;
                    videoUrlManual.value = data.file_path;
                    updateVideoPreview();
                } else {
                    videoUploadStatus.className = 'mt-2 small text-danger fw-bold';
                    videoUploadStatus.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i> ' + (data.error || 'Yükleme başarısız.');
                }
            })
            .catch(err => {
                videoUploadStatus.className = 'mt-2 small text-danger fw-bold';
                videoUploadStatus.innerHTML = 'Sunucu bağlantı hatası.';
            });
        });
    }

    function updateVideoPreview() {
        const val = inputVideoPath.value;
        if (val) {
            videoPreviewContainer.style.display = 'block';
            adminVideoPlayer.src = val.startsWith('http') ? val : SITE_URL + '/' + val;
            adminVideoPlayer.load();
        } else {
            videoPreviewContainer.style.display = 'none';
        }
    }

    function clearVideo() {
        inputVideoPath.value = '';
        videoUrlManual.value = '';
        if (videoFileInput) videoFileInput.value = '';
        updateVideoPreview();
    }

    // 3. Varyant Grupları & Hızlı Ekleme
    function toggleGroupOptions(groupId, isChecked) {
        const body = document.getElementById('grp_body_' + groupId);
        if (body) {
            body.style.opacity = isChecked ? '1' : '0.5';
        }
        document.querySelectorAll('.grp-opt-' + groupId).forEach(chk => {
            chk.checked = isChecked;
        });
    }

    function selectAllGroupOptions(groupId, state) {
        const master = document.getElementById('grp_' + groupId);
        if (master && state && !master.checked) {
            master.checked = true;
            toggleGroupOptions(groupId, true);
        }
        document.querySelectorAll('.grp-opt-' + groupId).forEach(chk => {
            chk.checked = state;
        });
    }

    function toggleAllIndustries(state) {
        document.querySelectorAll('.industry-check-input').forEach(chk => {
            chk.checked = state;
        });
    }

    function submitQuickGroup() {
        const name = document.getElementById('quickGroupName').value.trim();
        const desc = document.getElementById('quickGroupDesc').value.trim();
        const inputType = document.getElementById('quickGroupInputType').value;
        const isRequired = document.getElementById('quickGroupRequired').value;

        if (!name) {
            alert('Lütfen grup adını yazın.');
            return;
        }

        const formData = new FormData();
        formData.append('action', 'create_group');
        formData.append('name', name);
        formData.append('description', desc);
        formData.append('input_type', inputType);
        formData.append('is_required', isRequired);

        fetch(SITE_URL + '/admin/api_variants.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.group) {
                const grp = data.group;
                const container = document.getElementById('variantGroupsContainer');
                const col = document.createElement('div');
                col.className = 'col-12';
                col.id = 'grp_card_' + grp.id;
                col.innerHTML = `
                    <div class="card border rounded-4 overflow-hidden shadow-2xs">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3 border-start border-4 border-primary">
                            <div class="d-flex align-items-center gap-2">
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input group-master-switch" type="checkbox" name="allowed_variant_groups[]" value="${grp.id}" id="grp_${grp.id}" checked onchange="toggleGroupOptions(${grp.id}, this.checked)">
                                </div>
                                <label class="fw-bold text-dark mb-0 cursor-pointer" for="grp_${grp.id}">
                                    ${grp.name}
                                    ${grp.description ? `<span class="text-muted fw-normal small ms-1">(${grp.description})</span>` : ''}
                                </label>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 rounded-pill" style="font-size:11px;" onclick="openAddOptionModal(${grp.id}, '${grp.name}')">
                                    <i class="bi bi-plus"></i> Seçenek Ekle
                                </button>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size:11px;" onclick="selectAllGroupOptions(${grp.id}, true)">Tümü</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size:11px;" onclick="selectAllGroupOptions(${grp.id}, false)">Temizle</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3" id="grp_body_${grp.id}">
                            <div class="row g-2" id="grp_options_row_${grp.id}">
                                <div class="col-12 text-muted small py-2">Henüz seçenek eklenmedi. Sağ üstteki "+ Seçenek Ekle" butonuna tıklayarak ekleyebilirsiniz.</div>
                            </div>
                        </div>
                    </div>
                `;
                container.prepend(col);

                bootstrap.Modal.getInstance(document.getElementById('modalQuickAddGroup')).hide();
                document.getElementById('quickGroupName').value = '';
                document.getElementById('quickGroupDesc').value = '';
            } else {
                alert(data.error || 'Grup oluşturulamadı.');
            }
        });
    }

    function openAddOptionModal(groupId, groupName) {
        document.getElementById('quickOptGroupId').value = groupId;
        document.getElementById('targetGroupNameSpan').textContent = '"' + groupName + '" Grubuna';
        document.getElementById('quickOptName').value = '';
        document.getElementById('quickOptFeeValue').value = '';
        const modal = new bootstrap.Modal(document.getElementById('modalQuickAddOption'));
        modal.show();
    }

    function adjustQuickOptFeeInputs(calcType) {
        const label = document.getElementById('quickOptFeeLabel');
        if (calcType === 'percent') label.textContent = 'Yüzdelik Oran (%)';
        else if (calcType === 'sheet_usd') label.textContent = '70x100 Tabaka Fiyatı ($)';
        else if (calcType === 'fixed_try') label.textContent = 'Sabit Sipariş Ücreti (₺)';
        else if (calcType === 'fixed_usd') label.textContent = 'Sabit Sipariş Ücreti ($)';
        else label.textContent = 'Adet Başı Ek Ücret (₺)';
    }

    function submitQuickOption() {
        const groupId = document.getElementById('quickOptGroupId').value;
        const name = document.getElementById('quickOptName').value.trim();
        const calcType = document.getElementById('quickOptCalcType').value;
        const feeVal = document.getElementById('quickOptFeeValue').value;
        const isDef = document.getElementById('quickOptIsDefault').checked ? 1 : 0;

        if (!name) {
            alert('Lütfen seçenek adını yazın.');
            return;
        }

        const formData = new FormData();
        formData.append('action', 'create_option');
        formData.append('group_id', groupId);
        formData.append('name', name);
        formData.append('calc_type', calcType);
        if (calcType === 'percent') formData.append('percent_fee', feeVal);
        else if (calcType === 'sheet_usd') formData.append('price_usd_70x100', feeVal);
        else if (calcType === 'fixed_try') formData.append('fixed_fee_try', feeVal);
        else if (calcType === 'fixed_usd') formData.append('fixed_fee_usd', feeVal);
        else formData.append('per_unit_fee_try', feeVal);
        formData.append('is_default', isDef);

        fetch(SITE_URL + '/admin/api_variants.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.option) {
                const opt = data.option;
                const row = document.getElementById('grp_options_row_' + groupId);
                
                // Eğer daha önce "Henüz seçenek eklenmedi" yazısı varsa temizle
                if (row.querySelector('.text-muted')) {
                    row.innerHTML = '';
                }

                const col = document.createElement('div');
                col.className = 'col-md-4 col-sm-6';
                col.innerHTML = `
                    <div class="form-check p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between h-100">
                        <div class="d-flex align-items-center text-truncate">
                            <input class="form-check-input ms-1 grp-opt-${groupId}" type="checkbox" name="allowed_variant_options[]" value="${opt.id}" id="opt_chk_${opt.id}" checked>
                            <label class="form-check-label small ms-2 fw-semibold text-dark text-truncate" for="opt_chk_${opt.id}" title="${opt.name}">
                                ${opt.name}
                            </label>
                        </div>
                        ${opt.badge ? `<span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-1 text-nowrap">${opt.badge}</span>` : ''}
                    </div>
                `;
                row.appendChild(col);

                bootstrap.Modal.getInstance(document.getElementById('modalQuickAddOption')).hide();
            } else {
                alert(data.error || 'Seçenek eklenemedi.');
            }
        });
    }

    // 4. Hızlı Kategori Ekleme JS
    function submitQuickCategory() {
        const name = document.getElementById('quickCatName').value.trim();
        const icon = document.getElementById('quickCatIcon').value.trim() || 'bi bi-grid';

        if (!name) {
            alert('Lütfen kategori adını yazın.');
            return;
        }

        const formData = new FormData();
        formData.append('action', 'create_category');
        formData.append('name', name);
        formData.append('icon', icon);

        fetch(SITE_URL + '/admin/api_quick_create.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.category) {
                const cat = data.category;
                const select = document.getElementById('productCategorySelect');
                if (select) {
                    const opt = document.createElement('option');
                    opt.value = cat.id;
                    opt.textContent = cat.name;
                    opt.selected = true;
                    select.appendChild(opt);
                }
                bootstrap.Modal.getInstance(document.getElementById('modalQuickAddCategory')).hide();
                document.getElementById('quickCatName').value = '';
                alert('Kategori başarıyla oluşturuldu ve seçildi!');
            } else {
                alert(data.error || 'Kategori oluşturulamadı.');
            }
        });
    }

    // 5. Hızlı Kağıt Türü Ekleme JS
    function submitQuickPaper() {
        const name = document.getElementById('quickPaperName').value.trim();
        const gsm = document.getElementById('quickPaperGsm').value;
        const priceUsd = document.getElementById('quickPaperPriceUsd').value;

        if (!name) {
            alert('Lütfen kağıt adını yazın.');
            return;
        }

        const formData = new FormData();
        formData.append('action', 'create_paper');
        formData.append('name', name);
        formData.append('gsm', gsm);
        formData.append('price_usd_70x100', priceUsd);

        fetch(SITE_URL + '/admin/api_quick_create.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.paper) {
                const p = data.paper;
                const row = document.getElementById('papersContainerRow');
                if (row) {
                    const col = document.createElement('div');
                    col.className = 'col-md-3 col-sm-6';
                    col.id = 'paper_item_' + p.id;
                    col.innerHTML = `
                        <div class="form-check p-2 border rounded-3 bg-light d-flex justify-content-between align-items-center h-100">
                            <div class="d-flex align-items-center text-truncate">
                                <input class="form-check-input ms-1" type="checkbox" name="allowed_papers[]" value="${p.id}" id="paper_${p.id}" checked>
                                <label class="form-check-label small ms-2 fw-bold text-dark text-truncate" for="paper_${p.id}" title="${p.name}">
                                    ${p.name} (${p.gsm} gr)
                                </label>
                            </div>
                            <span class="badge bg-success-subtle text-success fw-bold ms-1 text-nowrap">$${p.price_usd_70x100}</span>
                        </div>
                    `;
                    row.prepend(col);
                }

                // 4'lü paket dropdownlarına da ekle
                document.querySelectorAll('select[name^="pkg_"][name$="_paper"]').forEach(sel => {
                    const opt = document.createElement('option');
                    opt.value = p.id;
                    opt.textContent = `${p.name} (${p.gsm} gr)`;
                    sel.appendChild(opt);
                });

                bootstrap.Modal.getInstance(document.getElementById('modalQuickAddPaper')).hide();
                document.getElementById('quickPaperName').value = '';
                alert('Yeni kağıt türü eklendi ve bu ürüne dahil edildi!');
            } else {
                alert(data.error || 'Kağıt eklenemedi.');
            }
        });
    }

    // 6. Hızlı Baskı İşçiliği Ekleme JS
    function submitQuickFinishing() {
        const name = document.getElementById('quickFinName').value.trim();
        const type = document.getElementById('quickFinType').value;
        const priceUnit = document.getElementById('quickFinPriceUnit').value;
        const baseSetup = document.getElementById('quickFinBaseSetup').value;

        if (!name) {
            alert('Lütfen işlem adını yazın.');
            return;
        }

        const formData = new FormData();
        formData.append('action', 'create_finishing');
        formData.append('name', name);
        formData.append('type', type);
        formData.append('price_per_unit_usd', priceUnit);
        formData.append('base_setup_usd', baseSetup);

        fetch(SITE_URL + '/admin/api_quick_create.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.finishing) {
                const f = data.finishing;
                const row = document.getElementById('finishingsContainerRow');
                if (row) {
                    const col = document.createElement('div');
                    col.className = 'col-md-3 col-sm-6';
                    col.id = 'finishing_item_' + f.id;
                    col.innerHTML = `
                        <div class="form-check p-2 border rounded-3 bg-light d-flex justify-content-between align-items-center h-100">
                            <div class="d-flex align-items-center text-truncate">
                                <input class="form-check-input ms-1" type="checkbox" name="allowed_finishings[]" value="${f.id}" id="fin_${f.id}" checked>
                                <label class="form-check-label small ms-2 fw-bold text-dark text-truncate" for="fin_${f.id}" title="${f.name}">
                                    ${f.name}
                                </label>
                            </div>
                            <span class="badge bg-primary-subtle text-primary fw-bold ms-1 text-nowrap">+$${f.price_per_unit_usd}</span>
                        </div>
                    `;
                    row.prepend(col);
                }

                // 4'lü paket dropdownlarına da ekle
                document.querySelectorAll('select[name^="pkg_"][name$="_fin"]').forEach(sel => {
                    const opt = document.createElement('option');
                    opt.value = f.id;
                    opt.textContent = f.name;
                    sel.appendChild(opt);
                });

                bootstrap.Modal.getInstance(document.getElementById('modalQuickAddFinishing')).hide();
                document.getElementById('quickFinName').value = '';
                alert('Yeni baskı işçiliği eklendi ve bu ürüne dahil edildi!');
            } else {
                alert(data.error || 'İşçilik seçeneği eklenemedi.');
            }
        });
    }
    </script>

<?php endif; ?>

<?php require_once __DIR__ . '/footer.php'; ?>
