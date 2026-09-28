<?php
/**
 * REST API v1 - Ürün Yönetimi (GET / POST / PUT / DELETE)
 */
require_once __DIR__ . '/auth_check.php';

$method = $_SERVER['REQUEST_METHOD'];
$productModel = new Product();
$db = Database::getInstance()->getConnection();

switch ($method) {
    case 'GET':
        if (!empty($_GET['id'])) {
            $product = $productModel->getById((int)$_GET['id']);
            if ($product) {
                Helper::jsonResponse(['success' => true, 'product' => $product]);
            } else {
                Helper::jsonResponse(['success' => false, 'error' => 'Ürün bulunamadı.'], 404);
            }
        } elseif (!empty($_GET['slug'])) {
            $product = $productModel->getBySlug($_GET['slug']);
            if ($product) {
                Helper::jsonResponse(['success' => true, 'product' => $product]);
            } else {
                Helper::jsonResponse(['success' => false, 'error' => 'Ürün bulunamadı.'], 404);
            }
        } else {
            $catId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
            $products = $productModel->getAll($limit, $catId);
            Helper::jsonResponse([
                'success' => true,
                'count'   => count($products),
                'products'=> $products
            ]);
        }
        break;

    case 'POST':
        // JSON body oku
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        if (empty($input['name'])) {
            Helper::jsonResponse(['success' => false, 'error' => 'Lütfen en az "name" (ürün adı) alanını doldurun.'], 400);
        }

        $result = $productModel->createFullProductFromAI($input);
        if ($result['success']) {
            Helper::jsonResponse([
                'success'    => true,
                'message'    => 'Ürün, varyantları, adet kademeleri ve şablonları başarıyla oluşturuldu.',
                'product_id' => $result['product_id'],
                'slug'       => $result['slug'],
                'url'        => $result['url']
            ], 201);
        } else {
            Helper::jsonResponse($result, 500);
        }
        break;

    case 'DELETE':
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            Helper::jsonResponse(['success' => false, 'error' => 'Ürün ID zorunludur.'], 400);
        }
        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        $deleted = $stmt->execute([$id]);
        Helper::jsonResponse(['success' => $deleted, 'message' => 'Ürün silindi.']);
        break;

    default:
        Helper::jsonResponse(['success' => false, 'error' => 'Desteklenmeyen metod.'], 405);
        break;
}
