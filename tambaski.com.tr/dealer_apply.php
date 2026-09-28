<?php
require_once __DIR__ . '/config/config.php';

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $companyName = trim($_POST['dealer_company'] ?? '');
    $taxNumber = trim($_POST['tax_number'] ?? '');
    $taxOffice = trim($_POST['tax_office'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $district = trim($_POST['district'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if (empty($fullName) || empty($email) || empty($password) || empty($companyName) || empty($taxNumber)) {
        $errorMsg = 'Lütfen tüm zorunlu alanları doldurunuz.';
    } else {
        $regData = [
            'full_name'      => $fullName,
            'email'          => $email,
            'password'       => $password,
            'phone'          => $phone,
            'role'           => 'dealer',
            'dealer_company' => $companyName,
            'tax_number'     => $taxNumber,
            'tax_office'     => $taxOffice,
            'city'           => $city,
            'district'       => $district,
            'address'        => $address
        ];

        $res = Auth::register($regData);
        if ($res['success']) {
            $successMsg = 'E-Bayi başvurunuz başarıyla alındı! Yönetici ekibimiz bilgilerinizi inceledikten sonra onay verecektir. Onaylandığında e-posta ile bilgilendirileceksiniz.';
        } else {
            $errorMsg = $res['error'] ?? 'Başvuru sırasında bir hata oluştu.';
        }
    }
}

$pageTitle = 'E-Bayi & B2B Başvuru Formu – TamBaskı';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="max-w-700 mx-auto">
        
        <div class="text-center mb-4">
            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-2">🤝 B2B Ortaklık</span>
            <h3 class="fw-bold">E-Bayi Başvuru Formu</h3>
            <p class="text-muted small">Reklam ajansları, matbaalar ve tasarımcılar için %25'e varan toptan iskonto tarifesi.</p>
        </div>

        <?php if (!empty($successMsg)): ?>
            <div class="alert alert-success rounded-4 p-4 shadow-sm text-center mb-4">
                <i class="bi bi-check-circle-fill fs-2 text-success d-block mb-2"></i>
                <h5 class="fw-bold">Başvurunuz Alındı</h5>
                <p class="small mb-3"><?= htmlspecialchars($successMsg) ?></p>
                <a href="<?= SITE_URL ?>/" class="btn btn-sm btn-apple btn-apple-pink">Ana Sayfaya Dön</a>
            </div>
        <?php else: ?>

            <?php if (!empty($errorMsg)): ?>
                <div class="alert alert-danger rounded-4 p-3 small shadow-sm mb-4">
                    <i class="bi bi-exclamation-octagon-fill me-2"></i><?= htmlspecialchars($errorMsg) ?>
                </div>
            <?php endif; ?>

            <div class="apple-card p-4 p-md-5">
                <form action="<?= SITE_URL ?>/dealer_apply.php" method="POST">
                    
                    <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary"><i class="bi bi-building me-1"></i> Firma & Yetkili Bilgileri</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Yetkili Ad Soyad *</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Firma Resmi Unvanı *</label>
                            <input type="text" name="dealer_company" class="form-control" placeholder="Örn: Atlas Reklam Ltd." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">E-Posta Adresi (Giriş İçin) *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Şifre Belirleyin *</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">İletişim Telefonu *</label>
                            <input type="tel" name="phone" class="form-control" placeholder="0555 123 45 67" required>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary"><i class="bi bi-file-earmark-text me-1"></i> Vergi & Adres Bilgileri</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Vergi Numarası / TC *</label>
                            <input type="text" name="tax_number" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Vergi Dairesi *</label>
                            <input type="text" name="tax_office" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">İl *</label>
                            <input type="text" name="city" class="form-control" placeholder="İstanbul" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">İlçe *</label>
                            <input type="text" name="district" class="form-control" placeholder="Şişli" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Firma Açık Adresi *</label>
                            <textarea name="address" class="form-control" rows="2" required></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn-apple btn-apple-pink w-100 py-3 fw-bold shadow">
                        <i class="bi bi-send-fill me-2"></i> E-Bayi Başvurusunu Gönder
                    </button>

                </form>
            </div>

        <?php endif; ?>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
