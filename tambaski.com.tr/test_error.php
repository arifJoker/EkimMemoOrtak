<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config/config.php';
// Mock admin session
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'admin';

include __DIR__ . '/admin/products.php';
