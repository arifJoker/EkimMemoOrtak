<?php
/**
 * TAMBASKI.COM.TR - Admin Yönetim Paneli
 */
require_once __DIR__ . '/../includes/functions.php';

$db = getDB();
$orders = [];
$dealers = [];

if ($db) {
    try {
        $orders = $db->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 20")->fetchAll();
        $dealers = $db->query("SELECT * FROM users WHERE user_type = 'dealer' ORDER BY created_at DESC LIMIT 10")->fetchAll();
    } catch (Exception $e) {}
}

// Mock Siparişler (DB yoksa)
if (empty($orders)) {
    $orders = [
        [
            'id' => 1,
            'order_no' => 'TB-20260928-1001',
            'customer_name' => 'Arif Uzun',
            'customer_phone' => '0544 000 00 00',
            'total_amount' => 1450.00,
            'payment_method' => 'credit_card',
            'payment_status' => 'paid',
            'order_status' => 'printing',
            'created_at' => '2026-09-28 19:50:00'
        ],
        [
            'id' => 2,
            'order_no' => 'TB-20260928-1002',
            'customer_name' => 'Hedef Reklam Ajansı',
            'customer_phone' => '0532 111 22 33',
            'total_amount' => 3850.00,
            'payment_method' => 'bank_transfer',
            'payment_status' => 'paid',
            'order_status' => 'approved',
            'created_at' => '2026-09-28 19:40:00'
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TamBaskı Admin Yönetim Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark py-3 px-4 shadow">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
            <img src="../assets/img/logo.svg" alt="TamBaskı" style="height: 32px; filter: brightness(0) invert(1);">
            <span class="badge bg-danger ms-2">Yönetici Paneli</span>
        </a>
        <div class="d-flex align-items-center gap-3">
            <a href="../index.php" target="_blank" class="btn btn-sm btn-outline-light"><i class="bi bi-box-arrow-up-right me-1"></i>Siteye Git</a>
        </div>
    </div>
</nav>

<div class="container-fluid py-4 px-4">
    <!-- İstatistik Kartları -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="apple-card p-3 bg-white">
                <span class="text-muted small">Bugünkü Siparişler</span>
                <h3 class="fw-bold mb-0">12</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="apple-card p-3 bg-white">
                <span class="text-muted small">Bekleyen Üretim</span>
                <h3 class="fw-bold text-warning mb-0">5 İş</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="apple-card p-3 bg-white">
                <span class="text-muted small">Aktif E-Bayiler</span>
                <h3 class="fw-bold text-success mb-0">28 Ajans</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="apple-card p-3 bg-white">
                <span class="text-muted small">Toplam Ciro (Aylık)</span>
                <h3 class="fw-bold text-primary mb-0">184.500 ₺</h3>
            </div>
        </div>
    </div>

    <!-- Son Siparişler Tablosu -->
    <div class="apple-card p-4 bg-white mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-receipt text-primary me-2"></i>Son Gelen Siparişler & Tasarımlar</h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light small">
                    <tr>
                        <th>Sipariş No</th>
                        <th>Müşteri / Ajans</th>
                        <th>Tutar</th>
                        <th>Ödeme</th>
                        <th>Üretim Durumu</th>
                        <th>Tarih</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $ord): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($ord['order_no']) ?></strong></td>
                            <td>
                                <div><?= htmlspecialchars($ord['customer_name']) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($ord['customer_phone']) ?></small>
                            </td>
                            <td><strong><?= format_price($ord['total_amount']) ?></strong></td>
                            <td>
                                <span class="badge bg-success-subtle text-success">Ödendi</span>
                            </td>
                            <td>
                                <span class="badge bg-primary px-3 py-2 rounded-pill"><?= ucfirst($ord['order_status']) ?></span>
                            </td>
                            <td class="small text-muted"><?= $ord['created_at'] ?></td>
                            <td>
                                <button class="btn btn-sm btn-apple btn-apple-orange py-1 px-3">
                                    <i class="bi bi-cloud-arrow-down me-1"></i> Tasarım İndir
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
