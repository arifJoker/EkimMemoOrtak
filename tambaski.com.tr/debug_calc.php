<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config/config.php';

try {
    $productModel = new Product();
    $res = $productModel->calculatePrice(1, 500, [], 0, 0, false, 'standart');
    echo json_encode($res);
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
