<?php
require_once __DIR__ . '/config/config.php';

$db = Database::getInstance()->getConnection();
$log = [];

try {
    // 1. Products tablosuna fiyatlandırma modu sütunları ekle
    $colsToAdd = [
        "pricing_mode" => "VARCHAR(20) DEFAULT 'auto_m2'",
        "profit_margin_percent" => "DECIMAL(5,2) DEFAULT 50.00",
        "supplier_cost_1000" => "DECIMAL(10,2) DEFAULT NULL",
        "supplier_unit_m2_cost" => "DECIMAL(10,4) DEFAULT NULL",
        "manual_base_price" => "DECIMAL(10,2) DEFAULT NULL"
    ];

    foreach ($colsToAdd as $col => $type) {
        $check = $db->query("SHOW COLUMNS FROM products LIKE '$col'")->fetch();
        if (!$check) {
            $db->exec("ALTER TABLE products ADD COLUMN `$col` $type");
            $log[] = "Added column products.$col";
        } else {
            $log[] = "Column products.$col already exists.";
        }
    }

    // 2. Settings tablosuna genel fiyatlandırma modu ayarları ekle
    $settings = [
        'default_pricing_mode' => 'auto_m2',
        'global_profit_margin' => '50.00',
        'dealer_profit_margin' => '20.00',
        'base_offset_setup_fee' => '150.00' // Ofset kalıp & makine hazırlık taban maliyeti
    ];

    foreach ($settings as $k => $v) {
        $check = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $check->execute([$k]);
        if (!$check->fetch()) {
            $ins = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
            $ins->execute([$k, $v]);
            $log[] = "Inserted setting: $k = $v";
        } else {
            $log[] = "Setting $k exists.";
        }
    }

    echo json_encode(['success' => true, 'log' => $log], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
