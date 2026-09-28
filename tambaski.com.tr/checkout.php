<?php
/**
 * TAMBASKI.COM.TR - Sipariş & Ödeme Sayfası
 */
require_once __DIR__ . '/includes/functions.php';

$cart = get_cart();
if (empty($cart)) {
    header("Location: cart.php");
    exit;
}

$totals = get_cart_totals();
$grand_total_with_kdv = $totals['grand_total'] * 1.20;

$order_success = false;
$created_order_no = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'place_order') {
    $order_no = 'TB-' . date('Ymd') . '-' . rand(1000, 9999);
    $customer_name = trim($_POST['customer_name'] ?? '');
    $customer_email = trim($_POST['customer_email'] ?? '');
    $customer_phone = trim($_POST['customer_phone'] ?? '');
    $shipping_address = trim($_POST['shipping_address'] ?? '');
    $payment_method = $_POST['payment_method'] ?? 'credit_card';

    // Veritabanı bağlıysa siparişi kaydet
    $db = getDB();
    if ($db) {
        try {
            $stmt = $db->prepare("INSERT INTO orders (order_no, customer_name, customer_email, customer_phone, shipping_address, subtotal, shipping_fee, total_amount, payment_method, order_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([$order_no, $customer_name, $customer_email, $customer_phone, $shipping_address, $totals['subtotal'], $totals['shipping'], $grand_total_with_kdv, $payment_method]);
            $order_id = $db->lastInsertId();

            $item_stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, item_title, specifications, quantity, unit_price, total_price, design_source, uploaded_design_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($cart as $item) {
                $item_stmt->execute([$order_id, $item['product_id'], $item['item_title'], $item['specs'], $item['quantity'], $item['unit_price'], $item['total_price'], $item['design_source'], $item['design_file'] ?? null]);
            }
        } catch (Exception $e) {}
    }

    // Sepeti Temizle
    $_SESSION['cart'] = [];
    $order_success = true;
    $created_order_no = $order_no;
}

$page_title = "Ödeme & Sipariş Tamamlama – TamBaskı";
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-4">
    <?php if ($order_success): ?>
        <div class="apple-card p-5 text-center my-4">
            <div class="mb-3 text-success">
                <i class="bi bi-patch-check-fill" style="font-size: 80px;"></i>
            </div>
            <h3 class="fw-bold mb-2">Siparişiniz Başarıyla Alındı!</h3>
            <p class="text-muted mb-4">Sipariş numaranız: <strong class="text-dark fs-5"><?= htmlspecialchars($created_order_no) ?></strong></p>
            <p class="small text-muted mx-auto" style="max-width: 500px;">Tasarım dosyalarınız baskı öncesi grafik ekibimiz tarafından incelenecek ve onaylandığında baskı aşamasına alınacaktır.</p>
            
            <div class="d-flex justify-content-center gap-3 mt-4">
                <a href="order_tracking.php?order_no=<?= urlencode($created_order_no) ?>" class="btn btn-apple btn-apple-orange px-4 py-2">
                    <i class="bi bi-truck me-2"></i> Sipariş Durumunu Takip Et
                </a>
                <a href="index.php" class="btn btn-apple btn-apple-secondary px-4 py-2">Anasayfaya Dön</a>
            </div>
        </div>
    <?php else: ?>
        <h3 class="fw-bold mb-4"><i class="bi bi-credit-card-2-front text-primary me-2"></i>Ödeme & Teslimat Bilgileri</h3>

        <form method="POST">
            <input type="hidden" name="action" value="place_order">

            <div class="row g-4">
                <div class="col-lg-8">
                    <!-- İletişim & Teslimat Bilgileri -->
                    <div class="apple-card p-4 mb-4">
                        <h5 class="fw-bold mb-3">1. İletişim & Teslimat Adresi</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Ad Soyad / Yetkili</label>
                                <input type="text" name="customer_name" class="form-control" required placeholder="Adınız Soyadınız">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">E-Posta Adresi</label>
                                <input type="email" name="customer_email" class="form-control" required placeholder="ornek@alanadi.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Telefon Numarası</label>
                                <input type="tel" name="customer_phone" class="form-control" required placeholder="05XX XXX XX XX">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Şehir / İlçe</label>
                                <input type="text" name="city" class="form-control" placeholder="İstanbul / Kadıköy">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Teslimat Adresi (Açık Adres)</label>
                                <textarea name="shipping_address" class="form-control" rows="3" required placeholder="Cadde, sokak, bina ve kapı no..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Ödeme Yöntemi -->
                    <div class="apple-card p-4">
                        <h5 class="fw-bold mb-3">2. Ödeme Yöntemi Seçin</h5>
                        
                        <div class="form-check p-3 border rounded-3 mb-2 bg-light">
                            <input class="form-check-input ms-0 me-2" type="radio" name="payment_method" id="payCC" value="credit_card" checked>
                            <label class="form-check-label fw-bold" for="payCC">
                                <i class="bi bi-credit-card-2-front text-primary me-2"></i> Kredi / Banka Kartı (PayTR 3D Secure)
                            </label>
                            <small class="text-muted d-block ms-4">Tüm banka ve kredi kartlarıyla 12 taksite kadar güvenli ödeme.</small>
                        </div>

                        <div class="form-check p-3 border rounded-3 bg-light">
                            <input class="form-check-input ms-0 me-2" type="radio" name="payment_method" id="payBank" value="bank_transfer">
                            <label class="form-check-label fw-bold" for="payBank">
                                <i class="bi bi-bank text-success me-2"></i> Havale / EFT ile Ödeme
                            </label>
                            <small class="text-muted d-block ms-4">Sipariş sonrası banka hesap numaralarımıza havale yapabilirsiniz.</small>
                        </div>
                    </div>
                </div>

                <!-- Sağ: Sipariş Özeti -->
                <div class="col-lg-4">
                    <div class="apple-card p-4 sticky-top" style="top: 90px;">
                        <h5 class="fw-bold mb-3">Siparişiniz</h5>
                        
                        <div class="mb-3">
                            <?php foreach ($cart as $item): ?>
                                <div class="d-flex justify-content-between py-2 border-bottom small">
                                    <div>
                                        <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                                        <div class="text-muted" style="font-size: 11px;"><?= $item['quantity'] ?> Adet</div>
                                    </div>
                                    <span class="fw-bold"><?= format_price($item['total_price']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Ara Toplam:</span>
                            <span><?= format_price($totals['subtotal']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Kargo:</span>
                            <span><?= $totals['shipping'] > 0 ? format_price($totals['shipping']) : '<span class="text-success">Ücretsiz</span>' ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">KDV (%20):</span>
                            <span><?= format_price($totals['subtotal'] * 0.20) ?></span>
                        </div>

                        <div class="d-flex justify-content-between mt-3 pt-3 border-top">
                            <span class="fw-bold fs-6">Toplam Tutar:</span>
                            <span class="fw-extrabold text-danger fs-4"><?= format_price($grand_total_with_kdv) ?></span>
                        </div>

                        <button type="submit" class="btn btn-apple btn-apple-orange w-100 py-3 mt-4 fw-bold shadow">
                            <i class="bi bi-shield-lock-fill me-2"></i> Siparişi Onayla & Öde
                        </button>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
