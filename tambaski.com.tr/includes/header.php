<?php
/**
 * TAMBASKI.COM.TR - Ana Header Şablonu (Apple Tarzı Modern & Şık)
 */
require_once __DIR__ . '/functions.php';

$all_categories = get_all_categories();
$cart_count = get_cart_count();
$flash_message = get_flash_message();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? SITE_TITLE) ?></title>
    <meta name="description" content="<?= htmlspecialchars($page_desc ?? SITE_DESCRIPTION) ?>">
    
    <!-- Favicon & Icons -->
    <link rel="icon" type="image/svg+xml" href="assets/img/logo.svg">

    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Apple Tarzı Özel CSS -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time() ?>">
</head>
<body>

<!-- Üst Duyuru Bandı -->
<div class="top-announcement">
    <div class="container d-flex align-items-center justify-content-between">
        <div>
            <span>🚀 <strong>750 ₺ ve Üzeri Siparişlerde</strong> Kargo Ücretsiz! • Türkiye'nin Her Yerine Hızlı Gönderim</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="dealer_apply.php"><i class="bi bi-briefcase-fill me-1"></i>E-Bayi Ol %25 İndirim Kazan</a>
            <span class="text-white-50">|</span>
            <a href="tel:08503080000" class="text-white text-decoration-none"><i class="bi bi-telephone-fill me-1"></i>0850 308 00 00</a>
        </div>
    </div>
</div>

<!-- Apple Tarzı Ana Header -->
<header class="apple-header">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between py-2">
            
            <!-- Logo -->
            <a class="navbar-brand me-3" href="index.php" title="TamBaskı Online Matbaa">
                <img src="assets/img/logo.svg" alt="TamBaskı" class="brand-logo-img">
            </a>

            <!-- Ana Menü -->
            <nav class="header-main-nav d-none d-lg-flex me-auto">
                <a href="category.php?slug=kartvizit" class="nav-link-modern">Kartvizit</a>
                <a href="category.php?slug=el-ilani-brosur" class="nav-link-modern">Broşür & El İlanı</a>
                <a href="category.php?slug=dekota-pleksi-kesim" class="nav-link-modern">Dekota & Pleksi</a>
                <a href="category.php?slug=folyo-branda-reklam" class="nav-link-modern">Folyo & Branda</a>
                <a href="category.php?slug=promosyon-hediyelik" class="nav-link-modern">Promosyon</a>
                
                <!-- Tüm Kategoriler Dropdown -->
                <div class="dropdown">
                    <a href="#" class="nav-link-modern dropdown-toggle" role="button" data-bs-toggle="dropdown">
                        Tüm Baskılar
                    </a>
                    <ul class="dropdown-menu shadow-lg border-0 rounded-4 p-2 mt-2">
                        <?php foreach ($all_categories as $cat): ?>
                            <li>
                                <a class="dropdown-item rounded-3 py-2 d-flex align-items-center gap-2 small" href="category.php?slug=<?= urlencode($cat['slug']) ?>">
                                    <i class="bi <?= htmlspecialchars($cat['icon']) ?> text-primary"></i> <?= htmlspecialchars($cat['name']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <a href="category.php?slug=acil-baski" class="nav-link-urgent ms-1">
                    <i class="bi bi-lightning-charge-fill"></i> Acil 24S
                </a>
            </nav>

            <!-- Sağ Aksiyonlar: Arama, Kargo Takip, Giriş, Sepet -->
            <div class="d-flex align-items-center gap-2 ms-auto">
                <!-- Arama Kutusu -->
                <form action="category.php" method="GET" class="header-search-form d-none d-md-flex">
                    <i class="bi bi-search text-muted"></i>
                    <input type="text" name="q" placeholder="Ürün veya ebat ara..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                </form>

                <!-- Kargo Takip -->
                <a href="order_tracking.php" class="header-action-pill d-none d-sm-inline-flex" title="Kargo ve Sipariş Takibi">
                    <i class="bi bi-truck text-primary"></i>
                    <span>Kargo Takip</span>
                </a>

                <!-- Giriş / Kullanıcı Menüsü -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <a href="#" class="header-action-pill dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-person-check-fill text-success"></i>
                            <span><?= htmlspecialchars($_SESSION['user_name'] ?? 'Hesabım') ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 p-2 mt-2">
                            <li><a class="dropdown-item rounded-2" href="account.php"><i class="bi bi-person me-2"></i>Hesap Bilgilerim</a></li>
                            <li><a class="dropdown-item rounded-2" href="orders.php"><i class="bi bi-box me-2"></i>Siparişlerim</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item rounded-2 text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Çıkış Yap</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="header-action-pill" title="Giriş Yap">
                        <i class="bi bi-person"></i>
                        <span class="d-none d-md-inline">Giriş Yap</span>
                    </a>
                <?php endif; ?>

                <!-- Sepet Butonu -->
                <a href="cart.php" class="header-cart-pill" title="Sepetim">
                    <div class="position-relative">
                        <i class="bi bi-bag-fill"></i>
                        <span id="headerCartCount" class="cart-count-badge"><?= $cart_count ?></span>
                    </div>
                    <span class="d-none d-sm-inline">Sepetim</span>
                </a>
            </div>

        </div>
    </div>

    <!-- Mobil Kategori Alt Çubuğu -->
    <div class="category-subbar d-lg-none">
        <div class="container">
            <div class="category-subbar-inner">
                <a href="category.php?slug=acil-baski" class="cat-chip urgent">
                    <i class="bi bi-lightning-charge-fill"></i> 24S Acil Baskı
                </a>
                <?php foreach ($all_categories as $cat): ?>
                    <a href="category.php?slug=<?= urlencode($cat['slug']) ?>" class="cat-chip">
                        <i class="bi <?= htmlspecialchars($cat['icon']) ?>"></i> <?= htmlspecialchars($cat['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</header>

<?php if ($flash_message): ?>
    <div class="container mt-3">
        <div class="alert alert-<?= htmlspecialchars($flash_message['type']) ?> alert-dismissible fade show rounded-4 shadow-sm" role="alert">
            <?= htmlspecialchars($flash_message['text']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
        </div>
    </div>
<?php endif; ?>
