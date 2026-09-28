<?php
/**
 * TAMBASKI.COM.TR - Uygulama ve Sistem Sabitleri
 */

require_once __DIR__ . '/db.php';

define('SITE_NAME', 'TamBaskı');
define('SITE_TITLE', 'TamBaskı – Online Matbaa, Dijital Baskı & Pleksi Kesim Merkezi');
define('SITE_DESCRIPTION', 'Türkiye\'nin En Hızlı ve Kaliteli Online Matbaa, Dekota / Pleksi Kesim & Promosyon Üretim Merkezi');
define('SITE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . str_replace('/index.php', '', dirname($_SERVER['SCRIPT_NAME'] ?? '')));
define('FREE_SHIPPING_LIMIT', 750.00);
define('DEFAULT_DEALER_DISCOUNT', 25); // %25 İskonto

// Flash Mesaj Yardımcısı
function set_flash_message($type, $text) {
    $_SESSION['flash_message'] = ['type' => $type, 'text' => $text];
}

function get_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $msg;
    }
    return null;
}

// Para Formatı (₺)
function format_price($amount) {
    return number_format((float)$amount, 2, ',', '.') . ' ₺';
}
