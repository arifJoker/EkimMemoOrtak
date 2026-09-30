<?php
/**
 * REST API v1 - Görsel & Mockup Yükleme
 * Desteklenen Yöntemler:
 * 1. multipart/form-data ($_FILES['file'])
 * 2. application/json {"image_url": "https://..."}
 * 3. application/json {"base64_image": "data:image/..."}
 */
require_once __DIR__ . '/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Helper::jsonResponse(['success' => false, 'error' => 'Sadece POST metodu desteklenir.'], 405);
}

$targetDir = UPLOAD_PATH . '/products';
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

// 1. Multipart Form-data Dosya Yükleme
if (!empty($_FILES['file'])) {
    $file = $_FILES['file'];
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        Helper::jsonResponse(['success' => false, 'error' => 'Geçersiz dosya uzantısı. İzin verilenler: jpg, png, webp, svg'], 400);
    }

    $fileName = 'prod_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $targetPath = $targetDir . '/' . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        Helper::jsonResponse([
            'success'   => true,
            'file_name' => $fileName,
            'file_path' => 'uploads/products/' . $fileName,
            'full_url'  => UPLOAD_URL . '/products/' . $fileName
        ], 201);
    } else {
        Helper::jsonResponse(['success' => false, 'error' => 'Dosya yükleme başarısız oldu.'], 500);
    }
}

// JSON Body Oku (image_url veya base64_image)
$input = json_decode(file_get_contents('php://input'), true);

// 2. Harici URL ile Yükleme
if (!empty($input['image_url'])) {
    $url = trim($input['image_url']);
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        Helper::jsonResponse(['success' => false, 'error' => 'Geçersiz görsel URL adresi.'], 400);
    }

    $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
        $ext = 'jpg';
    }

    $fileName = 'url_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $targetPath = $targetDir . '/' . $fileName;

    // cURL ile indir
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $imageData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $imageData && strlen($imageData) > 100) {
        file_put_contents($targetPath, $imageData);
        Helper::jsonResponse([
            'success'   => true,
            'file_name' => $fileName,
            'file_path' => 'uploads/products/' . $fileName,
            'full_url'  => UPLOAD_URL . '/products/' . $fileName
        ], 201);
    } else {
        Helper::jsonResponse(['success' => false, 'error' => 'Görsel URL adresinden indirilemedi.'], 400);
    }
}

// 3. Base64 Yükleme
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
        Helper::jsonResponse(['success' => false, 'error' => 'Geçersiz Base64 URI formatı.'], 400);
    }

    $fileName = 'b64_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $type;
    file_put_contents($targetDir . '/' . $fileName, $data);

    Helper::jsonResponse([
        'success'   => true,
        'file_name' => $fileName,
        'file_path' => 'uploads/products/' . $fileName,
        'full_url'  => UPLOAD_URL . '/products/' . $fileName
    ], 201);
}

Helper::jsonResponse(['success' => false, 'error' => 'Lütfen dosya (file), harici görsel linki (image_url) veya base64_image iletin.'], 400);
