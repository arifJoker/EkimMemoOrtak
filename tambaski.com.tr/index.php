<?php
/**
 * TAMBASKI.COM.TR - Anasayfa (Doğal Türk E-Ticaret & Online Tasarım Stüdyosu)
 */
require_once __DIR__ . '/includes/functions.php';

$page_title = "TAM BASKI | Hızlı ve Kaliteli Kurumsal Matbaa & Online Tasarım Merkezi";
$page_desc = "Kartvizit, broşür, etiket, cepli dosya ve pleksi kesim ürünlerinizi online tasarlayın veya dosyanızı yükleyin. 24 saatte hızlı üretim, 750 ₺ üzeri ücretsiz kargo.";
require_once __DIR__ . '/includes/header.php';

$all_categories = get_all_categories();
$featured_products = get_all_products(null, true);
?>

<!-- ================= HERO SECTION ================= -->
<section class="relative pt-10 pb-16 md:pt-14 md:pb-20 overflow-hidden subtle-grid border-b border-outline-variant/40 bg-gradient-to-b from-surface to-surface-container-lowest">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        
        <!-- Top Micro-Badge -->
        <div class="flex justify-center mb-6">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-surface-container-lowest border border-outline-variant shadow-xs">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-semibold text-primary">🚀 750 ₺ ve Üzeri Siparişlerde Kargo Ücretsiz!</span>
                <span class="text-outline-variant hidden sm:inline">|</span>
                <span class="text-xs text-secondary font-medium hidden sm:inline">Aynı Gün & 24 Saatte Hızlı Üretim</span>
            </div>
        </div>
        
        <!-- Headline & Subtitle -->
        <div class="text-center max-w-4xl mx-auto mb-10">
            <h1 class="text-3xl sm:text-5xl md:text-6xl font-extrabold text-primary tracking-tight mb-5 leading-tight">
                Markanızı Büyüten <span class="text-secondary">Kurumsal Baskı</span> & Reklam Çözümleri
            </h1>
            <p class="text-base sm:text-lg text-on-surface-variant max-w-2xl mx-auto leading-relaxed">
                Kartvizit, broşür, dosya ve özel kesim ürünlerinizi ister tek tıkla online stüdyomuzda kendiniz tasarlayın, ister hazır baskı dosyanızı anında yükleyin.
            </p>
            
            <!-- CTA Buttons -->
            <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                <a class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl bg-primary text-on-primary font-bold text-sm shadow-md hover:bg-slate-800 transition-all active:scale-95" href="#categories">
                    <span>Ürünleri & Fiyatları İncele</span>
                    <span class="material-symbols-outlined text-base">arrow_forward</span>
                </a>
                <a class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl bg-secondary text-white font-bold text-sm shadow-md shadow-blue-500/20 hover:bg-blue-600 transition-all active:scale-95" href="product.php?slug=ekonomik-kartvizit-250gr">
                    <span class="material-symbols-outlined text-lg">brush</span>
                    <span>Online Tasarıma Başla</span>
                </a>
                <a class="inline-flex items-center gap-2 px-5 py-3.5 rounded-xl bg-surface-container-lowest text-on-surface font-semibold text-sm border border-outline-variant shadow-xs hover:border-emerald-600 hover:text-emerald-700 transition-all active:scale-95" href="https://wa.me/905440000000" target="_blank">
                    <span class="material-symbols-outlined text-lg text-emerald-600">chat</span>
                    <span>WhatsApp Hızlı Fiyat</span>
                </a>
            </div>
            
            <!-- Live Badges -->
            <div class="mt-10 flex flex-wrap items-center justify-center gap-3 sm:gap-6 text-on-surface-variant">
                <div class="flex items-center gap-2 text-xs font-semibold bg-surface-container-lowest px-3.5 py-2 rounded-xl border border-outline-variant/70 shadow-xs">
                    <span class="material-symbols-outlined text-secondary text-lg">verified</span>
                    <span>Ücretsiz Baskı Prova Kontrolü</span>
                </div>
                <div class="flex items-center gap-2 text-xs font-semibold bg-surface-container-lowest px-3.5 py-2 rounded-xl border border-outline-variant/70 shadow-xs">
                    <span class="material-symbols-outlined text-secondary text-lg">bolt</span>
                    <span>24 Saatte Hızlı Sevkiyat</span>
                </div>
                <div class="flex items-center gap-2 text-xs font-semibold bg-surface-container-lowest px-3.5 py-2 rounded-xl border border-outline-variant/70 shadow-xs">
                    <span class="material-symbols-outlined text-secondary text-lg">factory</span>
                    <span>Doğrudan Üreticiden En İyi Fiyat</span>
                </div>
                <div class="flex items-center gap-2 text-xs font-semibold bg-surface-container-lowest px-3.5 py-2 rounded-xl border border-outline-variant/70 shadow-xs">
                    <span class="material-symbols-outlined text-secondary text-lg">lock</span>
                    <span>PayTR & 256-Bit SSL Güvencesi</span>
                </div>
            </div>
        </div>

        <!-- 3D Stationery Stage Container -->
        <div class="relative max-w-5xl mx-auto rounded-2xl bg-gradient-to-b from-surface-container-lowest to-surface-container-low p-2 md:p-3 border border-outline-variant shadow-xl">
            <div class="relative w-full rounded-xl overflow-hidden bg-surface-container-high aspect-[16/9] md:aspect-[21/9] flex items-center justify-center">
                <img class="w-full h-full object-cover" alt="Tam Baskı Kurumsal Matbaa ve Kartvizit Üretim Parkuru" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDz0TcydwvPaWv8eC5GJA0oh71XhZIcs0i4EBcwj15AZdXHUKeUhp2PgbxggR1pKRGLnw1gK3OSYfQ7Ne2UdoNjPjK2CC4N7l4fPxp0jEuN5OLA6pTnKQpwrZiHBGyhqCLGFFjPPWtkk4maQ-thaeyBKJaoKf4NI9uIK1of9QH2P8yxrKuT3vp6IoaeeaYBXv0WF6bOoJKoBcDZaFUVVA7uDh5xI2YtJ_yDznFwW7uCdj-kI0mVZ_IP"/>
                <!-- Floating Interactive Pill HUD -->
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 frosted-glass px-4 py-2 rounded-full shadow-lg flex items-center gap-4 text-xs font-medium text-primary">
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base text-secondary">360</span>
                        <span class="font-bold">Canlı Doku & Baskı Simülasyonu</span>
                    </div>
                    <span class="text-outline-variant">|</span>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                        <span class="font-label-numeric text-[11px] text-on-surface-variant font-semibold">350gr Mat Kuşe + Kabartma Lak</span>
                    </div>
                    <a href="product.php?slug=ekonomik-kartvizit-250gr" class="ml-2 px-3 py-1 rounded-full bg-primary text-on-primary text-[11px] font-bold hover:bg-slate-800 transition-colors">
                        Hemen Tasarla
                    </a>
                </div>
                <!-- Top-Right Status Tag -->
                <div class="absolute top-4 right-4 bg-surface-container-lowest/90 backdrop-blur-md px-3 py-1.5 rounded-lg border border-outline-variant/60 shadow-sm flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="font-label-numeric text-[11px] font-semibold text-primary">Üretim Hattı: Aktif (24s SLA)</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================= POPULAR PRINT CATEGORIES ================= -->
<section class="py-16 md:py-20 bg-surface-container-lowest border-b border-outline-variant/40" id="categories">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <!-- Section Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
            <div>
                <div class="flex items-center gap-2 text-secondary font-label-numeric text-xs tracking-wider uppercase font-bold mb-2">
                    <span class="material-symbols-outlined text-base">category</span>
                    <span>Zengin Ürün Portföyü</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-primary tracking-tight">
                    Popüler Baskı & Reklam Kategorileri
                </h2>
                <p class="text-sm text-on-surface-variant mt-1">
                    İhtiyacınıza uygun kategoriyi seçin, net fiyat hesaplayın veya tek tıkla online tasarlayın.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-on-surface-variant font-medium"><?= count($all_categories) ?> Ana Kategori</span>
                <a class="inline-flex items-center gap-1 text-xs font-bold text-secondary hover:underline" href="category.php?slug=kartvizit">
                    Tümünü Gör
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            <?php 
            $category_icons = [
                'kartvizit' => 'badge',
                'el-ilani-brosur' => 'menu_book',
                'kurumsal-urunler' => 'folder_open',
                'dekota-pleksi-kesim' => 'layers',
                'folyo-branda-reklam' => 'campaign',
                'etiket-sticker' => 'sell',
                'promosyon-hediyelik' => 'card_giftcard',
                'tekstil-canta' => 'shopping_bag',
                'ambalaj-kutu' => 'inventory_2',
                'kase-cesitleri' => 'approval',
                'acil-baski' => 'bolt'
            ];

            foreach (array_slice($all_categories, 0, 8) as $cat): 
                $icon = $category_icons[$cat['slug']] ?? 'layers';
            ?>
            <a href="category.php?slug=<?= urlencode($cat['slug']) ?>" class="group rounded-2xl border border-outline-variant/80 bg-surface p-5 hover:bg-surface-container-lowest hover:border-secondary hover:shadow-lg transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 border border-secondary/20 text-secondary flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-2xl"><?= $icon ?></span>
                    </div>
                    <h3 class="text-base font-bold text-primary mb-1 group-hover:text-secondary transition-colors">
                        <?= htmlspecialchars($cat['name']) ?>
                    </h3>
                    <p class="text-xs text-on-surface-variant leading-relaxed line-clamp-2">
                        <?= htmlspecialchars($cat['description'] ?? '') ?>
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-outline-variant/40 flex items-center justify-between text-xs font-bold text-secondary">
                    <span>Ürünleri İncele</span>
                    <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ================= FEATURED PRODUCTS (DATABASE DRIVEN) ================= -->
<section class="py-16 md:py-20 bg-surface border-b border-outline-variant/40" id="products">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
            <div>
                <div class="flex items-center gap-2 text-secondary font-label-numeric text-xs tracking-wider uppercase font-bold mb-2">
                    <span class="material-symbols-outlined text-base">star</span>
                    <span>En Çok Satanlar</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-primary tracking-tight">
                    Öne Çıkan Kurumsal Ürünler
                </h2>
                <p class="text-sm text-on-surface-variant mt-1">
                    On binlerce firmanın tercih ettiği garantili baskı kalitesi ve uygun hazır paketler.
                </p>
            </div>
            <div>
                <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-200">
                    ✓ Aynı Gün / 24 Saatte Hazır
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach (array_slice($featured_products, 0, 8) as $prod): 
                $price_display = !empty($prod['starting_price']) && $prod['starting_price'] > 0 
                    ? number_format($prod['starting_price'], 2, ',', '.') . ' ₺' 
                    : 'Fiyat Hesapla';
            ?>
            <div class="group rounded-2xl border border-outline-variant/80 bg-surface-container-lowest overflow-hidden hover:shadow-xl hover:border-secondary transition-all duration-200 flex flex-col">
                <div class="relative aspect-[4/3] bg-surface-container-low overflow-hidden flex items-center justify-center p-4">
                    <img class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300" 
                         alt="<?= htmlspecialchars($prod['name'] ?? $prod['title']) ?>" 
                         src="<?= htmlspecialchars($prod['image'] ?? 'assets/img/default_product.webp') ?>"
                         onerror="this.onerror=null; this.src='assets/img/products/kartvizit_eko.webp';">
                    
                    <div class="absolute top-3 left-3 bg-surface-container-lowest/90 backdrop-blur-md px-2.5 py-1 rounded text-primary font-label-numeric text-[11px] font-bold shadow-xs">
                        <?= htmlspecialchars($prod['category_name'] ?? 'Matbaa') ?>
                    </div>
                    <?php if (!empty($prod['is_urgent_available'])): ?>
                    <div class="absolute top-3 right-3 bg-emerald-600 text-white px-2 py-0.5 rounded text-[10px] font-bold">
                        ⚡ 24 Saatte
                    </div>
                    <?php endif; ?>
                </div>

                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-primary mb-1 group-hover:text-secondary transition-colors line-clamp-1">
                            <?= htmlspecialchars($prod['name'] ?? $prod['title']) ?>
                        </h3>
                        <p class="text-xs text-on-surface-variant mb-4 leading-relaxed line-clamp-2">
                            <?= htmlspecialchars($prod['short_desc'] ?? $prod['description'] ?? 'Yüksek kaliteli kurumsal baskı.') ?>
                        </p>
                    </div>

                    <div class="pt-4 flex items-center justify-between border-t border-outline-variant/40 mt-auto">
                        <div>
                            <span class="text-[10px] text-on-surface-variant font-label-numeric uppercase block">Başlangıç</span>
                            <span class="text-lg font-extrabold font-label-numeric text-primary">
                                <?= $price_display ?>
                            </span>
                        </div>
                        <a class="px-4 py-2 bg-primary hover:bg-secondary text-white rounded-xl text-xs font-bold transition-colors shadow-xs" href="product.php?slug=<?= urlencode($prod['slug']) ?>">
                            İncele & Tasarla
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ================= HOW IT WORKS (3 STEPS) ================= -->
<section class="py-16 md:py-20 bg-surface-container-lowest border-b border-outline-variant/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="font-label-numeric text-xs uppercase tracking-wider text-secondary font-bold">Zahmetsiz Süreç</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-primary tracking-tight mt-1 mb-3">
                3 Adımda Baskınız Kapınızda
            </h2>
            <p class="text-sm text-on-surface-variant">
                Matbaa süreçlerini karmaşıklıktan arındırdık. İster hazır dosyanızı yükleyin, ister online tasarlayın.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Step 1 -->
            <div class="p-6 rounded-2xl bg-surface border border-outline-variant/70 shadow-xs relative">
                <div class="w-12 h-12 rounded-xl bg-primary text-white flex items-center justify-center font-extrabold text-lg mb-5 shadow-sm">
                    1
                </div>
                <h3 class="text-base font-bold text-primary mb-2">Ürününüzü & Paketinizi Seçin</h3>
                <p class="text-xs text-on-surface-variant leading-relaxed">
                    Kartvizit, broşür veya tabelanızı seçin; adet ve özellik belirleyerek anında net fiyatınızı görün. Sürpriz ek maliyet yok.
                </p>
            </div>

            <!-- Step 2 -->
            <div class="p-6 rounded-2xl bg-blue-50/50 border border-secondary/30 shadow-xs relative">
                <div class="w-12 h-12 rounded-xl bg-secondary text-white flex items-center justify-center font-extrabold text-lg mb-5 shadow-sm">
                    2
                </div>
                <h3 class="text-base font-bold text-primary mb-2">Tasarımı Yükleyin veya Online Yapın</h3>
                <p class="text-xs text-on-surface-variant leading-relaxed">
                    Hazır PDF/AI dosyanızı tek tıkla yükleyin veya zengin sektör şablonlu online editörümüzle tarayıcınızda ücretsiz tasarlayın.
                </p>
            </div>

            <!-- Step 3 -->
            <div class="p-6 rounded-2xl bg-surface border border-outline-variant/70 shadow-xs relative">
                <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-extrabold text-lg mb-5 shadow-sm">
                    3
                </div>
                <h3 class="text-base font-bold text-primary mb-2">Kontrol Edelim & Kapınıza Gelsin</h3>
                <p class="text-xs text-on-surface-variant leading-relaxed">
                    Uzman grafikerlerimiz baskı provanızı ücretsiz denetler, onayınızla 24 saatte üretilip darbelere dayanıklı ambalajla sevk edilir.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ================= SAMPLE KIT CALLOUT (FREE SAMPLE PACK) ================= -->
<section class="py-16 md:py-20 bg-primary text-white relative overflow-hidden" id="sample-kit">
    <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-secondary/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
            <div class="lg:col-span-7">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-white text-[11px] font-bold mb-5">
                    <span class="material-symbols-outlined text-base text-amber-300">inventory_2</span>
                    <span>Ücretsiz Kurumsal Tanıtım Seti</span>
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight mb-4">
                    Dokunmadan Karar Vermeyin.
                </h2>
                <p class="text-sm sm:text-base text-slate-300 max-w-xl mb-6 leading-relaxed">
                    350gr Kuşe, Mat/Parlak Selefon, Bölgesel Kabartma Lak, Altın Varak ve Tuale fantezi kağıt numunelerini içeren <strong>Kurumsal Numune Paketini</strong> şirketinize ücretsiz talep edin.
                </p>
                <form class="flex flex-col sm:flex-row gap-3 max-w-lg" onsubmit="event.preventDefault(); alert('Ücretsiz numune talebiniz başarıyla alındı! Kargo hazırlanınca SMS ile bilgilendirileceksiniz.');">
                    <input class="px-4 py-3 bg-white text-slate-900 rounded-xl border-0 focus:outline-none focus:ring-2 focus:ring-secondary flex-1 text-sm font-medium" placeholder="Şirket e-posta veya telefon adresiniz..." required type="text"/>
                    <button class="px-6 py-3 bg-secondary hover:bg-blue-600 text-white rounded-xl font-bold text-sm transition-all active:scale-95 shadow-md flex items-center justify-center gap-2 shrink-0" type="submit">
                        <span>Numune Kiti İste</span>
                        <span class="material-symbols-outlined text-base">send</span>
                    </button>
                </form>
                <div class="mt-4 flex flex-wrap items-center gap-4 sm:gap-6 text-xs text-slate-300 font-medium">
                    <span>✓ Kurumsal firmalara 100% ücretsiz</span>
                    <span>✓ Ertesi gün kargo</span>
                    <span>✓ CMYK renk rehberi dahil</span>
                </div>
            </div>
            <div class="lg:col-span-5">
                <div class="relative rounded-2xl overflow-hidden border border-white/20 shadow-2xl bg-white/5 p-3 backdrop-blur-sm">
                    <img class="w-full h-72 sm:h-80 object-cover rounded-xl" alt="Kurumsal Numune Kiti Kutusu" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBhve8Q2UdDAqzPrSEYfDI1CbkOUUpgJ_GaQ3SGSpF-lVq-cOg3VO8p1Ku_m0NXWfL3Y3drWZx-yHGMp2H28_ObPJzjC0Ae-hibW3QdyvjqRHEnQt-irr_oho8tlvYWHhXnCPGjCE_oFB4ruY-ey4HWdxjltdVzGtK4XSg7yCxXZ0kd5BcOpXIkuKB-8I-gp5TZ70pL_qCMcv1LxOUJ93EXsAMC-OgETPGUYQ_QFib1zf8O6WnOD0hF"/>
                    <div class="absolute bottom-6 left-6 right-6 frosted-glass p-3.5 rounded-xl text-primary flex items-center justify-between shadow-lg">
                        <div>
                            <p class="text-xs font-bold text-slate-900">TamBaskı Numune Kartelası</p>
                            <p class="text-[10px] text-slate-600">14 Kağıt Stoğu & Özel Bitişler</p>
                        </div>
                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded-lg">
                            Stokta Var
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
