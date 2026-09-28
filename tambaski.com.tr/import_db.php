<?php
require_once __DIR__ . '/config/config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $db = Database::getInstance()->getConnection();
    if (!$db) {
        throw new Exception("Veritabanı bağlantısı kurulamadı.");
    }

    $sqlFile = __DIR__ . '/database.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("database.sql dosyası bulunamadı.");
    }

    $sql = file_get_contents($sqlFile);
    
    // Çoklu SQL komutlarını çalıştır
    $db->exec($sql);

    echo json_encode([
        'success' => true,
        'message' => 'Veritabanı tabloları ve varsayılan matbaa ürünleri başarıyla içe aktarıldı.'
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
