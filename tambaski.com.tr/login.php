<?php
require_once __DIR__ . '/config/config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $res = Auth::login($email, $password);
    if ($res['success']) {
        $redirect = $_GET['redirect'] ?? (Auth::isAdmin() ? SITE_URL . '/admin/' : SITE_URL . '/account.php');
        header("Location: " . $redirect);
        exit;
    } else {
        $error = $res['error'] ?? 'Giriş yapılamadı.';
    }
}

$pageTitle = 'Üye Girişi – TamBaskı';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="max-w-450 mx-auto">
        
        <div class="apple-card p-4 p-md-5">
            <div class="text-center mb-4">
                <h4 class="fw-bold mb-1">Üye Girişi</h4>
                <p class="text-muted small">Siparişlerinizi ve tasarımlarınızı takip edin</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger rounded-3 p-2 small mb-3">
                    <i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="<?= SITE_URL ?>/login.php<?= !empty($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : '' ?>" method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold">E-Posta Adresi</label>
                    <input type="email" name="email" class="form-control" placeholder="adiniz@ornek.com" required>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold">Şifre</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-apple btn-apple-pink w-100 py-2 fw-bold">
                    Giriş Yap
                </button>
            </form>

            <div class="text-center mt-4 small text-muted">
                Hesabınız yok mu? <a href="<?= SITE_URL ?>/register.php" class="text-primary fw-bold text-decoration-none">Hemen Kayıt Olun</a>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
