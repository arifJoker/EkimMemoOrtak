<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

header('Content-Type: text/plain; charset=utf-8');

try {
    require_once __DIR__ . '/config/config.php';
    $_SESSION['user_id'] = 1;
    $_SESSION['user_role'] = 'admin';

    // Mock Auth::user()
    ob_start();
    include __DIR__ . '/admin/products.php';
    $output = ob_get_clean();
    echo "SUCCESS! Rendered length: " . strlen($output) . "\n";
} catch (Throwable $e) {
    echo "EXCEPTION CAUGHT: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " on line " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
