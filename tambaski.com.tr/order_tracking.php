<?php
require_once __DIR__ . '/config/config.php';

$orderNumber = trim($_GET['order_number'] ?? '');
$order = null;
$error = '';

if (!empty($orderNumber)) {
    $orderModel = new Order();
    $order = $orderModel->getByOrderNumber($orderNumber);
    if (!$order) {
        $error = 'Belirtilen sipariş numarasına ait kayıt bulunamadı. Lütfen sipariş numaranızı kontrol edin.';
    }
}

$pageTitle = 'Kargo ve Sipariş Takibi – TamBaskı';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="max-w-800 mx-auto">
        
        <div class="text-center mb-4">
            <h3 class="fw-bold"><i class="bi bi-truck text-primary me-2"></i>Kargo & Sipariş Takibi</h3>
            <p class="text-muted small">Siparişinizin üretim, baskı ve kargo durumunu anlık olarak sorgulayın.</p>
        </div>

        <!-- Arama Formu -->
        <div class="apple-card p-4 mb-4">
            <form action="<?= SITE_URL ?>/order_tracking.php" method="GET" class="d-flex gap-2">
                <input type="text" name="order_number" class="form-control" placeholder="Örn: BM-2609-A1B2" value="<?= htmlspecialchars($orderNumber) ?>" required>
                <button type="submit" class="btn btn-apple btn-apple-pink px-4">
                    <i class="bi bi-search me-1"></i> Sorgula
                </button>
            </form>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger rounded-4 p-3 small shadow-sm">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($order): ?>
            <!-- Sipariş Sonuç Kartı -->
            <div class="apple-card p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                    <div>
                        <span class="text-muted small">Sipariş Numarası:</span>
                        <h5 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($order['order_number']) ?></h5>
                    </div>
                    <div>
                        <?= Helper::getOrderStatusBadge($order['order_status']) ?>
                        <?= Helper::getPaymentStatusBadge($order['payment_status']) ?>
                    </div>
                </div>

                <div class="row g-3 small text-muted mb-4">
                    <div class="col-sm-6">
                        <div><strong>Müşteri:</strong> <?= htmlspecialchars($order['customer_name']) ?></div>
                        <div><strong>Tarih:</strong> <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></div>
                    </div>
                    <div class="col-sm-6">
                        <div><strong>Kargo Firması:</strong> <?= htmlspecialchars($order['cargo_company'] ?? 'Yurtiçi Kargo') ?></div>
                        <div><strong>Kargo Takip No:</strong> 
                            <?php if (!empty($order['cargo_tracking_code'])): ?>
                                <a href="<?= Cargo::getTrackingLink($order['cargo_company'], $order['cargo_tracking_code']) ?>" target="_blank" class="fw-bold text-primary text-decoration-none">
                                    <?= htmlspecialchars($order['cargo_tracking_code']) ?> <i class="bi bi-box-arrow-up-right small"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-warning">Henüz kargo takip kodu girilmedi (Üretimde).</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Sipariş Kalemleri -->
                <h6 class="fw-bold border-bottom pb-2 mb-3">Sipariş İçeriği</h6>
                <?php foreach ($order['items'] as $item): ?>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom small">
                        <div>
                            <div class="fw-bold"><?= htmlspecialchars($item['product_name']) ?> (<?= number_format($item['quantity'], 0, '', '.') ?> Adet)</div>
                            <?php if (!empty($item['options_array'])): ?>
                                <div class="text-muted"><?= implode(' • ', array_map('htmlspecialchars', $item['options_array'])) ?></div>
                            <?php endif; ?>
                        </div>
                        <span class="fw-bold text-primary"><?= Helper::formatPrice($item['total_price']) ?></span>
                    </div>
                <?php endforeach; ?>
                
                <div class="text-end mt-3">
                    <span class="small text-muted">Toplam Tutar: <strong class="fs-5 text-dark"><?= Helper::formatPrice($order['total_amount']) ?></strong></span>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
