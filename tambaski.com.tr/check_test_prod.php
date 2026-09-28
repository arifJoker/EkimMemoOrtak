<?php
require_once __DIR__ . '/config/config.php';
header('Content-Type: application/json; charset=utf-8');

$all = $db->query("SELECT id, title, industry_slug, status FROM design_templates LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);
$total = $db->query("SELECT COUNT(*) FROM design_templates")->fetchColumn();
$statusGroups = $db->query("SELECT status, COUNT(*) as cnt FROM design_templates GROUP BY status")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'total_templates' => $total,
    'status_groups' => $statusGroups,
    'sample' => $all
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
