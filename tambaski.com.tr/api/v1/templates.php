<?php
/**
 * REST API v1 - Vektörel Şablon Yönetimi
 */
require_once __DIR__ . '/auth_check.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getInstance()->getConnection();

if ($method === 'GET') {
    $productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : null;
    $sql = "SELECT * FROM design_templates WHERE status = 1";
    $params = [];
    if ($productId) {
        $sql .= " AND product_id = ?";
        $params[] = $productId;
    }
    $sql .= " ORDER BY sort_order ASC, id DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $templates = $stmt->fetchAll();
    Helper::jsonResponse(['success' => true, 'count' => count($templates), 'templates' => $templates]);
} elseif ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    if (empty($input['product_id']) || empty($input['title']) || empty($input['default_svg'])) {
        Helper::jsonResponse(['success' => false, 'error' => '"product_id", "title" ve "default_svg" alanları zorunludur.'], 400);
    }
    $slug = !empty($input['slug']) ? Helper::slugify($input['slug']) : Helper::slugify($input['title']);
    $stmt = $db->prepare("INSERT INTO design_templates (product_id, title, slug, thumbnail, category, canvas_width, canvas_height, default_svg, template_data, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        (int)$input['product_id'],
        $input['title'],
        $slug,
        $input['thumbnail'] ?? null,
        $input['category'] ?? 'Genel',
        $input['canvas_width'] ?? 850,
        $input['canvas_height'] ?? 500,
        $input['default_svg'],
        !empty($input['fields']) ? json_encode(['fields' => $input['fields']]) : ($input['template_data'] ?? null),
        $input['sort_order'] ?? 0
    ]);
    Helper::jsonResponse(['success' => true, 'template_id' => $db->lastInsertId()], 201);
} else {
    Helper::jsonResponse(['success' => false, 'error' => 'Geçersiz metod.'], 405);
}
