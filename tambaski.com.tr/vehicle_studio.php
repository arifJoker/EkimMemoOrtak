<?php
require_once __DIR__ . '/classes/Product.php';
$productObj = new Product();
$settings = $productObj->getSiteSettings();
?>
<!DOCTYPE html>
<html lang="tr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3D Canlı Araç Sticker & Giydirme Stüdyosu | <?php echo htmlspecialchars($settings['site_title'] ?? 'TamBaskı'); ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        tb: {
                            orange: '#f15a24',
                            hover: '#d94e1d',
                            dark: '#0b0f17',
                            card: '#131b26',
                            border: '#1e293b'
                        }
                    }
                }
            }
        }
    </script>
    <!-- Fabric.js Vektörel Editör -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0b0f17;
            color: #f8fafc;
        }
        .canvas-container {
            position: absolute !important;
            top: 0;
            left: 0;
            z-index: 20;
        }
        .custom-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: #0f172a;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }
        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #f15a24;
        }
    </style>
</head>
<body class="h-screen flex flex-col overflow-hidden select-none">

    <!-- ÜST BAR: ARAÇ SEÇİMİ VE BİLGİLERİ -->
    <header class="h-16 bg-slate-900 border-b border-slate-800 flex items-center justify-between px-6 z-30 shrink-0">
        <div class="flex items-center space-x-6">
            <a href="index.php" class="flex items-center space-x-2 text-white hover:text-tb-orange transition-colors">
                <i class="fa-solid fa-arrow-left text-sm"></i>
                <img src="assets/images/logo.svg" alt="TamBaskı" class="h-7 w-auto" onerror="this.src='https://via.placeholder.com/120x35?text=TAMBASKI'">
            </a>
            <div class="h-6 w-px bg-slate-800"></div>
            <div class="flex items-center space-x-2 text-sm font-semibold">
                <span class="text-tb-orange"><i class="fa-solid fa-car-side mr-1.5"></i> 3D Canlı Araç Giydirme & 1:1 Sticker Stüdyosu</span>
            </div>
        </div>

        <!-- Araç Hızlı Filtre Barı (1990-2026) -->
        <div class="flex items-center space-x-3">
            <div class="flex items-center bg-slate-800/80 rounded-lg p-1 border border-slate-700">
                <span class="text-xs text-slate-400 font-bold px-2">MARKA:</span>
                <select id="vehicleBrandSelect" class="bg-slate-900 text-white text-xs font-semibold rounded px-2.5 py-1.5 outline-none border border-slate-700 focus:border-tb-orange">
                    <!-- JS ile dinamik doldurulur -->
                </select>
            </div>

            <div class="flex items-center bg-slate-800/80 rounded-lg p-1 border border-slate-700">
                <span class="text-xs text-slate-400 font-bold px-2">MODEL & YIL:</span>
                <select id="vehicleModelSelect" class="bg-slate-900 text-white text-xs font-semibold rounded px-2.5 py-1.5 outline-none border border-slate-700 focus:border-tb-orange">
                    <!-- JS ile dinamik doldurulur -->
                </select>
            </div>

            <div class="hidden md:flex items-center space-x-2">
                <span id="vehicleCategoryBadge" class="text-[11px] bg-slate-800 text-slate-300 px-2.5 py-1 rounded-full border border-slate-700 font-medium">Binek Sedan</span>
                <span id="vehicleLengthBadge" class="text-[11px] bg-orange-500/10 text-orange-400 px-2.5 py-1 rounded-full border border-orange-500/30 font-bold">453 cm (4532 mm)</span>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            <button id="btnDeleteSelectedSticker" style="display: none;" class="text-xs font-bold text-red-400 bg-red-950/40 border border-red-800/60 hover:bg-red-900/60 px-3 py-1.5 rounded-lg transition-all items-center space-x-1">
                <i class="fa-solid fa-trash-can"></i>
                <span>Seçili Stickerı Sil</span>
            </button>
            <a href="product.php" class="text-xs text-slate-400 hover:text-white px-3 py-1.5 rounded-lg border border-slate-700 hover:bg-slate-800 transition-colors">
                <i class="fa-solid fa-xmark mr-1"></i> Çıkış
            </a>
        </div>
    </header>

    <!-- ANA ÇALIŞMA ALANI: 3 SÜTUN (SOL AYARLAR - ORTA SAHNE - SAĞ FİYAT & SEPET) -->
    <div class="flex-1 flex overflow-hidden">
        
        <!-- SOL PANEL: KAPORTA BOYASI, 0-4 CAM FİLMİ & STICKER KÜTÜPHANESİ -->
        <aside class="w-80 bg-slate-900/95 border-r border-slate-800 flex flex-col shrink-0 custom-scroll overflow-y-auto">
            
            <!-- 1. KAPORTA BOYASI -->
            <div class="p-4 border-b border-slate-800">
                <div class="flex items-center justify-between mb-2.5">
                    <span class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center">
                        <i class="fa-solid fa-palette text-tb-orange mr-2"></i> Canlı Kaporta Rengi
                    </span>
                    <input type="color" id="vehicleColorPicker" value="#e2e8f0" class="w-6 h-6 rounded border-0 cursor-pointer bg-transparent">
                </div>
                <!-- Hızlı Renk Paleti -->
                <div class="grid grid-cols-6 gap-2">
                    <button type="button" class="car-color-preset w-8 h-8 rounded-full border-2 border-slate-600 shadow-sm hover:scale-110 transition-transform bg-[#ffffff]" data-color="#ffffff" title="Beyaz"></button>
                    <button type="button" class="car-color-preset w-8 h-8 rounded-full border-2 border-slate-600 shadow-sm hover:scale-110 transition-transform bg-[#0f172a]" data-color="#0f172a" title="Gece Siyahı"></button>
                    <button type="button" class="car-color-preset w-8 h-8 rounded-full border-2 border-slate-600 shadow-sm hover:scale-110 transition-transform bg-[#475569]" data-color="#475569" title="Füme Gri"></button>
                    <button type="button" class="car-color-preset w-8 h-8 rounded-full border-2 border-slate-600 shadow-sm hover:scale-110 transition-transform bg-[#dc2626]" data-color="#dc2626" title="Yarış Kırmızısı"></button>
                    <button type="button" class="car-color-preset w-8 h-8 rounded-full border-2 border-slate-600 shadow-sm hover:scale-110 transition-transform bg-[#1d4ed8]" data-color="#1d4ed8" title="Safir Mavi"></button>
                    <button type="button" class="car-color-preset w-8 h-8 rounded-full border-2 border-slate-600 shadow-sm hover:scale-110 transition-transform bg-[#eab308]" data-color="#eab308" title="Metalik Sarı"></button>
                </div>
            </div>

            <!-- 2. CAM FİLMİ DERECESİ (0 - 4 NUMARA) -->
            <div class="p-4 border-b border-slate-800 bg-slate-900/40">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center">
                        <i class="fa-solid fa-glasses text-sky-400 mr-2"></i> Cam Filmi Derecesi
                    </span>
                    <span class="text-[10px] text-slate-400">0 - 4 Numara</span>
                </div>
                <div class="grid grid-cols-5 gap-1.5">
                    <button type="button" class="tint-btn py-2 px-1 rounded border border-slate-700 bg-slate-800 text-center hover:border-tb-orange transition-all" data-tint="0">
                        <span class="block text-xs font-bold">0 No</span>
                        <span class="text-[9px] text-slate-400">Şeffaf</span>
                    </button>
                    <button type="button" class="tint-btn py-2 px-1 rounded border border-slate-700 bg-slate-800 text-center hover:border-tb-orange transition-all" data-tint="1">
                        <span class="block text-xs font-bold">1 No</span>
                        <span class="text-[9px] text-slate-400">Açık</span>
                    </button>
                    <button type="button" class="tint-btn py-2 px-1 rounded border border-orange-500 bg-orange-500 text-white text-center transition-all" data-tint="2">
                        <span class="block text-xs font-bold">2 No</span>
                        <span class="text-[9px] opacity-90">Orta</span>
                    </button>
                    <button type="button" class="tint-btn py-2 px-1 rounded border border-slate-700 bg-slate-800 text-center hover:border-tb-orange transition-all" data-tint="3">
                        <span class="block text-xs font-bold">3 No</span>
                        <span class="text-[9px] text-slate-400">Koyu</span>
                    </button>
                    <button type="button" class="tint-btn py-2 px-1 rounded border border-slate-700 bg-slate-800 text-center hover:border-tb-orange transition-all" data-tint="4">
                        <span class="block text-xs font-bold">4 No</span>
                        <span class="text-[9px] text-slate-400">VIP</span>
                    </button>
                </div>
                <p class="text-[10px] text-slate-400 mt-2">
                    <i class="fa-solid fa-circle-info text-sky-400 mr-1"></i> Cam filminin koyuluğuna göre stickerın cam üzerinde nasıl duracağını canlı simüle edin.
                </p>
            </div>

            <!-- 3. ÖZEL GÖRSEL / LOGO YÜKLE -->
            <div class="p-4 border-b border-slate-800">
                <label class="flex flex-col items-center justify-center p-3 border-2 border-dashed border-slate-700 hover:border-tb-orange rounded-xl bg-slate-800/40 hover:bg-slate-800/80 cursor-pointer transition-all">
                    <i class="fa-solid fa-cloud-arrow-up text-lg text-tb-orange mb-1"></i>
                    <span class="text-xs font-bold text-slate-200">Kendi Logonuzu / Stickerınızı Yükleyin</span>
                    <span class="text-[10px] text-slate-400 mt-0.5">PNG, SVG, JPG (Şeffaf zemin önerilir)</span>
                    <input type="file" id="customStickerUpload" accept="image/png, image/jpeg, image/svg+xml" class="hidden">
                </label>
            </div>

            <!-- 4. HAZIR STICKER VE GRAFİK KÜTÜPHANESİ -->
            <div class="p-4 flex-1">
                <div class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-3 flex items-center">
                    <i class="fa-solid fa-shapes text-tb-orange mr-2"></i> Popüler Sticker Kataloğu
                </div>
                <div id="stickerCategoriesAccordion">
                    <!-- JS ile kategoriler doldurulur -->
                </div>
            </div>
        </aside>

        <!-- ORTA PANEL: CANLI 3D / 2.5D ARAÇ SAHNESİ -->
        <main class="flex-1 bg-slate-950 flex flex-col items-center justify-center p-6 relative overflow-hidden">
            
            <!-- Izgara / Zemin Efekti -->
            <div class="absolute inset-0 bg-[radial-gradient(#1e293b_1px,transparent_1px)] [background-size:24px_24px] opacity-30 pointer-events-none"></div>

            <!-- Araç Tuval Alanı Konteyneri -->
            <div class="relative w-[1020px] h-[440px] bg-slate-900/60 rounded-2xl border border-slate-800 shadow-2xl flex items-center justify-center overflow-hidden">
                
                <!-- Alt Katman: Dinamik SVG Araç Çizimi -->
                <div id="vehicleStageSvg" class="absolute inset-0 w-full h-full pointer-events-none flex items-center justify-center z-10">
                    <!-- SVG Buraya Yüklenir -->
                </div>

                <!-- Üst Katman: Fabric.js Etkileşimli Sticker Tuvali -->
                <canvas id="vehicleFabricCanvas" width="1020" height="440" class="relative z-20"></canvas>
            </div>

            <!-- Sahne Altı Kılavuz & İpuçları -->
            <div class="mt-4 flex items-center space-x-6 text-xs text-slate-400">
                <span><i class="fa-solid fa-arrows-up-down-left-right text-tb-orange mr-1"></i> Stickerı sürükleyerek çamurluk, kapı veya cama yerleştirin</span>
                <span><i class="fa-solid fa-expand text-tb-orange mr-1"></i> Köşelerden tutup büyüterek birebir milimetrik ölçü ayarlayın</span>
                <span><i class="fa-solid fa-rotate text-tb-orange mr-1"></i> Üst tutamaçla istediğiniz açıda döndürün</span>
            </div>
        </main>

        <!-- SAĞ PANEL: CANLI 1:1 ÖLÇÜ, MALZEME & DİNAMİK FİYAT HESAPLAMA -->
        <aside class="w-84 bg-slate-900 border-l border-slate-800 flex flex-col justify-between p-5 shrink-0 z-20">
            <div>
                <!-- Başlık -->
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center">
                    <i class="fa-solid fa-ruler-combined text-tb-orange mr-2"></i> Bire Bir (1:1) Ölçü & Fiyat
                </div>

                <!-- Canlı Ölçü Kartı -->
                <div class="bg-slate-800/80 rounded-xl p-4 border border-slate-700 mb-4">
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div class="bg-slate-900/90 p-2.5 rounded-lg border border-slate-700/60">
                            <span class="text-[10px] text-slate-400 font-bold block uppercase">Genişlik (En)</span>
                            <span id="hudWidthCm" class="text-lg font-black text-white font-mono">45.0 cm</span>
                        </div>
                        <div class="bg-slate-900/90 p-2.5 rounded-lg border border-slate-700/60">
                            <span class="text-[10px] text-slate-400 font-bold block uppercase">Yükseklik (Boy)</span>
                            <span id="hudHeightCm" class="text-lg font-black text-white font-mono">20.0 cm</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs text-slate-400 pt-2 border-t border-slate-700/60">
                        <span>Toplam Baskı Alanı:</span>
                        <span id="hudAreaM2" class="font-bold text-slate-200 font-mono">0.090 m²</span>
                    </div>
                </div>

                <!-- Folyo Malzeme Seçici -->
                <div class="mb-4">
                    <label class="text-xs font-bold text-slate-300 block mb-1.5">
                        <i class="fa-solid fa-scroll text-tb-orange mr-1"></i> Folyo Malzemesi
                    </label>
                    <select id="stickerMaterialSelect" class="w-full bg-slate-800 text-white text-xs font-medium rounded-lg p-2.5 outline-none border border-slate-700 focus:border-tb-orange">
                        <!-- JS ile doldurulur -->
                    </select>
                </div>

                <!-- Özellikler Listesi -->
                <div class="space-y-2 text-[11px] text-slate-400 bg-slate-800/40 p-3 rounded-lg border border-slate-800">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-check text-emerald-400"></i>
                        <span>Güneş solmasına ve tazyikli suya %100 dayanıklı</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-check text-emerald-400"></i>
                        <span>Bire bir lazer plotter kesim hatları hazır</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-check text-emerald-400"></i>
                        <span>Kolay uygulama için transfer bandı dahil</span>
                    </div>
                </div>
            </div>

            <!-- Fiyat ve Sepete Ekle Butonu -->
            <div class="pt-4 border-t border-slate-800">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs text-slate-400 font-bold uppercase">Hesaplanan Tutar:</span>
                    <span id="hudLivePrice" class="text-2xl font-black text-emerald-400 font-mono">185,00 ₺</span>
                </div>
                <button type="button" id="btnVehicleAddToCart" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-emerald-900/30 flex items-center justify-center space-x-2 transition-all hover:scale-[1.02]">
                    <i class="fa-solid fa-cart-shopping text-sm"></i>
                    <span>Bire Bir Ölçüde Sepete Ekle</span>
                </button>
            </div>
        </aside>
    </div>

    <!-- Özel JS Motoru -->
    <script src="assets/js/vehicle_customizer.js"></script>
</body>
</html>
