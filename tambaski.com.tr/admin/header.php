<?php
/**
 * TAMBASKI.COM.TR - Admin Paneli Header (Sol Sabit Sidebar Menü Mimarisi)
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
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #111113;
            --sidebar-hover: rgba(255, 255, 255, 0.08);
            --sidebar-active: #f15a24;
        }
        body {
            background-color: #f8fafc;
            min-height: 100vh;
        }
        .admin-wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }
        .admin-sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: #94a3b8;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1040;
            overflow-y: auto;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s ease;
        }
        .sidebar-brand {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .sidebar-menu {
            padding: 16px 12px;
            list-style: none;
            margin: 0;
            flex-grow: 1;
        }
        .sidebar-heading {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 700;
            color: #64748b;
            padding: 12px 14px 6px 14px;
        }
        .sidebar-item {
            margin-bottom: 4px;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 12px;
            color: #cbd5e1;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .sidebar-link:hover {
            background: var(--sidebar-hover);
            color: #ffffff;
        }
        .sidebar-link.active {
            background: var(--sidebar-active);
            color: #ffffff;
            font-weight: 600;
            box-shadow: 0 4px 14px rgba(241, 90, 36, 0.35);
        }
        .sidebar-link i {
            font-size: 18px;
        }
        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(0, 0, 0, 0.2);
        }
        .admin-main-content {
            flex-grow: 1;
            margin-left: var(--sidebar-width);
            min-width: 0;
            display: flex;
            flex-direction: column;
        }
        .admin-topbar {
            background: #ffffff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            padding: 14px 28px;
            position: sticky;
            top: 0;
            z-index: 1030;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        @media (max-width: 991px) {
            .admin-sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            .admin-sidebar.show {
                margin-left: 0;
            }
            .admin-main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

<div class="admin-wrapper">
    <!-- SOL SABİT SİDEBAR MENÜ -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand">
            <a href="index.php" class="d-flex align-items-center gap-2 text-decoration-none">
                <img src="../assets/img/logo.svg" alt="TamBaskı" style="height: 32px; filter: brightness(0) invert(1);">
            </a>
            <span class="badge bg-danger rounded-pill px-2 py-1" style="font-size: 10px;">V2.0</span>
        </div>

        <ul class="sidebar-menu">
            <li class="sidebar-heading">Genel Panel</li>
            <li class="sidebar-item">
                <a href="index.php" class="sidebar-link <?= $current_page === 'index.php' ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span>Siparişler & Özet</span>
                </a>
            </li>

            <li class="sidebar-heading">Ürün & Fiyat Yönetimi</li>
            <li class="sidebar-item">
                <a href="products.php" class="sidebar-link <?= $current_page === 'products.php' ? 'active' : '' ?>">
                    <i class="bi bi-box-seam"></i>
                    <span>Tüm Ürünler</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="product_add.php" class="sidebar-link <?= $current_page === 'product_add.php' ? 'active' : '' ?>">
                    <i class="bi bi-plus-circle-fill text-warning"></i>
                    <span>Yeni Ürün Ekle</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="categories.php" class="sidebar-link <?= $current_page === 'categories.php' ? 'active' : '' ?>">
                    <i class="bi bi-grid-fill"></i>
                    <span>Kategoriler</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="mockups.php" class="sidebar-link <?= $current_page === 'mockups.php' ? 'active' : '' ?>">
                    <i class="bi bi-layers-half text-info"></i>
                    <span>Mockup & Şablonlar</span>
                </a>
            </li>

            <li class="sidebar-heading">Pazarlama & B2B</li>
            <li class="sidebar-item">
                <a href="campaigns.php" class="sidebar-link <?= $current_page === 'campaigns.php' ? 'active' : '' ?>">
                    <i class="bi bi-percent"></i>
                    <span>Kampanya & Kuponlar</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="dealers.php" class="sidebar-link <?= $current_page === 'dealers.php' ? 'active' : '' ?>">
                    <i class="bi bi-briefcase-fill text-success"></i>
                    <span>E-Bayi Yönetimi</span>
                </a>
            </li>

            <li class="sidebar-heading">Sistem & Yapılandırma</li>
            <li class="sidebar-item">
                <a href="settings.php" class="sidebar-link <?= $current_page === 'settings.php' ? 'active' : '' ?>">
                    <i class="bi bi-gear-fill"></i>
                    <span>İletişim & Ayarlar</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="../index.php" target="_blank" class="sidebar-link">
                    <i class="bi bi-box-arrow-up-right"></i>
                    <span>Mağazayı Gör</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div class="small">
                    <div class="text-white fw-semibold"><?= htmlspecialchars($_SESSION['admin_user'] ?? 'Yönetici') ?></div>
                    <div class="text-muted" style="font-size: 11px;">Süper Admin</div>
                </div>
            </div>
            <a href="logout.php" class="text-danger fs-5" title="Çıkış Yap" onclick="return confirm('Çıkış yapmak istediğinize emin misiniz?');">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </aside>

    <!-- SAĞ ANA İÇERİK ALANI -->
    <main class="admin-main-content">
        <!-- TOPBAR -->
        <header class="admin-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-light d-lg-none" id="sidebarToggleBtn">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h5 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($page_title ?? 'Yönetim Paneli') ?></h5>
            </div>

            <div class="d-flex align-items-center gap-3">
                <a href="product_add.php" class="btn btn-sm btn-apple btn-apple-orange d-none d-sm-inline-flex align-items-center gap-1">
                    <i class="bi bi-plus-lg"></i> Yeni Ürün Ekle
                </a>
                <a href="../index.php" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-globe me-1"></i> Siteyi Aç
                </a>
            </div>
        </header>

        <div class="p-4 p-md-4">
