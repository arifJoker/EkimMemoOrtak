<?php
/**
 * TAMBASKI.COM.TR - Ürün Detay & İnteraktif Vektörel Tasarımcı Sayfası
 * Referans: baski.arifuz.com.tr/product.php?slug=test
 */
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? 'ekonomik-kartvizit-250gr';
$product = get_product_by_slug($slug);

if (!$product) {
    $products = get_all_products();
    $product = $products[0];
}

$page_title = ($product['name'] ?? 'Kurumsal Prestij Kartvizit (TamBaskı)') . " – Online Matbaa & Fiyat Hesaplayıcı";
$meta_desc = $product['short_desc'] ?? '350gr Kuşe, Soft-Touch Kadife, 24K Altın Varak ve Kabartma Lak Seçenekleriyle Firmanızı Zirveye Taşıyın.';

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-2 py-md-3 pb-lg-4 pb-5 mb-4">
    
    <!-- Breadcrumb (Mobilde gizli) -->
    <nav aria-label="breadcrumb" class="mb-2 mb-md-3 d-none d-md-block">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Ana Sayfa</a></li>
            <li class="breadcrumb-item"><a href="category.php?slug=<?= urlencode($product['category_slug'] ?? 'kartvizit') ?>" class="text-decoration-none text-muted"><?= ucfirst(str_replace('-', ' ', $product['category_slug'] ?? 'kartvizit')) ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name'] ?? 'Kurumsal Prestij Kartvizit (TamBaskı)') ?></li>
        </ol>
    </nav>

    <div class="row g-3 g-lg-4">
        
        <!-- ========================================================================= -->
        <!-- SOL: ÜRÜN GÖRSELLERİ, CANLI DİNAMİK MOCKUP & TEKNİK GÜVENCELER             -->
        <!-- ========================================================================= -->
        <div class="col-lg-5">
            <div class="apple-card p-3 sticky-top" style="top: 85px; z-index: 10;">
                
                <div class="product-top-feature-chips mb-2 d-none d-lg-flex flex-wrap gap-1">
                    <span class="badge bg-info-subtle text-info fw-bold rounded-pill px-2 py-1" style="font-size: 11px;">
                        <i class="bi bi-droplet-fill me-1"></i> Su &amp; Nem Korumalı
                    </span>
                    <span class="badge bg-primary-subtle text-primary fw-bold rounded-pill px-2 py-1" style="font-size: 11px;">
                        <i class="bi bi-palette-fill"></i> Online Vektörel Tasarımlı
                    </span>
                </div>

                <!-- 🎨 CANLI DİNAMİK MOCKUP SAHNESİ & 3D DOKU SİMÜLATÖRÜ -->
                <div class="mockup-stage-box text-center py-3 bg-light rounded-4 mb-2 position-relative overflow-hidden shadow-sm" style="min-height: 260px; background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%); border: 1px solid #334155;">
                    
                    <!-- Üst Bar: Kalınlık & Doku Göstergesi + 360° Video Butonu -->
                    <div class="position-absolute top-0 start-0 w-100 p-2 d-flex justify-content-between align-items-start" style="z-index: 25; pointer-events: none;">
                        <div class="d-flex flex-column gap-1 text-start" style="pointer-events: auto;">
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 shadow-sm" id="showcaseThicknessBadge" style="font-size: 10px;">
                                <i class="bi bi-layers-half text-warning me-1"></i> Kalınlık: <strong>0.38 mm (350 GSM)</strong>
                            </span>
                            <span class="badge bg-white bg-opacity-10 text-light rounded-pill px-2 py-1 shadow-sm" id="showcaseFinishBadge" style="font-size: 10px;">
                                <i class="bi bi-stars text-info me-1"></i> Doku: <strong>Mat Selefon Kaplama</strong>
                            </span>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 py-1 shadow d-flex align-items-center gap-1" onclick="if(window.PackageShowcase && PackageShowcase.playCinematicVideo) PackageShowcase.playCinematicVideo(); else openVideoModal();" style="font-size: 11px; pointer-events: auto; background: linear-gradient(135deg, #e11d48, #f43f5e); border: none;">
                            <i class="bi bi-play-circle-fill fs-6"></i> <span>360° Video</span>
                        </button>
                    </div>

                    <!-- 1. İnteraktif 3D Kart Sahnesi (Mouse ile Eğim & Işık Parıltısı) -->
                    <div id="mockup_interactive_view" class="mockup-view-pane" style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; min-height: 250px; perspective: 1000px; padding: 20px 8px;">
                        <div id="interactivePackageCard" class="interactive-showcase-card shadow-2xl position-relative overflow-hidden cursor-pointer" style="width: 280px; height: 165px; background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%); border-radius: 12px; transition: transform 0.15s ease-out; display: flex; flex-direction: column; justify-content: space-between; padding: 18px; box-shadow: 0 20px 40px rgba(0,0,0,0.4);">
                            
                            <!-- Dinamik Işık Parıldama Katmanı -->
                            <div id="cardLightGleam" class="position-absolute top-0 start-0 w-100 h-100 pointer-events-none" style="z-index: 5; mix-blend-mode: screen; transition: background 0.08s ease;"></div>

                            <!-- Kart İçeriği -->
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div id="showcaseBrandTitle" class="d-flex align-items-center">
                                    <img src="assets/img/logo.svg" alt="TamBaskı" style="height: 22px; max-width: 140px; object-fit: contain; transition: filter 0.3s ease;" id="showcaseLogoImg">
                                </div>
                                <span class="badge bg-dark bg-opacity-75 text-white" style="font-size: 9px;">Heidelberg HD</span>
                            </div>
                            <div class="text-start">
                                <div class="fw-bold text-dark fs-6" style="line-height: 1.2;">Murat Sancak</div>
                                <div class="text-muted" style="font-size: 10.5px;">Yönetim Kurulu Başkanı</div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center text-muted border-top pt-2" style="font-size: 9.5px;">
                                <span><i class="bi bi-envelope"></i> info@tambaski.com.tr</span>
                                <span class="fw-bold text-primary">tambaski.com.tr</span>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Canlı Yüksek Çözünürlüklü Fotoğraf Görünümü -->
                    <div id="mockup_photo_view" class="mockup-view-pane" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10; background: #0f172a; align-items: center; justify-content: center; padding: 8px;">
                        <img id="stagePhotoImg" src="" alt="TamBaskı Kurumsal Çekim" class="rounded-3 shadow-lg" style="max-width: 100%; max-height: 245px; width: auto; height: auto; object-fit: contain; cursor: zoom-in;" onclick="openFullscreenImage(this.src)" title="Tam ekran büyütmek için tıklayın">
                        <button type="button" class="btn btn-sm btn-dark position-absolute bottom-0 end-0 m-2 rounded-pill px-2 py-1 text-white opacity-85 shadow d-flex align-items-center gap-1" style="font-size: 10px; background: rgba(0,0,0,0.65); border: 1px solid rgba(255,255,255,0.25); z-index: 20;" onclick="openFullscreenImage(document.getElementById('stagePhotoImg').src)">
                            <i class="bi bi-arrows-fullscreen"></i> Tam Ekran
                        </button>
                    </div>

                    <!-- 3. Video Oynatıcı Görünümü (Varsa) -->
                    <div id="mockup_video_view" class="mockup-view-pane" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10; background: #0f172a; align-items: center; justify-content: center; padding: 8px;">
                        <video id="stageVideoPlayer" controls autoplay loop playsinline class="rounded-3 shadow-lg" style="max-width: 100%; max-height: 245px; width: auto; height: auto; object-fit: contain;"></video>
                    </div>

                    <!-- Alt Bilgi: 3D Döndürme İpucu -->
                    <div class="position-absolute bottom-0 start-50 translate-middle-x mb-1 text-secondary small text-nowrap" style="font-size: 9.5px; opacity: 0.85; z-index: 20;">
                        <i class="bi bi-hand-index-thumb text-warning me-1"></i> Kartı farenizle eğerek ışık ve varak yansımasını inceleyin
                    </div>
                </div>

                <!-- 📸 Çoklu Fotoğraf & Video Küçük Resim Şeridi -->
                <div class="d-flex gap-1 overflow-x-auto pb-2 mb-2 align-items-center" id="prodMediaStrip" style="white-space: nowrap;">
                    <!-- 3D Mockup Butonu -->
                    <button type="button" class="btn btn-sm btn-outline-primary active rounded-3 p-1 px-2 text-nowrap media-thumb-btn" onclick="showMediaMockup(this)" title="3D Mockup Önizleme" style="font-size: 11px; height: 42px;">
                        <i class="bi bi-layers-half me-1"></i> Mockup
                    </button>

                    <button type="button" class="btn btn-sm btn-light border rounded-3 p-0 overflow-hidden media-thumb-btn" onclick="showMediaPhoto('https://baski.arifuz.com.tr/uploads/products/veo_vip_showcase.jpg', this)" style="width: 42px; height: 42px; flex-shrink: 0;">
                        <img src="https://baski.arifuz.com.tr/uploads/products/veo_vip_showcase.jpg" alt="Fotoğraf 1" style="width: 100%; height: 100%; object-fit: cover;">
                    </button>
                    <button type="button" class="btn btn-sm btn-light border rounded-3 p-0 overflow-hidden media-thumb-btn" onclick="showMediaPhoto('https://baski.arifuz.com.tr/uploads/products/veo_premium_showcase.jpg', this)" style="width: 42px; height: 42px; flex-shrink: 0;">
                        <img src="https://baski.arifuz.com.tr/uploads/products/veo_premium_showcase.jpg" alt="Fotoğraf 2" style="width: 100%; height: 100%; object-fit: cover;">
                    </button>
                    <button type="button" class="btn btn-sm btn-light border rounded-3 p-0 overflow-hidden media-thumb-btn" onclick="showMediaPhoto('https://baski.arifuz.com.tr/uploads/products/veo_standart_showcase.jpg', this)" style="width: 42px; height: 42px; flex-shrink: 0;">
                        <img src="https://baski.arifuz.com.tr/uploads/products/veo_standart_showcase.jpg" alt="Fotoğraf 3" style="width: 100%; height: 100%; object-fit: cover;">
                    </button>
                    <button type="button" class="btn btn-sm btn-light border rounded-3 p-0 overflow-hidden media-thumb-btn" onclick="showMediaPhoto('https://baski.arifuz.com.tr/uploads/products/veo_ekonomik_showcase.jpg', this)" style="width: 42px; height: 42px; flex-shrink: 0;">
                        <img src="https://baski.arifuz.com.tr/uploads/products/veo_ekonomik_showcase.jpg" alt="Fotoğraf 4" style="width: 100%; height: 100%; object-fit: cover;">
                    </button>
                        
                    <!-- 🎥 Gerçek 3D Tanıtım Videosu Butonu -->
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-3 p-1 px-2 text-nowrap media-thumb-btn d-flex align-items-center gap-1 shadow-xs" onclick="showMediaVideo('https://baski.arifuz.com.tr/uploads/videos/tambaski_vip_showcase.mp4', this)" title="Gerçekçi 3D Altın Varak & Kalınlık Videosu" style="font-size: 11px; height: 42px; background: #fff;">
                        <i class="bi bi-play-circle-fill text-danger fs-6"></i> <span class="fw-bold">Video</span>
                    </button>
                </div>
                
                <h5 class="fw-bold mb-1" id="dynamicProdTitle"><?= htmlspecialchars($product['name'] ?? 'Kurumsal Prestij Kartvizit (TamBaskı)') ?></h5>
                <p class="text-muted small mb-2 d-none d-lg-block" id="dynamicProdDesc"><?= htmlspecialchars($product['short_desc'] ?? '350gr Kuşe, Soft-Touch Kadife, 24K Altın Varak ve Kabartma Lak Seçenekleriyle Firmanızı Zirveye Taşıyın.') ?></p>

                <!-- 🌟 TEKNİK GÜVENLİK & KALİTE ROZETLERİ (4'LÜ KUTULAR) -->
                <div class="d-none d-lg-block">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-2 bg-light rounded-3 border d-flex align-items-center gap-2">
                                <span class="fs-4 text-info"><i class="bi bi-droplet-half"></i></span>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 11px;">Su &amp; Nem Geçirmez</div>
                                    <div class="text-muted" style="font-size: 10px;">Selefonlu koruma</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light rounded-3 border d-flex align-items-center gap-2">
                                <span class="fs-4 text-primary"><i class="bi bi-shield-check"></i></span>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 11px;">Bükülmez 350gr</div>
                                    <div class="text-muted" style="font-size: 10px;">Tok rijit karton</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light rounded-3 border d-flex align-items-center gap-2">
                                <span class="fs-4 text-danger"><i class="bi bi-bullseye"></i></span>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 11px;">300 DPI Ultra HD</div>
                                    <div class="text-muted" style="font-size: 10px;">Heidelberg ofset</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light rounded-3 border d-flex align-items-center gap-2">
                                <span class="fs-4 text-success"><i class="bi bi-lightning-charge-fill"></i></span>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 11px;">24 Saatte Hızlı</div>
                                    <div class="text-muted" style="font-size: 10px;">Ekspres üretim</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Canlı Seçilen Paketin Detaylı Teknik Tablosu -->
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="fw-bold text-dark mb-2 pb-1 border-bottom d-flex justify-content-between align-items-center">
                            <span>Teknik Özellikler</span>
                            <span id="tablePkgName" class="badge bg-primary text-white">Ekonomik Paket</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom small">
                            <span class="text-muted">Kağıt Cinsi:</span>
                            <strong class="text-dark" id="specPaper">350 gr. Birinci Sınıf Kuşe</strong>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom small">
                            <span class="text-muted">Yüzey Kaplama:</span>
                            <strong class="text-dark" id="specLamination">Mat Selefon (Su İtici)</strong>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom small">
                            <span class="text-muted">Köşe Kesimi:</span>
                            <strong class="text-dark" id="specCorners">Standart Düz Kesim (90°)</strong>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom small">
                            <span class="text-muted">Baskı / Efekt:</span>
                            <strong class="text-dark" id="specFinish">Standart Ofset Baskı (300 DPI)</strong>
                        </div>
                        <div class="d-flex justify-content-between pt-1 small">
                            <span class="text-muted">Sıvı / Nem Dayanımı:</span>
                            <strong class="text-success" id="specWaterproof"><i class="bi bi-check-circle-fill me-1"></i> %100 Su &amp; Nem Korumalı</strong>
                        </div>
                    </div>

                    <div id="dynamicFullDesc" class="small text-muted mb-2">
                        <ul class="ps-3 mb-0">
                            <li><strong>350 gr/m² Kalın Kuşe Karton:</strong> Eğilip bükülmeye karşı dayanıklı gövde.</li>
                            <li><strong>Su Geçirmez Mat Selefon:</strong> Yüzeyde koruyucu film tabakası oluşturarak sıvı temasında kabarma yapmaz.</li>
                            <li><strong>Keskin Düz Kesim:</strong> Milimetrik lazer kesim ile pürüzsüz kenarlar.</li>
                            <li><strong>Heidelberg Ofset Baskı:</strong> Canlı renkler ve net mikro yazılar.</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- SAĞ: 3 ADIMLI SİPARİŞ KONFİGÜRATÖRÜ & CANLI HESAPLAMA                    -->
        <!-- ========================================================================= -->
        <div class="col-lg-7">
            <div class="apple-card p-3 p-md-4">
                
                <div class="d-flex justify-content-between align-items-baseline mb-2">
                    <div>
                        <h5 class="fw-bold mb-0">1. Baskı Paketini Seçin</h5>
                        <span class="text-muted small d-none d-md-inline">İhtiyacınıza en uygun hazır paketi belirleyin</span>
                    </div>
                </div>

                <form id="printConfigForm" data-product-id="<?= (int)($product['id'] ?? 1) ?>" action="cart.php" method="POST">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?= (int)($product['id'] ?? 1) ?>">
                    
                    <!-- Tasarım Verisi Gizli Girdileri -->
                    <input type="hidden" name="design_type" id="designTypeInput" value="none">
                    <input type="hidden" name="design_file" id="selectedDesignFile" value="">
                    <input type="hidden" name="design_svg" id="selectedDesignSvg" value="">
                    <input type="hidden" name="design_back_svg" id="selectedDesignBackSvg" value="">

                    <!-- 📦 4'LÜ HAZIR PAKET KARTLARI -->
                    <div class="package-grid mb-2 mb-lg-4">
                        <div class="pkg-card active" id="card_pkg_ekonomik" onclick="selectPackage('ekonomik', this)">
                            <input type="radio" name="selected_package" value="ekonomik" checked style="display:none;">
                            <span class="pkg-icon text-secondary">
                                <i class="bi bi-wallet2"></i>
                            </span>
                            <div class="pkg-title">Ekonomik</div>
                            <div class="pkg-desc">250gr Bristol, Tek Yön Düz Baskı</div>
                            <span class="pkg-badge bg-success-subtle text-success">En Uygun Fiyat</span>
                        </div>
                        <div class="pkg-card" id="card_pkg_standart" onclick="selectPackage('standart', this)">
                            <span class="pkg-ribbon">Popüler</span>
                            <input type="radio" name="selected_package" value="standart" style="display:none;">
                            <span class="pkg-icon text-primary">
                                <i class="bi bi-award-fill"></i>
                            </span>
                            <div class="pkg-title">Standart</div>
                            <div class="pkg-desc">350gr Kuşe, Çift Taraf Mat Selefon</div>
                            <span class="pkg-badge bg-primary-subtle text-primary">En Çok Satan</span>
                        </div>
                        <div class="pkg-card" id="card_pkg_premium" onclick="selectPackage('premium', this)">
                            <input type="radio" name="selected_package" value="premium" style="display:none;">
                            <span class="pkg-icon" style="color: #8b5cf6;">
                                <i class="bi bi-stars"></i>
                            </span>
                            <div class="pkg-title">Premium</div>
                            <div class="pkg-desc">Soft-Touch Kadife Selefon &amp; Kabartma Lak</div>
                            <span class="pkg-badge" style="background: #8b5cf6; color: #fff;">Lüks Doku</span>
                        </div>
                        <div class="pkg-card" id="card_pkg_vip" onclick="selectPackage('vip', this)">
                            <span class="pkg-ribbon ribbon-vip">VIP</span>
                            <input type="radio" name="selected_package" value="vip" style="display:none;">
                            <span class="pkg-icon text-warning">
                                <i class="bi bi-gem"></i>
                            </span>
                            <div class="pkg-title">VIP Prestij</div>
                            <div class="pkg-desc">Tuale Fantezi / 24K Altın Varak Yaldız</div>
                            <span class="pkg-badge bg-warning-subtle text-dark">Maksimum Prestij</span>
                        </div>
                    </div>

                    <!-- Mobilde 1 Satırlık Dinamik Paket Özeti -->
                    <div id="mobilePkgDesc" class="d-lg-none text-center small text-primary bg-primary-subtle border border-primary-subtle rounded-pill py-1 px-3 mb-3 fw-semibold shadow-xs" style="font-size: 11px;">
                        💧 Mat Selefonlu &amp; Suya Dayanıklı
                    </div>

                    <!-- 2. ADET KADEMELERİ (TİRAJ SEÇİMİ & ÖZEL ADET) -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="option-group-title mb-0">
                                <span><i class="bi bi-layers me-1 text-primary"></i> 2. Baskı Adedi</span>
                            </label>
                            <span class="text-muted small">Tiraj arttıkça birim fiyat %50'ye varan oranda düşer</span>
                        </div>
                        
                        <div class="qty-grid mb-2">
                            <div class="qty-box active" id="qty_box_1000" onclick="selectQuantity('1000', this)">
                                <input type="radio" name="quantity" value="1000" checked style="display:none;">
                                <div class="fw-bold text-dark fs-6">1.000</div>
                                <div class="text-muted" style="font-size: 11px;">Adet</div>
                            </div>
                            <div class="qty-box" id="qty_box_2000" onclick="selectQuantity('2000', this)">
                                <span class="position-absolute top-0 end-0 translate-middle-y badge bg-success rounded-pill" style="font-size: 9px; right: 4px;">%15 İndirim</span>
                                <input type="radio" name="quantity" value="2000" style="display:none;">
                                <div class="fw-bold text-dark fs-6">2.000</div>
                                <div class="text-muted" style="font-size: 11px;">Adet</div>
                            </div>
                            <div class="qty-box" id="qty_box_3000" onclick="selectQuantity('3000', this)">
                                <span class="position-absolute top-0 end-0 translate-middle-y badge bg-success rounded-pill" style="font-size: 9px; right: 4px;">%22 İndirim</span>
                                <input type="radio" name="quantity" value="3000" style="display:none;">
                                <div class="fw-bold text-dark fs-6">3.000</div>
                                <div class="text-muted" style="font-size: 11px;">Adet</div>
                            </div>
                            <div class="qty-box" id="qty_box_5000" onclick="selectQuantity('5000', this)">
                                <span class="position-absolute top-0 end-0 translate-middle-y badge bg-success rounded-pill" style="font-size: 9px; right: 4px;">%30 İndirim</span>
                                <input type="radio" name="quantity" value="5000" style="display:none;">
                                <div class="fw-bold text-dark fs-6">5.000</div>
                                <div class="text-muted" style="font-size: 11px;">Adet</div>
                            </div>
                            <div class="qty-box" id="qty_box_10000" onclick="selectQuantity('10000', this)">
                                <span class="position-absolute top-0 end-0 translate-middle-y badge bg-success rounded-pill" style="font-size: 9px; right: 4px;">%38 İndirim</span>
                                <input type="radio" name="quantity" value="10000" style="display:none;">
                                <div class="fw-bold text-dark fs-6">10.000</div>
                                <div class="text-muted" style="font-size: 11px;">Adet</div>
                            </div>
                            
                            <!-- Özel Adet Kutusu -->
                            <div class="qty-box" id="qty_box_custom" onclick="activateCustomQty(this)" style="border-style: dashed;">
                                <span class="position-absolute top-0 end-0 translate-middle-y badge bg-info rounded-pill" style="font-size: 9px; right: 4px;">Özel</span>
                                <input type="radio" name="quantity" id="customQtyRadio" value="500" style="display:none;">
                                <div class="fw-bold text-primary fs-6"><i class="bi bi-pencil-square"></i></div>
                                <div class="text-muted" style="font-size: 11px;">Özel Adet</div>
                            </div>
                        </div>

                        <!-- Özel Adet Giriş Alanı -->
                        <div id="customQtyInputContainer" class="p-2 bg-light rounded-3 border mb-2" style="display: none;">
                            <div class="row g-2 align-items-center">
                                <div class="col-auto">
                                    <label class="form-label small fw-bold mb-0 text-dark">İstediğiniz Özel Adet:</label>
                                </div>
                                <div class="col">
                                    <div class="input-group input-group-sm">
                                        <input type="number" id="manualCustomQtyInput" class="form-control" min="10" step="10" value="500" placeholder="Örn: 250, 500, 1500, 7500">
                                        <span class="input-group-text">Adet</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Avantajlı Adet Tavsiyesi -->
                        <div id="liveUpsellCard" class="p-3 rounded-4 border mt-2 shadow-sm" style="display: none; background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-color: #f59e0b !important;">
                            <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
                                <div>
                                    <span class="badge bg-warning text-dark fw-bold mb-1"><i class="bi bi-stars me-1 text-danger"></i> AVANTAJLI ADET TAVSİYESİ</span>
                                    <div class="small text-dark" id="upsellMessageText"></div>
                                </div>
                                <button type="button" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3 shadow-sm text-nowrap" id="btnApplyUpsell">
                                    <i class="bi bi-fire text-danger me-1"></i> Fırsatı Uygula
                                </button>
                            </div>
                        </div>

                    </div>

                    <!-- ✨ ÖZEL EBAT & İNCE AYAR BUTONU -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 shadow-xs d-inline-flex align-items-center gap-2" data-bs-toggle="collapse" data-bs-target="#customDesignPanel" aria-expanded="false" style="font-size: 12px; font-weight: 600;">
                            <i class="bi bi-sliders"></i>
                            <span>İnce Ayar / Özel Ölçü &amp; Kağıt</span>
                            <i class="bi bi-chevron-down ms-1" style="font-size: 10px;"></i>
                        </button>
                        <span class="text-muted small d-none d-sm-inline" style="font-size: 11px;">Özel ebat &amp; kağıt seçenekleri</span>
                    </div>

                    <!-- Açılır İnce Ayar Formu -->
                    <div class="collapse mb-4 p-3 bg-light rounded-4 border" id="customDesignPanel">
                        <!-- Ölçü Seçimi -->
                        <div class="mb-3 p-3 bg-white rounded-3 border">
                            <label class="option-group-title small fw-bold text-dark mb-2 d-block"><i class="bi bi-aspect-ratio me-1 text-primary"></i> Ölçü / Ebat Seçimi</label>
                            <div class="d-flex gap-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="size_type" id="sizeStandard" value="standard" checked onchange="toggleCustomSizeInput(false)">
                                    <label class="form-check-label small fw-semibold cursor-pointer" for="sizeStandard">
                                        Standart Ebat (8.40 x 5.20 cm)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="size_type" id="sizeCustom" value="custom" onchange="toggleCustomSizeInput(true)">
                                    <label class="form-check-label small fw-semibold cursor-pointer text-primary" for="sizeCustom">
                                        <i class="bi bi-pencil-square me-1"></i> Özel Ölçü Gir
                                    </label>
                                </div>
                            </div>

                            <div id="customSizeInputRow" class="row g-2 mt-2 pt-2 border-top" style="display: none;">
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">En (cm)</span>
                                        <input type="number" step="0.1" id="customWidth" name="custom_width" class="form-control" value="8.40" min="1" max="100">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Boy (cm)</span>
                                        <input type="number" step="0.1" id="customHeight" name="custom_height" class="form-control" value="5.20" min="1" max="100">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kağıt Türü Seçenekleri -->
                        <div class="mb-3">
                            <label class="option-group-title small fw-semibold text-dark mb-1 d-block">Kağıt Türü &amp; Gramajı</label>
                            <div class="segmented-grid">
                                <div class="segmented-option">
                                    <input type="radio" name="options[1]" id="vopt_1" value="1" checked>
                                    <label for="vopt_1" class="segmented-label bg-white">
                                        <span class="opt-title">350 gr. Mat Kuşe (Standart)</span>
                                        <span class="opt-extra text-muted fw-bold">0 ₺</span>
                                    </label>
                                </div>
                                <div class="segmented-option">
                                    <input type="radio" name="options[1]" id="vopt_2" value="2">
                                    <label for="vopt_2" class="segmented-label bg-white">
                                        <span class="opt-title">350 gr. Parlak Kuşe</span>
                                        <span class="opt-extra text-muted fw-bold">0 ₺</span>
                                    </label>
                                </div>
                                <div class="segmented-option">
                                    <input type="radio" name="options[1]" id="vopt_3" value="3">
                                    <label for="vopt_3" class="segmented-label bg-white">
                                        <span class="opt-title">250 gr. Amerikan Bristol</span>
                                        <span class="opt-extra text-success fw-bold">-100 ₺</span>
                                    </label>
                                </div>
                                <div class="segmented-option">
                                    <input type="radio" name="options[1]" id="vopt_4" value="4">
                                    <label for="vopt_4" class="segmented-label bg-white">
                                        <span class="opt-title">300 gr. İtalyan Tuale (Fantezi Dokulu)</span>
                                        <span class="opt-extra text-primary fw-bold">+350 ₺</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. TASARIM TERCİHİ -->
                    <div class="design-choice-box" id="designSectionBox">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="fw-bold small mb-0 text-dark" id="designSectionTitle"><i class="bi bi-palette text-primary me-1"></i> 3. Tasarım Yöntemini Seçin</label>
                            <span class="badge bg-primary-subtle text-primary shadow-2xs" id="designSectionStatusBadge" style="font-size: 10px;">
                                <i class="bi bi-stars me-1"></i> İnteraktif Tasarım &amp; Şablonlar
                            </span>
                        </div>
                        
                        <!-- Panel 1: Başlangıç Seçim Paneli -->
                        <div id="designInitialSelector">
                            <div class="design-nav-pills" style="grid-template-columns: repeat(2, 1fr);">
                                <button type="button" class="design-nav-btn active btn-canva-tab" data-tab="tabCanva">
                                    <i class="bi bi-palette-fill text-danger" style="color: #e11d48;"></i>
                                    <span class="fw-bold">🎨 Kendin Tasarla &amp; Şablonlar</span>
                                </button>

                                <button type="button" class="design-nav-btn" data-tab="tabSupport">
                                    <i class="bi bi-whatsapp text-success"></i>
                                    <span>Grafik Desteği (WhatsApp)</span>
                                </button>
                            </div>

                            <!-- TAB 1: KENDİN TASARLA (TAMBASKI STÜDYO) -->
                            <div id="tabCanva" class="design-tab-pane">
                                <div class="p-3 rounded-4 border bg-white mb-2 shadow-2xs" style="border-color: #fecdd3 !important; background: linear-gradient(180deg, #fff1f2 0%, #ffffff 100%) !important;">
                                    <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom">
                                        <div class="rounded-3 p-2 text-white shadow-xs" style="background: linear-gradient(135deg, #e11d48, #f43f5e); width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-palette-fill fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small lh-1">TamBaskı Online Editör &amp; Hazır Şablonlar</div>
                                            <span class="text-muted" style="font-size: 10.5px;">Şablon seçin, logonuzu/görselinizi ekleyin veya sıfırdan tasarlayın (300 DPI)</span>
                                        </div>
                                    </div>
                                    <p class="small text-muted mb-3" style="font-size: 11.5px; line-height: 1.4;">
                                        Sektörünüze özel hazır şablonları anında düzenleyebilir, kendi logonuzu ve görsellerinizi editöre yükleyerek 3D canlı olarak inceleyebilirsiniz.
                                    </p>
                                    
                                    <button type="button" class="btn btn-danger w-100 py-3 fw-bold rounded-pill shadow-xs d-flex align-items-center justify-content-center gap-2" onclick="openCanvaStudio()" style="background: linear-gradient(135deg, #e11d48, #fb7185); border: none;">
                                        <i class="bi bi-palette-fill fs-5"></i>
                                        <span class="fs-6">🎨 Tasarım Editörünü Başlat &amp; Şablon Seç</span>
                                    </button>
                                </div>
                            </div>

                            <!-- TAB 2: GRAFİK DESTEĞİ -->
                            <div id="tabSupport" class="design-tab-pane" style="display: none;">
                                <div class="p-3 rounded-4 border bg-white mb-2 shadow-2xs" style="border-color: #bbf7d0 !important; background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%) !important;">
                                    <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom">
                                        <div class="rounded-3 p-2 text-white shadow-xs" style="background: linear-gradient(135deg, #16a34a, #22c55e); width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-whatsapp fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small lh-1">Grafik Tasarım &amp; WhatsApp Destek Hattı</div>
                                            <span class="text-muted" style="font-size: 10.5px;">Baskı öncesi profesyonel grafikerlerimizle birebir görüşün</span>
                                        </div>
                                    </div>
                                    <p class="small text-muted mb-3" style="font-size: 11.5px; line-height: 1.4;">
                                        Tasarımınız hazır değilse veya özel bir çalışma istiyorsanız; logonuzu, metinlerinizi ve isteklerinizi WhatsApp üzerinden grafiker ekibimize iletebilirsiniz.
                                    </p>

                                    <a href="https://wa.me/905550000000?text=Merhaba,%20kartvizit%20tasarim%20destegi%20istiyorum." target="_blank" class="btn btn-success w-100 py-2 fw-bold rounded-pill shadow-xs d-flex align-items-center justify-content-center gap-2 mb-3" style="background: #25D366; border: none;">
                                        <i class="bi bi-whatsapp fs-5"></i>
                                        <span>Grafikerimize WhatsApp'tan Yazın</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Panel 2: Tasarım Kaydedildiğinde Canlı Gösterilecek SVG Önizleme Alanı -->
                        <div id="designSavedContainer" style="display: none;">
                            <div class="p-3 rounded-4 border bg-white shadow-sm" style="border-color: #10b981 !important; background: linear-gradient(180deg, #ecfdf5 0%, #ffffff 100%) !important;">
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom flex-wrap gap-2">
                                    <span class="badge bg-success rounded-pill px-3 py-1 shadow-xs" style="font-size: 11px;">
                                        <i class="bi bi-check-circle-fill me-1"></i> <span id="savedDesignBadgeLabel">Tasarımınız Kaydedildi (300 DPI Vektör)</span>
                                    </span>
                                    <div class="btn-group btn-group-sm" id="savedDesignSideToggleGroup" style="display: none;">
                                        <button type="button" class="btn btn-sm btn-primary active" id="btnPreviewSideFront" onclick="showSavedDesignSide('front')">Ön Yüz</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnPreviewSideBack" onclick="showSavedDesignSide('back')">Arka Yüz</button>
                                    </div>
                                </div>

                                <div class="saved-preview-stage p-3 rounded-3 border d-flex align-items-center justify-content-center mb-3" style="background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%); min-height: 220px;">
                                    <div id="savedDesignPreviewCard" class="shadow-lg rounded-2 overflow-hidden border bg-white position-relative" style="width: 100%; max-width: 360px; aspect-ratio: 84 / 52; display: flex; align-items: center; justify-content: center;">
                                        <div id="savedDesignPreviewBox" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;"></div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2 border-top">
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 py-1 fw-bold shadow-xs d-flex align-items-center gap-1" onclick="openCanvaStudio()">
                                            <i class="bi bi-pencil-square"></i> Tasarımı Düzenle
                                        </button>
                                        <button type="button" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-1 fw-semibold d-flex align-items-center gap-1" onclick="if(window.CanvaStudio) CanvaStudio.open3dMockup();">
                                            <i class="bi bi-box"></i> 3D Önizle
                                        </button>
                                    </div>
                                    <button type="button" class="btn btn-link btn-sm text-muted text-decoration-none p-0" onclick="resetDesignSelection()" style="font-size: 11.5px;">
                                        <i class="bi bi-arrow-repeat me-1"></i> Farklı Yöntem Seç / Değiştir
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Fiyat Özeti & Sepete Ekle -->
                    <div class="p-3 bg-light rounded-4 border mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1 small text-muted">
                            <span>Ara Toplam (KDV Hariç):</span>
                            <span id="calcSubtotal" class="fw-bold text-dark">450,00 ₺</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1 small text-muted">
                            <span>KDV (%20):</span>
                            <span id="calcTax" class="fw-bold text-dark">90,00 ₺</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2 small text-muted">
                            <span id="calcUnitPrice">Birim: 0,45 ₺</span>
                            <span class="badge bg-success">750 ₺ Üzeri Ücretsiz Kargo</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <span class="fw-bold">Toplam Tutar:</span>
                            <span id="calcGrandTotal" class="price-display-lg text-primary">540,00 ₺</span>
                        </div>
                    </div>

                    <button type="submit" class="btn-apple btn-apple-pink w-100 py-3 fs-6 fw-bold shadow">
                        <i class="bi bi-bag-plus-fill me-2"></i> Sepete Ekle ve Siparişi Başlat
                    </button>

                </form>

            </div>
        </div>

    </div>
</div>

<!-- ========================================================================= -->
<!-- 📱 SABİT ALT MOBİL AKSİYON BARI (Tek Tıkla Siparişi Tamamla)               -->
<!-- ========================================================================= -->
<div class="mobile-sticky-bar d-lg-none fixed-bottom bg-white border-top shadow-lg py-2 px-3" style="z-index: 1040; backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.96) !important;">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <div class="text-muted fw-semibold text-truncate" style="font-size: 10px; max-width: 140px;" id="stickyPkgQty">Ekonomik • 1.000 Adet</div>
            <div class="fw-bolder text-primary" style="font-size: 18px; line-height: 1.1;" id="stickyGrandTotal">540,00 ₺</div>
            <div class="text-success" style="font-size: 9px;"><i class="bi bi-shield-check"></i> KDV Dahil</div>
        </div>
        <button type="submit" form="printConfigForm" class="btn btn-apple btn-apple-pink px-4 py-2 fw-bold text-nowrap shadow-sm d-flex align-items-center gap-2" style="font-size: 14px; border-radius: 999px;">
            <span>Sepete Ekle</span>
            <i class="bi bi-bag-check-fill fs-6"></i>
        </button>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 🎨 CANVA-STYLE INTERACTIVE VECTOR DESIGN STUDIO MODAL                    -->
<!-- ========================================================================= -->
<div class="modal fade" id="canvaStudioModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content border-0 rounded-0 bg-light">
            
            <!-- TamBaskı Üst Başlık & Kontrol Çubuğu -->
            <div class="modal-header bg-dark text-white py-1 px-2 px-md-3 border-0 d-flex justify-content-between align-items-center gap-1 gap-md-2">
                <div class="canva-header-left d-flex align-items-center gap-1 gap-md-2">
                    <button type="button" class="btn btn-sm btn-outline-light rounded-pill" id="canvaBtnBack" data-bs-dismiss="modal" title="Geri Dön">
                        <i class="bi bi-arrow-left"></i> <span class="d-none d-md-inline ms-1">Geri</span>
                    </button>
                    <div class="d-none d-sm-block">
                        <span class="fw-bold text-white small d-block text-truncate" style="max-width: 170px;">
                            <i class="bi bi-palette-fill text-danger me-1"></i> TamBaskı Editör
                        </span>
                        <span class="text-secondary d-none d-md-block" id="canvaHeaderDimInfo" style="font-size: 11px;">Baskı Ebatı: 8.4 x 5.2 cm • 300 DPI Matbaa Çıktısı</span>
                    </div>
                </div>

                <!-- Ön / Arka Yüz Seçim Butonları -->
                <div class="d-flex align-items-center gap-1" id="canvaSideSwitchGroup">
                    <button type="button" class="btn btn-sm btn-primary rounded-pill fw-bold" id="btnSideFront" onclick="CanvaStudio.switchSide('front')">
                        <i class="bi bi-file-earmark-fill me-1"></i> Ön Yüz
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-light rounded-pill fw-semibold position-relative" id="btnSideBack" onclick="CanvaStudio.switchSide('back')">
                        <i class="bi bi-file-earmark-break me-1"></i> Arka Yüz
                        <i class="bi bi-lock-fill text-warning ms-1" id="backSideLockIcon" style="display:none;" title="Tek Yön Seçili (Kilitli)"></i>
                    </button>
                </div>

                <!-- Canlı 3D Mockup, Araçlar & Kaydet -->
                <div class="canva-header-actions d-flex align-items-center gap-1 gap-md-2">
                    <button type="button" class="btn btn-sm text-white shadow-sm" id="canvaBtn3d" style="background: linear-gradient(135deg, #8b5cf6, #ec4899); border:none;" onclick="CanvaStudio.open3dMockup()" title="Canlı 3D Mockup Önizleme">
                        <i class="bi bi-box-seam"></i> <span class="d-none d-md-inline ms-1 fw-bold">3D Önizle</span>
                    </button>

                    <button type="button" class="btn btn-sm btn-outline-secondary text-white rounded-pill px-2 py-1 active d-none d-lg-inline-flex" id="btnToggleCanvaGuides" onclick="CanvaStudio.toggleGuides()" title="Güvenli Metin Alanı Kılavuzunu Göster/Gizle">
                        <i class="bi bi-shield-check me-1"></i> <span>Güvenli Alan</span>
                    </button>
                    
                    <div class="btn-group btn-group-sm d-none d-md-inline-flex">
                        <button type="button" class="btn btn-outline-secondary text-white" onclick="CanvaStudio.undo()" title="Geri Al (Ctrl+Z)"><i class="bi bi-arrow-counterclockwise"></i></button>
                        <button type="button" class="btn btn-outline-secondary text-white" onclick="CanvaStudio.redo()" title="İleri Al (Ctrl+Y)"><i class="bi bi-arrow-clockwise"></i></button>
                    </div>

                    <div class="d-none d-xl-flex align-items-center gap-1 ms-1">
                        <i class="bi bi-zoom-in text-secondary small"></i>
                        <input type="range" class="form-range" id="canvaZoomRange" min="0.4" max="1.2" step="0.05" value="1" style="width: 60px;">
                    </div>

                    <button type="button" class="btn btn-sm btn-success fw-bold shadow-sm" id="canvaBtnSave" onclick="CanvaStudio.saveAndApplyToOrder()">
                        <i class="bi bi-check2-circle me-1"></i> <span>Kaydet</span>
                    </button>
                </div>
            </div>

            <!-- ⚡ HIZLI BASKI AYARLARI & CANLI NET FİYAT BARI -->
            <div class="canva-studio-quick-bar d-none d-md-flex bg-white border-bottom py-2 px-3 align-items-center justify-content-between flex-wrap gap-2 shadow-2xs position-relative" style="z-index: 25;">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <!-- 1. Baskı Paketi Seçimi -->
                    <div class="d-flex align-items-center gap-1">
                        <span class="small fw-bold text-muted" style="font-size: 11px;"><i class="bi bi-box-seam text-primary me-1"></i> Paket:</span>
                        <div class="btn-group btn-group-sm" id="canvaStudioPkgGroup">
                            <button type="button" class="btn btn-outline-primary btn-sm canva-pkg-btn active" id="canva_btn_pkg_ekonomik" onclick="CanvaStudio.setPackage('ekonomik')" style="font-size: 11px; padding: 3px 8px;">
                                <i class="bi bi-wallet2 me-1"></i> Ekonomik
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm canva-pkg-btn" id="canva_btn_pkg_standart" onclick="CanvaStudio.setPackage('standart')" style="font-size: 11px; padding: 3px 8px;">
                                <i class="bi bi-award-fill me-1"></i> Standart
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm canva-pkg-btn" id="canva_btn_pkg_premium" onclick="CanvaStudio.setPackage('premium')" style="font-size: 11px; padding: 3px 8px;">
                                <i class="bi bi-stars me-1"></i> Premium
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm canva-pkg-btn" id="canva_btn_pkg_vip" onclick="CanvaStudio.setPackage('vip')" style="font-size: 11px; padding: 3px 8px;">
                                <i class="bi bi-gem me-1"></i> VIP Prestij
                            </button>
                        </div>
                    </div>

                    <div class="vr mx-1 text-secondary opacity-25" style="height: 22px;"></div>

                    <!-- 2. Baskı Adedi Seçimi -->
                    <div class="d-flex align-items-center gap-1">
                        <span class="small fw-bold text-muted" style="font-size: 11px;"><i class="bi bi-layers text-primary me-1"></i> Adet:</span>
                        <select class="form-select form-select-sm fw-bold border-primary-subtle shadow-2xs" id="canvaStudioQtySelect" onchange="CanvaStudio.setQuantity(this.value)" style="width: 125px; font-size: 11px; padding: 3px 6px;">
                            <option value="1000">1.000 Adet</option>
                            <option value="2000">2.000 Adet</option>
                            <option value="3000">3.000 Adet</option>
                            <option value="5000">5.000 Adet</option>
                            <option value="10000">10.000 Adet</option>
                            <option value="custom">✏️ Özel Adet...</option>
                        </select>
                        <div id="canvaCustomQtyInputWrap" style="display: none;">
                            <input type="number" id="canvaStudioCustomQtyInput" class="form-control form-control-sm text-center fw-bold border-primary" style="width: 75px; font-size: 11px; padding: 3px 4px;" min="10" step="10" value="500" placeholder="Adet" oninput="CanvaStudio.setCustomQuantity(this.value)">
                        </div>
                    </div>

                    <div class="vr mx-1 text-secondary opacity-25" style="height: 22px;"></div>

                    <!-- 3. İnce Ayar Butonu -->
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 d-flex align-items-center gap-1 shadow-2xs" id="canvaBtnFineTuneToggle" onclick="CanvaStudio.toggleFineTunePanel()">
                        <i class="bi bi-sliders text-primary"></i>
                        <span class="fw-semibold" style="font-size: 11px;">İnce Ayar (Ölçü &amp; Kağıt)</span>
                        <i class="bi bi-chevron-down ms-1" id="canvaFineTuneChevron" style="font-size: 9px; transition: transform 0.2s ease;"></i>
                    </button>
                </div>

                <!-- Sağ: Canlı Net Fiyat -->
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <div class="text-end">
                        <div class="d-flex align-items-baseline gap-1 justify-content-end">
                            <span class="text-muted small fw-semibold" style="font-size: 11px;">Net Fiyat:</span>
                            <span class="fw-bolder text-primary fs-6" id="canvaStudioNetPrice">450,00 ₺</span>
                        </div>
                        <div class="d-flex align-items-center gap-1 justify-content-end" style="font-size: 10px;">
                            <span class="badge bg-secondary-subtle text-secondary py-0 px-1 fw-semibold" style="font-size: 9.5px;">KDV ve Kargo Hariç</span>
                            <span class="text-muted" id="canvaStudioUnitPriceText">Birim: 0,45 ₺</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobilde Ultra-Kompakt Baskı & Fiyat Şeridi -->
            <div class="d-md-none bg-white border-bottom px-3 py-2 d-flex align-items-center justify-content-between shadow-2xs cursor-pointer" style="z-index: 25;" onclick="CanvaStudio.toggleFineTunePanel()">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary text-white fw-bold px-2 py-1" id="mobilePkgBadge" style="font-size: 10.5px;">Standart</span>
                    <span class="text-dark fw-bold small" id="mobileQtyBadge" style="font-size: 11px;">1.000 Adet</span>
                    <span class="text-muted" style="font-size: 10px;"><i class="bi bi-sliders text-primary me-1"></i>Ayarla <i class="bi bi-chevron-down ms-1" style="font-size: 8px;"></i></span>
                </div>
                <div class="text-end">
                    <span class="fw-bolder text-primary" id="mobileNetPrice" style="font-size: 13px;">450,00 ₺</span>
                    <span class="text-muted small" style="font-size: 9px; display: block; line-height: 1;">+KDV/Kargo</span>
                </div>
            </div>

            <!-- Canva Ana Çalışma Alanı -->
            <div class="modal-body p-0 d-flex flex-column flex-md-row overflow-hidden" style="height: calc(100vh - 105px);">
                
                <!-- Sol Araç Paneli (Sidebar) -->
                <div class="canva-sidebar bg-white border-end d-flex flex-column" style="width: 100%; max-width: 320px; z-index: 10;">
                    
                    <!-- Sol Sekme Butonları -->
                    <ul class="nav nav-tabs nav-fill border-bottom small bg-light" id="canvaSidebarTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active py-2 px-1 fw-semibold" id="cTab-templates" data-bs-toggle="tab" data-bs-target="#cPane-templates" type="button"><i class="bi bi-grid-1x2 d-block fs-5 text-primary"></i>Şablonlar</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-2 px-1 fw-semibold" id="cTab-text" data-bs-toggle="tab" data-bs-target="#cPane-text" type="button"><i class="bi bi-type d-block fs-5 text-dark"></i>Metin</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-2 px-1 fw-semibold" id="cTab-image" data-bs-toggle="tab" data-bs-target="#cPane-image" type="button"><i class="bi bi-image d-block fs-5 text-success"></i>Logo/Resim</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-2 px-1 fw-semibold" id="cTab-icons" data-bs-toggle="tab" data-bs-target="#cPane-icons" type="button"><i class="bi bi-app-indicator d-block fs-5 text-warning"></i>İkon/QR</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-2 px-1 fw-semibold" id="cTab-shapes" data-bs-toggle="tab" data-bs-target="#cPane-shapes" type="button"><i class="bi bi-square d-block fs-5 text-info"></i>Şekiller</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-2 px-1 fw-semibold" id="cTab-bg" data-bs-toggle="tab" data-bs-target="#cPane-bg" type="button"><i class="bi bi-paint-bucket d-block fs-5 text-danger"></i>Renk</button>
                        </li>
                    </ul>

                    <!-- Sol Sekme İçerikleri -->
                    <div class="tab-content p-3 flex-grow-1 overflow-y-auto" style="max-height: calc(100vh - 120px);">
                        
                        <!-- Mobilde Çekmece Kapatma Barı -->
                        <div class="d-md-none d-flex justify-content-between align-items-center pb-2 mb-2 border-bottom">
                            <span class="small fw-bold text-dark"><i class="bi bi-sliders text-primary me-1"></i> Editör Paneli</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-0 fw-semibold" style="font-size: 11px;" onclick="CanvaStudio.closeMobileDrawer()">
                                <i class="bi bi-chevron-down me-1"></i> Tuvale Dön
                            </button>
                        </div>

                        <!-- 1. HAZIR ŞABLONLAR SEKMESİ -->
                        <div class="tab-pane fade show active" id="cPane-templates" role="tabpanel">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="fw-bold small text-dark mb-0"><i class="bi bi-grid-fill text-primary me-1"></i> Şablon Kütüphanesi</h6>
                                <span class="badge bg-primary text-white fw-bold" id="canvaTotalTemplatesBadge" style="font-size: 10px;">1.800+ Tasarım</span>
                            </div>
                            
                            <!-- Şablon Arama ve Sektör Seçim Listesi -->
                            <div class="mb-3">
                                <div class="input-group input-group-sm mb-2 shadow-2xs">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                                    <input type="text" id="canvaTemplateSearch" class="form-control border-start-0 ps-0" placeholder="Şablon veya meslek ara..." oninput="CanvaStudio.onSearchTemplates(this.value)">
                                    <button type="button" class="btn btn-outline-secondary border-start-0" onclick="document.getElementById('canvaTemplateSearch').value=''; CanvaStudio.onSearchTemplates('');" title="Temizle"><i class="bi bi-x"></i></button>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <label for="canvaIndustrySelect" class="form-label small fw-bold mb-0 text-muted text-nowrap" style="font-size: 11px;">
                                        <i class="bi bi-funnel-fill text-primary me-1"></i> Sektör:
                                    </label>
                                    <select id="canvaIndustrySelect" class="form-select form-select-sm fw-semibold shadow-2xs" onchange="CanvaStudio.filterTemplates(this.value)" style="font-size: 11.5px;">
                                        <option value="all">Tüm Sektörler (Tümü)</option>
                                        <option value="kurumsal">🏢 Kurumsal &amp; İş Dünyası</option>
                                        <option value="lüks">💎 VIP / Lüks &amp; Altın Yaldız</option>
                                        <option value="hukuk">⚖️ Hukuk &amp; Avukatlık</option>
                                        <option value="sağlık">🩺 Sağlık, Tıp &amp; Klinik</option>
                                        <option value="mimarlık">📐 Mimarlık &amp; İnşaat</option>
                                        <option value="emlak">🏡 Gayrimenkul &amp; Emlak</option>
                                        <option value="finans">📊 Mali Müşavir &amp; Finans</option>
                                        <option value="teknoloji">💻 Teknoloji &amp; Yazılım</option>
                                        <option value="güzellik">✨ Güzellik, Kuaför &amp; Spa</option>
                                        <option value="gıda">☕ Kafe, Restoran &amp; Gıda</option>
                                        <option value="otomotiv">🚗 Otomotiv &amp; Nakliyat</option>
                                        <option value="eğitim">🎓 Eğitim &amp; Akademi</option>
                                    </select>
                                </div>
                            </div>

                            <div id="canvaNoTemplatesAlert" class="alert alert-light text-center py-3 border rounded-3 mb-2" style="display: none;">
                                <i class="bi bi-search text-muted fs-4 d-block mb-1"></i>
                                <span class="small text-muted">Aramanıza uygun şablon bulunamadı.</span>
                            </div>

                            <div class="row g-2" id="canvaTemplatesList"></div>
                        </div>

                        <!-- 2. METİN SEKMESİ -->
                        <div class="tab-pane fade" id="cPane-text" role="tabpanel">
                            <h6 class="fw-bold small text-dark mb-2"><i class="bi bi-type text-dark me-1"></i> Metin Ekle</h6>
                            <p class="text-muted" style="font-size: 11px;">Kartvizitinize eklemek istediğiniz metin stilini seçin:</p>
                            
                            <button type="button" class="btn btn-light w-100 text-start p-2 mb-2 border rounded-3 fw-bold fs-6 shadow-xs" onclick="CanvaStudio.addHeading()">
                                <i class="bi bi-plus-circle-fill text-primary me-2"></i> Büyük Başlık Ekle
                            </button>
                            <button type="button" class="btn btn-light w-100 text-start p-2 mb-2 border rounded-3 fw-semibold small shadow-xs" onclick="CanvaStudio.addSubheading()">
                                <i class="bi bi-plus-circle text-primary me-2"></i> Alt Başlık / Unvan Ekle
                            </button>
                            <button type="button" class="btn btn-light w-100 text-start p-2 mb-3 border rounded-3 small text-muted shadow-xs" onclick="CanvaStudio.addBodyText()">
                                <i class="bi bi-plus text-primary me-2"></i> Gövde / İletişim Metni Ekle
                            </button>
                        </div>

                        <!-- 3. LOGO & RESİM YÜKLEME SEKMESİ -->
                        <div class="tab-pane fade" id="cPane-image" role="tabpanel">
                            <h6 class="fw-bold small text-dark mb-2"><i class="bi bi-image text-success me-1"></i> Logo / Görsel Ekle</h6>
                            <p class="text-muted" style="font-size: 11px;">Firma logonuzu veya fotoğrafınızı yükleyin, tuvalde istediğiniz yere sürükleyip boyutlandırın:</p>
                            
                            <div class="p-3 border rounded-3 bg-light text-center mb-2">
                                <i class="bi bi-cloud-arrow-up fs-2 text-primary mb-1 d-block"></i>
                                <label for="canvaLogoUploadInput" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold cursor-pointer">
                                    <i class="bi bi-folder2-open me-1"></i> Logo / Resim Seç
                                </label>
                                <input type="file" id="canvaLogoUploadInput" class="d-none" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                                <div class="text-muted mt-2" style="font-size: 10px;">Şeffaf PNG veya yüksek kaliteli JPG önerilir.</div>
                            </div>
                        </div>

                        <!-- 4. İKONLAR & QR KOD SEKMESİ -->
                        <div class="tab-pane fade" id="cPane-icons" role="tabpanel">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="fw-bold small text-dark mb-0"><i class="bi bi-app-indicator text-warning me-1"></i> Vektörel İkonlar</h6>
                                <span class="badge bg-warning-subtle text-dark" style="font-size: 10px;">40+ İkon</span>
                            </div>

                            <div class="input-group input-group-sm mb-2 shadow-2xs">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                                <input type="text" id="canvaIconSearchInput" class="form-control border-start-0 ps-0" placeholder="İkon ara (whatsapp, telefon, mail...)" oninput="CanvaStudio.filterIcons(this.value)">
                            </div>

                            <div class="canva-icon-category-group mb-2">
                                <div class="text-muted fw-bold mb-1" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">📞 İletişim &amp; Sosyal Medya</div>
                                <div class="row g-1">
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="whatsapp mesaj sohbet wa" onclick="CanvaStudio.addIcon('whatsapp')" title="WhatsApp"><i class="bi bi-whatsapp fs-5 text-success"></i><div style="font-size: 9px;">WhatsApp</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="telefon tel ara sabit" onclick="CanvaStudio.addIcon('phone')" title="Telefon"><i class="bi bi-telephone-fill fs-5 text-primary"></i><div style="font-size: 9px;">Telefon</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="cep mobil gsm akıllı telefon" onclick="CanvaStudio.addIcon('mobile')" title="Cep Telefonu"><i class="bi bi-phone fs-5 text-primary"></i><div style="font-size: 9px;">Mobil</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="mail e-posta email mektup" onclick="CanvaStudio.addIcon('mail')" title="E-Posta"><i class="bi bi-envelope-fill fs-5 text-danger"></i><div style="font-size: 9px;">E-Posta</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="konum adres harita yer pin" onclick="CanvaStudio.addIcon('location')" title="Konum"><i class="bi bi-geo-alt-fill fs-5 text-danger"></i><div style="font-size: 9px;">Konum</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="web site internet www globe" onclick="CanvaStudio.addIcon('globe')" title="Web Sitesi"><i class="bi bi-globe fs-5 text-info"></i><div style="font-size: 9px;">Web</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="instagram ig sosyal medya reels" onclick="CanvaStudio.addIcon('instagram')" title="Instagram"><i class="bi bi-instagram fs-5 text-danger"></i><div style="font-size: 9px;">Instagram</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="linkedin iş kariyer profesyonel" onclick="CanvaStudio.addIcon('linkedin')" title="LinkedIn"><i class="bi bi-linkedin fs-5 text-primary"></i><div style="font-size: 9px;">LinkedIn</div></button></div>
                                </div>
                            </div>

                            <hr class="my-2">

                            <h6 class="fw-bold small text-dark mb-1">QR Kod Oluştur</h6>
                            <p class="text-muted" style="font-size: 11px;">Web siteniz veya WhatsApp için dinamik QR kod oluşturun:</p>
                            <div class="input-group input-group-sm mb-2">
                                <input type="text" id="canvaQrInput" class="form-control" placeholder="https://tambaski.com.tr">
                                <button type="button" class="btn btn-primary fw-bold" onclick="CanvaStudio.addQrCode(document.getElementById('canvaQrInput').value)">Ekle</button>
                            </div>
                        </div>

                        <!-- 5. ŞEKİLLER SEKMESİ -->
                        <div class="tab-pane fade" id="cPane-shapes" role="tabpanel">
                            <h6 class="fw-bold small text-dark mb-2"><i class="bi bi-square text-info me-1"></i> Şekil Ekle</h6>
                            <div class="row g-2 mb-3">
                                <div class="col-4">
                                    <button type="button" class="btn btn-outline-secondary w-100 py-2 rounded-3 text-center" onclick="CanvaStudio.addShape('rect')">
                                        <i class="bi bi-square-fill fs-5 text-primary"></i>
                                        <div style="font-size: 10px;">Kutu</div>
                                    </button>
                                </div>
                                <div class="col-4">
                                    <button type="button" class="btn btn-outline-secondary w-100 py-2 rounded-3 text-center" onclick="CanvaStudio.addShape('circle')">
                                        <i class="bi bi-circle-fill fs-5 text-primary"></i>
                                        <div style="font-size: 10px;">Daire</div>
                                    </button>
                                </div>
                                <div class="col-4">
                                    <button type="button" class="btn btn-outline-secondary w-100 py-2 rounded-3 text-center" onclick="CanvaStudio.addShape('line')">
                                        <i class="bi bi-dash-lg fs-5 text-primary"></i>
                                        <div style="font-size: 10px;">Çizgi</div>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- 6. ARKA PLAN RENGİ SEKMESİ -->
                        <div class="tab-pane fade" id="cPane-bg" role="tabpanel">
                            <h6 class="fw-bold small text-dark mb-2"><i class="bi bi-paint-bucket text-danger me-1"></i> Tuval Arka Plan Rengi</h6>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <input type="color" id="canvaBgColorPicker" value="#ffffff" class="form-control form-control-color p-0" style="width: 40px; height: 35px; cursor: pointer;">
                                <span class="small text-muted" style="font-size: 11px;">Özel Renk Seç</span>
                            </div>
                            <div class="d-flex gap-2 flex-wrap mb-3">
                                <button type="button" class="btn btn-sm border rounded-circle p-0" style="width: 28px; height: 28px; background: #ffffff;" onclick="CanvaStudio.setBackgroundColor('#ffffff')" title="Beyaz"></button>
                                <button type="button" class="btn btn-sm border rounded-circle p-0" style="width: 28px; height: 28px; background: #0f172a;" onclick="CanvaStudio.setBackgroundColor('#0f172a')" title="Gece Mavisi"></button>
                                <button type="button" class="btn btn-sm border rounded-circle p-0" style="width: 28px; height: 28px; background: #1e293b;" onclick="CanvaStudio.setBackgroundColor('#1e293b')" title="Antrasit"></button>
                                <button type="button" class="btn btn-sm border rounded-circle p-0" style="width: 28px; height: 28px; background: #0071e3;" onclick="CanvaStudio.setBackgroundColor('#0071e3')" title="Kurumsal Mavi"></button>
                                <button type="button" class="btn btn-sm border rounded-circle p-0" style="width: 28px; height: 28px; background: #10b981;" onclick="CanvaStudio.setBackgroundColor('#10b981')" title="Zümrüt"></button>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Sağ Tuval & Özellik Çubuğu -->
                <div class="canva-workspace flex-grow-1 d-flex flex-column bg-secondary bg-opacity-10 position-relative">
                    
                    <!-- Dinamik Özellik Çubuğu (Seçili Nesneye Göre Açılır) -->
                    <div id="canvaPropertiesBar" class="bg-white border-bottom p-2 px-3 align-items-center justify-content-between flex-wrap gap-2 shadow-xs" style="display: none; z-index: 15;">
                        <div id="canvaTextControls" class="d-flex align-items-center gap-2 flex-wrap">
                            <select id="canvaFontFamily" class="form-select form-select-sm" style="width: 145px; font-size: 12px;">
                                <option value="Inter">Inter (Modern)</option>
                                <option value="Montserrat">Montserrat (Kalın)</option>
                                <option value="Poppins">Poppins (Yuvarlak)</option>
                                <option value="Playfair Display">Playfair (Lüks Serif)</option>
                                <option value="Roboto">Roboto (Temiz)</option>
                                <option value="Arial">Arial (Klasik)</option>
                            </select>

                            <div class="input-group input-group-sm" style="width: 105px;">
                                <button type="button" class="btn btn-outline-secondary px-2" onclick="CanvaStudio.changeFontSize(-2)">-</button>
                                <input type="number" id="canvaFontSize" class="form-control text-center px-1" value="16" min="6" max="150">
                                <button type="button" class="btn btn-outline-secondary px-2" onclick="CanvaStudio.changeFontSize(+2)">+</button>
                            </div>

                            <div class="d-flex align-items-center gap-1">
                                <input type="color" id="canvaTextColorPicker" value="#000000" class="form-control form-control-color p-0 border" style="width: 30px; height: 28px; cursor: pointer;">
                            </div>

                            <div class="btn-group btn-group-sm">
                                <button type="button" id="canvaBtnBold" class="btn btn-outline-secondary" onclick="CanvaStudio.toggleBold()"><i class="bi bi-type-bold"></i></button>
                                <button type="button" id="canvaBtnItalic" class="btn btn-outline-secondary" onclick="CanvaStudio.toggleItalic()"><i class="bi bi-type-italic"></i></button>
                                <button type="button" id="canvaBtnUnderline" class="btn btn-outline-secondary" onclick="CanvaStudio.toggleUnderline()"><i class="bi bi-type-underline"></i></button>
                            </div>

                            <div class="btn-group btn-group-sm">
                                <button type="button" id="canvaBtnAlignLeft" class="btn btn-outline-secondary" onclick="CanvaStudio.setTextAlign('left')"><i class="bi bi-text-left"></i></button>
                                <button type="button" id="canvaBtnAlignCenter" class="btn btn-outline-secondary" onclick="CanvaStudio.setTextAlign('center')"><i class="bi bi-text-center"></i></button>
                                <button type="button" id="canvaBtnAlignRight" class="btn btn-outline-secondary" onclick="CanvaStudio.setTextAlign('right')"><i class="bi bi-text-right"></i></button>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-1 ms-auto">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="CanvaStudio.alignObject('center-h')" title="Yatay Ortala"><i class="bi bi-align-center"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="CanvaStudio.alignObject('center-v')" title="Dikey Ortala"><i class="bi bi-align-middle"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="CanvaStudio.bringForward()" title="Öne Getir"><i class="bi bi-front"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="CanvaStudio.sendBackward()" title="Arkaya Gönder"><i class="bi bi-back"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="CanvaStudio.duplicateSelected()" title="Çoğalt"><i class="bi bi-copy"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="CanvaStudio.deleteSelected()" title="Sil"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>

                    <!-- Canlı Kesim / Güvenli Alan Uyarı Rozeti -->
                    <div id="canvaBleedAlert" class="alert alert-warning py-1 px-3 mb-0 shadow-sm align-items-center justify-content-between border-0 rounded-0" style="display: none; z-index: 20; background: #fffbeb; color: #b45309; border-bottom: 1px solid #fde68a !important; font-size: 12px;">
                        <span id="canvaBleedAlertText"><strong>Dikkat:</strong> Yazı / nesne kesim sınırının dışına taştı, baskıda kesilebilir!</span>
                        <button type="button" class="btn-close btn-close-sm" style="font-size: 9px;" onclick="document.getElementById('canvaBleedAlert').style.display='none'"></button>
                    </div>

                    <!-- Tuval Konteyneri (Cetvelli) -->
                    <div class="flex-grow-1 d-flex flex-column align-items-center justify-content-center p-3 overflow-auto position-relative" id="canvaCanvasWrapper">
                        <div class="badge bg-dark bg-opacity-75 text-white fw-normal px-3 py-1 mb-2 rounded-pill shadow-xs" style="font-size: 11px; letter-spacing: 0.5px;">
                            <i class="bi bi-arrows-left-right text-info me-1"></i> Genişlik: <strong id="rulerWidthValue" class="text-warning">8.4 cm (84 mm)</strong>
                        </div>

                        <div class="d-flex align-items-center justify-content-center position-relative" id="canvaViewportContainer">
                            <div class="position-absolute end-100 me-2 text-nowrap badge bg-dark bg-opacity-75 text-white fw-normal px-2 py-1 rounded-pill shadow-xs d-none d-md-inline-block" style="font-size: 11px; transform: rotate(-90deg); transform-origin: right center;">
                                <i class="bi bi-arrows-up-down text-info me-1"></i> Yükseklik: <strong id="rulerHeightValue" class="text-warning">5.2 cm (52 mm)</strong>
                            </div>

                            <div id="canvaCanvasHolder" class="shadow-lg rounded-3 overflow-hidden bg-white" style="line-height: 0; position: relative;">
                                <canvas id="canvaMainCanvas" width="850" height="526"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Alt Bilgilendirme Rozeti -->
                    <div class="p-2 bg-dark text-white border-top small d-none d-md-flex justify-content-between align-items-center px-4 flex-wrap gap-2" style="font-size: 11px;">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge bg-primary-subtle text-info fw-bold px-2 py-1"><i class="bi bi-shield-check me-1"></i> Mavi Kesikli Çizgi:</span> 
                            <span class="text-white-50">Güvenli Metin Alanı</span>
                        </div>
                        <div class="fw-semibold text-white">
                            <i class="bi bi-check-circle-fill text-success me-1"></i> Ekranda Gördüğünüz Tasarım <strong class="text-warning">%100 Net Boyutta</strong> Basılacaktır (<span id="rulerTotalDim" class="text-white fw-bold">8.4 x 5.2 cm</span>)
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 🌟 3D CANLI BASKI & MOCKUP ÖNİZLEME MODAL                                 -->
<!-- ========================================================================= -->
<div class="modal fade" id="canva3dMockupModal" tabindex="-1" aria-hidden="true" style="backdrop-filter: blur(8px); background: rgba(15, 23, 42, 0.75);">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-2xl rounded-4 overflow-hidden" style="background: #0f172a; color: #fff;">
            <div class="modal-header border-secondary border-opacity-25 px-4 py-3 bg-slate-900 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary p-2 rounded-circle"><i class="bi bi-box-seam fs-6"></i></span>
                    <div>
                        <h6 class="modal-title fw-bold text-white mb-0">Canlı 3D Baskı Mockup Önizleme</h6>
                        <small class="text-secondary" style="font-size: 11px;">Tasarımınızı gerçekçi 3D alanda inceleyin ve arkalı önlü kontrol edin</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>

            <div class="modal-body p-4 d-flex flex-column align-items-center justify-content-center" style="min-height: 480px; background: radial-gradient(circle at center, #1e293b 0%, #0b0f19 100%);">
                <div class="mockup-3d-scene" style="perspective: 1200px; width: 100%; display: flex; justify-content: center; align-items: center; padding: 25px 0;">
                    <div id="mockup3dCardInner" class="mockup-3d-card" style="width: 480px; height: 280px; position: relative; transform-style: preserve-3d; transition: transform 0.8s cubic-bezier(0.34, 1.56, 0.64, 1); cursor: pointer; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.6);" onclick="CanvaStudio.toggle3dFlip()" title="Çevirmek için tıklayın">
                        <div id="mockup3dFrontFace" class="mockup-face position-absolute top-0 start-0 w-100 h-100 rounded-3 overflow-hidden shadow-lg bg-white" style="backface-visibility: hidden; transform: rotateY(0deg);"></div>
                        <div id="mockup3dBackFace" class="mockup-face position-absolute top-0 start-0 w-100 h-100 rounded-3 overflow-hidden shadow-lg bg-white" style="backface-visibility: hidden; transform: rotateY(180deg);"></div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3 mt-3">
                    <button type="button" class="btn btn-outline-light rounded-pill px-4 btn-sm fw-bold shadow-sm" id="btnFlip3dMockup" onclick="CanvaStudio.toggle3dFlip()">
                        <i class="bi bi-arrow-repeat me-1"></i> <span id="btn3dFlipLabel">Arka Yüzü Göster</span>
                    </button>
                    <span class="text-secondary small" style="font-size: 12px;"><i class="bi bi-hand-index-thumb me-1"></i> Çevirmek için kartın üzerine de tıklayabilirsiniz.</span>
                </div>
            </div>

            <div class="modal-footer border-secondary border-opacity-25 px-4 py-3 bg-slate-900 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary text-white rounded-pill px-4 btn-sm" data-bs-dismiss="modal">
                    <i class="bi bi-pencil me-1"></i> Düzenlemeye Dön
                </button>
                <button type="button" class="btn btn-success rounded-pill px-4 btn-sm fw-bold" onclick="CanvaStudio.saveFromMockup()">
                    <i class="bi bi-check-circle me-1"></i> Tasarımı Onayla &amp; Siparişe Ekle
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 🎥 360° SİNEMATİK PAKET TANITIM VİDEO MODALI                              -->
<!-- ========================================================================= -->
<div class="modal fade" id="packageVideoModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-2xl rounded-4 overflow-hidden text-white" style="background: #0f172a;">
            <div class="modal-header border-secondary border-opacity-25 px-4 py-3 d-flex justify-content-between align-items-center" style="background: #0b0f19;">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-danger-subtle text-danger p-2 rounded-circle"><i class="bi bi-play-circle-fill fs-6"></i></span>
                    <h6 class="modal-title fw-bold text-white mb-0">TamBaskı 3D Sinematik Tanıtım Videosu</h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat" onclick="if(window.stopPackageModalVideo) window.stopPackageModalVideo();"></button>
            </div>
            <div class="modal-body p-0 d-flex flex-column align-items-center justify-content-center position-relative" style="background: #060910; min-height: 480px;">
                <video id="packageModalVideoPlayer" controls autoplay loop playsinline class="w-100 rounded-0" style="max-height: 75vh; object-fit: contain; background: #000;"></video>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 🔍 ULTRA HD TAM EKRAN GÖRSEL MODALI                                      -->
<!-- ========================================================================= -->
<div class="modal fade" id="imageFullscreenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 95vw;">
        <div class="modal-content border-0 shadow-2xl rounded-4 overflow-hidden text-white" style="background: #090d16;">
            <div class="modal-header border-secondary border-opacity-25 px-4 py-3 d-flex justify-content-between align-items-center" style="background: #060910;">
                <h6 class="modal-title fw-bold text-white mb-0">TamBaskı Ürün &amp; Doku İncelemesi</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body p-2 d-flex align-items-center justify-content-center position-relative" style="background: radial-gradient(circle at center, #1e293b 0%, #060910 100%); min-height: 72vh;">
                <img id="fullscreenModalImg" src="" alt="Tam Ekran" class="img-fluid rounded-3 shadow-2xl" style="max-height: 75vh; width: auto; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- JAVASCRIPT MOTORLARI & FABRIC.JS KÜTÜPHANELERİ                            -->
<!-- ========================================================================= -->
<script src="assets/js/fabric.min.js?v=<?= time() ?>"></script>
<script src="assets/js/calculator.js?v=<?= time() ?>"></script>
<script src="assets/js/editor.js?v=<?= time() ?>"></script>
<script src="assets/js/canva_templates_engine.js?v=<?= time() ?>"></script>
<script src="assets/js/canva_studio.js?v=<?= time() ?>"></script>
<script src="assets/js/package_showcase.js?v=<?= time() ?>"></script>
<script src="assets/js/cinematic_player.js?v=<?= time() ?>"></script>

<script>
const packageData = {
    ekonomik: {
        name: 'Ekonomik Paket',
        tag: '💧 Mat Selefonlu & Suya Dayanıklı',
        specPaper: '350 gr. Birinci Sınıf Kuşe',
        specLamination: 'Mat Selefon (Su İtici & Neme Dayanıklı)',
        specCorners: 'Standart Düz Kesim (90°)',
        specFinish: 'Standart Ofset Baskı (300 DPI)',
        specWaterproof: '<i class="bi bi-check-circle-fill me-1"></i> %100 Su & Nem Korumalı',
        title: 'Ekonomik Mat Kuşe Kartvizit',
        desc: '350 gr. Kuşe kağıt üzerine uygulanan mat selefon kaplaması sayesinde su damlacıklarına ve neme karşı dayanıklıdır.',
        fullDesc: '<ul class="ps-3 mb-0"><li><strong>350 gr/m² Kalın Kuşe Karton:</strong> Eğilip bükülmeye karşı dayanıklı gövde.</li><li><strong>Su Geçirmez Mat Selefon:</strong> Yüzeyde koruyucu film tabakası oluşturur.</li><li><strong>Heidelberg Ofset Baskı:</strong> Canlı renkler ve net mikro yazılar.</li></ul>'
    },
    standart: {
        name: 'Standart Paket (En Çok Satan)',
        tag: '⭐ Çift Yön Mat & Oval Yıpranmaz Köşe',
        specPaper: '350 gr. Ekstra Kaliteli Kuşe',
        specLamination: 'Çift Yön Mat Selefon (Çift Kat Koruma)',
        specCorners: 'Oval Radyus Köşe Kesim (Yıpranmaz)',
        specFinish: 'Çift Yön Renkli Ofset Baskı',
        specWaterproof: '<i class="bi bi-shield-check me-1"></i> %100 Çift Taraflı Su & Nem Bariyeri',
        title: 'Standart Oval Köşe Çift Yön Kartvizit',
        desc: 'En çok tercih edilen kurumsal model. Çift taraflı koruyucu selefonu ve yuvarlatılmış oval köşeleri sayesinde deforme olmaz.',
        fullDesc: '<ul class="ps-3 mb-0"><li><strong>Çift Yön Mat Selefon:</strong> Hem ön hem arka yüz tam korumalıdır.</li><li><strong>Oval Radyus Köşeler:</strong> Sivri köşeleri olmadığı için bükülmez.</li><li><strong>350 gr Tok Rijit Gövde:</strong> Yüksek mukavemetli kartvizit kalitesi.</li></ul>'
    },
    premium: {
        name: 'Premium Paket (Lüks Doku)',
        tag: '💎 Kadife Dokunuş & 3D Parlak Kabartma Lak',
        specPaper: '350 - 400 gr. Ağır Gramajlı Kuşe',
        specLamination: 'Soft-Touch Kadife Selefon (İpeksi Doku)',
        specCorners: 'Hassas Kesim / Oval Radyus',
        specFinish: '3D Parlak Kabartma Lak (Spot UV)',
        specWaterproof: '<i class="bi bi-droplet-half me-1"></i> İpeksi Kadife & Su İtici Doku',
        title: 'Premium Kadife & Kabartma Laklı Kartvizit',
        desc: 'Dokunduğunuzda hissedilen kadife yumuşaklığındaki Soft-Touch yüzeyi ve logonuzu öne çıkaran 3D parlak kabartma lakı ile prestij kazanın.',
        fullDesc: '<ul class="ps-3 mb-0"><li><strong>Soft-Touch Kadife Selefon:</strong> İpeksi ve kadifemsi lüks dokunuş hissi.</li><li><strong>3D Kabartma Lak (Spot UV):</strong> Logo ve unvan kabartılarak vurgulanır.</li><li><strong>VIP Kurumsal İmaj:</strong> Yönetici ve üst düzey temsiller için idealdir.</li></ul>'
    },
    vip: {
        name: 'VIP Prestij Paket (Altın Varak)',
        tag: '👑 24K Altın Varak Yaldız & İtalyan Tuale Kağıt',
        specPaper: '350 gr. İtalyan Tuale Fantezi Doku',
        specLamination: 'Doğal Özel Dokulu (Tuale Çizgili)',
        specCorners: 'Özel Prestij Kesim',
        specFinish: '24K Altın / Gümüş Varak Yaldız Baskı',
        specWaterproof: '<i class="bi bi-gem me-1"></i> Fantezi Koleksiyon Kartı',
        title: 'VIP Tuale Dokulu & 24K Varak Yaldızlı Kartvizit',
        desc: 'Özel dokulu ithal İtalyan Tuale fantezi kağıdı ve ışıl ışıl parıldayan 24K sıcak altın yaldız baskısı.',
        fullDesc: '<ul class="ps-3 mb-0"><li><strong>İtalyan Tuale Fantezi Doku:</strong> Özel dokulu kabartmalı yüzey yapısı.</li><li><strong>24K Altın Varak Yaldız:</strong> Sıcak presle basılan ayna parlaklığında altın yaldız baskı.</li><li><strong>Özel Koleksiyon:</strong> En seçkin matbaa kağıtlarından üretilir.</li></ul>'
    }
};

function selectPackage(pkg, element) {
    window.currentSelectedPackageKey = pkg;
    if (window.PackageShowcase) {
        PackageShowcase.setPackage(pkg);
    }

    const interView = document.getElementById('mockup_interactive_view');
    const photoView = document.getElementById('mockup_photo_view');
    const videoView = document.getElementById('mockup_video_view');
    if (interView) interView.style.display = 'flex';
    if (photoView) photoView.style.display = 'none';
    if (videoView) {
        videoView.style.display = 'none';
        const vp = document.getElementById('stageVideoPlayer');
        if (vp) vp.pause();
    }

    document.querySelectorAll('.media-thumb-btn').forEach(b => {
        b.classList.remove('active', 'btn-primary');
        b.classList.add('btn-light');
    });
    const firstThumb = document.querySelector('.media-thumb-btn');
    if (firstThumb) {
        firstThumb.classList.add('active', 'btn-primary');
        firstThumb.classList.remove('btn-light');
    }

    document.querySelectorAll('.pkg-card').forEach(c => c.classList.remove('active'));
    if (element) {
        element.classList.add('active');
        const radio = element.querySelector('input[type="radio"]');
        if (radio) {
            radio.checked = true;
            radio.dispatchEvent(new Event('change'));
        }
    }

    const data = packageData[pkg];
    if (data) {
        const tablePkg = document.getElementById('tablePkgName');
        if (tablePkg) tablePkg.textContent = data.name;

        const specPaper = document.getElementById('specPaper');
        if (specPaper) specPaper.textContent = data.specPaper;

        const specLam = document.getElementById('specLamination');
        if (specLam) specLam.textContent = data.specLamination;

        const specCor = document.getElementById('specCorners');
        if (specCor) specCor.textContent = data.specCorners;

        const specFin = document.getElementById('specFinish');
        if (specFin) specFin.textContent = data.specFinish;

        const specWp = document.getElementById('specWaterproof');
        if (specWp) specWp.innerHTML = data.specWaterproof;

        const titleEl = document.getElementById('dynamicProdTitle');
        if (titleEl) titleEl.textContent = data.title;

        const descEl = document.getElementById('dynamicProdDesc');
        if (descEl) descEl.textContent = data.desc;

        const fullDescEl = document.getElementById('dynamicFullDesc');
        if (fullDescEl) fullDescEl.innerHTML = data.fullDesc;

        const mobilePkgDesc = document.getElementById('mobilePkgDesc');
        if (mobilePkgDesc) mobilePkgDesc.innerHTML = data.tag;
    }
    updateStickyBar();
}

function updateStickyBar() {
    const stickyPkgQty = document.getElementById('stickyPkgQty');
    if (!stickyPkgQty) return;
    
    const activePkgCard = document.querySelector('.pkg-card.active .pkg-title');
    const pkgName = activePkgCard ? activePkgCard.textContent.trim() : 'Standart';
    
    const activeQtyRadio = document.querySelector('input[name="quantity"]:checked');
    let qtyText = '1.000 Adet';
    if (activeQtyRadio) {
        const qtyVal = parseInt(activeQtyRadio.value) || 1000;
        qtyText = (qtyVal >= 1000 ? (qtyVal / 1000).toLocaleString('tr-TR') + '.000' : qtyVal.toLocaleString('tr-TR')) + ' Adet';
    }
    
    stickyPkgQty.textContent = pkgName + ' • ' + qtyText;
}

window.showMediaMockup = function(btn) {
    document.querySelectorAll('.media-thumb-btn').forEach(b => {
        b.classList.remove('active', 'btn-primary');
        b.classList.add('btn-light');
    });
    if (btn) {
        btn.classList.add('active', 'btn-primary');
        btn.classList.remove('btn-light');
    }
    
    const interView = document.getElementById('mockup_interactive_view');
    const photoView = document.getElementById('mockup_photo_view');
    const videoView = document.getElementById('mockup_video_view');
    
    if (photoView) photoView.style.display = 'none';
    if (videoView) {
        videoView.style.display = 'none';
        const vp = document.getElementById('stageVideoPlayer');
        if (vp) vp.pause();
    }
    if (interView) interView.style.display = 'flex';
};

window.showMediaPhoto = function(imgUrl, btn) {
    document.querySelectorAll('.media-thumb-btn').forEach(b => {
        b.classList.remove('active', 'btn-primary');
        b.classList.add('btn-light');
    });
    if (btn) {
        btn.classList.add('active', 'btn-primary');
        btn.classList.remove('btn-light');
    }

    const interView = document.getElementById('mockup_interactive_view');
    const photoView = document.getElementById('mockup_photo_view');
    const videoView = document.getElementById('mockup_video_view');
    const photoImg = document.getElementById('stagePhotoImg');

    if (interView) interView.style.display = 'none';
    if (videoView) {
        videoView.style.display = 'none';
        const vp = document.getElementById('stageVideoPlayer');
        if (vp) vp.pause();
    }
    if (photoView && photoImg) {
        photoImg.src = imgUrl;
        photoView.style.display = 'flex';
    }
};

window.showMediaVideo = function(videoUrl, btn) {
    document.querySelectorAll('.media-thumb-btn').forEach(b => {
        b.classList.remove('active', 'btn-primary');
        b.classList.add('btn-light');
    });
    if (btn) {
        btn.classList.add('active', 'btn-primary');
        btn.classList.remove('btn-light');
    }

    const interView = document.getElementById('mockup_interactive_view');
    const photoView = document.getElementById('mockup_photo_view');
    const videoView = document.getElementById('mockup_video_view');
    const videoPlayer = document.getElementById('stageVideoPlayer');

    if (interView) interView.style.display = 'none';
    if (photoView) photoView.style.display = 'none';
    if (videoView && videoPlayer) {
        videoPlayer.src = videoUrl;
        videoView.style.display = 'flex';
        videoPlayer.play().catch(() => {});
    }
};

window.openFullscreenImage = function(imgUrl) {
    const modalEl = document.getElementById('imageFullscreenModal');
    const imgEl = document.getElementById('fullscreenModalImg');
    if (modalEl && imgEl) {
        imgEl.src = imgUrl;
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
};

function selectQuantity(qty, element) {
    document.querySelectorAll('.qty-box').forEach(b => b.classList.remove('active'));
    const customContainer = document.getElementById('customQtyInputContainer');
    if (customContainer) customContainer.style.display = 'none';
    
    if (element) {
        element.classList.add('active');
        const radio = element.querySelector('input[type="radio"]');
        if (radio) {
            radio.checked = true;
            radio.dispatchEvent(new Event('change'));
        }
    }
    updateStickyBar();
}

function toggleCustomSizeInput(isCustom) {
    const row = document.getElementById('customSizeInputRow');
    if (row) row.style.display = isCustom ? 'flex' : 'none';
}

function activateCustomQty(element) {
    document.querySelectorAll('.qty-box').forEach(b => b.classList.remove('active'));
    if (element) element.classList.add('active');
    
    const customContainer = document.getElementById('customQtyInputContainer');
    const manualInput = document.getElementById('manualCustomQtyInput');
    const customRadio = document.getElementById('customQtyRadio');
    
    if (customContainer) customContainer.style.display = 'block';
    if (manualInput && customRadio) {
        manualInput.focus();
        customRadio.checked = true;
        customRadio.value = parseInt(manualInput.value) || 500;
        customRadio.dispatchEvent(new Event('change'));
    }
    updateStickyBar();
}

window.openCanvaStudio = function(templateKey) {
    const modalEl = document.getElementById('canvaStudioModal');
    if (!modalEl) return;

    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    modalEl.addEventListener('shown.bs.modal', function onModalShown() {
        modalEl.removeEventListener('shown.bs.modal', onModalShown);
        
        let stdW = parseFloat('8.40') || 8.4;
        let stdH = parseFloat('5.20') || 5.2;

        const customW = parseFloat(document.getElementById('customWidth')?.value);
        const customH = parseFloat(document.getElementById('customHeight')?.value);
        if (!isNaN(customW) && customW > 0 && !isNaN(customH) && customH > 0) {
            stdW = customW;
            stdH = customH;
        }

        const activePkg = window.currentSelectedPackageKey || document.querySelector('input[name="selected_package"]:checked')?.value || 'standart';
        let isDoubleSided = (activePkg !== 'ekonomik');

        const aspect = stdW / stdH;
        let canvasW = 850;
        let canvasH = Math.round(canvasW / aspect);

        if (aspect < 0.7) {
            canvasH = 650;
            canvasW = Math.round(canvasH * aspect);
        }

        CanvaStudio.init({
            width: canvasW,
            height: canvasH,
            widthCm: stdW,
            heightCm: stdH,
            isDoubleSided: isDoubleSided,
            productName: "Kurumsal Prestij Kartvizit (TamBaskı)",
            templateKey: templateKey || null
        });
    });
};

window.savedFrontSvg = '';
window.savedBackSvg = '';

window.showSavedDesignPanel = function(frontSvg, backSvg, isDoubleSided) {
    window.savedFrontSvg = frontSvg || '';
    window.savedBackSvg = backSvg || '';

    const initSelector = document.getElementById('designInitialSelector');
    const savedContainer = document.getElementById('designSavedContainer');
    const titleEl = document.getElementById('designSectionTitle');
    const badgeEl = document.getElementById('designSectionStatusBadge');
    const toggleGroup = document.getElementById('savedDesignSideToggleGroup');
    const badgeLabel = document.getElementById('savedDesignBadgeLabel');

    if (initSelector) initSelector.style.display = 'none';
    if (savedContainer) savedContainer.style.display = 'block';

    if (titleEl) titleEl.innerHTML = '<i class="bi bi-check-circle-fill text-success me-1"></i> 3. Tasarımınız Hazır';
    if (badgeEl) {
        badgeEl.className = 'badge bg-success-subtle text-success shadow-2xs';
        badgeEl.innerHTML = '<i class="bi bi-check2-all me-1"></i> Baskıya Hazır (300 DPI)';
    }

    if (badgeLabel) {
        badgeLabel.textContent = (isDoubleSided && backSvg) ? 'Çift Yön Tasarımınız Kaydedildi' : 'Ön Yüz Tasarımınız Kaydedildi';
    }

    if (toggleGroup) {
        toggleGroup.style.display = (isDoubleSided && backSvg) ? 'inline-flex' : 'none';
    }

    showSavedDesignSide('front');
};

window.showSavedDesignSide = function(side) {
    const previewBox = document.getElementById('savedDesignPreviewBox');
    const previewCard = document.getElementById('savedDesignPreviewCard');
    const btnFront = document.getElementById('btnPreviewSideFront');
    const btnBack = document.getElementById('btnPreviewSideBack');

    if (btnFront && btnBack) {
        if (side === 'front') {
            btnFront.className = 'btn btn-sm btn-primary active';
            btnBack.className = 'btn btn-sm btn-outline-secondary';
        } else {
            btnFront.className = 'btn btn-sm btn-outline-secondary';
            btnBack.className = 'btn btn-sm btn-primary active';
        }
    }

    if (previewCard && typeof CanvaStudio !== 'undefined') {
        const wCm = CanvaStudio.widthCm || 8.4;
        const hCm = CanvaStudio.heightCm || 5.2;
        previewCard.style.aspectRatio = `${wCm} / ${hCm}`;
    }

    if (previewBox) {
        const svgToDisplay = (side === 'back' && window.savedBackSvg) ? window.savedBackSvg : window.savedFrontSvg;
        previewBox.innerHTML = svgToDisplay;
    }
};

window.resetDesignSelection = function() {
    const initSelector = document.getElementById('designInitialSelector');
    const savedContainer = document.getElementById('designSavedContainer');
    const titleEl = document.getElementById('designSectionTitle');
    const badgeEl = document.getElementById('designSectionStatusBadge');

    if (initSelector) initSelector.style.display = 'block';
    if (savedContainer) savedContainer.style.display = 'none';

    if (titleEl) titleEl.innerHTML = '<i class="bi bi-palette text-primary me-1"></i> 3. Tasarım Yöntemini Seçin';
    if (badgeEl) {
        badgeEl.className = 'badge bg-primary-subtle text-primary shadow-2xs';
        badgeEl.innerHTML = '<i class="bi bi-stars me-1"></i> İnteraktif Tasarım &amp; Şablonlar';
    }

    const designTypeInput = document.getElementById('designTypeInput');
    if (designTypeInput) designTypeInput.value = 'none';
};

document.addEventListener('DOMContentLoaded', function() {
    const navButtons = document.querySelectorAll('.design-nav-btn');
    const tabPanes = document.querySelectorAll('.design-tab-pane');

    navButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            navButtons.forEach(b => b.classList.remove('active'));
            tabPanes.forEach(p => p.style.display = 'none');
            
            this.classList.add('active');
            const target = document.getElementById(this.dataset.tab);
            if (target) target.style.display = 'block';
        });
    });

    if (window.PackageShowcase) {
        PackageShowcase.init();
        PackageShowcase.setPackage('ekonomik');
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
