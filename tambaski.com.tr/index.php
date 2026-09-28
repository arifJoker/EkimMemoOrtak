<?php
/**
 * TAMBASKI.COM.TR - Anasayfa (Precision Studio Print - 1:1 Tam Uyumlu)
 */
$page_title = "TAM BASKI | Endüstriyel Baskı & Kurumsal Matbaa Çözümleri";
$page_desc = "Heidelberg ofset kalitesi, 420+ kurumsal şablon ve anında online vektör prova imkanıyla prestijli kurumsal baskı çözümleri.";
require_once __DIR__ . '/includes/header.php';

$featured_products = get_all_products(null, true);
?>

<!-- ================= HERO SECTION ================= -->
<section class="relative pt-12 pb-20 overflow-hidden subtle-grid border-b border-outline-variant/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <!-- Top Micro-Badge -->
        <div class="flex justify-center mb-6">
            <div class="inline-flex items-center gap-2.5 px-3.5 py-1 rounded-full bg-surface-container border border-outline-variant/60 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                <span class="font-label-numeric text-[11px] text-on-surface tracking-wider uppercase font-semibold">Heidelberg XL 106 10-Color Press Live</span>
                <span class="text-outline-variant">|</span>
                <span class="text-xs font-semibold text-secondary">v4.2 Vector Studio Aktif</span>
            </div>
        </div>
        
        <!-- Headline & Subtitle -->
        <div class="text-center max-w-4xl mx-auto mb-10">
            <h1 class="text-3xl sm:text-5xl md:text-6xl font-extrabold text-primary tracking-tight mb-5 leading-tight">
                Endüstriyel Baskı Hassasiyeti, Doğrudan Tarayıcınızda.
            </h1>
            <p class="text-base sm:text-lg text-on-surface-variant max-w-2xl mx-auto">
                Heidelberg Speedmaster ofset kalitesi, 420+ hazır kurumsal şablon ve anında online vektör prova imkanıyla prestijli kurumsal baskı çözümleri.
            </p>
            
            <!-- CTA Buttons -->
            <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                <a class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-primary text-on-primary font-semibold text-sm shadow-md hover:bg-slate-800 transition-all active:scale-95" href="#products">
                    <span>Ürünleri Keşfet</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
                <a class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-surface-container-lowest text-on-surface font-semibold text-sm border border-outline-variant shadow-xs hover:border-secondary hover:text-secondary transition-all active:scale-95" href="product.php?slug=ekonomik-kartvizit-250gr">
                    <span class="material-symbols-outlined text-lg text-secondary">design_services</span>
                    <span>Online Tasarım Editörünü Dene</span>
                </a>
            </div>
            
            <!-- Live Badges -->
            <div class="mt-10 flex flex-wrap items-center justify-center gap-4 sm:gap-6 text-on-surface-variant">
                <div class="flex items-center gap-2 text-xs font-medium bg-surface-container-lowest px-3 py-1.5 rounded-lg border border-outline-variant/60 shadow-xs">
                    <span class="material-symbols-outlined text-secondary text-base">verified</span>
                    <span>300 DPI Ultra HD Ofset</span>
                </div>
                <div class="flex items-center gap-2 text-xs font-medium bg-surface-container-lowest px-3 py-1.5 rounded-lg border border-outline-variant/60 shadow-xs">
                    <span class="material-symbols-outlined text-secondary text-base">timer</span>
                    <span>24 Saatte Üretim SLA</span>
                </div>
                <div class="flex items-center gap-2 text-xs font-medium bg-surface-container-lowest px-3 py-1.5 rounded-lg border border-outline-variant/60 shadow-xs">
                    <span class="material-symbols-outlined text-secondary text-base">palette</span>
                    <span>%100 Renk Doğruluk (FOGRA 39/51)</span>
                </div>
            </div>
        </div>

        <!-- 3D Stationery Stage Container -->
        <div class="relative max-w-5xl mx-auto rounded-2xl bg-gradient-to-b from-surface-container-lowest to-surface-container-low p-2 md:p-3 border border-outline-variant shadow-xl">
            <div class="relative w-full rounded-xl overflow-hidden bg-surface-container-high aspect-[16/9] md:aspect-[21/9] flex items-center justify-center">
                <img class="w-full h-full object-cover" alt="Tam Baskı Kurumsal Kırtasiye ve Kartvizit Baskı Parkuru" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDz0TcydwvPaWv8eC5GJA0oh71XhZIcs0i4EBcwj15AZdXHUKeUhp2PgbxggR1pKRGLnw1gK3OSYfQ7Ne2UdoNjPjK2CC4N7l4fPxp0jEuN5OLA6pTnKQpwrZiHBGyhqCLGFFjPPWtkk4maQ-thaeyBKJaoKf4NI9uIK1of9QH2P8yxrKuT3vp6IoaeeaYBXv0WF6bOoJKoBcDZaFUVVA7uDh5xI2YtJ_yDznFwW7uCdj-kI0mVZ_IP"/>
                <!-- Floating Interactive Pill HUD -->
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 frosted-glass px-4 py-2 rounded-full shadow-lg flex items-center gap-4 text-xs font-medium text-primary">
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base text-secondary">360</span>
                        <span class="font-semibold">3D Canlı Doku Önizleme</span>
                    </div>
                    <span class="text-outline-variant">|</span>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                        <span class="font-label-numeric text-[11px] text-on-surface-variant">Cotton Tuale 380 GSM</span>
                    </div>
                    <a href="product.php?slug=ekonomik-kartvizit-250gr" class="ml-2 px-2.5 py-1 rounded-full bg-primary text-on-primary text-[10px] font-bold hover:bg-slate-800 transition-colors">
                        Stüdyoda İncele
                    </a>
                </div>
                <!-- Top-Right Flight Status Tag -->
                <div class="absolute top-4 right-4 bg-surface-container-lowest/90 backdrop-blur-md px-3 py-1.5 rounded-lg border border-outline-variant/60 shadow-sm flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="font-label-numeric text-[11px] font-medium text-primary">Pre-flight: Ready for CTP Plate</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================= POPULAR PRINT CATEGORIES ================= -->
<section class="py-20 bg-surface-container-lowest border-b border-outline-variant/40" id="products">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <!-- Section Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
            <div>
                <div class="flex items-center gap-2 text-secondary font-label-numeric text-xs tracking-wider uppercase font-semibold mb-2">
                    <span class="material-symbols-outlined text-base">layers</span>
                    <span>Standart &amp; Özel Üretim Portföyü</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold text-primary tracking-tight">
                    Popüler Baskı Kategorileri
                </h2>
                <p class="text-sm text-on-surface-variant mt-1">
                    Kurumsal kimliğinizi zirveye taşıyan sertifikalı matbaa ürünleri.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-on-surface-variant font-label-numeric font-medium">12 Ana Kategori / 48 Varyant</span>
                <a class="inline-flex items-center gap-1 text-xs font-semibold text-secondary hover:underline" href="category.php?slug=kartvizit">
                    Tüm Ürünler
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
        </div>

        <!-- Categories 4-Column Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Card 1: Özel Kartvizitler -->
            <div class="group rounded-2xl border border-outline-variant/80 bg-surface-container-lowest overflow-hidden hover:shadow-xl hover:border-secondary transition-all duration-200 flex flex-col">
                <div class="relative aspect-[4/3] bg-surface-container-low overflow-hidden">
                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Özel Kartvizitler" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCLEpiRfEU7EJiQRDuxS10Jg2sEb0R7SucCnK-B7JvT2hl8zjtCaos34Rlol0SfIfRbZ1CrK7gylZh935mH_yC6UK06xRG78FViLw2-BQVFveQK-LmBxi9y4-M2mFuYzhLP_GYJA9msUaUUJ9rz20lvhkNHLbBfWRXVlmPwOs2_m1ADyGeSbnl8TL17zn3H4zb3s-MAnlAZx3oYpLD-owfJkz_Y_WnRxcPPsvjpjc6MeATBAxfy7Gpj"/>
                    <div class="absolute top-3 left-3 bg-surface-container-lowest/90 backdrop-blur-md px-2.5 py-1 rounded text-primary font-label-numeric text-[11px] font-semibold">
                        Ultra Prestij
                    </div>
                    <div class="absolute bottom-3 right-3 bg-primary text-on-primary px-2 py-0.5 rounded text-[10px] font-label-numeric">
                        Lak &amp; Varak Uyumlu
                    </div>
                </div>
                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-primary mb-1 group-hover:text-secondary transition-colors">
                            Özel Kartvizitler
                        </h3>
                        <p class="text-xs text-on-surface-variant mb-4 leading-relaxed">
                            Kabartma lak, altın/gümüş varak, PVC ve dokulu tuale kağıt seçenekleriyle kusursuz ilk izlenim.
                        </p>
                        <div class="space-y-1.5 pb-4 border-b border-outline-variant/40 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-on-surface-variant">Stok Gramajı:</span>
                                <span class="font-label-numeric font-semibold text-primary">350 - 600 GSM</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-on-surface-variant">Min. Sipariş:</span>
                                <span class="font-label-numeric font-semibold text-primary">100 Adet</span>
                            </div>
                        </div>
                    </div>
                    <div class="pt-4 flex items-center justify-between mt-auto">
                        <div>
                            <span class="text-[10px] text-on-surface-variant font-label-numeric uppercase block">Başlangıç</span>
                            <span class="text-lg font-bold font-label-numeric text-primary">450 ₺</span>
                        </div>
                        <a class="px-3.5 py-2 bg-surface-container hover:bg-secondary hover:text-white rounded-xl text-xs font-semibold transition-colors" href="product.php?slug=ekonomik-kartvizit-250gr">
                            Konfigüre Et
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card 2: Kurumsal Kimlik -->
            <div class="group rounded-2xl border border-outline-variant/80 bg-surface-container-lowest overflow-hidden hover:shadow-xl hover:border-secondary transition-all duration-200 flex flex-col">
                <div class="relative aspect-[4/3] bg-surface-container-low overflow-hidden">
                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Kurumsal Kimlik" src="https://lh3.googleusercontent.com/aida-public/AB6AXuATVhSAYambtmmhjHuz-047VBk1PuNa5TOCs11s0hUwpnFt-cNRcn65o4cyrqWDOwJ5L3cMk4240bmsttYsu8I5iJyE6qMaoxaxcEaj4aX1I_n4v_QEblLW3Lr-VCLYgXkduhsFKnzENQhPU6Ugi-LZ4FvGmthlPk-ylteZ2AQzqzMXPCPlNqIXrU3YUqETSfABMYR3vhqqEl2RtjbRe10oAYwp-Kj3AtGmfXUtrN4D4NAy2bMzLk5q"/>
                    <div class="absolute top-3 left-3 bg-surface-container-lowest/90 backdrop-blur-md px-2.5 py-1 rounded text-primary font-label-numeric text-[11px] font-semibold">
                        Ofset Serisi
                    </div>
                    <div class="absolute bottom-3 right-3 bg-surface-container-lowest/90 text-primary px-2 py-0.5 rounded text-[10px] font-label-numeric border border-outline-variant/40">
                        Pantone Calibrated
                    </div>
                </div>
                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-primary mb-1 group-hover:text-secondary transition-colors">
                            Kurumsal Kimlik
                        </h3>
                        <p class="text-xs text-on-surface-variant mb-4 leading-relaxed">
                            Antetli kağıt, pencereli/penceresiz cepli dosya, diplomat zarf ve bloknot takımları.
                        </p>
                        <div class="space-y-1.5 pb-4 border-b border-outline-variant/40 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-on-surface-variant">Kağıt Tipi:</span>
                                <span class="font-label-numeric font-semibold text-primary">110g 1. Hamur / Kuşe</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-on-surface-variant">Standart:</span>
                                <span class="font-label-numeric font-semibold text-primary">DIN A4 / A5</span>
                            </div>
                        </div>
                    </div>
                    <div class="pt-4 flex items-center justify-between mt-auto">
                        <div>
                            <span class="text-[10px] text-on-surface-variant font-label-numeric uppercase block">Başlangıç</span>
                            <span class="text-lg font-bold font-label-numeric text-primary">1.250 ₺</span>
                        </div>
                        <a class="px-3.5 py-2 bg-surface-container hover:bg-secondary hover:text-white rounded-xl text-xs font-semibold transition-colors" href="category.php?slug=kurumsal-urunler">
                            Konfigüre Et
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card 3: Tanıtım & Reklam -->
            <div class="group rounded-2xl border border-outline-variant/80 bg-surface-container-lowest overflow-hidden hover:shadow-xl hover:border-secondary transition-all duration-200 flex flex-col">
                <div class="relative aspect-[4/3] bg-surface-container-low overflow-hidden">
                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Tanıtım & Reklam" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC6ZD3XE0DcDiaujkXPlFPBZtT74Fbaqcx7koOtoFznCZ99tKHrCY7usEYfKLrNPPZBf06MnZfjLHJTJyEfry2cIFHVVD_ktNE0bG2bSJSskD-rcsVio2pdsmBA7TxASVJuh_Y3-3auNV0GLxHqChsNvX8-TlD6hKs8eIfEJV44J7W1Ev-YFPOTRCH2aUoKXG2ALZcgQdHhbeZB3Lc2Ce_UWICdz38QNawlDtIEs5qBx3_v8_3xFRWQ"/>
                    <div class="absolute top-3 left-3 bg-surface-container-lowest/90 backdrop-blur-md px-2.5 py-1 rounded text-primary font-label-numeric text-[11px] font-semibold">
                        Çok Sayfalı
                    </div>
                    <div class="absolute bottom-3 right-3 bg-surface-container-lowest/90 text-primary px-2 py-0.5 rounded text-[10px] font-label-numeric border border-outline-variant/40">
                        Tel Dikiş / Pur Cilt
                    </div>
                </div>
                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-primary mb-1 group-hover:text-secondary transition-colors">
                            Broşür &amp; El İlanı
                        </h3>
                        <p class="text-xs text-on-surface-variant mb-4 leading-relaxed">
                            Kırım broşür, lüks ürün katalogları, el ilanları ve restoran menüleri.
                        </p>
                        <div class="space-y-1.5 pb-4 border-b border-outline-variant/40 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-on-surface-variant">Selefon:</span>
                                <span class="font-label-numeric font-semibold text-primary">Mat / Parlak / Soft Touch</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-on-surface-variant">Baskı Türü:</span>
                                <span class="font-label-numeric font-semibold text-primary">8 Renk UV Ofset</span>
                            </div>
                        </div>
                    </div>
                    <div class="pt-4 flex items-center justify-between mt-auto">
                        <div>
                            <span class="text-[10px] text-on-surface-variant font-label-numeric uppercase block">Başlangıç</span>
                            <span class="text-lg font-bold font-label-numeric text-primary">890 ₺</span>
                        </div>
                        <a class="px-3.5 py-2 bg-surface-container hover:bg-secondary hover:text-white rounded-xl text-xs font-semibold transition-colors" href="category.php?slug=el-ilani-brosur">
                            Konfigüre Et
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card 4: Tabela & İç Mekan -->
            <div class="group rounded-2xl border border-outline-variant/80 bg-surface-container-lowest overflow-hidden hover:shadow-xl hover:border-secondary transition-all duration-200 flex flex-col">
                <div class="relative aspect-[4/3] bg-surface-container-low overflow-hidden">
                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Tabela & İç Mekan" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDsc4wYzNFA7DwYwfOwJCJOnyGa9HvueoWmGuRWdvLJnLzGrrp--cRAex3VNasYu13AfoIYq9wvyesEtMIqkgwYBKEM1VVkc722pGtAYODJWyOYxkDYAqWJnUJYt2VGGGR9oxk983q4eCRo0bxiMzYj5FzNlq4sDsUK9EsSWycbSwzo8dvZo2CewCpDua9eojeQV6qqTaxq_CB_rE5zd-xq6RCrImMGBziObbJVpGHgBXh_rrcv_79k"/>
                    <div class="absolute top-3 left-3 bg-surface-container-lowest/90 backdrop-blur-md px-2.5 py-1 rounded text-primary font-label-numeric text-[11px] font-semibold">
                        Geniş Format
                    </div>
                    <div class="absolute bottom-3 right-3 bg-surface-container-lowest/90 text-primary px-2 py-0.5 rounded text-[10px] font-label-numeric border border-outline-variant/40">
                        Direkt UV Baskı
                    </div>
                </div>
                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-primary mb-1 group-hover:text-secondary transition-colors">
                            Dekota &amp; Pleksi Kesim
                        </h3>
                        <p class="text-xs text-on-surface-variant mb-4 leading-relaxed">
                            Dekota foreks, şeffaf pleksi, vinil germe branda ve kurumsal roll-up banner sistemleri.
                        </p>
                        <div class="space-y-1.5 pb-4 border-b border-outline-variant/40 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-on-surface-variant">Dayanıklılık:</span>
                                <span class="font-label-numeric font-semibold text-primary">5 Yıl Solmazlık</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-on-surface-variant">Çözünürlük:</span>
                                <span class="font-label-numeric font-semibold text-primary">1440 DPI Piezo</span>
                            </div>
                        </div>
                    </div>
                    <div class="pt-4 flex items-center justify-between mt-auto">
                        <div>
                            <span class="text-[10px] text-on-surface-variant font-label-numeric uppercase block">Başlangıç</span>
                            <span class="text-lg font-bold font-label-numeric text-primary">640 ₺</span>
                        </div>
                        <a class="px-3.5 py-2 bg-surface-container hover:bg-secondary hover:text-white rounded-xl text-xs font-semibold transition-colors" href="category.php?slug=dekota-pleksi-kesim">
                            Konfigüre Et
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ================= WHY PRESS & STUDIO (VALUE PROPOSITIONS) ================= -->
<section class="py-20 bg-surface border-b border-outline-variant/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="font-label-numeric text-xs uppercase tracking-wider text-secondary font-bold">Endüstri Standartları</span>
            <h2 class="text-2xl sm:text-3xl font-bold text-primary tracking-tight mt-1 mb-3">
                Neden Tam Baskı Studio?
            </h2>
            <p class="text-sm text-on-surface-variant">
                Geleneksel matbaacılığın hata payını ortadan kaldıran yapay zeka denetimli dijital üretim altyapısı.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Prop 1 -->
            <div class="p-6 rounded-2xl bg-surface-container-lowest border border-outline-variant/60 shadow-xs hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-xl bg-surface-container-low flex items-center justify-center text-secondary mb-5 border border-outline-variant/40">
                    <span class="material-symbols-outlined text-[28px]">auto_fix_high</span>
                </div>
                <div class="font-label-numeric text-[11px] text-secondary font-bold mb-1">01 / PRE-FLIGHT</div>
                <h3 class="text-base font-bold text-primary mb-2">Otomatik Vektör Denetimi</h3>
                <p class="text-xs text-on-surface-variant leading-relaxed">
                    Yüklediğiniz PDF ve AI dosyaları 3mm taşma payı, RGB/CMYK dönüşümü ve 300 DPI çözünürlük açısından milisaniyeler içinde taranır.
                </p>
            </div>
            <!-- Prop 2 -->
            <div class="p-6 rounded-2xl bg-surface-container-lowest border border-outline-variant/60 shadow-xs hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-xl bg-surface-container-low flex items-center justify-center text-secondary mb-5 border border-outline-variant/40">
                    <span class="material-symbols-outlined text-[28px]">precision_manufacturing</span>
                </div>
                <div class="font-label-numeric text-[11px] text-secondary font-bold mb-1">02 / OFFSET MASTERY</div>
                <h3 class="text-base font-bold text-primary mb-2">Mikron Düzeyinde Baskı</h3>
                <p class="text-xs text-on-surface-variant leading-relaxed">
                    Heidelberg Speedmaster XL 106 parkurumuz, lazerle pozlandırılmış CTP kalıplarıyla ±0.02 mm kros hassasiyeti sunar.
                </p>
            </div>
            <!-- Prop 3 -->
            <div class="p-6 rounded-2xl bg-surface-container-lowest border border-outline-variant/60 shadow-xs hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-xl bg-surface-container-low flex items-center justify-center text-secondary mb-5 border border-outline-variant/40">
                    <span class="material-symbols-outlined text-[28px]">view_in_ar</span>
                </div>
                <div class="font-label-numeric text-[11px] text-secondary font-bold mb-1">03 / AR &amp; 3D PROVA</div>
                <h3 class="text-base font-bold text-primary mb-2">Gerçek Zamanlı 3D Simülasyon</h3>
                <p class="text-xs text-on-surface-variant leading-relaxed">
                    Baskıya onay vermeden önce kabartma lakın ışık kırılmasını, kağıt dokusunu ve kırım çizgilerini 3D ortamda döndürerek inceleyin.
                </p>
            </div>
            <!-- Prop 4 -->
            <div class="p-6 rounded-2xl bg-surface-container-lowest border border-outline-variant/60 shadow-xs hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-xl bg-surface-container-low flex items-center justify-center text-secondary mb-5 border border-outline-variant/40">
                    <span class="material-symbols-outlined text-[28px]">local_shipping</span>
                </div>
                <div class="font-label-numeric text-[11px] text-secondary font-bold mb-1">04 / EXPRESS LOGISTICS</div>
                <h3 class="text-base font-bold text-primary mb-2">VIP Kurumsal Sevkiyat</h3>
                <p class="text-xs text-on-surface-variant leading-relaxed">
                    750 ₺ üzeri siparişlerde neme ve darbelere dayanıklı kraft korumalı ambalajlarla Türkiye genelinde ücretsiz hızlı teslimat.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ================= SAMPLE KIT CALLOUT (FREE SAMPLE PACK) ================= -->
<section class="py-20 bg-primary-container text-on-primary relative overflow-hidden" id="sample-kit">
    <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-secondary/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-7">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-surface-container-lowest/10 border border-outline-variant/30 text-secondary-fixed text-[11px] font-label-numeric mb-6">
                    <span class="material-symbols-outlined text-base">inventory_2</span>
                    <span>Ücretsiz Kurumsal Tanıtım Seti</span>
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-white mb-4">
                    Dokunmadan Karar Vermeyin.
                </h2>
                <p class="text-sm sm:text-base text-slate-300 max-w-xl mb-8 leading-relaxed">
                    18 farklı seçkin kağıt stoğu, Soft Touch ve Kumlu Mat selefonlar, kabartma lak, gofre ve folyo yaldız örneklerini içeren <strong>Kurumsal Numune Kitini</strong> şirketinize ücretsiz talep edin.
                </p>
                <form class="flex flex-col sm:flex-row gap-3 max-w-lg" onsubmit="event.preventDefault(); alert('Numune talebiniz başarıyla alındı! Kargo takip kodunuz SMS ile iletilecektir.');">
                    <input class="px-4 py-3 bg-surface-container-lowest text-primary rounded-xl border border-outline-variant focus:outline-none focus:ring-2 focus:ring-secondary flex-1 text-sm font-medium" placeholder="Şirket e-posta veya telefon adresiniz..." required="" type="text"/>
                    <button class="px-6 py-3 bg-secondary hover:bg-blue-600 text-white rounded-xl font-semibold text-sm transition-all active:scale-95 shadow-md flex items-center justify-center gap-2" type="submit">
                        <span>Numune Kiti İste</span>
                        <span class="material-symbols-outlined text-base">send</span>
                    </button>
                </form>
                <div class="mt-4 flex flex-wrap items-center gap-6 text-[11px] font-label-numeric text-slate-400">
                    <span>✓ Kurumsal firmalara 100% ücretsiz</span>
                    <span>✓ Ertesi gün kurye teslimatı</span>
                    <span>✓ CMYK &amp; Pantone kılavuzu dahil</span>
                </div>
            </div>
            <div class="lg:col-span-5">
                <div class="relative rounded-2xl overflow-hidden border border-outline-variant/30 shadow-2xl bg-surface-container-lowest/5 p-3 backdrop-blur-sm">
                    <img class="w-full h-80 object-cover rounded-xl" alt="Numune Kiti Kutusu" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBhve8Q2UdDAqzPrSEYfDI1CbkOUUpgJ_GaQ3SGSpF-lVq-cOg3VO8p1Ku_m0NXWfL3Y3drWZx-yHGMp2H28_ObPJzjC0Ae-hibW3QdyvjqRHEnQt-irr_oho8tlvYWHhXnCPGjCE_oFB4ruY-ey4HWdxjltdVzGtK4XSg7yCxXZ0kd5BcOpXIkuKB-8I-gp5TZ70pL_qCMcv1LxOUJ93EXsAMC-OgETPGUYQ_QFib1zf8O6WnOD0hF"/>
                    <div class="absolute bottom-6 left-6 right-6 frosted-glass p-3.5 rounded-xl text-primary flex items-center justify-between shadow-lg">
                        <div>
                            <p class="text-xs font-bold text-slate-900">2025 Master Swatch Book</p>
                            <p class="text-[10px] font-label-numeric text-slate-600">18 Stok / 6 Bitiş Efekti</p>
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
