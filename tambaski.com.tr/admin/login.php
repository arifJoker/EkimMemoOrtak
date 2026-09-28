<?php
/**
 * TAMBASKI.COM.TR - Admin Güvenli Giriş Ekranı
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Varsayılan Admin Girişi veya DB kontrolü
    if (($username === 'admin' && $password === 'admin123') || ($username === 'arif' && $password === 'tambaski2026')) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $error = "Hatalı kullanıcı adı veya şifre girdiniz!";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Girişi – TamBaskı</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #ffffff;
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>

<div class="login-card text-center">
    <div class="mb-4">
        <img src="../assets/img/logo.svg" alt="TamBaskı" style="height: 48px;">
        <div class="mt-2 text-muted small fw-semibold">Yönetim & Kontrol Paneli</div>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger py-2 small rounded-3 mb-3">
            <i class="bi bi-exclamation-circle me-1"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="text-start">
        <div class="mb-3">
            <label class="form-label small fw-bold">Kullanıcı Adı</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                <input type="text" name="username" class="form-control form-control-lg" placeholder="admin" required autofocus>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label small fw-bold">Şifre</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                <input type="password" name="password" class="form-control form-control-lg" placeholder="••••••••" required>
            </div>
        </div>

        <button type="submit" class="btn btn-apple btn-apple-orange w-100 py-3 fw-bold fs-6 shadow">
            <i class="bi bi-box-arrow-in-right me-2"></i> Güvenli Giriş Yap
        </button>
    </form>

    <div class="mt-4 pt-3 border-top text-muted small">
        <a href="../index.php" class="text-decoration-none text-secondary">
            <i class="bi bi-arrow-left me-1"></i> Ana Siteye Dön
        </a>
    </div>
</div>

</body>
</html>
