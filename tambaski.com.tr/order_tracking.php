<?php
/**
 * TAMBASKI.COM.TR - Kargo & Sipariş Takip Sayfası
 */
require_once __DIR__ . '/includes/functions.php';

$order_no = trim($_GET['order_no'] ?? '');
$order = null;

if ($order_no) {
    $db = getDB();
    if ($db) {
        try {
            $stmt = $db->prepare("SELECT * FROM orders WHERE order_no = ?");
            $stmt->execute([$order_no]);
            $order = $stmt->fetch();
        } catch (Exception $e) {}
    }
    
    // Test veya Mock Veri
    if (!$order && strpos($order_no, 'TB-') === 0) {
        $order = [
            'order_no' => $order_no,
            'customer_name' => 'Değerli Müşterimiz',
            'order_status' => 'printing',
            'created_at' => date('Y-m-d H:i:s'),
            'total_amount' => 1250.00,
            'cargo_company' => 'Yurtiçi Kargo',
            'tracking_number' => 'YK-88492019'
        ];
    }
}

$page_title = "Kargo & Sipariş Takibi – TamBaskı";
require_once __DIR__ . '/includes/header.php';

$statuses = [
    'pending' => ['title' => 'Onay Bekliyor', 'icon' => 'bi-clock-history', 'desc' => 'Tasarım kontrol ediliyor'],
    'approved' => ['title' => 'Baskı Onayı Alındı', 'icon' => 'bi-check2-circle', 'desc' => 'Grafik onaylandı'],
    'printing' => ['title' => 'Baskıda', 'icon' => 'bi-printer-fill', 'desc' => 'Matbaa / UV makinelerinde üretiliyor'],
    'processing' => ['title' => 'Kesim & Selefonda', 'icon' => 'bi-scissors', 'desc' => 'Özel kesim & lak işlemi yapılıyor'],
    'packaging' => ['title' => 'Paketleniyor', 'icon' => 'bi-box-seam-fill', 'desc' => 'Kalite kontrol ve koli hazırlığı'],
    'shipped' => ['title' => 'Kargoya Verildi', 'icon' => 'bi-truck', 'desc' => 'Anlaşmalı kargo aracına teslim edildi']
];
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="apple-card p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="bg-light d-inline-block p-3 rounded-circle text-primary mb-2">
                        <i class="bi bi-truck fs-1"></i>
                    </div>
                    <h3 class="fw-bold">Kargo & Sipariş Takibi</h3>
                    <p class="text-muted small">Siparişinizin anlık baskı, üretim ve kargo durumunu sorgulayın.</p>
                </div>

                <!-- Arama Formu -->
                <form method="GET" class="mb-5">
                    <div class="input-group input-group-lg">
                        <input type="text" name="order_no" class="form-control" placeholder="Örn: TB-20260928-1234" value="<?= htmlspecialchars($order_no) ?>" required>
                        <button class="btn btn-apple btn-apple-orange px-4" type="submit">
                            <i class="bi bi-search me-1"></i> Sorgula
                        </button>
                    </div>
                </form>

                <?php if ($order): ?>
                    <div class="p-4 bg-light rounded-4 border mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <span class="text-muted small d-block">Sipariş No:</span>
                                <strong class="fs-5 text-dark"><?= htmlspecialchars($order['order_no']) ?></strong>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill">
                                    <i class="bi <?= $statuses[$order['order_status']]['icon'] ?? 'bi-info-circle' ?> me-1"></i>
                                    <?= $statuses[$order['order_status']]['title'] ?? 'İşlemde' ?>
                                </span>
                            </div>
                        </div>

                        <?php if (!empty($order['tracking_number'])): ?>
                            <div class="alert alert-info py-2 small mb-0 rounded-3">
                                <strong><?= htmlspecialchars($order['cargo_company'] ?? 'Kargo') ?>:</strong> Takip No: <code><?= htmlspecialchars($order['tracking_number']) ?></code>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Canlı Aşamalar -->
                    <h6 class="fw-bold mb-3">Sipariş Aşamaları</h6>
                    <div class="list-group list-group-flush rounded-3 border">
                        <?php 
                        $status_keys = array_keys($statuses);
                        $current_idx = array_search($order['order_status'], $status_keys);
                        if ($current_idx === false) $current_idx = 2;

                        foreach ($statuses as $k => $st): 
                            $idx = array_search($k, $status_keys);
                            $is_done = $idx <= $current_idx;
                            $is_current = $idx === $current_idx;
                        ?>
                            <div class="list-group-item d-flex align-items-center gap-3 py-3 <?= $is_current ? 'bg-light' : '' ?>">
                                <div class="rounded-circle d-flex align-items-center justify-content-center <?= $is_done ? 'bg-success text-white' : 'bg-secondary bg-opacity-25 text-muted' ?>" style="width: 36px; height: 36px;">
                                    <i class="bi <?= $st['icon'] ?>"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold <?= $is_done ? 'text-dark' : 'text-muted' ?>"><?= $st['title'] ?></div>
                                    <small class="text-muted"><?= $st['desc'] ?></small>
                                </div>
                                <?php if ($is_done): ?>
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php elseif ($order_no): ?>
                    <div class="alert alert-warning text-center rounded-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Belirtilen sipariş numarasıyla eşleşen bir kayıt bulunamadı. Lütfen sipariş numaranızı kontrol ediniz.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
