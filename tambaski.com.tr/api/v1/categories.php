<?php
/**
 * REST API v1 - Kategori Yönetimi
 */
require_once __DIR__ . '/auth_check.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getInstance()->getConnection();

if ($method === 'GET') {
    $stmt = $db->query("SELECT * FROM categories WHERE status = 1 ORDER BY sort_order ASC, id ASC");
    $cats = $stmt->fetchAll();
    Helper::jsonResponse(['success' => true, 'count' => count($cats), 'categories' => $cats]);
} elseif ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    if (empty($input['name'])) {
        Helper::jsonResponse(['success' => false, 'error' => 'Kategori adı ("name") zorunludur.'], 400);
    }
    $slug = !empty($input['slug']) ? Helper::slugify($input['slug']) : Helper::slugify($input['name']);
    $stmt = $db->prepare("INSERT INTO categories (parent_id, name, slug, description, image, icon, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $input['parent_id'] ?? 0,
        $input['name'],
        $slug,
        $input['description'] ?? '',
        $input['image'] ?? null,
        $input['icon'] ?? 'bi bi-grid',
        $input['sort_order'] ?? 0
    ]);
    Helper::jsonResponse(['success' => true, 'category_id' => $db->lastInsertId(), 'slug' => $slug], 201);
} else {
    Helper::jsonResponse(['success' => false, 'error' => 'Geçersiz metod.'], 405);
}
