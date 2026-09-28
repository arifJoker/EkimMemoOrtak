<?php
require_once __DIR__ . '/../config/config.php';
Auth::requireAdmin();

$orderModel = new Order();
$db = Database::getInstance()->getConnection();

$action = $_GET['action'] ?? 'list';

// 1. Vektörel SVG İndirme Aksiyonu (Operatör için)
if ($action === 'download_svg') {
    $itemId = (int)($_GET['item_id'] ?? 0);
    $stmt = $db->prepare("SELECT * FROM order_items WHERE id = ?");
    $stmt->execute([$itemId]);
    $item = $stmt->fetch();

    if ($item && !empty($item['design_svg'])) {
        $filename = 'baski_vektorel_siparis_' . $item['order_id'] . '_item_' . $item['id'] . '.svg';
        header('Content-Type: image/svg+xml');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $item['design_svg'];
        exit;
    } else {
        Helper::setFlash('danger', 'Bu kaleme ait vektörel SVG tasarımı bulunamadı.');
        header("Location: " . SITE_URL . "/admin/orders.php");
        exit;
    }
}

// 2. Durum ve Kargo Güncelleme Formu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
    $orderId = (int)($_POST['order_id'] ?? 0);
    $orderStatus = $_POST['order_status'] ?? 'pending_payment';
    $paymentStatus = $_POST['payment_status'] ?? 'pending';
    $cargoCompany = trim($_POST['cargo_company'] ?? '');
    $cargoTracking = trim($_POST['cargo_tracking_code'] ?? '');

    $orderModel->updateStatus($orderId, $orderStatus);
    $orderModel->updatePaymentStatus($orderId, $paymentStatus);
    if (!empty($cargoTracking)) {
        $orderModel->updateCargo($orderId, $cargoCompany, $cargoTracking);
    }

    Helper::setFlash('success', 'Sipariş durumu ve kargo bilgileri güncellendi.');
    header("Location: " . SITE_URL . "/admin/orders.php?action=view&id=" . $orderId);
    exit;
}

$pageTitle = 'Sipariş ve Tasarım Yönetimi';
require_once __DIR__ . '/header.php';

if ($action === 'view'):
    $orderId = (int)($_GET['id'] ?? 0);
    $order = $orderModel->getById($orderId);
    if (!$order) {
        echo '<div class="alert alert-danger">Sipariş bulunamadı.</div>';
        require_once __DIR__ . '/footer.php';
        exit;
    }
?>
    <!-- Sipariş Detay Görünümü -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= SITE_URL ?>/admin/orders.php" class="btn btn-sm btn-outline-secondary mb-2"><i class="bi bi-arrow-left"></i> Siparişlere Dön</a>
            <h4 class="fw-bold mb-0">Sipariş No: <?= htmlspecialchars($order['order_number']) ?></h4>
            <span class="text-muted small">Tarih: <?= date('d.m.Y H:i:s', strtotime($order['created_at'])) ?></span>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Fişi Yazdır
            </button>
            <?= Helper::getOrderStatusBadge($order['order_status']) ?>
            <?= Helper::getPaymentStatusBadge($order['payment_status']) ?>
        </div>
    </div>

    <div class="row g-4">
        
        <!-- Sol: Sipariş Kalemleri & Vektörel Dosyalar -->
        <div class="col-lg-8">
            <div class="apple-card p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Baskı Kalemleri & Tasarım Dosyaları</h5>
                
                <?php foreach ($order['items'] as $item): ?>
                    <div class="p-3 bg-light rounded-4 border mb-3">
                        <div class="row align-items-center g-3">
                            <div class="col-md-3 text-center">
                                <?php if (!empty($item['design_svg'])): ?>
                                    <div class="border rounded-3 p-1 bg-white shadow-sm mb-2" style="max-height: 100px; overflow: hidden;">
                                        <?= $item['design_svg'] ?>
                                    </div>
                                    <a href="<?= SITE_URL ?>/admin/orders.php?action=download_svg&item_id=<?= $item['id'] ?>" class="btn btn-sm btn-primary w-100 py-1">
                                        <i class="bi bi-download me-1"></i> Vektörel SVG İndir
                                    </a>
                                <?php elseif (!empty($item['design_file'])): ?>
                                    <i class="bi bi-file-earmark-arrow-up text-primary fs-1 d-block mb-1"></i>
                                    <a href="<?= SITE_URL . '/' . htmlspecialchars($item['design_file']) ?>" target="_blank" class="btn btn-sm btn-dark w-100 py-1">
                                        <i class="bi bi-cloud-arrow-down me-1"></i> Dosyayı İndir
                                    </a>
                                <?php else: ?>
                                    <i class="bi bi-printer text-muted fs-1"></i>
                                    <span class="badge bg-secondary d-block mt-1">Dosya Yok</span>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6">
                                <h6 class="fw-bold mb-1"><?= htmlspecialchars($item['product_name']) ?></h6>
                                <div class="small text-muted mb-1"><strong>Adet:</strong> <?= number_format($item['quantity'], 0, '', '.') ?> Adet</div>
                                <?php if (!empty($item['options_array'])): ?>
                                    <div class="small text-muted mb-2">
                                        <?= implode('<br>', array_map('htmlspecialchars', $item['options_array'])) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($item['design_notes'])): ?>
                                    <div class="p-2 bg-white rounded border small mt-2">
                                        <strong>Müşteri Notu:</strong> <?= nl2br(htmlspecialchars($item['design_notes'])) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-3 text-md-end">
                                <div class="fw-bold text-primary fs-5"><?= Helper::formatPrice($item['total_price']) ?></div>
                                <small class="text-muted">Birim: <?= Helper::formatPrice($item['unit_price']) ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="text-end pt-2 border-top">
                    <div class="small text-muted mb-1">Ara Toplam: <strong><?= Helper::formatPrice($order['subtotal']) ?></strong></div>
                    <div class="small text-muted mb-1">KDV: <strong><?= Helper::formatPrice($order['tax_amount']) ?></strong></div>
                    <?php if ($order['discount_amount'] > 0): ?>
                        <div class="small text-success mb-1">İndirim: <strong>-<?= Helper::formatPrice($order['discount_amount']) ?></strong></div>
                    <?php endif; ?>
                    <div class="small text-muted mb-2">Kargo: <strong><?= Helper::formatPrice($order['shipping_fee']) ?></strong></div>
                    <div class="fs-5 fw-bold text-dark">Genel Toplam: <span class="text-primary"><?= Helper::formatPrice($order['total_amount']) ?></span></div>
                </div>
            </div>

            <!-- Adres ve Müşteri Kartı -->
            <div class="apple-card p-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Müşteri & Teslimat Bilgileri</h5>
                <div class="row g-3 small">
                    <div class="col-md-6">
                        <div class="text-muted">Müşteri Adı:</div>
                        <div class="fw-bold"><?= htmlspecialchars($order['customer_name']) ?></div>
                        <div class="text-muted mt-2">E-Posta:</div>
                        <div><?= htmlspecialchars($order['customer_email']) ?></div>
                        <div class="text-muted mt-2">Telefon:</div>
                        <div><?= htmlspecialchars($order['customer_phone']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted">Teslimat Adresi:</div>
                        <div class="fw-bold"><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></div>
                        <div><?= htmlspecialchars($order['shipping_district']) ?> / <?= htmlspecialchars($order['shipping_city']) ?></div>
                        <?php if ($order['billing_type'] === 'corporate'): ?>
                            <div class="mt-2 p-2 bg-light rounded border">
                                <strong>Kurumsal Fatura:</strong> <?= htmlspecialchars($order['billing_company']) ?><br>
                                <strong>Vergi No:</strong> <?= htmlspecialchars($order['tax_number']) ?> (<?= htmlspecialchars($order['tax_office']) ?>)
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sağ: Durum & Kargo Güncelleme Formu -->
        <div class="col-lg-4">
            <div class="apple-card p-4 sticky-top" style="top: 20px;">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Sipariş İşlemleri</h5>
                
                <form action="<?= SITE_URL ?>/admin/orders.php?action=update" method="POST">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Sipariş / Üretim Durumu</label>
                        <select name="order_status" class="form-select">
                            <option value="pending_payment" <?= $order['order_status'] === 'pending_payment' ? 'selected' : '' ?>>Ödeme Bekleniyor</option>
                            <option value="payment_received" <?= $order['order_status'] === 'payment_received' ? 'selected' : '' ?>>Ödeme Alındı</option>
                            <option value="design_approval" <?= $order['order_status'] === 'design_approval' ? 'selected' : '' ?>>Tasarım Onayında</option>
                            <option value="in_production" <?= $order['order_status'] === 'in_production' ? 'selected' : '' ?>>Baskıda / Üretimde</option>
                            <option value="packaged" <?= $order['order_status'] === 'packaged' ? 'selected' : '' ?>>Paketlendi</option>
                            <option value="shipped" <?= $order['order_status'] === 'shipped' ? 'selected' : '' ?>>Kargoya Verildi</option>
                            <option value="delivered" <?= $order['order_status'] === 'delivered' ? 'selected' : '' ?>>Teslim Edildi</option>
                            <option value="cancelled" <?= $order['order_status'] === 'cancelled' ? 'selected' : '' ?>>İptal Edildi</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Ödeme Durumu</label>
                        <select name="payment_status" class="form-select">
                            <option value="pending" <?= $order['payment_status'] === 'pending' ? 'selected' : '' ?>>Bekliyor</option>
                            <option value="paid" <?= $order['payment_status'] === 'paid' ? 'selected' : '' ?>>Ödendi</option>
                            <option value="failed" <?= $order['payment_status'] === 'failed' ? 'selected' : '' ?>>Başarısız</option>
                            <option value="refunded" <?= $order['payment_status'] === 'refunded' ? 'selected' : '' ?>>İade Edildi</option>
                        </select>
                    </div>

                    <hr class="my-3">

                    <h6 class="fw-bold mb-2 small text-primary"><i class="bi bi-truck me-1"></i> Kargo Bilgileri</h6>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kargo Firması</label>
                        <select name="cargo_company" class="form-select">
                            <?php foreach (Cargo::getCompanies() as $key => $c): ?>
                                <option value="<?= $key ?>" <?= ($order['cargo_company'] === $key) ? 'selected' : '' ?>><?= $c['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Kargo Takip No</label>
                        <input type="text" name="cargo_tracking_code" class="form-control" placeholder="Örn: 123456789012" value="<?= htmlspecialchars($order['cargo_tracking_code'] ?? '') ?>">
                        <?php if (!empty($order['cargo_tracking_code'])): ?>
                            <a href="<?= Cargo::getTrackingLink($order['cargo_company'], $order['cargo_tracking_code']) ?>" target="_blank" class="small mt-1 d-inline-block text-decoration-none">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Kargo Sitesinde Takip Et
                            </a>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                        <i class="bi bi-save me-1"></i> Bilgileri Güncelle
                    </button>
                </form>

            </div>
        </div>

    </div>

<?php else: 
    $statusFilter = $_GET['status'] ?? null;
    $search = $_GET['search'] ?? null;
    $orders = $orderModel->getAll(100, $statusFilter, $search);
?>
    <!-- Sipariş Listesi Görünümü -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Siparişler & Tasarım Yönetimi</h4>
            <p class="text-muted small mb-0">Gelen baskı siparişlerini görüntüleyin ve vektörel çıktıları indirin.</p>
        </div>
    </div>

    <!-- Filtreleme Çubuğu -->
    <div class="apple-card p-3 mb-4">
        <form action="<?= SITE_URL ?>/admin/orders.php" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Sipariş No, Müşteri Adı veya Tel..." value="<?= htmlspecialchars($search ?? '') ?>">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Tüm Sipariş Durumları</option>
                    <option value="pending_payment" <?= $statusFilter === 'pending_payment' ? 'selected' : '' ?>>Ödeme Bekleniyor</option>
                    <option value="payment_received" <?= $statusFilter === 'payment_received' ? 'selected' : '' ?>>Ödeme Alındı</option>
                    <option value="design_approval" <?= $statusFilter === 'design_approval' ? 'selected' : '' ?>>Tasarım Onayında</option>
                    <option value="in_production" <?= $statusFilter === 'in_production' ? 'selected' : '' ?>>Baskıda / Üretimde</option>
                    <option value="shipped" <?= $statusFilter === 'shipped' ? 'selected' : '' ?>>Kargoya Verildi</option>
                    <option value="delivered" <?= $statusFilter === 'delivered' ? 'selected' : '' ?>>Teslim Edildi</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-dark w-100"><i class="bi bi-filter"></i> Filtrele</button>
            </div>
        </form>
    </div>

    <!-- Sipariş Tablosu -->
    <div class="apple-card p-4">
        <?php if (empty($orders)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-search fs-1 d-block mb-2"></i>
                Kayıtlı sipariş bulunamadı.
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
                            <th>Vektörel / Dosya</th>
                            <th class="text-end">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $ord): 
                            $items = $orderModel->getOrderItems($ord['id']);
                        ?>
                            <tr>
                                <td class="fw-bold font-monospace"><?= htmlspecialchars($ord['order_number']) ?></td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($ord['customer_name']) ?></div>
                                    <div class="text-muted" style="font-size: 11px;"><?= htmlspecialchars($ord['customer_phone']) ?></div>
                                </td>
                                <td class="fw-bold text-primary"><?= Helper::formatPrice($ord['total_amount']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= strtoupper($ord['payment_method']) ?></span></td>
                                <td><?= Helper::getPaymentStatusBadge($ord['payment_status']) ?></td>
                                <td><?= Helper::getOrderStatusBadge($ord['order_status']) ?></td>
                                <td>
                                    <?php foreach ($items as $it): ?>
                                        <?php if (!empty($it['design_svg'])): ?>
                                            <a href="<?= SITE_URL ?>/admin/orders.php?action=download_svg&item_id=<?= $it['id'] ?>" class="badge bg-primary text-white text-decoration-none py-1 px-2 d-inline-block mb-1">
                                                <i class="bi bi-download me-1"></i> SVG İndir
                                            </a>
                                        <?php elseif (!empty($it['design_file'])): ?>
                                            <a href="<?= SITE_URL . '/' . htmlspecialchars($it['design_file']) ?>" target="_blank" class="badge bg-dark text-white text-decoration-none py-1 px-2 d-inline-block mb-1">
                                                <i class="bi bi-cloud-arrow-down me-1"></i> Dosyayı İndir
                                            </a>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </td>
                                <td class="text-end">
                                    <a href="<?= SITE_URL ?>/admin/orders.php?action=view&id=<?= $ord['id'] ?>" class="btn btn-sm btn-apple-secondary py-1">
                                        İncele / Yönet
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/footer.php'; ?>
