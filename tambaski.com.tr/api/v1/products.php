<?php
/**
 * REST API v1 - Ürün Yönetimi (GET / POST / PUT / DELETE)
 * Memo ve Harici Entegrasyonlar için Tam Destek
 */
require_once __DIR__ . '/auth_check.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getInstance()->getConnection();

switch ($method) {
    case 'GET':
        // Tekil Ürün Detayı (ID veya Slug ile)
        if (!empty($_GET['id']) || !empty($_GET['slug'])) {
            if (!empty($_GET['id'])) {
                $stmt = $db->prepare("SELECT p.*, c.name as category_name, c.pricing_model FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
                $stmt->execute([(int)$_GET['id']]);
            } else {
                $stmt = $db->prepare("SELECT p.*, c.name as category_name, c.pricing_model FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.slug = ?");
                $stmt->execute([trim($_GET['slug'])]);
            }
            $product = $stmt->fetch();

            if ($product) {
                if (!empty($product['package_presets'])) {
                    $product['package_presets'] = json_decode($product['package_presets'], true);
                }
                // Adet kademelerini çek
                $tierStmt = $db->prepare("SELECT quantity, discount_percent, multiplier, fixed_price FROM product_quantity_tiers WHERE product_id = ? ORDER BY quantity ASC");
                $tierStmt->execute([$product['id']]);
                $product['quantity_tiers'] = $tierStmt->fetchAll();

                Helper::jsonResponse(['success' => true, 'product' => $product]);
            } else {
                Helper::jsonResponse(['success' => false, 'error' => 'Ürün bulunamadı.'], 404);
            }
        } else {
            // Liste
            $where = ["1=1"];
            $params = [];

            if (!empty($_GET['category_id'])) {
                $where[] = "p.category_id = ?";
                $params[] = (int)$_GET['category_id'];
            } elseif (!empty($_GET['category_slug'])) {
                $where[] = "c.slug = ?";
                $params[] = trim($_GET['category_slug']);
            }

            if (isset($_GET['status'])) {
                $where[] = "p.status = ?";
                $params[] = (int)$_GET['status'];
            }

            $limit = isset($_GET['limit']) ? min(100, max(1, (int)$_GET['limit'])) : 50;
            $offset = isset($_GET['page']) ? (max(1, (int)$_GET['page']) - 1) * $limit : 0;

            $sql = "SELECT p.id, p.category_id, c.name as category_name, c.pricing_model, p.name, p.slug, p.base_price, p.featured_image, p.mockup_image, p.status, p.created_at 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE " . implode(' AND ', $where) . " 
                    ORDER BY p.id DESC LIMIT $limit OFFSET $offset";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $products = $stmt->fetchAll();

            Helper::jsonResponse([
                'success'  => true,
                'count'    => count($products),
                'limit'    => $limit,
                'products' => $products
            ]);
        }
        break;

    case 'POST':
        // JSON body oku
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

        if (empty($input['name'])) {
            Helper::jsonResponse(['success' => false, 'error' => 'Ürün adı ("name") alanı zorunludur.'], 400);
        }

        // Kategori ID Çözümleme (Slug ile de gönderilebilir)
        $categoryId = (int)($input['category_id'] ?? 0);
        if (!$categoryId && !empty($input['category_slug'])) {
            $catStmt = $db->prepare("SELECT id FROM categories WHERE slug = ?");
            $catStmt->execute([trim($input['category_slug'])]);
            $categoryId = (int)$catStmt->fetchColumn();
        }
        if (!$categoryId) {
            $categoryId = 1; // Varsayılan
        }

        $name = trim($input['name']);
        $slug = !empty($input['slug']) ? Helper::slugify($input['slug']) : Helper::slugify($name);
        
        // Benzersiz slug
        $chkSlug = $db->prepare("SELECT id FROM products WHERE slug = ?");
        $chkSlug->execute([$slug]);
        if ($chkSlug->fetch()) {
            $slug .= '-' . bin2hex(random_bytes(2));
        }

        $sku = trim($input['sku'] ?? 'TB-' . strtoupper(bin2hex(random_bytes(3))));
        $shortDesc = trim($input['short_description'] ?? '');
        $fullDesc = $input['full_description'] ?? '';
        $basePrice = (float)str_replace(',', '.', $input['base_price'] ?? 95.00);
        $taxRate = (float)str_replace(',', '.', $input['tax_rate'] ?? 20.00);

        // Paket Önayarları (JSON)
        $packagesArray = [];
        if (!empty($input['packages']) && is_array($input['packages'])) {
            foreach ($input['packages'] as $key => $pkg) {
                $pkgKey = is_string($key) ? Helper::slugify($key) : Helper::slugify($pkg['name'] ?? 'paket');
                $packagesArray[$pkgKey] = [
                    'name'   => trim($pkg['name'] ?? ucfirst($pkgKey)),
                    'active' => isset($pkg['active']) ? (int)$pkg['active'] : 1,
                    'price'  => (float)str_replace(',', '.', $pkg['price'] ?? $basePrice),
                    'desc'   => trim($pkg['desc'] ?? ''),
                    'badge'  => trim($pkg['badge'] ?? '')
                ];
            }
        }
        $packagePresets = !empty($packagesArray) ? json_encode($packagesArray, JSON_UNESCAPED_UNICODE) : null;

        // Dekota & m² Dolar Değerleri
        $m2UsdPrice3mm = (float)str_replace(',', '.', $input['m2_usd_price_3mm'] ?? $input['m2_usd_price'] ?? 14.50);
        $m2UsdPrice5mm = (float)str_replace(',', '.', $input['m2_usd_price_5mm'] ?? 18.50);
        $m2UsdPrice9mm = (float)str_replace(',', '.', $input['m2_usd_price_9mm'] ?? 26.00);

        $allowOnlineEditor = isset($input['allow_online_editor']) ? (int)$input['allow_online_editor'] : 1;
        $allowDesignUpload = isset($input['allow_design_upload']) ? (int)$input['allow_design_upload'] : 1;
        $allowDesignService = isset($input['allow_design_service']) ? (int)$input['allow_design_service'] : 1;
        $designServicePrice = (float)str_replace(',', '.', $input['design_service_price'] ?? 150.00);
        $isFeatured = !empty($input['is_featured']) ? 1 : 0;
        $isUrgent = !empty($input['is_urgent']) ? 1 : 0;
        $status = isset($input['status']) ? (int)$input['status'] : 1;

        $featuredImage = trim($input['featured_image'] ?? 'assets/img/default_product.webp');
        $mockupImage = trim($input['mockup_image'] ?? '');

        try {
            $stmt = $db->prepare("INSERT INTO products (
                category_id, name, slug, sku, short_description, full_description,
                base_price, manual_base_price, m2_usd_price, m2_usd_price_3mm, m2_usd_price_5mm, m2_usd_price_9mm, tax_rate, package_presets,
                allow_online_editor, allow_design_upload, allow_design_service, design_service_price,
                is_featured, is_urgent, status, featured_image, mockup_image
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([
                $categoryId, $name, $slug, $sku, $shortDesc, $fullDesc,
                $basePrice, $basePrice, $m2UsdPrice3mm, $m2UsdPrice3mm, $m2UsdPrice5mm, $m2UsdPrice9mm, $taxRate, $packagePresets,
                $allowOnlineEditor, $allowDesignUpload, $allowDesignService, $designServicePrice,
                $isFeatured, $isUrgent, $status, $featuredImage, $mockupImage
            ]);

            $productId = (int)$db->lastInsertId();

            // Tiraj İndirimleri Kaydet
            if (!empty($input['tiers']) && is_array($input['tiers'])) {
                $tierStmt = $db->prepare("INSERT INTO product_quantity_tiers (product_id, quantity, discount_percent) VALUES (?, ?, ?)");
                foreach ($input['tiers'] as $qty => $discount) {
                    $tierStmt->execute([$productId, (int)$qty, (float)$discount]);
                }
            }

            Helper::jsonResponse([
                'success'    => true,
                'message'    => 'Ürün başarıyla oluşturuldu.',
                'product_id' => $productId,
                'slug'       => $slug,
                'url'        => SITE_URL . '/product.php?slug=' . $slug
            ], 201);

        } catch (Exception $e) {
            Helper::jsonResponse(['success' => false, 'error' => 'Ürün kaydedilirken hata oluştu: ' . $e->getMessage()], 500);
        }
        break;

    case 'PUT':
        // Güncelleme
        $productId = (int)($_GET['id'] ?? 0);
        if (!$productId) {
            Helper::jsonResponse(['success' => false, 'error' => 'Güncellenecek ürün ID ("id") parametresi zorunludur.'], 400);
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        if (empty($input)) {
            Helper::jsonResponse(['success' => false, 'error' => 'Güncellenecek alanlar gönderilmedi.'], 400);
        }

        // Mevcut ürünü çek
        $curStmt = $db->prepare("SELECT * FROM products WHERE id = ?");
        $curStmt->execute([$productId]);
        $curr = $curStmt->fetch();
        if (!$curr) {
            Helper::jsonResponse(['success' => false, 'error' => 'Ürün bulunamadı.'], 404);
        }

        // Güncellenecek alanları hazırla
        $name = trim($input['name'] ?? $curr['name']);
        $categoryId = isset($input['category_id']) ? (int)$input['category_id'] : $curr['category_id'];
        $shortDesc = isset($input['short_description']) ? trim($input['short_description']) : $curr['short_description'];
        $fullDesc = isset($input['full_description']) ? $input['full_description'] : $curr['full_description'];
        $basePrice = isset($input['base_price']) ? (float)str_replace(',', '.', $input['base_price']) : (float)$curr['base_price'];
        $featuredImage = isset($input['featured_image']) ? trim($input['featured_image']) : $curr['featured_image'];
        $mockupImage = isset($input['mockup_image']) ? trim($input['mockup_image']) : $curr['mockup_image'];
        $status = isset($input['status']) ? (int)$input['status'] : $curr['status'];

        $upStmt = $db->prepare("UPDATE products SET 
            category_id = ?, name = ?, short_description = ?, full_description = ?, 
            base_price = ?, featured_image = ?, mockup_image = ?, status = ? 
            WHERE id = ?");
        $upStmt->execute([$categoryId, $name, $shortDesc, $fullDesc, $basePrice, $featuredImage, $mockupImage, $status, $productId]);

        Helper::jsonResponse([
            'success' => true,
            'message' => 'Ürün başarıyla güncellendi.',
            'product_id' => $productId
        ]);
        break;

    case 'DELETE':
        $productId = (int)($_GET['id'] ?? 0);
        if (!$productId) {
            Helper::jsonResponse(['success' => false, 'error' => 'Ürün ID zorunludur.'], 400);
        }

        // Güvenli silme: Önce kademeleri, sonra ürünü sil
        $db->prepare("DELETE FROM product_quantity_tiers WHERE product_id = ?")->execute([$productId]);
        $del = $db->prepare("DELETE FROM products WHERE id = ?");
        $del->execute([$productId]);

        Helper::jsonResponse(['success' => true, 'message' => "Ürün (ID: $productId) silindi."]);
        break;

    default:
        Helper::jsonResponse(['success' => false, 'error' => 'Desteklenmeyen HTTP metodu.'], 405);
        break;
}
