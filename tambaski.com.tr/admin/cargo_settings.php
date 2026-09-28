<?php
require_once __DIR__ . '/../config/config.php';
Auth::requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Helper::saveSetting('cargo_default_company', trim($_POST['cargo_default_company'] ?? 'Yurtiçi Kargo'));
    Helper::saveSetting('cargo_customer_code', trim($_POST['cargo_customer_code'] ?? ''));
    Helper::saveSetting('cargo_api_key', trim($_POST['cargo_api_key'] ?? ''));
    Helper::saveSetting('cargo_api_url', trim($_POST['cargo_api_url'] ?? ''));

    Helper::setFlash('success', 'Kargo anlaşma bilgileri başarıyla kaydedildi.');
    header("Location: " . SITE_URL . "/admin/cargo_settings.php");
    exit;
}

$pageTitle = 'Kargo Anlaşmaları & Entegrasyon Ayarları';
require_once __DIR__ . '/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-truck text-primary me-2"></i>Anlaşmalı Kargo & Takip Ayarları</h4>
        <p class="text-muted small mb-0">Siparişler kargolandığında otomatik takip linki oluşturulması için kargo firmanızı belirleyin.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="apple-card p-4">
            <h6 class="fw-bold mb-3 border-bottom pb-2">Kargo Firma Tercihi & Anlaşma Bilgileri</h6>
            
            <form action="<?= SITE_URL ?>/admin/cargo_settings.php" method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Varsayılan Anlaşmalı Kargo Firması *</label>
                    <select name="cargo_default_company" class="form-select" required>
                        <?php foreach (Cargo::getCompanies() as $key => $c): ?>
                            <option value="<?= $key ?>" <?= (Helper::getSetting('cargo_default_company', 'Yurtiçi Kargo') === $key) ? 'selected' : '' ?>>
                                <?= $c['name'] ?> (Otomatik Takip Linki Entegre)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Kargo Müşteri / Anlaşma Numarası</label>
                    <input type="text" name="cargo_customer_code" class="form-control" value="<?= htmlspecialchars(Helper::getSetting('cargo_customer_code', '')) ?>" placeholder="Örn: 987654321">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Web Servis API Anahtarı (Opsiyonel Barkod Entegrasyonu)</label>
                    <input type="text" name="cargo_api_key" class="form-control" value="<?= htmlspecialchars(Helper::getSetting('cargo_api_key', '')) ?>">
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold">Kargo Web Servis Endpoint URL (Opsiyonel)</label>
                    <input type="text" name="cargo_api_url" class="form-control" value="<?= htmlspecialchars(Helper::getSetting('cargo_api_url', '')) ?>">
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                    <i class="bi bi-save me-1"></i> Kargo Ayarlarını Kaydet
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="apple-card p-4">
            <h6 class="fw-bold mb-3 border-bottom pb-2">Desteklenen Kargo Takip Sistemleri</h6>
            <div class="d-flex flex-column gap-2 small">
                <?php foreach (Cargo::getCompanies() as $key => $c): ?>
                    <div class="p-2 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                        <span class="fw-semibold"><i class="bi bi-truck me-2 text-primary"></i><?= $c['name'] ?></span>
                        <span class="badge bg-success-subtle text-success">Otomatik Link Aktif</span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-3 p-3 bg-white rounded border small text-muted">
                <i class="bi bi-info-circle text-primary me-1"></i> Sipariş detayından kargo takip numarasını girdiğinizde müşteri panelinde ve kargo takip sayfasında otomatik olarak kargo firmasının resmi takip sistemine yönlendiren buton açılır.
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
