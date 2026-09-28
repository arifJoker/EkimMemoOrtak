<?php
/**
 * TAMBASKI.COM.TR - Ürün Detay & İnteraktif Vektörel Tasarımcı
 * PDF Şablonuna ve Apple UI Standartlarına %100 Uyumlu
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

<style>
/* ==========================================================================
   CANVA STUDIO & PRODUCT PAGE PDF PIXEL-PERFECT STYLES
   ========================================================================== */

/* 📦 1. Paket Seçim Kartları */
.package-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-bottom: 20px;
}
@media (max-width: 991px) {
    .package-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }
}
.pkg-card {
    position: relative;
    background: #ffffff;
    border: 2px solid #e2e8f0;
    border-radius: 14px;
    padding: 14px 10px;
    text-align: center;
    cursor: pointer;
    transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    min-height: 142px;
}
.pkg-card:hover {
    border-color: #3b82f6;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(59, 130, 246, 0.12);
}
.pkg-card.active {
    border-color: #0071e3 !important;
    background: linear-gradient(180deg, #f0f7ff 0%, #ffffff 100%) !important;
    box-shadow: 0 8px 24px rgba(0, 113, 227, 0.18) !important;
}
.pkg-card .pkg-icon {
    font-size: 26px;
    line-height: 1;
    margin-bottom: 6px;
}
.pkg-card .pkg-title {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 3px;
    line-height: 1.2;
}
.pkg-card .pkg-desc {
    font-size: 11px;
    color: #64748b;
    line-height: 1.3;
    margin-bottom: 6px;
}
.pkg-card .pkg-badge {
    font-size: 10px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 999px;
    margin-top: auto;
}
.pkg-card .pkg-ribbon {
    position: absolute;
    top: -8px;
    right: -4px;
    background: linear-gradient(135deg, #e11d48, #f43f5e);
    color: #ffffff;
    font-size: 9.5px;
    font-weight: 800;
    padding: 2px 8px;
    border-radius: 999px;
    letter-spacing: 0.3px;
    box-shadow: 0 2px 6px rgba(225, 29, 72, 0.35);
    z-index: 5;
}
.pkg-card .pkg-ribbon.ribbon-vip {
    background: linear-gradient(135deg, #d97706, #f59e0b);
    box-shadow: 0 2px 6px rgba(217, 119, 6, 0.35);
}

/* 🔢 2. Adet Seçim Grid & Kutuları */
.qty-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 8px;
    margin-bottom: 20px;
}
@media (max-width: 991px) {
    .qty-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
    }
}
.qty-box {
    position: relative;
    background: #ffffff;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 10px 4px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 58px;
}
.qty-box:hover {
    border-color: #3b82f6;
    background: #f8fafc;
}
.qty-box.active {
    border-color: #0071e3 !important;
    background: #f0f7ff !important;
    box-shadow: 0 4px 14px rgba(0, 113, 227, 0.16) !important;
}

/* 🎨 3. Tasarım Seçenekleri Butonları */
.design-nav-pills {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    background: #f1f5f9;
    padding: 6px;
    border-radius: 14px;
    margin-bottom: 14px;
}
@media (max-width: 991px) {
    .design-nav-pills {
        grid-template-columns: repeat(2, 1fr);
    }
}
.design-nav-btn {
    border: 1.5px solid transparent;
    background: #ffffff;
    padding: 10px 6px;
    font-size: 11.5px;
    font-weight: 600;
    color: #475569;
    border-radius: 10px;
    transition: all 0.2s ease;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 3px;
    text-align: center;
    line-height: 1.2;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.design-nav-btn i {
    font-size: 18px;
}
.design-nav-btn:hover {
    color: #0071e3;
    border-color: #cbd5e1;
}
.design-nav-btn.active {
    background: #ffffff;
    color: #0071e3;
    font-weight: 700;
    border-color: #0071e3;
    box-shadow: 0 4px 12px rgba(0, 113, 227, 0.18);
}

/* Sektör Filtre Çipleri */
.industry-chip {
    padding: 4px 12px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 999px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.15s ease;
}
.industry-chip:hover {
    border-color: #0071e3;
    color: #0071e3;
}
.industry-chip.active {
    background: #0f172a;
    border-color: #0f172a;
    color: #ffffff;
}

/* ==========================================================================
   CANVA STUDIO MODAL - PDF BİREBİR ARAYÜZ STİLLERİ
   ========================================================================== */
#canvaStudioModal .modal-header {
    background: #0b1329 !important;
    border-bottom: 1px solid #1e293b !important;
    min-height: 56px;
    padding: 8px 16px !important;
}

.canva-top-subbar {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    padding: 8px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.canva-left-strip {
    width: 68px;
    background: #0b1329;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 12px;
    gap: 4px;
    flex-shrink: 0;
}
.canva-strip-btn {
    width: 54px;
    height: 52px;
    border-radius: 10px;
    border: none;
    background: transparent;
    color: #94a3b8;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 600;
    gap: 2px;
    cursor: pointer;
    transition: all 0.15s ease;
}
.canva-strip-btn i {
    font-size: 18px;
}
.canva-strip-btn:hover {
    color: #ffffff;
    background: rgba(255, 255, 255, 0.08);
}
.canva-strip-btn.active {
    background: #2563eb;
    color: #ffffff;
}

.canva-side-drawer {
    width: 300px;
    background: #ffffff;
    border-right: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
}

.canva-workspace-area {
    flex-grow: 1;
    background-color: #f8fafc;
    background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
    background-size: 18px 18px;
    position: relative;
    overflow: auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

/* Cetveller */
.ruler-top {
    position: absolute;
    top: 0;
    left: 40px;
    right: 0;
    height: 22px;
    background: #f1f5f9;
    border-bottom: 1px solid #cbd5e1;
    font-size: 9.5px;
    color: #64748b;
    display: flex;
    align-items: center;
}
.ruler-left {
    position: absolute;
    top: 22px;
    left: 0;
    bottom: 0;
    width: 40px;
    background: #f1f5f9;
    border-right: 1px solid #cbd5e1;
    font-size: 9.5px;
    color: #64748b;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Kılavuz Bilgi Kutusu */
.canva-legend-box {
    position: absolute;
    top: 16px;
    right: 16px;
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(8px);
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 11px;
    color: #334155;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    z-index: 20;
    pointer-events: none;
}

/* Alt Yüzen Zoom Kapsülü */
.canva-zoom-capsule {
    position: absolute;
    bottom: 20px;
    background: #0f172a;
    color: #ffffff;
    padding: 4px 14px;
    border-radius: 999px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.25);
    z-index: 30;
    font-size: 12px;
    font-weight: 600;
}
.canva-zoom-capsule button {
    background: transparent;
    border: none;
    color: #ffffff;
    cursor: pointer;
    padding: 2px 6px;
    border-radius: 4px;
}
.canva-zoom-capsule button:hover {
    background: rgba(255, 255, 255, 0.2);
}
</style>

<div class="container py-2 py-md-3 pb-lg-4 pb-5 mb-4">
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-2 mb-md-3 d-none d-md-block">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Ana Sayfa</a></li>
            <li class="breadcrumb-item"><a href="category.php?slug=<?= urlencode($product['category_slug'] ?? 'kartvizit') ?>" class="text-decoration-none text-muted"><?= ucfirst(str_replace('-', ' ', $product['category_slug'] ?? 'kartvizit')) ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name'] ?? 'Kurumsal Prestij Kartvizit (TamBaskı)') ?></li>
        </ol>
    </nav>

    <div class="row g-3 g-lg-4">
        
        <!-- ========================================================================= -->
        <!-- SOL: ÜRÜN GÖRSELLERİ, CANLI DİNAMİK MOCKUP & TEKNİK TABLO                 -->
        <!-- ========================================================================= -->
        <div class="col-lg-5">
            <div class="apple-card p-3 sticky-top" style="top: 85px; z-index: 10;">
                
                <div class="d-none d-lg-flex flex-wrap gap-1 mb-2">
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

                    <!-- 1. İnteraktif 3D Kart Sahnesi -->
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
                    </div>

                    <!-- 3. Video Oynatıcı Görünümü -->
                    <div id="mockup_video_view" class="mockup-view-pane" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10; background: #0f172a; align-items: center; justify-content: center; padding: 8px;">
                        <video id="stageVideoPlayer" controls autoplay loop playsinline class="rounded-3 shadow-lg" style="max-width: 100%; max-height: 245px; width: auto; height: auto; object-fit: contain;"></video>
                    </div>

                    <!-- Alt Bilgi: 3D Döndürme İpucu -->
                    <div class="position-absolute bottom-0 start-50 translate-middle-x mb-1 text-secondary small text-nowrap" style="font-size: 9.5px; opacity: 0.85; z-index: 20;">
                        <i class="bi bi-hand-index-thumb text-warning me-1"></i> Kartı farenizle eğerek ışık ve varak yansımasını inceleyin
                    </div>
                </div>

                <!-- 📸 Medya Şeridi -->
                <div class="d-flex gap-1 overflow-x-auto pb-2 mb-2 align-items-center" id="prodMediaStrip" style="white-space: nowrap;">
                    <button type="button" class="btn btn-sm btn-outline-primary active rounded-3 p-1 px-2 text-nowrap media-thumb-btn" onclick="showMediaMockup(this)" title="3D Mockup Önizleme" style="font-size: 11px; height: 42px;">
                        <i class="bi bi-layers-half me-1"></i> Mockup
                    </button>
                    <button type="button" class="btn btn-sm btn-light border rounded-3 p-0 overflow-hidden media-thumb-btn" onclick="showMediaPhoto('uploads/products/veo_vip_showcase.jpg', this)" style="width: 42px; height: 42px; flex-shrink: 0;">
                        <img src="uploads/products/veo_vip_showcase.jpg" alt="Fotoğraf 1" style="width: 100%; height: 100%; object-fit: cover;">
                    </button>
                    <button type="button" class="btn btn-sm btn-light border rounded-3 p-0 overflow-hidden media-thumb-btn" onclick="showMediaPhoto('uploads/products/veo_premium_showcase.jpg', this)" style="width: 42px; height: 42px; flex-shrink: 0;">
                        <img src="uploads/products/veo_premium_showcase.jpg" alt="Fotoğraf 2" style="width: 100%; height: 100%; object-fit: cover;">
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-3 p-1 px-2 text-nowrap media-thumb-btn d-flex align-items-center gap-1 shadow-xs" onclick="showMediaVideo('uploads/videos/tambaski_vip_showcase.mp4', this)" title="Gerçekçi 3D Altın Varak & Kalınlık Videosu" style="font-size: 11px; height: 42px; background: #fff;">
                        <i class="bi bi-play-circle-fill text-danger fs-6"></i> <span class="fw-bold">Video</span>
                    </button>
                </div>
                
                <h5 class="fw-bold mb-1" id="dynamicProdTitle"><?= htmlspecialchars($product['name'] ?? 'Kurumsal Prestij Kartvizit (TamBaskı)') ?></h5>
                <p class="text-muted small mb-2 d-none d-lg-block" id="dynamicProdDesc"><?= htmlspecialchars($product['short_desc'] ?? '350gr Kuşe, Soft-Touch Kadife, 24K Altın Varak ve Kabartma Lak Seçenekleriyle Firmanızı Zirveye Taşıyın.') ?></p>

                <!-- 🌟 4'lü Güvenlik Rozetleri -->
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

                    <!-- Teknik Özellikler Tablosu -->
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
                </div>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- SAĞ: SİPARİŞ AKIŞI (1. PAKET | 2. ADET | 3. TASARIM VE SEPET)             -->
        <!-- ========================================================================= -->
        <div class="col-lg-7">
            <div class="apple-card p-3 p-md-4">
                
                <!-- 1. BASKI PAKETİNİ SEÇİN -->
                <div class="d-flex justify-content-between align-items-baseline mb-2">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">1. Baskı Paketini Seçin</h5>
                        <span class="text-muted small">İhtiyacınıza en uygun hazır paketi belirleyin</span>
                    </div>
                </div>

                <form id="printConfigForm" data-product-id="<?= (int)($product['id'] ?? 1) ?>" action="cart.php" method="POST">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?= (int)($product['id'] ?? 1) ?>">
                    <input type="hidden" name="design_type" id="designTypeInput" value="canva_studio">
                    <input type="hidden" name="design_file" id="selectedDesignFile" value="">
                    <input type="hidden" name="design_svg" id="selectedDesignSvg" value="">
                    <input type="hidden" name="design_back_svg" id="selectedDesignBackSvg" value="">

                    <!-- 📦 4'LÜ HAZIR PAKET KARTLARI -->
                    <div class="package-grid">
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
                            <div class="pkg-desc">Soft-Touch Kadife Selefon &amp; Lak</div>
                            <span class="pkg-badge" style="background: #8b5cf6; color: #fff;">Lüks Doku</span>
                        </div>
                        <div class="pkg-card" id="card_pkg_vip" onclick="selectPackage('vip', this)">
                            <span class="pkg-ribbon ribbon-vip">VIP</span>
                            <input type="radio" name="selected_package" value="vip" style="display:none;">
                            <span class="pkg-icon text-warning">
                                <i class="bi bi-gem"></i>
                            </span>
                            <div class="pkg-title">VIP Prestij</div>
                            <div class="pkg-desc">Tuale Fantezi / 24K Altın Varak</div>
                            <span class="pkg-badge bg-warning-subtle text-dark">Maksimum Prestij</span>
                        </div>
                    </div>

                    <!-- 2. BASKI ADEDİ BELİRLEYİN -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="fw-bold mb-0 text-dark">
                                <i class="bi bi-layers me-1 text-primary"></i> 2. Baskı Adedi
                            </label>
                            <span class="text-muted small">Tiraj arttıkça birim fiyat düşer</span>
                        </div>
                        
                        <div class="qty-grid">
                            <div class="qty-box active" id="qty_box_1000" onclick="selectQuantity('1000', this)">
                                <input type="radio" name="quantity" value="1000" checked style="display:none;">
                                <div class="fw-bold text-dark fs-6">1.000</div>
                                <div class="text-muted" style="font-size: 11px;">Adet</div>
                            </div>
                            <div class="qty-box" id="qty_box_2000" onclick="selectQuantity('2000', this)">
                                <span class="position-absolute top-0 end-0 translate-middle-y badge bg-success rounded-pill" style="font-size: 8.5px; right: 2px;">%15 İndirim</span>
                                <input type="radio" name="quantity" value="2000" style="display:none;">
                                <div class="fw-bold text-dark fs-6">2.000</div>
                                <div class="text-muted" style="font-size: 11px;">Adet</div>
                            </div>
                            <div class="qty-box" id="qty_box_3000" onclick="selectQuantity('3000', this)">
                                <span class="position-absolute top-0 end-0 translate-middle-y badge bg-success rounded-pill" style="font-size: 8.5px; right: 2px;">%22 İndirim</span>
                                <input type="radio" name="quantity" value="3000" style="display:none;">
                                <div class="fw-bold text-dark fs-6">3.000</div>
                                <div class="text-muted" style="font-size: 11px;">Adet</div>
                            </div>
                            <div class="qty-box" id="qty_box_5000" onclick="selectQuantity('5000', this)">
                                <span class="position-absolute top-0 end-0 translate-middle-y badge bg-success rounded-pill" style="font-size: 8.5px; right: 2px;">%30 İndirim</span>
                                <input type="radio" name="quantity" value="5000" style="display:none;">
                                <div class="fw-bold text-dark fs-6">5.000</div>
                                <div class="text-muted" style="font-size: 11px;">Adet</div>
                            </div>
                            <div class="qty-box" id="qty_box_10000" onclick="selectQuantity('10000', this)">
                                <span class="position-absolute top-0 end-0 translate-middle-y badge bg-success rounded-pill" style="font-size: 8.5px; right: 2px;">%38 İndirim</span>
                                <input type="radio" name="quantity" value="10000" style="display:none;">
                                <div class="fw-bold text-dark fs-6">10.000</div>
                                <div class="text-muted" style="font-size: 11px;">Adet</div>
                            </div>
                            <div class="qty-box" id="qty_box_custom" onclick="activateCustomQty(this)" style="border-style: dashed;">
                                <span class="position-absolute top-0 end-0 translate-middle-y badge bg-info rounded-pill" style="font-size: 8.5px; right: 2px;">Özel</span>
                                <input type="radio" name="quantity" id="customQtyRadio" value="500" style="display:none;">
                                <div class="fw-bold text-primary fs-6"><i class="bi bi-pencil-square"></i></div>
                                <div class="text-muted" style="font-size: 11px;">Özel Adet</div>
                            </div>
                        </div>

                        <!-- Özel Adet Manuel Giriş -->
                        <div id="customQtyInputContainer" class="p-2 bg-light rounded-3 border mb-2" style="display: none;">
                            <div class="row g-2 align-items-center">
                                <div class="col-auto">
                                    <label class="form-label small fw-bold mb-0 text-dark">İstediğiniz Adet:</label>
                                </div>
                                <div class="col">
                                    <div class="input-group input-group-sm">
                                        <input type="number" id="manualCustomQtyInput" class="form-control" min="10" step="10" value="500">
                                        <span class="input-group-text">Adet</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. TASARIM YÖNTEMİNİ SEÇİN (TEK TIKLA BAŞLA & MESLEK ŞABLONLARI) -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="fw-bold mb-0 text-dark">
                                <i class="bi bi-palette-fill me-1 text-primary"></i> 3. Tasarım Yönteminizi Belirleyin
                            </label>
                            <span class="badge bg-primary-subtle text-primary" style="font-size: 10px;">
                                <i class="bi bi-stars me-1"></i> 300 DPI Vektör Editör
                            </span>
                        </div>

                        <!-- 4'lü Tasarım Yöntemi Sekmeleri -->
                        <div class="design-nav-pills">
                            <button type="button" class="design-nav-btn active" data-tab="tabQuickEditor">
                                <i class="bi bi-magic text-danger"></i>
                                <span>🎨 Kendin Tasarla (Tek Tıkla)</span>
                            </button>
                            <button type="button" class="design-nav-btn" data-tab="tabTemplates">
                                <i class="bi bi-grid-1x2 text-primary"></i>
                                <span>📋 Hazır Şablonlar (Meslekler)</span>
                            </button>
                            <button type="button" class="design-nav-btn" data-tab="tabUpload">
                                <i class="bi bi-cloud-arrow-up text-success"></i>
                                <span>📁 Dosya Yükle (PDF/AI/PNG)</span>
                            </button>
                            <button type="button" class="design-nav-btn" data-tab="tabSupport">
                                <i class="bi bi-whatsapp text-success"></i>
                                <span>💬 Grafik Desteği</span>
                            </button>
                        </div>

                        <!-- TAB 1: KENDİN TASARLA (TEK TIKLA BAŞLA) -->
                        <div id="tabQuickEditor" class="design-tab-pane">
                            <div class="p-3 rounded-4 border bg-white shadow-2xs" style="border-color: #fecdd3 !important; background: linear-gradient(180deg, #fff1f2 0%, #ffffff 100%) !important;">
                                <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom">
                                    <div class="rounded-3 p-2 text-white shadow-xs" style="background: linear-gradient(135deg, #e11d48, #f43f5e); width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-palette-fill fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small lh-1">TamBaskı Online Editör (Canva Modu)</div>
                                        <span class="text-muted" style="font-size: 10.5px;">Logonuzu ekleyin, yazılarınızı girin, 3D önizleyin (8.4 x 5.2 cm · 300 DPI)</span>
                                    </div>
                                </div>
                                <p class="small text-muted mb-3" style="font-size: 11.5px; line-height: 1.4;">
                                    Sıfırdan temiz sayfada veya hazır elementlerle anında kendi kartvizitinizi oluşturabilirsiniz.
                                </p>
                                <button type="button" class="btn btn-danger w-100 py-3 fw-bold rounded-pill shadow-xs d-flex align-items-center justify-content-center gap-2" onclick="openCanvaStudio()" style="background: linear-gradient(135deg, #e11d48, #fb7185); border: none;">
                                    <i class="bi bi-palette-fill fs-5"></i>
                                    <span class="fs-6">🎨 Tasarım Editörünü Başlat (Tek Tıkla)</span>
                                </button>
                            </div>
                        </div>

                        <!-- TAB 2: MESLEK & HAZIR ŞABLON SEÇİMİ -->
                        <div id="tabTemplates" class="design-tab-pane" style="display: none;">
                            <div class="p-3 rounded-4 border bg-white shadow-2xs">
                                <div class="fw-bold small text-dark mb-2"><i class="bi bi-briefcase me-1 text-primary"></i> Sektör / Mesleğinizi Seçin:</div>
                                
                                <!-- Sektör Hap Butonları -->
                                <div class="d-flex gap-1 overflow-x-auto pb-2 mb-3" style="white-space: nowrap;">
                                    <button type="button" class="industry-chip active" onclick="filterSectorTemplates('all', this)">Tümü</button>
                                    <button type="button" class="industry-chip" onclick="filterSectorTemplates('kurumsal', this)">🏢 Kurumsal</button>
                                    <button type="button" class="industry-chip" onclick="filterSectorTemplates('avukat', this)">⚖️ Avukat / Hukuk</button>
                                    <button type="button" class="industry-chip" onclick="filterSectorTemplates('emlak', this)">🏡 Emlak &amp; Gayrimenkul</button>
                                    <button type="button" class="industry-chip" onclick="filterSectorTemplates('kafe', this)">☕ Kafe &amp; Restoran</button>
                                    <button type="button" class="industry-chip" onclick="filterSectorTemplates('saglik', this)">🩺 Sağlık &amp; Klinik</button>
                                </div>

                                <!-- Şablon Kartları Grid (2 Sütunlu) -->
                                <div class="row g-2" id="mainSectorTemplatesGrid">
                                    <!-- JS Tarafından Doldurulur -->
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: DOSYA YÜKLE -->
                        <div id="tabUpload" class="design-tab-pane" style="display: none;">
                            <div class="p-4 rounded-4 border bg-white text-center shadow-2xs border-dashed" style="border-width: 2px;">
                                <i class="bi bi-cloud-arrow-up-fill fs-1 text-primary mb-2 d-block"></i>
                                <div class="fw-bold text-dark small mb-1">Tasarım Dosyanızı Buraya Sürükleyin veya Seçin</div>
                                <div class="text-muted small mb-3" style="font-size: 11px;">PDF, AI, PSD, EPS, TIFF, SVG veya Yüksek Çözünürlüklü PNG/JPG (Max: 50MB)</div>
                                <label for="mainFileUploadInput" class="btn btn-primary rounded-pill px-4 fw-bold cursor-pointer">
                                    <i class="bi bi-folder2-open me-1"></i> Dosya Seçin
                                </label>
                                <input type="file" id="mainFileUploadInput" class="d-none" onchange="handleMainFileUpload(this.files[0])">
                                <div id="mainUploadStatus" class="mt-2 small fw-bold" style="display: none;"></div>
                            </div>
                        </div>

                        <!-- TAB 4: WHATSAPP GRAFİK DESTEĞİ -->
                        <div id="tabSupport" class="design-tab-pane" style="display: none;">
                            <div class="p-3 rounded-4 border bg-white shadow-2xs" style="border-color: #bbf7d0 !important; background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%) !important;">
                                <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom">
                                    <div class="rounded-3 p-2 text-white shadow-xs" style="background: linear-gradient(135deg, #16a34a, #22c55e); width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-whatsapp fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small lh-1">Grafik Tasarım &amp; WhatsApp Destek Hattı</div>
                                        <span class="text-muted" style="font-size: 10.5px;">Baskı öncesi grafiker ekibimizle birebir görüşün</span>
                                    </div>
                                </div>
                                <p class="small text-muted mb-3" style="font-size: 11.5px; line-height: 1.4;">
                                    Tasarımınız hazır değilse; logonuzu ve bilgilerinizi WhatsApp üzerinden grafiker ekibimize doğrudan iletebilirsiniz. Baskı öncesi onayınız alınır.
                                </p>
                                <a href="https://wa.me/905550000000?text=Merhaba,%20kartvizit%20tasarim%20destegi%20istiyorum." target="_blank" class="btn btn-success w-100 py-2 fw-bold rounded-pill shadow-xs d-flex align-items-center justify-content-center gap-2" style="background: #25D366; border: none;">
                                    <i class="bi bi-whatsapp fs-5"></i>
                                    <span>Grafikerimize WhatsApp'tan Yazın</span>
                                </a>
                            </div>
                        </div>

                        <!-- Tasarım Kaydedildiğinde Canlı SVG Önizleme Kartı -->
                        <div id="designSavedContainer" class="mt-3" style="display: none;">
                            <div class="p-3 rounded-4 border bg-white shadow-sm" style="border-color: #10b981 !important; background: linear-gradient(180deg, #ecfdf5 0%, #ffffff 100%) !important;">
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom flex-wrap gap-2">
                                    <span class="badge bg-success rounded-pill px-3 py-1 shadow-xs" style="font-size: 11px;">
                                        <i class="bi bi-check-circle-fill me-1"></i> <span id="savedDesignBadgeLabel">Tasarımınız Hazır (300 DPI Vektör)</span>
                                    </span>
                                    <div class="btn-group btn-group-sm" id="savedDesignSideToggleGroup" style="display: none;">
                                        <button type="button" class="btn btn-sm btn-primary active" id="btnPreviewSideFront" onclick="showSavedDesignSide('front')">Ön Yüz</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnPreviewSideBack" onclick="showSavedDesignSide('back')">Arka Yüz</button>
                                    </div>
                                </div>

                                <div class="saved-preview-stage p-3 rounded-3 border d-flex align-items-center justify-content-center mb-3" style="background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%); min-height: 200px;">
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
                                        <i class="bi bi-arrow-repeat me-1"></i> Farklı Yöntem Seç
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- FİYAT ÖZETİ & SEPETE EKLE -->
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
                            <span id="calcGrandTotal" class="price-display-lg text-primary" style="font-size: 26px; font-weight: 800;">540,00 ₺</span>
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
<!-- 🎨 CANVA STUDIO MODALI (PDF ŞABLONUNA %100 BİREBİR)                         -->
<!-- ========================================================================= -->
<div class="modal fade" id="canvaStudioModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content border-0 rounded-0 bg-light">
            
            <!-- 1. ÜST BAR (DARK HEADER) -->
            <div class="modal-header bg-dark text-white py-1 px-3 border-0 d-flex justify-content-between align-items-center">
                <!-- Sol: Geri & Başlık -->
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3 py-1" data-bs-dismiss="modal" title="Geri Dön">
                        <i class="bi bi-arrow-left me-1"></i> <span>Ürüne Dön</span>
                    </button>
                    <div class="d-none d-sm-flex align-items-center gap-2">
                        <div class="rounded-2 p-1 text-white bg-primary d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                            <i class="bi bi-palette-fill small"></i>
                        </div>
                        <div>
                            <span class="fw-bold text-white small d-block lh-1">TamBaskı Editör</span>
                            <span class="text-white-50" style="font-size: 10.5px;">8.4 × 5.2 cm · 300 DPI · CMYK</span>
                        </div>
                    </div>
                </div>

                <!-- Orta: Ön Yüz / Arka Yüz Toggle -->
                <div class="d-flex align-items-center bg-black bg-opacity-40 p-1 rounded-pill border border-secondary border-opacity-25" id="canvaSideSwitchGroup">
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-bold" id="btnSideFront" onclick="CanvaStudio.switchSide('front')">
                        Ön Yüz
                    </button>
                    <button type="button" class="btn btn-sm text-white rounded-pill px-3 py-1 fw-semibold" id="btnSideBack" onclick="CanvaStudio.switchSide('back')">
                        Arka Yüz
                    </button>
                </div>

                <!-- Sağ: 3D Önizle, Kılavuz, Sepete Ekle -->
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3 py-1 fw-semibold d-none d-md-inline-flex align-items-center gap-1" onclick="CanvaStudio.open3dMockup()">
                        <i class="bi bi-box"></i> <span>3D Önizle</span>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary text-white rounded-pill px-2 py-1 active d-none d-lg-inline-flex" id="btnToggleCanvaGuides" onclick="CanvaStudio.toggleGuides()" title="Kılavuzları Göster/Gizle">
                        <i class="bi bi-bounding-box me-1"></i> <span>Güvenli Alanı Gizle</span>
                    </button>
                    <button type="button" class="btn btn-sm btn-primary fw-bold rounded-pill px-3 py-1 shadow-sm d-flex align-items-center gap-1" onclick="CanvaStudio.saveAndApplyToOrder()">
                        <i class="bi bi-cart-check-fill"></i> <span>Sepete Ekle &amp; Tamamla</span>
                    </button>
                </div>
            </div>

            <!-- 2. ALT HIZLI AYARLAR BARI (PAKET & ADET DROPDOWN + CANLI FİYAT) -->
            <div class="canva-top-subbar">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="d-flex align-items-center gap-1">
                        <span class="small fw-semibold text-muted" style="font-size: 11.5px;">Paket:</span>
                        <select class="form-select form-select-sm fw-bold border-secondary-subtle" id="canvaSubbarPkgSelect" onchange="CanvaStudio.setPackage(this.value)" style="width: 220px; font-size: 11.5px;">
                            <option value="ekonomik">Ekonomik · 250 gr Tek Yön</option>
                            <option value="standart">Standart · 350 gr Çift Yön Mat</option>
                            <option value="premium">Premium · 350 gr Kadife Laklı</option>
                            <option value="vip">VIP · 24K Altın Varak Tuale</option>
                        </select>
                    </div>

                    <div class="d-flex align-items-center gap-1">
                        <span class="small fw-semibold text-muted" style="font-size: 11.5px;">Adet:</span>
                        <select class="form-select form-select-sm fw-bold border-secondary-subtle" id="canvaStudioQtySelect" onchange="CanvaStudio.setQuantity(this.value)" style="width: 130px; font-size: 11.5px;">
                            <option value="1000">1.000 Adet</option>
                            <option value="2000">2.000 Adet</option>
                            <option value="3000">3.000 Adet</option>
                            <option value="5000">5.000 Adet</option>
                            <option value="10000">10.000 Adet</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <span class="text-success small fw-bold d-none d-sm-inline"><i class="bi bi-check2 me-1"></i> Otomatik kaydedildi</span>
                    <div class="fw-bold text-dark fs-6" id="canvaStudioNetPrice">540,00 ₺ <span class="small text-muted fw-normal" style="font-size: 10px;">+KDV</span></div>
                </div>
            </div>

            <!-- 3. ANA ÇALIŞMA ALANI (SOL İKON ŞERİDİ + ÇEKMECE + ORTA TUVAL) -->
            <div class="modal-body p-0 d-flex overflow-hidden" style="height: calc(100vh - 108px);">
                
                <!-- 1. En Sol Dikey Araç Şeridi (Koyu) -->
                <div class="canva-left-strip">
                    <button type="button" class="canva-strip-btn active" onclick="switchDrawerTab('templates', this)">
                        <i class="bi bi-grid-fill"></i>
                        <span>Şablonlar</span>
                    </button>
                    <button type="button" class="canva-strip-btn" onclick="switchDrawerTab('text', this)">
                        <i class="bi bi-type"></i>
                        <span>Metin</span>
                    </button>
                    <button type="button" class="canva-strip-btn" onclick="switchDrawerTab('image', this)">
                        <i class="bi bi-image"></i>
                        <span>Logo</span>
                    </button>
                    <button type="button" class="canva-strip-btn" onclick="switchDrawerTab('shapes', this)">
                        <i class="bi bi-square"></i>
                        <span>Şekiller</span>
                    </button>
                    <button type="button" class="canva-strip-btn" onclick="switchDrawerTab('icons', this)">
                        <i class="bi bi-qr-code"></i>
                        <span>QR Kod</span>
                    </button>
                    <button type="button" class="canva-strip-btn" onclick="switchDrawerTab('bg', this)">
                        <i class="bi bi-paint-bucket"></i>
                        <span>Renk</span>
                    </button>
                </div>

                <!-- 2. Sol Çekmece Paneli (Beyaz) -->
                <div class="canva-side-drawer p-3 overflow-y-auto">
                    
                    <!-- Çekmece: ŞABLONLAR -->
                    <div id="drawerPane-templates">
                        <h6 class="fw-bold small text-dark mb-2">Şablonlar</h6>
                        <div class="input-group input-group-sm mb-2 shadow-2xs">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" id="drawerTemplateSearch" class="form-control border-start-0 ps-0" placeholder="420+ şablonda ara..." oninput="CanvaStudio.onSearchTemplates(this.value)">
                        </div>

                        <!-- Sektör Çipleri -->
                        <div class="d-flex gap-1 overflow-x-auto pb-2 mb-2" style="white-space: nowrap;">
                            <button type="button" class="industry-chip active" onclick="filterDrawerTemplates('all', this)">Tümü</button>
                            <button type="button" class="industry-chip" onclick="filterDrawerTemplates('kurumsal', this)">Kurumsal</button>
                            <button type="button" class="industry-chip" onclick="filterDrawerTemplates('avukat', this)">Avukat</button>
                            <button type="button" class="industry-chip" onclick="filterDrawerTemplates('emlak', this)">Emlak</button>
                            <button type="button" class="industry-chip" onclick="filterDrawerTemplates('kafe', this)">Kafe</button>
                            <button type="button" class="industry-chip" onclick="filterDrawerTemplates('saglik', this)">Sağlık</button>
                        </div>

                        <!-- 2 Sütunlu Şablon Listesi -->
                        <div class="row g-2" id="drawerTemplatesGrid">
                            <!-- JS ile doldurulur -->
                        </div>
                    </div>

                    <!-- Çekmece: METİN -->
                    <div id="drawerPane-text" style="display: none;">
                        <h6 class="fw-bold small text-dark mb-2">Metin Ekle</h6>
                        <button type="button" class="btn btn-light w-100 text-start p-2 mb-2 border rounded-3 fw-bold fs-6" onclick="CanvaStudio.addHeading()">
                            <i class="bi bi-plus-circle text-primary me-2"></i> Başlık Ekle
                        </button>
                        <button type="button" class="btn btn-light w-100 text-start p-2 mb-2 border rounded-3 fw-semibold small" onclick="CanvaStudio.addSubheading()">
                            <i class="bi bi-plus-circle text-primary me-2"></i> Alt Başlık Ekle
                        </button>
                        <button type="button" class="btn btn-light w-100 text-start p-2 mb-2 border rounded-3 small text-muted" onclick="CanvaStudio.addBodyText()">
                            <i class="bi bi-plus text-primary me-2"></i> Gövde Metni Ekle
                        </button>
                    </div>

                    <!-- Çekmece: LOGO / GÖRSEL -->
                    <div id="drawerPane-image" style="display: none;">
                        <h6 class="fw-bold small text-dark mb-2">Logo &amp; Görsel Yükle</h6>
                        <div class="p-3 border rounded-3 bg-light text-center mb-2">
                            <i class="bi bi-cloud-arrow-up fs-2 text-primary mb-1 d-block"></i>
                            <label for="drawerLogoUploadInput" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold cursor-pointer">
                                <i class="bi bi-folder2-open me-1"></i> Logo / Resim Seç
                            </label>
                            <input type="file" id="drawerLogoUploadInput" class="d-none" accept="image/*" onchange="handleLogoUpload(this.files[0])">
                            <div class="text-muted mt-2" style="font-size: 10px;">PNG, JPG, SVG desteklenir.</div>
                        </div>
                    </div>

                    <!-- Çekmece: ŞEKİLLER -->
                    <div id="drawerPane-shapes" style="display: none;">
                        <h6 class="fw-bold small text-dark mb-2">Şekil Ekle</h6>
                        <div class="row g-2">
                            <div class="col-4"><button type="button" class="btn btn-outline-secondary w-100 p-2 rounded-3 text-center" onclick="CanvaStudio.addShape('rect')"><i class="bi bi-square-fill fs-5 text-primary"></i><div style="font-size: 10px;">Kutu</div></button></div>
                            <div class="col-4"><button type="button" class="btn btn-outline-secondary w-100 p-2 rounded-3 text-center" onclick="CanvaStudio.addShape('circle')"><i class="bi bi-circle-fill fs-5 text-primary"></i><div style="font-size: 10px;">Daire</div></button></div>
                            <div class="col-4"><button type="button" class="btn btn-outline-secondary w-100 p-2 rounded-3 text-center" onclick="CanvaStudio.addShape('line')"><i class="bi bi-dash-lg fs-5 text-primary"></i><div style="font-size: 10px;">Çizgi</div></button></div>
                        </div>
                    </div>

                    <!-- Çekmece: QR KOD -->
                    <div id="drawerPane-icons" style="display: none;">
                        <h6 class="fw-bold small text-dark mb-2">Dinamik QR Kod</h6>
                        <div class="input-group input-group-sm mb-2">
                            <input type="text" id="canvaQrInput" class="form-control" placeholder="https://tambaski.com.tr">
                            <button type="button" class="btn btn-primary fw-bold" onclick="CanvaStudio.addQrCode(document.getElementById('canvaQrInput').value)">Ekle</button>
                        </div>
                    </div>

                    <!-- Çekmece: RENK -->
                    <div id="drawerPane-bg" style="display: none;">
                        <h6 class="fw-bold small text-dark mb-2">Arka Plan Rengi</h6>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <input type="color" id="canvaBgColorPicker" value="#0f172a" class="form-control form-control-color p-0" style="width: 40px; height: 35px; cursor: pointer;">
                            <span class="small text-muted" style="font-size: 11px;">Özel Renk Seç</span>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-sm border rounded-circle p-0" style="width: 26px; height: 26px; background: #0f172a;" onclick="CanvaStudio.setBackgroundColor('#0f172a')"></button>
                            <button type="button" class="btn btn-sm border rounded-circle p-0" style="width: 26px; height: 26px; background: #ffffff;" onclick="CanvaStudio.setBackgroundColor('#ffffff')"></button>
                            <button type="button" class="btn btn-sm border rounded-circle p-0" style="width: 26px; height: 26px; background: #2563eb;" onclick="CanvaStudio.setBackgroundColor('#2563eb')"></button>
                            <button type="button" class="btn btn-sm border rounded-circle p-0" style="width: 26px; height: 26px; background: #450a0a;" onclick="CanvaStudio.setBackgroundColor('#450a0a')"></button>
                            <button type="button" class="btn btn-sm border rounded-circle p-0" style="width: 26px; height: 26px; background: #064e3b;" onclick="CanvaStudio.setBackgroundColor('#064e3b')"></button>
                        </div>
                    </div>

                </div>

                <!-- 3. Orta Canvas Çalışma Alanı -->
                <div class="canva-workspace-area">
                    
                    <!-- Kılavuz Bilgi Kutusu (Sağ Üst) -->
                    <div class="canva-legend-box">
                        <div><span style="color: #ef4444; font-weight: bold;">--</span> Kesim çizgisi (84×52 mm)</div>
                        <div><span style="color: #06b6d4; font-weight: bold;">--</span> Güvenli alan (3 mm)</div>
                        <div><span style="color: #94a3b8; font-weight: bold;">■</span> Taşma payı (3 mm)</div>
                    </div>

                    <!-- Canvas Kart Sahnesi -->
                    <div id="canvaCanvasHolder" class="shadow-2xl rounded-3 overflow-hidden bg-white" style="line-height: 0; position: relative;">
                        <canvas id="canvaMainCanvas" width="850" height="526"></canvas>
                    </div>

                    <!-- Alt Koyu Kapsül (Zoom & Undo/Redo) -->
                    <div class="canva-zoom-capsule">
                        <button type="button" onclick="CanvaStudio.undo()" title="Geri Al"><i class="bi bi-arrow-counterclockwise"></i></button>
                        <button type="button" onclick="CanvaStudio.redo()" title="İleri Al"><i class="bi bi-arrow-clockwise"></i></button>
                        <span class="text-white-50">|</span>
                        <button type="button" onclick="zoomCanvas(-0.1)">−</button>
                        <span id="canvaZoomLabel">100%</span>
                        <button type="button" onclick="zoomCanvas(+0.1)">+</button>
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
<!-- JAVASCRIPT MOTORLARI & FABRIC.JS KÜTÜPHANELERİ                            -->
<!-- ========================================================================= -->
<script src="assets/js/fabric.min.js?v=<?= time() ?>"></script>
<script src="assets/js/canva_templates_engine.js?v=<?= time() ?>"></script>
<script src="assets/js/canva_studio.js?v=<?= time() ?>"></script>
<script src="assets/js/package_showcase.js?v=<?= time() ?>"></script>

<script>
const packagePrices = {
    ekonomik: { 1000: 450, 2000: 765, 3000: 1050, 5000: 1575, 10000: 2790 },
    standart: { 1000: 650, 2000: 1105, 3000: 1520, 5000: 2275, 10000: 4030 },
    premium:  { 1000: 950, 2000: 1615, 3000: 2220, 5000: 3325, 10000: 5890 },
    vip:      { 1000: 1450, 2000: 2465, 3000: 3390, 5000: 5075, 10000: 8990 }
};

let currentPkg = 'ekonomik';
let currentQty = 1000;

function calculatePrice() {
    let subtotal = 450;
    if (packagePrices[currentPkg] && packagePrices[currentPkg][currentQty]) {
        subtotal = packagePrices[currentPkg][currentQty];
    } else {
        const base1k = (packagePrices[currentPkg] && packagePrices[currentPkg][1000]) || 450;
        subtotal = Math.round((currentQty / 1000) * base1k * 0.85);
    }

    const tax = Math.round(subtotal * 0.20);
    const grandTotal = subtotal + tax;
    const unitPrice = (subtotal / currentQty).toFixed(2);

    document.getElementById('calcSubtotal').textContent = subtotal.toLocaleString('tr-TR', { minimumFractionDigits: 2 }) + ' ₺';
    document.getElementById('calcTax').textContent = tax.toLocaleString('tr-TR', { minimumFractionDigits: 2 }) + ' ₺';
    document.getElementById('calcGrandTotal').textContent = grandTotal.toLocaleString('tr-TR', { minimumFractionDigits: 2 }) + ' ₺';
    document.getElementById('calcUnitPrice').textContent = 'Birim: ' + unitPrice.replace('.', ',') + ' ₺';

    const canvaNet = document.getElementById('canvaStudioNetPrice');
    if (canvaNet) canvaNet.innerHTML = grandTotal.toLocaleString('tr-TR', { minimumFractionDigits: 2 }) + ' ₺ <span class="small text-muted fw-normal" style="font-size: 10px;">+KDV</span>';
}

function selectPackage(pkg, element) {
    currentPkg = pkg;
    window.currentSelectedPackageKey = pkg;

    document.querySelectorAll('.pkg-card').forEach(c => c.classList.remove('active'));
    if (element) {
        element.classList.add('active');
        const radio = element.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;
    }

    const subbarSelect = document.getElementById('canvaSubbarPkgSelect');
    if (subbarSelect) subbarSelect.value = pkg;

    calculatePrice();
}

function selectQuantity(qty, element) {
    currentQty = parseInt(qty) || 1000;

    document.querySelectorAll('.qty-box').forEach(b => b.classList.remove('active'));
    document.getElementById('customQtyInputContainer').style.display = 'none';

    if (element) {
        element.classList.add('active');
        const radio = element.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;
    }

    const qtySelect = document.getElementById('canvaStudioQtySelect');
    if (qtySelect) qtySelect.value = qty;

    calculatePrice();
}

function activateCustomQty(element) {
    document.querySelectorAll('.qty-box').forEach(b => b.classList.remove('active'));
    if (element) element.classList.add('active');

    const container = document.getElementById('customQtyInputContainer');
    const input = document.getElementById('manualCustomQtyInput');
    container.style.display = 'block';
    input.focus();

    input.oninput = function() {
        currentQty = parseInt(this.value) || 500;
        document.getElementById('customQtyRadio').value = currentQty;
        calculatePrice();
    };
}

function switchDrawerTab(tabId, btn) {
    document.querySelectorAll('.canva-strip-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');

    const panes = ['templates', 'text', 'image', 'shapes', 'icons', 'bg'];
    panes.forEach(p => {
        const el = document.getElementById('drawerPane-' + p);
        if (el) el.style.display = (p === tabId) ? 'block' : 'none';
    });
}

function filterDrawerTemplates(sector, btn) {
    document.querySelectorAll('.canva-side-drawer .industry-chip').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');

    if (window.CanvaTemplatesEngine) {
        CanvaTemplatesEngine.renderTemplateGrid('drawerTemplatesGrid', sector);
    }
}

function filterSectorTemplates(sector, btn) {
    document.querySelectorAll('#tabTemplates .industry-chip').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');

    if (window.CanvaTemplatesEngine) {
        CanvaTemplatesEngine.renderTemplateGrid('mainSectorTemplatesGrid', sector);
    }
}

window.openCanvaStudio = function(templateId) {
    const modalEl = document.getElementById('canvaStudioModal');
    if (!modalEl) return;

    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    modalEl.addEventListener('shown.bs.modal', function onShown() {
        modalEl.removeEventListener('shown.bs.modal', onShown);

        CanvaStudio.init({
            widthCm: 8.4,
            heightCm: 5.2,
            isDoubleSided: (currentPkg !== 'ekonomik')
        });

        // Şablonları çekmeceye ve ana sekmeye bas
        if (window.CanvaTemplatesEngine) {
            CanvaTemplatesEngine.renderTemplateGrid('drawerTemplatesGrid', 'all');
            CanvaTemplatesEngine.renderTemplateGrid('mainSectorTemplatesGrid', 'all');
        }

        if (templateId && window.CanvaStudio) {
            CanvaStudio.loadTemplateById(templateId);
        } else if (window.CanvaStudio) {
            CanvaStudio.loadTemplateById('tpl_kurumsal_lacivert');
        }
    });
};

let currentZoom = 1;
function zoomCanvas(delta) {
    currentZoom = Math.max(0.5, Math.min(1.5, currentZoom + delta));
    const holder = document.getElementById('canvaCanvasHolder');
    const label = document.getElementById('canvaZoomLabel');
    if (holder) holder.style.transform = `scale(${currentZoom})`;
    if (label) label.textContent = Math.round(currentZoom * 100) + '%';
}

function handleMainFileUpload(file) {
    if (!file) return;
    const status = document.getElementById('mainUploadStatus');
    status.style.display = 'block';
    status.className = 'mt-2 small text-primary fw-bold';
    status.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Dosya yükleniyor: ' + file.name;

    const formData = new FormData();
    formData.append('file', file);

    fetch('api/upload_design.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            status.className = 'mt-2 small text-success fw-bold';
            status.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Dosya yüklendi: ' + data.original_name;
            document.getElementById('selectedDesignFile').value = data.file_path;
            document.getElementById('designTypeInput').value = 'uploaded';
        } else {
            status.className = 'mt-2 small text-danger fw-bold';
            status.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> ' + (data.error || 'Yükleme başarısız.');
        }
    });
}

function handleLogoUpload(file) {
    if (!file) return;
    const formData = new FormData();
    formData.append('file', file);

    fetch('api/upload_design.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && window.CanvaStudio) {
            fabric.Image.fromURL(data.file_path, function(img) {
                img.scaleToWidth(140);
                img.set({ left: 100, top: 100 });
                CanvaStudio.canvas.add(img);
                CanvaStudio.canvas.setActiveObject(img);
                CanvaStudio.canvas.renderAll();
            });
        }
    });
}

window.showSavedDesignPanel = function(frontSvg, backSvg, isDoubleSided) {
    const savedContainer = document.getElementById('designSavedContainer');
    const previewBox = document.getElementById('savedDesignPreviewBox');
    const toggleGroup = document.getElementById('savedDesignSideToggleGroup');

    if (savedContainer) savedContainer.style.display = 'block';
    if (previewBox) previewBox.innerHTML = frontSvg || '';
    if (toggleGroup) toggleGroup.style.display = (isDoubleSided && backSvg) ? 'inline-flex' : 'none';

    window.savedFrontSvg = frontSvg;
    window.savedBackSvg = backSvg;
    document.getElementById('selectedDesignSvg').value = frontSvg;
    if (backSvg) document.getElementById('selectedDesignBackSvg').value = backSvg;
};

window.showSavedDesignSide = function(side) {
    const previewBox = document.getElementById('savedDesignPreviewBox');
    const btnFront = document.getElementById('btnPreviewSideFront');
    const btnBack = document.getElementById('btnPreviewSideBack');

    if (side === 'front') {
        btnFront.className = 'btn btn-sm btn-primary active';
        btnBack.className = 'btn btn-sm btn-outline-secondary';
        previewBox.innerHTML = window.savedFrontSvg || '';
    } else {
        btnFront.className = 'btn btn-sm btn-outline-secondary';
        btnBack.className = 'btn btn-sm btn-primary active';
        previewBox.innerHTML = window.savedBackSvg || '';
    }
};

window.resetDesignSelection = function() {
    document.getElementById('designSavedContainer').style.display = 'none';
    document.getElementById('selectedDesignSvg').value = '';
    document.getElementById('selectedDesignBackSvg').value = '';
};

document.addEventListener('DOMContentLoaded', function() {
    // 4'lü Tasarım Sekmeleri Geçişi
    const navButtons = document.querySelectorAll('.design-nav-btn');
    const tabPanes = document.querySelectorAll('.design-tab-pane');

    navButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            navButtons.forEach(b => b.classList.remove('active'));
            tabPanes.forEach(p => p.style.display = 'none');
            
            this.classList.add('active');
            const target = document.getElementById(this.dataset.tab);
            if (target) target.style.display = 'block';

            if (this.dataset.tab === 'tabTemplates' && window.CanvaTemplatesEngine) {
                CanvaTemplatesEngine.renderTemplateGrid('mainSectorTemplatesGrid', 'all');
            }
        });
    });

    if (window.CanvaTemplatesEngine) {
        CanvaTemplatesEngine.renderTemplateGrid('mainSectorTemplatesGrid', 'all');
    }

    calculatePrice();
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
