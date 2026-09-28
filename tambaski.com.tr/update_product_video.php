<?php
require_once __DIR__ . '/config/config.php';

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("UPDATE products SET video_path = 'uploads/videos/tambaski_vip_showcase.mp4' WHERE slug = 'test'");
    $stmt->execute();
    echo "VIDEO_PATH_UPDATED_SUCCESS";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
