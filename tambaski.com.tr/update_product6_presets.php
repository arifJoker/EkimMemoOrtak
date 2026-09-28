<?php
require_once __DIR__ . '/config/config.php';
$db = Database::getInstance()->getConnection();

$presets = json_encode([
    'ekonomik' => [
        'active'             => 1,
        'supplier_cost_1000' => 220.00,
        'paper_id'           => 0,
        'fin_id'             => 0,
        'desc'               => '250gr Bristol, Tek Yön Düz Baskı'
    ],
    'standart' => [
        'active'             => 1,
        'supplier_cost_1000' => 400.00,
        'paper_id'           => 0,
        'fin_id'             => 0,
        'desc'               => '350gr Kuşe, Çift Taraf Mat Selefon'
    ],
    'premium' => [
        'active'             => 1,
        'supplier_cost_1000' => 650.00,
        'paper_id'           => 0,
        'fin_id'             => 0,
        'desc'               => 'Soft-Touch Kadife Selefon & Kabartma Lak'
    ],
    'vip' => [
        'active'             => 1,
        'supplier_cost_1000' => 950.00,
        'paper_id'           => 0,
        'fin_id'             => 0,
        'desc'               => 'Tuale Fantezi / 24K Altın Varak Yaldız'
    ]
], JSON_UNESCAPED_UNICODE);

$db->prepare("UPDATE products SET package_presets = ?, supplier_cost_1000 = 220.00, profit_margin_percent = 100.00, extra_fixed_fee = 0.00 WHERE id = 6")->execute([$presets]);

echo "Updated product 6 presets!";
