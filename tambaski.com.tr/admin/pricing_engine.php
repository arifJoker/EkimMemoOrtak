<?php
require_once __DIR__ . '/../config/config.php';
Auth::requireAdmin();

$db = Database::getInstance()->getConnection();
$productModel = new Product();
$message = '';
$messageType = 'success';

// -----------------------------------------------------------------------------
// 1. POST / GET AKSİYONLARI
// -----------------------------------------------------------------------------

// Döviz Kuru Güncelleme
if (isset($_POST['action']) && $_POST['action'] === 'update_currency') {
    if (isset($_POST['refresh_tcmb'])) {
        $newRate = Helper::getUsdRate(true);
        Helper::setFlash('success', 'TCMB canlı kuru başarıyla güncellendi: 1 USD = ' . number_format($newRate, 4, ',', '.') . ' ₺');
    } else {
        $manualRate = (float)str_replace(',', '.', $_POST['usd_try_rate'] ?? 38.50);
        Helper::saveSetting('usd_try_rate', number_format($manualRate, 4, '.', ''));
        Helper::saveSetting('cutting_labor_percent', (float)($_POST['cutting_labor_percent'] ?? 5));
        Helper::saveSetting('gift_waste_threshold', (int)($_POST['gift_waste_threshold'] ?? 2));
        Helper::setFlash('success', 'Döviz kuru ve genel parametreler kaydedildi.');
    }
    header("Location: " . SITE_URL . "/admin/pricing_engine.php?tab=currency");
    exit;
}

// Fiyatlandırma Modu ve Genel Kâr Marjı Güncelleme
if (isset($_POST['action']) && $_POST['action'] === 'update_global_pricing_mode') {
    $mode = in_array($_POST['default_pricing_mode'] ?? '', ['auto_m2', 'manual']) ? $_POST['default_pricing_mode'] : 'auto_m2';
    $defSupplier = (int)($_POST['default_supplier_id'] ?? 1);
    $profitMargin = (float)str_replace(',', '.', $_POST['global_profit_margin'] ?? 50.0);
    $dealerMargin = (float)str_replace(',', '.', $_POST['dealer_profit_margin'] ?? 20.0);

    Helper::saveSetting('default_pricing_mode', $mode);
    Helper::saveSetting('default_supplier_id', $defSupplier);
    Helper::saveSetting('global_profit_margin', number_format($profitMargin, 2, '.', ''));
    Helper::saveSetting('dealer_profit_margin', number_format($dealerMargin, 2, '.', ''));
    Helper::setFlash('success', 'Sistem fiyatlandırma modu ve varsayılan tedarikçi ayarları güncellendi.');
    header("Location: " . SITE_URL . "/admin/pricing_engine.php");
    exit;
}

// Tedarikçi Ekle / Güncelle
if (isset($_POST['action']) && $_POST['action'] === 'save_supplier') {
    $sId = (int)($_POST['supplier_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $code = Helper::slugify(trim($_POST['code'] ?: $name));
    $contact = trim($_POST['contact_info'] ?? '');
    $margin = (float)str_replace(',', '.', $_POST['default_margin'] ?? 50.0);
    $status = !empty($_POST['status']) ? 1 : 0;

    if ($sId > 0) {
        $stmt = $db->prepare("UPDATE suppliers SET name = ?, code = ?, contact_info = ?, default_margin = ?, status = ? WHERE id = ?");
        $stmt->execute([$name, $code, $contact, $margin, $status, $sId]);
        Helper::setFlash('success', "Tedarikçi '{$name}' güncellendi.");
    } else {
        $stmt = $db->prepare("INSERT INTO suppliers (name, code, contact_info, default_margin, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $code, $contact, $margin, $status]);
        Helper::setFlash('success', "Yeni tedarikçi '{$name}' eklendi.");
    }
    header("Location: " . SITE_URL . "/admin/pricing_engine.php?tab=suppliers");
    exit;
}

// Tedarikçi Sil
if (isset($_GET['delete_supplier'])) {
    $sId = (int)$_GET['delete_supplier'];
    if ($sId > 1) { // 1 numaralı ana tedarikçiyi koru
        $db->prepare("DELETE FROM supplier_price_items WHERE supplier_id = ?")->execute([$sId]);
        $db->prepare("DELETE FROM suppliers WHERE id = ?")->execute([$sId]);
        Helper::setFlash('success', 'Tedarikçi ve fiyat listesi silindi.');
    } else {
        Helper::setFlash('warning', 'Ana varsayılan tedarikçi silinemez.');
    }
    header("Location: " . SITE_URL . "/admin/pricing_engine.php?tab=suppliers");
    exit;
}

// Tedarikçi Fiyat Listesi Kalemi Ekle / Güncelle
if (isset($_POST['action']) && $_POST['action'] === 'save_supplier_item') {
    $itemId = (int)($_POST['item_id'] ?? 0);
    $supplierId = (int)($_POST['supplier_id'] ?? 1);
    $category = trim($_POST['category'] ?? 'Kartvizit');
    $name = trim($_POST['name'] ?? '');
    $cost1000 = (float)str_replace(',', '.', $_POST['cost_1000'] ?? 0);
    $cost2000 = (float)str_replace(',', '.', $_POST['cost_2000'] ?? 0);
    $cost5000 = (float)str_replace(',', '.', $_POST['cost_5000'] ?? 0);
    $notes = trim($_POST['notes'] ?? '');

    if ($itemId > 0) {
        $stmt = $db->prepare("UPDATE supplier_price_items SET supplier_id = ?, category = ?, name = ?, cost_1000 = ?, cost_2000 = ?, cost_5000 = ?, notes = ? WHERE id = ?");
        $stmt->execute([$supplierId, $category, $name, $cost1000, $cost2000, $cost5000, $notes, $itemId]);
        Helper::setFlash('success', 'Tedarikçi fiyat kalemi güncellendi.');
    } else {
        $stmt = $db->prepare("INSERT INTO supplier_price_items (supplier_id, category, name, cost_1000, cost_2000, cost_5000, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$supplierId, $category, $name, $cost1000, $cost2000, $cost5000, $notes]);
        Helper::setFlash('success', 'Tedarikçi listesine yeni ürün kalemi eklendi.');
    }
    header("Location: " . SITE_URL . "/admin/pricing_engine.php?tab=suppliers");
    exit;
}

// Tedarikçi Fiyat Kalemi Sil
if (isset($_GET['delete_supplier_item'])) {
    $itemId = (int)$_GET['delete_supplier_item'];
    $db->prepare("DELETE FROM supplier_price_items WHERE id = ?")->execute([$itemId]);
    Helper::setFlash('success', 'Fiyat listesi kalemi silindi.');
    header("Location: " . SITE_URL . "/admin/pricing_engine.php?tab=suppliers");
    exit;
}

// Ürüne Tedarikçi & m² Kalibrasyon Uygulama
if (isset($_POST['action']) && $_POST['action'] === 'calibrate_product_m2') {
    $pId = (int)($_POST['product_id'] ?? 0);
    $supplierId = (int)($_POST['supplier_id'] ?? 1);
    $suppCost1000 = (float)str_replace(',', '.', $_POST['supplier_cost_1000'] ?? 0);
    $profitMargin = (float)str_replace(',', '.', $_POST['profit_margin_percent'] ?? 50.0);
    $pMode = in_array($_POST['pricing_mode'] ?? '', ['auto_m2', 'manual']) ? $_POST['pricing_mode'] : 'auto_m2';
    $manualPrice = (float)str_replace(',', '.', $_POST['manual_base_price'] ?? 0);

    if ($pId > 0) {
        $pRow = $db->query("SELECT * FROM products WHERE id = $pId")->fetch();
        if ($pRow) {
            $w = (float)($pRow['standard_width'] ?: 8.4);
            $h = (float)($pRow['standard_height'] ?: 5.2);
            $area1000M2 = ($w * $h * 1000) / 10000;
            $unitM2Cost = $area1000M2 > 0 ? ($suppCost1000 / $area1000M2) : 0;
            
            $sellingPrice1000 = ($pMode === 'manual' && $manualPrice > 0) 
                ? $manualPrice 
                : ($suppCost1000 * (1 + ($profitMargin / 100)));

            $stmt = $db->prepare("UPDATE products SET 
                pricing_mode = ?,
                supplier_id = ?,
                profit_margin_percent = ?,
                supplier_cost_1000 = ?,
                supplier_unit_m2_cost = ?,
                manual_base_price = ?,
                base_price = ?
                WHERE id = ?");
            $stmt->execute([$pMode, $supplierId, $profitMargin, $suppCost1000, $unitM2Cost, $manualPrice, $sellingPrice1000, $pId]);

            $suppName = $db->query("SELECT name FROM suppliers WHERE id = $supplierId")->fetchColumn() ?: 'Tedarikçi';
            Helper::setFlash('success', "{$pRow['name']} için [{$suppName}] tedarikçisi, %{$profitMargin} kâr marjı ve " . ($pMode === 'auto_m2' ? 'Otomatik m²' : 'Manuel') . " modu uygulandı! (1.000 Adet Satış: " . Helper::formatPrice($sellingPrice1000) . ")");
        }
    }
    header("Location: " . SITE_URL . "/admin/pricing_engine.php");
    exit;
}

// Ürün Modunu Hızlı Değiştir (AJAX / GET)
if (isset($_GET['toggle_mode_product'])) {
    $pId = (int)$_GET['toggle_mode_product'];
    $pRow = $db->query("SELECT pricing_mode, name FROM products WHERE id = $pId")->fetch();
    if ($pRow) {
        $newMode = ($pRow['pricing_mode'] === 'manual') ? 'auto_m2' : 'manual';
        $db->exec("UPDATE products SET pricing_mode = '$newMode' WHERE id = $pId");
        Helper::setFlash('success', "{$pRow['name']} fiyatlandırma modu " . ($newMode === 'auto_m2' ? '⚡ Otomatik m² & Tedarikçi' : '✍️ Manuel Sabit Fiyat') . " olarak değiştirildi.");
    }
    header("Location: " . SITE_URL . "/admin/pricing_engine.php?tab=matrix");
    exit;
}

// Kağıt Tipi Ekle / Güncelle
if (isset($_POST['action']) && $_POST['action'] === 'save_paper') {
    $id = (int)($_POST['paper_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $gsm = (int)($_POST['gsm'] ?? 350);
    $priceUsd = (float)str_replace(',', '.', $_POST['price_usd_70x100'] ?? 7.00);
    $status = !empty($_POST['status']) ? 1 : 0;

    if ($id > 0) {
        $stmt = $db->prepare("UPDATE paper_types SET name = ?, gsm = ?, price_usd_70x100 = ?, status = ? WHERE id = ?");
        $stmt->execute([$name, $gsm, $priceUsd, $status, $id]);
        Helper::setFlash('success', 'Kağıt türü güncellendi.');
    } else {
        $stmt = $db->prepare("INSERT INTO paper_types (name, gsm, price_usd_70x100, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $gsm, $priceUsd, $status]);
        Helper::setFlash('success', 'Yeni 70x100 kağıt türü eklendi.');
    }
    header("Location: " . SITE_URL . "/admin/pricing_engine.php?tab=currency");
    exit;
}

// Kağıt Sil
if (isset($_GET['delete_paper'])) {
    $id = (int)$_GET['delete_paper'];
    $stmt = $db->prepare("DELETE FROM paper_types WHERE id = ?");
    $stmt->execute([$id]);
    Helper::setFlash('success', 'Kağıt türü silindi.');
    header("Location: " . SITE_URL . "/admin/pricing_engine.php?tab=currency");
    exit;
}

// Selefon / İşlem Ekle / Güncelle
if (isset($_POST['action']) && $_POST['action'] === 'save_finishing') {
    $id = (int)($_POST['finishing_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $type = $_POST['type'] ?? 'lamination';
    $calcType = $_POST['calc_type'] ?? 'sheet_usd';
    $priceUsd = (float)str_replace(',', '.', $_POST['price_usd_70x100'] ?? 0.00);
    $fixedFeeUsd = (float)str_replace(',', '.', $_POST['fixed_fee_usd'] ?? 0.00);
    $percentFee = (float)str_replace(',', '.', $_POST['percent_fee'] ?? 0.00);
    $perUnitFeeTry = (float)str_replace(',', '.', $_POST['per_unit_fee_try'] ?? 0.00);
    $fixedFeeTry = (float)str_replace(',', '.', $_POST['fixed_fee_try'] ?? 0.00);
    $status = !empty($_POST['status']) ? 1 : 0;

    if ($id > 0) {
        $stmt = $db->prepare("UPDATE finishing_options SET name = ?, type = ?, calc_type = ?, price_usd_70x100 = ?, fixed_fee_usd = ?, percent_fee = ?, per_unit_fee_try = ?, fixed_fee_try = ?, status = ? WHERE id = ?");
        $stmt->execute([$name, $type, $calcType, $priceUsd, $fixedFeeUsd, $percentFee, $perUnitFeeTry, $fixedFeeTry, $status, $id]);
        Helper::setFlash('success', 'İşlem / varyant güncellendi.');
    } else {
        $stmt = $db->prepare("INSERT INTO finishing_options (name, type, calc_type, price_usd_70x100, fixed_fee_usd, percent_fee, per_unit_fee_try, fixed_fee_try, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $type, $calcType, $priceUsd, $fixedFeeUsd, $percentFee, $perUnitFeeTry, $fixedFeeTry, $status]);
        Helper::setFlash('success', 'Yeni matbaa işlemi eklendi.');
    }
    header("Location: " . SITE_URL . "/admin/pricing_engine.php?tab=currency");
    exit;
}

// -----------------------------------------------------------------------------
// 2. VERİLERİ HAZIRLA
// -----------------------------------------------------------------------------
$usdRate = Helper::getUsdRate();
$laborPercent = (float)Helper::getSetting('cutting_labor_percent', 5);
$giftThreshold = (int)Helper::getSetting('gift_waste_threshold', 2);
$lastUpdate = Helper::getSetting('usd_rate_updated_at', date('Y-m-d H:i'));

$defaultPricingMode = Helper::getSetting('default_pricing_mode', 'auto_m2');
$defaultSupplierId = (int)Helper::getSetting('default_supplier_id', 1);
$globalProfitMargin = (float)Helper::getSetting('global_profit_margin', 100.0);
$dealerProfitMargin = (float)Helper::getSetting('dealer_profit_margin', 20.0);

$suppliers = $productModel->getSuppliers(false);
$supplierItems = $productModel->getSupplierItems();
$papers = $db->query("SELECT * FROM paper_types ORDER BY gsm ASC, id ASC")->fetchAll();
$finishings = $db->query("SELECT * FROM finishing_options ORDER BY type ASC, id ASC")->fetchAll();

$allProducts = $db->query("SELECT p.*, c.name as category_name, s.name as supplier_name 
                           FROM products p 
                           LEFT JOIN categories c ON p.category_id = c.id 
                           LEFT JOIN suppliers s ON p.supplier_id = s.id 
                           WHERE p.status = 1 
                           ORDER BY p.sort_order ASC, p.id DESC")->fetchAll();

$activeTab = $_GET['tab'] ?? 'calibrator';

$pageTitle = 'Fiyatlandırma Motoru & Tedarikçi Yönetimi';
require_once __DIR__ . '/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-calculator-fill text-primary me-2"></i>Fiyatlandırma Motoru & Tedarikçi Yönetimi</h4>
        <p class="text-muted small mb-0">Tedarikçi toptan alış fiyatlarını, kâr marjlarını ve manuel/otomatik fiyatlandırma modlarını yönetin.</p>
    </div>
</div>

<!-- 🎛️ 1. GENEL FİYATLANDIRMA MODU VE KÂR MARJI KONTROLÜ -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
    <div class="card-body p-4 text-white">
        <form action="<?= SITE_URL ?>/admin/pricing_engine.php" method="POST" class="row g-3 align-items-center">
            <input type="hidden" name="action" value="update_global_pricing_mode">
            
            <div class="col-lg-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle p-3 bg-primary bg-opacity-25 text-primary fs-3">
                        <i class="bi bi-toggle-on"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">Sistem Fiyatlandırma Modu</h5>
                        <p class="text-white-50 small mb-0">Tüm ürünler için varsayılan fiyat algoritması ve toptancı tedarikçi.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="bg-dark bg-opacity-50 p-2 rounded-3 border border-secondary border-opacity-25 d-flex gap-2">
                    <label class="btn btn-sm flex-fill fw-bold <?= $defaultPricingMode === 'auto_m2' ? 'btn-primary text-white' : 'btn-outline-light text-white-50 border-0' ?>" for="mode_auto">
                        <input type="radio" name="default_pricing_mode" id="mode_auto" value="auto_m2" <?= $defaultPricingMode === 'auto_m2' ? 'checked' : '' ?> class="d-none" onchange="this.form.submit()">
                        <i class="bi bi-lightning-charge-fill me-1"></i> ⚡ Otomatik m² & Tedarikçi
                    </label>
                    <label class="btn btn-sm flex-fill fw-bold <?= $defaultPricingMode === 'manual' ? 'btn-primary text-white' : 'btn-outline-light text-white-50 border-0' ?>" for="mode_manual">
                        <input type="radio" name="default_pricing_mode" id="mode_manual" value="manual" <?= $defaultPricingMode === 'manual' ? 'checked' : '' ?> class="d-none" onchange="this.form.submit()">
                        <i class="bi bi-pencil-square me-1"></i> ✍️ Manuel Sabit Fiyat
                    </label>
                </div>
            </div>

            <div class="col-lg-2">
                <label class="small text-white-50 fw-semibold d-block mb-1" style="font-size: 11px;">Varsayılan Tedarikçi</label>
                <select name="default_supplier_id" class="form-select form-select-sm bg-dark text-white border-secondary border-opacity-25" onchange="this.form.submit()">
                    <?php foreach ($suppliers as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= $defaultSupplierId == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-lg-3">
                <div class="d-flex align-items-center gap-2">
                    <div>
                        <label class="small text-white-50 fw-semibold d-block mb-1" style="font-size: 11px;">Genel Kâr Marjı (%)</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-dark text-white border-secondary border-opacity-25">%</span>
                            <input type="number" step="1" name="global_profit_margin" class="form-control form-control-sm bg-dark text-white border-secondary border-opacity-25 fw-bold" value="<?= $globalProfitMargin ?>" style="width: 70px;">
                        </div>
                    </div>
                    <div>
                        <label class="small text-white-50 fw-semibold d-block mb-1" style="font-size: 11px;">Bayi İskontosu (%)</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-dark text-white border-secondary border-opacity-25">%</span>
                            <input type="number" step="1" name="dealer_profit_margin" class="form-control form-control-sm bg-dark text-white border-secondary border-opacity-25 fw-bold" value="<?= $dealerProfitMargin ?>" style="width: 70px;">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 py-2 fw-bold mt-3" title="Kaydet">
                        <i class="bi bi-check2"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 📑 ANA SEKMELER -->
<ul class="nav nav-pills mb-4 gap-2 bg-white p-2 rounded-4 shadow-sm border" id="pricingTabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link rounded-pill px-4 fw-bold <?= $activeTab === 'calibrator' ? 'active' : '' ?>" href="<?= SITE_URL ?>/admin/pricing_engine.php?tab=calibrator">
            <i class="bi bi-arrow-left-right me-1"></i> 1. Fiyat Simülatörü & Kalibratör
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link rounded-pill px-4 fw-bold <?= $activeTab === 'suppliers' ? 'active' : '' ?>" href="<?= SITE_URL ?>/admin/pricing_engine.php?tab=suppliers">
            <i class="bi bi-building me-1"></i> 2. Tedarikçi Firmalar & Fiyat Listeleri
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link rounded-pill px-4 fw-bold <?= $activeTab === 'matrix' ? 'active' : '' ?>" href="<?= SITE_URL ?>/admin/pricing_engine.php?tab=matrix">
            <i class="bi bi-table me-1"></i> 3. Tüm Ürünlerin Fiyat Matrisi
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link rounded-pill px-4 fw-bold <?= $activeTab === 'currency' ? 'active' : '' ?>" href="<?= SITE_URL ?>/admin/pricing_engine.php?tab=currency">
            <i class="bi bi-currency-dollar me-1"></i> 4. Döviz Kuru & Tabaka Hammaddeleri
        </a>
    </li>
</ul>

<div class="tab-content">

    <!-- ========================================================================= -->
    <!-- 📐 SEKME 1: AKILLI TEDARİKÇİ & M² KALİBRATÖRÜ -->
    <!-- ========================================================================= -->
    <?php if ($activeTab === 'calibrator'): ?>
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-lightning-charge-fill text-warning me-2"></i>Tedarikçi Fiyatından Canlı Kalibratör & Kâr Simülasyonu
                </h6>
                <span class="badge bg-primary-subtle text-primary fw-semibold">Tersine Mühendislik & Anında Fiyat</span>
            </div>
            <div class="card-body p-4">
                <form action="<?= SITE_URL ?>/admin/pricing_engine.php" method="POST" id="calibForm">
                    <input type="hidden" name="action" value="calibrate_product_m2">
                    
                    <div class="row g-3 align-items-end mb-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">1. Kalibre Edilecek Ürün</label>
                            <select name="product_id" id="calibProductSelect" class="form-select form-select-sm fw-semibold" onchange="onProductSelectChange()">
                                <?php foreach ($allProducts as $p): ?>
                                    <option value="<?= $p['id'] ?>" 
                                            data-w="<?= (float)($p['standard_width'] ?: 8.4) ?>" 
                                            data-h="<?= (float)($p['standard_height'] ?: 5.2) ?>"
                                            data-margin="<?= (float)($p['profit_margin_percent'] ?: $globalProfitMargin) ?>"
                                            data-cost="<?= (float)($p['supplier_cost_1000'] ?: 450) ?>"
                                            data-supplier="<?= (int)($p['supplier_id'] ?: 1) ?>"
                                            data-manual="<?= (float)($p['manual_base_price'] ?: $p['base_price']) ?>"
                                            data-mode="<?= htmlspecialchars($p['pricing_mode'] ?: $defaultPricingMode) ?>">
                                        <?= htmlspecialchars($p['name']) ?> (<?= $p['standard_width'] ?>x<?= $p['standard_height'] ?> cm)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-bold">2. Tedarikçi Firma</label>
                            <select name="supplier_id" id="calibSupplierSelect" class="form-select form-select-sm" onchange="onSupplierSelectChange()">
                                <?php foreach ($suppliers as $s): ?>
                                    <option value="<?= $s['id'] ?>" data-margin="<?= (float)$s['default_margin'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold">3. Tedarikçi Fiyat Listesi Kalemi (Opsiyonel)</label>
                            <select id="calibSupplierItemSelect" class="form-select form-select-sm" onchange="onSupplierItemSelectChange()">
                                <option value="0">-- Serbest Maliyet Gir --</option>
                                <?php foreach ($supplierItems as $item): ?>
                                    <option value="<?= $item['cost_1000'] ?>" data-supplier-id="<?= $item['supplier_id'] ?>" data-cost2000="<?= $item['cost_2000'] ?>" data-cost5000="<?= $item['cost_5000'] ?>">
                                        [<?= htmlspecialchars($item['supplier_name']) ?>] <?= htmlspecialchars($item['name']) ?> (<?= Helper::formatPrice($item['cost_1000']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Tedarikçi Alış (1.000 Adet ₺)</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">₺</span>
                                <input type="number" step="0.5" name="supplier_cost_1000" id="calibSupplierCost" class="form-control form-control-sm fw-bold" value="450.00" oninput="updateCalibSimulation()">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Hedef Kâr Marjı (%)</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">%</span>
                                <input type="number" step="1" name="profit_margin_percent" id="calibProfitMargin" class="form-control form-control-sm fw-bold" value="<?= $globalProfitMargin ?>" oninput="updateCalibSimulation()">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Fiyat Modu</label>
                            <select name="pricing_mode" id="calibPricingMode" class="form-select form-select-sm" onchange="updateCalibSimulation()">
                                <option value="auto_m2">⚡ Otomatik m² & Tedarikçi</option>
                                <option value="manual">✍️ Manuel Sabit Fiyat</option>
                            </select>
                        </div>

                        <div class="col-md-2" id="manualPriceBox" style="display: none;">
                            <label class="form-label small fw-bold">Manuel Satış Fiyatı (₺)</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">₺</span>
                                <input type="number" step="0.5" name="manual_base_price" id="calibManualPrice" class="form-control form-control-sm" value="900.00" oninput="updateCalibSimulation()">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-sm btn-primary w-100 rounded-pill fw-bold">
                                <i class="bi bi-magic me-1"></i> Ürüne Kaydet
                            </button>
                        </div>
                    </div>

                    <!-- Canlı Çıktı Tablosu -->
                    <div class="p-3 bg-light rounded-4 border">
                        <div class="row g-2 text-center align-items-center">
                            <div class="col-md-2 col-6 border-end">
                                <div class="text-muted small" style="font-size:11px;">1 Adet Maliyet</div>
                                <div class="fs-5 fw-bold text-secondary" id="calibUnitCost">0,45 ₺</div>
                            </div>
                            <div class="col-md-2 col-6 border-end">
                                <div class="text-muted small" style="font-size:11px;">1 m² Baz Maliyeti</div>
                                <div class="fs-5 fw-bold text-dark" id="calibUnitM2Cost">103,02 ₺ / m²</div>
                            </div>
                            <div class="col-md-3 col-6 border-end">
                                <div class="text-muted small" style="font-size:11px;">1.000 Adet Satış (Net)</div>
                                <div class="fs-5 fw-bold text-success" id="calibSale1000">900,00 ₺</div>
                                <span class="badge bg-success-subtle text-success small" id="calibProfit1000">+450,00 ₺ Kâr</span>
                            </div>
                            <div class="col-md-2 col-6 border-end">
                                <div class="text-muted small" style="font-size:11px;">2.000 Adet Satış</div>
                                <div class="fs-5 fw-bold text-primary" id="calibSale2000">1.530,00 ₺</div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="text-muted small" style="font-size:11px;">5.000 Adet Satış</div>
                                <div class="fs-5 fw-bold text-primary" id="calibSale5000">3.285,00 ₺</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script>
        function onProductSelectChange() {
            const sel = document.getElementById('calibProductSelect');
            const opt = sel.options[sel.selectedIndex];
            if (!opt) return;

            document.getElementById('calibSupplierCost').value = opt.dataset.cost || 450;
            document.getElementById('calibProfitMargin').value = opt.dataset.margin || 100;
            document.getElementById('calibPricingMode').value = opt.dataset.mode || 'auto_m2';
            document.getElementById('calibSupplierSelect').value = opt.dataset.supplier || 1;
            document.getElementById('calibManualPrice').value = opt.dataset.manual || (opt.dataset.cost * 2);

            updateCalibSimulation();
        }

        function onSupplierSelectChange() {
            const sSel = document.getElementById('calibSupplierSelect');
            const opt = sSel.options[sSel.selectedIndex];
            if (opt && opt.dataset.margin) {
                document.getElementById('calibProfitMargin').value = opt.dataset.margin;
            }
            updateCalibSimulation();
        }

        function onSupplierItemSelectChange() {
            const itemSel = document.getElementById('calibSupplierItemSelect');
            const val = parseFloat(itemSel.value);
            if (val > 0) {
                document.getElementById('calibSupplierCost').value = val.toFixed(2);
                const opt = itemSel.options[itemSel.selectedIndex];
                if (opt && opt.dataset.supplierId) {
                    document.getElementById('calibSupplierSelect').value = opt.dataset.supplierId;
                }
            }
            updateCalibSimulation();
        }

        function updateCalibSimulation() {
            const sel = document.getElementById('calibProductSelect');
            const opt = sel.options[sel.selectedIndex];
            const w = parseFloat(opt ? opt.dataset.w : 8.4) || 8.4;
            const h = parseFloat(opt ? opt.dataset.h : 5.2) || 5.2;

            const mode = document.getElementById('calibPricingMode').value;
            const manualBox = document.getElementById('manualPriceBox');
            if (manualBox) manualBox.style.display = (mode === 'manual') ? 'block' : 'none';

            const supp1000 = parseFloat(document.getElementById('calibSupplierCost').value) || 0;
            const margin = parseFloat(document.getElementById('calibProfitMargin').value) || 0;
            const manualPrice = parseFloat(document.getElementById('calibManualPrice').value) || (supp1000 * 2);

            const area1000M2 = (w * h * 1000) / 10000;
            const unitCost = supp1000 / 1000;
            const unitM2Cost = area1000M2 > 0 ? (supp1000 / area1000M2) : 0;

            let sale1000 = (mode === 'manual' && manualPrice > 0) ? manualPrice : (supp1000 * (1 + margin / 100));
            let profit1000 = sale1000 - supp1000;

            let sale2000 = sale1000 * 1.70;
            let sale5000 = sale1000 * 3.65;

            document.getElementById('calibUnitCost').innerText = unitCost.toFixed(2).replace('.', ',') + ' ₺';
            document.getElementById('calibUnitM2Cost').innerText = unitM2Cost.toFixed(2).replace('.', ',') + ' ₺ / m²';
            document.getElementById('calibSale1000').innerText = sale1000.toFixed(2).replace('.', ',') + ' ₺';
            document.getElementById('calibProfit1000').innerText = '+' + profit1000.toFixed(2).replace('.', ',') + ' ₺ Kâr';
            document.getElementById('calibSale2000').innerText = sale2000.toFixed(2).replace('.', ',') + ' ₺';
            document.getElementById('calibSale5000').innerText = sale5000.toFixed(2).replace('.', ',') + ' ₺';
        }

        window.addEventListener('DOMContentLoaded', () => {
            onProductSelectChange();
        });
        </script>
    <?php endif; ?>

    <!-- ========================================================================= -->
    <!-- 🏢 SEKME 2: TEDARİKÇİ FİRMALAR & FİYAT LİSTESİ HAFIZASI -->
    <!-- ========================================================================= -->
    <?php if ($activeTab === 'suppliers'): ?>
        <div class="row g-4">
            
            <!-- Tedarikçi Firmalar Listesi -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-buildings text-primary me-2"></i>Tedarikçi Firmalar (Matbaalar)</h6>
                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalSupplier">
                            <i class="bi bi-plus-lg me-1"></i> Yeni Tedarikçi Ekle
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-muted">
                                <tr>
                                    <th>Firma Adı</th>
                                    <th>Varsayılan Kâr</th>
                                    <th>Durum</th>
                                    <th class="text-end">İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($suppliers as $s): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($s['name']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($s['contact_info'] ?: 'İletişim yok') ?></small>
                                        </td>
                                        <td><span class="badge bg-success-subtle text-success font-monospace">%<?= (int)$s['default_margin'] ?> Kâr</span></td>
                                        <td>
                                            <?php if ($s['status']): ?>
                                                <span class="badge bg-success-subtle text-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary">Pasif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-light border py-0 px-2" onclick="editSupplier(<?= htmlspecialchars(json_encode($s)) ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <?php if ($s['id'] > 1): ?>
                                                <a href="<?= SITE_URL ?>/admin/pricing_engine.php?delete_supplier=<?= $s['id'] ?>" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="return confirm('Bu tedarikçiyi silmek istediğinize emin misiniz?');">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tedarikçi Fiyat Listesi Kalemleri -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-receipt-cutoff text-success me-2"></i>Tedarikçi Fiyat Listesi Kalemleri</h6>
                            <small class="text-muted">Türmatsan veya diğer toptancıların ham maliyetleri</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalSupplierItem">
                            <i class="bi bi-plus-lg me-1"></i> Fiyat Kalemi Ekle
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-muted">
                                <tr>
                                    <th>Kategori & Ürün Kalemi</th>
                                    <th>Tedarikçi</th>
                                    <th>1.000 Adet</th>
                                    <th>2.000 Adet</th>
                                    <th>5.000 Adet</th>
                                    <th class="text-end">İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($supplierItems)): ?>
                                    <tr><td colspan="6" class="text-center text-muted py-4">Henüz fiyat listesi kalemi eklenmemiş.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($supplierItems as $item): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary-subtle text-secondary me-1"><?= htmlspecialchars($item['category']) ?></span>
                                                <strong class="text-dark"><?= htmlspecialchars($item['name']) ?></strong>
                                            </td>
                                            <td><span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($item['supplier_name']) ?></span></td>
                                            <td><strong class="text-dark"><?= Helper::formatPrice($item['cost_1000']) ?></strong></td>
                                            <td><span class="text-muted"><?= $item['cost_2000'] > 0 ? Helper::formatPrice($item['cost_2000']) : '—' ?></span></td>
                                            <td><span class="text-muted"><?= $item['cost_5000'] > 0 ? Helper::formatPrice($item['cost_5000']) : '—' ?></span></td>
                                            <td class="text-end">
                                                <a href="<?= SITE_URL ?>/admin/pricing_engine.php?delete_supplier_item=<?= $item['id'] ?>" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="return confirm('Bu fiyat kalemini silmek istediğinize emin misiniz?');">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    <?php endif; ?>

    <!-- ========================================================================= -->
    <!-- 📊 SEKME 3: TÜM ÜRÜNLERİN FİYATLANDIRMA & TEDARİKÇİ MATRİSİ -->
    <!-- ========================================================================= -->
    <?php if ($activeTab === 'matrix'): ?>
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
            <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-table text-primary me-2"></i>Tüm Ürünlerin Tedarikçi ve Fiyatlandırma Durumu</h6>
                    <small class="text-muted">Her ürünün modunu (Otomatik / Manuel) ve tedarikçisini tek tıkla değiştirin.</small>
                </div>
                <span class="badge bg-dark rounded-pill px-3"><?= count($allProducts) ?> Yayında Ürün</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-muted">
                        <tr>
                            <th class="ps-3">Ürün Adı</th>
                            <th>Kategori</th>
                            <th>Ebat</th>
                            <th>Fiyatlandırma Modu</th>
                            <th>Bağlı Tedarikçi</th>
                            <th>1.000 Adet Alış</th>
                            <th>Kâr Marjı</th>
                            <th>1.000 Adet Satış</th>
                            <th class="text-end pe-3">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allProducts as $p): 
                            $isAuto = ($p['pricing_mode'] === 'auto_m2');
                        ?>
                            <tr>
                                <td class="ps-3">
                                    <strong class="text-dark"><?= htmlspecialchars($p['name']) ?></strong>
                                    <div class="text-muted small font-monospace" style="font-size:10px;">/<?= $p['slug'] ?></div>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($p['category_name'] ?: 'Genel') ?></span></td>
                                <td><span class="small fw-semibold"><?= $p['standard_width'] ?>x<?= $p['standard_height'] ?> cm</span></td>
                                <td>
                                    <?php if ($isAuto): ?>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                            <i class="bi bi-lightning-charge-fill me-1"></i> ⚡ Otomatik m²
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-dark border px-2 py-1">
                                            <i class="bi bi-pencil-square me-1"></i> ✍️ Manuel Sabit
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info fw-semibold">
                                        <?= htmlspecialchars($p['supplier_name'] ?: 'Türmatsan') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-secondary"><?= Helper::formatPrice($p['supplier_cost_1000']) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success">%<?= (int)$p['profit_margin_percent'] ?></span>
                                </td>
                                <td>
                                    <strong class="text-success fs-6"><?= Helper::formatPrice($p['base_price']) ?></strong>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="<?= SITE_URL ?>/admin/pricing_engine.php?toggle_mode_product=<?= $p['id'] ?>" class="btn btn-xs btn-outline-primary rounded-pill px-2 py-1" style="font-size: 11px;" title="Modu Değiştir">
                                        <?= $isAuto ? '✍️ Manuele Al' : '⚡ Otomatiğe Al' ?>
                                    </a>
                                    <a href="<?= SITE_URL ?>/admin/products.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-xs btn-light border rounded-pill px-2 py-1" style="font-size: 11px;" title="Düzenle">
                                        <i class="bi bi-gear"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- ========================================================================= -->
    <!-- 💵 SEKME 4: DÖVİZ KURU, KAĞITLAR VE SELEFONLAR -->
    <!-- ========================================================================= -->
    <?php if ($activeTab === 'currency'): ?>
        <div class="row g-4">
            
            <!-- Canlı Döviz Kuru ve Parametreler -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <h6 class="fw-bold mb-3 d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-currency-exchange text-success me-2"></i>Canlı Döviz Kuru</span>
                        <span class="badge bg-success-subtle text-success">Otomatik TCMB</span>
                    </h6>
                    
                    <form action="<?= SITE_URL ?>/admin/pricing_engine.php" method="POST">
                        <input type="hidden" name="action" value="update_currency">
                        
                        <div class="p-3 bg-light rounded-4 text-center mb-3 border">
                            <div class="text-muted small mb-1">1 Amerikan Doları ($ USD)</div>
                            <div class="display-6 fw-bold text-dark"><?= number_format($usdRate, 4, ',', '.') ?> <span class="fs-4 text-muted">₺</span></div>
                            <div class="small text-muted mt-1" style="font-size: 11px;">Son Güncelleme: <?= htmlspecialchars($lastUpdate) ?></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Manuel Dolar Kuru (₺)</label>
                            <input type="number" step="0.0001" name="usd_try_rate" class="form-control" value="<?= number_format($usdRate, 4, '.', '') ?>" required>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold">İşçilik & Kesim Payı (%)</label>
                                <div class="input-group">
                                    <span class="input-group-text">%</span>
                                    <input type="number" step="0.1" name="cutting_labor_percent" class="form-control" value="<?= $laborPercent ?>">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">Hediye Fire Eşiği (Adet)</label>
                                <input type="number" name="gift_waste_threshold" class="form-control" value="<?= $giftThreshold ?>">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill fw-bold">
                                <i class="bi bi-check-lg me-1"></i> Parametreleri Kaydet
                            </button>
                            <button type="submit" name="refresh_tcmb" value="1" class="btn btn-outline-secondary btn-sm rounded-pill">
                                <i class="bi bi-arrow-repeat me-1"></i> TCMB'den Şimdi Güncelle
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Kağıt Türleri Listesi -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-layers text-primary me-2"></i>70x100 Kağıt Türleri ($ USD / Tabaka)</h6>
                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalPaper">
                            <i class="bi bi-plus-lg me-1"></i> Yeni Kağıt Ekle
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-muted">
                                <tr>
                                    <th>Kağıt Türü</th>
                                    <th>Gramaj</th>
                                    <th>70x100 Fiyat ($)</th>
                                    <th>Tabaka Fiyatı (₺)</th>
                                    <th class="text-end">İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($papers as $p): ?>
                                    <tr>
                                        <td class="fw-bold text-dark"><?= htmlspecialchars($p['name']) ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= $p['gsm'] ?> gr</span></td>
                                        <td><span class="badge bg-success-subtle text-success fw-bold fs-6">$<?= number_format($p['price_usd_70x100'], 2) ?></span></td>
                                        <td><strong class="text-dark"><?= number_format($p['price_usd_70x100'] * $usdRate, 2, ',', '.') ?> ₺</strong></td>
                                        <td class="text-end">
                                            <a href="<?= SITE_URL ?>/admin/pricing_engine.php?delete_paper=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="return confirm('Bu kağıdı silmek istediğinize emin misiniz?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    <?php endif; ?>

</div>

<!-- ========================================================================= -->
<!-- MODALLER: TEDARİKÇİ & FİYAT KALEMİ EKLE / DÜZENLE -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalSupplier" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="<?= SITE_URL ?>/admin/pricing_engine.php" method="POST">
                <input type="hidden" name="action" value="save_supplier">
                <input type="hidden" name="supplier_id" id="modal_supplier_id" value="0">
                <div class="modal-header border-bottom">
                    <h6 class="modal-title fw-bold" id="modalSupplierTitle">Yeni Tedarikçi Firma Ekle</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tedarikçi / Matbaa Adı *</label>
                        <input type="text" name="name" id="supp_name" class="form-control" placeholder="Örn: Türmatsan, Net Matbaa, Atölyemiz" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">İletişim & Web / Notlar</label>
                        <input type="text" name="contact_info" id="supp_contact" class="form-control" placeholder="Örn: www.turmatsan.com / 0212 000 00 00">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Varsayılan Kâr Marjı (%)</label>
                            <div class="input-group">
                                <span class="input-group-text">%</span>
                                <input type="number" step="1" name="default_margin" id="supp_margin" class="form-control" value="100.00">
                            </div>
                        </div>
                        <div class="col-6 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="status" id="supp_status" value="1" checked>
                                <label class="form-check-label small" for="supp_status">Firma Aktif</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Vazgeç</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSupplierItem" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="<?= SITE_URL ?>/admin/pricing_engine.php" method="POST">
                <input type="hidden" name="action" value="save_supplier_item">
                <input type="hidden" name="item_id" id="modal_item_id" value="0">
                <div class="modal-header border-bottom">
                    <h6 class="modal-title fw-bold">Yeni Tedarikçi Fiyat Listesi Kalemi Ekle</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Tedarikçi *</label>
                            <select name="supplier_id" class="form-select" required>
                                <?php foreach ($suppliers as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Kategori *</label>
                            <input type="text" name="category" class="form-control" placeholder="Örn: Kartvizit, Broşür" value="Kartvizit" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Ürün / Varyant Kalem Adı *</label>
                        <input type="text" name="name" class="form-control" placeholder="Örn: 350gr Kuşe Çift Yön Mat Selefon + Kabartma Lak" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <label class="form-label small fw-bold">1.000 Adet (₺)</label>
                            <input type="number" step="0.5" name="cost_1000" class="form-control" placeholder="450.00" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-bold">2.000 Adet (₺)</label>
                            <input type="number" step="0.5" name="cost_2000" class="form-control" placeholder="765.00">
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-bold">5.000 Adet (₺)</label>
                            <input type="number" step="0.5" name="cost_5000" class="form-control" placeholder="1640.00">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Notlar / Açıklama</label>
                        <input type="text" name="notes" class="form-control" placeholder="Örn: 4 gün termin, kısmi lak">
                    </div>
                </div>
                <div class="modal-footer border-top py-2">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Vazgeç</button>
                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-4 fw-bold">Kalemi Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editSupplier(s) {
    document.getElementById('modal_supplier_id').value = s.id;
    document.getElementById('supp_name').value = s.name;
    document.getElementById('supp_contact').value = s.contact_info || '';
    document.getElementById('supp_margin').value = s.default_margin || 100;
    document.getElementById('supp_status').checked = s.status == 1;
    document.getElementById('modalSupplierTitle').innerText = 'Tedarikçi Düzenle: ' + s.name;
    new bootstrap.Modal(document.getElementById('modalSupplier')).show();
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
