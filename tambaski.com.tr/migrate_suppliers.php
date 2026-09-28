<?php
// Migration for Multi-Supplier Management and Supplier Price Items
require_once __DIR__ . '/config/config.php';
$db = Database::getInstance()->getConnection();

try {
    // 1. suppliers tablosu
    $db->exec("CREATE TABLE IF NOT EXISTS `suppliers` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(150) NOT NULL,
        `code` VARCHAR(100) NOT NULL UNIQUE,
        `contact_info` VARCHAR(255) NULL,
        `default_margin` DECIMAL(5,2) DEFAULT 50.00,
        `status` TINYINT(1) DEFAULT 1,
        `sort_order` INT DEFAULT 0,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Varsayılan tedarikçileri ekle
    $db->exec("INSERT INTO `suppliers` (`id`, `name`, `code`, `contact_info`, `default_margin`, `status`, `sort_order`) VALUES
        (1, 'Türmatsan', 'turmatsan', 'www.turmatsan.com', 100.00, 1, 1),
        (2, 'Net Matbaa & Promosyon', 'net_matbaa', 'İstanbul Matbaacılar Sitesi', 80.00, 1, 2),
        (3, 'Atölyemiz / Kendi Üretimimiz', 'in_house', 'Özel Üretim Atölyesi', 60.00, 1, 3)
    ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);");

    // 2. supplier_price_items tablosu (Tedarikçi fiyat listesi hafızası)
    $db->exec("CREATE TABLE IF NOT EXISTS `supplier_price_items` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `supplier_id` INT NOT NULL,
        `category` VARCHAR(100) NOT NULL,
        `name` VARCHAR(200) NOT NULL,
        `cost_1000` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        `cost_2000` DECIMAL(10,2) NULL DEFAULT 0.00,
        `cost_3000` DECIMAL(10,2) NULL DEFAULT 0.00,
        `cost_5000` DECIMAL(10,2) NULL DEFAULT 0.00,
        `cost_10000` DECIMAL(10,2) NULL DEFAULT 0.00,
        `notes` VARCHAR(255) NULL,
        `status` TINYINT(1) DEFAULT 1,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX (`supplier_id`),
        INDEX (`category`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Örnek Türmatsan Kalemleri Ekle
    $db->exec("INSERT INTO `supplier_price_items` (`id`, `supplier_id`, `category`, `name`, `cost_1000`, `cost_2000`, `cost_5000`, `notes`) VALUES
        (1, 1, 'Kartvizit', '350gr Kuşe Çift Yön Mat Selefon (Düz)', 450.00, 765.00, 1640.00, 'Standart ofset kartvizit'),
        (2, 1, 'Kartvizit', '350gr Kuşe Çift Yön Mat Selefon + Kabartma Lak', 650.00, 1150.00, 2500.00, 'Kısmi kabartma lak uygulamalı'),
        (3, 1, 'Kartvizit', '350gr Kuşe Çift Yön + 24K Altın Varak Yaldız', 950.00, 1700.00, 3700.00, 'Altın / Gümüş varak yaldızlı'),
        (4, 1, 'Kartvizit', '250gr Amerikan Bristol Tek Yön Renkli', 350.00, 600.00, 1300.00, 'Ekonomik tek yön kartvizit'),
        (5, 1, 'Kartvizit', '700gr Sıvama Ekstra Kalın Kartvizit', 1400.00, 2500.00, 5500.00, 'Özel mukavva sıvamalı'),
        (6, 1, 'Broşür', 'A5 Broşür 130gr Kuşe Çift Yön Renkli', 700.00, 1200.00, 2600.00, 'A5 standart broşür'),
        (7, 1, 'Broşür', 'A4 Broşür 130gr Kuşe Çift Yön Renkli', 1350.00, 2300.00, 4900.00, 'A4 standart broşür'),
        (8, 1, 'Kurumsal', 'Cepli Sunum Dosyası 350gr Mat Kuşe', 2200.00, 3900.00, 8500.00, 'Kartvizit yuvalı cepli dosya'),
        (9, 1, 'Magnet', 'Standart Magnet 0.4mm (7x5 cm)', 600.00, 1100.00, 2400.00, 'Buzdolabı magneti')
    ON DUPLICATE KEY UPDATE `cost_1000`=VALUES(`cost_1000`);");

    // 3. products tablosuna supplier_id ve supplier_item_id ekle
    $cols = $db->query("SHOW COLUMNS FROM products")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('supplier_id', $cols)) {
        $db->exec("ALTER TABLE products ADD COLUMN supplier_id INT DEFAULT 1 AFTER pricing_mode;");
    }
    if (!in_array('supplier_item_id', $cols)) {
        $db->exec("ALTER TABLE products ADD COLUMN supplier_item_id INT DEFAULT 0 AFTER supplier_id;");
    }

    echo "Migration completed successfully!";
} catch (Exception $e) {
    echo "Migration error: " . $e->getMessage();
}
