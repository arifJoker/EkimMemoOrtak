<?php
require_once __DIR__ . '/config/config.php';

$db = Database::getInstance()->getConnection();

echo "<h3>Varyant Veritabanı Kurulum ve Güncelleme</h3><pre>";

try {
    // 1. variant_groups Tablosu
    $db->exec("CREATE TABLE IF NOT EXISTS `variant_groups` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(150) NOT NULL,
        `slug` varchar(150) NOT NULL,
        `description` text DEFAULT NULL,
        `input_type` enum('radio','select','checkbox') NOT NULL DEFAULT 'radio',
        `is_required` tinyint(1) NOT NULL DEFAULT 1,
        `sort_order` int(11) NOT NULL DEFAULT 0,
        `status` tinyint(1) NOT NULL DEFAULT 1,
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "✔ variant_groups tablosu hazır.\n";

    // 2. variant_options Tablosu
    $db->exec("CREATE TABLE IF NOT EXISTS `variant_options` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `group_id` int(11) NOT NULL,
        `name` varchar(150) NOT NULL,
        `calc_type` enum('per_unit_try','percent','sheet_usd','fixed_try','fixed_usd') NOT NULL DEFAULT 'per_unit_try',
        `price_usd_70x100` decimal(10,2) NOT NULL DEFAULT 0.00,
        `fixed_fee_usd` decimal(10,2) NOT NULL DEFAULT 0.00,
        `percent_fee` decimal(5,2) NOT NULL DEFAULT 0.00,
        `per_unit_fee_try` decimal(10,2) NOT NULL DEFAULT 0.00,
        `fixed_fee_try` decimal(10,2) NOT NULL DEFAULT 0.00,
        `is_default` tinyint(1) NOT NULL DEFAULT 0,
        `sort_order` int(11) NOT NULL DEFAULT 0,
        `status` tinyint(1) NOT NULL DEFAULT 1,
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_group_id` (`group_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "✔ variant_options tablosu hazır.\n";

    // 3. products tablosuna allowed_variant_groups ekle
    $cols = $db->query("SHOW COLUMNS FROM `products` LIKE 'allowed_variant_groups'")->fetchAll();
    if (empty($cols)) {
        $db->exec("ALTER TABLE `products` ADD COLUMN `allowed_variant_groups` TEXT NULL DEFAULT NULL AFTER `allowed_templates`");
        echo "✔ products tablosuna allowed_variant_groups kolonu eklendi.\n";
    } else {
        echo "✔ products.allowed_variant_groups kolonu zaten mevcut.\n";
    }

    // 4. Varsayılan Grupları ve Seçenekleri Tohumla (Eğer Boşsa)
    $groupCount = (int)$db->query("SELECT COUNT(*) FROM variant_groups")->fetchColumn();
    if ($groupCount === 0) {
        // Kesim Türü
        $db->exec("INSERT INTO variant_groups (id, name, slug, description, input_type, is_required, sort_order, status) VALUES 
        (1, 'Kesim Türü', 'kesim-turu', 'Köşe ve kesim formatı', 'radio', 1, 1, 1),
        (2, 'Baskı Yönü', 'baski-yonu', 'Baskının tek veya çift taraflı olması', 'radio', 1, 2, 1),
        (3, 'Yüzey Kaplama / Selefon', 'yuzey-kaplama-selefon', 'Koruyucu film ve kaplama tipi', 'radio', 1, 3, 1),
        (4, 'Ekstra Efekt & Yaldız', 'ekstra-efekt-yaldiz', 'Görsel zenginleştirici özel efektler', 'radio', 0, 4, 1)");

        // Seçenekler
        // Kesim Türü Seçenekleri
        $db->exec("INSERT INTO variant_options (group_id, name, calc_type, per_unit_fee_try, fixed_fee_try, is_default, sort_order) VALUES
        (1, 'Standart Düz Kesim (90°)', 'per_unit_try', 0.00, 0.00, 1, 1),
        (1, 'Oval Köşe Kesim (Yarıçap R5)', 'per_unit_try', 0.15, 0.00, 0, 2),
        (1, 'Özel Geometrik Bıçak Kesim', 'fixed_try', 0.00, 250.00, 0, 3),
        (1, 'Perforaj / Koparma Çizgisi', 'per_unit_try', 0.10, 0.00, 0, 4),
        (1, 'Pilyaj / Katlama İzi', 'per_unit_try', 0.10, 0.00, 0, 5)");

        // Baskı Yönü Seçenekleri
        $db->exec("INSERT INTO variant_options (group_id, name, calc_type, percent_fee, is_default, sort_order) VALUES
        (2, 'Tek Yön Renkli Baskı (4+0)', 'percent', 0.00, 1, 1),
        (2, 'Çift Yön Renkli Baskı (4+4)', 'percent', 25.00, 0, 2)");

        // Yüzey Kaplama Seçenekleri
        $db->exec("INSERT INTO variant_options (group_id, name, calc_type, price_usd_70x100, is_default, sort_order) VALUES
        (3, 'Mat Selefon (Su Geçirmez)', 'sheet_usd', 2.50, 1, 1),
        (3, 'Parlak Selefon (Canlı & Parlak)', 'sheet_usd', 2.50, 0, 2),
        (3, 'Kadife (Soft Touch) Selefon', 'sheet_usd', 4.50, 0, 3),
        (3, 'Kaplamasız (Doğal Doku)', 'sheet_usd', 0.00, 0, 4)");

        // Ekstra Efektler
        $db->exec("INSERT INTO variant_options (group_id, name, calc_type, fixed_usd, is_default, sort_order) VALUES
        (4, 'Ekstra Efekt Yok', 'fixed_usd', 0.00, 1, 1),
        (4, 'Altın Varak Yaldız (Klişe Dahil)', 'fixed_usd', 25.00, 0, 2),
        (4, 'Gümüş Varak Yaldız (Klişe Dahil)', 'fixed_usd', 25.00, 0, 3),
        (4, 'Kabartma / Gofre Baskı', 'fixed_usd', 20.00, 0, 4),
        (4, 'Lokal / Kısmi Parlak Lak', 'fixed_usd', 20.00, 0, 5)");

        echo "✔ Varsayılan varyant grupları ve seçenekleri eklendi.\n";
    }

    // Mevcut ürünlere bu grupları bağla (eğer boşsa)
    $db->exec("UPDATE products SET allowed_variant_groups = '[1,2,3,4]' WHERE allowed_variant_groups IS NULL OR allowed_variant_groups = '' OR allowed_variant_groups = '[]'");
    echo "✔ Tüm ürünlere varsayılan varyant grupları bağlandı.\n";

    echo "\n🎉 KURULUM VE GÜNCELLEME TAMAMLANDI!";
} catch (Exception $e) {
    echo "HATA: " . $e->getMessage();
}
echo "</pre>";
