<?php
/**
 * REST API v1 - Görsel & Mockup Yükleme
 */
require_once __DIR__ . '/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Helper::jsonResponse(['success' => false, 'error' => 'Sadece POST desteklenir.'], 405);
}

// 1. Multipart Form-data Dosya Yükleme
if (!empty($_FILES['file'])) {
    $res = Helper::uploadFile($_FILES['file'], 'products', ['jpg', 'jpeg', 'png', 'webp', 'svg'], 20);
    Helper::jsonResponse($res);
}

// 2. Base64 Yükleme
$input = json_decode(file_get_contents('php://input'), true);
if (!empty($input['base64_image'])) {
    $data = $input['base64_image'];
    if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
        $data = substr($data, strpos($data, ',') + 1);
        $type = strtolower($type[1]);
        if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png', 'webp', 'svg'])) {
            Helper::jsonResponse(['success' => false, 'error' => 'Geçersiz resim tipi.'], 400);
        }
        $data = base64_decode($data);
        if ($data === false) {
            Helper::jsonResponse(['success' => false, 'error' => 'Base64 decode hatası.'], 400);
        }
    } else {
        Helper::jsonResponse(['success' => false, 'error' => 'Geçersiz Base64 URI.'], 400);
    }

    $fileName = 'ai_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $type;
    $targetDir = UPLOAD_PATH . '/products';
    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
    file_put_contents($targetDir . '/' . $fileName, $data);

    Helper::jsonResponse([
        'success'   => true,
        'file_name' => $fileName,
        'file_path' => 'uploads/products/' . $fileName,
        'full_url'  => UPLOAD_URL . '/products/' . $fileName
    ]);
}

Helper::jsonResponse(['success' => false, 'error' => 'Dosya veya base64_image bulunamadı.'], 400);
