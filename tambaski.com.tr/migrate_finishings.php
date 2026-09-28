<?php
require_once __DIR__ . '/config/config.php';

$db = Database::getInstance()->getConnection();

$cols = $db->query("DESCRIBE finishing_options")->fetchAll(PDO::FETCH_COLUMN);

$needed = [
    'calc_type'        => "ALTER TABLE finishing_options ADD COLUMN calc_type VARCHAR(30) NOT NULL DEFAULT 'sheet_usd' AFTER type",
    'percent_fee'      => "ALTER TABLE finishing_options ADD COLUMN percent_fee DECIMAL(5,2) NOT NULL DEFAULT 0.00 AFTER price_usd_70x100",
    'per_unit_fee_try' => "ALTER TABLE finishing_options ADD COLUMN per_unit_fee_try DECIMAL(8,2) NOT NULL DEFAULT 0.00 AFTER percent_fee",
    'fixed_fee_try'    => "ALTER TABLE finishing_options ADD COLUMN fixed_fee_try DECIMAL(8,2) NOT NULL DEFAULT 0.00 AFTER per_unit_fee_try"
];

foreach ($needed as $col => $sql) {
    if (!in_array($col, $cols)) {
        try {
            $db->exec($sql);
            echo "Added column $col\n";
        } catch (Exception $e) {
            echo "Error adding $col: " . $e->getMessage() . "\n";
        }
    } else {
        echo "Column $col already exists.\n";
    }
}

// Add sample/default variant options if they don't exist
$samples = [
    [
        'name' => 'Tek Yön Baskı',
        'type' => 'print_side',
        'calc_type' => 'percent',
        'price_usd_70x100' => 0.00,
        'percent_fee' => 0.00,
        'per_unit_fee_try' => 0.00,
        'fixed_fee_try' => 0.00,
        'fixed_fee_usd' => 0.00,
        'is_default' => 1
    ],
    [
        'name' => 'Çift Yön Baskı',
        'type' => 'print_side',
        'calc_type' => 'percent',
        'price_usd_70x100' => 0.00,
        'percent_fee' => 25.00,
        'per_unit_fee_try' => 0.00,
        'fixed_fee_try' => 0.00,
        'fixed_fee_usd' => 0.00,
        'is_default' => 0
    ],
    [
        'name' => 'Özel Bıçak Kesim Kalıbı',
        'type' => 'cut',
        'calc_type' => 'fixed_try',
        'price_usd_70x100' => 0.00,
        'percent_fee' => 0.00,
        'per_unit_fee_try' => 0.00,
        'fixed_fee_try' => 150.00,
        'fixed_fee_usd' => 0.00,
        'is_default' => 0
    ]
];

foreach ($samples as $s) {
    $chk = $db->prepare("SELECT id FROM finishing_options WHERE name = ?");
    $chk->execute([$s['name']]);
    if (!$chk->fetch()) {
        $ins = $db->prepare("INSERT INTO finishing_options (name, type, calc_type, price_usd_70x100, percent_fee, per_unit_fee_try, fixed_fee_try, fixed_fee_usd, is_default, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
        $ins->execute([$s['name'], $s['type'], $s['calc_type'], $s['price_usd_70x100'], $s['percent_fee'], $s['per_unit_fee_try'], $s['fixed_fee_try'], $s['fixed_fee_usd'], $s['is_default']]);
        echo "Inserted sample: {$s['name']}\n";
    }
}

echo "Migration finished successfully.\n";
