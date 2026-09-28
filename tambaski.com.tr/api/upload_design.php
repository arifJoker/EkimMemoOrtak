<?php
/**
 * TAMBASKI.COM.TR - Tasarım & Logo Yükleme API Endpoint
 */
header('Content-Type: application/json; charset=utf-8');

$uploadDir = __DIR__ . '/../uploads/designs/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file'])) {
    echo json_encode(['success' => false, 'error' => 'Geçersiz istek veya dosya yüklenmedi.']);
    exit;
}

$file = $_FILES['file'];
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'Dosya yükleme hatası kodu: ' . $file['error']]);
    exit;
}

// Güvenlik & Uzantı Kontrolü
$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'svg', 'pdf', 'ai', 'psd', 'eps', 'tiff', 'zip'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowedExtensions)) {
    echo json_encode(['success' => false, 'error' => 'Desteklenmeyen dosya türü. Sadece PNG, JPG, SVG, PDF vb. kabul edilir.']);
    exit;
}

// Benzersiz dosya adı
$filename = 'design_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$targetPath = $uploadDir . $filename;

if (move_uploaded_file($file['tmp_name'], $targetPath)) {
    $publicUrl = 'uploads/designs/' . $filename;
    echo json_encode([
        'success' => true,
        'original_name' => htmlspecialchars($file['name']),
        'file_path' => $publicUrl,
        'url' => $publicUrl
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Dosya sunucuya kaydedilemedi.']);
}
