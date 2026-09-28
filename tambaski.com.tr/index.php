<?php
require_once __DIR__ . '/config/config.php';

$pageTitle = Helper::getSetting('site_title', 'TamBaskı - Online Matbaa ve Dijital Baskı');
$pageDesc = Helper::getSetting('site_slogan', 'Türkiye\'nin En Hızlı ve Kaliteli Online Matbaası');

$productModel = new Product();
$featuredProducts = $productModel->getAll(8, null, true);
$urgentProducts = $productModel->getAll(4, null, false, true);
$dbConn = Database::getInstance()->getConnection();
$categories = $dbConn ? $dbConn->query("SELECT * FROM categories WHERE status = 1 ORDER BY sort_order ASC")->fetchAll() : [];
$templates = $dbConn ? $dbConn->query("SELECT dt.*, p.slug as prod_slug, p.name as prod_name FROM design_templates dt JOIN products p ON dt.product_id = p.id WHERE dt.status = 1 LIMIT 3")->fetchAll() : [];

require_once __DIR__ . '/includes/header.php';
?>

<!-- Apple Tarzı Otomatik Ürün Slaytı (Hero Carousel) -->
<section class="hero-slider-section position-relative overflow-hidden">
    <!-- Ambient Animated Gradient Mesh Background -->
    <div class="hero-ambient-glow orb-1"></div>
    <div class="hero-ambient-glow orb-2"></div>
    <div class="hero-ambient-glow orb-3"></div>

    <div id="tamBaskiHeroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000" data-bs-pause="hover">
        
        <!-- Slayt İlerleme Çubuğu (Apple Progress Bar) -->
        <div class="hero-slider-progress"><div class="hero-slider-progress-bar" id="heroProgressBar"></div></div>

        <!-- Slayt Göstergeleri (Segmented Indicators) -->
        <div class="hero-slider-indicators">
            <button type="button" data-bs-target="#tamBaskiHeroCarousel" data-bs-slide-to="0" class="hero-indicator-btn active">
                <span class="ind-num">01</span> Kartvizit
            </button>
            <button type="button" data-bs-target="#tamBaskiHeroCarousel" data-bs-slide-to="1" class="hero-indicator-btn">
                <span class="ind-num">02</span> Broşür &amp; El İlanı
            </button>
            <button type="button" data-bs-target="#tamBaskiHeroCarousel" data-bs-slide-to="2" class="hero-indicator-btn">
                <span class="ind-num">03</span> Cepli Dosya
            </button>
            <button type="button" data-bs-target="#tamBaskiHeroCarousel" data-bs-slide-to="3" class="hero-indicator-btn">
                <span class="ind-num">04</span> ⚡ 24S Acil Baskı
            </button>
            <button type="button" data-bs-target="#tamBaskiHeroCarousel" data-bs-slide-to="4" class="hero-indicator-btn">
                <span class="ind-num">05</span> Etiket &amp; Kutu
            </button>
        </div>

        <div class="carousel-inner">
            
            <!-- SLIDE 1: KARTVİZİT -->
            <div class="carousel-item active">
                <div class="container">
                    <div class="row hero-slide-row">
                        <!-- Sol Bilgi Alanı -->
                        <div class="col-lg-6 slide-content-col">
                            <span class="hero-pill-badge">
                                <span class="badge-dot"></span> 💎 Prestij &amp; Çok Satan
                            </span>
                            <h1 class="slide-headline">
                                Kartvizitte İlk İzlenim,<br>
                                <span class="slide-grad-kartvizit">Kusursuz Prestij.</span>
                            </h1>
                            <p class="slide-description">
                                350gr Mat Kuşe, Parlak Selefon, Kabartma Lak ve Özel Kesim seçenekleri. 1.000 adetten başlayan avantajlı toptancı fiyatlarıyla firmanızı zirveye taşıyın.
                            </p>
                            <div class="slide-price-pill">
                                <i class="bi bi-tag-fill text-primary"></i> 1.000 Adet Paket: <strong>484 ₺</strong> (+KDV) • <strong>Bedava Kargo</strong>
                            </div>
                            <div class="d-flex gap-3 flex-wrap">
                                <a href="<?= SITE_URL ?>/category.php?slug=kartvizit" class="btn-apple btn-apple-pink px-4 py-3 btn-shine">
                                    <i class="bi bi-card-heading"></i> Kartvizitleri İncele
                                </a>
                                <a href="<?= SITE_URL ?>/category.php?slug=kartvizit" class="btn-apple btn-apple-secondary px-4 py-3">
                                    <i class="bi bi-palette2"></i> Canlı Tasarla
                                </a>
                            </div>
                            <div class="slide-features-check">
                                <span><i class="bi bi-check-circle-fill"></i> Çift Yön Renkli</span>
                                <span><i class="bi bi-check-circle-fill"></i> 350gr Kuşe</span>
                                <span><i class="bi bi-check-circle-fill"></i> Kabartma Lak</span>
                            </div>
                        </div>

                        <!-- Sağ 3D Mockup Alanı -->
                        <div class="col-lg-6 d-flex align-items-center justify-content-center">
                            <div class="slide-visual-stage stage-kartvizit">
                                <div class="bizcard-back"></div>
                                <div class="bizcard-main">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="bizcard-chip"></div>
                                        <span class="badge bg-secondary bg-opacity-50 text-white font-monospace" style="font-size: 8px;">LUXURY 350GR</span>
                                    </div>
                                    <div>
                                        <div class="bizcard-logo">TAM <span>BASKI!</span></div>
                                        <div class="bizcard-line"></div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-end">
                                        <div class="bizcard-meta">Ofset Kartvizit Serisi</div>
                                        <div class="text-warning small"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                                    </div>
                                </div>
                                <div class="floating-badge badge-pos-left">
                                    <i class="bi bi-patch-check-fill text-primary"></i> 350gr Mat Kuşe &amp; Lak
                                </div>
                                <div class="floating-badge badge-pos-right">
                                    <i class="bi bi-box-seam text-success"></i> 1.000 Adet Kutulu
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 2: BROŞÜR & EL İLANI -->
            <div class="carousel-item">
                <div class="container">
                    <div class="row hero-slide-row">
                        <!-- Sol Bilgi Alanı -->
                        <div class="col-lg-6 slide-content-col">
                            <span class="hero-pill-badge">
                                <span class="badge-dot"></span> 🚀 Yüksek Tiraj &amp; Ofset Baskı
                            </span>
                            <h1 class="slide-headline">
                                Kampanyalarınızı Duyurun,<br>
                                <span class="slide-grad-brosur">Tirajlı Fiyatlarla Kazanın.</span>
                            </h1>
                            <p class="slide-description">
                                A4, A5, kırımlı broşür ve el ilanlarında Heidelberg ofset kalitesi. Canlı renkler, çift yön baskı ve Türkiye'nin her yerine anlaşmalı hızlı kargo.
                            </p>
                            <div class="slide-price-pill">
                                <i class="bi bi-tag-fill text-danger"></i> 2.000 Adet Broşür: <strong>1.150 ₺</strong>'den Başlayan Fiyatlarla
                            </div>
                            <div class="d-flex gap-3 flex-wrap">
                                <a href="<?= SITE_URL ?>/category.php?slug=el-ilani-brosur" class="btn-apple btn-apple-pink px-4 py-3 btn-shine">
                                    <i class="bi bi-file-earmark-richtext"></i> Broşür Fiyatı Hesapla
                                </a>
                                <a href="<?= SITE_URL ?>/category.php?slug=el-ilani-brosur" class="btn-apple btn-apple-secondary px-4 py-3">
                                    <i class="bi bi-grid"></i> Şablonları Gör
                                </a>
                            </div>
                            <div class="slide-features-check">
                                <span><i class="bi bi-check-circle-fill"></i> A4 &amp; A5 Ebatları</span>
                                <span><i class="bi bi-check-circle-fill"></i> Z / Akordiyon Katlama</span>
                                <span><i class="bi bi-check-circle-fill"></i> Canlı Renk Garantisi</span>
                            </div>
                        </div>

                        <!-- Sağ 3D Mockup Alanı -->
                        <div class="col-lg-6 d-flex align-items-center justify-content-center">
                            <div class="slide-visual-stage stage-brosur">
                                <div class="brochure-3d">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-danger bg-opacity-10 text-danger fw-bold" style="font-size: 10px;">OFSET BROŞÜR</span>
                                        <div class="brochure-cmyk">
                                            <span class="cmyk-c"></span><span class="cmyk-m"></span><span class="cmyk-y"></span><span class="cmyk-k"></span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark fs-6">Tanıtım ve Kampanya Broşürleri</div>
                                        <div class="brochure-grid">
                                            <div class="grid-box"><i class="bi bi-file-text me-1 text-primary"></i>Ön Yüz</div>
                                            <div class="grid-box"><i class="bi bi-card-image me-1 text-danger"></i>Kırım</div>
                                            <div class="grid-box"><i class="bi bi-file-text-fill me-1 text-success"></i>Arka Yüz</div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-light">
                                        <span class="text-muted" style="font-size: 11px;">135gr Parlak Kuşe</span>
                                        <span class="badge bg-primary text-white" style="font-size: 11px;">Sipariş Ver →</span>
                                    </div>
                                </div>
                                <div class="floating-badge badge-pos-left">
                                    <i class="bi bi-lightning-charge-fill text-warning"></i> 24 Saatte Hızlı Ofset
                                </div>
                                <div class="floating-badge badge-pos-right">
                                    <i class="bi bi-truck text-primary"></i> Ücretsiz Hızlı Kargo
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 3: CEPLİ DOSYA & KURUMSAL -->
            <div class="carousel-item">
                <div class="container">
                    <div class="row hero-slide-row">
                        <!-- Sol Bilgi Alanı -->
                        <div class="col-lg-6 slide-content-col">
                            <span class="hero-pill-badge">
                                <span class="badge-dot"></span> 📁 Kurumsal Kimlik &amp; Bayilik
                            </span>
                            <h1 class="slide-headline">
                                Firmanıza Özel Dosyalar,<br>
                                <span class="slide-grad-kurumsal">Eksiksiz Kurumsal Kimlik.</span>
                            </h1>
                            <p class="slide-description">
                                Sunum dosyası, cepli dosya, antetli kağıt, diplomat zarf ve oto kokusu. Bayilerimize özel %25'e varan toptan iskonto fırsatıyla tüm kurumsal baskılarınız tek elden.
                            </p>
                            <div class="slide-price-pill">
                                <i class="bi bi-briefcase-fill text-success"></i> E-Bayi Başvurusu ile <strong>%25 İndirim</strong> Fırsatı
                            </div>
                            <div class="d-flex gap-3 flex-wrap">
                                <a href="<?= SITE_URL ?>/category.php?slug=kurumsal-urunler" class="btn-apple btn-apple-pink px-4 py-3 btn-shine">
                                    <i class="bi bi-folder2-open"></i> Kurumsal Ürünleri Gör
                                </a>
                                <a href="<?= SITE_URL ?>/dealer_apply.php" class="btn-apple btn-apple-secondary px-4 py-3">
                                    <i class="bi bi-person-badge"></i> E-Bayi Ol
                                </a>
                            </div>
                            <div class="slide-features-check">
                                <span><i class="bi bi-check-circle-fill"></i> Kartvizit Yuvalı Cep</span>
                                <span><i class="bi bi-check-circle-fill"></i> 350gr Amerikan Bristol</span>
                                <span><i class="bi bi-check-circle-fill"></i> Mat &amp; Parlak Selefon</span>
                            </div>
                        </div>

                        <!-- Sağ 3D Mockup Alanı -->
                        <div class="col-lg-6 d-flex align-items-center justify-content-center">
                            <div class="slide-visual-stage stage-kurumsal">
                                <div class="folder-3d">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-info bg-opacity-25 text-info font-monospace" style="font-size: 8px;">KURUMSAL DOSYA</span>
                                        <span class="text-white small fw-bold">TAM BASKI</span>
                                    </div>
                                    <div class="folder-pocket">
                                        <div class="text-white small fw-semibold">Kartvizit &amp; Belge Yuvası</div>
                                        <span class="badge bg-primary text-white" style="font-size: 9px;">A4 Uyumlu</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="text-muted" style="font-size: 11px;">Özel Bıçak Kesim</div>
                                        <div class="text-warning small"><i class="bi bi-shield-fill-check"></i> Garantili</div>
                                    </div>
                                </div>
                                <div class="floating-badge badge-pos-left">
                                    <i class="bi bi-award-fill text-warning"></i> Özel Kabartma Laklı
                                </div>
                                <div class="floating-badge badge-pos-right">
                                    <i class="bi bi-percent text-success"></i> Bayilere %25 İndirim
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 4: 24S ACİL BASKI -->
            <div class="carousel-item">
                <div class="container">
                    <div class="row hero-slide-row">
                        <!-- Sol Bilgi Alanı -->
                        <div class="col-lg-6 slide-content-col">
                            <span class="hero-pill-badge">
                                <span class="badge-dot" style="background:#ef4444;box-shadow:0 0 8px #ef4444;"></span> ⚡ Acil Teslimat
                            </span>
                            <h1 class="slide-headline">
                                Zamanınız Az mı?<br>
                                <span class="slide-grad-acil">24 Saatte Kapınızda!</span>
                            </h1>
                            <p class="slide-description">
                                Fuarınız, açılışınız veya toplantınız mı var? Acil kartvizit, broşür ve etiket siparişleriniz aynı gün baskıya girsin, 24 saat içinde kapınıza gelsin.
                            </p>
                            <div class="slide-price-pill">
                                <i class="bi bi-lightning-charge-fill text-danger"></i> <strong>Aynı Gün Baskı</strong> • 24 Saat Ekspres Kargo
                            </div>
                            <div class="d-flex gap-3 flex-wrap">
                                <a href="<?= SITE_URL ?>/category.php?urgent=1" class="btn-apple btn-apple-pink px-4 py-3 btn-shine">
                                    <i class="bi bi-lightning-fill"></i> 24S Acil Baskıları Gör
                                </a>
                                <a href="<?= SITE_URL ?>/category.php" class="btn-apple btn-apple-secondary px-4 py-3">
                                    <i class="bi bi-grid"></i> Tüm Kategoriler
                                </a>
                            </div>
                            <div class="slide-features-check">
                                <span><i class="bi bi-check-circle-fill"></i> Aynı Gün Üretim</span>
                                <span><i class="bi bi-check-circle-fill"></i> Hızlı Kurye / Kargo</span>
                                <span><i class="bi bi-check-circle-fill"></i> Baskı Öncesi Grafik Kontrol</span>
                            </div>
                        </div>

                        <!-- Sağ 3D Mockup Alanı -->
                        <div class="col-lg-6 d-flex align-items-center justify-content-center">
                            <div class="slide-visual-stage stage-acil">
                                <div class="speed-card">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-danger text-white fw-bold"><i class="bi bi-lightning-fill"></i> 24 SAATTE TESLİM</span>
                                        <i class="bi bi-stopwatch text-danger fs-4"></i>
                                    </div>
                                    <div class="my-auto">
                                        <div class="fw-bold text-dark fs-5">Ekspres Matbaa Hattı</div>
                                        <div class="text-muted small">Saat 14:00'e kadar verilen siparişler aynı gün kargoda!</div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-light">
                                        <span class="badge bg-success text-white">Aktif Hat</span>
                                        <span class="text-danger fw-bold small">Hemen Sipariş Ver →</span>
                                    </div>
                                </div>
                                <div class="floating-badge badge-pos-left">
                                    <i class="bi bi-truck-flatbed text-primary"></i> Anlaşmalı Hızlı Kargo
                                </div>
                                <div class="floating-badge badge-pos-right">
                                    <i class="bi bi-shield-check text-success"></i> %100 Memnuniyet
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 5: RULO ETİKET & KUTU AMBALAJ -->
            <div class="carousel-item">
                <div class="container">
                    <div class="row hero-slide-row">
                        <!-- Sol Bilgi Alanı -->
                        <div class="col-lg-6 slide-content-col">
                            <span class="hero-pill-badge">
                                <span class="badge-dot" style="background:#8b5cf6;box-shadow:0 0 8px #8b5cf6;"></span> 🏷️ Ambalaj &amp; Özel Kesim
                            </span>
                            <h1 class="slide-headline">
                                Markanıza Değer Katan<br>
                                <span class="slide-grad-etiket">Etiket &amp; Kutu Çözümleri.</span>
                            </h1>
                            <p class="slide-description">
                                Özel lazer bıçak kesimli rulo etiketler, şeffaf &amp; kraft stickerlar ve baskılı ürün ambalaj kutuları. Suya, neme dayanıklı yapışkan ve canlı dijital ofset baskı.
                            </p>
                            <div class="slide-price-pill">
                                <i class="bi bi-stars text-warning"></i> Minimum <strong>500 Adetten Başlayan</strong> Hızlı &amp; Esnek Üretim
                            </div>
                            <div class="d-flex gap-3 flex-wrap">
                                <a href="<?= SITE_URL ?>/category.php?slug=etiket-sticker" class="btn-apple btn-apple-pink px-4 py-3 btn-shine">
                                    <i class="bi bi-tags-fill"></i> Etiket Modelleri
                                </a>
                                <a href="<?= SITE_URL ?>/category.php" class="btn-apple btn-apple-secondary px-4 py-3">
                                    <i class="bi bi-box-seam"></i> Kutu &amp; Ambalaj
                                </a>
                            </div>
                            <div class="slide-features-check">
                                <span><i class="bi bi-check-circle-fill"></i> Lazer Özel Kesim</span>
                                <span><i class="bi bi-check-circle-fill"></i> Suya &amp; Isıya Dayanıklı</span>
                                <span><i class="bi bi-check-circle-fill"></i> Rulo &amp; Tabaka Seçeneği</span>
                            </div>
                        </div>

                        <!-- Sağ 3D Mockup Alanı -->
                        <div class="col-lg-6 d-flex align-items-center justify-content-center">
                            <div class="slide-visual-stage stage-etiket">
                                <div class="package-box-3d">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-white bg-opacity-25 text-white font-monospace" style="font-size: 8px;">CUSTOM PACKAGING</span>
                                        <span class="text-white small fw-bold"><i class="bi bi-shield-check"></i> PRO SEAL</span>
                                    </div>
                                    <div class="sticker-showcase">
                                        <div class="sticker-item sticker-gold">GOLD</div>
                                        <div class="sticker-item sticker-neon">ECO</div>
                                        <div class="sticker-item sticker-silver">UV LAK</div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-white border-opacity-25">
                                        <div class="text-white-50" style="font-size: 11px;">Koli &amp; Rulo Çözümleri</div>
                                        <span class="badge bg-warning text-dark fw-bold">Yüksek Yapışkan</span>
                                    </div>
                                </div>
                                <div class="floating-badge badge-pos-left">
                                    <i class="bi bi-droplet-half text-info"></i> Su Geçirmez Malzeme
                                </div>
                                <div class="floating-badge badge-pos-right">
                                    <i class="bi bi-scissors text-danger"></i> İstenen Şekilde Kesim
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Önceki / Sonraki Ok Butonları -->
        <button class="hero-slider-arrow prev" type="button" data-bs-target="#tamBaskiHeroCarousel" data-bs-slide="prev" aria-label="Önceki Slayt">
            <i class="bi bi-chevron-left"></i>
        </button>
        <button class="hero-slider-arrow next" type="button" data-bs-target="#tamBaskiHeroCarousel" data-bs-slide="next" aria-label="Sonraki Slayt">
            <i class="bi bi-chevron-right"></i>
        </button>

    </div>
</section>

<!-- Slider Gösterge & İlerleme Scripti -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var carouselEl = document.getElementById('tamBaskiHeroCarousel');
    var progressBar = document.getElementById('heroProgressBar');
    if (!carouselEl) return;

    var indBtns = carouselEl.querySelectorAll('.hero-indicator-btn');
    var slideDuration = 5000;
    var progressInterval = null;
    var startTime = Date.now();
    var isPaused = false;

    function startProgress() {
        if (!progressBar) return;
        clearInterval(progressInterval);
        startTime = Date.now();
        progressBar.style.width = '0%';
        
        progressInterval = setInterval(function() {
            if (isPaused) return;
            var elapsed = Date.now() - startTime;
            var pct = Math.min((elapsed / slideDuration) * 100, 100);
            progressBar.style.width = pct + '%';
            if (pct >= 100) {
                clearInterval(progressInterval);
            }
        }, 30);
    }

    carouselEl.addEventListener('mouseenter', function() {
        isPaused = true;
    });

    carouselEl.addEventListener('mouseleave', function() {
        isPaused = false;
        startTime = Date.now() - (parseFloat(progressBar.style.width || 0) / 100 * slideDuration);
    });

    carouselEl.addEventListener('slide.bs.carousel', function(e) {
        indBtns.forEach(function(btn, idx) {
            if (idx === e.to) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
        startProgress();
    });

    startProgress();
});
</script>

<!-- Avantajlar / Güven Faktörleri -->
<section class="py-4 bg-white border-bottom">
    <div class="container">
        <div class="row g-3 text-center">
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-vector-pen text-primary fs-3"></i>
                    <div class="text-start">
                        <div class="fw-bold small">Online Vektörel Tasarım</div>
                        <div class="text-muted" style="font-size: 11px;">Tarayıcıda Canlı Düzenle</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-truck text-success fs-3"></i>
                    <div class="text-start">
                        <div class="fw-bold small">750 ₺ Üzeri Bedava Kargo</div>
                        <div class="text-muted" style="font-size: 11px;">Anlaşmalı Hızlı Kurye</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-shield-check text-primary fs-3"></i>
                    <div class="text-start">
                        <div class="fw-bold small">PayTR & iyzico 3D Güvenlik</div>
                        <div class="text-muted" style="font-size: 11px;">12 Taksit İmkanı</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-headset text-danger fs-3"></i>
                    <div class="text-start">
                        <div class="fw-bold small">Ücretsiz Grafik Desteği</div>
                        <div class="text-muted" style="font-size: 11px;">Baskı Öncesi Kontrol</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Popüler Kategoriler -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h3 class="fw-bold mb-1">Popüler Baskı Kategorileri</h3>
                <p class="text-muted small mb-0">En çok tercih edilen ofset ve dijital matbaa ürünleri</p>
            </div>
            <a href="<?= SITE_URL ?>/category.php" class="btn btn-sm btn-apple-outline d-none d-sm-inline-flex">Tümünü Gör <i class="bi bi-arrow-right ms-1"></i></a>
        </div>

        <div class="row g-3">
            <?php foreach ($categories as $cat): ?>
                <div class="col-lg-2 col-md-4 col-6">
                    <a href="<?= SITE_URL ?>/category.php?slug=<?= $cat['slug'] ?>" class="text-decoration-none">
                        <div class="apple-card p-4 text-center h-100">
                            <div class="mb-3">
                                <i class="<?= $cat['icon'] ?> text-primary" style="font-size: 32px;"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1" style="font-size: 14px;"><?= htmlspecialchars($cat['name']) ?></h6>
                            <span class="text-muted" style="font-size: 11px;">Ürünleri İncele →</span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Öne Çıkan Ürünler & Fiyat Hesaplayıcı Vitrini -->
<section class="py-5 bg-white border-top border-bottom">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="badge bg-primary-subtle text-primary px-2 py-1 rounded-pill mb-1">🔥 Çok Satanlar</span>
                <h3 class="fw-bold mb-1">Öne Çıkan Matbaa Ürünleri</h3>
                <p class="text-muted small mb-0">Yüksek baskı kalitesi, zengin varyant seçenekleri ve tiraj indirimleri</p>
            </div>
        </div>

        <div class="row g-4">
            <?php foreach ($featuredProducts as $prod): ?>
                <div class="col-lg-3 col-md-6">
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
                                <i class="bi bi-printer text-muted" style="font-size: 64px; opacity: 0.3;"></i>
                            <?php endif; ?>
                        </div>

                        <div class="product-body">
                            <span class="text-muted small mb-1"><?= htmlspecialchars($prod['category_name'] ?? 'Matbaa') ?></span>
                            <a href="<?= SITE_URL ?>/product.php?slug=<?= $prod['slug'] ?>" class="product-title"><?= htmlspecialchars($prod['name']) ?></a>
                            <p class="product-desc"><?= htmlspecialchars($prod['short_description'] ?? '') ?></p>
                            
                            <div class="product-price-row">
                                <div>
                                    <span class="product-price"><?= Helper::formatPrice($prod['base_price']) ?></span>
                                    <span class="product-price-sub">'den başlayan fiyatlarla <small class="text-muted fw-normal" style="font-size:11px;">(+KDV)</small></span>
                                </div>
                                <a href="<?= SITE_URL ?>/product.php?slug=<?= $prod['slug'] ?>" class="btn btn-sm btn-apple">
                                    Hesapla <i class="bi bi-chevron-right ms-1"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Hazır Vektörel Şablonlar (Canlı Önizleme & Düzenleme Vitrini) -->
<?php if (!empty($templates)): ?>
<section class="py-5">
    <div class="container">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="badge bg-purple text-white px-3 py-1 rounded-pill mb-2" style="background:#8b5cf6;">🎨 Sıfırdan Tasarım Yapmaya Son</span>
            <h3 class="fw-bold">Hazır Vektörel Kartvizit Şablonları</h3>
            <p class="text-muted small">İstediğiniz şablonu seçin, bilgilerinizi yazın, anında baskıya gönderelim.</p>
        </div>

        <div class="row g-4 justify-content-center">
            <?php foreach ($templates as $tpl): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="apple-card p-3 h-100 d-flex flex-column">
                        <div class="template-preview-box rounded-3 mb-3 p-2">
                            <?= $tpl['default_svg'] ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($tpl['title']) ?></h6>
                                <span class="badge bg-light text-muted border"><?= htmlspecialchars($tpl['category']) ?></span>
                            </div>
                            <a href="<?= SITE_URL ?>/product.php?slug=<?= $tpl['prod_slug'] ?>&tpl=<?= $tpl['id'] ?>" class="btn btn-sm btn-apple-pink">
                                <i class="bi bi-pencil-square me-1"></i> Bu Şablonu Düzenle
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- E-Bayi & B2B Çağrısı -->
<section class="py-5 bg-dark text-white rounded-5 mx-3 mx-lg-5 my-5" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);">
    <div class="container py-3">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-2">🤝 Ajanslar & Grafikerler İçin</span>
                <h2 class="fw-bold mb-2">TamBaskı E-Bayi Ailesine Katılın</h2>
                <p class="text-light opacity-75 mb-0">
                    Reklam ajansları, matbaacılar ve serbest grafik tasarımcılar için %25'e varan özel iskonto tarifesi, faturasız/isimsiz kargo gönderimi ve öncelikli üretim ayrıcalıkları.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?= SITE_URL ?>/dealer_apply.php" class="btn btn-lg btn-warning text-dark fw-bold px-4 py-3 rounded-pill shadow">
                    <i class="bi bi-award-fill me-2"></i>E-Bayi Başvurusu Yap
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
