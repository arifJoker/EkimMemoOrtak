<?php
require_once __DIR__ . '/config/config.php';

$orderNumber = $_GET['order_number'] ?? '';
$orderModel = new Order();
$order = $orderModel->getByOrderNumber($orderNumber);

if (!$order) {
    Helper::setFlash('danger', 'Sipariş bulunamadı.');
    header("Location: " . SITE_URL . "/cart.php");
    exit;
}

$pageTitle = 'Ödeme Ekranı – Sipariş No: ' . $order['order_number'];
require_once __DIR__ . '/includes/header.php';

$paymentMethod = $order['payment_method'];
$paytrHtml = '';
$iyzicoHtml = '';
$paymentError = '';

if ($paymentMethod === 'paytr') {
    $paytr = new PayTR();
    $tokenRes = $paytr->getToken($order);
    if ($tokenRes['success']) {
        $paytrToken = $tokenRes['token'];
        $paytrHtml = '<script src="https://www.paytr.com/js/iframeResizer.min.js"></script>'
                   . '<iframe src="https://www.paytr.com/odeme/guvenli/' . htmlspecialchars($paytrToken) . '" id="paytriframe" frameborder="0" scrolling="no" style="width: 100%; min-height: 600px;"></iframe>'
                   . '<script>iFrameResize({},"#paytriframe");</script>';
    } else {
        $paymentError = $tokenRes['error'] ?? 'PayTR ödeme başlatılamadı.';
    }
} elseif ($paymentMethod === 'iyzico') {
    $iyzico = new Iyzico();
    $iyziRes = $iyzico->initializeCheckoutForm($order);
    if ($iyziRes['success']) {
        $iyzicoHtml = '<div id="iyzipay-checkout-form" class="responsive">' . $iyziRes['checkout_form_content'] . '</div>';
    } else {
        $paymentError = $iyziRes['error'] ?? 'iyzico ödeme formu yüklenemedi.';
    }
}
?>

<div class="container py-5">
    
    <div class="max-w-800 mx-auto">
        
        <!-- Sipariş Başlığı -->
        <div class="apple-card p-4 text-center mb-4">
            <span class="badge bg-primary px-3 py-1 rounded-pill mb-2">Sipariş No: <?= htmlspecialchars($order['order_number']) ?></span>
            <h4 class="fw-bold mb-1">Ödeme Adımı</h4>
            <p class="text-muted small mb-0">Toplam Ödenecek Tutar: <strong class="text-primary fs-5"><?= Helper::formatPrice($order['total_amount']) ?></strong></p>
        </div>

        <?php if (!empty($paymentError)): ?>
            <div class="alert alert-danger rounded-4 shadow-sm p-4">
                <h5 class="fw-bold"><i class="bi bi-exclamation-octagon-fill me-2"></i>Ödeme Başlatma Hatası</h5>
                <p class="small mb-3"><?= htmlspecialchars($paymentError) ?></p>
                <div class="d-flex gap-2">
                    <a href="<?= SITE_URL ?>/checkout.php" class="btn btn-sm btn-apple-secondary">Bilgileri Düzenle</a>
                    <a href="<?= SITE_URL ?>/success.php?order_number=<?= urlencode($order['order_number']) ?>" class="btn btn-sm btn-warning">Havale/EFT ile Öde</a>
                </div>
            </div>
        <?php else: ?>

            <?php if ($paymentMethod === 'paytr'): ?>
                <div class="apple-card p-3 shadow-sm">
                    <?= $paytrHtml ?>
                </div>
            <?php elseif ($paymentMethod === 'iyzico'): ?>
                <div class="apple-card p-3 shadow-sm">
                    <?= $iyzicoHtml ?>
                </div>
            <?php elseif ($paymentMethod === 'bank_transfer'): ?>
                <div class="apple-card p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-bank me-2 text-primary"></i>Banka Havalesi / EFT Bilgileri</h5>
                    <p class="small text-muted mb-4">Lütfen aşağıdaki banka hesabımıza sipariş numaranızı (<strong><?= htmlspecialchars($order['order_number']) ?></strong>) açıklama kısmına yazarak ödemenizi gönderiniz.</p>
                    
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="row g-2 small">
                            <div class="col-sm-4 text-muted">Banka Adı:</div>
                            <div class="col-sm-8 fw-bold">Garanti BBVA / Türkiye İş Bankası</div>
                            <div class="col-sm-4 text-muted">Hesap Sahibi:</div>
                            <div class="col-sm-8 fw-bold">Baskı Matbaa A.Ş.</div>
                            <div class="col-sm-4 text-muted">IBAN:</div>
                            <div class="col-sm-8 fw-bold text-primary">TR00 0006 2000 0000 1234 5678 90</div>
                            <div class="col-sm-4 text-muted">Tutar:</div>
                            <div class="col-sm-8 fw-bold fs-6"><?= Helper::formatPrice($order['total_amount']) ?></div>
                        </div>
                    </div>

                    <a href="<?= SITE_URL ?>/success.php?order_number=<?= urlencode($order['order_number']) ?>" class="btn btn-apple btn-apple-pink w-100 py-3">
                        Ödemeyi Yaptım, Siparişimi Görüntüle
                    </a>
                </div>
            <?php endif; ?>

        <?php endif; ?>

    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
