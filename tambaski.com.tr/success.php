<?php
require_once __DIR__ . '/config/config.php';

$orderNumber = $_GET['order_number'] ?? '';
$orderModel = new Order();
$order = $orderModel->getByOrderNumber($orderNumber);

if (!$order) {
    Helper::setFlash('warning', 'Sipariş bilgisi bulunamadı.');
    header("Location: " . SITE_URL . "/");
    exit;
}

$pageTitle = 'Siparişiniz Alındı – ' . $order['order_number'];
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="max-w-800 mx-auto">
        
        <!-- Başarı Başlığı -->
        <div class="apple-card p-5 text-center mb-4">
            <div class="mb-3">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 64px;"></i>
            </div>
            <h3 class="fw-bold mb-2">Teşekkürler! Siparişiniz Başarıyla Alındı.</h3>
            <p class="text-muted small mb-3">Sipariş Takip Numaranız: <strong class="text-dark fs-5 font-monospace"><?= htmlspecialchars($order['order_number']) ?></strong></p>
            
            <div class="d-flex justify-content-center gap-2 mb-3">
                <?= Helper::getOrderStatusBadge($order['order_status']) ?>
                <?= Helper::getPaymentStatusBadge($order['payment_status']) ?>
            </div>

            <p class="text-muted small max-w-500 mx-auto">
                Grafik ve baskı ekibimiz siparişinizi incelemeye başladı. Baskı onayı ve kargo takip numarası SMS & E-posta ile tarafınıza iletilecektir.
            </p>
        </div>

        <!-- Sipariş Aşamaları / Durum Çizelgesi -->
        <div class="apple-card p-4 mb-4">
            <h6 class="fw-bold mb-4 border-bottom pb-2"><i class="bi bi-diagram-3 me-2 text-primary"></i>Sipariş Süreci</h6>
            <div class="row text-center g-2 small">
                <div class="col">
                    <i class="bi bi-check2-circle text-success fs-4 d-block mb-1"></i>
                    <span class="fw-bold">1. Sipariş Alındı</span>
                </div>
                <div class="col">
                    <i class="bi <?= in_array($order['order_status'], ['design_approval', 'in_production', 'packaged', 'shipped', 'delivered']) ? 'bi-check2-circle text-success' : 'bi-clock text-muted' ?> fs-4 d-block mb-1"></i>
                    <span class="fw-bold">2. Tasarım Kontrolü</span>
                </div>
                <div class="col">
                    <i class="bi <?= in_array($order['order_status'], ['in_production', 'packaged', 'shipped', 'delivered']) ? 'bi-check2-circle text-success' : 'bi-clock text-muted' ?> fs-4 d-block mb-1"></i>
                    <span class="fw-bold">3. Baskıda</span>
                </div>
                <div class="col">
                    <i class="bi <?= in_array($order['order_status'], ['shipped', 'delivered']) ? 'bi-check2-circle text-success' : 'bi-clock text-muted' ?> fs-4 d-block mb-1"></i>
                    <span class="fw-bold">4. Kargolandı</span>
                </div>
            </div>
        </div>

        <!-- Sipariş Kalemleri & Tasarım Detayı -->
        <div class="apple-card p-4 mb-4">
            <h6 class="fw-bold mb-3 border-bottom pb-2">Sipariş Edilen Ürünler & Tasarımlar</h6>

            <?php foreach ($order['items'] as $item): ?>
                <div class="row align-items-center py-3 border-bottom g-3">
                    <div class="col-md-3 col-4 text-center">
                        <?php if (!empty($item['design_svg'])): ?>
                            <div class="border rounded-3 p-1 bg-light shadow-sm" style="max-height: 90px; overflow: hidden;">
                                <?= $item['design_svg'] ?>
                            </div>
                            <span class="badge bg-primary mt-1" style="font-size: 10px;">Vektörel SVG</span>
                        <?php elseif (!empty($item['featured_image'])): ?>
                            <img src="<?= SITE_URL . '/' . htmlspecialchars($item['featured_image']) ?>" class="img-fluid rounded-3" style="max-height: 80px; object-fit: contain;">
                        <?php else: ?>
                            <i class="bi bi-printer text-muted fs-1"></i>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 col-8">
                        <h6 class="fw-bold mb-1"><?= htmlspecialchars($item['product_name']) ?></h6>
                        <div class="small text-muted mb-1"><?= number_format($item['quantity'], 0, '', '.') ?> Adet</div>
                        <?php if (!empty($item['options_array'])): ?>
                            <div class="small text-muted"><?= implode(' • ', array_map('htmlspecialchars', $item['options_array'])) ?></div>
                        <?php endif; ?>
                        
                        <?php if ($item['design_type'] === 'uploaded' && !empty($item['design_file'])): ?>
                            <div class="mt-2">
                                <a href="<?= SITE_URL . '/' . htmlspecialchars($item['design_file']) ?>" target="_blank" class="btn btn-sm btn-outline-primary py-0">
                                    <i class="bi bi-download"></i> Yüklenen Dosyayı İndir
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-3 text-md-end">
                        <div class="fw-bold text-primary fs-5"><?= Helper::formatPrice($item['total_price']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="mt-3 pt-2 text-end">
                <div class="small text-muted">Ödenen Toplam Tutar: <strong class="fs-5 text-dark"><?= Helper::formatPrice($order['total_amount']) ?></strong></div>
            </div>
        </div>

        <!-- Butonlar -->
        <div class="d-flex justify-content-center gap-3">
            <a href="<?= SITE_URL ?>/order_tracking.php?order_number=<?= urlencode($order['order_number']) ?>" class="btn btn-apple btn-apple-secondary">
                <i class="bi bi-truck me-1"></i> Kargo & Sipariş Durumu Sorgula
            </a>
            <a href="<?= SITE_URL ?>/" class="btn btn-apple btn-apple-pink">
                <i class="bi bi-house-door me-1"></i> Ana Sayfaya Dön
            </a>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
