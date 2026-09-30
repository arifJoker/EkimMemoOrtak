<?php
/**
 * REST API v1 - Kategori Yönetimi ve Şema Bilgisi
 */
require_once __DIR__ . '/auth_check.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getInstance()->getConnection();

if ($method === 'GET') {
    $stmt = $db->query("SELECT id, parent_id, name, slug, icon, pricing_model, sort_order, status FROM categories WHERE status = 1 ORDER BY sort_order ASC, id ASC");
    $rawCats = $stmt->fetchAll();

    $categories = array_map(function($cat) {
        $model = $cat['pricing_model'] ?: 'package_tier';
        $rules = [
            'pricing_model' => $model,
            'model_title'   => '',
            'required_fields' => ['category_id', 'name', 'base_price'],
            'optional_fields' => ['short_description', 'full_description', 'featured_image', 'mockup_image', 'status']
        ];

        switch ($model) {
            case 'rigid_board':
                $rules['model_title'] = 'Sert Zemin & Levha Modeli (Dekota / İSG / Pleksi)';
                $rules['required_fields'] = array_merge($rules['required_fields'], ['m2_usd_price_3mm']);
                $rules['optional_fields'] = array_merge($rules['optional_fields'], ['m2_usd_price_5mm', 'm2_usd_price_9mm', 'allow_online_editor']);
                break;
            case 'm2_calculator':
                $rules['model_title'] = 'Dinamik Metrekare Modeli (Folyo / Araç Sticker)';
                $rules['required_fields'] = array_merge($rules['required_fields'], ['price_per_sqm']);
                break;
            case 'tiered_qty':
                $rules['model_title'] = 'Kademeli Parça/Adet Modeli (Promosyon / Tişört)';
                $rules['required_fields'] = array_merge($rules['required_fields'], ['tiers']);
                break;
            case 'package_tier':
            default:
                $rules['model_title'] = 'Paket & Tiraj Modeli (Kartvizit / Broşür)';
                $rules['required_fields'] = array_merge($rules['required_fields'], ['packages']);
                $rules['optional_fields'] = array_merge($rules['optional_fields'], ['tiers', 'allow_online_editor']);
                break;
        }

        return array_merge($cat, ['schema_rules' => $rules]);
    }, $rawCats);

    Helper::jsonResponse([
        'success'    => true,
        'count'      => count($categories),
        'categories' => $categories
    ]);
} elseif ($method === 'POST') {
    // Sadece admin yeni kategori açabilir
    $role = $currentApiKeyData['role'] ?? 'memo';
    if ($role === 'memo') {
        Helper::jsonResponse(['success' => false, 'error' => 'Yeni kategori açma yetkisi yalnızca Sistem Mimarı Arif\'e aittir. Memo rolü mevcut kategorilere ürün ekleyebilir.'], 403);
    }

    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    if (empty($input['name'])) {
        Helper::jsonResponse(['success' => false, 'error' => 'Kategori adı ("name") zorunludur.'], 400);
    }
    $slug = !empty($input['slug']) ? Helper::slugify($input['slug']) : Helper::slugify($input['name']);
    $stmt = $db->prepare("INSERT INTO categories (parent_id, name, slug, icon, pricing_model, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $input['parent_id'] ?? 0,
        $input['name'],
        $slug,
        $input['icon'] ?? 'bi bi-grid',
        $input['pricing_model'] ?? 'package_tier',
        $input['sort_order'] ?? 0,
        1
    ]);
    Helper::jsonResponse(['success' => true, 'category_id' => $db->lastInsertId(), 'slug' => $slug], 201);
} else {
    Helper::jsonResponse(['success' => false, 'error' => 'Geçersiz metod.'], 405);
}
