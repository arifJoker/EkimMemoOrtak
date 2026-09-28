<?php
/**
 * TAMBASKI.COM.TR - E-Bayi & B2B Başvuru Sayfası
 */
require_once __DIR__ . '/includes/functions.php';

$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company = trim($_POST['company_name'] ?? '');
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $tax_no = trim($_POST['tax_no'] ?? '');
    $tax_office = trim($_POST['tax_office'] ?? '');
    $sector = trim($_POST['sector'] ?? '');

    $db = getDB();
    if ($db) {
        try {
            $stmt = $db->prepare("INSERT INTO users (fullname, email, password, phone, company_name, tax_no, tax_office, user_type, is_approved) VALUES (?, ?, ?, ?, ?, ?, ?, 'dealer', 0)");
            $stmt->execute([$fullname, $email, password_hash('123456', PASSWORD_DEFAULT), $phone, $company, $tax_no, $tax_office]);
        } catch (Exception $e) {}
    }

    $success = true;
}

$page_title = "E-Bayi Başvurusu (%25 İskonto) – TamBaskı";
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="row align-items-center g-5">
        <div class="col-lg-6">
            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-2">🤝 B2B & Ajans Programı</span>
            <h2 class="fw-bold mb-3">TamBaskı E-Bayilik ile Kazancınızı Katlayın</h2>
            <p class="text-muted">
                Reklam ajansları, grafik tasarımcılar, matbaacılar ve kurumsal firmalar için özel toptan fiyatlandırma, faturasız/isimsiz kargo ve öncelikli üretim desteği.
            </p>

            <div class="row g-3 mt-2">
                <div class="col-sm-6">
                    <div class="p-3 bg-white rounded-3 border">
                        <i class="bi bi-percent text-danger fs-3 mb-1 d-block"></i>
                        <h6 class="fw-bold mb-1">%25 Sabit İskonto</h6>
                        <small class="text-muted">Tüm ofset, dijital ve pleksi kesimlerde bayi indirimi.</small>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="p-3 bg-white rounded-3 border">
                        <i class="bi bi-box-seam text-primary fs-3 mb-1 d-block"></i>
                        <h6 class="fw-bold mb-1">İsimsiz / Kör Kargo</h6>
                        <small class="text-muted">Müşterinize doğrudan sizin adınıza sevk edilir.</small>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="p-3 bg-white rounded-3 border">
                        <i class="bi bi-lightning-charge text-warning fs-3 mb-1 d-block"></i>
                        <h6 class="fw-bold mb-1">Öncelikli Üretim</h6>
                        <small class="text-muted">Siparişleriniz ekspres üretim bandına alınır.</small>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="p-3 bg-white rounded-3 border">
                        <i class="bi bi-headset text-success fs-3 mb-1 d-block"></i>
                        <h6 class="fw-bold mb-1">Özel Müşteri Temsilcisi</h6>
                        <small class="text-muted">Birebir telefon & WhatsApp teknik destek hattı.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="apple-card p-4 p-md-5">
                <?php if ($success): ?>
                    <div class="text-center py-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 70px;"></i>
                        <h4 class="fw-bold mt-3 mb-2">Başvurunuz Alındı!</h4>
                        <p class="text-muted small">E-Bayilik başvurunuz müşteri temsilcimiz tarafından incelenip en kısa sürede telefon ile onaylanacaktır.</p>
                        <a href="index.php" class="btn btn-apple btn-apple-orange mt-2">Anasayfaya Dön</a>
                    </div>
                <?php else: ?>
                    <h4 class="fw-bold mb-3">E-Bayi Başvuru Formu</h4>
                    <p class="text-muted small mb-4">Lütfen firma veya ajans bilgilerinizi eksiksiz doldurunuz.</p>

                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold">Firma / Ajans Adı</label>
                                <input type="text" name="company_name" class="form-control" required placeholder="Örn: ABC Reklam Ajansı">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Yetkili Ad Soyad</label>
                                <input type="text" name="fullname" class="form-control" required placeholder="Adınız Soyadınız">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Telefon Numarası</label>
                                <input type="tel" name="phone" class="form-control" required placeholder="05XX XXX XX XX">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">E-Posta Adresi</label>
                                <input type="email" name="email" class="form-control" required placeholder="ajans@alanadi.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Vergi Dairesi</label>
                                <input type="text" name="tax_office" class="form-control" placeholder="Kadıköy V.D.">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Vergi Numarası / T.C.</label>
                                <input type="text" name="tax_no" class="form-control" placeholder="1234567890">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-apple btn-apple-orange w-100 py-3 fw-bold mt-2">
                                    <i class="bi bi-award-fill me-2"></i> Başvuruyu Gönder
                                </button>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
