<?php
/**
 * TAMBASKI.COM.TR - Güvenli Ödeme & Sipariş Onayı
 * Kurumsal/Bireysel Fatura, PayTR & Havale Seçenekleri
 */
require_once __DIR__ . '/includes/functions.php';

$cart_items = $_SESSION['cart'] ?? [];

// Sepet boşsa ve önizleme yoksa
if (empty($cart_items)) {
    // Örnek sepet kalemi (Ziyaretçi doğrudan ödeme sayfasına bakıyorsa)
    $cart_items = [
        [
            'id' => 1,
            'name' => 'Kurumsal Prestij Kartvizit (350gr Mat Kuşe + Lak)',
            'package' => 'Standart Paket (350gr Kuşe, Çift Yön, Mat Selefon)',
            'quantity' => 1000,
            'price' => 650.00,
            'design_code' => '#TB-ONLINE-CANVA'
        ]
    ];
}

$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += floatval($item['price']);
}
$kdv = $subtotal * 0.20;
$shipping_cost = $subtotal >= 750 ? 0.00 : 79.90;
$grand_total = $subtotal + $kdv + $shipping_cost;

// Sipariş Gönderimi (POST)
$order_placed = false;
$order_id = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['cart'] = [];
    $order_placed = true;
    $order_id = 'TB-' . date('Ymd') . '-' . rand(1000, 9999);
}

$page_title = "Güvenli Ödeme & Sipariş | TamBaskı";
require_once __DIR__ . '/includes/header.php';
?>

<!-- Stepper Breadcrumb Track -->
<nav aria-label="Sipariş Adımları" class="border-b border-outline-variant bg-surface-container-low py-3.5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <ol class="flex items-center justify-center sm:justify-start gap-3 sm:gap-6 text-xs">
            <li class="flex items-center gap-2 text-emerald-700 font-semibold">
                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold">✓</span>
                <span>1. Sepetim</span>
            </li>
            <span class="text-outline-variant">/</span>
            <li class="flex items-center gap-2 text-emerald-700 font-semibold">
                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold">✓</span>
                <span>2. Fatura &amp; Teslimat</span>
            </li>
            <span class="text-outline-variant">/</span>
            <li class="flex items-center gap-2 text-brand font-bold">
                <span class="w-5 h-5 rounded-full bg-brand text-white flex items-center justify-center font-bold">3</span>
                <span>3. Ödeme &amp; Onay</span>
            </li>
        </ol>
    </div>
</nav>

<main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 py-8 sm:py-10">

<?php if ($order_placed): ?>
    <div class="max-w-2xl mx-auto bg-surface-container-lowest p-8 sm:p-12 rounded-2xl border border-outline-variant text-center shadow-xl">
        <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-4xl">check_circle</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-primary mb-2">Siparişiniz Başarıyla Alındı!</h1>
        <p class="text-sm text-on-surface-variant mb-4">
            Sipariş Numaranız: <strong class="text-brand font-mono font-bold text-base"><?= $order_id ?></strong>
        </p>
        <p class="text-xs sm:text-sm text-on-surface-variant max-w-md mx-auto mb-8 leading-relaxed">
            Baskı öncesi CTP ve renk profili kontrolleri başlatılmıştır. Hazırlanan prova ve kargo takip bilgileriniz e-posta ve SMS ile iletilecektir.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="order_tracking.php" class="px-6 py-3 bg-brand hover:bg-orange-600 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md transition-all">
                Siparişimi Takip Et
            </a>
            <a href="index.php" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-primary rounded-xl text-xs sm:text-sm font-bold transition-all">
                Anasayfaya Dön
            </a>
        </div>
    </div>
<?php else: ?>

    <form action="checkout.php" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
        
        <!-- LEFT COLUMN: Payment & Billing Form Engine (Col 1-7) -->
        <section class="lg:col-span-7 space-y-6">
            
            <!-- STEP 01: Fatura ve İletişim Tipi -->
            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant shadow-xs">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-outline-variant">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-xs font-bold">1</span>
                        <h2 class="text-sm font-bold text-primary">Fatura ve Vergi Bilgileri</h2>
                    </div>
                </div>

                <!-- Fatura Tipi Seçici -->
                <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-xl mb-4 text-xs font-bold">
                    <button type="button" class="py-2.5 px-4 text-center rounded-lg bg-white text-secondary shadow-xs transition-all" id="btnInvoiceCorp" onclick="switchInvoiceType('corp')">
                        Kurumsal Fatura (Şirket)
                    </button>
                    <button type="button" class="py-2.5 px-4 text-center rounded-lg text-slate-600 hover:text-primary transition-all" id="btnInvoiceIndiv" onclick="switchInvoiceType('indiv')">
                        Bireysel Fatura (Şahıs)
                    </button>
                </div>

                <!-- Kurumsal Alanlar -->
                <div id="corpInvoiceFields" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="sm:col-span-2">
                        <label class="block font-bold text-primary mb-1">Resmi Şirket Ünvanı *</label>
                        <input name="company_name" class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant bg-white text-primary font-medium focus:border-secondary outline-none transition-all" type="text" placeholder="Örn: ABC Mimarlık Tasarım San. ve Tic. Ltd. Şti." required/>
                    </div>
                    <div>
                        <label class="block font-bold text-primary mb-1">Vergi Dairesi *</label>
                        <input name="tax_office" class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant bg-white text-primary font-medium focus:border-secondary outline-none transition-all" type="text" placeholder="Örn: Beşiktaş Vergi Dairesi" required/>
                    </div>
                    <div>
                        <label class="block font-bold text-primary mb-1">Vergi Numarası (VKN) *</label>
                        <input name="tax_no" class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant bg-white text-primary font-mono focus:border-secondary outline-none transition-all" type="text" placeholder="10 Haneli VKN" required/>
                    </div>
                </div>

                <!-- Bireysel Alanlar (Gizli) -->
                <div id="indivInvoiceFields" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-bold text-primary mb-1">Ad Soyad *</label>
                        <input id="indivNameInput" class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant bg-white text-primary font-medium focus:border-secondary outline-none transition-all" type="text" placeholder="Adınız Soyadınız"/>
                    </div>
                    <div>
                        <label class="block font-bold text-primary mb-1">T.C. Kimlik Numarası (Opsiyonel)</label>
                        <input id="indivTcknInput" class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant bg-white text-primary font-mono focus:border-secondary outline-none transition-all" type="text" placeholder="11 Haneli TCKN"/>
                    </div>
                </div>
            </div>

            <!-- STEP 02: Teslimat Adresi & İletişim -->
            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant shadow-xs">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-outline-variant">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-xs font-bold">2</span>
                        <h2 class="text-sm font-bold text-primary">Teslimat Adresi ve İletişim</h2>
                    </div>
                    <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md">
                        Ücretsiz Kargo
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-bold text-primary mb-1">Teslim Alacak Kişi *</label>
                        <input name="shipping_name" class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant bg-white text-primary font-medium focus:border-secondary outline-none" type="text" placeholder="Ad Soyad" required/>
                    </div>
                    <div>
                        <label class="block font-bold text-primary mb-1">Cep Telefonu (SMS Takip için) *</label>
                        <input name="shipping_phone" class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant bg-white text-primary font-mono focus:border-secondary outline-none" type="tel" placeholder="05XX XXX XX XX" required/>
                    </div>
                    <div>
                        <label class="block font-bold text-primary mb-1">İl *</label>
                        <input name="shipping_city" class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant bg-white text-primary font-medium focus:border-secondary outline-none" type="text" placeholder="Örn: İstanbul" required/>
                    </div>
                    <div>
                        <label class="block font-bold text-primary mb-1">İlçe *</label>
                        <input name="shipping_district" class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant bg-white text-primary font-medium focus:border-secondary outline-none" type="text" placeholder="Örn: Beşiktaş" required/>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block font-bold text-primary mb-1">Açık Adres (Mahalle, Cadde, Sokak, No, Daire) *</label>
                        <textarea name="shipping_address" rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-outline-variant bg-white text-primary font-medium focus:border-secondary outline-none resize-none" placeholder="Kargonuzun teslim edileceği tam adres..." required></textarea>
                    </div>
                </div>
            </div>

            <!-- STEP 03: Ödeme Yöntemi -->
            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant shadow-xs">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-outline-variant">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-brand text-white flex items-center justify-center text-xs font-bold">3</span>
                        <h2 class="text-sm font-bold text-primary">Ödeme Yöntemi</h2>
                    </div>
                    <span class="text-[11px] font-bold text-slate-500 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm text-emerald-600">lock</span> 256-Bit 3D Secure
                    </span>
                </div>

                <div class="space-y-3">
                    <!-- Kredi Kartı Seçeneği -->
                    <label class="block p-4 rounded-xl border-2 border-secondary bg-blue-50/20 cursor-pointer">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <input type="radio" name="payment_method" value="credit_card" checked class="text-secondary focus:ring-0">
                                <span class="text-xs font-bold text-primary">Kredi veya Banka Kartı (PayTR Güvenli Ödeme)</span>
                            </div>
                            <span class="material-symbols-outlined text-secondary text-xl">credit_card</span>
                        </div>
                        <p class="text-[11px] text-on-surface-variant mt-1.5 pl-6">
                            Tüm banka ve kredi kartlarına tek çekim veya taksit imkanı. Kart bilgileriniz sunucumuzda tutulmaz.
                        </p>
                    </label>

                    <!-- Havale Seçeneği -->
                    <label class="block p-4 rounded-xl border border-outline-variant hover:border-slate-400 cursor-pointer">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <input type="radio" name="payment_method" value="bank_transfer" class="text-secondary focus:ring-0">
                                <span class="text-xs font-bold text-primary">Banka Havalesi / EFT (%5 Anında İndirim)</span>
                            </div>
                            <span class="material-symbols-outlined text-slate-500 text-xl">account_balance</span>
                        </div>
                        <p class="text-[11px] text-on-surface-variant mt-1.5 pl-6">
                            Garanti BBVA, Akbank, İş Bankası hesaplarımıza havale yapabilirsiniz.
                        </p>
                    </label>
                </div>
            </div>

        </section>

        <!-- RIGHT COLUMN: Order Summary (Cols 8-12) -->
        <aside class="lg:col-span-5 sticky top-20 flex flex-col gap-6">
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-6 shadow-sm">
                <h3 class="text-sm font-bold text-primary pb-3 border-b border-outline-variant">Sipariş Edilen Ürünler</h3>
                
                <div class="divide-y divide-outline-variant/60">
                    <?php foreach ($cart_items as $item): ?>
                    <div class="py-3 flex justify-between gap-3 text-xs">
                        <div>
                            <h4 class="font-bold text-primary"><?= htmlspecialchars($item['name']) ?></h4>
                            <p class="text-[11px] text-on-surface-variant mt-0.5"><?= htmlspecialchars($item['package']) ?></p>
                            <span class="text-[10px] text-slate-500 font-mono"><?= number_format($item['quantity'], 0, ',', '.') ?> Adet</span>
                        </div>
                        <span class="font-bold text-primary font-mono whitespace-nowrap">
                            <?= number_format($item['price'], 2, ',', '.') ?> ₺
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="space-y-2.5 py-4 border-t border-b border-outline-variant text-xs">
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant">Ara Toplam:</span>
                        <span class="font-bold text-primary font-mono"><?= number_format($subtotal, 2, ',', '.') ?> ₺</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant">KDV (%20):</span>
                        <span class="font-semibold text-primary font-mono"><?= number_format($kdv, 2, ',', '.') ?> ₺</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-on-surface-variant">Anlaşmalı Kargo:</span>
                        <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded text-[11px]">Ücretsiz</span>
                    </div>
                </div>

                <div class="flex justify-between items-baseline pt-4 mb-6">
                    <span class="text-sm font-bold text-primary">Genel Toplam:</span>
                    <div class="text-right">
                        <span class="text-2xl font-extrabold text-brand font-mono"><?= number_format($grand_total, 2, ',', '.') ?> ₺</span>
                        <span class="text-[10px] text-slate-500 block">KDV &amp; Kargo Dahil</span>
                    </div>
                </div>

                <button type="submit" class="w-full bg-brand hover:bg-orange-600 text-white font-bold py-3.5 px-4 rounded-xl text-sm shadow-md flex items-center justify-center gap-2 transition-all active:scale-95">
                    <span class="material-symbols-outlined text-lg">verified_user</span>
                    <span>Siparişi Onayla &amp; Öde</span>
                </button>
            </div>
        </aside>

    </form>

<?php endif; ?>

</main>

<script>
function switchInvoiceType(type) {
    const corpFields = document.getElementById('corpInvoiceFields');
    const indivFields = document.getElementById('indivInvoiceFields');
    const btnCorp = document.getElementById('btnInvoiceCorp');
    const btnIndiv = document.getElementById('btnInvoiceIndiv');

    if (type === 'corp') {
        corpFields.classList.remove('hidden');
        indivFields.classList.add('hidden');
        btnCorp.className = 'py-2.5 px-4 text-center rounded-lg bg-white text-secondary shadow-xs transition-all font-bold';
        btnIndiv.className = 'py-2.5 px-4 text-center rounded-lg text-slate-600 hover:text-primary transition-all font-medium';
    } else {
        corpFields.classList.add('hidden');
        indivFields.classList.remove('hidden');
        btnIndiv.className = 'py-2.5 px-4 text-center rounded-lg bg-white text-secondary shadow-xs transition-all font-bold';
        btnCorp.className = 'py-2.5 px-4 text-center rounded-lg text-slate-600 hover:text-primary transition-all font-medium';
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
