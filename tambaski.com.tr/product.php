<?php
/**
 * TAMBASKI.COM.TR - Gelişmiş Ürün Sayfası, 3D Mockup Simülasyonu & Entegre "Kendin Tasarla" Editörü
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
    } elseif (!empty($_POST['custom_design_data'])) {
        $uploaded_design_name = $_POST['custom_design_data']; // Canvas Data URL
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

$page_title = $product['name'] . " Fiyatı & 3D Canlı Tasarla – TamBaskı";
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

<!-- Fabric.js CDN for Online Customizer -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>

<div class="container py-3 py-md-4">
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3 d-none d-md-block">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="index.php" class="text-muted">Ana Sayfa</a></li>
            <li class="breadcrumb-item"><a href="category.php?slug=<?= urlencode($product['category_slug']) ?>" class="text-muted"><?= ucfirst(str_replace('-', ' ', $product['category_slug'])) ?></a></li>
            <li class="breadcrumb-item active text-dark fw-bold"><?= htmlspecialchars($product['name']) ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        
        <!-- ========================================================================= -->
        <!-- SOL: 3D CANLI DİNAMİK MOCKUP SAHNESİ & TEKNİK TABLO                        -->
        <!-- ========================================================================= -->
        <div class="col-lg-5">
            <div class="apple-card p-3 p-md-4 sticky-top" style="top: 85px; z-index: 10;">
                
                <!-- Üst Özellik Rozetleri -->
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 font-monospace fw-bold" style="font-size: 11px;">
                        <i class="bi bi-palette2 me-1"></i> Canlı 3D Mockup
                    </span>
                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 fw-bold" style="font-size: 11px;">
                        <i class="bi bi-shield-check me-1"></i> %100 Memnuniyet
                    </span>
                    <?php if (!empty($product['is_urgent_available'])): ?>
                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1 fw-bold" style="font-size: 11px;">
                            <i class="bi bi-lightning-charge-fill me-1"></i> 24S Acil Baskı
                        </span>
                    <?php endif; ?>
                </div>

                <!-- 🎨 3D MOCKUP GÖRSELLEŞTİRİCİ KUTUSU -->
                <div class="mockup-stage-box mb-3" id="mockupStageBox">
                    <!-- Üst Doku ve Kalınlık Rozetleri -->
                    <div class="position-absolute top-0 start-0 w-100 p-3 d-flex justify-content-between align-items-center" style="z-index: 20; pointer-events: none;">
                        <div class="d-flex flex-column gap-1">
                            <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-2 py-1 shadow-sm" style="font-size: 10px;">
                                <i class="bi bi-layers text-warning me-1"></i> Kalınlık: <strong>0.38 mm (350 GSM)</strong>
                            </span>
                        </div>
                        <button type="button" class="btn btn-sm btn-dark bg-opacity-50 text-white rounded-pill px-2 py-1 border-0" style="font-size: 10px; pointer-events: auto;" onclick="resetCardTilt()">
                            <i class="bi bi-arrow-clockwise"></i> Sıfırla
                        </button>
                    </div>

                    <!-- 3D İnteraktif Kart Sahnesi -->
                    <div id="interactiveCardStage" style="perspective: 1000px; padding: 20px;">
                        <div id="interactiveCard" class="interactive-showcase-card" style="background-image: url('assets/img/logo.svg'); background-repeat: no-repeat; background-position: center 30px; background-size: 130px;">
                            <div class="card-glare" id="cardGlare"></div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-dark text-white font-monospace" style="font-size: 8px;">TAM BASKI 3D</span>
                                <span class="badge bg-secondary bg-opacity-50 text-white" style="font-size: 8px;">Heidelberg HD</span>
                            </div>

                            <div class="text-start mt-auto">
                                <div class="fw-bold text-dark fs-6" id="mockupNameText">Kurumsal Prestij Serisi</div>
                                <div class="text-muted" style="font-size: 10px;" id="mockupSubText">350gr Mat Kuşe + Kabartma Lak</div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center text-muted border-top pt-2 mt-2" style="font-size: 9px;">
                                <span><i class="bi bi-envelope"></i> info@tambaski.com.tr</span>
                                <span class="fw-bold text-primary">tambaski.com.tr</span>
                            </div>
                        </div>
                    </div>

                    <!-- Alt İpucu -->
                    <div class="position-absolute bottom-0 start-50 translate-middle-x mb-2 text-white-50 small" style="font-size: 10px; pointer-events: none;">
                        <i class="bi bi-hand-index-thumb text-warning me-1"></i> Farenizle eğerek 3D ışık yansımasını inceleyin
                    </div>
                </div>

                <!-- Ürün Başlığı & Kısa Açıklama -->
                <h4 class="fw-bold mb-1"><?= htmlspecialchars($product['name']) ?></h4>
                <p class="text-muted small mb-3"><?= htmlspecialchars($product['short_desc']) ?></p>

                <!-- Teknik Güven Tablosu -->
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-2 bg-light rounded-3 border d-flex align-items-center gap-2">
                            <i class="bi bi-droplet-half text-info fs-4"></i>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 11px;">Su & Nem Korumalı</div>
                                <div class="text-muted" style="font-size: 10px;">Selefonlu kaplama</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-light rounded-3 border d-flex align-items-center gap-2">
                            <i class="bi bi-printer text-primary fs-4"></i>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 11px;">300 DPI Ofset</div>
                                <div class="text-muted" style="font-size: 10px;">Heidelberg baskı</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-4 small border">
                    <div class="fw-bold text-dark mb-2 pb-1 border-bottom d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-cpu me-1 text-primary"></i> Teknik Detaylar</span>
                        <span class="badge bg-dark text-white">Standart</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom text-muted">
                        <span>Üretim Süresi:</span>
                        <strong class="text-dark">1-2 İş Günü (24S Acil Seçeneği)</strong>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom text-muted">
                        <span>Kargo:</span>
                        <strong class="text-success">750 ₺ Üzeri ÜCRETSİZ</strong>
                    </div>
                </div>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- SAĞ: 3 ADIMLI SİPARİŞ, FİYAT MOTORU & KENDİN TASARLA ENTEGRASYONU          -->
        <!-- ========================================================================= -->
        <div class="col-lg-7">
            <form id="productConfigForm" method="POST" enctype="multipart/form-data"
                  data-setup-fee="<?= $product['base_setup_fee'] ?>"
                  data-unit-multiplier="<?= $product['custom_unit_multiplier'] ?>"
                  data-base-sqm="<?= $product['base_sqm_price'] ?>">

                <input type="hidden" name="action" value="add_to_cart">
                <input type="hidden" name="pricing_mode" id="pricingMode" value="<?= ($product['pricing_type'] === 'sqm_calculator' ? 'sqm' : 'package') ?>">
                <input type="hidden" name="package_id" id="selectedPackageId" value="<?= $product['packages'][0]['id'] ?? '' ?>">
                <input type="hidden" name="custom_design_data" id="customDesignDataInput" value="">

                <!-- 1. ADIM: SİPARİŞ YÖNTEMİ (KENDİN TASARLA / DOSYA YÜKLE / GRAFİK DESTEK) -->
                <div class="apple-card p-4 mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold mb-0"><i class="bi bi-magic text-primary me-2"></i>1. Tasarım & Sipariş Yönteminizi Seçin</h5>
                        <span class="badge bg-primary rounded-pill px-3 py-1">Adım 1/3</span>
                    </div>

                    <div class="row g-3">
                        <!-- Yöntem A: Kendin Tasarla -->
                        <div class="col-md-4">
                            <div class="order-path-card text-center h-100" id="pathDesignerCard" onclick="selectOrderPath('designer')">
                                <div class="mb-2 text-primary fs-3">
                                    <i class="bi bi-palette2"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Online Tasarla</h6>
                                <p class="text-muted small mb-0" style="font-size: 11px;">Tarayıcıda logo, metin ve renkleri canlı düzenleyin</p>
                                <span class="badge bg-warning text-dark mt-2 font-monospace" style="font-size: 10px;">✨ 3D Canlı Editör</span>
                            </div>
                        </div>

                        <!-- Yöntem B: Tasarımım Hazır -->
                        <div class="col-md-4">
                            <div class="order-path-card active text-center h-100" id="pathUploadCard" onclick="selectOrderPath('upload')">
                                <div class="mb-2 text-success fs-3">
                                    <i class="bi bi-cloud-arrow-up"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Tasarımım Hazır</h6>
                                <p class="text-muted small mb-0" style="font-size: 11px;">PDF, AI, PSD, CDR veya JPG dosyanızı yükleyin</p>
                                <span class="badge bg-success-subtle text-success mt-2" style="font-size: 10px;">Hızlı Yükleme</span>
                            </div>
                        </div>

                        <!-- Yöntem C: Grafik Desteği -->
                        <div class="col-md-4">
                            <div class="order-path-card text-center h-100" id="pathSupportCard" onclick="selectOrderPath('support')">
                                <div class="mb-2 text-danger fs-3">
                                    <i class="bi bi-headset"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Tasarım Desteği</h6>
                                <p class="text-muted small mb-0" style="font-size: 11px;">Grafik ekibimiz firmanıza özel hazırlasın</p>
                                <span class="badge bg-danger-subtle text-danger mt-2" style="font-size: 10px;">Uzman Desteği</span>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="design_source" id="designSourceInput" value="upload">

                    <!-- TASARIM YÜKLEME ALANI (VARSAYILAN) -->
                    <div id="uploadContainerSection" class="mt-4 p-3 bg-light rounded-4 border">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label small fw-bold mb-0">Tasarım Dosyası Yükleyin (PDF, AI, PSD, CDR, TIFF, JPG, PNG)</label>
                            <span class="badge bg-secondary">Max 100MB</span>
                        </div>
                        <input type="file" name="design_file" id="mainDesignFileInput" class="form-control form-control-lg" accept=".pdf,.ai,.psd,.cdr,.tiff,.jpg,.jpeg,.png,.svg">
                        <small class="text-muted d-block mt-1"><i class="bi bi-info-circle me-1"></i>Baskı öncesi grafik ekibimiz çözünürlük ve kesim paylarını ücretsiz kontrol edecektir.</small>
                    </div>

                    <!-- KENDİN TASARLA BUTONU ALANI -->
                    <div id="designerLaunchSection" class="mt-4 p-4 bg-light rounded-4 border text-center" style="display: none;">
                        <i class="bi bi-palette-fill text-primary fs-1 mb-2 d-block"></i>
                        <h6 class="fw-bold mb-1">Canlı Vektörel Tasarımcınızı Açın</h6>
                        <p class="text-muted small mb-3">Tuval üzerine yazılarınızı yazın, logonuzu ekleyin ve canlı 3D kart simülasyonunu anında görün.</p>
                        <button type="button" class="btn btn-apple btn-apple-orange px-4 py-2" data-bs-toggle="modal" data-bs-target="#embeddedDesignerModal">
                            <i class="bi bi-brush-fill me-2"></i> Tasarımcıyı Başlat
                        </button>
                        <div id="designerSavedBadge" class="mt-2 text-success small fw-bold" style="display:none;">
                            <i class="bi bi-check-circle-fill"></i> Özel tasarımınız hazırlandı ve siparişe bağlandı!
                        </div>
                    </div>

                    <!-- GRAFİK DESTEK ALANI -->
                    <div id="supportContainerSection" class="mt-4 p-3 bg-light rounded-4 border" style="display: none;">
                        <label class="form-label small fw-bold mb-1">Tasarım Talebi & Firma Bilgileriniz</label>
                        <textarea name="design_note" class="form-control" rows="3" placeholder="Kartvizit veya baskıda yer almasını istediğiniz unvan, telefon, logo ve renk tercihlerinizi buraya yazabilirsiniz..."></textarea>
                    </div>
                </div>

                <!-- 2. ADIM: PAKET / ADET / m² SEÇİMİ -->
                <div class="apple-card p-4 mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold mb-0"><i class="bi bi-boxes text-primary me-2"></i>2. Paket & Adet Belirleyin</h5>
                        <span class="badge bg-primary rounded-pill px-3 py-1">Adım 2/3</span>
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
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Sipariş Adedi</label>
                                <input type="number" name="sqm_quantity" id="sqmQuantityInput" class="form-control form-control-lg" value="1" min="1">
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Hazır Paketler Tab & Özel Adet -->
                        <ul class="nav nav-pills nav-fill mb-3 bg-light p-1 rounded-4" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active rounded-4 fw-bold" id="tab-packages-btn" data-bs-toggle="pill" data-bs-target="#tab-packages-pane" type="button">
                                    <i class="bi bi-box-seam me-1"></i> Standart Hazır Paketler
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link rounded-4 fw-bold" id="tab-custom-btn" data-bs-toggle="pill" data-bs-target="#tab-custom-pane" type="button">
                                    <i class="bi bi-pencil-square me-1"></i> Özel Adet Girin
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- Paketler -->
                            <div class="tab-pane fade show active" id="tab-packages-pane">
                                <div class="row g-2">
                                    <?php if (!empty($product['packages'])): ?>
                                        <?php foreach ($product['packages'] as $idx => $pkg): ?>
                                            <div class="col-md-6">
                                                <div class="package-pill-card <?= $idx === 0 ? 'active' : '' ?>"
                                                     data-package-id="<?= $pkg['id'] ?>"
                                                     data-price="<?= $pkg['price'] ?>"
                                                     data-qty="<?= $pkg['quantity'] ?>"
                                                     data-specs="<?= htmlspecialchars($pkg['specs']) ?>">
                                                    <div>
                                                        <strong class="d-block text-dark"><?= number_format($pkg['quantity']) ?> Adet</strong>
                                                        <small class="text-muted" style="font-size: 11px;"><?= htmlspecialchars($pkg['specs']) ?></small>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="fw-bold text-danger fs-6"><?= format_price($pkg['price']) ?></span>
                                                        <small class="text-muted d-block" style="font-size: 10px;">+KDV</small>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Özel Adet Girişi -->
                            <div class="tab-pane fade" id="tab-custom-pane">
                                <div class="p-3 bg-light rounded-4 border">
                                    <label class="form-label small fw-bold">İstediğiniz Özel Adedi Girin</label>
                                    <div class="input-group mb-2">
                                        <input type="number" name="custom_quantity" id="customQuantityInput" class="form-control form-control-lg" placeholder="Örn: 53, 120, 350, 1.250..." min="<?= $product['min_quantity'] ?>">
                                        <span class="input-group-text">Adet</span>
                                    </div>
                                    <small class="text-muted">Min sipariş: <strong><?= $product['min_quantity'] ?> adet</strong>. Fiyat kurulum tabanı ve birim maliyete göre canlı hesaplanır.</small>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 3. ADIM: CANLI FİYAT ÖZETİ & SEPETE EKLEME -->
                <div class="apple-card p-4 border-2 border-primary shadow-lg">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <span class="text-muted small d-block">Seçilen Konfigürasyon:</span>
                            <strong id="displaySpecs" class="text-dark fs-6"><?= htmlspecialchars($initial_price['specs']) ?></strong>
                            <div id="displayUnitPrice" class="text-muted small">Birim Fiyat: <?= format_price($initial_price['unit_price']) ?></div>
                        </div>
                        <div class="text-md-end">
                            <span class="text-muted small d-block">Toplam Tutar (+KDV):</span>
                            <div id="displayTotalPrice" class="fw-extrabold text-danger fs-2"><?= format_price($initial_price['total_price']) ?></div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-apple btn-apple-orange btn-lg w-100 py-3 fw-bold shadow">
                            <i class="bi bi-bag-plus-fill me-2"></i> Sepete Ekle
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- ENTEGRE "KENDİN TASARLA" MODALI (FABRIC.JS VEKTÖREL EDİTÖR)              -->
<!-- ========================================================================= -->
<div class="modal fade" id="embeddedDesignerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-2xl">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold">✨ Online Canlı Vektörel Tasarım Editörü</h5>
                    <small class="text-muted">Yazılarınızı düzenleyin, logonuzu ekleyin ve 3D önizlemede görün</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body p-4">
                
                <!-- Araç Çubuğu -->
                <div class="d-flex flex-wrap gap-2 align-items-center mb-3 p-2 bg-light rounded-3 border">
                    <button type="button" class="btn btn-sm btn-outline-dark rounded-pill" id="modalAddHeadingBtn">
                        <i class="bi bi-type-h1 me-1"></i> Başlık Ekle
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-dark rounded-pill" id="modalAddTextBtn">
                        <i class="bi bi-fonts me-1"></i> Metin Ekle
                    </button>

                    <label class="btn btn-sm btn-apple btn-apple-orange rounded-pill mb-0" style="cursor: pointer;">
                        <i class="bi bi-image me-1"></i> Logo Yükle
                        <input type="file" id="modalLogoUploadInput" class="d-none" accept="image/*">
                    </label>

                    <div class="vr mx-2"></div>

                    <div class="d-flex align-items-center gap-2">
                        <label class="small fw-bold mb-0">Arka Plan:</label>
                        <input type="color" id="modalBgColorPicker" class="form-control form-control-color p-0 border-0" value="#ffffff" title="Tuval Rengi">
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <label class="small fw-bold mb-0">Yazı Rengi:</label>
                        <input type="color" id="modalTextColorPicker" class="form-control form-control-color p-0 border-0" value="#111113" title="Yazı Rengi">
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill ms-auto" id="modalDeleteBtn">
                        <i class="bi bi-trash"></i> Sil
                    </button>
                </div>

                <!-- Tuval Alanı -->
                <div class="text-center py-3 bg-secondary bg-opacity-10 rounded-4">
                    <div class="designer-canvas-box">
                        <canvas id="modalFabricCanvas" width="500" height="300"></canvas>
                    </div>
                </div>

            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-apple btn-apple-orange rounded-pill px-5 fw-bold" id="applyModalDesignBtn">
                    <i class="bi bi-check2-circle me-1"></i> Tasarımı 3D Sahneye ve Siparişe Uygula
                </button>
            </div>
        </div>
    </div>
</div>

<!-- CANLI MOUSE 3D EĞİM VE ETKİLEŞİM SCRİPTİ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    init3DCardTilt();
    initEmbeddedDesigner();
});

// 1. Sipariş Yolu Seçimi
function selectOrderPath(path) {
    document.querySelectorAll('.order-path-card').forEach(c => c.classList.remove('active'));
    document.getElementById('designSourceInput').value = path;

    document.getElementById('uploadContainerSection').style.display = 'none';
    document.getElementById('designerLaunchSection').style.display = 'none';
    document.getElementById('supportContainerSection').style.display = 'none';

    if (path === 'designer') {
        document.getElementById('pathDesignerCard').classList.add('active');
        document.getElementById('designerLaunchSection').style.display = 'block';
    } else if (path === 'upload') {
        document.getElementById('pathUploadCard').classList.add('active');
        document.getElementById('uploadContainerSection').style.display = 'block';
    } else if (path === 'support') {
        document.getElementById('pathSupportCard').classList.add('active');
        document.getElementById('supportContainerSection').style.display = 'block';
    }
}

// 2. 3D Mouse Eğimi & Parlama
function init3DCardTilt() {
    const stage = document.getElementById('mockupStageBox');
    const card = document.getElementById('interactiveCard');
    const glare = document.getElementById('cardGlare');
    if (!stage || !card) return;

    stage.addEventListener('mousemove', function(e) {
        const rect = stage.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const centerX = rect.width / 2;
        const centerY = rect.height / 2;

        const rotateX = -((y - centerY) / centerY) * 18;
        const rotateY = ((x - centerX) / centerX) * 22;

        card.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.02)`;
        
        if (glare) {
            const glareX = (x / rect.width) * 100;
            const glareY = (y / rect.height) * 100;
            glare.style.background = `radial-gradient(circle at ${glareX}% ${glareY}%, rgba(255,255,255,0.4) 0%, rgba(255,255,255,0) 65%)`;
        }
    });

    stage.addEventListener('mouseleave', function() {
        card.style.transform = 'rotateX(0deg) rotateY(0deg) scale(1)';
    });
}

function resetCardTilt() {
    const card = document.getElementById('interactiveCard');
    if (card) card.style.transform = 'rotateX(0deg) rotateY(0deg) scale(1)';
}

// 3. Entegre Fabric.js Tasarımcı Modalı
let modalCanvas = null;
function initEmbeddedDesigner() {
    const modalEl = document.getElementById('embeddedDesignerModal');
    if (!modalEl) return;

    modalEl.addEventListener('shown.bs.modal', function () {
        if (!modalCanvas) {
            modalCanvas = new fabric.Canvas('modalFabricCanvas', {
                backgroundColor: '#ffffff',
                preserveObjectStacking: true
            });

            const title = new fabric.IText('TAM BASKI', {
                left: 60, top: 60, fontFamily: 'Helvetica', fontSize: 30, fontWeight: 'bold', fill: '#111113'
            });
            const subtitle = new fabric.IText('Ahmet Yılmaz | Genel Müdür', {
                left: 60, top: 120, fontFamily: 'Helvetica', fontSize: 16, fill: '#f15a24'
            });
            const contact = new fabric.IText('📞 0850 308 00 00 • ✉️ info@tambaski.com.tr', {
                left: 60, top: 180, fontFamily: 'Helvetica', fontSize: 12, fill: '#64748b'
            });

            modalCanvas.add(title);
            modalCanvas.add(subtitle);
            modalCanvas.add(contact);
        }
    });

    document.getElementById('modalAddHeadingBtn')?.addEventListener('click', function() {
        if (!modalCanvas) return;
        const h = new fabric.IText('Yeni Başlık', {
            left: 100, top: 100, fontFamily: 'Helvetica', fontSize: 26, fontWeight: 'bold', fill: document.getElementById('modalTextColorPicker').value
        });
        modalCanvas.add(h);
    });

    document.getElementById('modalAddTextBtn')?.addEventListener('click', function() {
        if (!modalCanvas) return;
        const t = new fabric.IText('Metin...', {
            left: 100, top: 140, fontFamily: 'Helvetica', fontSize: 15, fill: document.getElementById('modalTextColorPicker').value
        });
        modalCanvas.add(t);
    });

    document.getElementById('modalLogoUploadInput')?.addEventListener('change', function(e) {
        if (!modalCanvas || !e.target.files[0]) return;
        const reader = new FileReader();
        reader.onload = function(f) {
            fabric.Image.fromURL(f.target.result, function(img) {
                img.scaleToWidth(120);
                img.set({ left: 300, top: 50 });
                modalCanvas.add(img);
            });
        };
        reader.readAsDataURL(e.target.files[0]);
    });

    document.getElementById('modalDeleteBtn')?.addEventListener('click', function() {
        if (!modalCanvas) return;
        const active = modalCanvas.getActiveObject();
        if (active) modalCanvas.remove(active);
    });

    document.getElementById('modalBgColorPicker')?.addEventListener('input', function() {
        if (!modalCanvas) return;
        modalCanvas.setBackgroundColor(this.value, modalCanvas.renderAll.bind(modalCanvas));
    });

    // Tasarımı Uygulama
    document.getElementById('applyModalDesignBtn')?.addEventListener('click', function() {
        if (!modalCanvas) return;
        const dataUrl = modalCanvas.toDataURL({ format: 'png', quality: 0.95 });
        
        // 3D Karta Giydir
        const card = document.getElementById('interactiveCard');
        if (card) {
            card.style.backgroundImage = `url(${dataUrl})`;
            card.style.backgroundSize = 'cover';
        }

        // Form girdisine ekle
        document.getElementById('customDesignDataInput').value = dataUrl;
        document.getElementById('designerSavedBadge').style.display = 'block';

        // Modalı Kapat
        bootstrap.Modal.getInstance(modalEl).hide();
    });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
