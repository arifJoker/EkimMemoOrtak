<?php
require_once __DIR__ . '/config/config.php';
header('Content-Type: application/json; charset=utf-8');

$pModel = new Product();
$pTest = $pModel->getBySlug('test');
$pKart = $pModel->getBySlug('luks-kabartma-lakli-kartvizit');
$pBrosur = $pModel->getBySlug('a5-renkli-el-ilani-brosur');

echo json_encode([
    'test_product' => [
        'name' => $pTest['name'] ?? '',
        'category_slug' => $pTest['category_slug'] ?? '',
        'templates_count' => count($pTest['templates'] ?? [])
    ],
    'luks_kartvizit' => [
        'name' => $pKart['name'] ?? '',
        'category_slug' => $pKart['category_slug'] ?? '',
        'templates_count' => count($pKart['templates'] ?? [])
    ],
    'brosur_product' => [
        'name' => $pBrosur['name'] ?? '',
        'category_slug' => $pBrosur['category_slug'] ?? '',
        'templates_count' => count($pBrosur['templates'] ?? [])
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
