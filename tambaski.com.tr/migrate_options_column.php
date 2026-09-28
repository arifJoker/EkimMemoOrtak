<?php
require_once __DIR__ . '/config/config.php';

$db = Database::getInstance()->getConnection();

echo "<h3>Varyant Seçenekleri Kolonu Ekleme</h3><pre>";

try {
    $cols = $db->query("SHOW COLUMNS FROM `products` LIKE 'allowed_variant_options'")->fetchAll();
    if (empty($cols)) {
        $db->exec("ALTER TABLE `products` ADD COLUMN `allowed_variant_options` TEXT NULL DEFAULT NULL AFTER `allowed_variant_groups`");
        echo "✔ products.allowed_variant_options kolonu eklendi.\n";
    } else {
        echo "✔ products.allowed_variant_options kolonu zaten mevcut.\n";
    }

    // Mevcut ürünlere varsayılan seçenekleri bağla
    $allOptIds = $db->query("SELECT id FROM variant_options WHERE status = 1")->fetchAll(PDO::FETCH_COLUMN);
    $jsonOpts = json_encode(array_map('intval', $allOptIds));
    $db->exec("UPDATE products SET allowed_variant_options = '$jsonOpts' WHERE allowed_variant_options IS NULL OR allowed_variant_options = ''");
    echo "✔ Tüm ürünlere mevcut varyant seçenekleri bağlandı.\n";

    echo "\n🎉 GÜNCELLEME TAMAMLANDI!";
} catch (Exception $e) {
    echo "HATA: " . $e->getMessage();
}
echo "</pre>";
