<?php
require_once __DIR__ . '/config/config.php';

try {
    $db = Database::getInstance()->getConnection();
    $cols = $db->query("DESCRIBE products")->fetchAll(PDO::FETCH_COLUMN);
    echo "COLUMNS: " . implode(', ', $cols) . "<br>";
    
    $gallery = [
        'uploads/products/veo_vip_showcase.jpg',
        'uploads/products/veo_premium_showcase.jpg',
        'uploads/products/veo_standart_showcase.jpg',
        'uploads/products/veo_ekonomik_showcase.jpg'
    ];
    $jsonGallery = json_encode($gallery);
    
    $imgCol = in_array('image_url', $cols) ? 'image_url' : (in_array('main_image', $cols) ? 'main_image' : (in_array('image', $cols) ? 'image' : null));
    $galCol = in_array('gallery', $cols) ? 'gallery' : (in_array('gallery_images', $cols) ? 'gallery_images' : null);
    
    $setParts = [];
    $vals = [];
    if ($imgCol) {
        $setParts[] = "$imgCol = ?";
        $vals[] = 'uploads/products/veo_vip_showcase.jpg';
    }
    if ($galCol) {
        $setParts[] = "$galCol = ?";
        $vals[] = $jsonGallery;
    }
    
    if (!empty($setParts)) {
        $sql = "UPDATE products SET " . implode(', ', $setParts) . " WHERE slug = 'test'";
        $stmt = $db->prepare($sql);
        $stmt->execute($vals);
        echo "SUCCESSFULLY_UPDATED_PRODUCTS_SHOWCASE";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
