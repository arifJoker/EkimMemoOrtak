<?php
require_once __DIR__ . '/config/config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // 1. Add video_path column if not exists
    $cols = $db->query("SHOW COLUMNS FROM products LIKE 'video_path'")->fetchAll();
    if (empty($cols)) {
        $db->exec("ALTER TABLE products ADD COLUMN video_path VARCHAR(255) NULL AFTER featured_image");
        $videoColMsg = "video_path column added.";
    } else {
        $videoColMsg = "video_path column already exists.";
    }

    // 2. Ensure gallery column exists
    $galleryCols = $db->query("SHOW COLUMNS FROM products LIKE 'gallery'")->fetchAll();
    if (empty($galleryCols)) {
        $db->exec("ALTER TABLE products ADD COLUMN gallery TEXT NULL AFTER featured_image");
        $galleryColMsg = "gallery column added.";
    } else {
        $galleryColMsg = "gallery column already exists.";
    }

    // 3. Create upload directories
    $uploadDirs = [
        UPLOAD_PATH . '/products',
        UPLOAD_PATH . '/products/videos',
        UPLOAD_PATH . '/designs',
        UPLOAD_PATH . '/templates'
    ];
    $dirStatus = [];
    foreach ($uploadDirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
            $dirStatus[$dir] = "created";
        } else {
            $dirStatus[$dir] = "exists";
        }
    }

    // 4. Check GD WebP support
    $gdInfo = function_exists('gd_info') ? gd_info() : [];
    $webpSupported = !empty($gdInfo['WebP Support']) || function_exists('imagewebp');

    echo json_encode([
        'success' => true,
        'video_column' => $videoColMsg,
        'gallery_column' => $galleryColMsg,
        'dirs' => $dirStatus,
        'gd_webp_supported' => $webpSupported
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
