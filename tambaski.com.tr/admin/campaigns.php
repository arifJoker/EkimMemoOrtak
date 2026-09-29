<?php
require_once __DIR__ . '/../config/config.php';
Auth::requireDesignPermission();

$db = Database::getInstance()->getConnection();
$action = $_GET['action'] ?? 'list';

if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $db->prepare("DELETE FROM campaigns WHERE id = ?");
    $stmt->execute([$id]);
    Helper::setFlash('success', 'Kampanya kuponu silindi.');
    header("Location: " . SITE_URL . "/admin/campaigns.php");
    exit;
}

// Genel Kargo & Sepet Limiti Güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_shipping_rules'])) {
    Helper::saveSetting('free_shipping_limit', (float)$_POST['free_shipping_limit']);
    Helper::saveSetting('default_shipping_fee', (float)$_POST['default_shipping_fee']);
    Helper::setFlash('success', 'Kargo kuralları ve ücretsiz kargo limiti güncellendi.');
    header("Location: " . SITE_URL . "/admin/campaigns.php");
    exit;
}

// Yeni Kupon / Kampanya Ekleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_campaign'])) {
    $title = trim($_POST['title'] ?? '');
    $code = strtoupper(trim($_POST['code'] ?? ''));
    $type = $_POST['type'] ?? 'percent';
    $value = (float)($_POST['value'] ?? 0);
    $minCart = (float)($_POST['min_cart_amount'] ?? 0);
    $limit = (int)($_POST['usage_limit'] ?? 1000);

    $stmt = $db->prepare("INSERT INTO campaigns (title, code, type, value, min_cart_amount, usage_limit) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $code, $type, $value, $minCart, $limit]);

    Helper::setFlash('success', 'Yeni indirim kuponu oluşturuldu.');
    header("Location: " . SITE_URL . "/admin/campaigns.php");
    exit;
}

$campaigns = $db->query("SELECT * FROM campaigns ORDER BY id DESC")->fetchAll();
$freeShippingLimit = Helper::getSetting('free_shipping_limit', '750.00');
$defaultShippingFee = Helper::getSetting('default_shipping_fee', '79.90');

$pageTitle = 'Kampanya & Sepet Kuralları Yönetimi';
require_once __DIR__ . '/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Kampanyalar & Sepet İndirim Kuralları</h4>
        <p class="text-muted small mb-0">İndirim kuponları, ücretsiz kargo limitleri ve promosyonları tanımlayın.</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Genel Kargo & Sepet Limiti Ayarı -->
    <div class="col-lg-6">
        <div class="apple-card p-4 h-100">
            <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary"><i class="bi bi-truck me-1"></i> Sepet Kargo Kuralları</h6>
            <form action="<?= SITE_URL ?>/admin/campaigns.php" method="POST">
                <input type="hidden" name="save_shipping_rules" value="1">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Ücretsiz Kargo Sepet Limiti ₺</label>
                        <input type="number" step="0.01" name="free_shipping_limit" class="form-control" value="<?= htmlspecialchars($freeShippingLimit) ?>" required>
                        <small class="text-muted" style="font-size: 11px;">Bu tutar ve üzeri sepetlerde kargo bedava olur.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Varsayılan Kargo Ücreti ₺</label>
                        <input type="number" step="0.01" name="default_shipping_fee" class="form-control" value="<?= htmlspecialchars($defaultShippingFee) ?>" required>
                    </div>
                    <div class="col-12 text-end mt-3">
                        <button type="submit" class="btn btn-dark btn-sm px-3">
                            <i class="bi bi-save me-1"></i> Kargo Limitini Güncelle
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Yeni Kupon Ekleme Formu -->
    <div class="col-lg-6">
        <div class="apple-card p-4 h-100">
            <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary"><i class="bi bi-ticket-perforated me-1"></i> Yeni Kupon Kodu Tanımla</h6>
            <form action="<?= SITE_URL ?>/admin/campaigns.php" method="POST">
                <input type="hidden" name="save_campaign" value="1">
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Kampanya Başlığı</label>
                        <input type="text" name="title" class="form-control form-control-sm" placeholder="Örn: Bahar İndirimi %15" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Kupon Kodu (BÜYÜK HARF)</label>
                        <input type="text" name="code" class="form-control form-control-sm text-uppercase" placeholder="BAHAR15" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">İndirim Türü</label>
                        <select name="type" class="form-select form-select-sm">
                            <option value="percent">Yüzde (%) İndirim</option>
                            <option value="fixed">Sabit Tutar (₺) İndirim</option>
                            <option value="free_shipping">Kargo Ücretsiz</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">İndirim Değeri (% veya ₺)</label>
                        <input type="number" step="0.01" name="value" class="form-control form-control-sm" value="10" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Min. Sepet Tutarı ₺</label>
                        <input type="number" step="0.01" name="min_cart_amount" class="form-control form-control-sm" value="200">
                    </div>
                    <div class="col-12 text-end mt-3">
                        <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold">
                            <i class="bi bi-plus-lg me-1"></i> Kuponu Oluştur
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Kupon Listesi -->
<div class="apple-card p-4">
    <h6 class="fw-bold mb-3 border-bottom pb-2">Tanımlı Kuponlar ve Kampanyalar (<?= count($campaigns) ?>)</h6>
    <div class="table-responsive">
        <table class="table table-hover align-middle small">
            <thead class="table-light">
                <tr>
                    <th>Kupon Başlığı</th>
                    <th>Kod</th>
                    <th>Tür</th>
                    <th>İndirim Değeri</th>
                    <th>Min. Sepet</th>
                    <th>Kullanım</th>
                    <th>Durum</th>
                    <th class="text-end">İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($campaigns as $camp): ?>
                    <tr>
                        <td class="fw-bold"><?= htmlspecialchars($camp['title']) ?></td>
                        <td><code><?= htmlspecialchars($camp['code']) ?></code></td>
                        <td>
                            <?php if ($camp['type'] === 'percent'): ?>
                                <span class="badge bg-info text-dark">Yüzde İndirimi</span>
                            <?php elseif ($camp['type'] === 'fixed'): ?>
                                <span class="badge bg-primary">Sabit Tutar</span>
                            <?php else: ?>
                                <span class="badge bg-success">Bedava Kargo</span>
                            <?php endif; ?>
                        </td>
                        <td class="fw-bold">
                            <?= $camp['type'] === 'percent' ? '%' . (int)$camp['value'] : Helper::formatPrice($camp['value']) ?>
                        </td>
                        <td><?= Helper::formatPrice($camp['min_cart_amount']) ?></td>
                        <td><?= $camp['usage_count'] ?> / <?= $camp['usage_limit'] ?></td>
                        <td><?= $camp['status'] ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Pasif</span>' ?></td>
                        <td class="text-end">
                            <a href="<?= SITE_URL ?>/admin/campaigns.php?action=delete&id=<?= $camp['id'] ?>" class="btn btn-sm btn-outline-danger py-0" onclick="return confirm('Bu kuponu silmek istediğinize emin misiniz?');">
                                Sil
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
