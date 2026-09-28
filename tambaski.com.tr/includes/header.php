<?php
/**
 * TAMBASKI.COM.TR - Ana Header Şablonu (Precision Studio Print - Tailwind & Material Design)
 */
require_once __DIR__ . '/functions.php';

$all_categories = get_all_categories();
$cart_count = get_cart_count();
$flash_message = get_flash_message();
?>
<!DOCTYPE html>
<html class="scroll-smooth light" lang="tr">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= htmlspecialchars($page_title ?? 'TAM BASKI | Endüstriyel Baskı & Kurumsal Matbaa Çözümleri') ?></title>
    <meta name="description" content="<?= htmlspecialchars($page_desc ?? 'Heidelberg ofset kalitesi, 420+ kurumsal şablon ve anında online vektör prova imkanıyla prestijli kurumsal baskı çözümleri.') ?>">
    
    <!-- Favicon & PWA -->
    <link rel="icon" type="image/svg+xml" href="assets/img/logo.svg">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#0F172A">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <!-- Google Fonts: Inter & JetBrains Mono -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&amp;family=JetBrains+Mono:wght@400;500;600&amp;display=swap" rel="stylesheet"/>
    <!-- Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    
    <!-- Tailwind CSS v3 -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
          darkMode: "class",
          theme: {
            extend: {
              "colors": {
                "on-tertiary": "#ffffff",
                "on-tertiary-fixed-variant": "#005236",
                "error-container": "#ffdad6",
                "surface-dim": "#d8dadc",
                "secondary-container": "#316bf3",
                "primary-container": "#131b2e",
                "secondary-fixed-dim": "#b4c5ff",
                "tertiary-fixed-dim": "#4edea3",
                "surface-container-high": "#e6e8ea",
                "on-secondary-container": "#fefcff",
                "on-primary-fixed": "#131b2e",
                "surface": "#f7f9fb",
                "tertiary-fixed": "#6ffbbe",
                "surface-bright": "#f7f9fb",
                "secondary": "#0051d5",
                "on-error-container": "#93000a",
                "outline-variant": "#c6c6cd",
                "tertiary": "#000000",
                "inverse-surface": "#2d3133",
                "on-secondary-fixed": "#00174b",
                "primary-fixed-dim": "#bec6e0",
                "surface-container-highest": "#e0e3e5",
                "on-secondary-fixed-variant": "#003ea8",
                "on-surface-variant": "#45464d",
                "primary-fixed": "#dae2fd",
                "on-primary-container": "#7c839b",
                "on-tertiary-container": "#009668",
                "primary": "#000000",
                "on-error": "#ffffff",
                "on-primary": "#ffffff",
                "surface-container-low": "#f2f4f6",
                "secondary-fixed": "#dbe1ff",
                "tertiary-container": "#002113",
                "on-surface": "#191c1e",
                "error": "#ba1a1a",
                "on-secondary": "#ffffff",
                "on-background": "#191c1e",
                "surface-container": "#eceef0",
                "surface-variant": "#e0e3e5",
                "outline": "#76777d",
                "background": "#f7f9fb",
                "on-tertiary-fixed": "#002113",
                "on-primary-fixed-variant": "#3f465c",
                "inverse-on-surface": "#eff1f3",
                "surface-tint": "#565e74",
                "surface-container-lowest": "#ffffff",
                "inverse-primary": "#bec6e0"
              },
              "borderRadius": {
                "DEFAULT": "0.25rem",
                "lg": "0.5rem",
                "xl": "0.75rem",
                "2xl": "1rem",
                "full": "9999px"
              },
              "spacing": {
                "space-lg": "1.5rem",
                "space-sm": "0.5rem",
                "space-xl": "2.5rem",
                "space-xs": "0.25rem",
                "gutter": "1.5rem",
                "space-md": "1rem",
                "margin-mobile": "1rem",
                "margin": "3rem",
                "gutter-mobile": "0.75rem"
              },
              "fontFamily": {
                "sans": ["Inter", "sans-serif"],
                "headline-lg-mobile": ["Inter"],
                "body-lg": ["Inter"],
                "body-sm": ["Inter"],
                "body-md": ["Inter"],
                "headline-sm": ["Inter"],
                "headline-md": ["Inter"],
                "headline-lg": ["Inter"],
                "label-numeric": ["JetBrains Mono", "monospace"],
                "display-hero-mobile": ["Inter"],
                "label-caps": ["Inter"],
                "display-hero": ["Inter"]
              },
              "fontSize": {
                "body-lg": ["16px", { "lineHeight": "24px", "letterSpacing": "-0.005em", "fontWeight": "400" }],
                "headline-sm": ["18px", { "lineHeight": "24px", "letterSpacing": "-0.01em", "fontWeight": "600" }],
                "body-md": ["14px", { "lineHeight": "20px", "letterSpacing": "0em", "fontWeight": "400" }],
                "body-sm": ["12px", { "lineHeight": "16px", "letterSpacing": "0.005em", "fontWeight": "400" }],
                "headline-md": ["22px", { "lineHeight": "28px", "letterSpacing": "-0.015em", "fontWeight": "600" }],
                "label-numeric": ["11px", { "lineHeight": "14px", "letterSpacing": "0.02em", "fontWeight": "500" }],
                "display-hero": ["56px", { "lineHeight": "64px", "letterSpacing": "-0.03em", "fontWeight": "600" }],
                "display-hero-mobile": ["36px", { "lineHeight": "44px", "letterSpacing": "-0.025em", "fontWeight": "600" }],
                "label-caps": ["11px", { "lineHeight": "14px", "letterSpacing": "0.06em", "fontWeight": "600" }],
                "headline-lg": ["32px", { "lineHeight": "40px", "letterSpacing": "-0.02em", "fontWeight": "600" }],
                "headline-lg-mobile": ["26px", { "lineHeight": "34px", "letterSpacing": "-0.015em", "fontWeight": "600" }]
              }
            }
          }
        }
    </script>
    <style>
        .material-symbols-outlined {
          font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
          display: inline-block;
          vertical-align: middle;
          line-height: 1;
        }
        .frosted-glass {
          background: rgba(255, 255, 255, 0.85);
          backdrop-filter: blur(16px) saturate(180%);
          border: 1px solid rgba(255, 255, 255, 0.6);
        }
        .subtle-grid {
          background-size: 32px 32px;
          background-image: 
            linear-gradient(to right, rgba(15, 23, 42, 0.03) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(15, 23, 42, 0.03) 1px, transparent 1px);
        }
    </style>
</head>
<body class="bg-background text-on-surface antialiased selection:bg-secondary-fixed-dim selection:text-primary min-h-screen flex flex-col font-sans">

<!-- ================= TOP ANNOUNCEMENT BAR ================= -->
<div class="bg-primary-container text-on-primary py-1.5 px-4 text-xs border-b border-slate-800">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="font-medium">🚀 <strong>750 ₺ ve Üzeri Siparişlerde</strong> Kargo Ücretsiz!</span>
            <span class="hidden md:inline text-slate-400">• Heidelberg XL 10-Color Press Live</span>
        </div>
        <div class="flex items-center gap-4 text-[11px] font-label-numeric text-slate-300">
            <a href="dealer_apply.php" class="hover:text-white transition-colors flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">badge</span> E-Bayi %25 İndirim
            </a>
            <span class="text-slate-600">|</span>
            <a href="tel:08503080000" class="hover:text-white transition-colors flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">call</span> 0850 308 00 00
            </a>
        </div>
    </div>
</div>

<!-- ================= TOP NAVBAR (SHARED COMPONENT) ================= -->
<header class="bg-surface-container-lowest border-b border-outline-variant shadow-xs top-0 sticky z-40 transition-all duration-150 ease-out">
    <div class="flex justify-between items-center w-full px-4 sm:px-6 py-3 max-w-7xl mx-auto">
        <!-- Brand & Search Bar -->
        <div class="flex items-center gap-6">
            <a class="text-headline-sm font-semibold tracking-tight text-primary flex items-center gap-2 group" href="index.php">
                <span class="w-8 h-8 rounded-lg bg-primary-container text-on-primary flex items-center justify-center font-label-numeric text-sm font-bold border border-outline-variant shadow-sm">TB</span>
                <span class="font-extrabold tracking-tight">TAM BASKI</span>
                <span class="hidden sm:inline bg-surface-container-high text-on-surface-variant text-[10px] font-semibold px-2 py-0.5 rounded tracking-wider uppercase border border-outline-variant">STUDIO</span>
            </a>
            <!-- Search Bar Input -->
            <form action="category.php" method="GET" class="hidden lg:flex items-center relative">
                <span class="material-symbols-outlined absolute left-3 text-outline text-body-lg pointer-events-none">search</span>
                <input name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" class="pl-9 pr-12 py-1.5 bg-surface-container-low text-body-sm font-body-sm rounded-lg border border-outline-variant focus:outline-none focus:border-secondary focus:ring-1 focus:ring-secondary w-64 transition-all" placeholder="Ürün, gramaj veya ICC kodu ara..." type="text"/>
                <span class="absolute right-2.5 px-1.5 py-0.5 rounded bg-surface-container-high text-on-surface-variant font-label-numeric text-[10px]">⌘K</span>
            </form>
        </div>

        <!-- Navigation Links -->
        <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
            <a class="text-secondary font-semibold border-b-2 border-secondary pb-1 tracking-wider" href="index.php#products">Ürünler</a>
            <a class="text-on-surface-variant hover:text-primary transition-colors tracking-wider flex items-center gap-1" href="product.php?slug=ekonomik-kartvizit-250gr">
                <span class="material-symbols-outlined text-[16px] text-secondary">brush</span>
                <span>Custom Studio</span>
            </a>
            <a class="text-on-surface-variant hover:text-primary transition-colors tracking-wider" href="category.php?slug=kartvizit">Kartvizit</a>
            <a class="text-on-surface-variant hover:text-primary transition-colors tracking-wider" href="category.php?slug=el-ilani-brosur">Broşür</a>
            <a class="text-on-surface-variant hover:text-primary transition-colors tracking-wider" href="category.php?slug=dekota-pleksi-kesim">Dekota &amp; Pleksi</a>
            <a class="text-on-surface-variant hover:text-primary transition-colors tracking-wider" href="index.php#paper-stocks">Kağıtlar</a>
        </nav>

        <!-- Trailing Action Hub -->
        <div class="flex items-center gap-3">
            <a class="hidden sm:inline-flex items-center text-xs font-semibold text-on-surface hover:text-secondary px-3 py-1.5 rounded-lg border border-outline-variant bg-surface-container-lowest transition-all" href="index.php#sample-kit">
                Numune Kiti
            </a>
            <a class="inline-flex items-center gap-1.5 bg-primary text-on-primary text-xs font-semibold px-3.5 py-1.5 rounded-lg shadow-sm hover:bg-slate-800 transition-all active:scale-95" href="product.php?slug=ekonomik-kartvizit-250gr">
                <span class="material-symbols-outlined text-[16px] text-amber-300">magic_button</span>
                <span>Stüdyoyu Aç</span>
            </a>

            <!-- Trailing Icons -->
            <div class="flex items-center border-l border-outline-variant pl-3 ml-1 gap-1.5 text-on-surface-variant">
                <a href="cart.php" class="p-1.5 hover:text-primary hover:bg-surface-container rounded-lg transition-colors relative" title="Sepet">
                    <span class="material-symbols-outlined text-[20px]">shopping_bag</span>
                    <?php if ($cart_count > 0): ?>
                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-secondary text-white text-[10px] font-bold rounded-full flex items-center justify-center"><?= $cart_count ?></span>
                    <?php endif; ?>
                </a>
                <a href="order_tracking.php" class="p-1.5 hover:text-primary hover:bg-surface-container rounded-lg transition-colors" title="Sipariş & Kargo Takibi">
                    <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                </a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="account.php" class="p-1.5 text-emerald-600 hover:bg-surface-container rounded-lg transition-colors" title="Hesabım">
                        <span class="material-symbols-outlined text-[20px]">account_circle</span>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="p-1.5 hover:text-primary hover:bg-surface-container rounded-lg transition-colors" title="Giriş Yap">
                        <span class="material-symbols-outlined text-[20px]">person</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<?php if ($flash_message): ?>
    <div class="max-w-7xl mx-auto px-6 mt-4">
        <div class="p-4 rounded-xl border <?= $flash_message['type'] === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800' ?> flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2 text-sm font-medium">
                <span class="material-symbols-outlined text-[20px]"><?= $flash_message['type'] === 'success' ? 'check_circle' : 'error' ?></span>
                <span><?= htmlspecialchars($flash_message['text']) ?></span>
            </div>
        </div>
    </div>
<?php endif; ?>
