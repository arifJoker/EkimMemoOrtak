<?php
require_once __DIR__ . '/config/config.php';
$db = Database::getInstance()->getConnection();

// 1. paper_types
$db->exec("CREATE TABLE IF NOT EXISTS paper_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    gsm INT NOT NULL DEFAULT 350,
    price_usd_70x100 DECIMAL(10,2) NOT NULL DEFAULT 7.00,
    is_default TINYINT(1) DEFAULT 0,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Insert default papers if empty
$chk = $db->query("SELECT COUNT(*) FROM paper_types")->fetchColumn();
if ($chk == 0) {
    $db->exec("INSERT INTO paper_types (name, gsm, price_usd_70x100, is_default) VALUES 
    ('135 gr. Kuşe Kağıt', 135, 5.00, 0),
    ('250 gr. Kuşe Kağıt', 250, 6.00, 0),
    ('350 gr. Kuşe Kağıt', 350, 7.00, 1),
    ('350 gr. İtalyan Tuale Fantezi', 350, 14.00, 0);");
}

// 2. finishing_options
$db->exec("CREATE TABLE IF NOT EXISTS finishing_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    type VARCHAR(50) NOT NULL DEFAULT 'lamination',
    price_usd_70x100 DECIMAL(10,2) NOT NULL DEFAULT 1.50,
    fixed_fee_usd DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$chk2 = $db->query("SELECT COUNT(*) FROM finishing_options")->fetchColumn();
if ($chk2 == 0) {
    $db->exec("INSERT INTO finishing_options (name, type, price_usd_70x100, fixed_fee_usd) VALUES 
    ('Mat Selefon (Tek Yön)', 'lamination', 1.50, 0.00),
    ('Mat Selefon (Çift Yön)', 'lamination', 2.50, 0.00),
    ('Parlak Selefon', 'lamination', 1.50, 0.00),
    ('Soft-Touch Kadife Selefon', 'lamination', 4.00, 0.00),
    ('3D Parlak Kabartma Lak (Spot UV)', 'uv', 3.00, 10.00),
    ('24K Sıcak Altın Varak Yaldız', 'foil', 5.00, 20.00),
    ('Oval Köşe Kesimi (Radyus)', 'cut', 0.50, 0.00);");
}

// 3. Add columns to products if not exist
$cols = $db->query("SHOW COLUMNS FROM products")->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('standard_width', $cols)) {
    $db->exec("ALTER TABLE products ADD COLUMN standard_width DECIMAL(6,2) DEFAULT 8.40 AFTER price_per_sqm;");
}
if (!in_array('standard_height', $cols)) {
    $db->exec("ALTER TABLE products ADD COLUMN standard_height DECIMAL(6,2) DEFAULT 5.20 AFTER standard_width;");
}
if (!in_array('extra_fixed_fee', $cols)) {
    $db->exec("ALTER TABLE products ADD COLUMN extra_fixed_fee DECIMAL(10,2) DEFAULT 0.00 AFTER standard_height;");
}
if (!in_array('extra_percent_fee', $cols)) {
    $db->exec("ALTER TABLE products ADD COLUMN extra_percent_fee DECIMAL(5,2) DEFAULT 0.00 AFTER extra_fixed_fee;");
}
if (!in_array('allowed_papers', $cols)) {
    $db->exec("ALTER TABLE products ADD COLUMN allowed_papers TEXT NULL AFTER extra_percent_fee;");
}
if (!in_array('allowed_finishings', $cols)) {
    $db->exec("ALTER TABLE products ADD COLUMN allowed_finishings TEXT NULL AFTER allowed_papers;");
}
if (!in_array('package_presets', $cols)) {
    $db->exec("ALTER TABLE products ADD COLUMN package_presets TEXT NULL AFTER allowed_finishings;");
}

// 4. Default settings
Helper::saveSetting('usd_try_rate', '38.50');
Helper::saveSetting('cutting_labor_percent', '5');
Helper::saveSetting('sheet_width', '70');
Helper::saveSetting('sheet_height', '100');
Helper::saveSetting('gift_waste_threshold', '2');

echo "MIGRATION_SUCCESS\n";
