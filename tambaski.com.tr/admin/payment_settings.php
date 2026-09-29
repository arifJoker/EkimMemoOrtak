<?php
require_once __DIR__ . '/../config/config.php';
Auth::requireDesignPermission();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // PayTR Ayarları
    Helper::saveSetting('paytr_active', !empty($_POST['paytr_active']) ? '1' : '0');
    Helper::saveSetting('paytr_merchant_id', trim($_POST['paytr_merchant_id'] ?? ''));
    Helper::saveSetting('paytr_merchant_key', trim($_POST['paytr_merchant_key'] ?? ''));
    Helper::saveSetting('paytr_merchant_salt', trim($_POST['paytr_merchant_salt'] ?? ''));
    Helper::saveSetting('paytr_test_mode', !empty($_POST['paytr_test_mode']) ? '1' : '0');

    // iyzico Ayarları
    Helper::saveSetting('iyzico_active', !empty($_POST['iyzico_active']) ? '1' : '0');
    Helper::saveSetting('iyzico_api_key', trim($_POST['iyzico_api_key'] ?? ''));
    Helper::saveSetting('iyzico_secret_key', trim($_POST['iyzico_secret_key'] ?? ''));
    Helper::saveSetting('iyzico_base_url', trim($_POST['iyzico_base_url'] ?? 'https://sandbox-api.iyzipay.com'));

    Helper::setFlash('success', 'PayTR ve iyzico ödeme ayarları başarıyla kaydedildi.');
    header("Location: " . SITE_URL . "/admin/payment_settings.php");
    exit;
}

$pageTitle = 'PayTR & iyzico Ödeme Ayarları';
require_once __DIR__ . '/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-credit-card text-primary me-2"></i>PayTR & iyzico Sanal POS Yapılandırması</h4>
        <p class="text-muted small mb-0">Müşterilerinizin güvenle 3D Secure kredi kartı ve taksitli ödeme yapabilmesi için API bilgilerinizi girin.</p>
    </div>
</div>

<form action="<?= SITE_URL ?>/admin/payment_settings.php" method="POST">
    
    <div class="row g-4">
        
        <!-- PayTR Kartı -->
        <div class="col-lg-6">
            <div class="apple-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-dark fs-6 px-3 py-2">PayTR</span>
                        <h5 class="fw-bold mb-0">PayTR iFrame POS</h5>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="paytr_active" id="paytrActive" value="1" <?= Helper::getSetting('paytr_active', '1') == '1' ? 'checked' : '' ?>>
                        <label class="form-check-label small fw-bold" for="paytrActive">Aktif</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Mağaza Numarası (Merchant ID) *</label>
                    <input type="text" name="paytr_merchant_id" class="form-control" value="<?= htmlspecialchars(Helper::getSetting('paytr_merchant_id', '')) ?>" placeholder="Örn: 123456">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Mağaza Anahtarı (Merchant Key) *</label>
                    <input type="text" name="paytr_merchant_key" class="form-control" value="<?= htmlspecialchars(Helper::getSetting('paytr_merchant_key', '')) ?>" placeholder="Örn: aBcDeFgHiJkLmNoP">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Mağaza Gizli Anahtarı (Merchant Salt) *</label>
                    <input type="text" name="paytr_merchant_salt" class="form-control" value="<?= htmlspecialchars(Helper::getSetting('paytr_merchant_salt', '')) ?>" placeholder="Örn: xYz123456789">
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="paytr_test_mode" id="paytrTest" value="1" <?= Helper::getSetting('paytr_test_mode', '1') == '1' ? 'checked' : '' ?>>
                        <label class="form-check-label small fw-bold text-danger" for="paytrTest">Test / Sandbox Modu (Canlıya geçildiğinde kapatın)</label>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-3 small text-muted border">
                    <strong>PayTR Bildirim URL (Webhook):</strong><br>
                    <code><?= SITE_URL ?>/api/paytr_callback.php</code>
                    <div class="mt-1" style="font-size: 11px;">PayTR panelinizden "Bildirim URL" kısmına bu adresi tanımlayın.</div>
                </div>
            </div>
        </div>

        <!-- iyzico Kartı -->
        <div class="col-lg-6">
            <div class="apple-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary fs-6 px-3 py-2">iyzico</span>
                        <h5 class="fw-bold mb-0">iyzico Checkout Form</h5>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="iyzico_active" id="iyziActive" value="1" <?= Helper::getSetting('iyzico_active', '1') == '1' ? 'checked' : '' ?>>
                        <label class="form-check-label small fw-bold" for="iyziActive">Aktif</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">API Key (API Anahtarı) *</label>
                    <input type="text" name="iyzico_api_key" class="form-control" value="<?= htmlspecialchars(Helper::getSetting('iyzico_api_key', '')) ?>" placeholder="sandbox-... veya live-...">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Secret Key (Güvenlik Anahtarı) *</label>
                    <input type="text" name="iyzico_secret_key" class="form-control" value="<?= htmlspecialchars(Helper::getSetting('iyzico_secret_key', '')) ?>" placeholder="sandbox-... veya live-...">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">API Base URL</label>
                    <select name="iyzico_base_url" class="form-select">
                        <option value="https://sandbox-api.iyzipay.com" <?= Helper::getSetting('iyzico_base_url') === 'https://sandbox-api.iyzipay.com' ? 'selected' : '' ?>>Test / Sandbox (https://sandbox-api.iyzipay.com)</option>
                        <option value="https://api.iyzipay.com" <?= Helper::getSetting('iyzico_base_url') === 'https://api.iyzipay.com' ? 'selected' : '' ?>>Canlı / Production (https://api.iyzipay.com)</option>
                    </select>
                </div>

                <div class="p-3 bg-light rounded-3 small text-muted border mt-4">
                    <strong>iyzico 3D Dönüş URL:</strong><br>
                    <code><?= SITE_URL ?>/api/iyzico_callback.php</code>
                </div>
            </div>
        </div>

        <div class="col-12 text-end">
            <button type="submit" class="btn btn-primary px-5 py-3 fw-bold shadow">
                <i class="bi bi-save me-1"></i> Tüm Ödeme Ayarlarını Kaydet
            </button>
        </div>

    </div>

</form>

<?php require_once __DIR__ . '/footer.php'; ?>
