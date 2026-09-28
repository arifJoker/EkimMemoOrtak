<?php
/**
 * TAMBASKI.COM.TR - Alışveriş Sepeti (Precision Studio Print - 1:1 Tam Uyumlu)
 */
require_once __DIR__ . '/includes/functions.php';

// Handle Add to Cart action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $product_id = intval($_POST['product_id'] ?? 1);
    $product_name = $_POST['product_name'] ?? 'Ultra Prestij Kartvizit';
    $package = $_POST['package'] ?? 'standart';
    $quantity = intval($_POST['quantity'] ?? 1000);
    $price = floatval($_POST['price'] ?? 450);

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $_SESSION['cart'][] = [
        'id' => $product_id,
        'name' => $product_name,
        'package' => $package,
        'quantity' => $quantity,
        'price' => $price,
        'design_code' => '#TB-' . rand(1000, 9999)
    ];

    set_flash_message('success', 'Ürün başarıyla sepetinize eklendi!');
    header("Location: cart.php");
    exit;
}

// Handle Remove from Cart
if (isset($_GET['remove'])) {
    $index = intval($_GET['remove']);
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // re-index
        set_flash_message('info', 'Ürün sepetten kaldırıldı.');
    }
    header("Location: cart.php");
    exit;
}

// Default items if empty for presentation
$cart_items = $_SESSION['cart'] ?? [
    [
        'id' => 1,
        'name' => 'Kurumsal Prestij Kartvizit (350gr Mat Kuşe + Lak)',
        'package' => 'Standart Paket (350gr Kuşe, Çift Yön, Mat Selefon)',
        'quantity' => 1000,
        'price' => 650.00,
        'design_code' => '#TB-ONLINE-CANVA'
    ]
];

$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += floatval($item['price']);
}
$kdv = $subtotal * 0.20;
$shipping_cost = $subtotal >= 750 ? 0.00 : 79.90;
$grand_total = $subtotal + $kdv + $shipping_cost;

$page_title = "Alışveriş Sepetim | TamBaskı";
require_once __DIR__ . '/includes/header.php';
?>

<!-- Step Indicator Bar -->
<section class="bg-surface-container-lowest border-b border-outline-variant py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <!-- Step Indicator -->
            <nav aria-label="Aşama Takibi" class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-secondary text-white text-[11px] font-label-numeric font-bold flex items-center justify-center shadow-xs">1</span>
                    <span class="text-xs sm:text-sm font-bold text-primary">Sepetim</span>
                </div>
                <span class="material-symbols-outlined text-outline-variant text-base">chevron_right</span>
                <div class="flex items-center gap-2 text-outline">
                    <span class="w-6 h-6 rounded-full border border-outline-variant text-[11px] font-label-numeric flex items-center justify-center">2</span>
                    <span class="text-xs sm:text-sm font-medium">Teslimat &amp; Fatura</span>
                </div>
                <span class="material-symbols-outlined text-outline-variant text-base">chevron_right</span>
                <div class="flex items-center gap-2 text-outline">
                    <span class="w-6 h-6 rounded-full border border-outline-variant text-[11px] font-label-numeric flex items-center justify-center">3</span>
                    <span class="text-xs sm:text-sm font-medium">Ödeme &amp; Onay</span>
                </div>
            </nav>
            <div class="flex items-center gap-2 text-on-surface-variant text-xs font-semibold">
                <span class="material-symbols-outlined text-emerald-600 text-base">verified</span>
                <span>ISO 12647-2 Renk Kalibrasyon Onaylı</span>
            </div>
        </div>
    </div>
</section>

<!-- Main Content Layout -->
<main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 py-8">
    
    <!-- Free Shipping Progress Banner -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-4 mb-8 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-start gap-3">
            <div class="p-2.5 rounded-xl bg-surface-container text-secondary flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">local_shipping</span>
            </div>
            <div>
                <h2 class="text-xs sm:text-sm font-bold text-primary flex items-center gap-2">
                    750 ₺ üzeri Ücretsiz Kargo Kazandınız!
                    <span class="bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full text-[10px] font-bold">Tebrikler</span>
                </h2>
                <p class="text-on-surface-variant text-xs mt-0.5">
                    Mevcut sepet tutarınız kargo muafiyet limitini aştı. Ekspres anlaşmalı kargo gönderimi tamamen ücretsiz.
                </p>
            </div>
        </div>
        <div class="min-w-[240px] flex flex-col gap-1.5">
            <div class="flex justify-between items-center text-[11px] font-label-numeric">
                <span class="text-on-surface-variant"><?= number_format($subtotal, 2, ',', '.') ?> ₺ / 750,00 ₺</span>
                <span class="text-emerald-700 font-bold">%100 Tamamlandı</span>
            </div>
            <div class="w-full h-2 bg-surface-container-high rounded-full overflow-hidden">
                <div class="h-full bg-secondary rounded-full transition-all duration-500" style="width: 100%;"></div>
            </div>
        </div>
    </div>

    <!-- 2-Column E-Commerce Cart Interface -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Column: Cart Items -->
        <div class="lg:col-span-8 flex flex-col gap-6">
            <div class="flex items-center justify-between pb-2 border-b border-outline-variant">
                <h1 class="text-lg sm:text-xl font-bold text-primary tracking-tight">Sipariş Edilecek Kalemler (<?= count($cart_items) ?>)</h1>
                <span class="text-xs text-on-surface-variant">Üretim Kodu: <span class="font-label-numeric">#PS-2025-089</span></span>
            </div>

            <?php foreach ($cart_items as $index => $item): ?>
            <!-- Product Card -->
            <article class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-6 shadow-xs hover:shadow-md transition-shadow relative overflow-hidden">
                <div class="flex flex-col md:flex-row gap-5">
                    <div class="w-full md:w-36 h-36 rounded-xl bg-surface-container-low border border-outline-variant overflow-hidden flex-shrink-0 relative group">
                        <img class="w-full h-full object-cover" alt="<?= htmlspecialchars($item['name']) ?>" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA7DShFk8ulfn-FWi_RJ-1eQX35lnOfBb-51anXTN_o721cTad8QD871Qkhm8bJdhGyvDFZmA2Wa83pvGSGLN2VYfOyneAj3nzOBFCKTjhtEmYz0g2imlZWYIMdQXmJQ0Ta5plQCM-TkVun7ShKfqQ2DJ4cbm4YJe22FdUfxGA2LbYycv0fsOV6hZ5-mxt51xZ85aueEAEIrX5Q7lZOxpC4AGnbgx5uUC2Og6jSwmWWCWR2DM8-mDcN"/>
                        <span class="absolute top-2 left-2 bg-primary/80 backdrop-blur-sm text-on-primary text-[10px] font-label-numeric px-1.5 py-0.5 rounded">350 GSM</span>
                    </div>
                    <div class="flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <span class="text-[10px] font-label-caps text-secondary font-bold uppercase tracking-wider">Premium Kurumsal Baskı</span>
                                    <h3 class="text-base font-bold text-primary mt-0.5"><?= htmlspecialchars($item['name']) ?></h3>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-bold font-label-numeric text-primary"><?= number_format($item['price'], 2, ',', '.') ?> ₺</span>
                                    <span class="text-[10px] font-label-numeric text-on-surface-variant block">+KDV</span>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-1.5 mt-3">
                                <span class="px-2 py-0.5 bg-surface-container text-on-surface text-[10px] font-label-numeric rounded border border-outline-variant">350gr Kuşe</span>
                                <span class="px-2 py-0.5 bg-surface-container text-on-surface text-[10px] font-label-numeric rounded border border-outline-variant">Çift Yön</span>
                                <span class="px-2 py-0.5 bg-surface-container text-on-surface text-[10px] font-label-numeric rounded border border-outline-variant">Mat Selefon</span>
                                <span class="px-2 py-0.5 bg-secondary-fixed text-on-secondary-fixed text-[10px] font-label-numeric rounded border border-secondary/20 font-bold">Bölgesel Kabartma Lak</span>
                            </div>
                            <div class="mt-4 p-3 rounded-xl bg-surface border border-outline-variant flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-secondary text-lg">design_services</span>
                                    <div class="text-xs">
                                        <span class="text-primary font-bold">Online Editörde Hazırlandı</span>
                                        <span class="text-on-surface-variant font-label-numeric text-[10px] block sm:inline sm:ml-1">(<?= htmlspecialchars($item['design_code']) ?>)</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 text-xs font-semibold">
                                    <a href="product.php?slug=ekonomik-kartvizit-250gr" class="text-secondary hover:underline flex items-center gap-0.5">
                                        <span class="material-symbols-outlined text-sm">edit</span> Düzenle
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center justify-between gap-4 pt-4 mt-4 border-t border-outline-variant text-xs">
                            <div class="flex items-center gap-3">
                                <span class="text-on-surface-variant font-medium">Baskı Adedi:</span>
                                <span class="font-label-numeric font-bold text-primary px-2.5 py-1 rounded bg-surface-container border border-outline-variant">
                                    <?= number_format($item['quantity'], 0, ',', '.') ?> Adet
                                </span>
                            </div>
                            <a href="cart.php?remove=<?= $index ?>" class="text-red-600 hover:text-red-700 font-semibold flex items-center gap-1">
                                <span class="material-symbols-outlined text-base">delete</span> Sil
                            </a>
                        </div>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>

            <div class="flex justify-between items-center pt-2">
                <a href="index.php#products" class="inline-flex items-center gap-1.5 text-xs font-bold text-secondary hover:underline">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    <span>Alışverişe Devam Et</span>
                </a>
            </div>
        </div>

        <!-- Right Column: Order Summary (4 cols) -->
        <aside class="lg:col-span-4 sticky top-20 flex flex-col gap-6">
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-6 shadow-sm">
                <h2 class="text-base font-bold text-primary pb-4 border-b border-outline-variant">Sipariş Özeti</h2>
                <div class="space-y-3 py-4 border-b border-outline-variant text-xs">
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant">Ara Toplam:</span>
                        <span class="font-label-numeric font-bold text-primary"><?= number_format($subtotal, 2, ',', '.') ?> ₺</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant">KDV (%20):</span>
                        <span class="font-label-numeric font-semibold text-primary"><?= number_format($kdv, 2, ',', '.') ?> ₺</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-on-surface-variant">Anlaşmalı Kargo:</span>
                        <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded text-[11px]">Ücretsiz</span>
                    </div>
                </div>
                <div class="flex justify-between items-baseline pt-4 mb-6">
                    <span class="text-sm font-bold text-primary">Genel Toplam:</span>
                    <div class="text-right">
                        <span class="text-2xl font-extrabold font-label-numeric text-primary"><?= number_format($grand_total, 2, ',', '.') ?> ₺</span>
                        <span class="text-[10px] text-on-surface-variant block font-label-numeric">KDV Dahil</span>
                    </div>
                </div>
                <a href="checkout.php" class="w-full bg-primary hover:bg-slate-800 text-white font-bold py-3.5 px-4 rounded-xl text-xs sm:text-sm tracking-wide shadow-md flex items-center justify-center gap-2 transition-all active:scale-95">
                    <span>Güvenli Ödemeye Geç</span>
                    <span class="material-symbols-outlined text-base">arrow_forward</span>
                </a>
            </div>
        </aside>

    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
