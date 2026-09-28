<?php
require_once __DIR__ . '/config/config.php';

$slug = $_GET['slug'] ?? '';
$productModel = new Product();
$product = $productModel->getBySlug($slug);

if (!$product) {
    Helper::setFlash('danger', 'Aradığınız ürün bulunamadı veya yayından kaldırılmış.');
    header("Location: " . SITE_URL . "/category.php");
    exit;
}

$industries = $productModel->getIndustries();
$recommendedProducts = $productModel->getRecommendedProductsByIndustry('genel-kurumsal', $product['id'], 4);

$pageTitle = $product['name'] . ' – Online Matbaa & Fiyat Hesaplayıcı';
$pageDesc = $product['short_description'] ?? 'En uygun fiyatlarla kaliteli ' . $product['name'] . ' baskı siparişi verin.';

// Seçili gelen şablon parametresi varsa
$selectedTplId = (int)($_GET['tpl'] ?? 0);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-2 py-md-3 pb-lg-4 pb-5 mb-4">
    
    <!-- Breadcrumb (Mobilde gizli) -->
    <nav aria-label="breadcrumb" class="mb-2 mb-md-3 d-none d-md-block">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/" class="text-decoration-none text-muted">Ana Sayfa</a></li>
            <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/category.php?slug=<?= $product['category_slug'] ?>" class="text-decoration-none text-muted"><?= htmlspecialchars($product['category_name'] ?? 'Kategori') ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name']) ?></li>
        </ol>
    </nav>

    <div class="row g-3 g-lg-4">
        
        <!-- Sol: Ürün Görselleri, Canlı Dinamik Mockup & Teknik Güvenceler -->
        <div class="col-lg-5">
            <div class="apple-card p-3 sticky-top" style="top: 85px; z-index: 10;">
                
                <div class="product-top-feature-chips mb-2 d-none d-lg-flex flex-wrap gap-1">
                    <?php if ($product['is_urgent']): ?>
                        <span class="badge bg-danger text-white fw-bold rounded-pill px-2 py-1" style="font-size: 11px;"><i class="bi bi-lightning-fill"></i> 24 Saatte Acil Baskı</span>
                    <?php endif; ?>
                    <span class="badge bg-info-subtle text-info fw-bold rounded-pill px-2 py-1" style="font-size: 11px;">
                        <i class="bi bi-droplet-fill me-1"></i> Su & Nem Korumalı
                    </span>
                    <?php if ($product['allow_online_editor']): ?>
                        <span class="badge bg-primary-subtle text-primary fw-bold rounded-pill px-2 py-1" style="font-size: 11px;"><i class="bi bi-palette-fill"></i> Online Vektörel Tasarımlı</span>
                    <?php endif; ?>
                </div>

                <!-- ========================================================================= -->
                <!-- 🎨 CANLI DİNAMİK MOCKUP SAHNESİ & 3D DOKU SİMÜLATÖRÜ -->
                <!-- ========================================================================= -->
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
                        <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 py-1 shadow d-flex align-items-center gap-1" onclick="PackageShowcase.playCinematicVideo()" style="font-size: 11px; pointer-events: auto; background: linear-gradient(135deg, #e11d48, #f43f5e); border: none;">
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
                                    <img src="<?= SITE_URL ?>/assets/img/logo.svg" alt="TamBaskı" style="height: 22px; max-width: 140px; object-fit: contain; transition: filter 0.3s ease;" id="showcaseLogoImg">
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

                <!-- 📸 Çoklu Fotoğraf & Video Küçük Resim Şeridi (Varsa) -->
                <div class="d-flex gap-1 overflow-x-auto pb-2 mb-2 align-items-center" id="prodMediaStrip" style="white-space: nowrap;">
                    <!-- 3D Mockup Butonu -->
                    <button type="button" class="btn btn-sm btn-outline-primary active rounded-3 p-1 px-2 text-nowrap media-thumb-btn" onclick="showMediaMockup(this)" title="3D Mockup Önizleme" style="font-size: 11px; height: 42px;">
                        <i class="bi bi-layers-half me-1"></i> Mockup
                    </button>

                    <!-- Fotoğraflar (WebP / Ultra HD) -->
                    <?php 
                    $hasGallery = !empty($product['gallery_array']);
                    if ($hasGallery): 
                        foreach ($product['gallery_array'] as $gIdx => $gImg): 
                            $fullImgUrl = str_starts_with($gImg, 'http') ? $gImg : SITE_URL . '/' . $gImg;
                        ?>
                            <button type="button" class="btn btn-sm btn-light border rounded-3 p-0 overflow-hidden media-thumb-btn" onclick="showMediaPhoto('<?= $fullImgUrl ?>', this)" style="width: 42px; height: 42px; flex-shrink: 0;">
                                <img src="<?= $fullImgUrl ?>" alt="Fotoğraf <?= $gIdx + 1 ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            </button>
                        <?php endforeach; 
                    endif; ?>

                    <!-- 🎥 Gerçek 3D Tanıtım Videosu Butonu -->
                    <?php 
                    $fullVideoUrl = !empty($product['video_path']) ? (str_starts_with($product['video_path'], 'http') ? $product['video_path'] : SITE_URL . '/' . $product['video_path']) : SITE_URL . '/uploads/videos/tambaski_vip_showcase.mp4';
                    ?>
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-3 p-1 px-2 text-nowrap media-thumb-btn d-flex align-items-center gap-1 shadow-xs" onclick="showMediaVideo('<?= $fullVideoUrl ?>', this)" title="Gerçekçi 3D Altın Varak & Kalınlık Videosu" style="font-size: 11px; height: 42px; background: #fff;">
                        <i class="bi bi-play-circle-fill text-danger fs-6"></i> <span class="fw-bold">Video</span>
                    </button>
                </div>
                
                <h5 class="fw-bold mb-1" id="dynamicProdTitle"><?= htmlspecialchars($product['name']) ?></h5>
                <p class="text-muted small mb-2 d-none d-lg-block" id="dynamicProdDesc"><?= htmlspecialchars($product['short_description'] ?? '') ?></p>

                <!-- ========================================================================= -->
                <!-- 💧 DESKTOP: ROZETLER & TEKNİK TABLO (Mobilde Alttaki Akordiyona Alındı) -->
                <!-- ========================================================================= -->
                <div class="d-none d-lg-block">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-2 bg-light rounded-3 border d-flex align-items-center gap-2">
                                <span class="fs-4 text-info"><i class="bi bi-droplet-half"></i></span>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 11px;">Su & Nem Geçirmez</div>
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
                                    <div class="text-muted" style="font-size: 10px;">Ücretsiz grafik kontrol</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-4 small border mb-3">
                        <div class="fw-bold text-dark mb-2 pb-1 border-bottom d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-cpu me-1 text-primary"></i> Seçili Paket Özellikleri</span>
                            <span id="tablePkgName" class="badge bg-primary text-white">Ekonomik</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom text-muted">
                            <span>Kağıt Türü:</span>
                            <strong class="text-dark" id="specPaper">350 gr. Birinci Sınıf Kuşe</strong>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom text-muted">
                            <span>Yüzey Kaplama:</span>
                            <strong class="text-dark" id="specLamination">Mat Selefon (Su İtici)</strong>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom text-muted">
                            <span>Köşe Kesimi:</span>
                            <strong class="text-dark" id="specCorners">Standart Düz Kesim (90°)</strong>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom text-muted">
                            <span>Ekstra Efekt:</span>
                            <strong class="text-dark" id="specFinish">Standart Ofset Baskı</strong>
                        </div>
                        <div class="d-flex justify-content-between pt-1 text-muted">
                            <span>Sıvı / Nem Dayanımı:</span>
                            <strong class="text-success" id="specWaterproof"><i class="bi bi-check-circle-fill me-1"></i> %100 Su & Nem Korumalı</strong>
                        </div>
                    </div>

                    <div class="mt-2">
                        <h6 class="fw-bold border-bottom pb-2">Ürün Açıklaması</h6>
                        <div class="small text-muted" id="dynamicFullDesc">
                            <?= $product['full_description'] ?: '<p>Firmanızı en profesyonel şekilde temsil eden standart boyutlu (84x52 mm) mat selefon kaplamalı dayanıklı kartvizit.</p>' ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Sağ: Paket Seçimi (Ekonomik, Standart, Premium, VIP) + Adet + Tasarım -->
        <div class="col-lg-7">
            <div class="configurator-panel">
                
                <style>
                .package-grid {
                    display: grid;
                    grid-template-columns: repeat(4, 1fr);
                    gap: 10px;
                }
                .pkg-card {
                    background: #ffffff;
                    border: 2px solid #e5e5ea;
                    border-radius: 14px;
                    padding: 14px 10px;
                    cursor: pointer;
                    transition: all 0.2s ease;
                    position: relative;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    text-align: center;
                    min-height: 145px;
                    user-select: none;
                    box-shadow: 0 2px 6px rgba(0,0,0,0.03);
                }
                .pkg-card:hover {
                    border-color: #0071e3;
                    transform: translateY(-2px);
                    box-shadow: 0 6px 16px rgba(0,113,227,0.12);
                }
                .pkg-card.active {
                    border-color: #0071e3 !important;
                    background: linear-gradient(180deg, #f0f7ff 0%, #ffffff 100%) !important;
                    box-shadow: 0 6px 20px rgba(0,113,227,0.18) !important;
                }
                .pkg-card .pkg-icon {
                    font-size: 24px;
                    margin-bottom: 6px;
                    line-height: 1;
                }
                .pkg-card .pkg-title {
                    font-size: 15px;
                    font-weight: 700;
                    color: #1d1d1f;
                    margin-bottom: 3px;
                }
                .pkg-card .pkg-desc {
                    font-size: 11px;
                    color: #6e6e73;
                    line-height: 1.25;
                    margin-bottom: 8px;
                    flex-grow: 1;
                }
                .pkg-card .pkg-badge {
                    font-size: 10px;
                    font-weight: 600;
                    padding: 3px 8px;
                    border-radius: 999px;
                    display: inline-block;
                }
                .pkg-ribbon {
                    position: absolute;
                    top: 6px;
                    right: 6px;
                    background: linear-gradient(135deg, #e60087, #ff5e3a);
                    color: #fff;
                    font-size: 9px;
                    font-weight: 700;
                    padding: 2px 7px;
                    border-radius: 999px;
                    letter-spacing: 0.3px;
                    box-shadow: 0 2px 6px rgba(230,0,135,0.3);
                }
                .pkg-ribbon.ribbon-vip {
                    background: linear-gradient(135deg, #f59e0b, #d97706);
                    box-shadow: 0 2px 6px rgba(245,158,11,0.3);
                }
                .qty-grid {
                    display: grid;
                    grid-template-columns: repeat(5, 1fr);
                    gap: 8px;
                }
                .qty-box {
                    background: #f5f5f7;
                    border: 2px solid #e5e5ea;
                    border-radius: 12px;
                    padding: 8px 4px;
                    text-align: center;
                    cursor: pointer;
                    transition: all 0.2s ease;
                    position: relative;
                }
                .qty-box:hover {
                    border-color: #0071e3;
                    background: #fff;
                }
                .qty-box.active {
                    border-color: #0071e3 !important;
                    background: #ffffff !important;
                    box-shadow: 0 4px 12px rgba(0,113,227,0.15) !important;
                }

                /* 📱 Mobil Ultra Kompakt İyileştirmeleri */
                @media (max-width: 991px) {
                    .mockup-stage-box {
                        min-height: 125px !important;
                        padding: 6px 0 !important;
                        margin-bottom: 6px !important;
                    }
                    .mockup-card-body {
                        width: 210px !important;
                        height: 110px !important;
                        padding: 10px !important;
                        border-radius: 6px !important;
                    }
                    .mockup-card-body .fs-6 {
                        font-size: 11px !important;
                    }
                    .mockup-card-body div {
                        font-size: 9px !important;
                    }
                    .mockup-card-body span {
                        font-size: 8px !important;
                    }
                    
                    .package-grid {
                        display: grid;
                        grid-template-columns: repeat(4, 1fr);
                        gap: 6px;
                        margin-bottom: 6px !important;
                    }
                    .pkg-card {
                        min-height: 52px;
                        padding: 6px 2px;
                        border-radius: 10px;
                        justify-content: center;
                    }
                    .pkg-card .pkg-icon {
                        font-size: 16px;
                        margin-bottom: 2px;
                    }
                    .pkg-card .pkg-title {
                        font-size: 11px;
                        font-weight: 700;
                        line-height: 1.1;
                        margin-bottom: 0;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        max-width: 100%;
                    }
                    .pkg-card .pkg-desc,
                    .pkg-card .pkg-badge,
                    .pkg-card .pkg-ribbon {
                        display: none !important;
                    }

                    .qty-grid {
                        display: grid;
                        grid-template-columns: repeat(3, 1fr);
                        gap: 6px;
                        margin-bottom: 6px !important;
                    }
                    .qty-box {
                        padding: 5px 2px;
                        border-radius: 8px;
                    }
                    .qty-box .fs-6 {
                        font-size: 13px !important;
                        font-weight: 700;
                    }
                    .qty-box .text-muted {
                        font-size: 10px !important;
                    }

                    .design-choice-box {
                        padding: 12px 10px !important;
                    }
                    .dropzone-box {
                        padding: 14px 10px !important;
                    }
                    .dropzone-icon {
                        font-size: 26px !important;
                        margin-bottom: 4px !important;
                    }
                    
                    body {
                        padding-bottom: 75px;
                    }
                }

                /* 🌟 4'lü Tasarım Yöntemi Butonları (Mobil 2x2, Masaüstü 4 Yan Yana) */
                .design-nav-pills {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    background: #f1f5f9;
                    padding: 6px;
                    border-radius: 14px;
                    gap: 6px;
                    margin-bottom: 14px;
                }
                @media (min-width: 992px) {
                    .design-nav-pills {
                        grid-template-columns: repeat(4, 1fr);
                    }
                }
                .design-nav-btn {
                    border: 1.5px solid transparent;
                    background: #ffffff;
                    padding: 10px 6px;
                    font-size: 12px;
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
                    line-height: 1.15;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
                }
                .design-nav-btn i {
                    font-size: 17px;
                }
                .design-nav-btn:hover {
                    color: #0284c7;
                    border-color: #cbd5e1;
                    transform: translateY(-1px);
                }
                .design-nav-btn.active {
                    background: #ffffff;
                    color: #0284c7;
                    font-weight: 700;
                    border-color: #0284c7;
                    box-shadow: 0 3px 10px rgba(2,132,199,0.18);
                }
                .design-nav-btn.btn-ai-tab {
                    background: linear-gradient(135deg, #fdf4ff 0%, #fae8ff 100%);
                    border-color: #f0abfc;
                    color: #9333ea;
                }
                .design-nav-btn.btn-ai-tab.active {
                    background: #ffffff;
                    color: #9333ea;
                    border-color: #a855f7;
                    box-shadow: 0 4px 12px rgba(168,85,247,0.25);
                }
                .ai-vector-result-card {
                    border: 2px solid #e2e8f0;
                    border-radius: 12px;
                    transition: all 0.2s ease;
                    background: #fff;
                    overflow: hidden;
                }
                .ai-vector-result-card:hover, .ai-vector-result-card.selected {
                    border-color: #9333ea;
                    box-shadow: 0 6px 18px rgba(147,51,234,0.15);
                    transform: translateY(-2px);
                }
                /* 🌟 3D Mockup Canlı Kart Stilleri */
                .mockup-3d-scene {
                    perspective: 1200px;
                }
                .mockup-3d-card {
                    transform-style: preserve-3d;
                    transition: transform 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
                }
                .mockup-3d-card.flipped {
                    transform: rotateY(180deg) !important;
                }
                .mockup-face {
                    backface-visibility: hidden;
                    -webkit-backface-visibility: hidden;
                }
                .mockup-face svg,
                #savedDesignPreviewCard svg,
                #savedDesignPreviewBox svg {
                    width: 100% !important;
                    height: 100% !important;
                    display: block !important;
                }

                /* 🎨 TamBaskı Tasarım Editörü - Üst Başlık & Buton Kurumsal Mimarisi */
                #canvaStudioModal .modal-header {
                    min-height: 52px !important;
                    background: #0f172a !important;
                    padding: 8px 16px !important;
                    border-bottom: 1px solid rgba(255,255,255,0.08) !important;
                }

                #canvaSideSwitchGroup {
                    display: inline-flex !important;
                    align-items: center !important;
                    background: rgba(0, 0, 0, 0.45) !important;
                    padding: 3px !important;
                    border-radius: 999px !important;
                    border: 1px solid rgba(255, 255, 255, 0.15) !important;
                    flex-shrink: 0 !important;
                }

                #canvaSideSwitchGroup .btn {
                    height: 30px !important;
                    padding: 0 14px !important;
                    font-size: 11.5px !important;
                    font-weight: 600 !important;
                    border-radius: 999px !important;
                    white-space: nowrap !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    border: none !important;
                    line-height: 1 !important;
                    transition: all 0.2s ease !important;
                }

                #canvaBtnSave {
                    height: 32px !important;
                    padding: 0 14px !important;
                    font-size: 12px !important;
                    font-weight: 700 !important;
                    border-radius: 999px !important;
                    white-space: nowrap !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    background: #10b981 !important;
                    border: none !important;
                    color: #fff !important;
                }

                #canvaBtn3d {
                    height: 32px !important;
                    border-radius: 999px !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    white-space: nowrap !important;
                }

                /* 📱 TamBaskı Tasarım Editörü - Mobil Boyut Optimizasyonu */
                @media (max-width: 767.98px) {
                    #canvaStudioModal .modal-header {
                        padding: 6px 8px !important;
                        min-height: 48px !important;
                        height: 48px !important;
                        gap: 4px !important;
                        flex-wrap: nowrap !important;
                    }

                    .canva-header-left {
                        gap: 4px !important;
                        flex-shrink: 0 !important;
                    }

                    #canvaBtnBack {
                        width: 32px !important;
                        height: 32px !important;
                        padding: 0 !important;
                        display: inline-flex !important;
                        align-items: center !important;
                        justify-content: center !important;
                        border-radius: 50% !important;
                        background: rgba(255,255,255,0.12) !important;
                        border: 1px solid rgba(255,255,255,0.15) !important;
                        color: #fff !important;
                    }

                    #canvaSideSwitchGroup {
                        padding: 2px !important;
                    }

                    #canvaSideSwitchGroup .btn {
                        height: 28px !important;
                        padding: 0 8px !important;
                        font-size: 10.5px !important;
                        gap: 2px !important;
                    }

                    .canva-header-actions {
                        gap: 4px !important;
                        flex-shrink: 0 !important;
                    }

                    #canvaBtn3d {
                        width: 30px !important;
                        height: 30px !important;
                        padding: 0 !important;
                        border-radius: 50% !important;
                    }

                    #canvaBtnSave {
                        height: 30px !important;
                        padding: 0 10px !important;
                        font-size: 11.5px !important;
                    }

                    #canvaStudioModal .modal-body {
                        display: flex !important;
                        flex-direction: column !important;
                        height: calc(100vh - 48px) !important;
                        position: relative !important;
                        overflow: hidden !important;
                        background: #f1f5f9 !important;
                    }
                    
                    /* Tuval Alanı: Ekranın merkezinde odak noktası */
                    .canva-workspace {
                        flex: 1 1 auto !important;
                        height: 100% !important;
                        min-height: 0 !important;
                        overflow: hidden !important;
                        position: relative !important;
                        background: #e2e8f0 !important;
                    }
                    #canvaCanvasWrapper {
                        padding: 6px !important;
                        height: 100% !important;
                        width: 100% !important;
                        display: flex !important;
                        align-items: center !important;
                        justify-content: center !important;
                    }

                    /* Mobilde Alt Yüzen Dock Bar */
                    .canva-sidebar {
                        position: absolute !important;
                        bottom: 0 !important;
                        left: 0 !important;
                        right: 0 !important;
                        width: 100% !important;
                        max-width: 100% !important;
                        z-index: 50 !important;
                        background: transparent !important;
                        border: none !important;
                        pointer-events: none !important;
                    }

                    #canvaSidebarTabs {
                        pointer-events: auto !important;
                        display: flex !important;
                        flex-direction: row !important;
                        justify-content: space-around !important;
                        background: #ffffff !important;
                        border-top: 1px solid #cbd5e1 !important;
                        box-shadow: 0 -4px 20px rgba(0,0,0,0.15) !important;
                        padding: 4px 6px !important;
                        margin: 0 !important;
                    }

                    #canvaSidebarTabs .nav-item {
                        flex: 1 !important;
                        text-align: center !important;
                    }

                    #canvaSidebarTabs .nav-link {
                        padding: 4px 2px !important;
                        font-size: 10px !important;
                        display: flex !important;
                        flex-direction: column !important;
                        align-items: center !important;
                        gap: 2px !important;
                        color: #64748b !important;
                        border: none !important;
                        border-radius: 8px !important;
                        background: transparent !important;
                    }

                    #canvaSidebarTabs .nav-link.active {
                        color: #0071e3 !important;
                        background: #eff6ff !important;
                        font-weight: 700 !important;
                    }

                    #canvaSidebarTabs .nav-link i {
                        font-size: 20px !important;
                    }

                    /* Mobilde Açılır Tab Çekmecesi (Slide-Up Bottom Sheet) */
                    .canva-sidebar .tab-content {
                        pointer-events: auto !important;
                        display: none;
                        max-height: 58vh !important;
                        height: 58vh !important;
                        overflow-y: auto !important;
                        background: #ffffff !important;
                        border-top-left-radius: 20px !important;
                        border-top-right-radius: 20px !important;
                        box-shadow: 0 -12px 36px rgba(0,0,0,0.22) !important;
                        border: 1px solid #cbd5e1 !important;
                        border-bottom: none !important;
                        padding: 12px 14px 16px 14px !important;
                        -webkit-overflow-scrolling: touch;
                    }

                    .canva-sidebar .tab-content.mobile-drawer-open {
                        display: block !important;
                    }

                    /* İnce Ayar Çekmecesi Mobilde Tam Sayfa Modal Gibi Açılır */
                    #canvaStudioFineTuneDrawer {
                        position: absolute !important;
                        top: 0 !important;
                        left: 0 !important;
                        right: 0 !important;
                        bottom: 0 !important;
                        max-height: 100% !important;
                        height: 100% !important;
                        z-index: 60 !important;
                        overflow-y: auto !important;
                        background: #f8fafc !important;
                    }

                    #canvaPropertiesBar {
                        padding: 4px 8px !important;
                        overflow-x: auto !important;
                        flex-wrap: nowrap !important;
                        -webkit-overflow-scrolling: touch;
                    }
                    #canvaPropertiesBar::-webkit-scrollbar {
                        display: none;
                    }
                }
                </style>

                <div class="d-flex justify-content-between align-items-baseline mb-2">
                    <div>
                        <h5 class="fw-bold mb-0">1. Baskı Paketini Seçin</h5>
                        <span class="text-muted small d-none d-md-inline">İhtiyacınıza en uygun hazır paketi belirleyin</span>
                    </div>
                </div>

                <form id="printConfigForm" data-product-id="<?= $product['id'] ?>" action="<?= SITE_URL ?>/cart.php" method="POST">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    
                    <!-- Tasarım Verisi Gizli Girdileri -->
                    <input type="hidden" name="design_type" id="designTypeInput" value="none">
                    <input type="hidden" name="design_file" id="selectedDesignFile" value="">
                    <input type="hidden" name="design_svg" id="selectedDesignSvg" value="">
                    <input type="hidden" name="design_back_svg" id="selectedDesignBackSvg" value="">

                    <!-- ========================================================================= -->
                    <!-- 📦 4'LÜ HAZIR PAKET KARTLARI (Admin Ayarlarına Göre Dinamik) -->
                    <!-- ========================================================================= -->
                    <?php
                    $presets = !empty($product['package_presets']) ? json_decode($product['package_presets'], true) : [];
                    $allPackages = [
                        'ekonomik' => [
                            'key'         => 'ekonomik',
                            'title'       => 'Ekonomik',
                            'icon'        => 'bi-wallet2',
                            'icon_class'  => 'text-secondary',
                            'desc'        => $presets['ekonomik']['desc'] ?? '350gr Kuşe, Mat Selefon, Düz Kesim',
                            'badge'       => 'En Uygun Fiyat',
                            'badge_class' => 'bg-success-subtle text-success',
                            'active'      => isset($presets['ekonomik']) ? (!empty($presets['ekonomik']['active'])) : true
                        ],
                        'standart' => [
                            'key'         => 'standart',
                            'title'       => 'Standart',
                            'ribbon'      => 'Popüler',
                            'ribbon_class'=> '',
                            'icon'        => 'bi-award-fill',
                            'icon_class'  => 'text-primary',
                            'desc'        => $presets['standart']['desc'] ?? '350gr Kuşe, Çift Taraf Mat, Oval Köşe',
                            'badge'       => 'En Çok Satan',
                            'badge_class' => 'bg-primary-subtle text-primary',
                            'active'      => isset($presets['standart']) ? (!empty($presets['standart']['active'])) : true
                        ],
                        'premium' => [
                            'key'         => 'premium',
                            'title'       => 'Premium',
                            'icon'        => 'bi-stars',
                            'icon_style'  => 'color: #8b5cf6;',
                            'desc'        => $presets['premium']['desc'] ?? 'Soft-Touch Kadife Selefon & Kabartma Lak',
                            'badge'       => 'Lüks Doku',
                            'badge_style' => 'background: #8b5cf6; color: #fff;',
                            'active'      => isset($presets['premium']) ? (!empty($presets['premium']['active'])) : true
                        ],
                        'vip' => [
                            'key'         => 'vip',
                            'title'       => 'VIP Prestij',
                            'ribbon'      => 'VIP',
                            'ribbon_class'=> 'ribbon-vip',
                            'icon'        => 'bi-gem',
                            'icon_class'  => 'text-warning',
                            'desc'        => $presets['vip']['desc'] ?? 'Tuale Fantezi / Altın Varak Yaldız',
                            'badge'       => 'Maksimum Prestij',
                            'badge_class' => 'bg-warning-subtle text-dark',
                            'active'      => isset($presets['vip']) ? (!empty($presets['vip']['active'])) : true
                        ]
                    ];

                    $activePackages = [];
                    foreach ($allPackages as $k => $p) {
                        if (!empty($p['active'])) {
                            $activePackages[$k] = $p;
                        }
                    }
                    if (empty($activePackages)) {
                        $activePackages = $allPackages;
                    }
                    $pkgKeys = array_keys($activePackages);
                    $firstPkgKey = $pkgKeys[0] ?? 'standart';
                    ?>
                    <div class="package-grid mb-2 mb-lg-4">
                        <?php foreach ($activePackages as $pKey => $pkg): ?>
                            <div class="pkg-card <?= $pKey === $firstPkgKey ? 'active' : '' ?>" id="card_pkg_<?= $pKey ?>" onclick="selectPackage('<?= $pKey ?>', this)">
                                <?php if (!empty($pkg['ribbon'])): ?>
                                    <span class="pkg-ribbon <?= $pkg['ribbon_class'] ?? '' ?>"><?= htmlspecialchars($pkg['ribbon']) ?></span>
                                <?php endif; ?>
                                <input type="radio" name="selected_package" value="<?= $pKey ?>" <?= $pKey === $firstPkgKey ? 'checked' : '' ?> style="display:none;">
                                <span class="pkg-icon <?= $pkg['icon_class'] ?? '' ?>" <?= !empty($pkg['icon_style']) ? 'style="' . $pkg['icon_style'] . '"' : '' ?>>
                                    <i class="bi <?= $pkg['icon'] ?>"></i>
                                </span>
                                <div class="pkg-title"><?= htmlspecialchars($pkg['title']) ?></div>
                                <div class="pkg-desc"><?= htmlspecialchars($pkg['desc']) ?></div>
                                <span class="pkg-badge <?= $pkg['badge_class'] ?? '' ?>" <?= !empty($pkg['badge_style']) ? 'style="' . $pkg['badge_style'] . '"' : '' ?>><?= htmlspecialchars($pkg['badge']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- 📱 Mobilde 1 Satırlık Dinamik Paket Özeti -->
                    <div id="mobilePkgDesc" class="d-lg-none text-center small text-primary bg-primary-subtle border border-primary-subtle rounded-pill py-1 px-3 mb-3 fw-semibold shadow-xs" style="font-size: 11px;">
                        💧 Mat Selefonlu & Suya Dayanıklı
                    </div>

                    <!-- ========================================================================= -->
                    <!-- 2. ADET KADEMELERİ (TİRAJ SEÇİMİ & ÖZEL ADET) -->
                    <!-- ========================================================================= -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="option-group-title mb-0">
                                <span><i class="bi bi-layers me-1 text-primary"></i> 2. Baskı Adedi</span>
                            </label>
                            <span class="text-muted small">Tiraj arttıkça birim fiyat %50'ye varan oranda düşer</span>
                        </div>
                        
                        <div class="qty-grid mb-2">
                            <?php if (!empty($product['quantity_tiers'])): ?>
                                <?php foreach ($product['quantity_tiers'] as $tIdx => $tier): ?>
                                    <div class="qty-box <?= $tIdx === 0 ? 'active' : '' ?>" id="qty_box_<?= $tier['quantity'] ?>" onclick="selectQuantity('<?= $tier['quantity'] ?>', this)">
                                        <?php if ((float)$tier['discount_percent'] > 0): ?>
                                            <span class="position-absolute top-0 end-0 translate-middle-y badge bg-success rounded-pill" style="font-size: 9px; right: 4px;">%<?= (int)$tier['discount_percent'] ?> İndirim</span>
                                        <?php endif; ?>
                                        <input type="radio" name="quantity" value="<?= $tier['quantity'] ?>" <?= $tIdx === 0 ? 'checked' : '' ?> style="display:none;">
                                        <div class="fw-bold text-dark fs-6"><?= number_format($tier['quantity'], 0, '', '.') ?></div>
                                        <div class="text-muted" style="font-size: 11px;">Adet</div>
                                    </div>
                                <?php endforeach; ?>
                                
                                <!-- ✏️ Özel Adet Kutusu -->
                                <div class="qty-box" id="qty_box_custom" onclick="activateCustomQty(this)" style="border-style: dashed;">
                                    <span class="position-absolute top-0 end-0 translate-middle-y badge bg-info rounded-pill" style="font-size: 9px; right: 4px;">Özel</span>
                                    <input type="radio" name="quantity" id="customQtyRadio" value="500" style="display:none;">
                                    <div class="fw-bold text-primary fs-6"><i class="bi bi-pencil-square"></i></div>
                                    <div class="text-muted" style="font-size: 11px;">Özel Adet</div>
                                </div>
                            <?php else: ?>
                                <div style="grid-column: span 5;">
                                    <input type="number" name="quantity" class="form-control" value="1000" min="1">
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Özel Adet Manuel Giriş Alanı (Gerektiğinde Açılır) -->
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

                        <!-- 💡 Avantajlı Üretim & Akıllı Adet Tavsiyesi -->
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

                    <!-- Standart Ölçü Sabitleri (İnce ayar kaldırıldı, standart paketler devrede) -->
                    <input type="hidden" name="size_type" value="standard">
                    <input type="hidden" name="custom_width" id="customWidth" value="<?= $product['standard_width'] ?? 8.4 ?>">
                    <input type="hidden" name="custom_height" id="customHeight" value="<?= $product['standard_height'] ?? 5.2 ?>">

                    <!-- ========================================================================= -->
                    <!-- 3. TASARIM TERCİHİ (1. Kendin Tasarla | 2. Hazır Şablon | 3. Dosya Yükle | 4. Grafik Desteği) -->
                    <!-- ========================================================================= -->
                    <div class="design-choice-box" id="designSectionBox">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="fw-bold small mb-0 text-dark" id="designSectionTitle"><i class="bi bi-palette text-primary me-1"></i> 3. Tasarım Yöntemini Seçin</label>
                            <span class="badge bg-primary-subtle text-primary shadow-2xs" id="designSectionStatusBadge" style="font-size: 10px;">
                                <i class="bi bi-stars me-1"></i> İnteraktif Tasarım &amp; Şablonlar
                            </span>
                        </div>
                        
                        <!-- 1. HENÜZ TASARIM OLUŞTURULMAMIŞKEN GÖRÜNEN SEÇİCİ PANEL -->
                        <div id="designInitialSelector">
                            <!-- 4'lü Tasarım Sekmeleri -->
                            <div class="design-nav-pills">
                                <button type="button" class="design-nav-btn active btn-canva-tab" data-tab="tabCanva">
                                    <i class="bi bi-palette-fill text-danger" style="color: #e11d48;"></i>
                                    <span class="fw-bold">1. Kendin Tasarla</span>
                                </button>

                                <button type="button" class="design-nav-btn" data-tab="tabTemplate">
                                    <i class="bi bi-grid-3x3-gap-fill text-warning"></i>
                                    <span>2. Hazır Şablon</span>
                                </button>

                                <button type="button" class="design-nav-btn" data-tab="tabUpload">
                                    <i class="bi bi-cloud-arrow-up-fill text-info"></i>
                                    <span>3. Dosya Yükle</span>
                                </button>

                                <button type="button" class="design-nav-btn" data-tab="tabSupport">
                                    <i class="bi bi-whatsapp text-success"></i>
                                    <span>4. Grafik Desteği</span>
                                </button>
                            </div>

                            <!-- 🎨 TAB 1: KENDİN TASARLA (TEK TIKLA BAŞLA) -->
                            <div id="tabCanva" class="design-tab-pane">
                                <div class="p-3 rounded-4 border bg-white mb-2 shadow-2xs" style="border-color: #fecdd3 !important; background: linear-gradient(180deg, #fff1f2 0%, #ffffff 100%) !important;">
                                    <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom">
                                        <div class="rounded-3 p-2 text-white shadow-xs" style="background: linear-gradient(135deg, #f15a24, #ea580c); width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-brush-fill fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small lh-1">TamBaskı Online Vektör Stüdyosu</div>
                                            <span class="text-muted" style="font-size: 10.5px;">Tarayıcınızda sıfırdan logonuzu ekleyin, metinleri düzenleyin, QR kod oluşturun (300 DPI)</span>
                                        </div>
                                    </div>
                                    <p class="small text-muted mb-3" style="font-size: 11.5px; line-height: 1.4;">
                                        Hiçbir grafik programına gerek olmadan, doğrudan tarayıcınızda 1 tıkla tasarlamaya başlayın. CMYK renk modunda, milimetrik baskı ve taşma payı garantilidir.
                                    </p>
                                    
                                    <button type="button" class="btn btn-danger w-100 py-3 fw-bold rounded-pill shadow-xs d-flex align-items-center justify-content-center gap-2" onclick="openCanvaStudio()" style="background: linear-gradient(135deg, #f15a24, #e11d48); border: none;">
                                        <i class="bi bi-palette-fill fs-5"></i>
                                        <span class="fs-6">🎨 Tasarım Editörünü Aç (Tek Tıkla Başla)</span>
                                    </button>

                                    <div class="mt-3 d-flex flex-wrap items-center justify-content-around gap-2 text-muted" style="font-size: 11px;">
                                        <span><i class="bi bi-check2-circle text-success me-1"></i>300 DPI Vektör Çıktı</span>
                                        <span><i class="bi bi-check2-circle text-success me-1"></i>Ücretsiz QR Kod Motoru</span>
                                        <span><i class="bi bi-check2-circle text-success me-1"></i>Tek/Çift Yön Tasarım</span>
                                    </div>
                                </div>
                            </div>

                            <!-- 📑 TAB 2: HAZIR ŞABLON KULLAN (MESLEK / SEKTÖR SEÇİMİ) -->
                            <div id="tabTemplate" class="design-tab-pane" style="display: none;">
                                <div class="p-3 rounded-4 border bg-white mb-2 shadow-2xs" style="border-color: #fef08a !important; background: linear-gradient(180deg, #fefce8 0%, #ffffff 100%) !important;">
                                    <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom">
                                        <div class="rounded-3 p-2 text-white shadow-xs" style="background: linear-gradient(135deg, #eab308, #ca8a04); width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-briefcase-fill fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small lh-1">Meslek &amp; Sektör Şablonları</div>
                                            <span class="text-muted" style="font-size: 10.5px;">Mesleğinizi seçin, sektörünüze uygun hazır şablonlarla anında başlayın</span>
                                        </div>
                                    </div>

                                    <!-- Sektör Seçici Dropdown -->
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-dark mb-1">
                                            <i class="bi bi-filter-circle text-primary me-1"></i> Sektör / Meslek Filtresi:
                                        </label>
                                        <select class="form-select form-select-sm rounded-3 fw-semibold border-secondary" id="industrySelectDropdown" onchange="onIndustrySelectChange(this.value)">
                                            <option value="all">🌟 Tüm Meslekler &amp; Sektörler</option>
                                            <?php if (!empty($industries)): ?>
                                                <?php foreach ($industries as $ind): ?>
                                                    <option value="<?= htmlspecialchars($ind['slug']) ?>"><?= htmlspecialchars($ind['name']) ?></option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="hukuk-avukatlik">⚖️ Hukuk, Avukatlık &amp; Danışmanlık</option>
                                                <option value="saglik-klinik">🩺 Sağlık, Tıp, Doktor &amp; Diş Hekimi</option>
                                                <option value="gayrimenkul-emlak">🏠 Gayrimenkul, Emlak &amp; Değerleme</option>
                                                <option value="insaat-mimarlik">🏗️ İnşaat, Mimarlık &amp; Mühendislik</option>
                                                <option value="restoran-kafe">☕ Restoran, Kafe, Fırın &amp; Gıda</option>
                                                <option value="guzellik-kuafor">✂️ Güzellik, Kuaför, Berber &amp; Spa</option>
                                                <option value="otomotiv-servis">🚗 Otomotiv, Araç Servisi &amp; Kiralama</option>
                                                <option value="finans-muhasebe">💼 Finans, Muhasebe &amp; Sigorta</option>
                                                <option value="teknoloji-yazilim">💻 Teknoloji, Yazılım, Ajans &amp; Medya</option>
                                                <option value="egitim-kurs">🎓 Eğitim, Kurs, Okul &amp; Akademi</option>
                                                <option value="genel-kurumsal">🏢 Genel Kurumsal &amp; Ticaret</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <!-- Şablonlar Listesi veya Boş Durum Bilgisi -->
                                    <div id="templatesListContainer">
                                        <?php if (!empty($product['templates'])): ?>
                                            <div class="row g-2 mb-2" id="templatesRow">
                                                <?php foreach ($product['templates'] as $tpl): ?>
                                                    <div class="col-6 template-card-wrapper" data-industry="<?= htmlspecialchars($tpl['industry_slug'] ?? 'all') ?>">
                                                        <div class="card p-2 h-100 border text-center shadow-2xs hover-lift">
                                                            <div class="template-preview-box mb-2" style="height: 90px; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #f8fafc; border-radius: 6px;">
                                                                <?= $tpl['default_svg'] ?? '<i class="bi bi-file-earmark-image fs-1 text-muted"></i>' ?>
                                                            </div>
                                                            <div class="small fw-bold text-truncate mb-2"><?= htmlspecialchars($tpl['title']) ?></div>
                                                            <button type="button" class="btn btn-sm btn-primary rounded-pill w-100 open-editor-btn" data-template='<?= htmlspecialchars(json_encode($tpl), ENT_QUOTES) ?>'>
                                                                <i class="bi bi-pencil-square me-1"></i> Düzenle
                                                            </button>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Şablonlar Henüz Eklenmemişse Görünecek Şık Bilgi Kartı -->
                                        <div id="noTemplatesNoticeBox" class="p-3 rounded-3 bg-white border text-center my-1" style="<?= empty($product['templates']) ? '' : 'display: none;' ?>">
                                            <div class="mb-2 text-warning"><i class="bi bi-folder-plus fs-3"></i></div>
                                            <h6 class="fw-bold text-dark mb-1" id="selectedIndustryNoticeTitle">Sektöre Özel Şablonlar</h6>
                                            <p class="text-muted small mb-3" style="font-size: 11px;">
                                                Bu meslek grubu için hazır şablonlarımız yükleme aşamasındadır. Dilerseniz 'Kendin Tasarla' editörümüzde logonuzu ve bilgilerinizi ekleyerek 1 dakikada sıfırdan oluşturabilirsiniz.
                                            </p>
                                            <button type="button" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-4 py-2 shadow-xs" onclick="openCanvaStudio()">
                                                <i class="bi bi-palette-fill me-1 text-danger"></i> Sıfırdan Tasarlamaya Başla
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 📤 TAB 3: DOSYA YÜKLE (BASKI DOSYAM HAZIR) -->
                            <div id="tabUpload" class="design-tab-pane" style="display: none;">
                                <div class="p-3 rounded-4 border bg-white mb-2 shadow-2xs" style="border-color: #bae6fd !important; background: linear-gradient(180deg, #f0f9ff 0%, #ffffff 100%) !important;">
                                    <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom">
                                        <div class="rounded-3 p-2 text-white shadow-xs" style="background: linear-gradient(135deg, #0284c7, #0369a1); width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-cloud-arrow-up-fill fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small lh-1">Baskı Dosyası Yükleme</div>
                                            <span class="text-muted" style="font-size: 10.5px;">Hazırladığınız PDF, AI, PSD veya yüksek çözünürlüklü baskı dosyanızı ekleyin</span>
                                        </div>
                                    </div>

                                    <!-- Sürükle Bırak Kutusu -->
                                    <div class="dropzone-box text-center p-4 rounded-3 border-2 border-dashed my-2 cursor-pointer" id="dropzoneBox" style="background: #f8fafc; border-color: #38bdf8; transition: all 0.2s ease;">
                                        <i class="bi bi-cloud-arrow-up text-primary dropzone-icon" style="font-size: 38px;"></i>
                                        <div class="fw-bold text-dark small mt-2">Baskı Dosyanızı Buraya Sürükleyin veya Tıklayın</div>
                                        <div class="text-muted" style="font-size: 11px;">PDF, AI, PSD, CDR, EPS, TIF, PNG, JPG (Maks. 100 MB)</div>
                                        <input type="file" id="fileUploadInput" style="display: none;" accept=".pdf,.ai,.psd,.cdr,.eps,.tif,.tiff,.jpg,.jpeg,.png">
                                        <div id="uploadStatus" class="mt-2 small fw-bold" style="display: none;"></div>
                                    </div>

                                    <div class="p-2 bg-light rounded-3 border small text-muted d-flex align-items-center gap-2" style="font-size: 11px;">
                                        <i class="bi bi-shield-check text-success fs-5"></i>
                                        <span>Yüklenen tüm dosyalar üretime alınmadan önce grafikerlerimiz tarafından CMYK renk, çözünürlük (300 DPI) ve kesim payı açısından <strong>ücretsiz kontrol edilir</strong>.</span>
                                    </div>
                                </div>
                            </div>

                            <!-- 💬 TAB 4: GRAFİK TASARIM DESTEĞİ & WHATSAPP DANIŞMA -->
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
                                        Tasarımınız hazır değilse veya özel bir çalışma istiyorsanız; logonuzu, metinlerinizi ve isteklerinizi WhatsApp üzerinden grafiker ekibimize doğrudan iletebilirsiniz. Baskı öncesi onayınız alınır.
                                    </p>

                                    <!-- WhatsApp Doğrudan İletişim Butonu -->
                                    <a href="https://wa.me/<?= Helper::getSetting('site_whatsapp', '905550000000') ?>?text=Merhaba,%20<?= urlencode($product['name']) ?>%20urunu%20icin%20grafik%20tasarim%20destegi%20almak%20istiyorum." target="_blank" class="btn btn-success w-100 py-2 fw-bold rounded-pill shadow-xs d-flex align-items-center justify-content-center gap-2 mb-3" style="background: #25D366; border: none;">
                                        <i class="bi bi-whatsapp fs-5"></i>
                                        <span>Grafikerimize WhatsApp'tan Yazın</span>
                                    </a>

                                    <?php if ($product['allow_design_service']): ?>
                                        <div class="p-2 bg-white rounded-3 border mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="includeDesignService" name="include_design_service" value="1">
                                                <label class="form-check-label small fw-bold text-dark" for="includeDesignService">
                                                    <i class="bi bi-magic text-warning me-1"></i> Profesyonel Grafik Tasarım Hizmetini Siparişime Ekle (+<?= Helper::formatPrice($product['design_service_price']) ?>)
                                                </label>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <textarea name="design_notes" id="designNotesArea" class="form-control form-control-sm" rows="2" placeholder="Sipariş notlarınız (Kartta yazacak isim, unvan, adres veya renk tercihleriniz)..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- 2. 🌟 TASARIM KAYDEDİLDİĞİNDE GÖSTERİLECEK AKILLI ÖNİZLEME & DÜZENLEME KARTI -->
                        <div id="designSavedContainer" style="display: none;">
                            <div class="p-3 rounded-4 border bg-white shadow-sm" style="border-color: #10b981 !important; background: linear-gradient(180deg, #ecfdf5 0%, #ffffff 100%) !important;">
                                
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-success rounded-pill px-3 py-1 shadow-xs" style="font-size: 11px;">
                                            <i class="bi bi-check-circle-fill me-1"></i> <span id="savedDesignBadgeLabel">Tasarımınız Kaydedildi (300 DPI Vektör)</span>
                                        </span>
                                    </div>
                                    <!-- Ön / Arka Yüz Sekmeleri (Çift Yön ise) -->
                                    <div class="btn-group btn-group-sm" id="savedDesignSideToggleGroup" style="display: none;">
                                        <button type="button" class="btn btn-sm btn-primary active" id="btnPreviewSideFront" onclick="showSavedDesignSide('front')">Ön Yüz</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnPreviewSideBack" onclick="showSavedDesignSide('back')">Arka Yüz</button>
                                    </div>
                                </div>

                                <!-- Canlı SVG Önizleme Alanı (Orantılı Kart Sahnesi) -->
                                <div class="saved-preview-stage p-3 rounded-3 border d-flex align-items-center justify-content-center mb-3" style="background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%); min-height: 220px;">
                                    <div id="savedDesignPreviewCard" class="shadow-lg rounded-2 overflow-hidden border bg-white position-relative" style="width: 100%; max-width: 360px; aspect-ratio: 84 / 52; display: flex; align-items: center; justify-content: center;">
                                        <div id="savedDesignPreviewBox" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;"></div>
                                    </div>
                                </div>

                                <!-- Aksiyon Butonları (Düzenle / 3D İncele / Değiştir) -->
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2 border-top">
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 py-1 fw-bold shadow-xs d-flex align-items-center gap-1" onclick="openCanvaStudio()">
                                            <i class="bi bi-pencil-square"></i> Tasarımı Düzenle
                                        </button>
                                        <button type="button" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-1 fw-semibold d-flex align-items-center gap-1" onclick="CanvaStudio.open3dMockup()">
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
                            <span id="calcSubtotal" class="fw-bold text-dark">0,00 ₺</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1 small text-muted">
                            <span>KDV (%<?= (int)$product['tax_rate'] ?>):</span>
                            <span id="calcTax" class="fw-bold text-dark">0,00 ₺</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2 small text-muted">
                            <span id="calcUnitPrice">Birim: 0,00 ₺</span>
                            <span class="badge bg-success">750 ₺ Üzeri Ücretsiz Kargo</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <span class="fw-bold">Toplam Tutar:</span>
                            <span id="calcGrandTotal" class="price-display-lg text-primary">0,00 ₺</span>
                        </div>
                    </div>

                    <button type="submit" class="btn-apple btn-apple-pink w-100 py-3 fs-6 fw-bold shadow">
                        <i class="bi bi-bag-plus-fill me-2"></i> Sepete Ekle ve Siparişi Başlat
                    </button>

                </form>

                <!-- ========================================================================= -->
                <!-- 📱 MOBİLDE AÇILABİLİR DETAYLI ÜRÜN BİLGİSİ & TEKNİK TABLO -->
                <!-- ========================================================================= -->
                <div class="accordion d-lg-none mt-3 mb-4" id="mobileProductDetailsAccordion">
                    <div class="accordion-item rounded-3 border overflow-hidden">
                        <h2 class="accordion-header" id="headingDetails">
                            <button class="accordion-button collapsed fw-bold py-2 bg-light small text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDetails" aria-expanded="false" aria-controls="collapseDetails">
                                <i class="bi bi-info-circle-fill text-primary me-2"></i> Detaylı Özellikler & Baskı Tablosu
                            </button>
                        </h2>
                        <div id="collapseDetails" class="accordion-collapse collapse" aria-labelledby="headingDetails" data-bs-parent="#mobileProductDetailsAccordion">
                            <div class="accordion-body p-3 small text-muted">
                                <div class="p-2 bg-light rounded-3 border mb-3">
                                    <div class="fw-bold text-dark mb-2 pb-1 border-bottom d-flex justify-content-between align-items-center">
                                        <span>Paket Özellikleri</span>
                                        <span id="mobileTablePkgName" class="badge bg-primary text-white">Ekonomik</span>
                                    </div>
                                    <div class="d-flex justify-content-between py-1 border-bottom">
                                        <span>Kağıt Türü:</span>
                                        <strong class="text-dark" id="mobileSpecPaper">350 gr Kuşe</strong>
                                    </div>
                                    <div class="d-flex justify-content-between py-1 border-bottom">
                                        <span>Kaplama:</span>
                                        <strong class="text-dark" id="mobileSpecLamination">Mat Selefon</strong>
                                    </div>
                                    <div class="d-flex justify-content-between py-1 border-bottom">
                                        <span>Kesim:</span>
                                        <strong class="text-dark" id="mobileSpecCorners">Standart Düz</strong>
                                    </div>
                                    <div class="d-flex justify-content-between pt-1">
                                        <span>Sıvı Dayanımı:</span>
                                        <strong class="text-success"><i class="bi bi-check-circle-fill me-1"></i> Su & Nem Korumalı</strong>
                                    </div>
                                </div>
                                <div id="mobileDynamicFullDesc">
                                    <?= $product['full_description'] ?: '<p>Firmanızı en profesyonel şekilde temsil eden standart boyutlu (84x52 mm) mat selefon kaplamalı dayanıklı kartvizit.</p>' ?>
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
<!-- 📱 SABİT ALT MOBİL AKSİYON BARI (Tek Tıkla Siparişi Tamamla) -->
<!-- ========================================================================= -->
<div class="mobile-sticky-bar d-lg-none fixed-bottom bg-white border-top shadow-lg py-2 px-3" style="z-index: 1040; backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.96) !important;">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <div class="text-muted fw-semibold text-truncate" style="font-size: 10px; max-width: 140px;" id="stickyPkgQty">Ekonomik • 1.000 Adet</div>
            <div class="fw-bolder text-primary" style="font-size: 18px; line-height: 1.1;" id="stickyGrandTotal">0,00 ₺</div>
            <div class="text-success" style="font-size: 9px;"><i class="bi bi-shield-check"></i> KDV Dahil</div>
        </div>
        <button type="submit" form="printConfigForm" class="btn btn-apple btn-apple-pink px-4 py-2 fw-bold text-nowrap shadow-sm d-flex align-items-center gap-2" style="font-size: 14px; border-radius: 999px;">
            <span>Sepete Ekle</span>
            <i class="bi bi-bag-check-fill fs-6"></i>
        </button>
    </div>
</div>

<!-- ========================================================================= -->
<!-- ONLINE VEKTÖREL SVG DÜZENLEYİCİ MODAL -->
<!-- ========================================================================= -->
<!-- ========================================================================= -->
<!-- 🎨 CANVA-STYLE INTERACTIVE VECTOR DESIGN STUDIO MODAL -->
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

            <!-- ========================================================================= -->
            <!-- ⚡ HIZLI BASKI AYARLARI & CANLI NET FİYAT BARI -->
            <!-- ========================================================================= -->
            <!-- 1. Masaüstü Hızlı Ayarlar Barı -->
            <div class="canva-studio-quick-bar d-none d-md-flex bg-white border-bottom py-2 px-3 align-items-center justify-content-between flex-wrap gap-2 shadow-2xs position-relative" style="z-index: 25;">
                <!-- Sol: Paket Seçimi, Adet Seçimi ve İnce Ayar Butonu -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <!-- 1. Baskı Paketi Seçimi -->
                    <div class="d-flex align-items-center gap-1">
                        <span class="small fw-bold text-muted" style="font-size: 11px;"><i class="bi bi-box-seam text-primary me-1"></i> Paket:</span>
                        <div class="btn-group btn-group-sm" id="canvaStudioPkgGroup">
                            <?php foreach ($activePackages as $pKey => $pkg): ?>
                                <button type="button" class="btn btn-outline-primary btn-sm canva-pkg-btn <?= $pKey === $firstPkgKey ? 'active' : '' ?>" id="canva_btn_pkg_<?= $pKey ?>" onclick="CanvaStudio.setPackage('<?= $pKey ?>')" style="font-size: 11px; padding: 3px 8px;">
                                    <i class="bi <?= $pkg['icon'] ?> me-1"></i> <?= htmlspecialchars($pkg['title']) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="vr mx-1 text-secondary opacity-25" style="height: 22px;"></div>

                    <!-- 2. Baskı Adedi Seçimi -->
                    <div class="d-flex align-items-center gap-1">
                        <span class="small fw-bold text-muted" style="font-size: 11px;"><i class="bi bi-layers text-primary me-1"></i> Adet:</span>
                        <select class="form-select form-select-sm fw-bold border-primary-subtle shadow-2xs" id="canvaStudioQtySelect" onchange="CanvaStudio.setQuantity(this.value)" style="width: 125px; font-size: 11px; padding: 3px 6px;">
                            <?php if (!empty($product['quantity_tiers'])): ?>
                                <?php foreach ($product['quantity_tiers'] as $tier): ?>
                                    <option value="<?= $tier['quantity'] ?>"><?= number_format($tier['quantity'], 0, '', '.') ?> Adet</option>
                                <?php endforeach; ?>
                                <option value="custom">✏️ Özel Adet...</option>
                            <?php else: ?>
                                <option value="1000">1.000 Adet</option>
                            <?php endif; ?>
                        </select>
                        <!-- Özel Adet Manuel Giriş Kutusu -->
                        <div id="canvaCustomQtyInputWrap" style="display: none;">
                            <input type="number" id="canvaStudioCustomQtyInput" class="form-control form-control-sm text-center fw-bold border-primary" style="width: 75px; font-size: 11px; padding: 3px 4px;" min="10" step="10" value="500" placeholder="Adet" oninput="CanvaStudio.setCustomQuantity(this.value)">
                        </div>
                    </div>

                    <div class="vr mx-1 text-secondary opacity-25" style="height: 22px;"></div>

                    <!-- 3. İnce Ayar (Özel Ölçü & Kağıt) Açılır Butonu -->
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 d-flex align-items-center gap-1 shadow-2xs" id="canvaBtnFineTuneToggle" onclick="CanvaStudio.toggleFineTunePanel()">
                        <i class="bi bi-sliders text-primary"></i>
                        <span class="fw-semibold" style="font-size: 11px;">İnce Ayar (Ölçü &amp; Kağıt)</span>
                        <i class="bi bi-chevron-down ms-1" id="canvaFineTuneChevron" style="font-size: 9px; transition: transform 0.2s ease;"></i>
                    </button>
                </div>

                <!-- Sağ: Canlı Net Fiyat (KDV ve Kargo Hariç) -->
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <div class="text-end">
                        <div class="d-flex align-items-baseline gap-1 justify-content-end">
                            <span class="text-muted small fw-semibold" style="font-size: 11px;">Net Fiyat:</span>
                            <span class="fw-bolder text-primary fs-6" id="canvaStudioNetPrice">0,00 ₺</span>
                        </div>
                        <div class="d-flex align-items-center gap-1 justify-content-end" style="font-size: 10px;">
                            <span class="badge bg-secondary-subtle text-secondary py-0 px-1 fw-semibold" style="font-size: 9.5px;">KDV ve Kargo Hariç</span>
                            <span class="text-muted" id="canvaStudioUnitPriceText">Birim: 0,00 ₺</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Mobilde Ultra-Kompakt Baskı & Fiyat Şeridi (Tıklayınca İnce Ayar Açılır) -->
            <div class="d-md-none bg-white border-bottom px-3 py-2 d-flex align-items-center justify-content-between shadow-2xs cursor-pointer" style="z-index: 25;" onclick="CanvaStudio.toggleFineTunePanel()">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary text-white fw-bold px-2 py-1" id="mobilePkgBadge" style="font-size: 10.5px;">Standart</span>
                    <span class="text-dark fw-bold small" id="mobileQtyBadge" style="font-size: 11px;">1.000 Adet</span>
                    <span class="text-muted" style="font-size: 10px;"><i class="bi bi-sliders text-primary me-1"></i>Ayarla <i class="bi bi-chevron-down ms-1" style="font-size: 8px;"></i></span>
                </div>
                <div class="text-end">
                    <span class="fw-bolder text-primary" id="mobileNetPrice" style="font-size: 13px;">0,00 ₺</span>
                    <span class="text-muted small" style="font-size: 9px; display: block; line-height: 1;">+KDV/Kargo</span>
                </div>
            </div>

            <!-- 🛠️ AÇILIR İNCE AYAR ÇEKMECESİ / PANELİ (CANVA İÇİ - TÜM SEÇENEKLER & FİYAT FARKLARI) -->
            <div id="canvaStudioFineTuneDrawer" class="bg-light border-bottom p-3 shadow-sm" style="display: none; z-index: 24; max-height: 280px; overflow-y: auto;">
                <div class="container-fluid p-0">
                    
                    <!-- 1. Ölçü / Ebat Seçimi -->
                    <div class="mb-3 p-2 bg-white rounded-3 border">
                        <label class="small fw-bold text-dark mb-1 d-block"><i class="bi bi-aspect-ratio text-primary me-1"></i> 1. Ölçü / Ebat Seçimi</label>
                        <div class="d-flex gap-3 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="canva_size_mode" id="canvaSizeStd" value="standard" checked onchange="CanvaStudio.onSizeModeChange('standard')">
                                <label class="form-check-label small fw-semibold cursor-pointer" for="canvaSizeStd">
                                    Standart Ebat (<?= $product['standard_width'] ?> x <?= $product['standard_height'] ?> cm)
                                </label>
                            </div>
                            <?php if ($product['is_custom_size']): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="canva_size_mode" id="canvaSizeCustom" value="custom" onchange="CanvaStudio.onSizeModeChange('custom')">
                                    <label class="form-check-label small fw-semibold cursor-pointer text-primary" for="canvaSizeCustom">
                                        <i class="bi bi-pencil-square me-1"></i> Özel Ölçü Gir
                                    </label>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($product['is_custom_size']): ?>
                            <div id="canvaCustomDimInputs" class="row g-2 pt-2 border-top" style="display: none;">
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">En (cm)</span>
                                        <input type="number" step="0.1" id="canvaInpWidth" class="form-control" value="<?= $product['standard_width'] ?>" min="1" max="100" oninput="CanvaStudio.onDimensionChange()">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Boy (cm)</span>
                                        <input type="number" step="0.1" id="canvaInpHeight" class="form-control" value="<?= $product['standard_height'] ?>" min="1" max="100" oninput="CanvaStudio.onDimensionChange()">
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 2. Kağıt Türü & Gramajı -->
                    <!-- 2. Dinamik Matbaa Varyant Grupları (Türmatsan Standartları) -->
                    <?php if (!empty($product['variant_groups'])): ?>
                        <?php foreach ($product['variant_groups'] as $vGroup): ?>
                            <div class="mb-3 p-2 bg-white rounded-3 border">
                                <label class="small fw-semibold text-dark mb-1 d-block">
                                    <span><?= htmlspecialchars($vGroup['name']) ?></span>
                                    <?php if (!empty($vGroup['description'])): ?>
                                        <span class="text-muted fw-normal ms-1" style="font-size: 10px;">(<?= htmlspecialchars($vGroup['description']) ?>)</span>
                                    <?php endif; ?>
                                </label>
                                <div class="segmented-grid">
                                    <?php foreach ($vGroup['options'] as $idx => $opt): 
                                        $suppDelta = isset($opt['supplier_cost_1000']) ? (float)$opt['supplier_cost_1000'] : 0.00;
                                        $badgeText = '';
                                        $badgeClass = 'text-primary';

                                        if ($suppDelta > 0) {
                                            $badgeText = '+' . number_format($suppDelta, 0, '', '.') . ' ₺';
                                        } elseif ($suppDelta < 0) {
                                            $badgeText = number_format($suppDelta, 0, '', '.') . ' ₺';
                                            $badgeClass = 'text-success';
                                        } elseif ((float)$opt['fixed_fee_try'] > 0) {
                                            $badgeText = '+' . number_format((float)$opt['fixed_fee_try'], 0, '', '.') . ' ₺';
                                        } elseif ((float)$opt['per_unit_fee_try'] > 0) {
                                            $badgeText = '+' . number_format((float)$opt['per_unit_fee_try'] * 1000, 0, '', '.') . ' ₺';
                                        } elseif ($opt['calc_type'] === 'percent' && (float)$opt['percent_fee'] > 0) {
                                            $badgeText = '+%' . (float)$opt['percent_fee'];
                                        } else {
                                            $badgeText = '0 ₺';
                                            $badgeClass = 'text-muted';
                                        }
                                    ?>
                                        <div class="segmented-option">
                                            <input type="radio" 
                                                   name="canva_options[<?= $vGroup['id'] ?>]" 
                                                   id="canva_vopt_<?= $opt['id'] ?>" 
                                                   value="<?= $opt['id'] ?>" 
                                                   <?= ($opt['is_default'] || $idx === 0) ? 'checked' : '' ?>
                                                   onchange="CanvaStudio.onFineTuneOptionChange('options[<?= $vGroup['id'] ?>]', '<?= $opt['id'] ?>')">
                                            <label for="canva_vopt_<?= $opt['id'] ?>" class="segmented-label bg-white">
                                                <span class="opt-title"><?= htmlspecialchars($opt['name']) ?></span>
                                                <span class="opt-extra <?= $badgeClass ?> fw-bold"><?= $badgeText ?></span>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

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
                                <!-- Arama Kutusu -->
                                <div class="input-group input-group-sm mb-2 shadow-2xs">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                                    <input type="text" id="canvaTemplateSearch" class="form-control border-start-0 ps-0" placeholder="Şablon veya meslek ara..." oninput="CanvaStudio.onSearchTemplates(this.value)">
                                    <button type="button" class="btn btn-outline-secondary border-start-0" onclick="document.getElementById('canvaTemplateSearch').value=''; CanvaStudio.onSearchTemplates('');" title="Temizle"><i class="bi bi-x"></i></button>
                                </div>

                                <!-- Sektör Açılır Listesi (Dropdown Select) -->
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
                                        <option value="fotoğraf">📷 Fotoğraf &amp; Medya</option>
                                        <option value="moda">👗 Moda &amp; Butik</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Şablon Bulunamadı Uyarısı -->
                            <div id="canvaNoTemplatesAlert" class="alert alert-light text-center py-3 border rounded-3 mb-2" style="display: none;">
                                <i class="bi bi-search text-muted fs-4 d-block mb-1"></i>
                                <span class="small text-muted">Aramanıza uygun şablon bulunamadı.</span>
                            </div>

                            <!-- Şablon Kartları Listesi (JS Dinamik Olarak Basar) -->
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

                        <!-- 4. İKONLAR & QR KOD SEKMESİ (ZENGİN VEKTÖR KÜTÜPHANESİ) -->
                        <div class="tab-pane fade" id="cPane-icons" role="tabpanel">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="fw-bold small text-dark mb-0"><i class="bi bi-app-indicator text-warning me-1"></i> Vektörel İkonlar</h6>
                                <span class="badge bg-warning-subtle text-dark" style="font-size: 10px;">40+ İkon</span>
                            </div>

                            <!-- İkon Arama Kutusu -->
                            <div class="input-group input-group-sm mb-2 shadow-2xs">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                                <input type="text" id="canvaIconSearchInput" class="form-control border-start-0 ps-0" placeholder="İkon ara (whatsapp, terazi, vinç, ev...)" oninput="CanvaStudio.filterIcons(this.value)">
                            </div>

                            <!-- 1. İletişim & Sosyal Medya -->
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
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="facebook fb sosyal" onclick="CanvaStudio.addIcon('facebook')" title="Facebook"><i class="bi bi-facebook fs-5 text-primary"></i><div style="font-size: 9px;">Facebook</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="twitter x sosyal tweet" onclick="CanvaStudio.addIcon('twitter')" title="Twitter / X"><i class="bi bi-twitter-x fs-5 text-dark"></i><div style="font-size: 9px;">Twitter X</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="telegram mesajlaşma kanal" onclick="CanvaStudio.addIcon('telegram')" title="Telegram"><i class="bi bi-telegram fs-5 text-info"></i><div style="font-size: 9px;">Telegram</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="youtube video kanal abone" onclick="CanvaStudio.addIcon('youtube')" title="YouTube"><i class="bi bi-youtube fs-5 text-danger"></i><div style="font-size: 9px;">YouTube</div></button></div>
                                </div>
                            </div>

                            <!-- 2. Sektörel İkonlar -->
                            <div class="canva-icon-category-group mb-2">
                                <div class="text-muted fw-bold mb-1" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">🏢 Sektörel Çizim &amp; İkonlar</div>
                                <div class="row g-1">
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="terazi adalet hukuk avukat mahkeme baro" onclick="CanvaStudio.addIcon('scale')" title="Adalet Terazisi"><i class="bi bi-scale fs-5 text-warning"></i><div style="font-size: 9px;">Terazi</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="inşaat şantiye kask mühendis mimar bina" onclick="CanvaStudio.addIcon('helmet')" title="İnşaat Kaskı"><i class="bi bi-shield-shaded fs-5 text-warning"></i><div style="font-size: 9px;">Kask/İnşaat</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="bina gökdelen plaza şirket mimarlık" onclick="CanvaStudio.addIcon('building')" title="Bina / Plaza"><i class="bi bi-building fs-5 text-primary"></i><div style="font-size: 9px;">Plaza/Bina</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="ev emlak gayrimenkul konut villa çatı" onclick="CanvaStudio.addIcon('house')" title="Emlak / Ev"><i class="bi bi-house-door-fill fs-5 text-success"></i><div style="font-size: 9px;">Emlak / Ev</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="anahtar kapı kilit teslim emlak" onclick="CanvaStudio.addIcon('key')" title="Anahtar"><i class="bi bi-key-fill fs-5 text-warning"></i><div style="font-size: 9px;">Anahtar</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="stetoskop doktor tıp sağlık klinik" onclick="CanvaStudio.addIcon('stethoscope')" title="Stetoskop / Tıp"><i class="bi bi-heart-pulse-fill fs-5 text-danger"></i><div style="font-size: 9px;">Sağlık/Tıp</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="diş hekimi hekim implant klinik gülüş" onclick="CanvaStudio.addIcon('tooth')" title="Diş Hekimi"><i class="bi bi-emoji-smile fs-5 text-info"></i><div style="font-size: 9px;">Diş Hekimi</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="restoran yemek kafe çatal bıçak gıda" onclick="CanvaStudio.addIcon('restaurant')" title="Restoran"><i class="bi bi-egg-fried fs-5 text-warning"></i><div style="font-size: 9px;">Restoran</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="kahve cafe coffee fincan içecek" onclick="CanvaStudio.addIcon('coffee')" title="Kahve / Kafe"><i class="bi bi-cup-hot-fill fs-5 text-danger"></i><div style="font-size: 9px;">Kahve</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="makas kuaför berber güzellik saç salon" onclick="CanvaStudio.addIcon('scissors')" title="Kuaför / Berber"><i class="bi bi-scissors fs-5 text-dark"></i><div style="font-size: 9px;">Kuaför</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="araba otomotiv oto galeri araç servis" onclick="CanvaStudio.addIcon('car')" title="Otomotiv / Araba"><i class="bi bi-car-front-fill fs-5 text-primary"></i><div style="font-size: 9px;">Otomotiv</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="tır kamyon kargo nakliyat lojistik teslimat" onclick="CanvaStudio.addIcon('truck')" title="Nakliyat / Lojistik"><i class="bi bi-truck fs-5 text-success"></i><div style="font-size: 9px;">Lojistik</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="grafik borsa finans muhasebe para trend" onclick="CanvaStudio.addIcon('chart')" title="Finans & Grafik"><i class="bi bi-graph-up-arrow fs-5 text-success"></i><div style="font-size: 9px;">Finans</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="kod yazılım bilişim yazılımcı it bilgisayar" onclick="CanvaStudio.addIcon('code')" title="Yazılım / Kod"><i class="bi bi-code-slash fs-5 text-primary"></i><div style="font-size: 9px;">Yazılım</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="kamera fotoğraf video medya stüdyo" onclick="CanvaStudio.addIcon('camera')" title="Fotoğraf & Kamera"><i class="bi bi-camera-fill fs-5 text-dark"></i><div style="font-size: 9px;">Fotoğraf</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="mezuniyet kep eğitim akademi okul üniversite" onclick="CanvaStudio.addIcon('education')" title="Eğitim / Akademi"><i class="bi bi-mortarboard-fill fs-5 text-primary"></i><div style="font-size: 9px;">Eğitim</div></button></div>
                                </div>
                            </div>

                            <!-- 3. Rozet & Semboller -->
                            <div class="canva-icon-category-group mb-2">
                                <div class="text-muted fw-bold mb-1" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">👑 Rozet, VIP &amp; Semboller</div>
                                <div class="row g-1">
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="taç kraliyet vip lüks altın crown" onclick="CanvaStudio.addIcon('crown')" title="Kraliyet Tacı"><i class="bi bi-award-fill fs-5 text-warning"></i><div style="font-size: 9px;">Taç</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="yıldız puan favori star gold" onclick="CanvaStudio.addIcon('star')" title="Yıldız"><i class="bi bi-star-fill fs-5 text-warning"></i><div style="font-size: 9px;">Yıldız</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="elmas pırlanta mücevher lüks" onclick="CanvaStudio.addIcon('diamond')" title="Elmas / Pırlanta"><i class="bi bi-gem fs-5 text-info"></i><div style="font-size: 9px;">Elmas</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="onay rozet garanti kaliteli verified" onclick="CanvaStudio.addIcon('badge')" title="Onaylı Rozet"><i class="bi bi-patch-check-fill fs-5 text-success"></i><div style="font-size: 9px;">Onay Rozeti</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="kalkan güvenlik koruma sigorta" onclick="CanvaStudio.addIcon('shield')" title="Kalkan / Güvenlik"><i class="bi bi-shield-fill-check fs-5 text-primary"></i><div style="font-size: 9px;">Güvenlik</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="kalp sevgi sağlık aşk" onclick="CanvaStudio.addIcon('heart')" title="Kalp"><i class="bi bi-heart-fill fs-5 text-danger"></i><div style="font-size: 9px;">Kalp</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="kupa şampiyon başarı lider" onclick="CanvaStudio.addIcon('trophy')" title="Kupa / Başarı"><i class="bi bi-trophy-fill fs-5 text-warning"></i><div style="font-size: 9px;">Kupa</div></button></div>
                                    <div class="col-3"><button type="button" class="btn btn-outline-secondary w-100 p-1 text-center rounded-3 canva-icon-btn" data-tags="çanta alışveriş mağaza butik sepet" onclick="CanvaStudio.addIcon('bag')" title="Alışveriş"><i class="bi bi-bag-fill fs-5 text-dark"></i><div style="font-size: 9px;">Alışveriş</div></button></div>
                                </div>
                            </div>

                            <hr class="my-2">

                            <h6 class="fw-bold small text-dark mb-1">QR Kod Oluştur</h6>
                            <p class="text-muted" style="font-size: 11px;">Web siteniz veya WhatsApp için dinamik QR kod oluşturun:</p>
                            <div class="input-group input-group-sm mb-2">
                                <input type="text" id="canvaQrInput" class="form-control" placeholder="https://siteniz.com">
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
                                <button type="button" class="btn btn-sm border rounded-circle p-0" style="width: 28px; height: 28px; background: #f8fafc;" onclick="CanvaStudio.setBackgroundColor('#f8fafc')" title="Buz Beyazı"></button>
                                <button type="button" class="btn btn-sm border rounded-circle p-0" style="width: 28px; height: 28px; background: #0071e3;" onclick="CanvaStudio.setBackgroundColor('#0071e3')" title="Kurumsal Mavi"></button>
                                <button type="button" class="btn btn-sm border rounded-circle p-0" style="width: 28px; height: 28px; background: #10b981;" onclick="CanvaStudio.setBackgroundColor('#10b981')" title="Zümrüt"></button>
                                <button type="button" class="btn btn-sm border rounded-circle p-0" style="width: 28px; height: 28px; background: #7c2d12;" onclick="CanvaStudio.setBackgroundColor('#7c2d12')" title="Bordo"></button>
                            </div>

                            <hr class="my-2">
                            <button type="button" class="btn btn-outline-danger w-100 text-start p-2 rounded-3 small shadow-2xs" onclick="if(confirm('Tüm tuval sıfırlansın mı?')) { CanvaStudio.canvas.clear(); CanvaStudio.setBackgroundColor('#ffffff'); CanvaStudio.drawGuides(); }">
                                <div class="fw-bold text-danger"><i class="bi bi-trash me-1"></i> Boş Tuvale Sıfırla</div>
                                <div class="text-muted" style="font-size: 10px;">Sıfırdan temiz beyaz sayfa ile başla</div>
                            </button>
                        </div>

                    </div>
                </div>

                <!-- Sağ Tuval & Özellik Çubuğu -->
                <div class="canva-workspace flex-grow-1 d-flex flex-column bg-secondary bg-opacity-10 position-relative">
                    
                    <!-- Dinamik Özellik Çubuğu (Seçili Nesneye Göre Açılır) -->
                    <div id="canvaPropertiesBar" class="bg-white border-bottom p-2 px-3 align-items-center justify-content-between flex-wrap gap-2 shadow-xs" style="display: none; z-index: 15;">
                        
                        <!-- Metin Seçiliyse Gösterilen Araçlar -->
                        <div id="canvaTextControls" class="d-flex align-items-center gap-2 flex-wrap">
                            <!-- Font Ailesi -->
                            <select id="canvaFontFamily" class="form-select form-select-sm" style="width: 145px; font-size: 12px;">
                                <option value="Inter">Inter (Modern)</option>
                                <option value="Montserrat">Montserrat (Kalın)</option>
                                <option value="Poppins">Poppins (Yuvarlak)</option>
                                <option value="Playfair Display">Playfair (Lüks Serif)</option>
                                <option value="Oswald">Oswald (Kompakt)</option>
                                <option value="Roboto">Roboto (Temiz)</option>
                                <option value="Lato">Lato (Zarif)</option>
                                <option value="Arial">Arial (Klasik)</option>
                                <option value="Times New Roman">Times New Roman</option>
                                <option value="Courier New">Courier New</option>
                            </select>

                            <!-- Punto / Font Boyutu (- / input / +) -->
                            <div class="input-group input-group-sm" style="width: 105px;">
                                <button type="button" class="btn btn-outline-secondary px-2" onclick="CanvaStudio.changeFontSize(-2)" title="Punto Küçült">-</button>
                                <input type="number" id="canvaFontSize" class="form-control text-center px-1" value="16" min="6" max="150" title="Punto">
                                <button type="button" class="btn btn-outline-secondary px-2" onclick="CanvaStudio.changeFontSize(+2)" title="Punto Büyüt">+</button>
                            </div>

                            <!-- Renk Seçici -->
                            <div class="d-flex align-items-center gap-1">
                                <input type="color" id="canvaTextColorPicker" value="#000000" class="form-control form-control-color p-0 border" style="width: 30px; height: 28px; cursor: pointer;" title="Renk Seç">
                                <!-- Hızlı Renk Noktaları -->
                                <button type="button" class="btn btn-xs rounded-circle border p-0" style="width: 18px; height: 18px; background: #0f172a;" onclick="CanvaStudio.setTextColor('#0f172a')" title="Siyah"></button>
                                <button type="button" class="btn btn-xs rounded-circle border p-0" style="width: 18px; height: 18px; background: #ffffff;" onclick="CanvaStudio.setTextColor('#ffffff')" title="Beyaz"></button>
                                <button type="button" class="btn btn-xs rounded-circle border p-0" style="width: 18px; height: 18px; background: #0071e3;" onclick="CanvaStudio.setTextColor('#0071e3')" title="Mavi"></button>
                                <button type="button" class="btn btn-xs rounded-circle border p-0" style="width: 18px; height: 18px; background: #d4af37;" onclick="CanvaStudio.setTextColor('#d4af37')" title="Altın"></button>
                                <button type="button" class="btn btn-xs rounded-circle border p-0" style="width: 18px; height: 18px; background: #e11d48;" onclick="CanvaStudio.setTextColor('#e11d48')" title="Kırmızı"></button>
                            </div>

                            <!-- Kalın, İtalik, Altı Çizili -->
                            <div class="btn-group btn-group-sm">
                                <button type="button" id="canvaBtnBold" class="btn btn-outline-secondary" onclick="CanvaStudio.toggleBold()" title="Kalın (Bold)"><i class="bi bi-type-bold"></i></button>
                                <button type="button" id="canvaBtnItalic" class="btn btn-outline-secondary" onclick="CanvaStudio.toggleItalic()" title="İtalik"><i class="bi bi-type-italic"></i></button>
                                <button type="button" id="canvaBtnUnderline" class="btn btn-outline-secondary" onclick="CanvaStudio.toggleUnderline()" title="Altı Çizili"><i class="bi bi-type-underline"></i></button>
                            </div>

                            <!-- Metin Hizalama -->
                            <div class="btn-group btn-group-sm">
                                <button type="button" id="canvaBtnAlignLeft" class="btn btn-outline-secondary" onclick="CanvaStudio.setTextAlign('left')" title="Sola Hizala"><i class="bi bi-text-left"></i></button>
                                <button type="button" id="canvaBtnAlignCenter" class="btn btn-outline-secondary" onclick="CanvaStudio.setTextAlign('center')" title="Ortala"><i class="bi bi-text-center"></i></button>
                                <button type="button" id="canvaBtnAlignRight" class="btn btn-outline-secondary" onclick="CanvaStudio.setTextAlign('right')" title="Sağa Hizala"><i class="bi bi-text-right"></i></button>
                            </div>
                        </div>

                        <!-- Ortak Hizalama, Katman & İşlem Butonları -->
                        <div class="d-flex align-items-center gap-1 ms-auto">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="CanvaStudio.alignObject('center-h')" title="Yatay Ortala"><i class="bi bi-align-center"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="CanvaStudio.alignObject('center-v')" title="Dikey Ortala"><i class="bi bi-align-middle"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="CanvaStudio.bringForward()" title="Öne Getir"><i class="bi bi-front"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="CanvaStudio.sendBackward()" title="Arkaya Gönder"><i class="bi bi-back"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="CanvaStudio.duplicateSelected()" title="Çoğalt (Ctrl+D)"><i class="bi bi-copy"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="CanvaStudio.deleteSelected()" title="Sil (Delete)"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>

                    <!-- Canlı Taşma / Kesim Sınırı Uyarı Rozeti -->
                    <div id="canvaBleedAlert" class="alert alert-warning py-1 px-3 mb-0 shadow-sm align-items-center justify-content-between border-0 rounded-0" style="display: none; z-index: 20; background: #fffbeb; color: #b45309; border-bottom: 1px solid #fde68a !important; font-size: 12px;">
                        <div class="d-flex align-items-center gap-2">
                            <span id="canvaBleedAlertText"><strong>Dikkat:</strong> Yazı / nesne kesim sınırının dışına taştı, baskıda kesilebilir!</span>
                        </div>
                        <button type="button" class="btn-close btn-close-sm" style="font-size: 9px;" onclick="document.getElementById('canvaBleedAlert').style.display='none'"></button>
                    </div>

                    <!-- Tuval Konteyneri (Ortalanmış ve Kılavuzlu) -->
                    <div class="flex-grow-1 d-flex flex-column align-items-center justify-content-center p-3 overflow-auto position-relative" id="canvaCanvasWrapper">
                        <!-- Üst Genişlik Cetvel Rozeti -->
                        <div class="badge bg-dark bg-opacity-75 text-white fw-normal px-3 py-1 mb-2 rounded-pill shadow-xs" style="font-size: 11px; letter-spacing: 0.5px;">
                            <i class="bi bi-arrows-left-right text-info me-1"></i> Genişlik: <strong id="rulerWidthValue" class="text-warning">8.4 cm (84 mm)</strong>
                        </div>

                        <div class="d-flex align-items-center justify-content-center position-relative" id="canvaViewportContainer">
                            <!-- Sol Yükseklik Cetvel Rozeti -->
                            <div class="position-absolute end-100 me-2 text-nowrap badge bg-dark bg-opacity-75 text-white fw-normal px-2 py-1 rounded-pill shadow-xs d-none d-md-inline-block" style="font-size: 11px; transform: rotate(-90deg); transform-origin: right center;">
                                <i class="bi bi-arrows-up-down text-info me-1"></i> Yükseklik: <strong id="rulerHeightValue" class="text-warning">5.2 cm (52 mm)</strong>
                            </div>

                            <!-- Canvas Çerçevesi (Boyutları JS Tarafından Dinamik Ölçeklendirilir) -->
                            <div id="canvaCanvasHolder" class="shadow-lg rounded-3 overflow-hidden bg-white" style="line-height: 0; position: relative;">
                                <canvas id="canvaMainCanvas" width="850" height="526"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Alt Bilgilendirme Rozeti (Masaüstünde Gösterilir) -->
                    <div class="p-2 bg-dark text-white border-top small d-none d-md-flex justify-content-between align-items-center px-4 flex-wrap gap-2" style="font-size: 11px;">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge bg-primary-subtle text-info fw-bold px-2 py-1"><i class="bi bi-shield-check me-1"></i> Mavi Kesikli Çizgi:</span> 
                            <span class="text-white-50">Güvenli Metin Alanı (Yazılarınızın kenarlara yapışmaması için bu sınır içinde kalması önerilir)</span>
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
<!-- 🌟 3D CANLI BASKI & MOCKUP ÖNİZLEME MODAL -->
<!-- ========================================================================= -->
<div class="modal fade" id="canva3dMockupModal" tabindex="-1" aria-hidden="true" style="backdrop-filter: blur(8px); background: rgba(15, 23, 42, 0.75);">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-2xl rounded-4 overflow-hidden" style="background: #0f172a; color: #fff;">
            <!-- Başlık -->
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

            <!-- 3D Gövde -->
            <div class="modal-body p-4 d-flex flex-column align-items-center justify-content-center" style="min-height: 480px; background: radial-gradient(circle at center, #1e293b 0%, #0b0f19 100%);">
                
                <!-- 3D Kart Sahnesi -->
                <div class="mockup-3d-scene" style="perspective: 1200px; width: 100%; display: flex; justify-content: center; align-items: center; padding: 25px 0;">
                    <div id="mockup3dCardInner" class="mockup-3d-card" style="width: 480px; height: 280px; position: relative; transform-style: preserve-3d; transition: transform 0.8s cubic-bezier(0.34, 1.56, 0.64, 1); cursor: pointer; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.6);" onclick="CanvaStudio.toggle3dFlip()" title="Çevirmek için tıklayın">
                        
                        <!-- Ön Yüz (Front Face) -->
                        <div id="mockup3dFrontFace" class="mockup-face position-absolute top-0 start-0 w-100 h-100 rounded-3 overflow-hidden shadow-lg bg-white" style="backface-visibility: hidden; transform: rotateY(0deg);">
                            <!-- Front SVG will be inserted here -->
                        </div>

                        <!-- Arka Yüz (Back Face) -->
                        <div id="mockup3dBackFace" class="mockup-face position-absolute top-0 start-0 w-100 h-100 rounded-3 overflow-hidden shadow-lg bg-white" style="backface-visibility: hidden; transform: rotateY(180deg);">
                            <!-- Back SVG will be inserted here -->
                        </div>

                    </div>
                </div>

                <!-- Çevirme & Bilgi İpucu -->
                <div class="d-flex align-items-center gap-3 mt-3">
                    <button type="button" class="btn btn-outline-light rounded-pill px-4 btn-sm fw-bold shadow-sm" id="btnFlip3dMockup" onclick="CanvaStudio.toggle3dFlip()">
                        <i class="bi bi-arrow-repeat me-1"></i> <span id="btn3dFlipLabel">Arka Yüzü Göster</span>
                    </button>
                    <span class="text-secondary small" style="font-size: 12px;"><i class="bi bi-hand-index-thumb me-1"></i> Çevirmek için kartın üzerine de tıklayabilirsiniz.</span>
                </div>
            </div>

            <!-- Alt Butonlar -->
            <div class="modal-footer border-secondary border-opacity-25 px-4 py-3 bg-slate-900 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary text-white rounded-pill px-4 btn-sm" data-bs-dismiss="modal">
                    <i class="bi bi-pencil me-1"></i> Düzenlemeye Dön
                </button>
                <button type="button" class="btn btn-success rounded-pill px-4 btn-sm fw-bold" onclick="CanvaStudio.saveFromMockup()">
                    <i class="bi bi-check-circle me-1"></i> Tasarımı Onayla & Siparişe Ekle
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 🎥 360° SİNEMATİK PAKET TANITIM VİDEO MODALI -->
<!-- ========================================================================= -->
<div class="modal fade" id="packageVideoModal" tabindex="-1" aria-labelledby="pkgVideoModalTitle" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-2xl rounded-4 overflow-hidden text-white" style="background: #0f172a;">
            <div class="modal-header border-secondary border-opacity-25 px-4 py-3 d-flex justify-content-between align-items-center" style="background: #0b0f19;">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-danger-subtle text-danger p-2 rounded-circle"><i class="bi bi-play-circle-fill fs-6"></i></span>
                    <div>
                        <h6 class="modal-title fw-bold text-white mb-0" id="pkgVideoModalTitle">TamBaskı Kurumsal 3D Sinematik Tanıtım Videosu</h6>
                        <small class="text-secondary" style="font-size: 11px;">Masa Üstü Gerçek Çekim • 24K Altın Varak & Kabartma Lak Işık Yansıması</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat" onclick="if(window.stopPackageModalVideo) window.stopPackageModalVideo();"></button>
            </div>
            <div class="modal-body p-0 d-flex flex-column align-items-center justify-content-center position-relative" style="background: #060910; min-height: 480px;">
                <video id="packageModalVideoPlayer" controls autoplay loop playsinline class="w-100 rounded-0" style="max-height: 75vh; object-fit: contain; background: #000;"></video>
            </div>
            <div class="modal-footer border-secondary border-opacity-25 px-4 py-2 d-flex justify-content-between align-items-center" style="background: #0b0f19;">
                <span class="text-secondary small" style="font-size: 11px;"><i class="bi bi-stars text-warning me-1"></i> 60 FPS Ultra HD 4K Işık & Kalınlık Gösterimi</span>
                <button type="button" class="btn btn-outline-light rounded-pill px-4 btn-sm fw-bold" data-bs-dismiss="modal" onclick="if(window.stopPackageModalVideo) window.stopPackageModalVideo();">
                    Tamam, Kapat
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 🔍 ULTRA HD TAM EKRAN GÖRSEL İNCELEME MODALI (LIGHTBOX) -->
<!-- ========================================================================= -->
<div class="modal fade" id="imageFullscreenModal" tabindex="-1" aria-labelledby="imageFullscreenTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 95vw;">
        <div class="modal-content border-0 shadow-2xl rounded-4 overflow-hidden text-white" style="background: #090d16;">
            <div class="modal-header border-secondary border-opacity-25 px-4 py-3 d-flex justify-content-between align-items-center" style="background: #060910;">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary p-2 rounded-circle"><i class="bi bi-arrows-fullscreen fs-6"></i></span>
                    <div>
                        <h6 class="modal-title fw-bold text-white mb-0" id="imageFullscreenTitle">TamBaskı Kurumsal Baskı & Doku İncelemesi</h6>
                        <small class="text-secondary" style="font-size: 11px;">Ultra HD 8K Çözünürlükte Stüdyo Çekimi</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body p-2 d-flex align-items-center justify-content-center position-relative" style="background: radial-gradient(circle at center, #1e293b 0%, #060910 100%); min-height: 72vh;">
                <!-- Önceki Butonu -->
                <button type="button" class="btn btn-dark position-absolute start-0 top-50 translate-middle-y ms-3 rounded-circle shadow d-flex align-items-center justify-content-center" onclick="navigateFullscreenGallery(-1)" style="width: 48px; height: 48px; z-index: 30; background: rgba(0,0,0,0.65); border: 1px solid rgba(255,255,255,0.25);" title="Önceki Görsel">
                    <i class="bi bi-chevron-left fs-5"></i>
                </button>

                <!-- Tam Ekran Görsel -->
                <img id="fullscreenModalImg" src="" alt="Tam Ekran Ürün İnceleme" class="img-fluid rounded-3 shadow-2xl" style="max-height: 75vh; width: auto; object-fit: contain;">

                <!-- Sonraki Butonu -->
                <button type="button" class="btn btn-dark position-absolute end-0 top-50 translate-middle-y me-3 rounded-circle shadow d-flex align-items-center justify-content-center" onclick="navigateFullscreenGallery(1)" style="width: 48px; height: 48px; z-index: 30; background: rgba(0,0,0,0.65); border: 1px solid rgba(255,255,255,0.25);" title="Sonraki Görsel">
                    <i class="bi bi-chevron-right fs-5"></i>
                </button>
            </div>
            <div class="modal-footer border-secondary border-opacity-25 px-4 py-2 d-flex justify-content-between align-items-center" style="background: #060910;">
                <span class="text-secondary small" style="font-size: 11px;" id="fullscreenModalCounter"><i class="bi bi-image me-1"></i> Görsel 1 / 4</span>
                <button type="button" class="btn btn-outline-light rounded-pill px-4 btn-sm fw-bold" data-bs-dismiss="modal">
                    Kapat (ESC)
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- ONLINE VEKTÖREL SVG DÜZENLEYİCİ MODAL (Eski Şablon Editörü) -->
<!-- ========================================================================= -->
<!-- Fabric.js Kütüphanesi -->
<script src="<?= SITE_URL ?>/assets/js/fabric.min.js?v=<?= time() ?>"></script>

<script src="<?= SITE_URL ?>/assets/js/calculator.js?v=<?= time() ?>"></script>
<script src="<?= SITE_URL ?>/assets/js/editor.js?v=<?= time() ?>"></script>
<script src="<?= SITE_URL ?>/assets/js/canva_templates_engine.js?v=<?= time() ?>"></script>
<script src="<?= SITE_URL ?>/assets/js/canva_studio.js?v=<?= time() ?>"></script>
<script src="<?= SITE_URL ?>/assets/js/package_showcase.js?v=<?= time() ?>"></script>
<script src="<?= SITE_URL ?>/assets/js/cinematic_player.js?v=<?= time() ?>"></script>

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
        desc: '350 gr. Kuşe kağıt üzerine uygulanan mat selefon kaplaması sayesinde su damlacıklarına ve neme karşı dayanıklıdır. Günlük kurumsal kullanım için bütçe dostu ve profesyonel çözüm.',
        fullDesc: '<ul class="ps-3 mb-0"><li><strong>350 gr/m² Kalın Kuşe Karton:</strong> Eğilip bükülmeye karşı dayanıklı gövde.</li><li><strong>Su Geçirmez Mat Selefon:</strong> Yüzeyde koruyucu film tabakası oluşturarak sıvı temasında kabarma yapmaz.</li><li><strong>Keskin Düz Kesim:</strong> Milimetrik lazer kesim ile pürüzsüz kenarlar.</li><li><strong>Heidelberg Ofset Baskı:</strong> Canlı renkler ve net mikro yazılar.</li></ul>'
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
        desc: 'En çok tercih edilen kurumsal model. Çift taraflı koruyucu selefonu ve yuvarlatılmış oval köşeleri sayesinde cüzdanda veya cepte ezilmez, yıpranmaz, deforme olmaz.',
        fullDesc: '<ul class="ps-3 mb-0"><li><strong>Çift Yön Mat Selefon:</strong> Kartvizitin hem ön hem arka yüzü sıvıya, kire ve neme karşı tam korumalıdır.</li><li><strong>Oval Radyus Köşeler:</strong> Sivri köşeleri olmadığı için cebe girip çıkarken ezilmez, bükülmez.</li><li><strong>350 gr Tok Rijit Gövde:</strong> Yüksek mukavemetli kartvizit kalitesi.</li><li><strong>Canlı Ofset Renkler:</strong> 300 DPI ultra keskin ofset baskı.</li></ul>'
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
        desc: 'Dokunduğunuzda hissedilen kadife yumuşaklığındaki Soft-Touch yüzeyi ve logonuzu öne çıkaran 3D parlak kabartma lakı ile prestijli bir ilk izlenim bırakın.',
        fullDesc: '<ul class="ps-3 mb-0"><li><strong>Soft-Touch Kadife Selefon:</strong> İpeksi ve kadifemsi lüks dokunuş hissi. Parmak izi tutmaz, su ve neme dayanıklıdır.</li><li><strong>3D Kabartma Lak (Spot UV):</strong> Logo ve unvan kabartılarak parlak camsı bir dokuyla vurgulanır.</li><li><strong>Ağır Gramaj Mukavemeti:</strong> Tok ve son derece sağlam kartvizit yapısı.</li><li><strong>VIP Kurumsal İmaj:</strong> Yönetici ve üst düzey temsiller için idealdir.</li></ul>'
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
        desc: 'Özel dokulu ithal İtalyan Tuale fantezi kağıdı ve ışıl ışıl parıldayan 24K sıcak altın yaldız baskısı ile en üst düzey yönetici kartviziti deneyimi.',
        fullDesc: '<ul class="ps-3 mb-0"><li><strong>İtalyan Tuale Fantezi Doku:</strong> Özel dokulu kabartmalı yüzey yapısı ile elinize aldığınızda kalitesini hissettirir.</li><li><strong>24K Altın Varak Yaldız:</strong> Sıcak presle basılan ayna parlaklığında altın yaldız baskı.</li><li><strong>Üst Düzey Yönetici ve VIP Temsil:</strong> Sıra dışı ve akılda kalıcı zarafet.</li><li><strong>Özel Koleksiyon:</strong> En seçkin matbaa kağıtlarından üretilir.</li></ul>'
    }
};

function selectPackage(pkg, element) {
    window.currentSelectedPackageKey = pkg;
    if (window.PackageShowcase) {
        PackageShowcase.setPackage(pkg);
    }

    // Pakete tıklandığında interaktif 3D sahneyi öne getir
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

    // Sol Metin ve Teknik Tabloyu Güncelle
    const data = packageData[pkg];
    if (data) {
        const tagEl = document.getElementById('mockupPackageTag');
        if (tagEl) tagEl.innerHTML = data.tag;

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

        // 📱 Mobil Bilgileri Güncelle
        const mobilePkgDesc = document.getElementById('mobilePkgDesc');
        if (mobilePkgDesc) mobilePkgDesc.innerHTML = data.tag;

        const mobileTablePkg = document.getElementById('mobileTablePkgName');
        if (mobileTablePkg) mobileTablePkg.textContent = data.name;

        const mobileSpecPaper = document.getElementById('mobileSpecPaper');
        if (mobileSpecPaper) mobileSpecPaper.textContent = data.specPaper;

        const mobileSpecLam = document.getElementById('mobileSpecLamination');
        if (mobileSpecLam) mobileSpecLam.textContent = data.specLamination;

        const mobileSpecCor = document.getElementById('mobileSpecCorners');
        if (mobileSpecCor) mobileSpecCor.textContent = data.specCorners;

        const mobileFullDesc = document.getElementById('mobileDynamicFullDesc');
        if (mobileFullDesc) mobileFullDesc.innerHTML = data.fullDesc;
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

window.productGalleryList = <?= json_encode(array_values(array_map(function($g) { return str_starts_with($g, 'http') ? $g : SITE_URL . '/' . $g; }, $product['gallery_array'] ?? []))) ?>;
window.currentFullscreenIdx = 0;

window.openFullscreenImage = function(imgUrl) {
    const modalEl = document.getElementById('imageFullscreenModal');
    if (!modalEl) return;
    
    if (!window.productGalleryList || window.productGalleryList.length === 0) {
        window.productGalleryList = [imgUrl];
    }
    
    let idx = window.productGalleryList.indexOf(imgUrl);
    if (idx === -1) idx = 0;
    window.currentFullscreenIdx = idx;
    
    updateFullscreenModalView();
    
    if (window.bootstrap && bootstrap.Modal) {
        const modal = bootstrap.Modal.getOrCreateInstance ? bootstrap.Modal.getOrCreateInstance(modalEl) : new bootstrap.Modal(modalEl);
        modal.show();
    }
};

window.navigateFullscreenGallery = function(dir) {
    if (!window.productGalleryList || window.productGalleryList.length === 0) return;
    window.currentFullscreenIdx = (window.currentFullscreenIdx + dir + window.productGalleryList.length) % window.productGalleryList.length;
    updateFullscreenModalView();
};

function updateFullscreenModalView() {
    const imgEl = document.getElementById('fullscreenModalImg');
    const counterEl = document.getElementById('fullscreenModalCounter');
    if (window.productGalleryList && window.productGalleryList[window.currentFullscreenIdx]) {
        const url = window.productGalleryList[window.currentFullscreenIdx];
        if (imgEl) imgEl.src = url;
        if (counterEl) counterEl.innerHTML = `<i class="bi bi-image me-1"></i> Görsel ${window.currentFullscreenIdx + 1} / ${window.productGalleryList.length}`;
    }
}

// Klavye ok tuşları ile tam ekran geçişi
document.addEventListener('keydown', function(e) {
    const modalEl = document.getElementById('imageFullscreenModal');
    if (modalEl && modalEl.classList.contains('show')) {
        if (e.key === 'ArrowLeft') window.navigateFullscreenGallery(-1);
        if (e.key === 'ArrowRight') window.navigateFullscreenGallery(1);
    }
});

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
    const widthInput = document.getElementById('customWidth');
    const heightInput = document.getElementById('customHeight');
    if (row) row.style.display = isCustom ? 'flex' : 'none';
    if (!isCustom && widthInput && heightInput) {
        widthInput.value = '<?= $product['standard_width'] ?>';
        heightInput.value = '<?= $product['standard_height'] ?>';
        widthInput.dispatchEvent(new Event('input'));
    }
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

document.addEventListener('DOMContentLoaded', function() {
    // Manuel Özel Adet Dinleyicisi
    const manualQtyInput = document.getElementById('manualCustomQtyInput');
    const customRadio = document.getElementById('customQtyRadio');
    if (manualQtyInput && customRadio) {
        manualQtyInput.addEventListener('input', function() {
            const val = parseInt(this.value) || 10;
            customRadio.value = val;
            customRadio.checked = true;
            customRadio.dispatchEvent(new Event('change'));
        });
    }

    // İlk aktif paketi başlat
    const initPkgCard = document.getElementById('card_pkg_<?= $firstPkgKey ?>');
    if (initPkgCard) {
        selectPackage('<?= $firstPkgKey ?>', initPkgCard);
    }
    const navButtons = document.querySelectorAll('.design-nav-btn');
    const tabPanes = document.querySelectorAll('.design-tab-pane');
    const designTypeInput = document.getElementById('designTypeInput');

    navButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            navButtons.forEach(b => b.classList.remove('active'));
            tabPanes.forEach(p => p.style.display = 'none');
            
            this.classList.add('active');
            const target = document.getElementById(this.dataset.tab);
            if (target) target.style.display = 'block';

            if (this.dataset.tab === 'tabUpload') {
                designTypeInput.value = 'uploaded';
            } else if (this.dataset.tab === 'tabAiVector') {
                designTypeInput.value = 'ai_generated';
            } else if (this.dataset.tab === 'tabCanva') {
                designTypeInput.value = 'canva_studio';
            } else if (this.dataset.tab === 'tabTemplate') {
                designTypeInput.value = 'online_editor';
            } else if (this.dataset.tab === 'tabSupport') {
                designTypeInput.value = 'design_request';
            }
        });
    });

    window.availableTemplates = <?= json_encode($product['templates'] ?? []) ?>;
    window.availableIndustries = <?= json_encode($industries ?? []) ?>;

    // 🎨 Canva Studio Açılış Fonksiyonu
    window.openCanvaStudio = function(templateKey) {
        const modalEl = document.getElementById('canvaStudioModal');
        if (!modalEl) return;

        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        // Modal tamamen açıldığında Canvas'ı başlat (Görünürlük ve boyut hesaplaması için)
        modalEl.addEventListener('shown.bs.modal', function onModalShown() {
            modalEl.removeEventListener('shown.bs.modal', onModalShown);
            
            // Ürünün standart ebatı veya özel girilen ebat
            let stdW = parseFloat('<?= $product['standard_width'] ?? 8.4 ?>') || 8.4;
            let stdH = parseFloat('<?= $product['standard_height'] ?? 5.2 ?>') || 5.2;

            const customW = parseFloat(document.getElementById('customWidth')?.value);
            const customH = parseFloat(document.getElementById('customHeight')?.value);
            if (!isNaN(customW) && customW > 0 && !isNaN(customH) && customH > 0) {
                stdW = customW;
                stdH = customH;
            }

            // Çift Yön / Tek Yön Tespiti (Paket ve Opsiyonlara Göre)
            const activePkg = window.currentSelectedPackageKey || document.querySelector('input[name="selected_package"]:checked')?.value || 'standart';
            let isDoubleSided = (activePkg !== 'ekonomik');

            // Opsiyonlarda 'tek' veya 'tek yön' seçildiyse çift yönü kapat
            document.querySelectorAll('#printConfigForm select, #printConfigForm input:checked').forEach(el => {
                const text = (el.selectedOptions ? el.selectedOptions[0]?.text : (el.nextElementSibling?.textContent || el.value || '')).toLowerCase();
                if (text.includes('tek yön') || text.includes('tek taraf')) {
                    isDoubleSided = false;
                } else if (text.includes('çift yön') || text.includes('çift taraf') || text.includes('arkalı önlü')) {
                    isDoubleSided = true;
                }
            });

            const aspect = stdW / stdH;
            let canvasW = 850;
            let canvasH = Math.round(canvasW / aspect);

            if (aspect < 0.7) {
                // Dikey ürünler için (Örn: Yelken Bayrak, Rollup, Dikey Broşür)
                canvasH = 650;
                canvasW = Math.round(canvasH * aspect);
            }

            CanvaStudio.init({
                width: canvasW,
                height: canvasH,
                widthCm: stdW,
                heightCm: stdH,
                isDoubleSided: isDoubleSided,
                productName: <?= json_encode($product['name'] ?? 'Matbaa Ürünü') ?>,
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
            const wCm = CanvaStudio.widthCm || parseFloat('<?= $product['standard_width'] ?? 8.4 ?>') || 8.4;
            const hCm = CanvaStudio.heightCm || parseFloat('<?= $product['standard_height'] ?? 5.2 ?>') || 5.2;
            previewCard.style.aspectRatio = `${wCm} / ${hCm}`;
            if (wCm < hCm) {
                previewCard.style.maxWidth = '210px';
            } else {
                previewCard.style.maxWidth = '360px';
            }
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
        const hiddenSvgInput = document.getElementById('selectedDesignSvg');
        if (hiddenSvgInput) hiddenSvgInput.value = '';
        const hiddenBackSvgInput = document.getElementById('selectedDesignBackSvg');
        if (hiddenBackSvgInput) hiddenBackSvgInput.value = '';
    };

    window.activateDesignSupportTab = function() {
        const supTabBtn = document.querySelector('.design-nav-btn[data-tab="tabSupport"]');
        if (supTabBtn) supTabBtn.click();
        const supChk = document.getElementById('includeDesignService');
        if (supChk && !supChk.checked) {
            supChk.checked = true;
            supChk.dispatchEvent(new Event('change'));
        }
    };

    // Sürükle Bırak Dosya Yükleme (Ajax) & Çözünürlük Kontrolü
    const dropzone = document.getElementById('dropzoneBox');
    const fileInput = document.getElementById('fileUploadInput');
    const uploadStatus = document.getElementById('uploadStatus');
    const preflightBox = document.getElementById('preflightAlertBox');
    const designFileInput = document.getElementById('selectedDesignFile');

    if (dropzone && fileInput) {
        dropzone.addEventListener('click', () => fileInput.click());

        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('dragover');
        });

        dropzone.addEventListener('dragleave', () => dropzone.classList.remove('dragover'));

        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                handleFileUpload(e.dataTransfer.files[0]);
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length) {
                handleFileUpload(fileInput.files[0]);
            }
        });
    }

    function handleFileUpload(file) {
        uploadStatus.style.display = 'block';
        uploadStatus.className = 'mt-2 small text-primary fw-bold';
        uploadStatus.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Dosya yükleniyor: ' + file.name;

        const formData = new FormData();
        formData.append('file', file);

        fetch(SITE_URL + '/api/upload_design.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                uploadStatus.className = 'mt-2 small text-success fw-bold';
                uploadStatus.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Dosya başarıyla yüklendi: ' + data.original_name;
                designFileInput.value = data.file_path;
                designTypeInput.value = 'uploaded';
            } else {
                uploadStatus.className = 'mt-2 small text-danger fw-bold';
                uploadStatus.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> ' + (data.error || 'Yükleme başarısız.');
            }
        })
        .catch(err => {
            uploadStatus.className = 'mt-2 small text-danger fw-bold';
            uploadStatus.innerHTML = 'Sunucu bağlantı hatası.';
        });
    }

    // Online Vektörel Şablon Düzenleyici Modalı
    document.querySelectorAll('.open-editor-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const template = JSON.parse(this.dataset.template);
            openCanvaStudio(template.default_svg || null);
        });
    });

    // Sektör / Meslek Dropdown Değişimi
    window.onIndustrySelectChange = function(slug) {
        const select = document.getElementById('industrySelectDropdown');
        const selectedText = select ? select.options[select.selectedIndex]?.text : '';
        const titleEl = document.getElementById('selectedIndustryNoticeTitle');
        if (titleEl && selectedText && slug !== 'all') {
            titleEl.textContent = selectedText + ' İçin Şablonlar';
        } else if (titleEl) {
            titleEl.textContent = 'Sektöre Özel Şablonlar';
        }
        
        let visibleCount = 0;
        document.querySelectorAll('.template-card-wrapper').forEach(card => {
            const cardInd = card.dataset.industry;
            if (slug === 'all' || cardInd === slug) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        const noticeBox = document.getElementById('noTemplatesNoticeBox');
        if (noticeBox) {
            noticeBox.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    };

    // Sektöre Göre Şablon Filtreleme
    window.filterTemplatesByIndustry = function(slug, btnElement) {
        document.querySelectorAll('.industry-filter-btn').forEach(b => {
            b.classList.remove('btn-primary', 'active');
            b.classList.add('btn-outline-secondary');
        });
        if (btnElement) {
            btnElement.classList.remove('btn-outline-secondary');
            btnElement.classList.add('btn-primary', 'active');
        }

        let visibleCount = 0;
        document.querySelectorAll('.template-card-wrapper').forEach(card => {
            const cardInd = card.dataset.industry;
            if (slug === 'all' || cardInd === slug) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        const badge = document.getElementById('templateCountBadge');
        if (badge) badge.textContent = visibleCount + ' Şablon';

        const noAlert = document.getElementById('noTemplatesAlert');
        if (noAlert) noAlert.style.display = visibleCount === 0 ? 'block' : 'none';

        // Sektör önerilerini de güncelle
        if (slug !== 'all') {
            const indName = btnElement ? btnElement.textContent.trim() : slug;
            loadIndustryRecommendations(slug, indName);
        }
    };

    // Sektörel Çapraz Ürün Önerilerini Getir
    window.loadIndustryRecommendations = function(slug, name) {
        localStorage.setItem('user_selected_industry', slug);
        localStorage.setItem('user_selected_industry_name', name);

        const titleEl = document.getElementById('crossSellIndustryTitle');
        const dropBtn = document.getElementById('activeIndustryDropdownBtn');
        if (titleEl) titleEl.textContent = name + ' İçin Önerilen Tamamlayıcı Ürünler';
        if (dropBtn) dropBtn.innerHTML = '<i class="bi bi-check2-circle text-success me-1"></i> ' + name;

        const row = document.getElementById('industryRecommendationsRow');
        if (row) {
            row.style.opacity = '0.5';
            fetch(SITE_URL + '/api/get_industry_recommendations.php?industry=' + encodeURIComponent(slug) + '&exclude_product_id=<?= $product['id'] ?>')
                .then(res => res.json())
                .then(data => {
                    row.style.opacity = '1';
                    if (data.success && data.html) {
                        row.innerHTML = data.html;
                    }
                })
                .catch(err => {
                    row.style.opacity = '1';
                });
        }
    };

    // Varsa hafızadaki sektörü yükle
    const savedInd = localStorage.getItem('user_selected_industry');
    const savedIndName = localStorage.getItem('user_selected_industry_name');
    if (savedInd && savedIndName) {
        const indBtn = document.querySelector('.industry-filter-btn[data-industry="' + savedInd + '"]');
        if (indBtn) {
            filterTemplatesByIndustry(savedInd, indBtn);
        } else {
            loadIndustryRecommendations(savedInd, savedIndName);
        }
    }

    if (window.PackageShowcase) {
        PackageShowcase.init();
        PackageShowcase.setPackage('<?= $firstPkgKey ?>');
    }

    <?php if ($selectedTplId): ?>
        const autoBtn = document.querySelector('.open-editor-btn');
        if (autoBtn) {
            setTimeout(() => autoBtn.click(), 400);
        }
    <?php endif; ?>
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
