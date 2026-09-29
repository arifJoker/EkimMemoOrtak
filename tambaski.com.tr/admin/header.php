<?php
require_once __DIR__ . '/../config/config.php';
Auth::requireAdmin();

$currentAdmin = Auth::user();
$dbConn = Database::getInstance()->getConnection();

$pendingOrdersCount = $dbConn ? $dbConn->query("SELECT COUNT(*) FROM orders WHERE order_status = 'pending_payment' OR order_status = 'payment_received'")->fetchColumn() : 0;
$pendingDealersCount = $dbConn ? $dbConn->query("SELECT COUNT(*) FROM users WHERE role = 'dealer' AND dealer_status = 'pending'")->fetchColumn() : 0;

$activePage = basename($_SERVER['PHP_SELF']);
$currentAction = $_GET['action'] ?? 'list';

$isProductMenuOpen = in_array($activePage, ['products.php', 'categories.php', 'variants.php', 'pricing_engine.php']);
$isSettingsMenuOpen = in_array($activePage, ['payment_settings.php', 'cargo_settings.php', 'ai_api.php']);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Yönetim Paneli') ?> – TamBaskı Admin</title>

    <!-- Favicon & App Icons -->
    <link rel="icon" type="image/svg+xml" href="<?= SITE_URL ?>/assets/img/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= SITE_URL ?>/assets/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= SITE_URL ?>/assets/img/favicon-16x16.png">
    <link rel="shortcut icon" href="<?= SITE_URL ?>/assets/img/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= SITE_URL ?>/assets/img/apple-touch-icon.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <style>
        body {
            overflow-x: hidden;
            background: #f8fafc;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }
        .admin-sidebar {
            width: 260px;
            height: 100vh;
            background: #0f172a;
            color: #e2e8f0;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1000;
            overflow-y: auto;
            border-right: 1px solid rgba(255, 255, 255, 0.07);
        }
        .admin-main {
            margin-left: 260px;
            padding: 25px 30px;
            min-height: 100vh;
            width: calc(100% - 260px);
            max-width: calc(100% - 260px);
            box-sizing: border-box;
            background: #f8fafc;
        }
        .admin-nav-group-title {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: #64748b;
            padding: 12px 14px 4px 14px;
        }
        .admin-nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 14px;
            color: #94a3b8;
            font-size: 13.5px;
            font-weight: 500;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 2px;
            transition: all 0.15s ease-in-out;
            cursor: pointer;
        }
        .admin-nav-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.07);
        }
        .admin-nav-link.active {
            background: #2563eb;
            color: #ffffff;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(37,99,235,0.3);
        }
        .admin-sub-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 12px 7px 32px;
            color: #94a3b8;
            font-size: 12.8px;
            font-weight: 500;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 2px;
            transition: all 0.15s;
            position: relative;
        }
        .admin-sub-link::before {
            content: '';
            position: absolute;
            left: 18px;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: #475569;
            transition: all 0.15s;
        }
        .admin-sub-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.05);
        }
        .admin-sub-link:hover::before {
            background: #38bdf8;
            transform: scale(1.4);
        }
        .admin-sub-link.active {
            color: #38bdf8;
            background: rgba(56, 189, 248, 0.1);
            font-weight: 600;
        }
        .admin-sub-link.active::before {
            background: #38bdf8;
            box-shadow: 0 0 6px #38bdf8;
        }
        .admin-menu-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }
        .admin-menu-toggle .chevron-icon {
            transition: transform 0.2s ease;
            font-size: 11px;
        }
        .admin-menu-toggle[aria-expanded="true"] .chevron-icon {
            transform: rotate(180deg);
        }
        @media (max-width: 991px) {
            .admin-sidebar { position: relative; width: 100%; height: auto; }
            .admin-main { margin-left: 0; width: 100%; max-width: 100%; padding: 15px; }
        }
        @media print {
            .admin-sidebar, .btn, .alert, .dropdown, a.btn, form button, .sticky-top { display: none !important; }
            .admin-main { margin-left: 0 !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; background: #fff !important; }
            .apple-card { box-shadow: none !important; border: 1px solid #ccc !important; }
            body { background: #fff !important; }
        }
    </style>
</head>
<body>

<div class="d-flex flex-column flex-lg-row">
    
    <!-- Sol Sidebar -->
    <aside class="admin-sidebar p-3 d-flex flex-column">
        <!-- Logo & Başlık -->
        <div class="d-flex align-items-center gap-2 mb-3 px-2 pt-2 pb-2 border-bottom border-secondary border-opacity-25">
            <a href="<?= SITE_URL ?>/admin/index.php" class="d-flex align-items-center gap-2 text-decoration-none">
                <img src="<?= SITE_URL ?>/assets/img/logo-badge.svg" alt="TamBaskı" style="height: 38px; width: auto; border-radius: 8px;">
                <div>
                    <div class="fw-bold text-white fs-6 lh-1">TamBaskı</div>
                    <div class="text-muted small mt-1" style="font-size: 11px;">Yönetim & Operasyon</div>
                </div>
            </a>
        </div>

        <!-- Gezinme Menüsü -->
        <nav class="flex-grow-1 overflow-y-auto" style="max-height: calc(100vh - 150px);">
            
            <div class="admin-nav-group-title">Genel</div>
            <a href="<?= SITE_URL ?>/admin/index.php" class="admin-nav-link <?= $activePage == 'index.php' ? 'active' : '' ?>">
                <i class="bi bi-speedometer2 text-info"></i> Dashboard
            </a>
            
            <a href="<?= SITE_URL ?>/admin/orders.php" class="admin-nav-link <?= $activePage == 'orders.php' ? 'active' : '' ?>">
                <i class="bi bi-bag-check text-warning"></i> Siparişler
                <?php if ($pendingOrdersCount > 0): ?>
                    <span class="badge bg-danger rounded-pill ms-auto px-2 py-1" style="font-size: 10px;"><?= $pendingOrdersCount ?></span>
                <?php endif; ?>
            </a>

            <!-- ÜRÜN İŞLEMLERİ (Açılır / Alt Menülü Grup) -->
            <div class="admin-nav-group-title">Katalog & Üretim</div>
            
            <a class="admin-nav-link admin-menu-toggle <?= $isProductMenuOpen ? 'text-white' : '' ?>" data-bs-toggle="collapse" href="#submenuProducts" role="button" aria-expanded="<?= $isProductMenuOpen ? 'true' : 'false' ?>">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-box-seam text-primary"></i>
                    <span>Ürün İşlemleri</span>
                </div>
                <i class="bi bi-chevron-down chevron-icon text-muted"></i>
            </a>

            <div class="collapse <?= $isProductMenuOpen ? 'show' : '' ?>" id="submenuProducts">
                <div class="py-1">
                    <a href="<?= SITE_URL ?>/admin/products.php" class="admin-sub-link <?= ($activePage == 'products.php' && $currentAction != 'create') ? 'active' : '' ?>">
                        <i class="bi bi-list-ul"></i> Tüm Ürünler
                    </a>
                    <a href="<?= SITE_URL ?>/admin/products.php?action=add" class="admin-sub-link <?= ($activePage == 'products.php' && in_array($currentAction, ['add', 'create'])) ? 'active' : '' ?>">
                        <i class="bi bi-plus-circle text-success"></i> Yeni Ürün Ekle
                    </a>
                    <a href="<?= SITE_URL ?>/admin/categories.php" class="admin-sub-link <?= $activePage == 'categories.php' ? 'active' : '' ?>">
                        <i class="bi bi-grid"></i> Kategoriler
                    </a>
                </div>
            </div>

            <?php if (Auth::canManageDesign()): ?>
            <div class="admin-nav-group-title">Tasarım & Müşteri</div>
            <a href="<?= SITE_URL ?>/admin/templates.php" class="admin-nav-link <?= $activePage == 'templates.php' ? 'active' : '' ?>">
                <i class="bi bi-vector-pen text-primary"></i> Sektörel Şablonlar
            </a>

            <a href="<?= SITE_URL ?>/admin/dealers.php" class="admin-nav-link <?= $activePage == 'dealers.php' ? 'active' : '' ?>">
                <i class="bi bi-people text-info"></i> Müşteriler & E-Bayiler
                <?php if ($pendingDealersCount > 0): ?>
                    <span class="badge bg-warning text-dark rounded-pill ms-auto px-2 py-1" style="font-size: 10px;"><?= $pendingDealersCount ?> Bekleyen</span>
                <?php endif; ?>
            </a>

            <a href="<?= SITE_URL ?>/admin/campaigns.php" class="admin-nav-link <?= $activePage == 'campaigns.php' ? 'active' : '' ?>">
                <i class="bi bi-ticket-perforated text-success"></i> Kampanyalar & Kuponlar
            </a>

            <!-- SİSTEM & ENTEGRASYON (Açılır Alt Menü) -->
            <div class="admin-nav-group-title">Sistem & Entegrasyon</div>
            <a class="admin-nav-link admin-menu-toggle <?= $isSettingsMenuOpen ? 'text-white' : '' ?>" data-bs-toggle="collapse" href="#submenuSettings" role="button" aria-expanded="<?= $isSettingsMenuOpen ? 'true' : 'false' ?>">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-sliders text-secondary"></i>
                    <span>Sistem Ayarları</span>
                </div>
                <i class="bi bi-chevron-down chevron-icon text-muted"></i>
            </a>

            <div class="collapse <?= $isSettingsMenuOpen ? 'show' : '' ?>" id="submenuSettings">
                <div class="py-1">
                    <a href="<?= SITE_URL ?>/admin/payment_settings.php" class="admin-sub-link <?= $activePage == 'payment_settings.php' ? 'active' : '' ?>">
                        <i class="bi bi-credit-card"></i> PayTR / iyzico Ödeme
                    </a>
                    <a href="<?= SITE_URL ?>/admin/cargo_settings.php" class="admin-sub-link <?= $activePage == 'cargo_settings.php' ? 'active' : '' ?>">
                        <i class="bi bi-truck"></i> Kargo Anlaşmaları
                    </a>
                    <a href="<?= SITE_URL ?>/admin/ai_api.php" class="admin-sub-link <?= $activePage == 'ai_api.php' ? 'active' : '' ?>">
                        <i class="bi bi-robot text-warning"></i> AI & REST API
                    </a>
                </div>
            </div>
            <?php endif; ?>

        </nav>

        <!-- Kullanıcı & Çıkış -->
        <div class="pt-3 border-top border-secondary border-opacity-25 px-1 mt-auto">
            <div class="mb-2">
                <div class="d-flex align-items-center justify-content-between text-muted small px-1">
                    <span class="text-truncate fw-medium text-light" style="max-width: 140px;">
                        <i class="bi bi-person-circle me-1 <?= Auth::isAdmin() ? 'text-primary' : 'text-info' ?>"></i> <?= htmlspecialchars($currentAdmin['full_name']) ?>
                    </span>
                    <a href="<?= SITE_URL ?>/logout.php" class="text-danger text-decoration-none small fw-bold"><i class="bi bi-power"></i> Çıkış</a>
                </div>
                <div class="px-1 mt-1">
                    <?php if (Auth::isAdmin()): ?>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5 rounded-pill" style="font-size: 10px;">👑 Yönetici (Tam Yetkili)</span>
                    <?php else: ?>
                        <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-0.5 rounded-pill" style="font-size: 10px;">📦 Ürün Editörü (Yalnızca Ürün Ekleme)</span>
                    <?php endif; ?>
                </div>
            </div>
            <a href="<?= SITE_URL ?>/" target="_blank" class="btn btn-sm btn-outline-light w-100 mt-1 py-1 small rounded-3">
                <i class="bi bi-box-arrow-up-right me-1"></i> Siteyi Görüntüle
            </a>
        </div>
    </aside>

    <!-- Sağ Ana İçerik -->
    <main class="admin-main flex-grow-1">
        <?php if ($flash = Helper::getFlash()): ?>
            <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
                <?= htmlspecialchars($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
