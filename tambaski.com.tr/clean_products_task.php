<?php
require_once __DIR__ . '/config/config.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // 1. 'test' haricindeki tüm ürünleri tespit et
    $stmt = $db->query("SELECT id, slug, name FROM products WHERE slug != 'test'");
    $otherProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $deletedIds = [];
    foreach ($otherProducts as $p) {
        $deletedIds[] = (int)$p['id'];
    }
    
    if (!empty($deletedIds)) {
        $inClause = implode(',', $deletedIds);
        $db->exec("DELETE FROM product_quantity_tiers WHERE product_id IN ($inClause)");
        $db->exec("DELETE FROM product_attribute_values WHERE attribute_id IN (SELECT id FROM product_attributes WHERE product_id IN ($inClause))");
        $db->exec("DELETE FROM product_attributes WHERE product_id IN ($inClause)");
        $db->exec("DELETE FROM cart_items WHERE product_id IN ($inClause)");
        $db->exec("DELETE FROM products WHERE id IN ($inClause)");
        echo "Deleted " . count($deletedIds) . " other products.<br>";
    } else {
        echo "No other products to delete.<br>";
    }

    // 2. 'test' ürününün varlığını kontrol et / oluştur
    $testStmt = $db->prepare("SELECT id FROM products WHERE slug = 'test'");
    $testStmt->execute();
    $testProd = $testStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($testProd) {
        $testId = $testProd['id'];
        $up = $db->prepare("UPDATE products SET 
            name = 'Kurumsal Prestij Kartvizit (TamBaskı)',
            short_description = '350gr Kuşe, Soft-Touch Kadife, 24K Altın Varak ve Kabartma Lak Seçenekleriyle Firmanızı Zirveye Taşıyın.',
            full_description = 'TamBaskı güvencesiyle en yüksek ofset baskı standardı olan Heidelberg teknolojisiyle üretilen kurumsal kartvizitler. İster Ekonomik Tek Yön, ister Çift Yön Mat Selefon Oval Kesim, ister Soft-Touch Kadife Laklı, ister 24K Altın Varak Yaldızlı VIP paketler arasından seçiminizi yapın.',
            standard_width = 8.4,
            standard_height = 5.2,
            is_custom_size = 1,
            allow_online_editor = 1,
            allow_design_service = 1,
            design_service_price = 150.00,
            pricing_mode = 'auto_m2',
            supplier_id = 1,
            profit_margin_percent = 75.00,
            status = 1,
            is_featured = 1
            WHERE id = ?");
        $up->execute([$testId]);
        echo "Updated test product (ID: $testId).<br>";
    }

    echo "CLEANUP_SUCCESS";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
