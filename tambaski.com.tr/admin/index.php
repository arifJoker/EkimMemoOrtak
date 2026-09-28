<?php
/**
 * TAMBASKI.COM.TR - Admin Yönetim Paneli Anasayfası
 */
$page_title = "Siparişler & Üretim Takip";
require_once __DIR__ . '/header.php';

$db = getDB();
$orders = [];

if ($db) {
    try {
        $orders = $db->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 20")->fetchAll();
    } catch (Exception $e) {}
}

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
        ],
        [
            'id' => 3,
            'order_no' => 'TB-20260928-1003',
            'customer_name' => 'Kuzey Mimarlık (Pleksi Kesim)',
            'customer_phone' => '0533 222 44 55',
            'total_amount' => 2640.00,
            'payment_method' => 'credit_card',
            'payment_status' => 'paid',
            'order_status' => 'processing',
            'created_at' => '2026-09-28 18:20:00'
        ]
    ];
}
?>

<div class="row g-4">
    <!-- İstatistik Kartları -->
    <div class="col-md-3">
        <div class="apple-card p-4 bg-white">
            <span class="text-muted small d-block mb-1">Bugünkü Siparişler</span>
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="fw-bold mb-0">14</h2>
                <i class="bi bi-bag-check text-primary fs-2"></i>
            </div>
            <small class="text-success mt-2 d-block"><i class="bi bi-arrow-up-right"></i> Düne göre +%20 artış</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="apple-card p-4 bg-white">
            <span class="text-muted small d-block mb-1">Baskı / Kesimde Olanlar</span>
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="fw-bold text-warning mb-0">6 İş</h2>
                <i class="bi bi-printer text-warning fs-2"></i>
            </div>
            <small class="text-muted mt-2 d-block">UV & Ofset parkurunda</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="apple-card p-4 bg-white">
            <span class="text-muted small d-block mb-1">Aktif E-Bayiler</span>
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="fw-bold text-success mb-0">32 Ajans</h2>
                <i class="bi bi-briefcase text-success fs-2"></i>
            </div>
            <small class="text-muted mt-2 d-block">%25 toptan iskonto tanımlı</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="apple-card p-4 bg-white">
            <span class="text-muted small d-block mb-1">Aylık Toplam Ciro</span>
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="fw-bold text-dark mb-0">214.800 ₺</h2>
                <i class="bi bi-wallet2 text-success fs-2"></i>
            </div>
            <small class="text-success mt-2 d-block"><i class="bi bi-graph-up"></i> Hedeflenen cironun %86'sı</small>
        </div>
    </div>

    <!-- Son Siparişler & Tasarım İndirme Listesi -->
    <div class="col-12">
        <div class="apple-card p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-receipt text-primary me-2"></i>Gelen Siparişler & Müşteri Tasarımları</h5>
                <div class="d-flex gap-2">
                    <span class="badge bg-light text-dark border p-2"><i class="bi bi-funnel me-1"></i>Tüm Siparişler</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light small">
                        <tr>
                            <th>Sipariş No</th>
                            <th>Müşteri / Ajans</th>
                            <th>Tutar (+KDV)</th>
                            <th>Ödeme Yöntemi</th>
                            <th>Üretim Durumu</th>
                            <th>Tarih</th>
                            <th>Tasarım & İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $ord): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($ord['order_no']) ?></strong></td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($ord['customer_name']) ?></div>
                                    <small class="text-muted"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($ord['customer_phone']) ?></small>
                                </td>
                                <td><strong class="text-danger"><?= format_price($ord['total_amount']) ?></strong></td>
                                <td>
                                    <?php if ($ord['payment_method'] === 'credit_card'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success"><i class="bi bi-credit-card me-1"></i>PayTR 3D</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary-subtle text-primary border border-primary"><i class="bi bi-bank me-1"></i>Havale/EFT</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <select class="form-select form-select-sm fw-bold w-auto" style="border-radius: 12px;">
                                        <option value="pending" <?= $ord['order_status'] === 'pending' ? 'selected' : '' ?>>⏳ Onay Bekliyor</option>
                                        <option value="approved" <?= $ord['order_status'] === 'approved' ? 'selected' : '' ?>>✅ Grafik Onaylandı</option>
                                        <option value="printing" <?= $ord['order_status'] === 'printing' ? 'selected' : '' ?>>🖨️ Baskıda</option>
                                        <option value="processing" <?= $ord['order_status'] === 'processing' ? 'selected' : '' ?>>✂️ Kesim & Selefonda</option>
                                        <option value="packaging" <?= $ord['order_status'] === 'packaging' ? 'selected' : '' ?>>📦 Paketleniyor</option>
                                        <option value="shipped" <?= $ord['order_status'] === 'shipped' ? 'selected' : '' ?>>🚚 Kargoya Verildi</option>
                                    </select>
                                </td>
                                <td class="small text-muted"><?= $ord['created_at'] ?></td>
                                <td>
                                    <button class="btn btn-sm btn-apple btn-apple-orange py-1 px-3" title="Müşterinin yüklediği PDF/AI dosyasını indir">
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
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
