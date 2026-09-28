<?php
require_once __DIR__ . '/../config/config.php';
Auth::requireAdmin();

header('Content-Type: application/json; charset=utf-8');

$db = Database::getInstance()->getConnection();
$action = $_REQUEST['action'] ?? '';

try {
    // -------------------------------------------------------------------------
    // 1. Hızlı Kategori Ekle
    // -------------------------------------------------------------------------
    if ($action === 'create_category') {
        $name = trim($_POST['name'] ?? '');
        $icon = trim($_POST['icon'] ?? 'bi bi-grid');
        $sortOrder = (int)($_POST['sort_order'] ?? 0);

        if (empty($name)) {
            echo json_encode(['success' => false, 'error' => 'Kategori adı boş olamaz.']);
            exit;
        }

        $slug = Helper::slugify($name);
        $stmt = $db->prepare("INSERT INTO categories (name, slug, icon, sort_order) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $slug, $icon, $sortOrder]);
        $catId = $db->lastInsertId();

        echo json_encode([
            'success' => true,
            'message' => 'Kategori başarıyla oluşturuldu.',
            'category' => [
                'id'   => $catId,
                'name' => $name,
                'slug' => $slug,
                'icon' => $icon
            ]
        ]);
        exit;
    }

    // -------------------------------------------------------------------------
    // 2. Hızlı Kağıt Türü (70x100) Ekle
    // -------------------------------------------------------------------------
    if ($action === 'create_paper') {
        $name = trim($_POST['name'] ?? '');
        $gsm = (int)($_POST['gsm'] ?? 350);
        $priceUsd = (float)str_replace(',', '.', $_POST['price_usd_70x100'] ?? 7.00);

        if (empty($name)) {
            echo json_encode(['success' => false, 'error' => 'Kağıt türü adı boş olamaz.']);
            exit;
        }

        $stmt = $db->prepare("INSERT INTO paper_types (name, gsm, price_usd_70x100, status) VALUES (?, ?, ?, 1)");
        $stmt->execute([$name, $gsm, $priceUsd]);
        $paperId = $db->lastInsertId();

        echo json_encode([
            'success' => true,
            'message' => 'Kağıt türü başarıyla eklendi.',
            'paper' => [
                'id'              => $paperId,
                'name'            => $name,
                'gsm'             => $gsm,
                'price_usd_70x100'=> $priceUsd
            ]
        ]);
        exit;
    }

    // -------------------------------------------------------------------------
    // 3. Hızlı Ekstra İşçilik / Selefon / Lak Ekle
    // -------------------------------------------------------------------------
    if ($action === 'create_finishing') {
        $name = trim($_POST['name'] ?? '');
        $type = $_POST['type'] ?? 'lamination';
        $pricePerUnitUsd = (float)str_replace(',', '.', $_POST['price_per_unit_usd'] ?? 0.05);
        $baseSetupUsd = (float)str_replace(',', '.', $_POST['base_setup_usd'] ?? 10.00);

        if (empty($name)) {
            echo json_encode(['success' => false, 'error' => 'İşlem adı boş olamaz.']);
            exit;
        }

        $stmt = $db->prepare("INSERT INTO finishing_options (name, type, price_per_unit_usd, base_setup_usd, status) VALUES (?, ?, ?, ?, 1)");
        $stmt->execute([$name, $type, $pricePerUnitUsd, $baseSetupUsd]);
        $finId = $db->lastInsertId();

        echo json_encode([
            'success' => true,
            'message' => 'Baskı işçiliği / selefon seçeneği başarıyla eklendi.',
            'finishing' => [
                'id'                 => $finId,
                'name'               => $name,
                'type'               => $type,
                'price_per_unit_usd' => $pricePerUnitUsd,
                'base_setup_usd'     => $baseSetupUsd
            ]
        ]);
        exit;
    }

    // -------------------------------------------------------------------------
    // 4. Hızlı Varyant Grubu Ekle
    // -------------------------------------------------------------------------
    if ($action === 'create_group') {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $inputType = $_POST['input_type'] ?? 'radio';
        $isRequired = !empty($_POST['is_required']) ? 1 : 0;
        $sortOrder = (int)($_POST['sort_order'] ?? 0);

        if (empty($name)) {
            echo json_encode(['success' => false, 'error' => 'Varyant grup adı boş olamaz.']);
            exit;
        }

        $slug = Helper::slugify($name);
        $stmt = $db->prepare("INSERT INTO variant_groups (name, slug, description, input_type, is_required, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, 1)");
        $stmt->execute([$name, $slug, $description, $inputType, $isRequired, $sortOrder]);
        $groupId = $db->lastInsertId();

        echo json_encode([
            'success' => true,
            'message' => 'Varyant grubu oluşturuldu.',
            'group' => [
                'id'          => $groupId,
                'name'        => $name,
                'slug'        => $slug,
                'description' => $description,
                'options'     => []
            ]
        ]);
        exit;
    }

    // -------------------------------------------------------------------------
    // 5. Gruba Hızlı Seçenek Ekle
    // -------------------------------------------------------------------------
    if ($action === 'create_option') {
        $groupId = (int)($_POST['group_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $calcType = $_POST['calc_type'] ?? 'per_unit_try';
        $priceUsd = (float)str_replace(',', '.', $_POST['price_usd_70x100'] ?? 0.00);
        $fixedFeeUsd = (float)str_replace(',', '.', $_POST['fixed_fee_usd'] ?? 0.00);
        $percentFee = (float)str_replace(',', '.', $_POST['percent_fee'] ?? 0.00);
        $perUnitFeeTry = (float)str_replace(',', '.', $_POST['per_unit_fee_try'] ?? 0.00);
        $fixedFeeTry = (float)str_replace(',', '.', $_POST['fixed_fee_try'] ?? 0.00);
        $isDefault = !empty($_POST['is_default']) ? 1 : 0;
        $sortOrder = (int)($_POST['sort_order'] ?? 0);

        if ($groupId <= 0 || empty($name)) {
            echo json_encode(['success' => false, 'error' => 'Grup ve seçenek adı zorunludur.']);
            exit;
        }

        if ($isDefault) {
            $db->prepare("UPDATE variant_options SET is_default = 0 WHERE group_id = ?")->execute([$groupId]);
        }

        $stmt = $db->prepare("INSERT INTO variant_options (group_id, name, calc_type, price_usd_70x100, fixed_fee_usd, percent_fee, per_unit_fee_try, fixed_fee_try, is_default, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->execute([$groupId, $name, $calcType, $priceUsd, $fixedFeeUsd, $percentFee, $perUnitFeeTry, $fixedFeeTry, $isDefault, $sortOrder]);
        $optId = $db->lastInsertId();

        // Badge hesaplama
        $badge = '';
        if ($calcType === 'percent' && $percentFee > 0) $badge = '+%' . $percentFee;
        elseif ($calcType === 'per_unit_try' && $perUnitFeeTry > 0) $badge = '+' . Helper::formatPrice($perUnitFeeTry) . '/ad';
        elseif ($calcType === 'sheet_usd' && $priceUsd > 0) $badge = '+$' . number_format($priceUsd, 2);
        elseif ($calcType === 'fixed_try' && $fixedFeeTry > 0) $badge = '+' . Helper::formatPrice($fixedFeeTry);
        elseif ($calcType === 'fixed_usd' && $fixedFeeUsd > 0) $badge = '+$' . number_format($fixedFeeUsd, 2);

        echo json_encode([
            'success' => true,
            'message' => 'Seçenek eklendi.',
            'option' => [
                'id'         => $optId,
                'group_id'   => $groupId,
                'name'       => $name,
                'calc_type'  => $calcType,
                'badge'      => $badge,
                'is_default' => $isDefault
            ]
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'error' => 'Geçersiz istek aksiyonu.']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Sunucu hatası: ' . $e->getMessage()]);
}
