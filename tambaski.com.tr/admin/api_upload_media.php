<?php
require_once __DIR__ . '/../config/config.php';
Auth::requireAdmin();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Geçersiz istek yöntemi.']);
    exit;
}

$type = $_POST['type'] ?? 'image'; // 'image' or 'video'

try {
    if ($type === 'video') {
        if (empty($_FILES['video_file']['name'])) {
            echo json_encode(['success' => false, 'error' => 'Video dosyası seçilmedi.']);
            exit;
        }

        $res = Helper::uploadVideo($_FILES['video_file'], 'products/videos', 100);
        if ($res['success']) {
            echo json_encode([
                'success'   => true,
                'type'      => 'video',
                'file_path' => $res['file_path'],
                'full_url'  => $res['full_url'],
                'file_name' => $res['file_name'],
                'size'      => $res['size']
            ]);
            exit;
        } else {
            echo json_encode(['success' => false, 'error' => $res['error'] ?? 'Video yükleme hatası.']);
            exit;
        }
    } else {
        // Multi Image Upload (or single) with WebP Conversion
        $uploadedFiles = [];
        $filesToProcess = [];

        if (isset($_FILES['images'])) {
            // Normalized multiple files array
            if (is_array($_FILES['images']['name'])) {
                $count = count($_FILES['images']['name']);
                for ($i = 0; $i < $count; $i++) {
                    if (!empty($_FILES['images']['name'][$i])) {
                        $filesToProcess[] = [
                            'name'     => $_FILES['images']['name'][$i],
                            'type'     => $_FILES['images']['type'][$i],
                            'tmp_name' => $_FILES['images']['tmp_name'][$i],
                            'error'    => $_FILES['images']['error'][$i],
                            'size'     => $_FILES['images']['size'][$i],
                        ];
                    }
                }
            } else {
                $filesToProcess[] = $_FILES['images'];
            }
        } elseif (isset($_FILES['image'])) {
            $filesToProcess[] = $_FILES['image'];
        }

        if (empty($filesToProcess)) {
            echo json_encode(['success' => false, 'error' => 'Yüklenecek görsel seçilmedi.']);
            exit;
        }

        foreach ($filesToProcess as $file) {
            $res = Helper::uploadImageAsWebp($file, 'products', 85, 1920);
            if ($res['success']) {
                $uploadedFiles[] = [
                    'file_path' => $res['file_path'],
                    'full_url'  => $res['full_url'],
                    'file_name' => $res['file_name'],
                    'is_webp'   => $res['is_webp'] ?? true,
                    'size'      => $res['size']
                ];
            }
        }

        if (!empty($uploadedFiles)) {
            echo json_encode([
                'success' => true,
                'type'    => 'image',
                'files'   => $uploadedFiles
            ]);
            exit;
        } else {
            echo json_encode(['success' => false, 'error' => 'Görseller işlenirken bir hata oluştu.']);
            exit;
        }
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
