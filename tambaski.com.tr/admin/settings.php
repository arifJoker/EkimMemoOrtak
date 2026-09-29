<?php
/**
 * TAMBASKI.COM.TR - Admin İletişim, Şirket & Site Ayarları Modülü
 */
require_once __DIR__ . '/../config/config.php';
Auth::requireDesignPermission();
$page_title = "İletişim & Genel Site Ayarları";
require_once __DIR__ . '/header.php';

$success_msg = null;

// Mock / Default Ayarlar
if (!isset($_SESSION['site_settings'])) {
    $_SESSION['site_settings'] = [
        'company_title' => 'TamBaskı Matbaa & Reklam San. Tic. Ltd. Şti.',
        'phone' => '0850 308 00 00',
        'whatsapp' => '0544 000 00 00',
        'email_support' => 'destek@tambaski.com.tr',
        'email_accounting' => 'muhasebe@tambaski.com.tr',
        'address' => 'İkitelli OSB Mah. Matbaacılar Sitesi 4. Cadde No: 42 Başakşehir / İstanbul',
        'working_hours' => 'Hafta İçi: 08:30 - 18:30 | Cumartesi: 09:00 - 14:00',
        'maps_embed' => '<iframe src="https://www.google.com/maps/embed" width="100%" height="200" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
        'free_shipping_limit' => 750.00,
        'standard_shipping_fee' => 75.00,
        'paytr_merchant_id' => '123456',
        'paytr_merchant_key' => 'key_sample_test',
        'paytr_merchant_salt' => 'salt_sample_test',
        'bank_name' => 'Garanti BBVA',
        'bank_iban' => 'TR92 0006 2000 0001 2345 6789 01',
        'bank_account_holder' => 'TamBaskı Matbaa Ltd. Şti.',
        'social_instagram' => 'https://instagram.com/tambaski',
        'social_facebook' => 'https://facebook.com/tambaski',
        'social_linkedin' => 'https://linkedin.com/company/tambaski',
        'analytics_code' => 'G-XXXXXXXXXX'
    ];
}

// Ayarları Kaydetme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_settings') {
    foreach ($_SESSION['site_settings'] as $key => $val) {
        if (isset($_POST[$key])) {
            $_SESSION['site_settings'][$key] = trim($_POST[$key]);
        }
    }
    $success_msg = "Tüm iletişim ve sistem ayarları başarıyla güncellendi!";
}

$s = $_SESSION['site_settings'];
?>

<div class="row g-4">
    <div class="col-12">
        <h3 class="fw-bold mb-1"><i class="bi bi-gear-fill text-primary me-2"></i>İletişim & Genel Site Ayarları</h3>
        <p class="text-muted small mb-0">Müşteri iletişim kanalları, şirket bilgileri, ödeme ve kargo entegrasyon ayarları</p>
    </div>

    <?php if ($success_msg): ?>
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($success_msg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <div class="col-12">
        <form method="POST">
            <input type="hidden" name="action" value="save_settings">

            <div class="row g-4">
                <!-- 1. İLETİŞİM & LOKASYON BİLGİLERİ -->
                <div class="col-lg-6">
                    <div class="apple-card p-4 bg-white h-100">
                        <h5 class="fw-bold mb-3"><i class="bi bi-telephone-inbound text-primary me-2"></i>İletişim & Lokasyon</h5>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Şirket / Ticari Ünvan</label>
                            <input type="text" name="company_title" class="form-control" value="<?= htmlspecialchars($s['company_title']) ?>" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Müşteri Hizmetleri (0850 / Sabit)</label>
                                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($s['phone']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">WhatsApp Sipariş & Destek Hattı</label>
                                <input type="text" name="whatsapp" class="form-control" value="<?= htmlspecialchars($s['whatsapp']) ?>" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Müşteri Destek E-Postası</label>
                                <input type="email" name="email_support" class="form-control" value="<?= htmlspecialchars($s['email_support']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Muhasebe & Fatura E-Postası</label>
                                <input type="email" name="email_accounting" class="form-control" value="<?= htmlspecialchars($s['email_accounting']) ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Fabrika / Atölye / Mağaza Açık Adresi</label>
                            <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($s['address']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Çalışma ve Üretim Saatleri</label>
                            <input type="text" name="working_hours" class="form-control" value="<?= htmlspecialchars($s['working_hours']) ?>">
                        </div>

                        <div class="mb-0">
                            <label class="form-label small fw-bold">Google Maps Embed Harita Kodu (Iframe)</label>
                            <textarea name="maps_embed" class="form-control font-monospace" rows="2" style="font-size: 11px;"><?= htmlspecialchars($s['maps_embed']) ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- 2. KARGO, ÖDEME & SOSYAL MEDYA -->
                <div class="col-lg-6">
                    <div class="apple-card p-4 bg-white mb-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-truck text-success me-2"></i>Kargo & Ödeme Entegrasyonları</h5>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Ücretsiz Kargo Sepet Alt Limiti (₺)</label>
                                <input type="number" name="free_shipping_limit" class="form-control" value="<?= htmlspecialchars($s['free_shipping_limit']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Standart Kargo Bedeli (₺)</label>
                                <input type="number" name="standard_shipping_fee" class="form-control" value="<?= htmlspecialchars($s['standard_shipping_fee']) ?>">
                            </div>
                        </div>

                        <hr class="my-3">
                        <h6 class="fw-bold mb-2"><i class="bi bi-bank me-1 text-primary"></i>Banka Havale / EFT Bilgileri</h6>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Banka Adı</label>
                                <input type="text" name="bank_name" class="form-control" value="<?= htmlspecialchars($s['bank_name']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Hesap Sahibi</label>
                                <input type="text" name="bank_account_holder" class="form-control" value="<?= htmlspecialchars($s['bank_account_holder']) ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">IBAN Numarası</label>
                                <input type="text" name="bank_iban" class="form-control font-monospace fw-bold" value="<?= htmlspecialchars($s['bank_iban']) ?>">
                            </div>
                        </div>

                        <hr class="my-3">
                        <h6 class="fw-bold mb-2"><i class="bi bi-credit-card-2-front me-1 text-danger"></i>PayTR Sanal POS Bilgileri</h6>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Mağaza No (ID)</label>
                                <input type="text" name="paytr_merchant_id" class="form-control" value="<?= htmlspecialchars($s['paytr_merchant_id']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Merchant Key</label>
                                <input type="password" name="paytr_merchant_key" class="form-control" value="<?= htmlspecialchars($s['paytr_merchant_key']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Merchant Salt</label>
                                <input type="password" name="paytr_merchant_salt" class="form-control" value="<?= htmlspecialchars($s['paytr_merchant_salt']) ?>">
                            </div>
                        </div>
                    </div>

                    <!-- 3. SOSYAL MEDYA & TAKİP KODLARI -->
                    <div class="apple-card p-4 bg-white">
                        <h5 class="fw-bold mb-3"><i class="bi bi-share text-warning me-2"></i>Sosyal Medya & Analitik</h5>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold"><i class="bi bi-instagram text-danger me-1"></i>Instagram</label>
                                <input type="text" name="social_instagram" class="form-control" value="<?= htmlspecialchars($s['social_instagram']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold"><i class="bi bi-facebook text-primary me-1"></i>Facebook</label>
                                <input type="text" name="social_facebook" class="form-control" value="<?= htmlspecialchars($s['social_facebook']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold"><i class="bi bi-linkedin text-info me-1"></i>LinkedIn</label>
                                <input type="text" name="social_linkedin" class="form-control" value="<?= htmlspecialchars($s['social_linkedin']) ?>">
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label small fw-bold">Google Analytics Ölçüm Kimliği (Measurement ID)</label>
                            <input type="text" name="analytics_code" class="form-control" value="<?= htmlspecialchars($s['analytics_code']) ?>">
                        </div>
                    </div>
                </div>

                <!-- KAYDET BUTONU -->
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-apple btn-apple-orange btn-lg px-5 py-3 fw-bold shadow">
                        <i class="bi bi-check2-circle me-2"></i> Tüm Ayarları Kaydet
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
