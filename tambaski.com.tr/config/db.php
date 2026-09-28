<?php
/**
 * TAMBASKI.COM.TR - Veritabanı Bağlantı Yapılandırması (PDO)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// cPanel ve Yerel Otomatik Algılama
$is_cpanel = file_exists('/home/arifuzco');

if ($is_cpanel) {
    $db_host = 'localhost';
    $db_name = 'arifuzco_tambaski';
    $db_user = 'arifuzco_tambaski';
    $db_pass = 'TamBaski2026!Secure';
} else {
    $db_host = getenv('DB_HOST') ?: '127.0.0.1';
    $db_name = getenv('DB_NAME') ?: 'tambaski_db';
    $db_user = getenv('DB_USER') ?: 'root';
    $db_pass = getenv('DB_PASS') ?: '';
}

$db_charset = 'utf8mb4';
$dsn = "mysql:host=$db_host;dbname=$db_name;charset=$db_charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    $pdo = null;
    $db_error = $e->getMessage();
}

/**
 * PDO nesnesini güvenli getirme fonksiyonu
 */
function getDB() {
    global $pdo;
    return $pdo;
}
