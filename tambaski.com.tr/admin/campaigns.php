<?php
/**
 * TAMBASKI.COM.TR - Admin Kampanya & Kupon Kurguları Modülü
 */
$page_title = "Kampanya Kurguları & Kupon Yönetimi";
require_once __DIR__ . '/header.php';

$success_msg = null;

// Mock Kuponlar & Kampanya Kuralları
if (!isset($_SESSION['mock_coupons'])) {
    $_SESSION['mock_coupons'] = [
        [
            'id' => 1,
            'code' => 'YENIYIL15',
            'type' => 'percent',
            'amount' => 15,
            'min_basket' => 500,
            'max_discount' => 300,
            'usage_limit' => 100,
            'used_count' => 34,
            'expire_date' => '2026-12-31',
            'status' => 1
        ],
        [
            'id' => 2,
            'code' => 'KARTVIZIT50',
            'type' => 'fixed',
            'amount' => 50,
            'min_basket' => 400,
            'max_discount' => 50,
            'usage_limit' => 500,
            'used_count' => 128,
            'expire_date' => '2026-10-15',
            'status' => 1
        ]
    ];
}

// Yeni Kupon Ekleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_coupon') {
    $new_coupon = [
        'id' => count($_SESSION['mock_coupons']) + 1,
        'code' => strtoupper(trim($_POST['code'] ?? '')),
        'type' => $_POST['type'] ?? 'percent',
        'amount' => (float)($_POST['amount'] ?? 0),
        'min_basket' => (float)($_POST['min_basket'] ?? 0),
        'max_discount' => (float)($_POST['max_discount'] ?? 0),
        'usage_limit' => (int)($_POST['usage_limit'] ?? 100),
        'used_count' => 0,
        'expire_date' => $_POST['expire_date'] ?? date('Y-m-d', strtotime('+30 days')),
        'status' => 1
    ];
    $_SESSION['mock_coupons'][] = $new_coupon;
    $success_msg = "Yeni kupon başarıyla oluşturuldu ve aktif edildi!";
}

// Kupon Silme
if (isset($_GET['delete_coupon'])) {
    $del_id = (int)$_GET['delete_coupon'];
    $_SESSION['mock_coupons'] = array_filter($_SESSION['mock_coupons'], function($c) use ($del_id) {
        return $c['id'] !== $del_id;
    });
    $success_msg = "Kupon sistemden kaldırıldı.";
}
?>

<div class="row g-4">
    <!-- Başlık & Buton -->
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-percent text-danger me-2"></i>Kampanya & İndirim Kurguları</h3>
            <p class="text-muted small mb-0">Müşterilerinize özel indirim kuponları, sepet kuralları ve üst duyuru bandı yönetimi</p>
        </div>
        <button class="btn btn-apple btn-apple-orange" data-bs-toggle="modal" data-bs-target="#newCouponModal">
            <i class="bi bi-plus-lg me-1"></i> Yeni Kupon Oluştur
        </button>
    </div>

    <?php if ($success_msg): ?>
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($success_msg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <!-- 1. KUPONLAR LİSTESİ -->
    <div class="col-lg-8">
        <div class="apple-card p-4 bg-white mb-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-ticket-perforated text-primary me-2"></i>Aktif İndirim Kuponları</h5>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light small">
                        <tr>
                            <th>Kupon Kodu</th>
                            <th>İndirim</th>
                            <th>Min. Sepet</th>
                            <th>Kullanım</th>
                            <th>Son Tarih</th>
                            <th>Durum</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['mock_coupons'] as $coupon): ?>
                            <tr>
                                <td><code class="fs-6 fw-bold text-dark px-2 py-1 bg-light rounded"><?= htmlspecialchars($coupon['code']) ?></code></td>
                                <td>
                                    <span class="badge bg-danger-subtle text-danger fs-6">
                                        <?= $coupon['type'] === 'percent' ? '%' . $coupon['amount'] : format_price($coupon['amount']) ?>
                                    </span>
                                </td>
                                <td><?= format_price($coupon['min_basket']) ?></td>
                                <td>
                                    <small><?= $coupon['used_count'] ?> / <?= $coupon['usage_limit'] ?></small>
                                    <div class="progress" style="height: 4px;">
                                        <div class="progress-bar bg-primary" style="width: <?= ($coupon['used_count'] / $coupon['usage_limit']) * 100 ?>%"></div>
                                    </div>
                                </td>
                                <td class="small text-muted"><?= $coupon['expire_date'] ?></td>
                                <td><span class="badge bg-success">Aktif</span></td>
                                <td>
                                    <a href="campaigns.php?delete_coupon=<?= $coupon['id'] ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Bu kuponu silmek istediğinize emin misiniz?');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. OTOMATİK SEPET İNDİRİM KURALLARI -->
        <div class="apple-card p-4 bg-white">
            <h5 class="fw-bold mb-3"><i class="bi bi-cart-check text-success me-2"></i>Otomatik Sepet Kurguları (Kupunsuz)</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 border rounded-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0">Tirajlı Alışveriş İndirimi</h6>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" checked>
                            </div>
                        </div>
                        <p class="text-muted small mb-2">1.500 TL ve üzeri sepette anında %10 indirim uygulanır.</p>
                        <small class="badge bg-primary">Aktif Kural</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 border rounded-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0">Kombinasyon Kampanyası</h6>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" checked>
                            </div>
                        </div>
                        <p class="text-muted small mb-2">Kartvizit + Cepli Dosya birlikte alındığında 150 ₺ ek indirim.</p>
                        <small class="badge bg-primary">Aktif Kural</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SAĞ: ÜST DUYURU & FLASH BAR YÖNETİMİ -->
    <div class="col-lg-4">
        <div class="apple-card p-4 bg-white">
            <h5 class="fw-bold mb-3"><i class="bi bi-megaphone-fill text-warning me-2"></i>Üst Duyuru Bandı (Header Bar)</h5>
            
            <form method="POST">
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="announcementActive" checked>
                        <label class="form-check-label fw-bold small" for="announcementActive">Duyuru Bandı Yayında</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Duyuru Metni</label>
                    <textarea class="form-control" rows="2">🚀 750 ₺ ve Üzeri Siparişlerde Kargo Ücretsiz! • Türkiye'nin Her Yerine Hızlı Gönderim</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Sağ Taraf Buton / Link Metni</label>
                    <input type="text" class="form-control" value="E-Bayi Ol %25 İndirim Kazan">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Yönlendirme Linki (URL)</label>
                    <input type="text" class="form-control" value="dealer_apply.php">
                </div>

                <button type="button" class="btn btn-apple btn-apple-orange w-100 py-2">
                    <i class="bi bi-save me-1"></i> Duyuru Bandını Güncelle
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Yeni Kupon Ekleme Modalı -->
<div class="modal fade" id="newCouponModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Yeni İndirim Kuponu Tanımla</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add_coupon">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold">Kupon Kodu</label>
                            <input type="text" name="code" class="form-control form-control-lg text-uppercase fw-bold" placeholder="Örn: EKIM20" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">İndirim Türü</label>
                            <select name="type" class="form-select">
                                <option value="percent">% Yüzde İndirim</option>
                                <option value="fixed">Sabit Tutar (₺)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">İndirim Oranı / Tutarı</label>
                            <input type="number" name="amount" class="form-control" placeholder="15" required min="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Minimum Sepet Tutarı (₺)</label>
                            <input type="number" name="min_basket" class="form-control" value="500">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Maksimum İndirim (₺)</label>
                            <input type="number" name="max_discount" class="form-control" value="300">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Toplam Kullanım Limiti</label>
                            <input type="number" name="usage_limit" class="form-control" value="100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Son Kullanma Tarihi</label>
                            <input type="date" name="expire_date" class="form-control" value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Vazgeç</button>
                    <button type="submit" class="btn btn-apple btn-apple-orange rounded-pill px-4">Kuponu Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
