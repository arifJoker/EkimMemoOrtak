<?php
require_once __DIR__ . '/config/config.php';

$cart = new Cart();
$summary = $cart->getSummary();

if (empty($summary['items'])) {
    Helper::setFlash('warning', 'Sepetiniz boş olduğu için ödeme adımına geçilemedi.');
    header("Location: " . SITE_URL . "/cart.php");
    exit;
}

$user = Auth::user();
$orderModel = new Order();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName = trim($_POST['customer_name'] ?? '');
    $customerEmail = trim($_POST['customer_email'] ?? '');
    $customerPhone = trim($_POST['customer_phone'] ?? '');
    $shippingAddress = trim($_POST['shipping_address'] ?? '');
    $shippingCity = trim($_POST['shipping_city'] ?? '');
    $shippingDistrict = trim($_POST['shipping_district'] ?? '');
    $billingType = $_POST['billing_type'] ?? 'individual';
    $billingCompany = trim($_POST['billing_company'] ?? '');
    $taxNumber = trim($_POST['tax_number'] ?? '');
    $taxOffice = trim($_POST['tax_office'] ?? '');
    $paymentMethod = $_POST['payment_method'] ?? 'paytr';
    $notes = trim($_POST['notes'] ?? '');

    if (empty($customerName) || empty($customerEmail) || empty($customerPhone) || empty($shippingAddress) || empty($shippingCity)) {
        $errors[] = 'Lütfen zorunlu iletişim ve teslimat adres alanlarını doldurunuz.';
    }

    if (empty($errors)) {
        $orderData = [
            'customer_name'     => $customerName,
            'customer_email'    => $customerEmail,
            'customer_phone'    => $customerPhone,
            'shipping_address'  => $shippingAddress,
            'shipping_city'     => $shippingCity,
            'shipping_district' => $shippingDistrict,
            'billing_type'      => $billingType,
            'billing_company'   => $billingCompany,
            'tax_number'        => $taxNumber,
            'tax_office'        => $taxOffice,
            'payment_method'    => $paymentMethod,
            'notes'             => $notes
        ];

        $createRes = $orderModel->createFromCart($orderData);

        if ($createRes['success']) {
            $orderNumber = $createRes['order_number'];

            if ($paymentMethod === 'paytr' || $paymentMethod === 'iyzico') {
                header("Location: " . SITE_URL . "/payment.php?order_number=" . urlencode($orderNumber));
                exit;
            } else {
                header("Location: " . SITE_URL . "/success.php?order_number=" . urlencode($orderNumber));
                exit;
            }
        } else {
            $errors[] = $createRes['error'] ?? 'Sipariş oluşturulamadı.';
        }
    }
}

$pageTitle = 'Güvenli Ödeme & Teslimat – TamBaskı';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    
    <div class="max-w-900 mx-auto">
        <h3 class="fw-bold mb-4"><i class="bi bi-shield-check text-success me-2"></i>Güvenli Sipariş & Ödeme</h3>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger rounded-4 shadow-sm mb-4">
                <ul class="mb-0 small">
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= SITE_URL ?>/checkout.php" method="POST">
            
            <div class="row g-4">
                
                <!-- Sol Kolon: Teslimat ve Fatura Bilgileri -->
                <div class="col-lg-7">
                    
                    <!-- 1. İletişim Bilgileri -->
                    <div class="apple-card p-4 mb-4">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">1. İletişim Bilgileri</h5>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold">Ad Soyad *</label>
                                <input type="text" name="customer_name" class="form-control" value="<?= htmlspecialchars($_POST['customer_name'] ?? ($user['full_name'] ?? '')) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">E-Posta Adresi (Baskı Onayı İçin) *</label>
                                <input type="email" name="customer_email" class="form-control" value="<?= htmlspecialchars($_POST['customer_email'] ?? ($user['email'] ?? '')) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Cep Telefonu (SMS Kargo Takibi) *</label>
                                <input type="tel" name="customer_phone" class="form-control" placeholder="0555 123 45 67" value="<?= htmlspecialchars($_POST['customer_phone'] ?? ($user['phone'] ?? '')) ?>" required>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Kargo & Teslimat Adresi -->
                    <div class="apple-card p-4 mb-4">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">2. Kargo & Teslimat Adresi</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">İl *</label>
                                <input type="text" name="shipping_city" class="form-control" placeholder="İstanbul" value="<?= htmlspecialchars($_POST['shipping_city'] ?? ($user['city'] ?? '')) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">İlçe *</label>
                                <input type="text" name="shipping_district" class="form-control" placeholder="Kadıköy" value="<?= htmlspecialchars($_POST['shipping_district'] ?? ($user['district'] ?? '')) ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Açık Adres (Cadde, Mahalle, Kapı No) *</label>
                                <textarea name="shipping_address" class="form-control" rows="3" required><?= htmlspecialchars($_POST['shipping_address'] ?? ($user['address'] ?? '')) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Fatura Tipi & Bilgileri -->
                    <div class="apple-card p-4 mb-4">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">3. Fatura Bilgileri</h5>
                        <div class="d-flex gap-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="billing_type" id="billInd" value="individual" checked onclick="toggleBillingFields(false)">
                                <label class="form-check-label small fw-bold" for="billInd">Bireysel Fatura</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="billing_type" id="billCorp" value="corporate" onclick="toggleBillingFields(true)">
                                <label class="form-check-label small fw-bold" for="billCorp">Kurumsal Fatura (Şirket)</label>
                            </div>
                        </div>

                        <div id="corporateFields" style="display: none;">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Şirket Resmi Unvanı</label>
                                    <input type="text" name="billing_company" class="form-control" placeholder="Örn: ABC Reklam Ltd. Şti.">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Vergi Numarası</label>
                                    <input type="text" name="tax_number" class="form-control" placeholder="10 Haneli Vergi No">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Vergi Dairesi</label>
                                    <input type="text" name="tax_office" class="form-control" placeholder="Örn: Maslak V.D.">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Ödeme Yöntemi Tercihi -->
                    <div class="apple-card p-4">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">4. Ödeme Yöntemi Seçin</h5>
                        
                        <div class="d-flex flex-column gap-3">
                            
                            <!-- PayTR -->
                            <label class="d-flex align-items-center justify-content-between p-3 border rounded-3 cursor-pointer bg-light">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="radio" name="payment_method" value="paytr" class="form-check-input" checked>
                                    <div>
                                        <div class="fw-bold small">PayTR ile Kredi Kartı / Banka Kartı (12 Taksit)</div>
                                        <div class="text-muted" style="font-size: 11px;">Tüm banka kartlarına güvenli 3D Secure anında ödeme</div>
                                    </div>
                                </div>
                                <span class="badge bg-dark">PayTR</span>
                            </label>

                            <!-- iyzico -->
                            <label class="d-flex align-items-center justify-content-between p-3 border rounded-3 cursor-pointer bg-light">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="radio" name="payment_method" value="iyzico" class="form-check-input">
                                    <div>
                                        <div class="fw-bold small">iyzico Korumalı Alışveriş</div>
                                        <div class="text-muted" style="font-size: 11px;">Tek çekim veya taksitli 3D Secure ödeme</div>
                                    </div>
                                </div>
                                <span class="badge bg-primary">iyzico</span>
                            </label>

                            <!-- Havale / EFT -->
                            <label class="d-flex align-items-center justify-content-between p-3 border rounded-3 cursor-pointer bg-light">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="radio" name="payment_method" value="bank_transfer" class="form-check-input">
                                    <div>
                                        <div class="fw-bold small">Banka Havalesi / EFT</div>
                                        <div class="text-muted" style="font-size: 11px;">Sipariş sonrası hesap numaralarımıza transfer edin</div>
                                    </div>
                                </div>
                                <span class="badge bg-secondary">Havale</span>
                            </label>

                        </div>

                        <div class="mt-3">
                            <label class="form-label small fw-bold">Sipariş / Kargo Notunuz (Opsiyonel)</label>
                            <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Varsa kargo veya teslimat notunuzu buraya yazabilirsiniz..."></textarea>
                        </div>
                    </div>

                </div>

                <!-- Sağ Kolon: Sipariş Özeti & Öde Butonu -->
                <div class="col-lg-5">
                    <div class="apple-card p-4 sticky-top" style="top: 85px;">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">Sipariş Kalemleri (<?= $summary['count'] ?>)</h5>

                        <div class="mb-3" style="max-height: 240px; overflow-y: auto;">
                            <?php foreach ($summary['items'] as $item): ?>
                                <div class="py-2 border-bottom small">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars($item['product_name']) ?></div>
                                            <div class="text-muted"><?= number_format($item['quantity'], 0, '', '.') ?> Adet</div>
                                        </div>
                                        <span class="fw-bold text-primary"><?= $item['formatted_price'] ?></span>
                                    </div>
                                    <?php if (!empty($item['upsell']) && $item['upsell']['active']): ?>
                                        <div class="p-2 rounded-3 mt-2 border" style="background: #fffbeb; border-color: #fde68a !important;">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1">
                                                <span style="font-size: 11px;" class="text-dark">
                                                    <i class="bi bi-stars text-danger"></i> +<?= $item['upsell']['added_quantity'] ?> Adet Daha: <strong><?= $item['upsell']['formatted_diff'] ?></strong>
                                                </span>
                                                <form action="<?= SITE_URL ?>/cart.php" method="POST" class="m-0">
                                                    <input type="hidden" name="action" value="upgrade_qty">
                                                    <input type="hidden" name="redirect_to" value="checkout">
                                                    <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                                    <input type="hidden" name="new_quantity" value="<?= $item['upsell']['target_quantity'] ?>">
                                                    <button type="submit" class="btn btn-xs btn-warning text-dark fw-bold rounded-pill px-2 py-0 shadow-sm" style="font-size: 10px;">
                                                        <?= number_format($item['upsell']['target_quantity'], 0, '', '.') ?> Adete Yükselt
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="d-flex justify-content-between small text-muted mb-2">
                            <span>Ara Toplam:</span>
                            <span class="fw-bold text-dark"><?= $summary['formatted_subtotal'] ?></span>
                        </div>
                        <div class="d-flex justify-content-between small text-muted mb-2">
                            <span>KDV:</span>
                            <span class="fw-bold text-dark"><?= $summary['formatted_tax'] ?></span>
                        </div>
                        <?php if ($summary['discount_amount'] > 0): ?>
                            <div class="d-flex justify-content-between small text-success fw-bold mb-2">
                                <span>İndirim / Kupon:</span>
                                <span>-<?= $summary['formatted_discount'] ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between small text-muted mb-3">
                            <span>Kargo:</span>
                            <span class="fw-bold text-dark"><?= $summary['formatted_shipping'] ?></span>
                        </div>

                        <hr class="my-3">

                        <div class="d-flex justify-content-between align-items-baseline mb-4">
                            <span class="fw-bold">Toplam Ödeme:</span>
                            <span class="price-display-lg text-primary"><?= $summary['formatted_grand_total'] ?></span>
                        </div>

                        <button type="submit" class="btn-apple btn-apple-pink w-100 py-3 fs-6 fw-bold shadow">
                            <i class="bi bi-lock-fill me-2"></i> Güvenli Ödemeye Geç
                        </button>

                        <div class="mt-3 text-center text-muted" style="font-size: 11px;">
                            "Güvenli Ödemeye Geç" butonuna basarak Mesafeli Satış Sözleşmesini kabul etmiş sayılırsınız.
                        </div>
                    </div>
                </div>

            </div>

        </form>

    </div>

</div>

<script>
function toggleBillingFields(isCorporate) {
    document.getElementById('corporateFields').style.display = isCorporate ? 'block' : 'none';
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
