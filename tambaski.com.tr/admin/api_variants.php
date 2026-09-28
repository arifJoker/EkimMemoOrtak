<?php
require_once __DIR__ . '/../config/config.php';
Auth::requireAdmin();

header('Content-Type: application/json; charset=utf-8');

$db = Database::getInstance()->getConnection();
$action = $_REQUEST['action'] ?? '';

try {
    // 1. Yeni Varyant Grubu Ekle (AJAX)
    if ($action === 'create_group') {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $inputType = $_POST['input_type'] ?? 'radio';
        $isRequired = !empty($_POST['is_required']) ? 1 : 0;
        $sortOrder = (int)($_POST['sort_order'] ?? 0);

        if (empty($name)) {
            echo json_encode(['success' => false, 'error' => 'Grup adı boş olamaz.']);
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

    // 2. Gruba Hızlı Seçenek Ekle (AJAX)
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

    // 3. Tüm Varyant Gruplarını ve Seçeneklerini Getir (AJAX)
    if ($action === 'get_all') {
        $groups = $db->query("SELECT * FROM variant_groups WHERE status = 1 ORDER BY sort_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($groups as &$grp) {
            $optStmt = $db->prepare("SELECT * FROM variant_options WHERE group_id = ? AND status = 1 ORDER BY sort_order ASC, id ASC");
            $optStmt->execute([$grp['id']]);
            $grp['options'] = $optStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode(['success' => true, 'groups' => $groups]);
        exit;
    }

    echo json_encode(['success' => false, 'error' => 'Tanımsız işlem.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
