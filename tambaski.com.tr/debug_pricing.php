<?php
require_once __DIR__ . '/config/config.php';

$db = Database::getInstance()->getConnection();

$prod = $db->query("SELECT * FROM products WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
echo "PACKAGE PRESETS:\n" . $prod['package_presets'] . "\n\n";

$tiers = $db->query("SELECT * FROM product_quantity_tiers WHERE product_id = 1 ORDER BY quantity ASC")->fetchAll(PDO::FETCH_ASSOC);
echo "TIERS:\n";
print_r($tiers);

$papers = $db->query("SELECT * FROM paper_types")->fetchAll(PDO::FETCH_ASSOC);
echo "\nPAPERS:\n";
print_r($papers);

$fin = $db->query("SELECT * FROM finishing_options")->fetchAll(PDO::FETCH_ASSOC);
echo "\nFINISHINGS:\n";
print_r($fin);
