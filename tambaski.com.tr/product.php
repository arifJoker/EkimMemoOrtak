<?php
/**
 * TAMBASKI.COM.TR - Ürün Detay & Online Vektör Stüdyosu (1:1 Precision Studio Print)
 */
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? 'ekonomik-kartvizit-250gr';
$product = get_product_by_slug($slug);

if (!$product) {
    // Fallback default mock
    $product = [
        'id' => 1,
        'title' => 'Ultra Prestij Kartvizit (Özel Dokulu & Kabartma Laklı)',
        'slug' => 'ekonomik-kartvizit-250gr',
        'category_name' => 'Kartvizit',
        'category_slug' => 'kartvizit',
        'pricing_type' => 'fixed_tier',
        'base_price' => 450,
        'description' => '350gr Mat Selefon Kaplama & Bölgesel Parlak Kabartma Lak Kombinasyonu',
        'features' => json_encode([
            'Kağıt Cinsi' => '350 gr/m² Birinci Sınıf İthal Mat Kuşe',
            'Bitmiş Ebat' => '84 x 52 mm (Tasarım Alanı: 86 x 54 mm)',
            'Baskı Yönü' => 'Çift Yön 4+4 CMYK Full Color Ofset',
            'Yüzey Kaplama' => 'Ön & Arka İpeksi Mat Koruyucu Termal Selefon',
            'Özel İşçilik' => 'Ön ve/veya Arka Yüz Bölgesel Kabartma Lak (Spot UV)'
        ])
    ];
}

$page_title = $product['title'] . " | TAM BASKI STUDIO";
$page_desc = $product['description'] ?? 'Kurumsal prestijli matbaa ve online tasarım editörü.';
require_once __DIR__ . '/includes/header.php';
?>

<!-- SECONDARY CATEGORY STRIP / BREADCRUMB -->
<div class="bg-surface-container-low border-b border-outline-variant text-xs py-2.5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between">
        <nav class="flex items-center gap-2 text-on-surface-variant font-medium">
            <a class="hover:text-primary transition-colors" href="index.php">Anasayfa</a>
            <span class="text-outline text-xs">/</span>
            <a class="hover:text-primary transition-colors" href="category.php?slug=<?= urlencode($product['category_slug'] ?? 'kartvizit') ?>"><?= htmlspecialchars($product['category_name'] ?? 'Kurumsal Kimlik') ?></a>
            <span class="text-outline text-xs">/</span>
            <span class="text-primary font-semibold"><?= htmlspecialchars($product['title']) ?></span>
        </nav>
        <div class="hidden sm:flex items-center gap-4 text-xs text-on-surface-variant font-medium">
            <span class="flex items-center gap-1 text-emerald-700 font-semibold"><span class="material-symbols-outlined text-sm">verified</span> %100 Heidelberg Ofset Garantisi</span>
            <span>•</span>
            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">schedule</span> 24 Saatte Hızlı Üretim</span>
        </div>
    </div>
</div>

<!-- MAIN PDP CONTAINER -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 py-8 flex-1 w-full">
    <!-- PRODUCT DETAIL GRID (2-Column Studio Architecture) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 xl:gap-12 items-start">
        
        <!-- LEFT COLUMN: Sticky 3D Visual Stage & Specs (Cols 1-7) -->
        <section class="lg:col-span-7 flex flex-col gap-6 lg:sticky lg:top-20">
            <!-- Floating Feature Badges -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-surface-container-high text-on-surface border border-outline-variant">
                    <span>💧</span> Su &amp; Nem Korumalı
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-secondary-fixed text-on-secondary-fixed border border-secondary/20">
                    <span>🎨</span> Online Vektörel Tasarımlı
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                    <span>⚡</span> 24 Saatte Kargoda
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-surface-container-high text-on-surface border border-outline-variant">
                    <span>🚚</span> Ücretsiz Kargo (750₺+)
                </span>
            </div>

            <!-- 3D Interactive Stage Card -->
            <div class="relative bg-gradient-to-b from-white to-surface-container-low rounded-2xl border border-outline-variant p-6 sm:p-10 overflow-hidden shadow-sm">
                <!-- Dynamic Corner Badges -->
                <div class="absolute top-4 left-4 z-20">
                    <span class="bg-surface-container-lowest/90 backdrop-blur-md text-on-surface border border-outline-variant text-[11px] font-semibold px-2.5 py-1 rounded-lg shadow-xs">
                        350 gr/m² Kuşe + Mat Selefon
                    </span>
                </div>
                <div class="absolute top-4 right-4 z-20">
                    <span class="bg-primary/90 backdrop-blur-md text-on-primary text-[11px] font-medium px-2.5 py-1 rounded-lg shadow-xs flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-secondary animate-pulse"></span>
                        Ön-Arka Kısmi Lak (Kabartma)
                    </span>
                </div>

                <!-- Tilt Visualizer Canvas Stage -->
                <div class="perspective-stage w-full h-[320px] sm:h-[380px] flex items-center justify-center cursor-grab active:cursor-grabbing my-4" id="tiltStage">
                    <!-- Business Card 3D Object -->
                    <div class="tilt-card relative w-[310px] sm:w-[420px] h-[190px] sm:h-[260px] rounded-xl bg-[#0b1329] border border-slate-700/60 p-6 flex flex-col justify-between overflow-hidden shadow-2xl select-none" id="tiltCard">
                        <!-- Subtle Paper Texture Overlay & Glare -->
                        <div class="absolute inset-0 opacity-15 pointer-events-none mix-blend-screen bg-repeat" style="background-image: radial-gradient(#94a3b8 0.75px, transparent 0.75px); background-size: 6px 6px;"></div>
                        <div class="glare-overlay absolute inset-0 z-10 transition-opacity duration-150" id="cardGlare"></div>
                        
                        <!-- Card Content: FRONT FACE -->
                        <div class="relative z-10 flex justify-between items-start">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-lg bg-slate-900 border border-amber-500/40 flex items-center justify-center spot-uv-gloss shadow-inner">
                                    <span class="gold-foil-text font-black text-lg">N</span>
                                </div>
                                <div>
                                    <h3 class="gold-foil-text font-bold tracking-wider text-sm sm:text-base">NEXUS</h3>
                                    <p class="text-[9px] sm:text-[10px] tracking-widest text-slate-300 font-label-caps">ARCHITECTURE / STUDIO</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-label-numeric text-amber-300/80 bg-slate-900/80 px-2 py-0.5 rounded border border-amber-500/30">PARİS • İSTANBUL</span>
                            </div>
                        </div>

                        <!-- Card Center Spot UV graphic details -->
                        <div class="relative z-10 my-auto py-2">
                            <p class="text-[11px] text-slate-300 font-light tracking-wide">Yenilikçi Yaşam Alanları &amp; Kentsel Dönüşüm</p>
                            <div class="w-16 h-0.5 bg-gradient-to-r from-amber-400 to-transparent mt-1"></div>
                        </div>

                        <!-- Card Bottom Details -->
                        <div class="relative z-10 flex justify-between items-end border-t border-slate-700/50 pt-3">
                            <div>
                                <h4 class="text-white text-xs sm:text-sm font-semibold tracking-tight" id="pdpOwnerName">Mert Yılmaz</h4>
                                <p class="text-[10px] text-amber-200/90 font-medium">Kurucu Ortak &amp; Baş Mimar</p>
                            </div>
                            <div class="text-right text-[9px] sm:text-[10px] font-label-numeric text-slate-300 space-y-0.5">
                                <p class="flex items-center justify-end gap-1"><span class="text-amber-400">+90 212 555 0192</span></p>
                                <p>mert@nexusmimarlik.com</p>
                                <p class="text-slate-400">Levent Loft No:14 Beşiktaş</p>
                            </div>
                        </div>

                        <!-- Embossed Badge Tag in Card -->
                        <div class="absolute bottom-2 left-1/2 -translate-x-1/2 opacity-30 text-[8px] font-label-numeric tracking-widest text-slate-400 pointer-events-none">
                            350 GSM VELVET MATTE • SPOT UV FINISH
                        </div>
                    </div>
                </div>

                <!-- Bottom Action Controls inside Mockup Stage -->
                <div class="flex flex-wrap items-center justify-between pt-3 border-t border-outline-variant/60 text-xs gap-2">
                    <div class="flex items-center gap-1.5 text-on-surface-variant font-medium">
                        <span class="material-symbols-outlined text-base text-secondary">explore</span>
                        <span>Farenizi hareket ettirerek 3D ışık yansımasını inceleyin</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button class="inline-flex items-center gap-1 px-3 py-1.5 bg-surface-container-lowest hover:bg-surface-container text-on-surface font-semibold rounded-lg border border-outline-variant shadow-xs transition-all" onclick="flipCardDemo()">
                            <span class="material-symbols-outlined text-base">flip_camera_android</span>
                            <span>360° Çevir</span>
                        </button>
                        <button class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-primary text-on-primary font-semibold rounded-lg shadow-xs hover:bg-slate-800 transition-all" onclick="openStudioModal()">
                            <span class="material-symbols-outlined text-base text-amber-300">brush</span>
                            <span>Online Tasarla</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Technical Specification Grid -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-5 shadow-xs">
                <h3 class="text-xs font-bold text-primary uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary text-lg">verified_user</span>
                    Üst Segment Üretim Standartları
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-3 rounded-xl bg-surface-container-low border border-outline-variant/60 flex items-start gap-3">
                        <span class="material-symbols-outlined text-secondary text-xl">water_drop</span>
                        <div>
                            <h4 class="text-xs font-bold text-primary">Su &amp; Nem Geçirmez</h4>
                            <p class="text-[11px] text-on-surface-variant mt-0.5">Mat selefon kaplama ile sıvı temasında deforme olmaz.</p>
                        </div>
                    </div>
                    <div class="p-3 rounded-xl bg-surface-container-low border border-outline-variant/60 flex items-start gap-3">
                        <span class="material-symbols-outlined text-secondary text-xl">fitness_center</span>
                        <div>
                            <h4 class="text-xs font-bold text-primary">Bükülmez 350gr</h4>
                            <p class="text-[11px] text-on-surface-variant mt-0.5">İthal sert kuşe mukavemeti ile tok ve dik duruş.</p>
                        </div>
                    </div>
                    <div class="p-3 rounded-xl bg-surface-container-low border border-outline-variant/60 flex items-start gap-3">
                        <span class="material-symbols-outlined text-secondary text-xl">center_focus_strong</span>
                        <div>
                            <h4 class="text-xs font-bold text-primary">300 DPI Ultra HD</h4>
                            <p class="text-[11px] text-on-surface-variant mt-0.5">Heidelberg Speedmaster ofset ile sıfır nokta kayması.</p>
                        </div>
                    </div>
                    <div class="p-3 rounded-xl bg-surface-container-low border border-outline-variant/60 flex items-start gap-3">
                        <span class="material-symbols-outlined text-secondary text-xl">bolt</span>
                        <div>
                            <h4 class="text-xs font-bold text-primary">24 Saatte Hızlı</h4>
                            <p class="text-[11px] text-on-surface-variant mt-0.5">Otomasyonlu hat ile ekspres prova ve ertesi gün kargo.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Technical Specs Table -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant overflow-hidden shadow-xs">
                <div class="px-5 py-3 bg-surface-container border-b border-outline-variant flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-on-surface">Teknik Parametreler</span>
                    <span class="text-[11px] font-label-numeric text-on-surface-variant font-medium">ISO 216 &amp; DIN Format</span>
                </div>
                <table class="w-full text-left text-xs">
                    <tbody class="divide-y divide-outline-variant/60">
                        <tr class="hover:bg-surface-container-low">
                            <td class="px-5 py-2.5 font-medium text-on-surface-variant w-1/3">Kağıt Cinsi</td>
                            <td class="px-5 py-2.5 font-semibold text-on-surface">350 gr/m² Birinci Sınıf İthal Mat Kuşe</td>
                        </tr>
                        <tr class="hover:bg-surface-container-low bg-surface-container-low/40">
                            <td class="px-5 py-2.5 font-medium text-on-surface-variant">Bitmiş Ebat</td>
                            <td class="px-5 py-2.5 font-semibold text-on-surface">84 x 52 mm (Tasarım Alanı: 86 x 54 mm Kesim Paylı)</td>
                        </tr>
                        <tr class="hover:bg-surface-container-low">
                            <td class="px-5 py-2.5 font-medium text-on-surface-variant">Baskı Yönü</td>
                            <td class="px-5 py-2.5 font-semibold text-on-surface">Çift Yön 4+4 CMYK Full Color Ofset</td>
                        </tr>
                        <tr class="hover:bg-surface-container-low bg-surface-container-low/40">
                            <td class="px-5 py-2.5 font-medium text-on-surface-variant">Yüzey Kaplama</td>
                            <td class="px-5 py-2.5 font-semibold text-on-surface">Ön &amp; Arka İpeksi Mat Koruyucu Termal Selefon</td>
                        </tr>
                        <tr class="hover:bg-surface-container-low">
                            <td class="px-5 py-2.5 font-medium text-on-surface-variant">Özel İşçilik</td>
                            <td class="px-5 py-2.5 font-semibold text-on-surface">Ön ve/veya Arka Yüz Bölgesel Kabartma Lak (Spot UV)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- RIGHT COLUMN: Stepped PDP Configurator & Pricing Engine (Cols 8-12) -->
        <section class="lg:col-span-5 flex flex-col gap-6">
            <!-- Header Info -->
            <div class="border-b border-outline-variant pb-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-amber-100 text-amber-800 text-[11px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wider flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">hotel_class</span> Prestij Koleksiyonu
                    </span>
                    <span class="text-on-surface-variant text-xs flex items-center gap-1">
                        <span class="text-amber-500 font-bold">★ 4.9</span> (340+ Onaylı Sipariş)
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-primary tracking-tight leading-tight">
                    <?= htmlspecialchars($product['title']) ?>
                </h1>
                <p class="text-on-surface-variant text-xs sm:text-sm mt-1">
                    <?= htmlspecialchars($product['description'] ?? '350gr Mat Selefon Kaplama & Bölgesel Parlak Kabartma Lak Kombinasyonu') ?>
                </p>
            </div>

            <form action="cart.php" method="POST" id="pdpConfigForm">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['title']) ?>">
                <input type="hidden" name="package" id="hiddenPackage" value="standart">
                <input type="hidden" name="quantity" id="hiddenQty" value="3000">
                <input type="hidden" name="price" id="hiddenPrice" value="945">

                <!-- STEP 1: BASKI PAKETİ SEÇİMİ -->
                <div class="bg-surface-container-lowest p-5 rounded-2xl border border-outline-variant shadow-sm mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-primary text-on-primary flex items-center justify-center text-xs font-label-numeric font-bold">01</span>
                            <h2 class="text-xs sm:text-sm font-bold text-primary">Baskı Paketini Belirleyin</h2>
                        </div>
                        <a href="index.php#sample-kit" class="text-[11px] text-secondary font-semibold hover:underline">Numune İste</a>
                    </div>
                    <div class="grid grid-cols-2 gap-3" id="packageOptionsContainer">
                        <!-- Paket 1: Ekonomik -->
                        <div class="package-card cursor-pointer border border-outline-variant rounded-xl p-3.5 hover:border-outline transition-all relative" id="pkg-ekonomik" onclick="selectPackage('ekonomik', 0)">
                            <span class="text-xs font-bold text-on-surface block">Ekonomik</span>
                            <span class="text-[11px] text-on-surface-variant block mt-0.5 leading-tight">300gr Kuşe, Tek Yön</span>
                            <div class="mt-2 text-xs font-label-numeric font-semibold text-on-surface">+0 ₺</div>
                        </div>
                        <!-- Paket 2: Standart (Default Active) -->
                        <div class="package-card cursor-pointer border-2 border-secondary bg-blue-50/40 rounded-xl p-3.5 transition-all relative" id="pkg-standart" onclick="selectPackage('standart', 140)">
                            <div class="absolute -top-2.5 right-2 bg-emerald-600 text-white text-[9px] font-bold px-2 py-0.5 rounded-full shadow-xs">
                                EN ÇOK TERCİH EDİLEN
                            </div>
                            <span class="text-xs font-bold text-primary block">Standart</span>
                            <span class="text-[11px] text-on-surface-variant block mt-0.5 leading-tight">350gr Kuşe, Çift Yön, Mat Selefon</span>
                            <div class="mt-2 text-xs font-label-numeric font-bold text-secondary">+140 ₺</div>
                        </div>
                        <!-- Paket 3: Premium -->
                        <div class="package-card cursor-pointer border border-outline-variant rounded-xl p-3.5 hover:border-outline transition-all relative" id="pkg-premium" onclick="selectPackage('premium', 290)">
                            <span class="text-xs font-bold text-on-surface block">Premium</span>
                            <span class="text-[11px] text-on-surface-variant block mt-0.5 leading-tight">350gr, Çift Yön, Kabartma Lak</span>
                            <div class="mt-2 text-xs font-label-numeric font-semibold text-on-surface">+290 ₺</div>
                        </div>
                        <!-- Paket 4: VIP Prestij -->
                        <div class="package-card cursor-pointer border border-outline-variant rounded-xl p-3.5 hover:border-outline transition-all relative" id="pkg-vip" onclick="selectPackage('vip', 550)">
                            <span class="text-xs font-bold text-on-surface block">VIP Prestij</span>
                            <span class="text-[11px] text-on-surface-variant block mt-0.5 leading-tight">Plastik PVC + Altın Yaldız</span>
                            <div class="mt-2 text-xs font-label-numeric font-semibold text-on-surface">+550 ₺</div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: BASKI ADEDİ SEÇİMİ -->
                <div class="bg-surface-container-lowest p-5 rounded-2xl border border-outline-variant shadow-sm mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-primary text-on-primary flex items-center justify-center text-xs font-label-numeric font-bold">02</span>
                            <h2 class="text-xs sm:text-sm font-bold text-primary">Baskı Adedini Belirleyin</h2>
                        </div>
                        <span class="text-[10px] font-label-numeric text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded font-bold border border-emerald-200">
                            %40'a Varan İndirim
                        </span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5" id="qtyContainer">
                        <button type="button" class="qty-btn text-left p-3 rounded-xl border border-outline-variant hover:border-outline transition-all" onclick="selectQty(1000, 450, 0.45)">
                            <div class="text-xs font-bold text-on-surface">1.000 Adet</div>
                            <div class="text-xs font-label-numeric font-semibold text-primary mt-1">450 ₺</div>
                            <div class="text-[10px] text-on-surface-variant font-label-numeric">0.45 ₺ / Adet</div>
                        </button>
                        <button type="button" class="qty-btn text-left p-3 rounded-xl border border-outline-variant hover:border-outline transition-all" onclick="selectQty(2000, 720, 0.36)">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-on-surface">2.000 Adet</span>
                                <span class="text-[9px] font-bold text-emerald-700 bg-emerald-100 px-1 rounded">%20</span>
                            </div>
                            <div class="text-xs font-label-numeric font-semibold text-primary mt-1">720 ₺</div>
                            <div class="text-[10px] text-on-surface-variant font-label-numeric">0.36 ₺ / Adet</div>
                        </button>
                        <button type="button" class="qty-btn text-left p-3 rounded-xl border-2 border-secondary bg-blue-50/40 transition-all" id="qty-3000-active" onclick="selectQty(3000, 945, 0.315)">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-secondary">3.000 Adet</span>
                                <span class="text-[9px] font-bold text-emerald-700 bg-emerald-100 px-1 rounded">%30</span>
                            </div>
                            <div class="text-xs font-label-numeric font-bold text-secondary mt-1">945 ₺</div>
                            <div class="text-[10px] text-secondary font-label-numeric font-medium">0.315 ₺ / Adet</div>
                        </button>
                        <button type="button" class="qty-btn text-left p-3 rounded-xl border border-outline-variant hover:border-outline transition-all" onclick="selectQty(5000, 1350, 0.27)">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-on-surface">5.000 Adet</span>
                                <span class="text-[9px] font-bold text-emerald-700 bg-emerald-100 px-1 rounded">Avantaj</span>
                            </div>
                            <div class="text-xs font-label-numeric font-semibold text-primary mt-1">1.350 ₺</div>
                            <div class="text-[10px] text-on-surface-variant font-label-numeric">0.27 ₺ / Adet</div>
                        </button>
                        <button type="button" class="qty-btn text-left p-3 rounded-xl border border-outline-variant hover:border-outline transition-all" onclick="selectQty(10000, 2250, 0.225)">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-on-surface">10.000 Adet</span>
                                <span class="text-[9px] font-bold text-emerald-700 bg-emerald-100 px-1 rounded">Toptan</span>
                            </div>
                            <div class="text-xs font-label-numeric font-semibold text-primary mt-1">2.250 ₺</div>
                            <div class="text-[10px] text-on-surface-variant font-label-numeric">0.225 ₺ / Adet</div>
                        </button>
                        <button type="button" class="p-3 rounded-xl border border-dashed border-outline-variant hover:border-secondary flex flex-col items-center justify-center text-center transition-all bg-surface-container-low" onclick="openCustomQtyPrompt()">
                            <span class="material-symbols-outlined text-lg text-secondary">tune</span>
                            <span class="text-[11px] font-bold text-primary mt-0.5">Özel Adet Gir</span>
                        </button>
                    </div>

                    <!-- İnce Ayarlar Dropdowns -->
                    <div class="mt-4 pt-4 border-t border-outline-variant space-y-2.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-on-surface-variant flex items-center gap-1.5 font-medium">
                                <span class="material-symbols-outlined text-base">rounded_corner</span> Köşe Kesimi:
                            </span>
                            <select class="text-xs font-semibold bg-surface border border-outline-variant rounded-lg px-2.5 py-1.5 text-on-surface focus:outline-none focus:border-secondary" id="cornerCutSelect" onchange="calculateGrandTotal()">
                                <option value="0">Düz Kesim (Standart) - 0 ₺</option>
                                <option value="40">4 Köşe Oval Kesim (+40 ₺)</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-on-surface-variant flex items-center gap-1.5 font-medium">
                                <span class="material-symbols-outlined text-base">local_shipping</span> Üretim Hızı:
                            </span>
                            <select class="text-xs font-semibold bg-surface border border-outline-variant rounded-lg px-2.5 py-1.5 text-on-surface focus:outline-none focus:border-secondary" id="shippingSpeedSelect" onchange="calculateGrandTotal()">
                                <option value="0">Standart Süreç (3 İş Günü) - 0 ₺</option>
                                <option value="95">Ekspres 24 Saat Üretim (+95 ₺)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: TASARIM YÖNTEMİ SEÇİMİ -->
                <div class="bg-surface-container-lowest p-5 rounded-2xl border border-outline-variant shadow-sm mb-6">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-6 h-6 rounded-full bg-primary text-on-primary flex items-center justify-center text-xs font-label-numeric font-bold">03</span>
                        <h2 class="text-xs sm:text-sm font-bold text-primary">Tasarımınızı Belirleyin</h2>
                    </div>
                    <!-- Switchable Tabs -->
                    <div class="flex rounded-xl bg-surface-container-low p-1 gap-1 mb-4">
                        <button type="button" class="flex-1 py-2 px-2 rounded-lg text-xs font-bold bg-surface-container-lowest shadow-xs text-primary transition-all flex items-center justify-center gap-1" id="tab-editor-btn" onclick="switchDesignTab('editor')">
                            <span class="material-symbols-outlined text-base text-secondary">brush</span>
                            <span>Kendin Tasarla</span>
                        </button>
                        <button type="button" class="flex-1 py-2 px-2 rounded-lg text-xs font-medium text-on-surface-variant hover:text-primary transition-all flex items-center justify-center gap-1" id="tab-upload-btn" onclick="switchDesignTab('upload')">
                            <span class="material-symbols-outlined text-base">cloud_upload</span>
                            <span>Dosya Yükle</span>
                        </button>
                        <button type="button" class="flex-1 py-2 px-2 rounded-lg text-xs font-medium text-on-surface-variant hover:text-primary transition-all flex items-center justify-center gap-1" id="tab-support-btn" onclick="switchDesignTab('support')">
                            <span class="material-symbols-outlined text-base">support_agent</span>
                            <span>Grafik Desteği</span>
                        </button>
                    </div>
                    
                    <!-- Tab Content 1: Online Studio -->
                    <div class="space-y-3" id="tab-content-editor">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-secondary/30 rounded-xl p-4 text-center">
                            <div class="w-10 h-10 rounded-full bg-secondary text-white flex items-center justify-center mx-auto mb-2 shadow-md">
                                <span class="material-symbols-outlined text-xl">magic_button</span>
                            </div>
                            <h3 class="text-xs sm:text-sm font-bold text-primary">Online Vektörel Tasarım Editörü</h3>
                            <p class="text-xs text-on-surface-variant mt-1 max-w-xs mx-auto">
                                Heidelberg CMYK standartlarına tam uyumlu 420+ modern sektör şablonuyla kartvizitinizi tarayıcınızda 2 dakikada hazırlayın.
                            </p>
                            <button type="button" class="mt-3.5 inline-flex items-center gap-2 w-full justify-center bg-secondary hover:bg-blue-600 text-white py-2.5 px-4 rounded-xl font-bold text-xs tracking-wide shadow-md shadow-blue-500/20 transition-all active:scale-95" onclick="openStudioModal()">
                                <span class="material-symbols-outlined text-base">brush</span>
                                <span>Tasarım Editörünü Başlat (420+ Şablon Hazır)</span>
                            </button>
                        </div>
                    </div>

                    <!-- Tab Content 2: File Upload -->
                    <div class="hidden space-y-3" id="tab-content-upload">
                        <div class="border-2 border-dashed border-outline-variant hover:border-secondary rounded-xl p-6 text-center bg-surface transition-all cursor-pointer">
                            <span class="material-symbols-outlined text-3xl text-outline">upload_file</span>
                            <p class="text-xs font-bold text-primary mt-2">Baskıya hazır dosyanızı buraya sürükleyin veya seçin</p>
                            <p class="text-[11px] text-on-surface-variant mt-1">Desteklenen: PDF, AI, PSD, EPS, TIFF (Maks. 250 MB)</p>
                            <input type="file" name="print_file" class="hidden" id="pdpFileInput">
                            <button type="button" onclick="document.getElementById('pdpFileInput').click()" class="mt-3 px-3 py-1.5 bg-surface-container-high text-xs font-semibold rounded-lg text-on-surface hover:bg-surface-container">
                                Dosya Seç
                            </button>
                        </div>
                    </div>

                    <!-- Tab Content 3: Graphic Support -->
                    <div class="hidden space-y-3" id="tab-content-support">
                        <div class="bg-surface-container-low p-4 rounded-xl text-center">
                            <span class="material-symbols-outlined text-2xl text-emerald-600">forum</span>
                            <h3 class="text-xs font-bold text-primary mt-1">Baskı Tasarımcımız ile Canlı Görüşün</h3>
                            <p class="text-xs text-on-surface-variant mt-1">
                                Logonuzu ve bilgilerinizi iletin, uzman grafikerlerimiz onayınız için 3 farklı alternatif mockup hazırlasın.
                            </p>
                            <a class="mt-3 inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2 px-4 rounded-lg shadow-sm transition-all" href="https://wa.me/905440000000" target="_blank">
                                <span>WhatsApp ile Tasarımcıya Bağlan</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- STICKY SUMMARY & CHECKOUT DOCK -->
                <div class="bg-surface-container-lowest p-5 rounded-2xl border-2 border-primary/10 shadow-lg sticky bottom-4 z-30">
                    <div class="flex items-end justify-between mb-3 pb-3 border-b border-outline-variant">
                        <div>
                            <span class="text-xs text-on-surface-variant block">Toplam Tutar</span>
                            <div class="flex items-baseline gap-1.5 mt-0.5">
                                <span class="text-2xl font-extrabold font-label-numeric text-primary" id="grandTotalPrice">945,00 ₺</span>
                                <span class="text-[11px] text-on-surface-variant font-label-numeric" id="taxPriceBadge">+189 ₺ KDV Dahil</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-label-numeric font-bold text-secondary bg-blue-50 px-2 py-0.5 rounded border border-blue-200" id="unitPriceDisplay">
                                0.315 ₺ / Adet
                            </span>
                        </div>
                    </div>

                    <!-- Free Shipping Progress Meter -->
                    <div class="mb-4">
                        <div class="flex justify-between items-center text-xs mb-1">
                            <span class="font-bold text-emerald-800 flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">local_shipping</span>
                                750 ₺ üzeri Ücretsiz Kargo
                            </span>
                            <span class="text-[11px] font-label-numeric font-semibold text-on-surface-variant" id="shippingRemainingText">Kargo Bedava</span>
                        </div>
                        <div class="w-full bg-surface-container h-1.5 rounded-full overflow-hidden">
                            <div class="bg-emerald-600 h-full rounded-full transition-all duration-300" id="shippingProgressBar" style="width: 100%;"></div>
                        </div>
                    </div>

                    <!-- Checkout CTA -->
                    <button type="submit" class="w-full bg-primary hover:bg-slate-800 active:scale-98 text-on-primary py-3.5 px-6 rounded-xl font-bold text-sm tracking-wide shadow-lg shadow-black/10 flex items-center justify-center gap-2 transition-all">
                        <span class="material-symbols-outlined text-xl text-amber-300">shopping_bag</span>
                        <span>Sepete Ekle &amp; Tasarıma Devam Et</span>
                    </button>
                    <div class="flex items-center justify-center gap-4 text-[11px] text-on-surface-variant pt-2">
                        <span class="flex items-center gap-1"><span class="material-symbols-outlined text-xs text-emerald-600">lock</span> 256-Bit SSL Güvenli</span>
                        <span>•</span>
                        <span class="flex items-center gap-1"><span class="material-symbols-outlined text-xs text-emerald-600">verified</span> %100 Renk Garantisi</span>
                    </div>
                </div>
            </form>
        </section>
    </div>
</main>

<!-- ========================================================================= -->
<!-- FULL-SCREEN ONLINE VECTOR DESIGN TOOL MODAL (CANVAS CUSTOMIZER)           -->
<!-- ========================================================================= -->
<div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md hidden flex flex-col items-stretch overflow-hidden animate-fadeIn" id="studioCustomizerModal">
    <!-- Dark Studio Top Toolstrip -->
    <header class="h-14 bg-slate-900 border-b border-slate-800 px-4 flex items-center justify-between text-white shrink-0">
        <!-- Left: Exit & Title -->
        <div class="flex items-center gap-4">
            <button class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 rounded-lg text-xs font-semibold text-slate-200 transition-all border border-slate-700" onclick="closeStudioModal()">
                <span class="material-symbols-outlined text-base">arrow_back</span>
                <span>Ürüne Dön</span>
            </button>
            <div class="h-5 w-px bg-slate-800"></div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold tracking-tight text-white">TAM BASKI STÜDYO VEKTÖR</span>
                    <span class="text-[9px] bg-secondary text-white font-label-numeric font-bold px-1.5 py-0.5 rounded">V4.2</span>
                </div>
                <p class="text-[10px] text-slate-400 font-label-numeric">84.00 x 52.00 mm • CMYK ISO Coated v2 • 300 DPI Vector Artboard</p>
            </div>
        </div>

        <!-- Center: Front / Back Switcher -->
        <div class="flex items-center bg-slate-950 p-1 rounded-xl border border-slate-800">
            <button class="px-3.5 py-1 text-xs font-bold rounded-lg bg-secondary text-white shadow-sm transition-all flex items-center gap-1" id="btn-card-front" onclick="switchCardSide('front')">
                <span class="material-symbols-outlined text-sm">crop_landscape</span>
                <span>Ön Yüz (Logo &amp; Ünvan)</span>
            </button>
            <button class="px-3.5 py-1 text-xs font-medium rounded-lg text-slate-400 hover:text-white transition-all flex items-center gap-1" id="btn-card-back" onclick="switchCardSide('back')">
                <span class="material-symbols-outlined text-sm">flip</span>
                <span>Arka Yüz (QR &amp; İletişim)</span>
            </button>
        </div>

        <!-- Right Actions -->
        <div class="flex items-center gap-2.5">
            <button class="flex items-center gap-1 px-2.5 py-1.5 bg-slate-800 hover:bg-slate-700 rounded-lg text-xs font-medium text-slate-300 border border-slate-700" id="btnBleedToggle" onclick="toggleBleedGuides()">
                <span class="material-symbols-outlined text-base text-amber-400">square_foot</span>
                <span>Taşma Payı (Bleed)</span>
            </button>
            <button class="flex items-center gap-1.5 px-4 py-1.5 bg-secondary hover:bg-blue-600 text-white rounded-lg text-xs font-bold shadow-md shadow-blue-500/25 transition-all" onclick="applyAndCloseStudio()">
                <span class="material-symbols-outlined text-base">check_circle</span>
                <span>Tasarımı Onayla</span>
            </button>
        </div>
    </header>

    <!-- Customizer Studio Workspace -->
    <div class="flex-1 flex overflow-hidden relative">
        <!-- LEFT TOOLBAR -->
        <aside class="w-16 bg-slate-900 border-r border-slate-800 flex flex-col items-center py-3 gap-2 shrink-0 z-20">
            <button class="w-12 h-12 rounded-xl bg-slate-800 text-secondary flex flex-col items-center justify-center gap-1 transition-all" title="Şablonlar">
                <span class="material-symbols-outlined text-xl">dashboard</span>
                <span class="text-[9px] font-bold">Şablon</span>
            </button>
            <button class="w-12 h-12 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 flex flex-col items-center justify-center gap-1 transition-all" onclick="addTextSnippet()" title="Tipografi">
                <span class="material-symbols-outlined text-xl">title</span>
                <span class="text-[9px] font-medium">Metin</span>
            </button>
            <button class="w-12 h-12 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 flex flex-col items-center justify-center gap-1 transition-all" onclick="alert('Şekil kütüphanesi açıldı')" title="Şekiller">
                <span class="material-symbols-outlined text-xl">category</span>
                <span class="text-[9px] font-medium">Şekil</span>
            </button>
            <button class="w-12 h-12 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 flex flex-col items-center justify-center gap-1 transition-all" onclick="alert('Logo yükleme penceresi açıldı')" title="Görsel / Logo">
                <span class="material-symbols-outlined text-xl">add_photo_alternate</span>
                <span class="text-[9px] font-medium">Görsel</span>
            </button>
            <button class="w-12 h-12 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 flex flex-col items-center justify-center gap-1 transition-all" onclick="switchCardSide('back')" title="QR Kod">
                <span class="material-symbols-outlined text-xl">qr_code_2</span>
                <span class="text-[9px] font-medium">QR Kod</span>
            </button>
        </aside>

        <!-- ACTIVE DRAWER PANEL -->
        <aside class="w-72 bg-slate-900/95 border-r border-slate-800 flex flex-col shrink-0 text-white overflow-y-auto p-4 z-10" id="studioDrawer">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-300">Sektörel Şablonlar</h3>
                <span class="text-[10px] font-label-numeric text-secondary font-bold">420+ Tasarım</span>
            </div>
            <!-- Template Filter Chips -->
            <div class="flex flex-wrap gap-1.5 mb-3 text-[10px]">
                <button class="px-2 py-0.5 rounded bg-secondary text-white font-bold">Mimar &amp; Tasarım</button>
                <button class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 hover:bg-slate-700">Hukuk &amp; Avukat</button>
                <button class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 hover:bg-slate-700">Sağlık &amp; Medikal</button>
            </div>
            <!-- Template Cards -->
            <div class="space-y-3">
                <div class="cursor-pointer border-2 border-secondary rounded-xl p-2.5 bg-slate-950 hover:bg-slate-900 transition-all" onclick="applyTemplatePreset('nexus')">
                    <div class="h-16 rounded-lg bg-slate-900 border border-amber-500/30 p-2 flex flex-col justify-between">
                        <span class="text-[10px] font-bold text-amber-400">NEXUS STUDIO</span>
                        <span class="text-[8px] text-slate-400">Altın Varak &amp; Mat Lacivert</span>
                    </div>
                    <div class="flex justify-between items-center mt-2 text-[10px]">
                        <span class="font-bold text-slate-200">Modern Mimarlık</span>
                        <span class="text-emerald-400 font-bold">Seçili</span>
                    </div>
                </div>
                <div class="cursor-pointer border border-slate-800 hover:border-slate-600 rounded-xl p-2.5 bg-slate-950 transition-all" onclick="applyTemplatePreset('minimal')">
                    <div class="h-16 rounded-lg bg-white p-2 flex flex-col justify-between text-slate-900">
                        <span class="text-[10px] font-bold">STUDIO MONO</span>
                        <span class="text-[8px] text-slate-600">Minimal Tipografi</span>
                    </div>
                    <div class="flex justify-between items-center mt-2 text-[10px]">
                        <span class="font-medium text-slate-200">Monokrom İskandinav</span>
                        <span class="text-slate-400">Uygula</span>
                    </div>
                </div>
                <div class="cursor-pointer border border-slate-800 hover:border-slate-600 rounded-xl p-2.5 bg-slate-950 transition-all" onclick="applyTemplatePreset('gold')">
                    <div class="h-16 rounded-lg bg-slate-950 p-2 flex flex-col justify-between border border-amber-600/40">
                        <span class="text-[10px] font-bold text-amber-300">AV. YILMAZ HUKUK</span>
                        <span class="text-[8px] text-slate-400">Klasik &amp; Ağırbaşlı</span>
                    </div>
                    <div class="flex justify-between items-center mt-2 text-[10px]">
                        <span class="font-medium text-slate-200">Kurumsal Hukuk</span>
                        <span class="text-slate-400">Uygula</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- CENTER VECTOR ARTBOARD STAGE -->
        <main class="flex-1 bg-slate-950 flex flex-col items-center justify-center p-6 overflow-auto relative">
            <div class="absolute top-4 left-6 flex items-center gap-4 text-[10px] font-label-numeric bg-slate-900/90 border border-slate-800 px-3 py-1.5 rounded-lg text-slate-300 z-10">
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span> Kırmızı: Dış Taşırma (86x54mm)</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-slate-400"></span> Siyah: Kesim Hattı (84x52mm)</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Mavi: Güvenli Alan (80x48mm)</span>
            </div>

            <!-- Outer Bleed Box -->
            <div class="p-2 border border-red-500/60 rounded-xl bg-red-950/10 transition-all" id="bleedBox">
                <!-- Trim Box -->
                <div class="relative w-[520px] h-[320px] rounded-lg shadow-2xl overflow-hidden border border-slate-600 transition-colors bg-[#0b1329] text-white p-6 flex flex-col justify-between" id="trimBox">
                    <div class="absolute inset-3 border border-dashed border-blue-400/40 pointer-events-none rounded" id="safeAreaBox"></div>
                    
                    <!-- Front View -->
                    <div class="h-full flex flex-col justify-between relative z-10" id="canvas-front-content">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-slate-900 border border-amber-400/50 flex items-center justify-center shadow-lg">
                                    <span class="gold-foil-text font-black text-2xl">N</span>
                                </div>
                                <div>
                                    <h2 class="gold-foil-text text-lg font-bold tracking-wider" id="studioBrandName">NEXUS ARCHITECTURE</h2>
                                    <p class="text-[10px] text-slate-400 tracking-widest font-label-caps">MİMARLIK &amp; TASARIM STÜDYOSU</p>
                                </div>
                            </div>
                            <span class="text-[9px] font-label-numeric text-amber-300 border border-amber-500/30 px-2 py-0.5 rounded bg-slate-900/60">
                                KABARTMA LAKLI KATMAN
                            </span>
                        </div>
                        <div class="my-auto py-2">
                            <p class="text-xs text-slate-300 font-light">Lüks Konut, Ticari Projeler &amp; İç Mimari Danışmanlık</p>
                            <div class="w-24 h-0.5 bg-gradient-to-r from-amber-400 to-transparent mt-1.5"></div>
                        </div>
                        <div class="flex justify-between items-end border-t border-slate-700/60 pt-3">
                            <div>
                                <h3 class="text-white text-sm font-bold tracking-tight" id="studioCardName">Mert Yılmaz</h3>
                                <p class="text-xs text-amber-300 font-medium">Kurucu Ortak &amp; Baş Mimar</p>
                            </div>
                            <div class="text-right text-[11px] font-label-numeric text-slate-300 space-y-0.5">
                                <p class="text-amber-400">+90 (212) 555 0192</p>
                                <p>mert@nexusmimarlik.com</p>
                                <p class="text-slate-400">Levent Loft No:14 Beşiktaş / İstanbul</p>
                            </div>
                        </div>
                    </div>

                    <!-- Back View -->
                    <div class="h-full flex flex-col justify-between relative z-10 hidden" id="canvas-back-content">
                        <div class="text-center py-2">
                            <div class="w-12 h-12 rounded-full bg-slate-900 border border-amber-500/40 mx-auto flex items-center justify-center mb-1">
                                <span class="gold-foil-text font-black text-xl">N</span>
                            </div>
                            <h3 class="gold-foil-text text-sm font-bold tracking-widest">NEXUS ARCHITECTURE</h3>
                            <p class="text-[9px] text-slate-400 tracking-wider">EST. 2018 • İSTANBUL</p>
                        </div>
                        <div class="flex items-center justify-center my-auto">
                            <div class="p-3 bg-white rounded-lg shadow-lg text-slate-900 flex flex-col items-center gap-1">
                                <span class="material-symbols-outlined text-5xl">qr_code_2</span>
                                <span class="text-[8px] font-label-numeric text-slate-500 font-bold tracking-wider">vCard Dijital Kartvizit</span>
                            </div>
                        </div>
                        <div class="text-center text-[10px] text-slate-400 font-label-numeric border-t border-slate-800 pt-2">
                            www.nexusmimarlik.com • info@nexusmimarlik.com
                        </div>
                    </div>
                </div>
            </div>

            <!-- Floating Zoom & Reset Dock -->
            <div class="absolute bottom-5 flex items-center gap-2 bg-slate-900/90 backdrop-blur-md border border-slate-800 px-3 py-1.5 rounded-full text-slate-300 shadow-xl text-xs">
                <button class="p-1 hover:text-white" onclick="adjustArtboardZoom(-10)" title="Küçült">
                    <span class="material-symbols-outlined text-base">remove</span>
                </button>
                <span class="font-label-numeric text-[11px] px-1 font-bold" id="zoomIndicator">100%</span>
                <button class="p-1 hover:text-white" onclick="adjustArtboardZoom(10)" title="Büyüt">
                    <span class="material-symbols-outlined text-base">add</span>
                </button>
            </div>
        </main>

        <!-- RIGHT PROPERTY INSPECTOR -->
        <aside class="w-72 bg-slate-900 border-l border-slate-800 flex flex-col shrink-0 text-white overflow-y-auto p-4 z-10 text-xs">
            <h3 class="font-bold text-slate-200 uppercase tracking-wider text-[11px] mb-3 flex items-center justify-between">
                <span>Özellik Denetçisi</span>
                <span class="text-secondary font-label-numeric">Katman #01</span>
            </h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-slate-400 text-[11px] mb-1 font-medium">İsim / Ünvan Metni</label>
                    <input class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs focus:outline-none focus:border-secondary" id="inspectorNameInput" oninput="syncCanvasText()" type="text" value="Mert Yılmaz"/>
                </div>
                <div>
                    <label class="block text-slate-400 text-[11px] mb-1 font-medium">Şirket / Marka Adı</label>
                    <input class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs focus:outline-none focus:border-secondary" id="inspectorBrandInput" oninput="syncCanvasText()" type="text" value="NEXUS ARCHITECTURE"/>
                </div>
                <div class="pt-3 border-t border-slate-800">
                    <span class="block text-slate-400 text-[11px] mb-1.5 font-medium">Özel Efekt &amp; Baskı Katmanı</span>
                    <label class="flex items-center gap-2 p-2 rounded bg-slate-950 border border-slate-800 cursor-pointer">
                        <input checked="" class="rounded border-slate-700 text-amber-500 focus:ring-0" type="checkbox"/>
                        <span class="text-amber-300 font-semibold">Bölgesel Kabartma Lak (Spot UV)</span>
                    </label>
                </div>
                <div class="mt-4 p-3 rounded-xl bg-emerald-950/40 border border-emerald-800/60 text-emerald-300 text-[11px] space-y-1">
                    <div class="flex items-center gap-1 font-bold">
                        <span class="material-symbols-outlined text-sm">verified</span>
                        <span>Ofset Baskıya Tam Uygun</span>
                    </div>
                    <p class="text-[10px] text-emerald-400/80">Metinler güvenli alanda, taşma payları 2mm eksiksiz hesaplandı.</p>
                </div>
                <button class="w-full mt-2 py-2.5 bg-secondary hover:bg-blue-600 text-white font-bold rounded-xl text-xs shadow-md shadow-blue-500/20 transition-all" onclick="applyAndCloseStudio()">
                    Düzenlemeyi Kaydet
                </button>
            </div>
        </aside>
    </div>
</div>

<script>
    // State Management
    let selectedPackagePrice = 140; // Default Standart
    let selectedPackageName = 'standart';
    let selectedBaseQtyPrice = 945;  // Default 3000
    let selectedQtyCount = 3000;
    let currentSide = 'front';
    let zoomLevel = 100;
    let bleedVisible = true;

    // 1. Mouse Tilt & Specular Glare Effect on 3D Card
    const tiltStage = document.getElementById('tiltStage');
    const tiltCard = document.getElementById('tiltCard');
    const cardGlare = document.getElementById('cardGlare');

    if (tiltStage && tiltCard) {
      tiltStage.addEventListener('mousemove', (e) => {
        const rect = tiltStage.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        
        const rotateX = ((y - centerY) / centerY) * -14;
        const rotateY = ((x - centerX) / centerX) * 14;

        tiltCard.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;

        const pX = (x / rect.width) * 100;
        const pY = (y / rect.height) * 100;
        cardGlare.style.setProperty('--mouse-x', `${pX}%`);
        cardGlare.style.setProperty('--mouse-y', `${pY}%`);
      });

      tiltStage.addEventListener('mouseleave', () => {
        tiltCard.style.transform = 'rotateX(0deg) rotateY(0deg)';
      });
    }

    // 2. Package Selector
    function selectPackage(pkgKey, additionalPrice) {
      selectedPackagePrice = additionalPrice;
      selectedPackageName = pkgKey;
      document.getElementById('hiddenPackage').value = pkgKey;

      const cards = document.querySelectorAll('.package-card');
      cards.forEach(c => {
        c.classList.remove('border-2', 'border-secondary', 'bg-blue-50/40');
        c.classList.add('border', 'border-outline-variant');
      });

      const activeEl = document.getElementById(`pkg-${pkgKey}`);
      if (activeEl) {
        activeEl.classList.remove('border-outline-variant');
        activeEl.classList.add('border-2', 'border-secondary', 'bg-blue-50/40');
      }

      calculateGrandTotal();
    }

    // 3. Quantity Selector
    function selectQty(count, basePrice, unitPrice) {
      selectedQtyCount = count;
      selectedBaseQtyPrice = basePrice;
      document.getElementById('hiddenQty').value = count;

      const btns = document.querySelectorAll('.qty-btn');
      btns.forEach(b => {
        b.classList.remove('border-2', 'border-secondary', 'bg-blue-50/40');
        b.classList.add('border', 'border-outline-variant');
      });

      if (event && event.currentTarget) {
        event.currentTarget.classList.remove('border-outline-variant');
        event.currentTarget.classList.add('border-2', 'border-secondary', 'bg-blue-50/40');
      }

      calculateGrandTotal();
    }

    // 4. Custom Quantity
    function openCustomQtyPrompt() {
      const customVal = prompt('Lütfen istediğiniz özel baskı adedini giriniz (Örn: 7500):', '5000');
      if (customVal && !isNaN(customVal) && parseInt(customVal) > 0) {
        const qty = parseInt(customVal);
        const estimatedBase = Math.round(qty * 0.28);
        selectQty(qty, estimatedBase, 0.28);
      }
    }

    // 5. Total Price & Free Shipping Calculator
    function calculateGrandTotal() {
      const cornerCut = parseInt(document.getElementById('cornerCutSelect').value || 0);
      const shippingSpeed = parseInt(document.getElementById('shippingSpeedSelect').value || 0);

      const netTotal = selectedBaseQtyPrice + selectedPackagePrice + cornerCut + shippingSpeed;
      const kdvAmount = netTotal * 0.20;
      const grossTotal = netTotal + kdvAmount;
      const unitPrice = (netTotal / selectedQtyCount).toFixed(3);

      document.getElementById('hiddenPrice').value = netTotal;
      document.getElementById('grandTotalPrice').innerText = `${netTotal.toLocaleString('tr-TR', {minimumFractionDigits: 2})} ₺`;
      document.getElementById('taxPriceBadge').innerText = `+${kdvAmount.toLocaleString('tr-TR', {minimumFractionDigits: 0})} ₺ KDV Dahil: ${grossTotal.toLocaleString('tr-TR', {minimumFractionDigits: 2})} ₺`;
      document.getElementById('unitPriceDisplay').innerText = `${unitPrice} ₺ / Adet`;

      // Free shipping threshold 750 ₺
      const threshold = 750;
      const progress = Math.min(100, Math.round((netTotal / threshold) * 100));
      const remaining = Math.max(0, threshold - netTotal);

      document.getElementById('shippingProgressBar').style.width = `${progress}%`;
      const remainingLabel = document.getElementById('shippingRemainingText');
      if (remaining === 0) {
        remainingLabel.innerText = 'Kargo ÜCRETSİZ!';
        remainingLabel.classList.add('text-emerald-600', 'font-bold');
      } else {
        remainingLabel.innerText = `${remaining.toLocaleString('tr-TR')} ₺ kaldı`;
        remainingLabel.classList.remove('text-emerald-600', 'font-bold');
      }
    }

    // 6. Design Tabs
    function switchDesignTab(tab) {
      document.getElementById('tab-content-editor').classList.add('hidden');
      document.getElementById('tab-content-upload').classList.add('hidden');
      document.getElementById('tab-content-support').classList.add('hidden');

      const tabs = ['editor', 'upload', 'support'];
      tabs.forEach(t => {
        const b = document.getElementById(`tab-${t}-btn`);
        b.classList.remove('bg-surface-container-lowest', 'shadow-xs', 'text-primary', 'font-bold');
        b.classList.add('text-on-surface-variant', 'font-medium');
      });

      const activeBtn = document.getElementById(`tab-${tab}-btn`);
      activeBtn.classList.add('bg-surface-container-lowest', 'shadow-xs', 'text-primary', 'font-bold');
      activeBtn.classList.remove('text-on-surface-variant', 'font-medium');

      document.getElementById(`tab-content-${tab}`).classList.remove('hidden');
    }

    // 7. 3D Card Flip Mockup Animation
    let isFlipped = false;
    function flipCardDemo() {
      isFlipped = !isFlipped;
      tiltCard.style.transition = 'transform 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
      tiltCard.style.transform = isFlipped ? 'rotateY(180deg)' : 'rotateY(0deg)';
      setTimeout(() => {
        tiltCard.style.transition = 'transform 0.15s cubic-bezier(0.2, 0.8, 0.4, 1)';
      }, 650);
    }

    // 8. Studio Customizer Modal Trigger
    function openStudioModal() {
      document.getElementById('studioCustomizerModal').classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }

    function closeStudioModal() {
      document.getElementById('studioCustomizerModal').classList.add('hidden');
      document.body.style.overflow = '';
    }

    function applyAndCloseStudio() {
      closeStudioModal();
      alert('Tasarımınız başarıyla kaydedildi! Şimdi siparişinizi tamamlayabilirsiniz.');
    }

    // 9. Studio Customizer Front/Back Switcher
    function switchCardSide(side) {
      currentSide = side;
      const frontContent = document.getElementById('canvas-front-content');
      const backContent = document.getElementById('canvas-back-content');
      const btnFront = document.getElementById('btn-card-front');
      const btnBack = document.getElementById('btn-card-back');

      if (side === 'front') {
        frontContent.classList.remove('hidden');
        backContent.classList.add('hidden');
        btnFront.classList.add('bg-secondary', 'text-white');
        btnFront.classList.remove('text-slate-400');
        btnBack.classList.remove('bg-secondary', 'text-white');
        btnBack.classList.add('text-slate-400');
      } else {
        frontContent.classList.add('hidden');
        backContent.classList.remove('hidden');
        btnBack.classList.add('bg-secondary', 'text-white');
        btnBack.classList.remove('text-slate-400');
        btnFront.classList.remove('bg-secondary', 'text-white');
        btnFront.classList.add('text-slate-400');
      }
    }

    // 10. Bleed Guides Toggle
    function toggleBleedGuides() {
      bleedVisible = !bleedVisible;
      const bleedBox = document.getElementById('bleedBox');
      const safeAreaBox = document.getElementById('safeAreaBox');
      const btn = document.getElementById('btnBleedToggle');

      if (bleedVisible) {
        bleedBox.classList.add('border-red-500/60', 'bg-red-950/10');
        safeAreaBox.classList.remove('opacity-0');
        btn.classList.add('text-amber-400');
      } else {
        bleedBox.classList.remove('border-red-500/60', 'bg-red-950/10');
        safeAreaBox.classList.add('opacity-0');
        btn.classList.remove('text-amber-400');
      }
    }

    // 11. Live Sync from Inspector to Artboard
    function syncCanvasText() {
      const name = document.getElementById('inspectorNameInput').value;
      const brand = document.getElementById('inspectorBrandInput').value;

      if (document.getElementById('studioCardName')) {
        document.getElementById('studioCardName').innerText = name || 'Mert Yılmaz';
      }
      if (document.getElementById('pdpOwnerName')) {
        document.getElementById('pdpOwnerName').innerText = name || 'Mert Yılmaz';
      }
      if (document.getElementById('studioBrandName')) {
        document.getElementById('studioBrandName').innerText = brand || 'NEXUS ARCHITECTURE';
      }
    }

    // 12. Zoom Controls
    function adjustArtboardZoom(delta) {
      zoomLevel = Math.max(70, Math.min(150, zoomLevel + delta));
      document.getElementById('zoomIndicator').innerText = `${zoomLevel}%`;
      const trimBox = document.getElementById('trimBox');
      trimBox.style.transform = `scale(${zoomLevel / 100})`;
      trimBox.style.transformOrigin = 'center center';
    }

    // 13. Presets
    function applyTemplatePreset(type) {
      const trimBox = document.getElementById('trimBox');
      if (type === 'nexus') {
        trimBox.style.backgroundColor = '#0b1329';
        document.getElementById('inspectorBrandInput').value = 'NEXUS ARCHITECTURE';
      } else if (type === 'minimal') {
        trimBox.style.backgroundColor = '#18181b';
        document.getElementById('inspectorBrandInput').value = 'STUDIO MONOKROM';
      } else if (type === 'gold') {
        trimBox.style.backgroundColor = '#0f172a';
        document.getElementById('inspectorBrandInput').value = 'YILMAZ HUKUK BÜROSU';
      }
      syncCanvasText();
    }

    function addTextSnippet() {
      alert('Yeni metin katmanı eklendi. Özellik denetçisinden düzenleyebilirsiniz.');
    }

    // Init
    calculateGrandTotal();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
