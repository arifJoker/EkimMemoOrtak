<?php
/**
 * TAMBASKI.COM.TR - İnteraktif Ürün & Fiyat Hesaplayıcı Sayfası
 */
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? '';
$product = get_product_by_slug($slug);

if (!$product) {
    header("Location: category.php");
    exit;
}

// Sepete Ekleme POST İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    $pricing_mode = $_POST['pricing_mode'] ?? 'package';
    $calc_params = [
        'package_id' => $_POST['package_id'] ?? null,
        'custom_quantity' => $_POST['custom_quantity'] ?? null,
        'width_cm' => $_POST['width_cm'] ?? 100,
        'height_cm' => $_POST['height_cm'] ?? 100,
        'quantity' => $_POST['sqm_quantity'] ?? 1,
        'thickness_multiplier' => $_POST['thickness_multiplier'] ?? 1.0,
        'extra_cutting_price' => $_POST['extra_cutting_price'] ?? 0.0
    ];

    $price_result = calculate_item_price($product, $calc_params);

    // Tasarım Dosyası Yükleme
    $uploaded_design_name = null;
    if (isset($_FILES['design_file']) && $_FILES['design_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/uploads/designs/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $ext = pathinfo($_FILES['design_file']['name'], PATHINFO_EXTENSION);
        $safe_name = time() . '_' . uniqid() . '.' . $ext;
        $dest = $upload_dir . $safe_name;
        if (move_uploaded_file($_FILES['design_file']['tmp_name'], $dest)) {
            $uploaded_design_name = 'uploads/designs/' . $safe_name;
        }
    }

    $cart_item = [
        'id' => uniqid('cart_'),
        'product_id' => $product['id'],
        'product_name' => $product['name'],
        'item_title' => $price_result['title'],
        'specs' => $price_result['specs'],
        'quantity' => $price_result['quantity'],
        'unit_price' => $price_result['unit_price'],
        'total_price' => $price_result['total_price'],
        'design_source' => $_POST['design_source'] ?? 'upload',
        'design_file' => $uploaded_design_name,
        'design_note' => trim($_POST['design_note'] ?? '')
    ];

    $_SESSION['cart'][] = $cart_item;
    set_flash_message('success', 'Ürün başarıyla sepetinize eklendi.');
    header("Location: cart.php");
    exit;
}

$page_title = $product['name'] . " Fiyatı & Sipariş – TamBaskı";
require_once __DIR__ . '/includes/header.php';

// İlk varsayılan fiyat hesaplaması
$initial_price = calculate_item_price($product, [
    'package_id' => $product['packages'][0]['id'] ?? null,
    'custom_quantity' => 100,
    'width_cm' => 100,
    'height_cm' => 100,
    'quantity' => 1
]);
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="index.php">Anasayfa</a></li>
            <li class="breadcrumb-item"><a href="category.php?slug=<?= urlencode($product['category_slug']) ?>"><?= ucfirst(str_replace('-', ' ', $product['category_slug'])) ?></a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($product['name']) ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Sol: Ürün Görseli & Özellikleri -->
        <div class="col-lg-5">
            <div class="apple-card p-4 text-center sticky-top" style="top: 90px;">
                <div class="product-img-wrapper mb-4" style="height: 300px;">
                    <i class="bi <?= ($product['pricing_type'] === 'sqm_calculator' ? 'bi-layers' : 'bi-box-seam') ?> text-primary" style="font-size: 110px;"></i>
                </div>
                <h3 class="fw-bold text-dark mb-2"><?= htmlspecialchars($product['name']) ?></h3>
                <p class="text-muted small"><?= htmlspecialchars($product['short_desc']) ?></p>
                
                <div class="d-flex justify-content-center gap-2 mt-3">
                    <span class="badge bg-light text-dark border"><i class="bi bi-shield-check text-success"></i> Kalite Garantisi</span>
                    <span class="badge bg-light text-dark border"><i class="bi bi-truck text-primary"></i> Hızlı Kargo</span>
                    <?php if (!empty($product['is_urgent_available'])): ?>
                        <span class="badge bg-danger-subtle text-danger border border-danger"><i class="bi bi-lightning-fill"></i> 24S Acil</span>
                    <?php endif; ?>
                </div>

                <div class="mt-4 pt-3 border-top text-start">
                    <h6 class="fw-bold mb-2">Ürün Açıklaması & Detaylar</h6>
                    <p class="text-muted small mb-0"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                </div>
            </div>
        </div>

        <!-- Sağ: İnteraktif Yapılandırıcı & Fiyatlandırıcı Formu -->
        <div class="col-lg-7">
            <form id="productConfigForm" method="POST" enctype="multipart/form-data" 
                  data-setup-fee="<?= $product['base_setup_fee'] ?>"
                  data-unit-multiplier="<?= $product['custom_unit_multiplier'] ?>"
                  data-base-sqm="<?= $product['base_sqm_price'] ?>">
                
                <input type="hidden" name="action" value="add_to_cart">
                <input type="hidden" name="pricing_mode" id="pricingMode" value="<?= ($product['pricing_type'] === 'sqm_calculator' ? 'sqm' : 'package') ?>">
                <input type="hidden" name="package_id" id="selectedPackageId" value="<?= $product['packages'][0]['id'] ?? '' ?>">

                <!-- 1. ADIM: EBAT / ADET / PAKET SEÇİMİ -->
                <div class="apple-card p-4 mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold mb-0">1. Seçenek & Adet Belirleyin</h5>
                        <span class="badge bg-primary text-white rounded-pill px-3">Adım 1/2</span>
                    </div>

                    <?php if ($product['pricing_type'] === 'sqm_calculator'): ?>
                        <!-- m² Hesaplayıcı (Dekota, Pleksi Kesim, Folyo, Branda) -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Genişlik / En (cm)</label>
                                <div class="input-group">
                                    <input type="number" name="width_cm" id="sqmWidthInput" class="form-control form-control-lg" value="100" min="10" max="3000">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Yükseklik / Boy (cm)</label>
                                <div class="input-group">
                                    <input type="number" name="height_cm" id="sqmHeightInput" class="form-control form-control-lg" value="100" min="10" max="3000">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>

                            <?php if (!empty($product['options']['thickness'])): ?>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Malzeme & Kalınlık</label>
                                    <select name="thickness_multiplier" id="thicknessSelect" class="form-select form-select-lg">
                                        <?php foreach ($product['options']['thickness'] as $opt): ?>
                                            <option value="<?= $opt['multiplier'] ?>"><?= htmlspecialchars($opt['title']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($product['options']['cutting'])): ?>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Kesim Şekli</label>
                                    <select name="extra_cutting_price" id="cuttingSelect" class="form-select form-select-lg">
                                        <?php foreach ($product['options']['cutting'] as $opt): ?>
                                            <option value="<?= $opt['price_extra'] ?>"><?= htmlspecialchars($opt['title']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Sipariş Adedi</label>
                                <input type="number" name="sqm_quantity" id="sqmQuantityInput" class="form-control form-control-lg" value="1" min="1">
                            </div>
                        </div>

                    <?php else: ?>
                        <!-- Hazır Sabit Paketler & Özel Adet Girişi -->
                        <ul class="nav nav-pills nav-fill mb-3 bg-light p-1 rounded-4" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active rounded-4 fw-bold" id="packages-tab" data-bs-toggle="pill" data-bs-target="#tab-packages" type="button">
                                    <i class="bi bi-boxes me-1"></i> Standart Hazır Paketler
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-4 fw-bold" id="custom-tab" data-bs-toggle="pill" data-bs-target="#tab-custom" type="button">
                                    <i class="bi bi-pencil-square me-1"></i> Özel Adet Girin
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- Hazır Paketler Tabı -->
                            <div class="tab-pane fade show active" id="tab-packages">
                                <div class="row g-3">
                                    <?php if (!empty($product['packages'])): ?>
                                        <?php foreach ($product['packages'] as $idx => $pkg): ?>
                                            <div class="col-md-6">
                                                <div class="package-select-card <?= $idx === 0 ? 'active' : '' ?>"
                                                     data-package-id="<?= $pkg['id'] ?>"
                                                     data-price="<?= $pkg['price'] ?>"
                                                     data-qty="<?= $pkg['quantity'] ?>"
                                                     data-specs="<?= htmlspecialchars($pkg['specs']) ?>">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="fw-bold fs-6"><?= $pkg['quantity'] ?> Adet</span>
                                                        <span class="fw-extrabold text-danger fs-5"><?= format_price($pkg['price']) ?></span>
                                                    </div>
                                                    <small class="text-muted d-block"><?= htmlspecialchars($pkg['specs']) ?></small>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Özel Adet Girişi Tabı -->
                            <div class="tab-pane fade" id="tab-custom">
                                <div class="custom-qty-box">
                                    <label class="form-label small fw-bold">İstediğiniz Özel Adeti Girin</label>
                                    <div class="input-group mb-2">
                                        <input type="number" name="custom_quantity" id="customQuantityInput" class="form-control form-control-lg" placeholder="Örn: 53, 120, 350..." min="<?= $product['min_quantity'] ?>" step="1">
                                        <span class="input-group-text">Adet</span>
                                    </div>
                                    <small class="text-muted">Minimum sipariş: <strong><?= $product['min_quantity'] ?> adet</strong>. Fiyat kurulum tabanı ve birim maliyete göre anlık hesaplanır.</small>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 2. ADIM: TASARIM YÜKLEME & DESTEK -->
                <div class="apple-card p-4 mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold mb-0">2. Tasarımınızı Ekleyin</h5>
                        <span class="badge bg-primary text-white rounded-pill px-3">Adım 2/2</span>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="design_source" id="designSourceUpload" value="upload" checked>
                            <label class="form-check-label fw-bold small" for="designSourceUpload">Tasarımım Hazır (Dosya Yükle)</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="design_source" id="designSourceSupport" value="graphic_support">
                            <label class="form-check-label fw-bold small" for="designSourceSupport">Tasarım Desteği İstiyorum</label>
                        </div>
                    </div>

                    <!-- Dosya Yükleme Alanı -->
                    <div id="uploadSection" class="upload-drop-zone mb-3">
                        <i class="bi bi-cloud-arrow-up text-primary fs-1 mb-2 d-block"></i>
                        <h6 class="fw-bold mb-1">Tasarım Dosyanızı Buraya Sürükleyin veya Seçin</h6>
                        <span class="text-muted small d-block mb-2">PDF, AI, PSD, CDR, TIFF, JPG, PNG formatları desteklenir (Max 100MB)</span>
                        <input type="file" name="design_file" id="designFileInput" class="d-none" accept=".pdf,.ai,.psd,.cdr,.tiff,.jpg,.jpeg,.png,.svg">
                        <div id="uploadedFileName" class="mt-2"></div>
                    </div>

                    <!-- Tasarım Notu -->
                    <div class="mb-0">
                        <label class="form-label small fw-bold">Sipariş & Baskı Notunuz (Opsiyonel)</label>
                        <textarea name="design_note" class="form-control" rows="2" placeholder="Varsa özel kesim çizgisi, renk kodu veya teslimatla ilgili notlarınızı iletebilirsiniz..."></textarea>
                    </div>
                </div>

                <!-- 3. ÖZET & SEPETE EKLEME ÇUBUĞU -->
                <div class="apple-card p-4 border-primary border-2 shadow-lg">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div>
                            <span class="text-muted small d-block">Seçilen Özellikler:</span>
                            <div id="displaySpecs" class="fw-bold text-dark fs-6"><?= htmlspecialchars($initial_price['specs']) ?></div>
                            <small id="displayUnitPrice" class="text-muted">Birim Fiyat: <?= format_price($initial_price['unit_price']) ?></small>
                        </div>
                        <div class="text-md-end">
                            <span class="text-muted small d-block">Toplam Tutar (+KDV):</span>
                            <div id="displayTotalPrice" class="fw-extrabold text-danger fs-3"><?= format_price($initial_price['total_price']) ?></div>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-top d-flex gap-3">
                        <button type="submit" class="btn btn-apple btn-apple-orange btn-lg w-100 py-3 fw-bold">
                            <i class="bi bi-bag-plus-fill me-2"></i> Sepete Ekle
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
