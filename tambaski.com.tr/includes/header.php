<?php
require_once __DIR__ . '/../config/config.php';
$cart = new Cart();
$cartCount = $cart->count();
$categories = (new Product())->getAll(null);
$dbConn = Database::getInstance()->getConnection();
$topCats = $dbConn ? $dbConn->query("SELECT * FROM categories WHERE status = 1 ORDER BY sort_order ASC LIMIT 8")->fetchAll() : [];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? Helper::getSetting('site_title', 'Online Matbaa & Dijital Baskı')) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDesc ?? Helper::getSetting('site_slogan', 'Türkiye\'nin En Hızlı ve Kaliteli Online Matbaası')) ?>">
    
    <!-- Favicon & App Icons -->
    <link rel="icon" type="image/svg+xml" href="<?= SITE_URL ?>/assets/img/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= SITE_URL ?>/assets/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= SITE_URL ?>/assets/img/favicon-16x16.png">
    <link rel="shortcut icon" href="<?= SITE_URL ?>/assets/img/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= SITE_URL ?>/assets/img/apple-touch-icon.png">

    <!-- PWA & Mobile Web App Meta Tags -->
    <link rel="manifest" href="<?= SITE_URL ?>/manifest.json">
    <meta name="theme-color" content="#1d1d1b">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="TamBaskı">
    <link rel="icon" type="image/png" sizes="192x192" href="<?= SITE_URL ?>/assets/img/icon-192.png">

    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Apple Sadeliğinde Özel CSS (Cache Buster ile) -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css?v=<?= time() ?>">
    
    <script>
        const SITE_URL = "<?= SITE_URL ?>";
        // PWA Service Worker Kaydı
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?= SITE_URL ?>/sw.js').catch(err => console.log('SW reg failed', err));
            });
        }
    </script>
</head>
<body class="pwa-app">

<!-- Üst Duyuru Bandı -->
<div class="top-announcement">
    <div class="container d-flex align-items-center justify-content-between">
        <div>
            <span>🚀 <strong>750 ₺ ve Üzeri Siparişlerde</strong> Kargo Ücretsiz!</span>
        </div>
        <div>
            <a href="<?= SITE_URL ?>/dealer_apply.php"><i class="bi bi-briefcase-fill me-1"></i>E-Bayi Ol %25 İndirim Kazan</a>
        </div>
    </div>
</div>

<!-- Apple Tarzı Ana Header -->
<header class="apple-header">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between py-2">
            
            <!-- Logo (Geniş Yatay Format - Karar Verilen Nihai Logo) -->
            <a class="navbar-brand me-2 me-xl-3" href="<?= SITE_URL ?>/" title="TamBaskı Online Matbaa">
                <img src="<?= SITE_URL ?>/assets/img/logo.svg?v=3" alt="TamBaskı" class="brand-logo-img" id="mainHeaderLogo" width="218" height="48" style="aspect-ratio: 3270 / 720; min-width: 140px; height: 48px; object-fit: contain;">
            </a>

            <!-- Ana Menü (Logo ile aynı hizada, sağa kaymış) -->
            <nav class="header-main-nav d-none d-lg-flex me-auto">
                <a href="<?= SITE_URL ?>/vehicle_studio.php" class="nav-link-modern fw-bold text-primary" style="color: #f15a24 !important;"><i class="bi bi-car-front-fill me-1"></i>🚗 Araç Stüdyosu</a>
                <a href="<?= SITE_URL ?>/category.php?slug=kartvizit" class="nav-link-modern">Kartvizit</a>
                <a href="<?= SITE_URL ?>/category.php?slug=dekota-uyari-levhalari" class="nav-link-modern fw-semibold text-dark"><i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>Dekota Uyarı Levhaları</a>
                <a href="<?= SITE_URL ?>/category.php?slug=el-ilani-brosur" class="nav-link-modern">Broşür & El İlanı</a>
                <a href="<?= SITE_URL ?>/category.php?slug=kurumsal-urunler" class="nav-link-modern">Kurumsal</a>
                
                <div class="dropdown">
                    <a href="#" class="nav-link-modern dropdown-toggle" role="button" data-bs-toggle="dropdown">
                        Tüm Baskılar
                    </a>
                    <ul class="dropdown-menu shadow-lg border-0 rounded-3 p-2 mt-2">
                        <?php foreach ($topCats as $c): ?>
                            <li>
                                <a class="dropdown-item rounded-2 py-2 d-flex align-items-center gap-2 small" href="<?= SITE_URL ?>/category.php?slug=<?= $c['slug'] ?>">
                                    <i class="<?= $c['icon'] ?> text-primary"></i> <?= htmlspecialchars($c['name']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <a href="<?= SITE_URL ?>/category.php?urgent=1" class="nav-link-urgent ms-1">
                    <i class="bi bi-lightning-charge-fill"></i> Acil 24S
                </a>
            </nav>

            <!-- Sağ Aksiyonlar: Arama, Kargo Takip, Giriş, Sepet -->
            <div class="d-flex align-items-center gap-2 ms-auto">
                <!-- Arama Kutusu -->
                <form action="<?= SITE_URL ?>/category.php" method="GET" class="header-search-form d-none d-md-flex">
                    <i class="bi bi-search"></i>
                    <input type="text" name="q" placeholder="Ürün ara..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                </form>

                <!-- Kargo Takip (Modern Pill) -->
                <a href="<?= SITE_URL ?>/order_tracking.php" class="header-action-pill d-none d-sm-inline-flex" title="Kargo ve Sipariş Takibi">
                    <i class="bi bi-truck text-primary"></i>
                    <span>Kargo Takip</span>
                </a>

                <!-- Giriş / Kullanıcı Menüsü -->
                <?php if (Auth::isAdmin()): ?>
                    <a href="<?= SITE_URL ?>/admin/" class="header-action-pill border-primary bg-primary bg-opacity-10 text-primary fw-bold" title="Admin Paneli">
                        <i class="bi bi-shield-lock-fill text-warning"></i>
                        <span class="d-none d-lg-inline">Admin</span>
                    </a>
                <?php endif; ?>

                <?php if (Auth::check()): ?>
                    <div class="dropdown">
                        <button class="header-action-pill dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle text-primary"></i>
                            <span class="d-none d-md-inline"><?= htmlspecialchars(explode(' ', Auth::user()['full_name'])[0]) ?></span>
                            <?php if (Auth::isDealer()): ?>
                                <span class="badge bg-primary ms-1" style="font-size: 10px;">Bayi</span>
                            <?php endif; ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2 p-2">
                            <li class="px-3 py-2 border-bottom mb-1">
                                <div class="fw-bold small"><?= htmlspecialchars(Auth::user()['full_name']) ?></div>
                                <div class="text-muted" style="font-size: 11px;"><?= htmlspecialchars(Auth::user()['email']) ?></div>
                            </li>
                            <?php if (Auth::isAdmin()): ?>
                                <li><a class="dropdown-item rounded-2 py-2 fw-bold text-primary" href="<?= SITE_URL ?>/admin/"><i class="bi bi-speedometer2 me-2"></i>Admin Paneli</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item rounded-2 py-2" href="<?= SITE_URL ?>/account.php"><i class="bi bi-bag-check me-2"></i>Siparişlerim</a></li>
                            <li><a class="dropdown-item rounded-2 py-2" href="<?= SITE_URL ?>/account.php?tab=address"><i class="bi bi-geo-alt me-2"></i>Adreslerim</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item rounded-2 py-2 text-danger" href="<?= SITE_URL ?>/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Çıkış Yap</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?= SITE_URL ?>/login.php" class="header-action-pill" title="Giriş Yap">
                        <i class="bi bi-person"></i>
                        <span class="d-none d-md-inline">Giriş</span>
                    </a>
                <?php endif; ?>

                <!-- Sepet Butonu -->
                <a href="<?= SITE_URL ?>/cart.php" class="header-cart-pill" title="Sepetim">
                    <div class="cart-icon-wrapper">
                        <i class="bi bi-bag-fill"></i>
                        <span id="headerCartCount" class="cart-count-badge"><?= $cartCount ?></span>
                    </div>
                    <span class="d-none d-sm-inline">Sepetim</span>
                </a>
            </div>

        </div>
    </div>

    <!-- Mobil Kategori Alt Çubuğu (Yalnızca mobilde hızlı kaydırma) -->
    <div class="category-subbar d-lg-none">
        <div class="container">
            <div class="category-subbar-inner">
                <a href="<?= SITE_URL ?>/category.php?urgent=1" class="cat-chip urgent">
                    <i class="bi bi-lightning-charge-fill"></i> Acil Baskı (24 Saat)
                </a>
                <?php foreach ($topCats as $c): ?>
                    <a href="<?= SITE_URL ?>/category.php?slug=<?= $c['slug'] ?>" class="cat-chip">
                        <i class="<?= $c['icon'] ?>"></i> <?= htmlspecialchars($c['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</header>

<script>
// Karar verilen nihai logo yüklendi, geçici varyasyon seçimini temizle
try { localStorage.removeItem('tam_baski_active_logo'); } catch(e){}
</script>

<!-- Flash Mesajları -->
<?php if ($flash = Helper::getFlash()): ?>
    <div class="container mt-3">
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show rounded-4 shadow-sm" role="alert">
            <i class="bi bi-info-circle me-2"></i><?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
<?php endif; ?>
