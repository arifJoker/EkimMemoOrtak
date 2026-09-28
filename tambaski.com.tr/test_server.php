<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "PHP Version: " . phpversion() . "<br>";

require_once __DIR__ . '/config/config.php';
echo "Config loaded.<br>";

$db = Database::getInstance()->getConnection();
echo "DB Connected: " . ($db ? 'YES' : 'NO') . "<br>";

$p = $db->query("SELECT id, name FROM products LIMIT 1")->fetch();
echo "Sample product: " . json_encode($p) . "<br>";

$pModel = new Product();
$res = $pModel->calculatePrice(1, 500, [], 0, 0, false, 'standart');
echo "Calc result: " . json_encode($res) . "<br>";
