<?php
/**
 * TAMBASKI.COM.TR - Güvenli Ödeme (Precision Studio Print - 1:1 Tam Uyumlu)
 */
require_once __DIR__ . '/includes/functions.php';

$cart_items = $_SESSION['cart'] ?? [
    [
        'id' => 1,
        'name' => 'Ultra Prestij Kartvizit',
        'package' => 'Standart (350gr Mat Selefon)',
        'quantity' => 3000,
        'price' => 945.00,
        'design_code' => '#NEXUS-ARCH-2025'
    ]
];

$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += floatval($item['price']);
}
$kdv = $subtotal * 0.20;
$shipping_cost = $subtotal >= 750 ? 0.00 : 85.00;
$grand_total = $subtotal + $kdv + $shipping_cost;

// Process payment form submit
$order_placed = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Empty cart and show success
    $_SESSION['cart'] = [];
    $order_placed = true;
    $order_id = 'TB-' . date('Ymd') . '-' . rand(1000, 9999);
}

$page_title = "Güvenli Ödeme | TAM BASKI STUDIO";
require_once __DIR__ . '/includes/header.php';
?>

<!-- Stepper Breadcrumb Track -->
<nav aria-label="Sipariş Adımları" class="border-b border-surface-container-high bg-surface-container-lowest">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5">
        <ol class="flex items-center justify-center sm:justify-start gap-3 sm:gap-6 text-xs">
            <li class="flex items-center gap-2 text-emerald-700 font-semibold">
                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-label-numeric font-bold">
                    <span class="material-symbols-outlined text-xs">check</span>
                </span>
                <span>1. Sepet</span>
            </li>
            <span class="text-outline-variant">/</span>
            <li class="flex items-center gap-2 text-emerald-700 font-semibold">
                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-label-numeric font-bold">
                    <span class="material-symbols-outlined text-xs">check</span>
                </span>
                <span>2. Teslimat &amp; Fatura</span>
            </li>
            <span class="text-outline-variant">/</span>
            <li class="flex items-center gap-2 text-secondary font-bold">
                <span class="w-5 h-5 rounded-full bg-secondary text-white flex items-center justify-center font-label-numeric font-bold">
                    3
                </span>
                <span>3. Ödeme &amp; Onay</span>
            </li>
        </ol>
    </div>
</nav>

<!-- Main Canvas: 2-Column Split Studio Architecture -->
<main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 sm:py-10">

<?php if ($order_placed): ?>
    <div class="max-w-2xl mx-auto bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant text-center shadow-lg">
        <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-4xl">check_circle</span>
        </div>
        <h1 class="text-2xl font-bold text-primary mb-2">Siparişiniz Başarıyla Alındı!</h1>
        <p class="text-sm text-on-surface-variant mb-4">
            Sipariş Numaranız: <strong class="text-primary font-label-numeric"><?= $order_id ?></strong>
        </p>
        <p class="text-xs text-on-surface-variant max-w-md mx-auto mb-6">
            Baskı öncesi CTP ve renk profili kontrolleri başlatılmıştır. Hazırlanan prova ve kargo takip bilgileriniz e-posta ve SMS ile iletilecektir.
        </p>
        <div class="flex justify-center gap-4">
            <a href="order_tracking.php" class="px-5 py-2.5 bg-primary text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-all">
                Siparişimi Takip Et
            </a>
            <a href="index.php" class="px-5 py-2.5 bg-surface-container text-primary rounded-xl text-xs font-bold hover:bg-surface-container-high transition-all">
                Anasayfaya Dön
            </a>
        </div>
    </div>
<?php else: ?>

    <form action="checkout.php" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
        
        <!-- LEFT COLUMN: Payment & Billing Form Engine (Col 1-7) -->
        <section class="lg:col-span-7 space-y-8">
            
            <!-- STEP 01: Fatura ve İletişim Tipi -->
            <div class="bg-surface-container-lowest p-6 sm:p-7 rounded-2xl border border-surface-container-high shadow-xs">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <span class="font-label-numeric text-xs px-2 py-0.5 rounded bg-surface-container-high text-on-surface-variant font-bold">01</span>
                        <h2 class="text-base font-bold text-primary">Fatura Tipi ve Vergi Bilgileri</h2>
                    </div>
                    <span class="text-[10px] font-label-caps text-secondary font-bold">GEREKLİ</span>
                </div>
                <!-- Tab Selector -->
                <div class="grid grid-cols-2 gap-2 p-1 bg-surface-container-low rounded-xl mb-5 border border-surface-container-high text-xs">
                    <button class="py-2.5 px-4 text-center rounded-lg bg-surface-container-lowest text-primary shadow-xs font-bold transition-all" type="button">
                        Kurumsal Fatura (Şirket)
                    </button>
                    <button class="py-2.5 px-4 text-center rounded-lg text-on-surface-variant hover:text-primary font-medium transition-all" type="button">
                        Bireysel Fatura (Şahıs)
                    </button>
                </div>
                <!-- Corporate Input Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="sm:col-span-2">
                        <label class="block font-medium text-on-surface-variant mb-1.5">Resmi Şirket Ünvanı</label>
                        <input name="company_name" class="w-full px-3.5 py-2.5 rounded-xl border border-surface-container-high bg-surface-container-lowest text-primary font-medium focus:border-secondary focus:ring-1 focus:ring-secondary outline-none transition-all" type="text" value="Studio Mono Tasarım ve Reklamcılık Ltd. Şti." required/>
                    </div>
                    <div>
                        <label class="block font-medium text-on-surface-variant mb-1.5">Vergi Dairesi</label>
                        <input name="tax_office" class="w-full px-3.5 py-2.5 rounded-xl border border-surface-container-high bg-surface-container-lowest text-primary font-medium focus:border-secondary focus:ring-1 focus:ring-secondary outline-none transition-all" type="text" value="Beşiktaş Vergi Dairesi" required/>
                    </div>
                    <div>
                        <label class="block font-medium text-on-surface-variant mb-1.5">Vergi Numarası (VKN)</label>
                        <input name="tax_no" class="w-full px-3.5 py-2.5 rounded-xl border border-surface-container-high bg-surface-container-lowest text-primary font-label-numeric focus:border-secondary focus:ring-1 focus:ring-secondary outline-none transition-all" type="text" value="7840192834" required/>
                    </div>
                </div>
            </div>

            <!-- STEP 02: Teslimat Adresi & Lojistik Seçimi -->
            <div class="bg-surface-container-lowest p-6 sm:p-7 rounded-2xl border border-surface-container-high shadow-xs">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <span class="font-label-numeric text-xs px-2 py-0.5 rounded bg-surface-container-high text-on-surface-variant font-bold">02</span>
                        <h2 class="text-base font-bold text-primary">Teslimat Adresi ve Kargo Planı</h2>
                    </div>
                </div>
                <!-- Saved Address Card -->
                <div class="p-4 rounded-xl bg-surface-container-low border border-surface-container-high flex items-start justify-between gap-4 mb-5 text-xs">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-primary">Mert Yılmaz</span>
                            <span class="px-2 py-0.5 text-[9px] font-label-caps font-bold rounded bg-surface-container-high text-on-surface-variant">OFİS / AJANS</span>
                        </div>
                        <p class="text-on-surface-variant leading-relaxed">
                            Levent Loft Residence No:14 Kat:4 D:42 Büyükdere Cad. Beşiktaş / İstanbul
                        </p>
                        <p class="font-label-numeric text-on-surface-variant pt-1">+90 (532) 482 91 **</p>
                    </div>
                    <span class="material-symbols-outlined text-secondary text-xl">verified</span>
                </div>
            </div>

            <!-- STEP 03: Ödeme Yöntemi Seçimi -->
            <div class="bg-surface-container-lowest p-6 sm:p-7 rounded-2xl border border-surface-container-high shadow-xs">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <span class="font-label-numeric text-xs px-2 py-0.5 rounded bg-secondary text-white font-bold">03</span>
                        <h2 class="text-base font-bold text-primary">Ödeme Yöntemi</h2>
                    </div>
                    <div class="flex items-center gap-1 text-on-surface-variant font-label-numeric text-[11px]">
                        <span class="material-symbols-outlined text-secondary text-sm">verified_user</span>
                        <span>3D SECURE ZORUNLU</span>
                    </div>
                </div>
                <!-- Credit Card Form -->
                <div class="border-2 border-secondary rounded-2xl p-5 bg-surface-container-lowest space-y-4 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-primary flex items-center gap-2">
                            <input checked="" class="w-4 h-4 text-secondary focus:ring-0" name="payment_method" type="radio" value="credit_card"/>
                            Kredi veya Banka Kartı ile Ödeme (PayTR 256-Bit)
                        </span>
                        <div class="flex gap-1.5 text-slate-400">
                            <span class="material-symbols-outlined text-xl">credit_card</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                        <div class="sm:col-span-2">
                            <label class="block font-medium text-on-surface-variant mb-1">Kart Üzerindeki İsim</label>
                            <input class="w-full px-3 py-2 rounded-lg border border-surface-container-high bg-surface-container-low text-primary focus:border-secondary outline-none font-medium" type="text" placeholder="MERT YILMAZ" required/>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block font-medium text-on-surface-variant mb-1">Kart Numarası</label>
                            <input class="w-full px-3 py-2 rounded-lg border border-surface-container-high bg-surface-container-low text-primary font-label-numeric focus:border-secondary outline-none" type="text" placeholder="**** **** **** ****" required/>
                        </div>
                        <div>
                            <label class="block font-medium text-on-surface-variant mb-1">Son Kullanma (AA/YY)</label>
                            <input class="w-full px-3 py-2 rounded-lg border border-surface-container-high bg-surface-container-low text-primary font-label-numeric focus:border-secondary outline-none" type="text" placeholder="12/28" required/>
                        </div>
                        <div>
                            <label class="block font-medium text-on-surface-variant mb-1">CVV / Güvenlik Kodu</label>
                            <input class="w-full px-3 py-2 rounded-lg border border-surface-container-high bg-surface-container-low text-primary font-label-numeric focus:border-secondary outline-none" type="text" placeholder="***" required/>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        <!-- RIGHT COLUMN: Order Summary Sticky Dock -->
        <aside class="lg:col-span-5 sticky top-20 flex flex-col gap-6">
            <div class="bg-surface-container-lowest rounded-2xl border border-surface-container-high p-6 shadow-sm">
                <h2 class="text-base font-bold text-primary pb-4 border-b border-surface-container-high">Sipariş Özeti</h2>
                <div class="space-y-3 py-4 border-b border-surface-container-high text-xs">
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant">Kalemler (<?= count($cart_items) ?>):</span>
                        <span class="font-label-numeric font-bold text-primary"><?= number_format($subtotal, 2, ',', '.') ?> ₺</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant">KDV (%20):</span>
                        <span class="font-label-numeric font-semibold text-primary"><?= number_format($kdv, 2, ',', '.') ?> ₺</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-on-surface-variant">Kargo Bedeli:</span>
                        <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded text-[11px]">Ücretsiz</span>
                    </div>
                </div>
                <div class="flex justify-between items-baseline pt-4 mb-6">
                    <span class="text-sm font-bold text-primary">Ödenecek Tutar:</span>
                    <div class="text-right">
                        <span class="text-2xl font-extrabold font-label-numeric text-primary"><?= number_format($grand_total, 2, ',', '.') ?> ₺</span>
                    </div>
                </div>
                <button type="submit" class="w-full bg-primary hover:bg-slate-800 text-white font-bold py-3.5 px-4 rounded-xl text-sm tracking-wide shadow-md flex items-center justify-center gap-2 transition-all active:scale-95">
                    <span class="material-symbols-outlined text-lg">lock</span>
                    <span>Ödemeyi Tamamla (<?= number_format($grand_total, 2, ',', '.') ?> ₺)</span>
                </button>
            </div>
        </aside>

    </form>
<?php endif; ?>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
