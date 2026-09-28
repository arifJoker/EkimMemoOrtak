<?php
require_once __DIR__ . '/config/config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');

    if (empty($fullName) || empty($email) || empty($password)) {
        $error = 'Lütfen tüm alanları doldurunuz.';
    } else {
        $res = Auth::register([
            'full_name' => $fullName,
            'email'     => $email,
            'password'  => $password,
            'phone'     => $phone,
            'role'      => 'customer'
        ]);

        if ($res['success']) {
            Helper::setFlash('success', 'Hesabınız başarıyla oluşturuldu!');
            header("Location: " . SITE_URL . "/account.php");
            exit;
        } else {
            $error = $res['error'] ?? 'Kayıt yapılamadı.';
        }
    }
}

$pageTitle = 'Yeni Üye Kaydı – TamBaskı';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="max-w-450 mx-auto">
        
        <div class="apple-card p-4 p-md-5">
            <div class="text-center mb-4">
                <h4 class="fw-bold mb-1">Yeni Hesap Oluşturun</h4>
                <p class="text-muted small">Hızlı sipariş verin, tasarımlarınızı kaydedin</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger rounded-3 p-2 small mb-3">
                    <i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="<?= SITE_URL ?>/register.php" method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Ad Soyad</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">E-Posta Adresi</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Cep Telefonu</label>
                    <input type="tel" name="phone" class="form-control" placeholder="0555 123 45 67">
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold">Şifre Belirleyin</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn-apple btn-apple-pink w-100 py-2 fw-bold">
                    Kayıt Ol ve Başla
                </button>
            </form>

            <div class="text-center mt-4 small text-muted">
                Zaten hesabınız var mı? <a href="<?= SITE_URL ?>/login.php" class="text-primary fw-bold text-decoration-none">Giriş Yapın</a>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
