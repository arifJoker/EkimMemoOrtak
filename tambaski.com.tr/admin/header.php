<?php
/**
 * TAMBASKI.COM.TR - Admin Paneli Header
 */
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Yönetim Paneli') ?> – TamBaskı</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<!-- Admin Top Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark py-3 px-4 shadow-sm sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
            <img src="../assets/img/logo.svg" alt="TamBaskı" style="height: 32px; filter: brightness(0) invert(1);">
            <span class="badge bg-danger rounded-pill px-2 py-1 small">Admin</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 gap-1">
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'index.php' ? 'active fw-bold text-white' : '' ?>" href="index.php">
                        <i class="bi bi-speedometer2 me-1"></i> Siparişler & Özet
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'campaigns.php' ? 'active fw-bold text-white' : '' ?>" href="campaigns.php">
                        <i class="bi bi-percent me-1"></i> Kampanya Kurguları
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'settings.php' ? 'active fw-bold text-white' : '' ?>" href="settings.php">
                        <i class="bi bi-gear-fill me-1"></i> İletişim & Site Ayarları
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../index.php" target="_blank">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Mağazayı Gör
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <span class="text-white-50 small"><i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['admin_user'] ?? 'Yönetici') ?></span>
                <a href="logout.php" class="btn btn-sm btn-outline-danger px-3 rounded-pill" onclick="return confirm('Çıkış yapmak istediğinize emin misiniz?');">
                    <i class="bi bi-box-arrow-right me-1"></i> Çıkış Yap
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid py-4 px-4">
