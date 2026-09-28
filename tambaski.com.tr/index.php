<?php
/**
 * TAMBASKI.COM.TR - Anasayfa
 */
$page_title = "TamBaskı – Online Matbaa, Dijital Baskı & Pleksi / Dekota Kesim Merkezi";
require_once __DIR__ . '/includes/header.php';

$featured_products = get_all_products(null, true);
?>

<!-- Apple Tarzı Otomatik Ürün Slaytı (Hero Carousel) -->
<section class="hero-slider-section position-relative overflow-hidden">
    <div class="hero-ambient-glow orb-1"></div>
    <div class="hero-ambient-glow orb-2"></div>

    <div id="tamBaskiHeroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-inner">
            
            <!-- SLIDE 1: KARTVİZİT -->
            <div class="carousel-item active">
                <div class="container">
                    <div class="row align-items-center py-5">
                        <div class="col-lg-6">
                            <span class="hero-pill-badge">
                                <i class="bi bi-patch-check-fill text-warning"></i> Prestij & Çok Satan
                            </span>
                            <h1 class="slide-headline">
                                Kartvizitte İlk İzlenim,<br>
                                <span class="slide-grad-kartvizit">Kusursuz Prestij.</span>
                            </h1>
                            <p class="slide-description">
                                250gr Solvent Ekonomik, 350gr Mat Kuşe, Kabartma Lak, Altın Varak ve Şeffaf PVC seçenekleri. 1.000 adetten başlayan hazır paketler veya ihtiyacınıza özel adet girişi.
                            </p>
                            <div class="slide-price-pill">
                                <i class="bi bi-tag-fill text-primary"></i> 1.000 Adet Paket: <strong>1.000 ₺</strong> • <strong>Bedava Kargo</strong>
                            </div>
                            <div class="d-flex gap-3 flex-wrap">
                                <a href="product.php?slug=ekonomik-kartvizit-250gr" class="btn btn-apple btn-apple-orange px-4 py-3">
                                    <i class="bi bi-calculator me-1"></i> Fiyat Hesapla
                                </a>
                                <a href="category.php?slug=kartvizit" class="btn btn-apple btn-apple-secondary px-4 py-3">
                                    Tüm Kartvizitler
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-6 text-center mt-4 mt-lg-0">
                            <div class="p-4 bg-white rounded-5 shadow-lg border d-inline-block">
                                <i class="bi bi-person-badge text-warning" style="font-size: 100px;"></i>
                                <h4 class="fw-bold mt-2 mb-1">Kurumsal Prestij Kartvizit</h4>
                                <span class="badge bg-dark rounded-pill px-3 py-2">350gr Mat Kuşe + Kabartma Lak</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 2: DEKOTA & PLEKSİ KESİM -->
            <div class="carousel-item">
                <div class="container">
                    <div class="row align-items-center py-5">
                        <div class="col-lg-6">
                            <span class="hero-pill-badge">
                                <i class="bi bi-scissors text-danger"></i> CNC & Lazer Kesim Parkuru
                            </span>
                            <h1 class="slide-headline">
                                Dekota & Pleksi Kesim,<br>
                                <span class="slide-grad-kesim">İstediğiniz Ebat ve Şekilde.</span>
                            </h1>
                            <p class="slide-description">
                                3mm - 5mm Dekota (Foreks) UV baskı, Pleksi lazer harf ve tabela kesimi. Milimetrik ebat girin, anlık m² ve birim fiyatınızı otomatik hesaplayın.
                            </p>
                            <div class="slide-price-pill">
                                <i class="bi bi-aspect-ratio text-success"></i> m² Başlangıç Fiyatı: <strong>320 ₺/m²</strong>
                            </div>
                            <div class="d-flex gap-3 flex-wrap">
                                <a href="product.php?slug=dekota-foreks-baski-kesim" class="btn btn-apple btn-apple-orange px-4 py-3">
                                    <i class="bi bi-aspect-ratio me-1"></i> Ebat Gir & Hesapla
                                </a>
                                <a href="category.php?slug=dekota-pleksi-kesim" class="btn btn-apple btn-apple-secondary px-4 py-3">
                                    Kesim Modelleri
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-6 text-center mt-4 mt-lg-0">
                            <div class="p-4 bg-white rounded-5 shadow-lg border d-inline-block">
                                <i class="bi bi-layers text-primary" style="font-size: 100px;"></i>
                                <h4 class="fw-bold mt-2 mb-1">3mm / 5mm Dekota & Pleksi</h4>
                                <span class="badge bg-danger rounded-pill px-3 py-2">UV Baskı + Özel Şekilli Kesim</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 3: BROŞÜR & EL İLANI -->
            <div class="carousel-item">
                <div class="container">
                    <div class="row align-items-center py-5">
                        <div class="col-lg-6">
                            <span class="hero-pill-badge">
                                <i class="bi bi-lightning-charge-fill text-primary"></i> Yüksek Tiraj & Ofset
                            </span>
                            <h1 class="slide-headline">
                                Kampanyanızı Duyurun,<br>
                                <span class="slide-grad-brosur">Toptan Fiyatla Kazanın.</span>
                            </h1>
                            <p class="slide-description">
                                A4, A5, Kırımlı ve Z-Katlamalı broşürlerde Heidelberg ofset baskı kalitesi. Canlı renkler, çift yön baskı ve 24 saatte hızlı teslimat.
                            </p>
                            <div class="slide-price-pill">
                                <i class="bi bi-tag-fill text-danger"></i> 1.000 Adet A5 Broşür: <strong>850 ₺</strong>
                            </div>
                            <div class="d-flex gap-3 flex-wrap">
                                <a href="product.php?slug=a5-tanitim-brosuru-135gr" class="btn btn-apple btn-apple-orange px-4 py-3">
                                    <i class="bi bi-calculator me-1"></i> Broşür Fiyatı Gör
                                </a>
                                <a href="category.php?slug=el-ilani-brosur" class="btn btn-apple btn-apple-secondary px-4 py-3">
                                    Broşür Çeşitleri
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-6 text-center mt-4 mt-lg-0">
                            <div class="p-4 bg-white rounded-5 shadow-lg border d-inline-block">
                                <i class="bi bi-file-earmark-richtext text-danger" style="font-size: 100px;"></i>
                                <h4 class="fw-bold mt-2 mb-1">A5 & A4 Ofset Broşür</h4>
                                <span class="badge bg-primary rounded-pill px-3 py-2">135gr & 170gr Parlak Kuşe</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Carousel Kontrolleri -->
        <button class="carousel-control-prev" type="button" data-bs-target="#tamBaskiHeroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon bg-dark rounded-circle p-3" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#tamBaskiHeroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon bg-dark rounded-circle p-3" aria-hidden="true"></span>
        </button>
    </div>
</section>

<!-- Avantajlar / Güven Faktörleri -->
<section class="py-4 bg-white border-bottom">
    <div class="container">
        <div class="row g-3 text-center">
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center justify-content-center gap-3">
                    <i class="bi bi-aspect-ratio text-primary fs-2"></i>
                    <div class="text-start">
                        <div class="fw-bold small">Hazır Paket & Özel Adet</div>
                        <div class="text-muted" style="font-size: 11px;">İstediğiniz Adeti Girin</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center justify-content-center gap-3">
                    <i class="bi bi-truck text-success fs-2"></i>
                    <div class="text-start">
                        <div class="fw-bold small">750 ₺ Üzeri Ücretsiz Kargo</div>
                        <div class="text-muted" style="font-size: 11px;">Anlaşmalı Hızlı Kurye</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center justify-content-center gap-3">
                    <i class="bi bi-shield-check text-primary fs-2"></i>
                    <div class="text-start">
                        <div class="fw-bold small">PayTR 3D Güvenli Ödeme</div>
                        <div class="text-muted" style="font-size: 11px;">Kredi Kartına 12 Taksit</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center justify-content-center gap-3">
                    <i class="bi bi-headset text-danger fs-2"></i>
                    <div class="text-start">
                        <div class="fw-bold small">Grafik & Baskı Kontrolü</div>
                        <div class="text-muted" style="font-size: 11px;">Ücretsiz Teknik Destek</div>
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
                <h3 class="fw-bold mb-1">Baskı & Üretim Kategorileri</h3>
                <p class="text-muted small mb-0">En çok tercih edilen ofset, dijital baskı ve lazer kesim ürünleri</p>
            </div>
            <a href="category.php" class="btn btn-sm btn-apple-secondary">Tümünü Gör <i class="bi bi-arrow-right ms-1"></i></a>
        </div>

        <div class="row g-3">
            <?php foreach ($all_categories as $cat): ?>
                <div class="col-lg-2 col-md-4 col-6">
                    <a href="category.php?slug=<?= urlencode($cat['slug']) ?>" class="text-decoration-none">
                        <div class="apple-card p-4 text-center h-100">
                            <div class="mb-3 text-primary">
                                <i class="bi <?= htmlspecialchars($cat['icon']) ?>" style="font-size: 32px;"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1" style="font-size: 14px;"><?= htmlspecialchars($cat['name']) ?></h6>
                            <span class="text-muted" style="font-size: 11px;">İncele & Hesapla →</span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Öne Çıkan Ürünler Vitrini -->
<section class="py-5 bg-white border-top border-bottom">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill mb-1">🔥 Çok Satanlar</span>
                <h3 class="fw-bold mb-1">Öne Çıkan Matbaa & Kesim Ürünleri</h3>
                <p class="text-muted small mb-0">Sabit hazır paket fiyatları veya anlık özel adet & m² hesaplayıcı</p>
            </div>
        </div>

        <div class="row g-4">
            <?php foreach ($featured_products as $p): ?>
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
                                        <span class="product-price-sub">/ m²'den başlayan fiyatlarla</span>
                                    <?php elseif (!empty($p['packages'])): ?>
                                        <span class="product-price"><?= format_price($p['packages'][0]['price']) ?></span>
                                        <span class="product-price-sub"><?= $p['packages'][0]['quantity'] ?> Adet Hazır Paket</span>
                                    <?php else: ?>
                                        <span class="product-price"><?= format_price($p['base_setup_fee']) ?></span>
                                        <span class="product-price-sub">'den başlayan</span>
                                    <?php endif; ?>
                                </div>
                                <a href="product.php?slug=<?= urlencode($p['slug']) ?>" class="btn btn-sm btn-apple btn-apple-orange">
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

<!-- B2B & E-Bayi Çağrısı -->
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
                <a href="dealer_apply.php" class="btn btn-lg btn-warning text-dark fw-bold px-4 py-3 rounded-pill shadow">
                    <i class="bi bi-award-fill me-2"></i>E-Bayi Başvurusu Yap
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
