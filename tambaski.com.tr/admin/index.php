<?php
require_once __DIR__ . '/../config/config.php';
$pageTitle = 'Dashboard';
require_once __DIR__ . '/header.php';

$dbConn = Database::getInstance()->getConnection();

// İstatistikler
$totalSales = $dbConn ? $dbConn->query("SELECT SUM(total_amount) FROM orders WHERE payment_status = 'paid'")->fetchColumn() : 0;
$totalOrders = $dbConn ? $dbConn->query("SELECT COUNT(*) FROM orders")->fetchColumn() : 0;
$totalProducts = $dbConn ? $dbConn->query("SELECT COUNT(*) FROM products WHERE status = 1")->fetchColumn() : 0;
$totalDealers = $dbConn ? $dbConn->query("SELECT COUNT(*) FROM users WHERE role = 'dealer' AND dealer_status = 'approved'")->fetchColumn() : 0;

$orderModel = new Order();
$recentOrders = $orderModel->getAll(10);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Yönetim & Operasyon Paneli</h4>
        <p class="text-muted small mb-0">Online matbaa siparişlerinizi, tasarımları ve AI entegrasyonlarını buradan yönetin.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?= SITE_URL ?>/admin/orders.php" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-bag-check me-1"></i> Gelen Siparişler
        </a>
        <a href="<?= SITE_URL ?>/admin/products.php" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-box-seam me-1"></i> Ürün Listesi
        </a>
        <a href="<?= SITE_URL ?>/admin/products.php?action=create" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Yeni Ürün Ekle
        </a>
    </div>
</div>

<!-- 4'lü İstatistik Kartları -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="apple-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small">Tamamlanan Satışlar</span>
                    <h4 class="fw-bold mb-0 text-success"><?= Helper::formatPrice($totalSales ?: 0) ?></h4>
                </div>
                <div class="p-3 bg-success-subtle text-success rounded-4 fs-4">
                    <i class="bi bi-currency-exchange"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="apple-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small">Toplam Sipariş</span>
                    <h4 class="fw-bold mb-0 text-primary"><?= number_format($totalOrders) ?></h4>
                </div>
                <div class="p-3 bg-primary-subtle text-primary rounded-4 fs-4">
                    <i class="bi bi-bag-check"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="apple-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small">Aktif Matbaa Ürünleri</span>
                    <h4 class="fw-bold mb-0 text-dark"><?= number_format($totalProducts) ?></h4>
                </div>
                <div class="p-3 bg-secondary-subtle text-secondary rounded-4 fs-4">
                    <i class="bi bi-box-seam"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="apple-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small">Aktif E-Bayiler</span>
                    <h4 class="fw-bold mb-0 text-warning"><?= number_format($totalDealers) ?></h4>
                </div>
                <div class="p-3 bg-warning-subtle text-warning rounded-4 fs-4">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Son Siparişler ve Vektörel Tasarım İndirme Listesi -->
<div class="apple-card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Son Gelen Siparişler & Tasarım Dosyaları</h5>
        <a href="<?= SITE_URL ?>/admin/orders.php" class="btn btn-sm btn-outline-primary">Tüm Siparişleri Gör</a>
    </div>

    <?php if (empty($recentOrders)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-bag-x fs-1 d-block mb-2"></i>
            Henüz verilmiş bir sipariş bulunmuyor.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle small">
                <thead class="table-light">
                    <tr>
                        <th>Sipariş No</th>
                        <th>Müşteri</th>
                        <th>Tutar</th>
                        <th>Ödeme Yöntemi</th>
                        <th>Ödeme Durumu</th>
                        <th>Sipariş Durumu</th>
                        <th>Tasarım Dosyası</th>
                        <th class="text-end">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $ord): 
                        $items = $orderModel->getOrderItems($ord['id']);
                    ?>
                        <tr>
                            <td class="fw-bold font-monospace"><?= htmlspecialchars($ord['order_number']) ?></td>
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($ord['customer_name']) ?></div>
                                <div class="text-muted" style="font-size: 11px;"><?= htmlspecialchars($ord['customer_phone']) ?></div>
                            </td>
                            <td class="fw-bold text-primary"><?= Helper::formatPrice($ord['total_amount']) ?></td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= strtoupper($ord['payment_method']) ?></span>
                            </td>
                            <td><?= Helper::getPaymentStatusBadge($ord['payment_status']) ?></td>
                            <td><?= Helper::getOrderStatusBadge($ord['order_status']) ?></td>
                            <td>
                                <?php foreach ($items as $it): ?>
                                    <?php if (!empty($it['design_svg'])): ?>
                                        <a href="<?= SITE_URL ?>/admin/orders.php?action=download_svg&item_id=<?= $it['id'] ?>" class="badge bg-primary text-white text-decoration-none py-1 px-2 d-inline-block mb-1">
                                            <i class="bi bi-download me-1"></i> Vektörel SVG İndir
                                        </a>
                                    <?php elseif (!empty($it['design_file'])): ?>
                                        <a href="<?= SITE_URL . '/' . htmlspecialchars($it['design_file']) ?>" target="_blank" class="badge bg-dark text-white text-decoration-none py-1 px-2 d-inline-block mb-1">
                                            <i class="bi bi-cloud-arrow-down me-1"></i> Dosyayı İndir
                                        </a>
                                    <?php elseif ($it['design_type'] === 'design_request'): ?>
                                        <span class="badge bg-warning text-dark py-1 px-2 d-inline-block mb-1">Tasarım İstendi</span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </td>
                            <td class="text-end">
                                <a href="<?= SITE_URL ?>/admin/orders.php?action=view&id=<?= $ord['id'] ?>" class="btn btn-sm btn-apple-secondary py-1">
                                    Detay / Yönet
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
