<?php
/**
 * TAMBASKI.COM.TR - Ürün Detay & Online Vektör Stüdyosu
 * Doğal Türk E-Ticaret Arayüzü, Hazır Paketler, Meslek Seçimli Şablonlar & Tek Tıkla Canva Stüdyo
 */
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? 'ekonomik-kartvizit-250gr';
$product = get_product_by_slug($slug);

if (!$product) {
    $product = [
        'id' => 1,
        'name' => 'Kurumsal Prestij Kartvizit (350gr Mat Kuşe + Lak)',
        'title' => 'Kurumsal Prestij Kartvizit (350gr Mat Kuşe + Lak)',
        'slug' => 'ekonomik-kartvizit-250gr',
        'category_name' => 'Kartvizit',
        'category_slug' => 'kartvizit',
        'pricing_type' => 'package_and_custom',
        'starting_price' => 450,
        'description' => '350gr İthal Mat Kuşe, Çift Yön Renkli Ofset Baskı, Mat Selefon ve Bölgesel Kabartma Lak Kombinasyonu',
        'packages' => [
            ['id' => 1, 'title' => 'Ekonomik Paket', 'specs' => '300gr Bristol, Tek Yön', 'quantity' => 1000, 'price' => 450],
            ['id' => 2, 'title' => 'Standart Paket', 'specs' => '350gr Kuşe, Çift Yön, Mat Selefon', 'quantity' => 1000, 'price' => 650],
            ['id' => 3, 'title' => 'Prestij Laklı Paket', 'specs' => '350gr Kuşe, Çift Yön, Kabartma Lak', 'quantity' => 1000, 'price' => 950],
            ['id' => 4, 'title' => 'Lüks Altın Varak', 'specs' => '350gr Kuşe, Altın Yaldız Varak', 'quantity' => 1000, 'price' => 1450]
        ]
    ];
}

$page_title = htmlspecialchars($product['title'] ?? $product['name']) . " | TamBaskı";
$page_desc = htmlspecialchars($product['description'] ?? 'Kurumsal baskı ve online tasarım editörü.');
require_once __DIR__ . '/includes/header.php';

// Meslek / Sektör Listesi
$industries = [
    ['slug' => 'hukuk-avukatlik', 'name' => 'Hukuk, Avukatlık & Danışmanlık', 'icon' => 'bi-bank'],
    ['slug' => 'saglik-klinik', 'name' => 'Sağlık, Doktor & Diş Hekimi', 'icon' => 'bi-heart-pulse'],
    ['slug' => 'gayrimenkul-emlak', 'name' => 'Gayrimenkul & Emlak', 'icon' => 'bi-building'],
    ['slug' => 'insaat-mimarlik', 'name' => 'İnşaat & Mimarlık Ofisleri', 'icon' => 'bi-cone-striped'],
    ['slug' => 'restoran-kafe', 'name' => 'Restoran, Kafe & Gıda', 'icon' => 'bi-cup-hot'],
    ['slug' => 'finans-muhasebe', 'name' => 'Mali Müşavir, Muhasebe & Finans', 'icon' => 'bi-calculator'],
    ['slug' => 'teknoloji-yazilim', 'name' => 'Teknoloji, Yazılım & Reklam Ajansı', 'icon' => 'bi-laptop'],
    ['slug' => 'guzellik-kuafor', 'name' => 'Güzellik, Kuaför & Berber', 'icon' => 'bi-scissors'],
    ['slug' => 'otomotiv-servis', 'name' => 'Otomotiv, Ekspertiz & Servis', 'icon' => 'bi-car-front'],
    ['slug' => 'genel-kurumsal', 'name' => 'Genel Kurumsal & Şirketler', 'icon' => 'bi-briefcase']
];
?>

<!-- ================= BREADCRUMB STRIP ================= -->
<div class="bg-surface-container-low border-b border-outline-variant text-xs py-3">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
        <nav class="flex items-center gap-2 text-on-surface-variant font-medium">
            <a class="hover:text-primary transition-colors" href="index.php">Anasayfa</a>
            <span class="text-outline-variant">/</span>
            <a class="hover:text-primary transition-colors" href="category.php?slug=<?= urlencode($product['category_slug'] ?? 'kartvizit') ?>">
                <?= htmlspecialchars($product['category_name'] ?? 'Baskı Ürünleri') ?>
            </a>
            <span class="text-outline-variant">/</span>
            <span class="text-primary font-bold"><?= htmlspecialchars($product['title'] ?? $product['name']) ?></span>
        </nav>
        <div class="flex items-center gap-4 text-xs font-semibold text-emerald-700">
            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">verified</span> Heidelberg Ofset Garantisi</span>
            <span>•</span>
            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">schedule</span> 24 Saatte Hızlı Üretim</span>
        </div>
    </div>
</div>

<!-- ================= MAIN PDP CONTAINER ================= -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 py-8 flex-1 w-full">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 xl:gap-12 items-start">
        
        <!-- LEFT COLUMN: 3D Stage & Specifications (Cols 1-6) -->
        <section class="lg:col-span-6 flex flex-col gap-6 lg:sticky lg:top-20">
            
            <!-- Quality Badges -->
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                    <span class="material-symbols-outlined text-sm">water_drop</span> Su &amp; Nem Korumalı
                </span>
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full font-semibold bg-blue-50 text-blue-800 border border-blue-200">
                    <span class="material-symbols-outlined text-sm">palette</span> Online Stüdyoda Tasarla
                </span>
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                    <span class="material-symbols-outlined text-sm">local_shipping</span> 750 ₺ Üzeri Ücretsiz Kargo
                </span>
            </div>

            <!-- 3D Interactive Stage Card -->
            <div class="relative bg-gradient-to-b from-white to-slate-100 rounded-2xl border border-outline-variant p-6 sm:p-8 overflow-hidden shadow-sm">
                <!-- Badges -->
                <div class="absolute top-4 left-4 z-20">
                    <span class="bg-white/95 backdrop-blur-md text-slate-800 border border-slate-200 text-xs font-bold px-3 py-1 rounded-lg shadow-xs" id="stagePaperBadge">
                        350 gr/m² İthal Kuşe + Mat Selefon
                    </span>
                </div>
                <div class="absolute top-4 right-4 z-20">
                    <span class="bg-primary text-white text-xs font-bold px-3 py-1 rounded-lg shadow-xs flex items-center gap-1.5" id="stageFinishBadge">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Bölgesel Kabartma Lak (Spot UV)
                    </span>
                </div>

                <!-- 3D Card Simulation Container -->
                <div class="perspective-stage w-full h-[300px] sm:h-[350px] flex items-center justify-center cursor-grab active:cursor-grabbing my-4 select-none" id="tiltStage">
                    <!-- Interactive Business Card -->
                    <div class="tilt-card relative w-[310px] sm:w-[380px] h-[180px] sm:h-[230px] rounded-xl bg-[#0f172a] border border-slate-700/80 p-5 sm:p-6 flex flex-col justify-between overflow-hidden shadow-2xl transition-transform duration-75" id="tiltCard">
                        <!-- Paper Texture & Dynamic Glare -->
                        <div class="absolute inset-0 opacity-15 pointer-events-none mix-blend-screen bg-repeat" style="background-image: radial-gradient(#94a3b8 0.75px, transparent 0.75px); background-size: 6px 6px;"></div>
                        <div class="glare-overlay absolute inset-0 z-10 pointer-events-none" id="cardGlare" style="background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.2) 0%, transparent 60%);"></div>

                        <!-- Card Header: Brand & Tagline -->
                        <div class="relative z-10 flex justify-between items-start">
                            <div class="flex items-center gap-2">
                                <img src="assets/img/logo.svg" alt="TamBaskı" class="h-6 w-auto brightness-0 invert drop-shadow-sm">
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-amber-300 bg-slate-900/80 px-2 py-0.5 rounded border border-amber-500/40">300 DPI OFSET</span>
                            </div>
                        </div>

                        <!-- Card Center: Holder Name & Title -->
                        <div class="relative z-10 my-auto py-1">
                            <h3 class="text-white font-bold text-base sm:text-lg tracking-tight" id="mockupCardName">Ahmet Yılmaz</h3>
                            <p class="text-xs text-amber-300/90 font-medium" id="mockupCardTitle">Genel Müdür / Kurucu Ortak</p>
                            <div class="w-12 h-0.5 bg-brand mt-1.5"></div>
                        </div>

                        <!-- Card Footer: Contact Info -->
                        <div class="relative z-10 flex justify-between items-end border-t border-slate-700/60 pt-2.5 text-[10px] text-slate-300">
                            <div class="space-y-0.5">
                                <p class="flex items-center gap-1"><span class="text-brand">📞</span> 0544 000 00 00</p>
                                <p class="flex items-center gap-1"><span class="text-brand">✉️</span> info@tambaski.com.tr</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-white">www.tambaski.com.tr</p>
                                <p class="text-slate-400">İstanbul, Türkiye</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stage Controls -->
                <div class="flex flex-wrap items-center justify-between pt-3 border-t border-outline-variant/60 text-xs gap-2">
                    <span class="text-on-surface-variant flex items-center gap-1">
                        <span class="material-symbols-outlined text-base text-secondary">explore</span>
                        <span>Farenizi hareket ettirerek 3D ışık yansımasını inceleyin</span>
                    </span>
                    <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-surface-container-lowest hover:bg-slate-100 text-primary font-bold rounded-lg border border-outline-variant shadow-xs transition-all" onclick="flipCardDemo()">
                        <span class="material-symbols-outlined text-base">flip_camera_android</span>
                        <span>Kartı Çevir</span>
                    </button>
                </div>
            </div>

            <!-- Technical Specifications Table -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant overflow-hidden shadow-xs">
                <div class="px-5 py-3.5 bg-surface-container-low border-b border-outline-variant flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-primary flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary text-base">tune</span>
                        Ürün &amp; Baskı Standartları
                    </span>
                    <span class="text-[11px] font-semibold text-emerald-700">ISO 12647-2 Onaylı</span>
                </div>
                <table class="w-full text-left text-xs">
                    <tbody class="divide-y divide-outline-variant/60">
                        <tr class="hover:bg-surface-container-low">
                            <td class="px-5 py-2.5 font-medium text-on-surface-variant w-1/3">Kağıt Cinsi</td>
                            <td class="px-5 py-2.5 font-bold text-primary" id="specPaperText">350 gr/m² Birinci Sınıf İthal Mat Kuşe</td>
                        </tr>
                        <tr class="hover:bg-surface-container-low bg-slate-50/50">
                            <td class="px-5 py-2.5 font-medium text-on-surface-variant">Bitmiş Ebat</td>
                            <td class="px-5 py-2.5 font-bold text-primary">84 x 52 mm (Baskı Ebatı: 86 x 54 mm Kesim Paylı)</td>
                        </tr>
                        <tr class="hover:bg-surface-container-low">
                            <td class="px-5 py-2.5 font-medium text-on-surface-variant">Baskı Teknolojisi</td>
                            <td class="px-5 py-2.5 font-bold text-primary">Heidelberg Speedmaster 4+4 CMYK Ofset Baskı</td>
                        </tr>
                        <tr class="hover:bg-surface-container-low bg-slate-50/50">
                            <td class="px-5 py-2.5 font-medium text-on-surface-variant">Yüzey Koruma</td>
                            <td class="px-5 py-2.5 font-bold text-primary" id="specLaminationText">Çift Yön İpeksi Termal Mat Selefon (Suya Dayanıklı)</td>
                        </tr>
                        <tr class="hover:bg-surface-container-low">
                            <td class="px-5 py-2.5 font-medium text-on-surface-variant">Özel İşçilik</td>
                            <td class="px-5 py-2.5 font-bold text-primary" id="specFinishText">Bölgesel Parlak Kabartma Lak (Spot UV)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- RIGHT COLUMN: Streamlined Configurator & Canva Launcher (Cols 7-12) -->
        <section class="lg:col-span-6 flex flex-col gap-6">
            
            <!-- Product Title & Rating -->
            <div class="border-b border-outline-variant pb-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-orange-100 text-brand text-[11px] font-bold px-2.5 py-0.5 rounded-md uppercase tracking-wider flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">verified</span> Orijinal TamBaskı
                    </span>
                    <span class="text-on-surface-variant text-xs flex items-center gap-1">
                        <span class="text-amber-500 font-bold">★ 4.9</span> (480+ Gerçek Müşteri Onayı)
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-primary tracking-tight leading-tight" id="mainProductTitle">
                    <?= htmlspecialchars($product['title'] ?? $product['name']) ?>
                </h1>
                <p class="text-on-surface-variant text-xs sm:text-sm mt-1 leading-relaxed">
                    <?= htmlspecialchars($product['description'] ?? '350gr Mat Kuşe, Çift Yön Renkli, Mat Selefon ve Kabartma Lak.') ?>
                </p>
            </div>

            <form action="cart.php" method="POST" id="pdpOrderForm">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="hidden" name="product_name" id="formProductName" value="<?= htmlspecialchars($product['title'] ?? $product['name']) ?>">
                <input type="hidden" name="package" id="formPackageName" value="standart">
                <input type="hidden" name="quantity" id="formQuantity" value="1000">
                <input type="hidden" name="price" id="formPrice" value="650">
                <input type="hidden" name="design_type" id="formDesignType" value="canva_studio">
                <input type="hidden" name="design_svg" id="formDesignSvg" value="">

                <!-- STEP 1: BASKI PAKETİ SEÇİMİ (4 NET PAKET) -->
                <div class="bg-surface-container-lowest p-5 rounded-2xl border border-outline-variant shadow-sm mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-xs font-bold">1</span>
                            <h2 class="text-sm font-bold text-primary">Baskı Paketini Seçin</h2>
                        </div>
                        <a href="index.php#sample-kit" class="text-xs font-bold text-secondary hover:underline">Ücretsiz Numune İste</a>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3" id="packageOptionsGrid">
                        <!-- Paket 1: Ekonomik -->
                        <div class="pkg-card cursor-pointer border-2 border-outline-variant rounded-xl p-3.5 hover:border-secondary transition-all relative" 
                             id="pkgCard_ekonomik" 
                             onclick="selectPrintPackage('ekonomik', 'Ekonomik Paket (300gr Bristol, Tek Yön)', 450, '300 gr/m² Bristol', 'Selefonsuz', 'Standart Ofset')">
                            <span class="text-xs font-bold text-primary block">Ekonomik</span>
                            <span class="text-[11px] text-on-surface-variant block mt-0.5 leading-tight">300gr Bristol, Tek Yön Renkli</span>
                            <div class="mt-2 text-xs font-bold text-primary">450 ₺</div>
                        </div>

                        <!-- Paket 2: Standart (Varsayılan & En Çok Tercih Edilen) -->
                        <div class="pkg-card cursor-pointer border-2 border-secondary bg-blue-50/40 rounded-xl p-3.5 transition-all relative" 
                             id="pkgCard_standart" 
                             onclick="selectPrintPackage('standart', 'Standart Paket (350gr Kuşe, Çift Yön, Mat Selefon)', 650, '350 gr/m² İthal Kuşe', 'Çift Yön Mat Selefon', 'Standart Ofset')">
                            <div class="absolute -top-2.5 right-2 bg-emerald-600 text-white text-[9px] font-bold px-2 py-0.5 rounded-full shadow-xs">
                                EN ÇOK SATAN
                            </div>
                            <span class="text-xs font-bold text-secondary block">Standart</span>
                            <span class="text-[11px] text-on-surface-variant block mt-0.5 leading-tight">350gr Kuşe, Çift Yön, Mat Selefon</span>
                            <div class="mt-2 text-xs font-bold text-secondary">650 ₺</div>
                        </div>

                        <!-- Paket 3: Prestij Laklı -->
                        <div class="pkg-card cursor-pointer border-2 border-outline-variant rounded-xl p-3.5 hover:border-secondary transition-all relative" 
                             id="pkgCard_prestij" 
                             onclick="selectPrintPackage('prestij', 'Prestij Paket (350gr Kuşe + Kabartma Lak)', 950, '350 gr/m² İthal Kuşe', 'Çift Yön Mat Selefon', 'Bölgesel Kabartma Lak (Spot UV)')">
                            <span class="text-xs font-bold text-primary block">Prestij Laklı</span>
                            <span class="text-[11px] text-on-surface-variant block mt-0.5 leading-tight">350gr, Çift Yön, Kabartma Lak</span>
                            <div class="mt-2 text-xs font-bold text-primary">950 ₺</div>
                        </div>

                        <!-- Paket 4: VIP Altın Varak -->
                        <div class="pkg-card cursor-pointer border-2 border-outline-variant rounded-xl p-3.5 hover:border-secondary transition-all relative" 
                             id="pkgCard_varak" 
                             onclick="selectPrintPackage('varak', 'Lüks Varak Paket (350gr + Altın Yaldız)', 1450, '350 gr/m² İthal Kuşe', 'Çift Yön Mat Selefon', 'Parlak Altın Varak Yaldız')">
                            <span class="text-xs font-bold text-primary block">Lüks Altın Varak</span>
                            <span class="text-[11px] text-on-surface-variant block mt-0.5 leading-tight">350gr Kuşe + Parlak Altın Varak</span>
                            <div class="mt-2 text-xs font-bold text-primary">1.450 ₺</div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: BASKI ADEDİ SEÇİMİ (5 NET BUTON) -->
                <div class="bg-surface-container-lowest p-5 rounded-2xl border border-outline-variant shadow-sm mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-xs font-bold">2</span>
                            <h2 class="text-sm font-bold text-primary">Baskı Adedini Belirleyin</h2>
                        </div>
                        <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                            Tiraj Arttıkça Birim Fiyat Düşer
                        </span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5" id="quantityGrid">
                        <button type="button" class="qty-btn text-left p-3 rounded-xl border-2 border-secondary bg-blue-50/40 transition-all" id="qtyBtn_1000" onclick="selectQuantity(1000, 1.0)">
                            <div class="text-xs font-bold text-secondary">1.000 Adet</div>
                            <div class="text-xs font-bold text-secondary mt-1" id="qtyPrice_1000">650 ₺</div>
                            <div class="text-[10px] text-on-surface-variant" id="qtyUnit_1000">0.65 ₺ / Adet</div>
                        </button>

                        <button type="button" class="qty-btn text-left p-3 rounded-xl border-2 border-outline-variant hover:border-secondary transition-all" id="qtyBtn_2000" onclick="selectQuantity(2000, 1.65)">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-primary">2.000 Adet</span>
                                <span class="text-[9px] font-bold text-emerald-700 bg-emerald-100 px-1 rounded">%20 İndirim</span>
                            </div>
                            <div class="text-xs font-bold text-primary mt-1" id="qtyPrice_2000">1.070 ₺</div>
                            <div class="text-[10px] text-on-surface-variant" id="qtyUnit_2000">0.53 ₺ / Adet</div>
                        </button>

                        <button type="button" class="qty-btn text-left p-3 rounded-xl border-2 border-outline-variant hover:border-secondary transition-all" id="qtyBtn_3000" onclick="selectQuantity(3000, 2.25)">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-primary">3.000 Adet</span>
                                <span class="text-[9px] font-bold text-emerald-700 bg-emerald-100 px-1 rounded">%30 Avantaj</span>
                            </div>
                            <div class="text-xs font-bold text-primary mt-1" id="qtyPrice_3000">1.460 ₺</div>
                            <div class="text-[10px] text-on-surface-variant" id="qtyUnit_3000">0.48 ₺ / Adet</div>
                        </button>

                        <button type="button" class="qty-btn text-left p-3 rounded-xl border-2 border-outline-variant hover:border-secondary transition-all" id="qtyBtn_5000" onclick="selectQuantity(5000, 3.4)">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-primary">5.000 Adet</span>
                                <span class="text-[9px] font-bold text-emerald-700 bg-emerald-100 px-1 rounded">%40 Fırsat</span>
                            </div>
                            <div class="text-xs font-bold text-primary mt-1" id="qtyPrice_5000">2.210 ₺</div>
                            <div class="text-[10px] text-on-surface-variant" id="qtyUnit_5000">0.44 ₺ / Adet</div>
                        </button>

                        <button type="button" class="qty-btn text-left p-3 rounded-xl border-2 border-outline-variant hover:border-secondary transition-all" id="qtyBtn_10000" onclick="selectQuantity(10000, 6.0)">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-primary">10.000 Adet</span>
                                <span class="text-[9px] font-bold text-emerald-700 bg-emerald-100 px-1 rounded">Toptan</span>
                            </div>
                            <div class="text-xs font-bold text-primary mt-1" id="qtyPrice_10000">3.900 ₺</div>
                            <div class="text-[10px] text-on-surface-variant" id="qtyUnit_10000">0.39 ₺ / Adet</div>
                        </button>
                    </div>
                </div>

                <!-- STEP 3: TASARIM YÖNTEMİNİ BELİRLEYİN (TAM KULLANICININ İSTEDİĞİ 3 SEÇENEK) -->
                <div class="bg-surface-container-lowest p-5 rounded-2xl border border-outline-variant shadow-sm mb-6">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-xs font-bold">3</span>
                        <h2 class="text-sm font-bold text-primary">Tasarım Yönteminizi Seçin</h2>
                    </div>

                    <!-- 3 Net Sekme Butonu -->
                    <div class="grid grid-cols-3 gap-2 bg-slate-100 p-1.5 rounded-xl mb-4 text-xs font-bold">
                        <button type="button" class="py-2.5 px-2 rounded-lg bg-white shadow-xs text-secondary flex items-center justify-center gap-1 transition-all" id="tabBtn_editor" onclick="switchDesignSection('editor')">
                            <span class="material-symbols-outlined text-base">brush</span>
                            <span>Kendin Tasarla</span>
                        </button>
                        <button type="button" class="py-2.5 px-2 rounded-lg text-slate-600 hover:text-primary flex items-center justify-center gap-1 transition-all" id="tabBtn_templates" onclick="switchDesignSection('templates')">
                            <span class="material-symbols-outlined text-base">dashboard</span>
                            <span>Hazır Şablonlar</span>
                        </button>
                        <button type="button" class="py-2.5 px-2 rounded-lg text-slate-600 hover:text-primary flex items-center justify-center gap-1 transition-all" id="tabBtn_upload" onclick="switchDesignSection('upload')">
                            <span class="material-symbols-outlined text-base">upload_file</span>
                            <span>Dosya Yükle</span>
                        </button>
                    </div>

                    <!-- SEÇENEK 1: KENDİN TASARLA (TEK TIKLA BAŞLA) -->
                    <div id="section_editor" class="space-y-3">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-secondary/30 rounded-xl p-5 text-center">
                            <div class="w-12 h-12 rounded-full bg-secondary text-white flex items-center justify-center mx-auto mb-2.5 shadow-md shadow-blue-500/20">
                                <span class="material-symbols-outlined text-2xl">magic_button</span>
                            </div>
                            <h3 class="text-sm font-bold text-primary">Online Vektörel Tasarım Stüdyosu</h3>
                            <p class="text-xs text-on-surface-variant mt-1 max-w-sm mx-auto leading-relaxed">
                                Bilgisayarınıza program kurmadan, tarayıcınızda logonuzu yükleyin, metinleri yazın ve canlı 3D doku üzerinde anında görün.
                            </p>
                            <button type="button" class="mt-4 inline-flex items-center gap-2 bg-secondary hover:bg-blue-600 text-white py-3 px-6 rounded-xl font-bold text-xs shadow-md shadow-blue-500/25 transition-all active:scale-95" onclick="openCanvaStudioModal()">
                                <span class="material-symbols-outlined text-lg">brush</span>
                                <span>Tasarım Editörünü Başlat (Tek Tıkla Aç)</span>
                            </button>
                        </div>
                    </div>

                    <!-- SEÇENEK 2: HAZIR ŞABLON SEÇ (MESLEK / SEKTÖR SEÇİCİ) -->
                    <div id="section_templates" class="hidden space-y-4">
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                            <label class="block text-xs font-bold text-primary mb-2 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-secondary">work</span>
                                Meslek / Sektörünüzü Seçin:
                            </label>
                            <select id="industrySectorSelect" class="w-full bg-white border border-outline-variant rounded-xl px-3 py-2.5 text-xs font-semibold text-primary focus:outline-none focus:border-secondary shadow-xs" onchange="filterIndustryTemplates(this.value)">
                                <option value="all">Tüm Sektörler (Kurumsal &amp; Ticaret)</option>
                                <?php foreach ($industries as $ind): ?>
                                    <option value="<?= $ind['slug'] ?>"><?= htmlspecialchars($ind['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Şablon Vitrini (Hazır Taslaklar & Kullanıcı Ekleme Öncesi Bilgilendirme) -->
                        <div class="grid grid-cols-2 gap-3" id="industryTemplatesList">
                            <div class="cursor-pointer border-2 border-secondary bg-blue-50/20 rounded-xl p-3 hover:shadow-md transition-all text-center" onclick="openCanvaStudioModal('minimalist')">
                                <div class="aspect-[16/10] bg-slate-900 rounded-lg flex items-center justify-center p-2 mb-2 text-white">
                                    <span class="text-[11px] font-bold text-amber-300">KURUMSAL PRESTİJ</span>
                                </div>
                                <span class="text-xs font-bold text-primary block">Modern Minimal Şablon</span>
                                <span class="text-[10px] text-secondary font-semibold mt-1 block">Bu Şablonla Başla →</span>
                            </div>

                            <div class="cursor-pointer border border-outline-variant hover:border-secondary rounded-xl p-3 hover:shadow-md transition-all text-center" onclick="openCanvaStudioModal('blank')">
                                <div class="aspect-[16/10] bg-white border border-dashed border-slate-300 rounded-lg flex flex-col items-center justify-center p-2 mb-2 text-slate-500">
                                    <span class="material-symbols-outlined text-2xl text-secondary">add_circle</span>
                                    <span class="text-[10px] font-bold mt-1">Boş Tuvalle Başla</span>
                                </div>
                                <span class="text-xs font-bold text-primary block">Sıfırdan Tasarla</span>
                                <span class="text-[10px] text-on-surface-variant mt-1 block">Özel Çizim Yap →</span>
                            </div>
                        </div>
                    </div>

                    <!-- SEÇENEK 3: DOSYA YÜKLE (BASKIYA HAZIR PDF / AI) -->
                    <div id="section_upload" class="hidden space-y-3">
                        <div class="border-2 border-dashed border-outline-variant hover:border-secondary rounded-xl p-6 text-center bg-slate-50 transition-all cursor-pointer" onclick="document.getElementById('pdpUploadInput').click()">
                            <span class="material-symbols-outlined text-4xl text-secondary">cloud_upload</span>
                            <p class="text-xs font-bold text-primary mt-2">Baskıya hazır dosyanızı buraya sürükleyin veya seçin</p>
                            <p class="text-[11px] text-on-surface-variant mt-1">Desteklenen: PDF, AI, PSD, EPS, TIFF, ZIP (Maks. 250 MB)</p>
                            <input type="file" name="print_file" class="hidden" id="pdpUploadInput" onchange="handleFileSelected(this)">
                            <button type="button" class="mt-3 px-4 py-2 bg-white text-xs font-bold rounded-lg border border-outline-variant text-primary shadow-xs hover:bg-slate-100">
                                Dosya Seç
                            </button>
                        </div>
                        <div id="fileSelectedBadge" class="hidden p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-800 flex items-center justify-between">
                            <span id="selectedFileName">dosya.pdf</span>
                            <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                        </div>
                    </div>
                </div>

                <!-- STICKY SUMMARY & ORDER DOCK -->
                <div class="bg-surface-container-lowest p-5 rounded-2xl border-2 border-primary/10 shadow-lg sticky bottom-4 z-30">
                    <div class="flex items-end justify-between mb-3 pb-3 border-b border-outline-variant">
                        <div>
                            <span class="text-xs text-on-surface-variant block">Toplam Fiyat</span>
                            <div class="flex items-baseline gap-1.5 mt-0.5">
                                <span class="text-2xl font-extrabold font-label-numeric text-primary" id="dockTotalPrice">650,00 ₺</span>
                                <span class="text-[11px] text-on-surface-variant" id="dockKdvBadge">+130 ₺ KDV Dahil</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-secondary bg-blue-50 px-2.5 py-1 rounded-md border border-blue-200" id="dockUnitPrice">
                                0.65 ₺ / Adet
                            </span>
                        </div>
                    </div>

                    <!-- Free Shipping Meter -->
                    <div class="mb-4">
                        <div class="flex justify-between items-center text-xs mb-1">
                            <span class="font-bold text-emerald-800 flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">local_shipping</span>
                                750 ₺ üzeri Ücretsiz Kargo
                            </span>
                            <span class="text-[11px] font-bold text-emerald-700" id="dockShippingNotice">Kargoya 100 ₺ kaldı</span>
                        </div>
                        <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-emerald-600 h-full rounded-full transition-all duration-300" id="dockShippingBar" style="width: 86%;"></div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <button type="submit" class="w-full bg-brand hover:bg-orange-600 active:scale-98 text-white py-3.5 px-6 rounded-xl font-bold text-sm tracking-wide shadow-lg shadow-orange-500/20 flex items-center justify-center gap-2 transition-all">
                        <span class="material-symbols-outlined text-xl">shopping_cart</span>
                        <span>Sepete Ekle &amp; Siparişi Onayla</span>
                    </button>
                    
                    <div class="flex items-center justify-center gap-4 text-[11px] text-on-surface-variant pt-2.5">
                        <span class="flex items-center gap-1"><span class="material-symbols-outlined text-xs text-emerald-600">lock</span> 256-Bit SSL Güvenli</span>
                        <span>•</span>
                        <span class="flex items-center gap-1"><span class="material-symbols-outlined text-xs text-emerald-600">verified</span> %100 Baskı Kalite Garantisi</span>
                    </div>
                </div>
            </form>
        </section>
    </div>
</main>

<!-- ========================================================================= -->
<!-- TAMBASKI ÇALIŞAN CANVA STÜDYO VEKTÖR MODALI (FABRIC.JS DESTEKLİ)         -->
<!-- ========================================================================= -->
<div class="modal fade" id="canvaStudioModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content border-0 rounded-0 bg-slate-900 text-white flex flex-col h-screen">
            
            <!-- Modal Header -->
            <div class="h-14 bg-slate-950 border-b border-slate-800 px-4 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <button type="button" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 rounded-lg text-xs font-bold text-slate-200 flex items-center gap-1" data-bs-dismiss="modal">
                        <span class="material-symbols-outlined text-base">arrow_back</span>
                        <span>Ürüne Dön</span>
                    </button>
                    <div class="h-4 w-px bg-slate-800"></div>
                    <div>
                        <span class="text-xs font-bold text-white">TamBaskı Online Tasarım Stüdyosu</span>
                        <span class="text-[10px] text-slate-400 block">84 x 52 mm • 300 DPI Matbaa Çıktısı</span>
                    </div>
                </div>

                <!-- Side Switcher (Ön / Arka Yüz) -->
                <div class="flex items-center bg-slate-900 p-1 rounded-xl border border-slate-800 text-xs">
                    <button type="button" class="px-3.5 py-1 rounded-lg bg-secondary text-white font-bold transition-all" id="btnSideFront" onclick="switchCanvasSide('front')">
                        Ön Yüz
                    </button>
                    <button type="button" class="px-3.5 py-1 rounded-lg text-slate-400 hover:text-white font-semibold transition-all" id="btnSideBack" onclick="switchCanvasSide('back')">
                        Arka Yüz
                    </button>
                </div>

                <!-- Right Save CTA -->
                <div class="flex items-center gap-2">
                    <button type="button" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-lg shadow-md transition-all flex items-center gap-1.5" onclick="saveAndApplyDesign()">
                        <span class="material-symbols-outlined text-base">check_circle</span>
                        <span>Tasarımı Onayla &amp; Kaydet</span>
                    </button>
                </div>
            </div>

            <!-- Modal Body (Canvas Workspace) -->
            <div class="flex-1 flex overflow-hidden">
                <!-- Left Toolbar -->
                <aside class="w-72 bg-slate-950 border-r border-slate-800 flex flex-col p-4 overflow-y-auto space-y-5 shrink-0">
                    <!-- 1. Metin Araçları -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Metin Ekle</h4>
                        <div class="space-y-1.5">
                            <button type="button" class="w-full text-left px-3 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-xs font-bold text-white border border-slate-800" onclick="addTextToCanvas('heading')">
                                + Başlık / Şirket Adı Ekle
                            </button>
                            <button type="button" class="w-full text-left px-3 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-xs font-semibold text-slate-300 border border-slate-800" onclick="addTextToCanvas('subheading')">
                                + Alt Başlık / Unvan Ekle
                            </button>
                            <button type="button" class="w-full text-left px-3 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-xs text-slate-400 border border-slate-800" onclick="addTextToCanvas('body')">
                                + İletişim / Adres / Telefon Ekle
                            </button>
                        </div>
                    </div>

                    <!-- 2. Logo / Resim Yükleme -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Logo &amp; Görsel Yükle</h4>
                        <div class="p-3 border border-dashed border-slate-700 rounded-xl text-center bg-slate-900">
                            <label for="canvaImageFileInput" class="cursor-pointer text-xs font-bold text-secondary hover:underline block">
                                Bilgisayardan Logo Seç
                            </label>
                            <input type="file" id="canvaImageFileInput" accept="image/*" class="hidden" onchange="uploadLogoToCanvas(this)">
                            <span class="text-[10px] text-slate-500 block mt-1">PNG, JPG veya SVG</span>
                        </div>
                    </div>

                    <!-- 3. QR Kod Üretici -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">QR Kod Ekle</h4>
                        <div class="flex gap-2">
                            <input type="text" id="qrTextInput" placeholder="https://siteniz.com" class="bg-slate-900 border border-slate-800 rounded-lg px-2.5 py-1.5 text-xs text-white flex-1 focus:outline-none focus:border-secondary">
                            <button type="button" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-xs font-bold rounded-lg" onclick="generateQrOnCanvas()">
                                Ekle
                            </button>
                        </div>
                    </div>

                    <!-- 4. Şekiller & Renkler -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Şekil &amp; Arka Plan</h4>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <button type="button" class="p-2 bg-slate-900 hover:bg-slate-800 rounded-lg border border-slate-800 text-slate-300 font-semibold" onclick="addShapeToCanvas('rect')">
                                ■ Dikdörtgen
                            </button>
                            <button type="button" class="p-2 bg-slate-900 hover:bg-slate-800 rounded-lg border border-slate-800 text-slate-300 font-semibold" onclick="addShapeToCanvas('circle')">
                                ● Daire
                            </button>
                        </div>
                        <div class="mt-3">
                            <label class="block text-[11px] text-slate-400 mb-1">Kartvizit Arka Plan Rengi:</label>
                            <div class="flex items-center gap-2">
                                <input type="color" id="canvasBgColorPicker" value="#ffffff" class="w-8 h-8 rounded border-0 cursor-pointer bg-transparent" onchange="changeCanvasBg(this.value)">
                                <span class="text-xs text-slate-300">Renk Seçin</span>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Center Canvas Area -->
                <main class="flex-1 bg-slate-900 flex flex-col items-center justify-center p-6 relative overflow-auto">
                    <!-- Guides indicator -->
                    <div class="absolute top-4 left-6 flex items-center gap-4 text-[10px] bg-slate-950/80 border border-slate-800 px-3 py-1.5 rounded-lg text-slate-400">
                        <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-red-500"></span> Kırmızı: Taşma Payı (86x54mm)</span>
                        <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Mavi: Güvenli Alan</span>
                    </div>

                    <!-- The Artboard -->
                    <div class="relative shadow-2xl rounded-sm p-4 bg-slate-800 border border-slate-700">
                        <canvas id="canvaMainCanvas" width="850" height="500" class="rounded-sm bg-white"></canvas>
                    </div>
                </main>
            </div>
        </div>
    </div>
</div>

<!-- ================= SCRIPTS ================= -->
<script src="assets/js/fabric.min.js"></script>
<script>
// Global PDP State
let currentPackageKey = 'standart';
let currentPackageTitle = 'Standart Paket (350gr Kuşe, Çift Yön, Mat Selefon)';
let currentBasePrice = 650;
let currentQuantity = 1000;
let currentQtyMultiplier = 1.0;
let canvasInstance = null;
let currentSide = 'front';
let sidesCanvasData = {
    front: null,
    back: null
};

// 1. Paket Seçimi
function selectPrintPackage(key, title, basePrice, paper, lamination, finish) {
    currentPackageKey = key;
    currentPackageTitle = title;
    currentBasePrice = basePrice;

    // UI Updates
    document.querySelectorAll('.pkg-card').forEach(el => {
        el.classList.remove('border-secondary', 'bg-blue-50/40');
        el.classList.add('border-outline-variant');
    });
    const activeCard = document.getElementById('pkgCard_' + key);
    if (activeCard) {
        activeCard.classList.remove('border-outline-variant');
        activeCard.classList.add('border-secondary', 'bg-blue-50/40');
    }

    // Specifications Updates
    const paperEl = document.getElementById('specPaperText');
    const lamEl = document.getElementById('specLaminationText');
    const finEl = document.getElementById('specFinishText');
    const stagePaper = document.getElementById('stagePaperBadge');
    const stageFinish = document.getElementById('stageFinishBadge');

    if (paperEl) paperEl.textContent = paper;
    if (lamEl) lamEl.textContent = lamination;
    if (finEl) finEl.textContent = finish;
    if (stagePaper) stagePaper.textContent = paper;
    if (stageFinish) stageFinish.textContent = finish;

    // Hidden form values
    document.getElementById('formPackageName').value = key;

    // Update quantity buttons prices based on new package
    updateQuantityPrices();
    recalculateTotal();
}

// 2. Adet Fiyatlarını Hesapla
function updateQuantityPrices() {
    const tiers = [
        { id: 1000, mult: 1.0 },
        { id: 2000, mult: 1.65 },
        { id: 3000, mult: 2.25 },
        { id: 5000, mult: 3.4 },
        { id: 10000, mult: 6.0 }
    ];

    tiers.forEach(t => {
        const total = Math.round(currentBasePrice * t.mult);
        const unit = (total / t.id).toFixed(3);
        const pEl = document.getElementById('qtyPrice_' + t.id);
        const uEl = document.getElementById('qtyUnit_' + t.id);
        if (pEl) pEl.textContent = total.toLocaleString('tr-TR') + ' ₺';
        if (uEl) uEl.textContent = unit + ' ₺ / Adet';
    });
}

// 3. Adet Seçimi
function selectQuantity(qty, multiplier) {
    currentQuantity = qty;
    currentQtyMultiplier = multiplier;

    document.querySelectorAll('.qty-btn').forEach(btn => {
        btn.classList.remove('border-secondary', 'bg-blue-50/40');
        btn.classList.add('border-outline-variant');
        const titleSpan = btn.querySelector('.text-secondary');
        if (titleSpan) {
            titleSpan.classList.remove('text-secondary');
            titleSpan.classList.add('text-primary');
        }
    });

    const activeBtn = document.getElementById('qtyBtn_' + qty);
    if (activeBtn) {
        activeBtn.classList.remove('border-outline-variant');
        activeBtn.classList.add('border-secondary', 'bg-blue-50/40');
        const titleSpan = activeBtn.querySelector('.text-primary');
        if (titleSpan) {
            titleSpan.classList.remove('text-primary');
            titleSpan.classList.add('text-secondary');
        }
    }

    document.getElementById('formQuantity').value = qty;
    recalculateTotal();
}

// 4. Toplam Fiyat Hesapla
function recalculateTotal() {
    const total = Math.round(currentBasePrice * currentQtyMultiplier);
    const kdv = Math.round(total * 0.20);
    const unitPrice = (total / currentQuantity).toFixed(3);

    document.getElementById('formPrice').value = total;
    document.getElementById('dockTotalPrice').textContent = total.toLocaleString('tr-TR') + ',00 ₺';
    document.getElementById('dockKdvBadge').textContent = '+' + kdv.toLocaleString('tr-TR') + ' ₺ KDV Dahil';
    document.getElementById('dockUnitPrice').textContent = unitPrice + ' ₺ / Adet';

    // Ücretsiz Kargo İlerlemesi (750 ₺)
    const progressBar = document.getElementById('dockShippingBar');
    const noticeEl = document.getElementById('dockShippingNotice');
    if (total >= 750) {
        progressBar.style.width = '100%';
        progressBar.classList.add('bg-emerald-600');
        noticeEl.textContent = 'Kargonuz Ücretsiz!';
        noticeEl.classList.add('text-emerald-700');
    } else {
        const remaining = 750 - total;
        const pct = Math.min(100, Math.round((total / 750) * 100));
        progressBar.style.width = pct + '%';
        noticeEl.textContent = 'Kargoya ' + remaining + ' ₺ kaldı';
    }
}

// 5. Tasarım Yöntemi Sekmeleri
function switchDesignSection(section) {
    document.getElementById('section_editor').classList.add('hidden');
    document.getElementById('section_templates').classList.add('hidden');
    document.getElementById('section_upload').classList.add('hidden');

    document.getElementById('tabBtn_editor').classList.remove('bg-white', 'text-secondary', 'shadow-xs');
    document.getElementById('tabBtn_templates').classList.remove('bg-white', 'text-secondary', 'shadow-xs');
    document.getElementById('tabBtn_upload').classList.remove('bg-white', 'text-secondary', 'shadow-xs');

    const activeBtn = document.getElementById('tabBtn_' + section);
    if (activeBtn) {
        activeBtn.classList.add('bg-white', 'text-secondary', 'shadow-xs');
    }

    const activeSec = document.getElementById('section_' + section);
    if (activeSec) {
        activeSec.classList.remove('hidden');
    }

    document.getElementById('formDesignType').value = section;
}

// 6. Dosya Seçimi
function handleFileSelected(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const badge = document.getElementById('fileSelectedBadge');
        const nameEl = document.getElementById('selectedFileName');
        if (badge && nameEl) {
            nameEl.textContent = file.name + ' (' + (file.size / (1024 * 1024)).toFixed(2) + ' MB)';
            badge.classList.remove('hidden');
        }
    }
}

// 7. Sektör Şablon Filtreleme
function filterIndustryTemplates(slug) {
    const list = document.getElementById('industryTemplatesList');
    if (!list) return;

    if (slug === 'all') {
        list.innerHTML = `
            <div class="cursor-pointer border-2 border-secondary bg-blue-50/20 rounded-xl p-3 hover:shadow-md transition-all text-center" onclick="openCanvaStudioModal('minimalist')">
                <div class="aspect-[16/10] bg-slate-900 rounded-lg flex items-center justify-center p-2 mb-2 text-white">
                    <span class="text-[11px] font-bold text-amber-300">KURUMSAL PRESTİJ</span>
                </div>
                <span class="text-xs font-bold text-primary block">Modern Minimal Şablon</span>
                <span class="text-[10px] text-secondary font-semibold mt-1 block">Bu Şablonla Başla →</span>
            </div>
            <div class="cursor-pointer border border-outline-variant hover:border-secondary rounded-xl p-3 hover:shadow-md transition-all text-center" onclick="openCanvaStudioModal('blank')">
                <div class="aspect-[16/10] bg-white border border-dashed border-slate-300 rounded-lg flex flex-col items-center justify-center p-2 mb-2 text-slate-500">
                    <span class="material-symbols-outlined text-2xl text-secondary">add_circle</span>
                    <span class="text-[10px] font-bold mt-1">Boş Tuvalle Başla</span>
                </div>
                <span class="text-xs font-bold text-primary block">Sıfırdan Tasarla</span>
                <span class="text-[10px] text-on-surface-variant mt-1 block">Özel Çizim Yap →</span>
            </div>
        `;
    } else {
        const sectorName = document.querySelector('#industrySectorSelect option:checked')?.textContent || 'Seçili Sektör';
        list.innerHTML = `
            <div class="col-span-2 p-4 rounded-xl bg-blue-50 border border-secondary/20 text-center">
                <span class="material-symbols-outlined text-3xl text-secondary mb-1">dashboard_customize</span>
                <h4 class="text-xs font-bold text-primary">${sectorName} İçin Özel Şablonlar Hazırlanıyor</h4>
                <p class="text-[11px] text-on-surface-variant mt-1">Sektörünüze özel 30+ vektör şablonumuz yakında eklenecektir. Şimdilik editörümüzü başlatıp kurumsal logonuz ve bilgilerinizle tek tıkla başlayabilirsiniz.</p>
                <button type="button" class="mt-3 px-4 py-2 bg-secondary text-white text-xs font-bold rounded-lg shadow-sm hover:bg-blue-600 transition-all" onclick="openCanvaStudioModal('blank')">
                    ${sectorName} Tasarımını Başlat
                </button>
            </div>
        `;
    }
}

// 8. Canva Stüdyo Modalı & Fabric.js Motoru
function openCanvaStudioModal(preset = 'minimalist') {
    const modalEl = document.getElementById('canvaStudioModal');
    if (!modalEl) return;

    if (window.bootstrap && bootstrap.Modal) {
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    } else {
        modalEl.classList.remove('fade');
        modalEl.style.display = 'block';
    }

    setTimeout(() => {
        initFabricCanvas(preset);
    }, 200);
}

function initFabricCanvas(preset) {
    if (!window.fabric) return;

    if (!canvasInstance) {
        canvasInstance = new fabric.Canvas('canvaMainCanvas', {
            width: 850,
            height: 500,
            backgroundColor: '#ffffff'
        });
    }

    if (preset === 'minimalist' || preset === 'default') {
        canvasInstance.clear();
        canvasInstance.setBackgroundColor('#0f172a', canvasInstance.renderAll.bind(canvasInstance));

        // Altın/Vurgu Çizgi
        const line = new fabric.Line([60, 180, 200, 180], {
            stroke: '#f15a24',
            strokeWidth: 3,
            selectable: false
        });
        canvasInstance.add(line);

        // Başlık
        const companyText = new fabric.IText('TAMBASKI MATBAA', {
            left: 60,
            top: 70,
            fontFamily: 'Inter',
            fontSize: 26,
            fontWeight: 'bold',
            fill: '#ffffff'
        });
        canvasInstance.add(companyText);

        const sloganText = new fabric.IText('Kurumsal Matbaa & Dijital Baskı', {
            left: 60,
            top: 110,
            fontFamily: 'Inter',
            fontSize: 14,
            fill: '#94a3b8'
        });
        canvasInstance.add(sloganText);

        const nameText = new fabric.IText('Ahmet Yılmaz', {
            left: 60,
            top: 230,
            fontFamily: 'Inter',
            fontSize: 28,
            fontWeight: 'bold',
            fill: '#ffffff'
        });
        canvasInstance.add(nameText);

        const titleText = new fabric.IText('Genel Müdür / Kurucu', {
            left: 60,
            top: 270,
            fontFamily: 'Inter',
            fontSize: 15,
            fill: '#f15a24'
        });
        canvasInstance.add(titleText);

        const contactText = new fabric.IText("📞 0544 000 00 00\n✉️ info@tambaski.com.tr\n🌐 www.tambaski.com.tr\n📍 Topkapı Matbaacılar Sitesi / İstanbul", {
            left: 450,
            top: 230,
            fontFamily: 'Inter',
            fontSize: 14,
            lineHeight: 1.5,
            fill: '#cbd5e1'
        });
        canvasInstance.add(contactText);

    } else if (preset === 'blank') {
        canvasInstance.clear();
        canvasInstance.setBackgroundColor('#ffffff', canvasInstance.renderAll.bind(canvasInstance));
    }

    canvasInstance.renderAll();
}

function addTextToCanvas(type) {
    if (!canvasInstance) return;

    let textObj;
    if (type === 'heading') {
        textObj = new fabric.IText('ŞİRKET ADINIZ', {
            left: 100, top: 100, fontFamily: 'Inter', fontSize: 28, fontWeight: 'bold', fill: '#0f172a'
        });
    } else if (type === 'subheading') {
        textObj = new fabric.IText('Ad Soyad - Unvan', {
            left: 100, top: 150, fontFamily: 'Inter', fontSize: 18, fontWeight: '600', fill: '#f15a24'
        });
    } else {
        textObj = new fabric.IText('+90 544 000 00 00\ninfo@siteniz.com', {
            left: 100, top: 200, fontFamily: 'Inter', fontSize: 14, fill: '#475569'
        });
    }

    canvasInstance.add(textObj);
    canvasInstance.setActiveObject(textObj);
    canvasInstance.renderAll();
}

function uploadLogoToCanvas(input) {
    if (!canvasInstance || !input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        fabric.Image.fromURL(e.target.result, function(img) {
            img.scaleToWidth(160);
            img.set({ left: 100, top: 100 });
            canvasInstance.add(img);
            canvasInstance.setActiveObject(img);
            canvasInstance.renderAll();
        });
    };
    reader.readAsDataURL(input.files[0]);
}

function addShapeToCanvas(shape) {
    if (!canvasInstance) return;
    let obj;
    if (shape === 'rect') {
        obj = new fabric.Rect({
            left: 150, top: 150, width: 140, height: 80, fill: '#f15a24', rx: 8, ry: 8
        });
    } else {
        obj = new fabric.Circle({
            left: 150, top: 150, radius: 45, fill: '#2563eb'
        });
    }
    canvasInstance.add(obj);
    canvasInstance.setActiveObject(obj);
    canvasInstance.renderAll();
}

function changeCanvasBg(color) {
    if (!canvasInstance) return;
    canvasInstance.setBackgroundColor(color, canvasInstance.renderAll.bind(canvasInstance));
}

function generateQrOnCanvas() {
    const text = document.getElementById('qrTextInput').value.trim() || 'https://tambaski.com.tr';
    const qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' + encodeURIComponent(text);
    fabric.Image.fromURL(qrUrl, function(img) {
        img.scaleToWidth(90);
        img.set({ left: 680, top: 70 });
        canvasInstance.add(img);
        canvasInstance.setActiveObject(img);
        canvasInstance.renderAll();
    }, { crossOrigin: 'anonymous' });
}

function switchCanvasSide(side) {
    if (!canvasInstance || currentSide === side) return;
    // Save current
    sidesCanvasData[currentSide] = canvasInstance.toJSON();
    currentSide = side;

    document.getElementById('btnSideFront').className = side === 'front' 
        ? 'px-3.5 py-1 rounded-lg bg-secondary text-white font-bold transition-all'
        : 'px-3.5 py-1 rounded-lg text-slate-400 hover:text-white font-semibold transition-all';
    document.getElementById('btnSideBack').className = side === 'back'
        ? 'px-3.5 py-1 rounded-lg bg-secondary text-white font-bold transition-all'
        : 'px-3.5 py-1 rounded-lg text-slate-400 hover:text-white font-semibold transition-all';

    canvasInstance.clear();
    if (sidesCanvasData[side]) {
        canvasInstance.loadFromJSON(sidesCanvasData[side], canvasInstance.renderAll.bind(canvasInstance));
    } else {
        canvasInstance.setBackgroundColor(side === 'back' ? '#ffffff' : '#0f172a', canvasInstance.renderAll.bind(canvasInstance));
        if (side === 'back') {
            const qrInfo = new fabric.IText("QR Kodu Okutarak\nDijital Kartviziti İnceleyin", {
                left: 320, top: 180, fontFamily: 'Inter', fontSize: 18, textAlign: 'center', fill: '#0f172a'
            });
            canvasInstance.add(qrInfo);
        }
        canvasInstance.renderAll();
    }
}

function saveAndApplyDesign() {
    if (!canvasInstance) return;
    const svg = canvasInstance.toSVG();
    document.getElementById('formDesignSvg').value = svg;
    document.getElementById('formDesignType').value = 'canva_studio';

    // Update 3D card preview
    const dataUrl = canvasInstance.toDataURL({ format: 'png', quality: 0.9 });
    const tiltCard = document.getElementById('tiltCard');
    if (tiltCard) {
        tiltCard.style.backgroundImage = `url(${dataUrl})`;
        tiltCard.style.backgroundSize = 'cover';
        tiltCard.style.backgroundPosition = 'center';
    }

    // Close Modal
    const modalEl = document.getElementById('canvaStudioModal');
    if (window.bootstrap && bootstrap.Modal) {
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    } else {
        modalEl.style.display = 'none';
    }

    alert('Tasarımınız başarıyla kaydedildi ve siparişinize eklendi!');
}

// 9. 3D Tilt Effect
document.addEventListener('DOMContentLoaded', () => {
    const stage = document.getElementById('tiltStage');
    const card = document.getElementById('tiltCard');
    const glare = document.getElementById('cardGlare');

    if (stage && card) {
        stage.addEventListener('mousemove', (e) => {
            const rect = stage.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            const rotateX = (-y / rect.height) * 25;
            const rotateY = (x / rect.width) * 25;

            card.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
            if (glare) {
                const glareX = ((e.clientX - rect.left) / rect.width) * 100;
                const glareY = ((e.clientY - rect.top) / rect.height) * 100;
                glare.style.background = `radial-gradient(circle at ${glareX}% ${glareY}%, rgba(255,255,255,0.25) 0%, transparent 60%)`;
            }
        });

        stage.addEventListener('mouseleave', () => {
            card.style.transform = 'rotateX(0deg) rotateY(0deg)';
        });
    }

    // Init with defaults
    recalculateTotal();
});

let isFlipped = false;
function flipCardDemo() {
    const card = document.getElementById('tiltCard');
    if (!card) return;
    isFlipped = !isFlipped;
    card.style.transform = isFlipped ? 'rotateY(180deg)' : 'rotateY(0deg)';
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
