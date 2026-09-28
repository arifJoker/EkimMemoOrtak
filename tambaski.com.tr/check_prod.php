<?php
require_once __DIR__ . '/config/config.php';
$db->exec("UPDATE products SET allowed_templates = NULL WHERE id = 1");
echo "Updated product 1 allowed_templates to NULL\n";

$productModel = new Product();
$p = $productModel->getBySlug('test-kasa');
echo "Loaded test-kasa templates count: " . count($p['templates']) . "\n";
foreach ($p['templates'] as $t) {
    echo "- ID: {$t['id']}, Title: {$t['title']}, Industry: {$t['industry_slug']}\n";
}
