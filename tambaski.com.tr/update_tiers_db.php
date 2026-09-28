<?php
require_once __DIR__ . '/config/config.php';

$db = Database::getInstance()->getConnection();

// Ürün 1 (ve varsa diğer tüm ürünler) için adet kademelerini 1.000 ve katlarına güncelle
$products = $db->query("SELECT id FROM products")->fetchAll();

foreach ($products as $p) {
    $pId = (int)$p['id'];
    
    // Eski kademeleri temizle
    $del = $db->prepare("DELETE FROM product_quantity_tiers WHERE product_id = ?");
    $del->execute([$pId]);

    // Yeni 1.000 ve katları kademelerini ekle
    $tiers = [
        ['qty' => 1000, 'mul' => 1.0000, 'disc' => 0.00,  'sort' => 1],
        ['qty' => 2000, 'mul' => 1.7000, 'disc' => 15.00, 'sort' => 2],
        ['qty' => 3000, 'mul' => 2.3500, 'disc' => 22.00, 'sort' => 3],
        ['qty' => 5000, 'mul' => 3.5000, 'disc' => 30.00, 'sort' => 4],
        ['qty' => 10000, 'mul' => 6.2000, 'disc' => 38.00, 'sort' => 5]
    ];

    $ins = $db->prepare("INSERT INTO product_quantity_tiers (product_id, quantity, multiplier, discount_percent, sort_order) VALUES (?, ?, ?, ?, ?)");
    foreach ($tiers as $t) {
        $ins->execute([$pId, $t['qty'], $t['mul'], $t['disc'], $t['sort']]);
    }
}

echo "SUCCESS: Product quantity tiers updated to 1.000 and multiples (1.000, 2.000, 3.000, 5.000, 10.000) for " . count($products) . " products.";
