<?php
/**
 * Baskı & E-Ticaret Platformu - Yapılandırma Dosyası
 * Paylaşımlı Hosting Uyumlu (PHP 7.4 - 8.x, LiteSpeed / Apache)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hata Raporlama (Geliştirme aşamasında açık, canlıda 0 yapılabilir)
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Zaman Dilimi & Dil
date_default_timezone_set('Europe/Istanbul');
setlocale(LC_TIME, 'tr_TR.UTF-8', 'tr_TR', 'turkish');

// Site URL ve Dizin Sabitleri
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$basePath = preg_replace('/(\/admin|\/api|\/api\/v1)$/', '', $scriptDir);
if ($basePath === '/') {
    $basePath = '';
}

define('SITE_URL', rtrim($protocol . $host . $basePath, '/'));
define('ROOT_PATH', str_replace('\\', '/', dirname(__DIR__)));
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('UPLOAD_URL', SITE_URL . '/uploads');

// Veritabanı Bilgileri (Hosting / Canlı Sunucu)
define('DB_HOST', 'localhost');
define('DB_NAME', 'arifuzco_baski');
define('DB_USER', 'arifuzco_baski');
define('DB_PASS', 'BaskiMatbaa2026Secure!');
define('DB_CHARSET', 'utf8mb4');

// Otomatik Sınıf Yükleyici (Autoload)
spl_autoload_register(function ($className) {
    $file = ROOT_PATH . '/classes/' . $className . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Güvenlik & CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Global Yardımcı Değişkenler
$db = Database::getInstance()->getConnection();
$siteSettings = Helper::getSettings();
