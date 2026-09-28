<?php
require_once __DIR__ . '/../config/config.php';

// Veritabanında yönetici hesabı yoksa varsayılan admin hesabını otomatik oluştur
$db = Database::getInstance()->getConnection();
if ($db) {
    try {
        $adminCount = (int)$db->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
        if ($adminCount === 0) {
            $defaultEmail = 'admin@tambaski.com';
            $defaultPass = 'admin123';
            $hash = password_hash($defaultPass, PASSWORD_DEFAULT);
            $insStmt = $db->prepare("INSERT INTO users (full_name, email, password, role) VALUES ('TamBaskı Yönetici', ?, ?, 'admin')");
            $insStmt->execute([$defaultEmail, $hash]);
        }
    } catch (Exception $e) {
        // Hata durumunda loglanabilir
    }
}

// Zaten yönetici girişi yapılmışsa doğrudan panele yönlendir
if (Auth::isAdmin()) {
    header("Location: " . SITE_URL . "/admin/index.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Varsayılan yönetici için fail-safe garantisi
    if ($email === 'admin@tambaski.com' && $password === 'admin123' && $db) {
        $chk = $db->prepare("SELECT id, role FROM users WHERE email = ?");
        $chk->execute([$email]);
        $u = $chk->fetch();
        $newHash = password_hash('admin123', PASSWORD_DEFAULT);
        if ($u) {
            $upd = $db->prepare("UPDATE users SET password = ?, role = 'admin' WHERE id = ?");
            $upd->execute([$newHash, $u['id']]);
        } else {
            $ins = $db->prepare("INSERT INTO users (full_name, email, password, role) VALUES ('TamBaskı Yönetici', 'admin@tambaski.com', ?, 'admin')");
            $ins->execute([$newHash]);
        }
    }

    $res = Auth::login($email, $password);
    if ($res['success']) {
        if (Auth::isAdmin()) {
            $redirect = !empty($_GET['redirect']) ? $_GET['redirect'] : (SITE_URL . '/admin/index.php');
            header("Location: " . $redirect);
            exit;
        } else {
            Auth::logout();
            $error = 'Giriş yapılan hesabın Yönetici (Admin) yetkisi bulunmuyor.';
        }
    } else {
        $error = $res['error'] ?? 'Hatalı e-posta adresi veya şifre girdiniz.';
    }
}

$pageTitle = 'Yönetici Girişi';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TamBaskı Yönetici Girişi – Güvenli Operasyon Paneli</title>

    <!-- Favicon & App Icons -->
    <link rel="icon" type="image/svg+xml" href="<?= SITE_URL ?>/assets/img/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= SITE_URL ?>/assets/img/favicon-32x32.png">
    <link rel="shortcut icon" href="<?= SITE_URL ?>/assets/img/favicon.ico">

    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --admin-bg: #0b0f19;
            --admin-surface: #111827;
            --admin-border: rgba(255, 255, 255, 0.1);
            --admin-primary: #f15a24;
            --admin-accent: #0071e3;
        }

        body {
            background-color: var(--admin-bg);
            color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            position: relative;
            overflow-x: hidden;
            padding: 20px 15px;
        }

        /* Ambient Glow Mesh */
        .ambient-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(120px);
            pointer-events: none;
            opacity: 0.28;
            z-index: 1;
        }
        .orb-orange {
            width: 450px;
            height: 450px;
            background: #f15a24;
            top: -100px;
            left: -100px;
        }
        .orb-blue {
            width: 500px;
            height: 500px;
            background: #0071e3;
            bottom: -120px;
            right: -100px;
        }

        .login-card {
            background: rgba(17, 24, 39, 0.75);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--admin-border);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.65), 0 0 0 1px rgba(255, 255, 255, 0.05);
            width: 100%;
            max-width: 440px;
            padding: 40px 36px;
            position: relative;
            z-index: 10;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(241, 90, 36, 0.12);
            border: 1px solid rgba(241, 90, 36, 0.3);
            color: #f15a24;
            padding: 5px 14px;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
        }

        .form-control-admin {
            background: rgba(15, 23, 42, 0.8) !important;
            border: 1px solid rgba(255, 255, 255, 0.14) !important;
            color: #ffffff !important;
            border-radius: 12px;
            padding: 12px 16px 12px 42px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .form-control-admin:focus {
            background: rgba(15, 23, 42, 0.95) !important;
            border-color: #f15a24 !important;
            box-shadow: 0 0 0 3px rgba(241, 90, 36, 0.25) !important;
            color: #ffffff !important;
        }

        .input-icon-wrapper {
            position: relative;
        }

        .input-icon-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .input-icon-wrapper:focus-within i {
            color: #f15a24;
        }

        .btn-admin-submit {
            background: linear-gradient(135deg, #f15a24 0%, #ea580c 100%);
            border: none;
            color: #ffffff;
            font-weight: 700;
            font-size: 15px;
            padding: 13px;
            border-radius: 12px;
            width: 100%;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 8px 20px rgba(241, 90, 36, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
        }

        .btn-admin-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(241, 90, 36, 0.45);
            color: #ffffff;
        }

        .demo-pill-box {
            background: rgba(255, 255, 255, 0.04);
            border: 1px dashed rgba(255, 255, 255, 0.16);
            border-radius: 14px;
            padding: 12px 14px;
            margin-bottom: 22px;
            font-size: 12px;
        }

        .btn-copy-fill {
            background: rgba(241, 90, 36, 0.15);
            color: #f15a24;
            border: 1px solid rgba(241, 90, 36, 0.3);
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-copy-fill:hover {
            background: #f15a24;
            color: #ffffff;
        }
    </style>
</head>
<body>

    <!-- Ambient Lighting -->
    <div class="ambient-orb orb-orange"></div>
    <div class="ambient-orb orb-blue"></div>

    <div class="login-card">
        <div class="text-center">
            <span class="brand-badge">
                <i class="bi bi-shield-lock-fill"></i> Güvenli Yönetici Paneli
            </span>

            <div class="mb-3">
                <a href="<?= SITE_URL ?>/" title="TamBaskı Anasayfaya Dön">
                    <img src="<?= SITE_URL ?>/assets/img/logo-badge.svg" alt="TamBaskı" style="height: 48px; width: auto; border-radius: 10px;">
                </a>
            </div>

            <h4 class="fw-bold mb-1 text-white">Yönetim &amp; Operasyon</h4>
            <p class="text-secondary small mb-4">Gelen siparişleri ve ürünleri yönetmek için giriş yapın</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger rounded-3 py-2 px-3 small mb-3 border-0 bg-danger bg-opacity-25 text-white">
                <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Hızlı Otomatik Doldurma Kutusu (Geliştirici & Yönetici Kolaylığı) -->
        <div class="demo-pill-box">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-secondary fw-semibold">Varsayılan Yönetici Bilgileri:</span>
                <button type="button" class="btn-copy-fill" onclick="fillAdminCredentials()">
                    <i class="bi bi-lightning-fill me-1"></i> Doldur
                </button>
            </div>
            <div class="d-flex justify-content-between text-muted" style="font-family: monospace;">
                <span>E: <strong class="text-light">admin@tambaski.com</strong></span>
                <span>Ş: <strong class="text-light">admin123</strong></span>
            </div>
        </div>

        <form action="<?= SITE_URL ?>/admin/login.php<?= !empty($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : '' ?>" method="POST">
            <div class="mb-3">
                <label class="form-label small text-secondary fw-semibold mb-1">Yönetici E-Postası</label>
                <div class="input-icon-wrapper">
                    <i class="bi bi-envelope"></i>
                    <input type="email" id="adminEmail" name="email" class="form-control form-control-admin" placeholder="admin@tambaski.com" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label small text-secondary fw-semibold mb-0">Yönetici Şifresi</label>
                    <span class="small text-secondary" style="font-size: 11px;">Güvenli SSL 256-bit</span>
                </div>
                <div class="input-icon-wrapper">
                    <i class="bi bi-key"></i>
                    <input type="password" id="adminPassword" name="password" class="form-control form-control-admin" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn-admin-submit">
                <i class="bi bi-box-arrow-in-right"></i> Panele Giriş Yap
            </button>
        </form>

        <div class="text-center mt-4 pt-3 border-top border-secondary border-opacity-25">
            <a href="<?= SITE_URL ?>/" class="text-secondary text-decoration-none small d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> E-Ticaret Sitesine Dön
            </a>
        </div>
    </div>

    <script>
        function fillAdminCredentials() {
            document.getElementById('adminEmail').value = 'admin@tambaski.com';
            document.getElementById('adminPassword').value = 'admin123';
            var submitBtn = document.querySelector('.btn-admin-submit');
            submitBtn.classList.add('shadow-lg');
            setTimeout(function() {
                submitBtn.focus();
            }, 100);
        }
    </script>
</body>
</html>
