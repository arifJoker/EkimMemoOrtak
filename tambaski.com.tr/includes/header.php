<?php
/**
 * TAMBASKI.COM.TR - Ana Header Şablonu (Modern, Doğal & Yüksek Dönüşümlü)
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
    <title><?= htmlspecialchars($page_title ?? 'TamBaskı – Online Matbaa, Dijital Baskı & Pleksi Kesim') ?></title>
    <meta name="description" content="<?= htmlspecialchars($page_desc ?? 'Türkiye’nin en hızlı ve uygun fiyatlı online matbaası. Kartvizit, broşür, rulo etiket, cepli dosya, dekota ve pleksi kesim çözümleri.') ?>">
    
    <!-- Favicon & App Icons -->
    <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg?v=2">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicon-32x32.png?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/img/favicon-16x16.png?v=2">
    <link rel="shortcut icon" href="assets/img/favicon.ico?v=2">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/apple-touch-icon.png?v=2">
    <link rel="icon" type="image/png" sizes="192x192" href="assets/img/icon-192.png?v=2">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#f15a24">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <!-- Google Fonts: Inter & JetBrains Mono -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&amp;family=JetBrains+Mono:wght@400;500;600&amp;display=swap" rel="stylesheet"/>
    <!-- Material Symbols Outlined & Bootstrap Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Tailwind CSS v3 -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
          darkMode: "class",
          theme: {
            extend: {
              colors: {
                brand: {
                  DEFAULT: "#f15a24",
                  50: "#fff7ed",
                  100: "#ffedd5",
                  200: "#fed7aa",
                  300: "#fdba74",
                  400: "#fb923c",
                  500: "#f15a24",
                  600: "#ea580c",
                  700: "#c2410c",
                  800: "#9a3412",
                  900: "#7c2d12",
                },
                primary: "#0f172a",
                secondary: "#2563eb",
                surface: "#f8fafc",
                "surface-card": "#ffffff",
                "surface-muted": "#f1f5f9",
                border: "#e2e8f0"
              },
              fontFamily: {
                sans: ["Inter", "system-ui", "-apple-system", "sans-serif"],
                mono: ["JetBrains Mono", "monospace"]
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
          background: rgba(255, 255, 255, 0.88);
          backdrop-filter: blur(16px);
          border: 1px solid rgba(226, 232, 240, 0.8);
        }
        .gold-foil-text {
          background: linear-gradient(135deg, #d4af37 0%, #fff2b2 45%, #aa771c 70%, #ffd700 100%);
          -webkit-background-clip: text;
          -webkit-text-fill-color: transparent;
        }
        .perspective-stage { perspective: 1200px; }
        .tilt-card { transform-style: preserve-3d; transition: transform 0.15s cubic-bezier(0.2, 0.8, 0.4, 1); }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased min-h-screen flex flex-col font-sans">

<!-- ================= TOP ANNOUNCEMENT BAR ================= -->
<div class="bg-slate-900 text-slate-200 py-1.5 px-4 text-xs border-b border-slate-800">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="font-medium text-white">🚀 <strong>750 ₺ ve Üzeri Siparişlerde Kargo Bedava!</strong></span>
            <span class="hidden md:inline text-slate-400">• Aynı Gün Üretim &amp; Ücretsiz Tasarım Kontrolü</span>
        </div>
        <div class="flex items-center gap-4 text-[11px] font-mono text-slate-300">
            <a href="https://wa.me/905440000000" target="_blank" class="hover:text-emerald-400 transition-colors flex items-center gap-1 font-sans">
                <span class="material-symbols-outlined text-[14px] text-emerald-400">chat</span> WhatsApp Sipariş: 0544 000 00 00
            </a>
            <span class="text-slate-700 hidden sm:inline">|</span>
            <a href="dealer_apply.php" class="hover:text-brand-300 transition-colors hidden sm:flex items-center gap-1 font-sans">
                <span class="material-symbols-outlined text-[14px] text-amber-400">badge</span> E-Bayi %25 İndirim
            </a>
            <span class="text-slate-700 hidden md:inline">|</span>
            <a href="tel:08503080000" class="hover:text-white transition-colors hidden md:flex items-center gap-1 font-sans">
                <span class="material-symbols-outlined text-[14px]">call</span> 0850 308 00 00
            </a>
        </div>
    </div>
</div>

<!-- ================= MAIN NAVBAR ================= -->
<header class="bg-white border-b border-slate-200 shadow-xs top-0 sticky z-40">
    <div class="flex justify-between items-center w-full px-4 sm:px-6 py-3 max-w-7xl mx-auto">
        <!-- Brand & Search Bar -->
        <div class="flex items-center gap-6">
            <a class="flex items-center gap-2 group py-1 shrink-0" href="index.php" title="TamBaskı Online Matbaa">
                <img src="assets/img/logo.svg?v=2" alt="TamBaskı" class="h-8 sm:h-9 w-auto">
            </a>
            
            <!-- Search Bar Input -->
            <form action="category.php" method="GET" class="hidden md:flex items-center relative w-72 lg:w-96">
                <span class="material-symbols-outlined absolute left-3 text-slate-400 text-lg pointer-events-none">search</span>
                <input name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" class="pl-9 pr-12 py-2 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20 w-full transition-all" placeholder="Kartvizit, broşür, rulo etiket, pleksi ara..." type="text"/>
                <button type="submit" class="absolute right-2 px-2 py-1 rounded bg-slate-200 hover:bg-brand-500 hover:text-white text-slate-600 text-[10px] font-bold transition-all">Ara</button>
            </form>
        </div>

        <!-- Navigation Links -->
        <nav class="hidden xl:flex items-center gap-6 text-xs font-semibold text-slate-700">
            <a class="hover:text-brand-600 transition-colors py-1" href="category.php?slug=kartvizit">Kartvizit</a>
            <a class="hover:text-brand-600 transition-colors py-1" href="category.php?slug=el-ilani-brosur">Broşür &amp; El İlanı</a>
            <a class="hover:text-brand-600 transition-colors py-1" href="category.php?slug=kurumsal-urunler">Cepli Dosya &amp; Zarf</a>
            <a class="hover:text-brand-600 transition-colors py-1" href="category.php?slug=dekota-pleksi-kesim">Dekota &amp; Pleksi Kesim</a>
            <a class="hover:text-brand-600 transition-colors py-1" href="category.php?slug=etiket-sticker">Etiket &amp; Sticker</a>
            <a class="hover:text-brand-600 transition-colors py-1" href="category.php?slug=promosyon-hediyelik">Promosyon</a>
        </nav>

        <!-- Trailing Action Hub -->
        <div class="flex items-center gap-3">
            <a class="inline-flex items-center gap-1.5 bg-gradient-to-r from-brand-500 to-brand-600 text-white text-xs font-bold px-3.5 py-2 rounded-xl shadow-xs hover:from-brand-600 hover:to-brand-700 transition-all active:scale-95" href="product.php?slug=ekonomik-kartvizit-250gr">
                <span class="material-symbols-outlined text-[16px]">draw</span>
                <span>Kendin Tasarla</span>
            </a>

            <!-- Action Icons -->
            <div class="flex items-center border-l border-slate-200 pl-3 gap-1.5 text-slate-600">
                <a href="order_tracking.php" class="p-2 hover:text-brand-600 hover:bg-slate-100 rounded-xl transition-colors" title="Kargo ve Sipariş Takibi">
                    <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                </a>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="account.php" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-xl transition-colors" title="Hesabım">
                        <span class="material-symbols-outlined text-[20px]">account_circle</span>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="p-2 hover:text-brand-600 hover:bg-slate-100 rounded-xl transition-colors" title="Giriş Yap">
                        <span class="material-symbols-outlined text-[20px]">person</span>
                    </a>
                <?php endif; ?>

                <a href="cart.php" class="p-2 hover:text-brand-600 hover:bg-slate-100 rounded-xl transition-colors relative" title="Sepetim">
                    <span class="material-symbols-outlined text-[20px]">shopping_bag</span>
                    <?php if ($cart_count > 0): ?>
                        <span class="absolute 0 top-0.5 right-0.5 w-4 h-4 bg-brand-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center"><?= $cart_count ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Mobile Subbar Navigation -->
    <div class="xl:hidden bg-slate-50 border-t border-slate-200 px-4 py-2 overflow-x-auto scrollbar-none flex items-center gap-2 text-xs">
        <a href="category.php?slug=kartvizit" class="px-3 py-1 rounded-full bg-white border border-slate-200 text-slate-700 whitespace-nowrap hover:border-brand-500 font-medium">Kartvizit</a>
        <a href="category.php?slug=el-ilani-brosur" class="px-3 py-1 rounded-full bg-white border border-slate-200 text-slate-700 whitespace-nowrap hover:border-brand-500 font-medium">Broşür &amp; El İlanı</a>
        <a href="category.php?slug=dekota-pleksi-kesim" class="px-3 py-1 rounded-full bg-white border border-slate-200 text-slate-700 whitespace-nowrap hover:border-brand-500 font-medium">Dekota &amp; Pleksi</a>
        <a href="category.php?slug=etiket-sticker" class="px-3 py-1 rounded-full bg-white border border-slate-200 text-slate-700 whitespace-nowrap hover:border-brand-500 font-medium">Etiket &amp; Sticker</a>
        <a href="category.php?slug=kurumsal-urunler" class="px-3 py-1 rounded-full bg-white border border-slate-200 text-slate-700 whitespace-nowrap hover:border-brand-500 font-medium">Cepli Dosya &amp; Zarf</a>
    </div>
</header>

<?php if ($flash_message): ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 mt-4">
        <div class="p-4 rounded-xl border <?= $flash_message['type'] === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800' ?> flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2 text-xs sm:text-sm font-medium">
                <span class="material-symbols-outlined text-[20px]"><?= $flash_message['type'] === 'success' ? 'check_circle' : 'error' ?></span>
                <span><?= htmlspecialchars($flash_message['text']) ?></span>
            </div>
        </div>
    </div>
<?php endif; ?>
