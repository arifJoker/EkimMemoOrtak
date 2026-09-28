<?php
// Türmatsan Standart Matbaa Varyantları Temizleme ve Yeniden Yükleme Scripti
require_once __DIR__ . '/config/config.php';
$db = Database::getInstance()->getConnection();

try {
    // 1. variant_options tablosuna supplier_cost_1000 ekle
    $cols = $db->query("SHOW COLUMNS FROM variant_options")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('supplier_cost_1000', $cols)) {
        $db->exec("ALTER TABLE variant_options ADD COLUMN supplier_cost_1000 DECIMAL(10,2) DEFAULT 0.00 AFTER price_usd_70x100;");
    }

    // 2. Eski tüm varyant gruplarını ve seçeneklerini temizle
    $db->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $db->exec("TRUNCATE TABLE variant_options;");
    $db->exec("TRUNCATE TABLE variant_groups;");
    $db->exec("SET FOREIGN_KEY_CHECKS = 1;");

    // 3. Resmi Türmatsan Standart Varyant Gruplarını Ekle
    $groups = [
        [
            'id' => 1,
            'name' => 'Kağıt Türü & Gramajı',
            'slug' => 'kagit-turu-ve-gramaji',
            'description' => 'Baskıda kullanılacak kağıt kalitesi ve kalınlığı',
            'input_type' => 'radio',
            'is_required' => 1,
            'sort_order' => 1,
            'options' => [
                ['name' => '350 gr. Mat Kuşe (Standart)', 'calc_type' => 'per_unit_try', 'per_unit_fee_try' => 0.00, 'supplier_cost_1000' => 0.00, 'is_default' => 1, 'sort' => 1],
                ['name' => '350 gr. Parlak Kuşe', 'calc_type' => 'per_unit_try', 'per_unit_fee_try' => 0.00, 'supplier_cost_1000' => 0.00, 'is_default' => 0, 'sort' => 2],
                ['name' => '250 gr. Amerikan Bristol', 'calc_type' => 'per_unit_try', 'per_unit_fee_try' => -0.10, 'supplier_cost_1000' => -100.00, 'is_default' => 0, 'sort' => 3],
                ['name' => '300 gr. İtalyan Tuale (Fantezi Dokulu)', 'calc_type' => 'per_unit_try', 'per_unit_fee_try' => 0.35, 'supplier_cost_1000' => 350.00, 'is_default' => 0, 'sort' => 4],
                ['name' => '700 gr. Sıvama Mukavva (Ekstra Kalın Lüks)', 'calc_type' => 'per_unit_try', 'per_unit_fee_try' => 0.95, 'supplier_cost_1000' => 950.00, 'is_default' => 0, 'sort' => 5],
            ]
        ],
        [
            'id' => 2,
            'name' => 'Baskı Yönü',
            'slug' => 'baski-yonu',
            'description' => 'Tek veya çift taraf renkli ofset baskı seçimi',
            'input_type' => 'radio',
            'is_required' => 1,
            'sort_order' => 2,
            'options' => [
                ['name' => 'Çift Yön Renkli Baskı (Ön & Arka)', 'calc_type' => 'per_unit_try', 'per_unit_fee_try' => 0.00, 'supplier_cost_1000' => 0.00, 'is_default' => 1, 'sort' => 1],
                ['name' => 'Tek Yön Renkli Baskı (Sadece Ön)', 'calc_type' => 'per_unit_try', 'per_unit_fee_try' => -0.03, 'supplier_cost_1000' => -30.00, 'is_default' => 0, 'sort' => 2],
            ]
        ],
        [
            'id' => 3,
            'name' => 'Selefon & Yüzey Kaplama',
            'slug' => 'selefon-ve-yuzey-kaplama',
            'description' => 'Koruyucu mat, parlak veya kadife yüzey laminasyonu',
            'input_type' => 'radio',
            'is_required' => 1,
            'sort_order' => 3,
            'options' => [
                ['name' => 'Çift Taraf Mat Selefon (Standart)', 'calc_type' => 'per_unit_try', 'per_unit_fee_try' => 0.00, 'supplier_cost_1000' => 0.00, 'is_default' => 1, 'sort' => 1],
                ['name' => 'Çift Taraf Parlak Selefon', 'calc_type' => 'per_unit_try', 'per_unit_fee_try' => 0.00, 'supplier_cost_1000' => 0.00, 'is_default' => 0, 'sort' => 2],
                ['name' => 'Soft-Touch Kadife Selefon', 'calc_type' => 'per_unit_try', 'per_unit_fee_try' => 0.15, 'supplier_cost_1000' => 150.00, 'is_default' => 0, 'sort' => 3],
                ['name' => 'Selefonsuz (Düz)', 'calc_type' => 'per_unit_try', 'per_unit_fee_try' => 0.00, 'supplier_cost_1000' => 0.00, 'is_default' => 0, 'sort' => 4],
            ]
        ],
        [
            'id' => 4,
            'name' => 'Ekstra Efekt & İşçilik (Lak & Yaldız)',
            'slug' => 'ekstra-efekt-ve-iscilik',
            'description' => 'Logonuzu parlatan 3D kabartma lak veya 24K altın yaldız',
            'input_type' => 'radio',
            'is_required' => 1,
            'sort_order' => 4,
            'options' => [
                ['name' => 'Efekt Yok (Standart Ofset Baskı)', 'calc_type' => 'per_unit_try', 'per_unit_fee_try' => 0.00, 'supplier_cost_1000' => 0.00, 'is_default' => 1, 'sort' => 1],
                ['name' => 'Kısmi Kabartma Lak (Tek Yüz)', 'calc_type' => 'per_unit_try', 'per_unit_fee_try' => 0.20, 'supplier_cost_1000' => 200.00, 'is_default' => 0, 'sort' => 2],
                ['name' => '24K Altın Varak Yaldız', 'calc_type' => 'per_unit_try', 'per_unit_fee_try' => 0.50, 'supplier_cost_1000' => 500.00, 'is_default' => 0, 'sort' => 3],
                ['name' => 'Gümüş Varak Yaldız', 'calc_type' => 'per_unit_try', 'per_unit_fee_try' => 0.50, 'supplier_cost_1000' => 500.00, 'is_default' => 0, 'sort' => 4],
                ['name' => 'Kabartma Lak + Altın Varak Yaldız', 'calc_type' => 'per_unit_try', 'per_unit_fee_try' => 0.65, 'supplier_cost_1000' => 650.00, 'is_default' => 0, 'sort' => 5],
            ]
        ],
        [
            'id' => 5,
            'name' => 'Köşe & Bıçak Kesimi',
            'slug' => 'kose-ve-bicak-kesimi',
            'description' => 'Köşe radyusları ve özel şekilli kesimler',
            'input_type' => 'radio',
            'is_required' => 1,
            'sort_order' => 5,
            'options' => [
                ['name' => 'Standart Düz Kesim (90°)', 'calc_type' => 'per_unit_try', 'per_unit_fee_try' => 0.00, 'supplier_cost_1000' => 0.00, 'is_default' => 1, 'sort' => 1],
                ['name' => '4 Köşe Oval Radyus Kesimli', 'calc_type' => 'per_unit_try', 'per_unit_fee_try' => 0.05, 'supplier_cost_1000' => 50.00, 'is_default' => 0, 'sort' => 2],
                ['name' => 'Özel Bıçak Kesimli (Şekilli)', 'calc_type' => 'fixed_try', 'fixed_fee_try' => 150.00, 'supplier_cost_1000' => 150.00, 'is_default' => 0, 'sort' => 3],
            ]
        ],
        [
            'id' => 6,
            'name' => 'Kırım & Katlama Türü',
            'slug' => 'kirim-ve-katlama-turu',
            'description' => 'Broşür ve el ilanları için katlama seçenekleri',
            'input_type' => 'radio',
            'is_required' => 1,
            'sort_order' => 6,
            'options' => [
                ['name' => 'Katlamasız (Düz Açık)', 'calc_type' => 'per_unit_try', 'per_unit_fee_try' => 0.00, 'supplier_cost_1000' => 0.00, 'is_default' => 1, 'sort' => 1],
                ['name' => 'Tek Kırım (Ortadan 2\'ye Katlama)', 'calc_type' => 'fixed_try', 'fixed_fee_try' => 80.00, 'supplier_cost_1000' => 80.00, 'is_default' => 0, 'sort' => 2],
                ['name' => 'Z Kırım (Akordiyon 3 Kırımlı)', 'calc_type' => 'fixed_try', 'fixed_fee_try' => 120.00, 'supplier_cost_1000' => 120.00, 'is_default' => 0, 'sort' => 3],
                ['name' => 'İçe Katlama (Mektup Katlama)', 'calc_type' => 'fixed_try', 'fixed_fee_try' => 120.00, 'supplier_cost_1000' => 120.00, 'is_default' => 0, 'sort' => 4],
            ]
        ]
    ];

    foreach ($groups as $g) {
        $gStmt = $db->prepare("INSERT INTO variant_groups (id, name, slug, description, input_type, is_required, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
        $gStmt->execute([$g['id'], $g['name'], $g['slug'], $g['description'], $g['input_type'], $g['is_required'], $g['sort_order']]);

        foreach ($g['options'] as $opt) {
            $oStmt = $db->prepare("INSERT INTO variant_options (group_id, name, calc_type, per_unit_fee_try, fixed_fee_try, supplier_cost_1000, is_default, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
            $oStmt->execute([
                $g['id'],
                $opt['name'],
                $opt['calc_type'],
                $opt['per_unit_fee_try'] ?? 0.00,
                $opt['fixed_fee_try'] ?? 0.00,
                $opt['supplier_cost_1000'] ?? 0.00,
                $opt['is_default'],
                $opt['sort']
            ]);
        }
    }

    // 4. Tüm aktif ürünlerin allowed_variant_groups ayarını güncelle
    $groupJson = json_encode([1, 2, 3, 4, 5, 6]);
    $db->exec("UPDATE products SET allowed_variant_groups = '$groupJson' WHERE allowed_variant_groups IS NULL OR allowed_variant_groups = '' OR allowed_variant_groups = '[]'");

    echo "SUCCESS: All Turmatsan variants seeded successfully!";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
