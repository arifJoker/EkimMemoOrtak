<?php
require_once __DIR__ . '/config/config.php';

$db = Database::getInstance()->getConnection();

// 1. variant_groups tablosu
$db->exec("CREATE TABLE IF NOT EXISTS variant_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    description VARCHAR(255) NULL,
    input_type VARCHAR(30) DEFAULT 'radio',
    is_required TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 2. variant_options tablosu
$db->exec("CREATE TABLE IF NOT EXISTS variant_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    calc_type VARCHAR(30) NOT NULL DEFAULT 'per_unit_try',
    price_usd_70x100 DECIMAL(8,2) DEFAULT 0.00,
    fixed_fee_usd DECIMAL(8,2) DEFAULT 0.00,
    percent_fee DECIMAL(5,2) DEFAULT 0.00,
    per_unit_fee_try DECIMAL(8,2) DEFAULT 0.00,
    fixed_fee_try DECIMAL(8,2) DEFAULT 0.00,
    is_default TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(group_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 3. product_variant_groups tablosu
$db->exec("CREATE TABLE IF NOT EXISTS product_variant_groups (
    product_id INT NOT NULL,
    group_id INT NOT NULL,
    PRIMARY KEY(product_id, group_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Sütun ekleme (products tablosuna allowed_variant_groups sütunu)
$pCols = $db->query("DESCRIBE products")->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('allowed_variant_groups', $pCols)) {
    $db->exec("ALTER TABLE products ADD COLUMN allowed_variant_groups TEXT NULL AFTER allowed_templates");
}

// Varsayılan Grupları ve Seçenekleri Tohumla (Seed)
$defaultGroups = [
    [
        'name' => 'Kesim Türü',
        'slug' => 'kesim-turu',
        'description' => 'Köşe ve bıçak kesim seçenekleri',
        'input_type' => 'radio',
        'is_required' => 1,
        'sort_order' => 1,
        'options' => [
            ['name' => 'Düz Kesim (90° Standart)', 'calc_type' => 'per_unit_try', 'val' => 0.00, 'is_default' => 1],
            ['name' => 'Oval Radyus Köşe Kesim', 'calc_type' => 'per_unit_try', 'val' => 0.25, 'is_default' => 0],
            ['name' => 'Özel Bıçak Kesim Kalıbı', 'calc_type' => 'fixed_try', 'val' => 150.00, 'is_default' => 0],
            ['name' => 'Perforaj (Koparma Çizgisi)', 'calc_type' => 'per_unit_try', 'val' => 0.15, 'is_default' => 0],
            ['name' => 'Pilyaj (Katlama Çizgisi)', 'calc_type' => 'per_unit_try', 'val' => 0.15, 'is_default' => 0]
        ]
    ],
    [
        'name' => 'Baskı Yönü',
        'slug' => 'baski-yonu',
        'description' => 'Tek taraf veya çift taraf renkli ofset baskı',
        'input_type' => 'radio',
        'is_required' => 1,
        'sort_order' => 2,
        'options' => [
            ['name' => 'Tek Yön Renkli Baskı', 'calc_type' => 'percent', 'val' => 0.00, 'is_default' => 1],
            ['name' => 'Çift Yön Renkli Baskı', 'calc_type' => 'percent', 'val' => 25.00, 'is_default' => 0]
        ]
    ],
    [
        'name' => 'Yüzey Kaplama / Selefon',
        'slug' => 'selefon-kaplama',
        'description' => 'Su ve neme karşı koruyucu yüzey selefonu',
        'input_type' => 'radio',
        'is_required' => 1,
        'sort_order' => 3,
        'options' => [
            ['name' => 'Selefonsuz (Doğal Kağıt)', 'calc_type' => 'sheet_usd', 'val' => 0.00, 'is_default' => 0],
            ['name' => 'Mat Selefon (Tek Yön)', 'calc_type' => 'sheet_usd', 'val' => 1.50, 'is_default' => 1],
            ['name' => 'Mat Selefon (Çift Yön)', 'calc_type' => 'sheet_usd', 'val' => 2.50, 'is_default' => 0],
            ['name' => 'Parlak Selefon', 'calc_type' => 'sheet_usd', 'val' => 1.50, 'is_default' => 0],
            ['name' => 'Soft-Touch Kadife Selefon', 'calc_type' => 'sheet_usd', 'val' => 4.00, 'is_default' => 0]
        ]
    ],
    [
        'name' => 'Ekstra Efekt & Yaldız',
        'slug' => 'ekstra-efekt',
        'description' => 'Kabartma lak, sıcak yaldız ve fantezi işlemler',
        'input_type' => 'radio',
        'is_required' => 0,
        'sort_order' => 4,
        'options' => [
            ['name' => 'Standart (Ekstra Efektsiz)', 'calc_type' => 'sheet_usd', 'val' => 0.00, 'is_default' => 1],
            ['name' => '3D Parlak Kabartma Lak (Spot UV)', 'calc_type' => 'sheet_usd', 'val' => 3.00, 'fixed_usd' => 10.00, 'is_default' => 0],
            ['name' => '24K Sıcak Altın Varak Yaldız', 'calc_type' => 'sheet_usd', 'val' => 5.00, 'fixed_usd' => 20.00, 'is_default' => 0],
            ['name' => 'Gümüş Varak Yaldız Baskı', 'calc_type' => 'sheet_usd', 'val' => 5.00, 'fixed_usd' => 20.00, 'is_default' => 0],
            ['name' => 'Gofre Kabartma', 'calc_type' => 'fixed_try', 'val' => 200.00, 'is_default' => 0]
        ]
    ]
];

foreach ($defaultGroups as $g) {
    $chkG = $db->prepare("SELECT id FROM variant_groups WHERE slug = ?");
    $chkG->execute([$g['slug']]);
    $gId = $chkG->fetchColumn();

    if (!$gId) {
        $insG = $db->prepare("INSERT INTO variant_groups (name, slug, description, input_type, is_required, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, 1)");
        $insG->execute([$g['name'], $g['slug'], $g['description'], $g['input_type'], $g['is_required'], $g['sort_order']]);
        $gId = $db->lastInsertId();
        echo "Created Group: {$g['name']}\n";
    }

    foreach ($g['options'] as $oIdx => $opt) {
        $chkO = $db->prepare("SELECT id FROM variant_options WHERE group_id = ? AND name = ?");
        $chkO->execute([$gId, $opt['name']]);
        if (!$chkO->fetchColumn()) {
            $cType = $opt['calc_type'];
            $priceUsd = ($cType === 'sheet_usd') ? $opt['val'] : 0.00;
            $percent = ($cType === 'percent') ? $opt['val'] : 0.00;
            $perUnit = ($cType === 'per_unit_try') ? $opt['val'] : 0.00;
            $fixedTry = ($cType === 'fixed_try') ? $opt['val'] : 0.00;
            $fixedUsd = $opt['fixed_usd'] ?? 0.00;

            $insO = $db->prepare("INSERT INTO variant_options (group_id, name, calc_type, price_usd_70x100, fixed_fee_usd, percent_fee, per_unit_fee_try, fixed_fee_try, is_default, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
            $insO->execute([$gId, $opt['name'], $cType, $priceUsd, $fixedUsd, $percent, $perUnit, $fixedTry, $opt['is_default'], $oIdx]);
            echo "  - Added option: {$opt['name']}\n";
        }
    }
}

echo "Variant Groups and Options database setup completed!\n";
