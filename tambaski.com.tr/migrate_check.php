<?php
require_once __DIR__ . '/config/config.php';

$db = Database::getInstance()->getConnection();

$cols = $db->query("DESCRIBE products")->fetchAll(PDO::FETCH_COLUMN);

echo "Current columns in products:\n" . implode(", ", $cols) . "\n\n";

$needed = [
    'standard_width'     => "ALTER TABLE products ADD COLUMN standard_width DECIMAL(6,2) DEFAULT 8.40 AFTER tax_rate",
    'standard_height'    => "ALTER TABLE products ADD COLUMN standard_height DECIMAL(6,2) DEFAULT 5.20 AFTER standard_width",
    'extra_fixed_fee'    => "ALTER TABLE products ADD COLUMN extra_fixed_fee DECIMAL(10,2) DEFAULT 0.00 AFTER max_height",
    'extra_percent_fee'  => "ALTER TABLE products ADD COLUMN extra_percent_fee DECIMAL(5,2) DEFAULT 0.00 AFTER extra_fixed_fee",
    'allowed_papers'     => "ALTER TABLE products ADD COLUMN allowed_papers TEXT NULL AFTER extra_percent_fee",
    'allowed_finishings' => "ALTER TABLE products ADD COLUMN allowed_finishings TEXT NULL AFTER allowed_papers",
    'package_presets'    => "ALTER TABLE products ADD COLUMN package_presets TEXT NULL AFTER allowed_finishings",
    'allowed_templates'  => "ALTER TABLE products ADD COLUMN allowed_templates TEXT NULL AFTER package_presets"
];

foreach ($needed as $col => $sql) {
    if (!in_array($col, $cols)) {
        try {
            $db->exec($sql);
            echo "Added missing column: $col\n";
        } catch (Exception $e) {
            echo "Error adding $col: " . $e->getMessage() . "\n";
        }
    } else {
        echo "Column already exists: $col\n";
    }
}

// Check paper_types and finishing_options tables
try {
    $db->exec("CREATE TABLE IF NOT EXISTS paper_types (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        gsm INT NOT NULL,
        type VARCHAR(50) DEFAULT 'Kuşe',
        price_usd_70x100 DECIMAL(8,2) NOT NULL DEFAULT 6.00,
        is_default TINYINT(1) DEFAULT 0,
        status TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Checked paper_types table.\n";
} catch (Exception $e) {
    echo "paper_types table error: " . $e->getMessage() . "\n";
}

try {
    $db->exec("CREATE TABLE IF NOT EXISTS finishing_options (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        type VARCHAR(50) NOT NULL,
        price_usd_70x100 DECIMAL(8,2) NOT NULL DEFAULT 1.50,
        fixed_fee_usd DECIMAL(8,2) NOT NULL DEFAULT 0.00,
        is_default TINYINT(1) DEFAULT 0,
        status TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Checked finishing_options table.\n";
} catch (Exception $e) {
    echo "finishing_options table error: " . $e->getMessage() . "\n";
}
