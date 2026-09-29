<?php
require_once __DIR__ . '/../config/config.php';
Auth::requireAdmin();

$db = Database::getInstance()->getConnection();
$action = $_GET['action'] ?? 'list';

// Otomatik Temizlik: Kartvizit ürünlerini Kartvizit kategorisine bağla
$kartvizitCat = $db->query("SELECT id FROM categories WHERE slug = 'kartvizit' LIMIT 1")->fetch();
if ($kartvizitCat) {
    $db->prepare("UPDATE products SET category_id = ? WHERE (name LIKE '%Kartvizit%' OR slug LIKE '%kartvizit%') AND category_id != ?")
       ->execute([$kartvizitCat['id'], $kartvizitCat['id']]);
}

// -----------------------------------------------------------------------------
// 1. Silme İşlemi
// -----------------------------------------------------------------------------
if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    // Ürün kademelerini sil
    $db->prepare("DELETE FROM product_quantity_tiers WHERE product_id = ?")->execute([$id]);
    // Ürünü sil
    $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    Helper::setFlash('success', 'Ürün başarıyla silindi.');
    header("Location: " . SITE_URL . "/admin/products.php");
    exit;
}

// -----------------------------------------------------------------------------
// 2. Form Kaydetme (Ekle / Güncelle)
// -----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = (int)($_POST['product_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 1);
    $sku = trim($_POST['sku'] ?? '');
    $slug = !empty($_POST['slug']) ? Helper::slugify($_POST['slug']) : Helper::slugify($name);
    $shortDesc = trim($_POST['short_description'] ?? '');
    $fullDesc = $_POST['full_description'] ?? '';

    // Fiyatlandırma (1. Faz - Net Paket & Taban Fiyat Modeli)
    $basePrice = (float)str_replace(',', '.', $_POST['base_price'] ?? 900.00);
    $taxRate = (float)str_replace(',', '.', $_POST['tax_rate'] ?? 20.00);

    // Dinamik Paket Önayarları (JSON)
    $packagesArray = [];
    if (!empty($_POST['packages']) && is_array($_POST['packages'])) {
        foreach ($_POST['packages'] as $idx => $pkg) {
            $pkgName = trim($pkg['name'] ?? '');
            if (empty($pkgName)) continue;
            $pkgKey = !empty($pkg['key']) ? Helper::slugify($pkg['key']) : Helper::slugify($pkgName);
            if (empty($pkgKey)) $pkgKey = 'paket_' . ($idx + 1);

            $packagesArray[$pkgKey] = [
                'name'   => $pkgName,
                'active' => !empty($pkg['active']) ? 1 : 0,
                'price'  => (float)str_replace(',', '.', $pkg['price'] ?? $basePrice),
                'desc'   => trim($pkg['desc'] ?? ''),
                'badge'  => trim($pkg['badge'] ?? '')
            ];
        }
    }
    // Eğer hiçbir paket kalmadıysa varsayılan 4 paket ekle
    if (empty($packagesArray)) {
        $packagesArray = [
            'ekonomik' => ['name' => 'Ekonomik', 'active' => 1, 'price' => round($basePrice * 0.85, 2), 'desc' => '250gr Bristol, Tek Yön Renkli', 'badge' => 'Uygun Fiyat'],
            'standart' => ['name' => 'Standart', 'active' => 1, 'price' => $basePrice, 'desc' => '350gr Kuşe, Çift Taraf Mat Selefon', 'badge' => 'Çok Satan'],
            'premium'  => ['name' => 'Premium', 'active' => 1, 'price' => round($basePrice * 1.45, 2), 'desc' => 'Soft-Touch Kadife Selefon & Kabartma Lak', 'badge' => 'Özel Doku'],
            'vip'      => ['name' => 'VIP Prestij', 'active' => 1, 'price' => round($basePrice * 1.85, 2), 'desc' => 'Tuale Fantezi / Altın Varak Yaldız', 'badge' => 'Lüks Seri']
        ];
    }
    $packagePresets = json_encode($packagesArray, JSON_UNESCAPED_UNICODE);

    // İzinler ve Özellikler
    $allowOnlineEditor = !empty($_POST['allow_online_editor']) ? 1 : 0;
    $allowDesignUpload = !empty($_POST['allow_design_upload']) ? 1 : 0;
    $allowDesignService = !empty($_POST['allow_design_service']) ? 1 : 0;
    $designServicePrice = (float)str_replace(',', '.', $_POST['design_service_price'] ?? 150.00);
    $isFeatured = !empty($_POST['is_featured']) ? 1 : 0;
    $isUrgent = !empty($_POST['is_urgent']) ? 1 : 0;
    $status = !empty($_POST['status']) ? 1 : 0;

    // Kapak Görseli Yükleme
    $featuredImage = trim($_POST['existing_image'] ?? '');
    if (!empty($_FILES['image']['name'])) {
        $up = Helper::uploadImageAsWebp($_FILES['image'], 'products', 85);
        if ($up['success']) {
            $featuredImage = $up['file_path'];
        }
    }

    // 3D Gerçekçi Mockup Görseli Yükleme / Seçme
    $mockupImage = trim($_POST['existing_mockup_image'] ?? '');
    if (!empty($_POST['selected_preset_mockup'])) {
        $mockupImage = trim($_POST['selected_preset_mockup']);
    }
    if (!empty($_FILES['mockup_image']['name'])) {
        $upMock = Helper::uploadImageAsWebp($_FILES['mockup_image'], 'mockups', 90);
        if ($upMock['success']) {
            $mockupImage = $upMock['file_path'];
        }
    }

    $m2UsdPrice = (float)str_replace(',', '.', $_POST['m2_usd_price_3mm'] ?? $_POST['m2_usd_price'] ?? 0.00);
    $m2UsdPrice3mm = (float)str_replace(',', '.', $_POST['m2_usd_price_3mm'] ?? $m2UsdPrice ?: 14.50);
    $m2UsdPrice5mm = (float)str_replace(',', '.', $_POST['m2_usd_price_5mm'] ?? 18.50);
    $m2UsdPrice9mm = (float)str_replace(',', '.', $_POST['m2_usd_price_9mm'] ?? 26.00);

    try {
        if ($productId > 0) {
            // Güncelle
            $stmt = $db->prepare("UPDATE products SET 
                category_id = ?, name = ?, slug = ?, sku = ?, short_description = ?, full_description = ?,
                base_price = ?, manual_base_price = ?, m2_usd_price = ?, m2_usd_price_3mm = ?, m2_usd_price_5mm = ?, m2_usd_price_9mm = ?, tax_rate = ?, package_presets = ?,
                allow_online_editor = ?, allow_design_upload = ?, allow_design_service = ?, design_service_price = ?,
                is_featured = ?, is_urgent = ?, status = ?, featured_image = ?, mockup_image = ?
                WHERE id = ?");
            
            $stmt->execute([
                $categoryId, $name, $slug, $sku, $shortDesc, $fullDesc,
                $basePrice, $basePrice, $m2UsdPrice, $m2UsdPrice3mm, $m2UsdPrice5mm, $m2UsdPrice9mm, $taxRate, $packagePresets,
                $allowOnlineEditor, $allowDesignUpload, $allowDesignService, $designServicePrice,
                $isFeatured, $isUrgent, $status, $featuredImage, $mockupImage, $productId
            ]);

            Helper::setFlash('success', "<strong>{$name}</strong> ürünü başarıyla güncellendi.");
        } else {
            // Yeni Ekle
            $stmt = $db->prepare("INSERT INTO products (
                category_id, name, slug, sku, short_description, full_description,
                base_price, manual_base_price, m2_usd_price, m2_usd_price_3mm, m2_usd_price_5mm, m2_usd_price_9mm, tax_rate, package_presets,
                allow_online_editor, allow_design_upload, allow_design_service, design_service_price,
                is_featured, is_urgent, status, featured_image, mockup_image
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([
                $categoryId, $name, $slug, $sku, $shortDesc, $fullDesc,
                $basePrice, $basePrice, $m2UsdPrice, $m2UsdPrice3mm, $m2UsdPrice5mm, $m2UsdPrice9mm, $taxRate, $packagePresets,
                $allowOnlineEditor, $allowDesignUpload, $allowDesignService, $designServicePrice,
                $isFeatured, $isUrgent, $status, $featuredImage, $mockupImage
            ]);

            $productId = (int)$db->lastInsertId();
            Helper::setFlash('success', "Yeni ürün <strong>{$name}</strong> başarıyla eklendi.");
        }

        // Adet Tiraj Kademelerini Güncelle / Ekle
        $db->prepare("DELETE FROM product_quantity_tiers WHERE product_id = ?")->execute([$productId]);
        $tiersToSave = [];
        if (!empty($_POST['dynamic_tiers']) && is_array($_POST['dynamic_tiers'])) {
            foreach ($_POST['dynamic_tiers'] as $dt) {
                $q = (int)($dt['quantity'] ?? 0);
                $d = (float)($dt['discount_percent'] ?? 0);
                if ($q > 0) {
                    $tiersToSave[$q] = $d;
                }
            }
        } elseif (!empty($_POST['tiers']) && is_array($_POST['tiers'])) {
            foreach ($_POST['tiers'] as $qty => $discount) {
                $q = (int)$qty;
                $d = (float)$discount;
                if ($q > 0) {
                    $tiersToSave[$q] = $d;
                }
            }
        }

        if (empty($tiersToSave)) {
            $tiersToSave = [1000 => 0, 2000 => 15, 3000 => 22, 5000 => 30, 10000 => 38];
        }

        ksort($tiersToSave);
        $tStmt = $db->prepare("INSERT INTO product_quantity_tiers (product_id, quantity, multiplier, discount_percent) VALUES (?, ?, ?, ?)");
        foreach ($tiersToSave as $q => $d) {
            $m = max(0.1, 1 - ($d / 100));
            $tStmt->execute([$productId, $q, $m, $d]);
        }

    } catch (Exception $e) {
        Helper::setFlash('danger', 'Kayıt sırasında hata oluştu: ' . $e->getMessage());
    }

    header("Location: " . SITE_URL . "/admin/products.php");
    exit;
}

// -----------------------------------------------------------------------------
// 3. Veri Listeleme & Sayfalama
// -----------------------------------------------------------------------------
$categories = $db->query("SELECT * FROM categories ORDER BY sort_order ASC, name ASC")->fetchAll();

if ($action === 'add' || $action === 'edit') {
    $product = null;
    $tiers = [];
    if ($action === 'edit' && isset($_GET['id'])) {
        $editId = (int)$_GET['id'];
        $pStmt = $db->prepare("SELECT * FROM products WHERE id = ?");
        $pStmt->execute([$editId]);
        $product = $pStmt->fetch();

        if ($product) {
            $tStmt = $db->prepare("SELECT * FROM product_quantity_tiers WHERE product_id = ? ORDER BY quantity ASC");
            $tStmt->execute([$editId]);
            $tiers = $tStmt->fetchAll();
        }
    }

    $presets = !empty($product['package_presets']) ? json_decode($product['package_presets'], true) : [];
    $pageTitle = ($action === 'edit') ? 'Ürün Düzenle: ' . htmlspecialchars($product['name'] ?? '') : 'Yeni Ürün Ekle';
    require_once __DIR__ . '/header.php';
    ?>

    <!-- ================= ÜRÜN EKLE / DÜZENLE FORMU ================= -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-box-seam text-primary me-2"></i><?= $pageTitle ?>
            </h4>
            <p class="text-muted small mb-0">1. Faz: Sade ürün &amp; paket fiyatlandırması (100x70 hesabı olmadan doğrudan net fiyatlar).</p>
        </div>
        <a href="<?= SITE_URL ?>/admin/products.php" class="btn btn-outline-secondary rounded-pill px-3 shadow-xs">
            <i class="bi bi-arrow-left me-1"></i> Ürün Listesine Dön
        </a>
    </div>

    <form action="<?= SITE_URL ?>/admin/products.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?= $product['id'] ?? 0 ?>">
        <input type="hidden" name="existing_image" value="<?= htmlspecialchars($product['featured_image'] ?? '') ?>">

        <div class="row g-4">
            <!-- Sol Kolon: Temel Bilgiler & Paket Fiyatları -->
            <div class="col-lg-8">
                
                <!-- 1. Temel Bilgiler Kartı -->
                <div class="apple-card p-4 mb-4">
                    <h6 class="fw-bold mb-3 border-bottom pb-2 text-dark">
                        <i class="bi bi-info-circle text-primary me-2"></i>1. Temel Ürün Bilgileri
                    </h6>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold text-dark">Ürün Adı *</label>
                            <input type="text" name="name" id="prodNameInput" class="form-control" required
                                   placeholder="Örn: Kurumsal Prestij Kartvizit"
                                   value="<?= htmlspecialchars($product['name'] ?? '') ?>"
                                   oninput="autoGenerateProductSlug(this.value)">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Kategori *</label>
                            <select name="category_id" class="form-select" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= (isset($product['category_id']) && $product['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label small fw-bold text-dark">Slug (SEO URL)</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text font-monospace text-muted" style="font-size: 11px;"><?= SITE_URL ?>/product.php?slug=</span>
                                <input type="text" name="slug" id="prodSlugInput" class="form-control font-monospace"
                                       placeholder="kurumsal-prestij-kartvizit"
                                       value="<?= htmlspecialchars($product['slug'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Stok Kodu / SKU</label>
                            <input type="text" name="sku" class="form-control form-control-sm font-monospace"
                                   placeholder="TB-KART-01"
                                   value="<?= htmlspecialchars($product['sku'] ?? '') ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark">Kısa Açıklama (Spot Metin)</label>
                            <textarea name="short_description" class="form-control form-control-sm" rows="2"
                                      placeholder="Ürün listelerinde ve kartlarda görünecek 1-2 cümlelik çarpıcı özet..."><?= htmlspecialchars($product['short_description'] ?? '') ?></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark">Detaylı Açıklama</label>
                            <textarea name="full_description" class="form-control" rows="4"
                                      placeholder="Kağıt gramajları, selefon özellikleri ve teknik detaylar..."><?= htmlspecialchars($product['full_description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- 2. Fiyatlandırma & Dinamik Paketler Kartı -->
                <div class="apple-card p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-tag text-success me-2"></i>2. Fiyatlandırma &amp; Paketler
                        </h6>
                        <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-pill">Dinamik Paket Modeli</span>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">Standart Taban Fiyatı (₺) *</label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold text-primary">₺</span>
                                <input type="number" step="0.01" name="base_price" id="basePriceInput" class="form-control fw-bold text-dark" required
                                       placeholder="95.00"
                                       value="<?= htmlspecialchars($product['base_price'] ?? '95.00') ?>"
                                       oninput="updatePackagePriceSuggestions(this.value)">
                            </div>
                            <small class="text-muted" style="font-size: 11px;">1.000 adet veya baz paket fiyattır (+KDV).</small>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">
                                <i class="bi bi-currency-dollar text-success me-1"></i>3 mm m² ($ USD)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-success-subtle text-success fw-bold">$</span>
                                <input type="number" step="0.01" name="m2_usd_price_3mm" class="form-control fw-bold" placeholder="14.50" value="<?= htmlspecialchars($product['m2_usd_price_3mm'] ?? $product['m2_usd_price'] ?? '14.50') ?>">
                            </div>
                            <small class="text-muted" style="font-size: 10.5px;">1 USD ≈ <?= number_format(Helper::getUsdRate(), 2, ',', '.') ?> ₺</small>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">
                                <i class="bi bi-currency-dollar text-primary me-1"></i>5 mm m² ($ USD)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary-subtle text-primary fw-bold">$</span>
                                <input type="number" step="0.01" name="m2_usd_price_5mm" class="form-control fw-bold" placeholder="18.50" value="<?= htmlspecialchars($product['m2_usd_price_5mm'] ?? '18.50') ?>">
                            </div>
                            <small class="text-muted" style="font-size: 10.5px;">5mm Sert Dekota</small>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">
                                <i class="bi bi-currency-dollar text-warning me-1"></i>9 mm m² ($ USD)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-warning-subtle text-dark fw-bold">$</span>
                                <input type="number" step="0.01" name="m2_usd_price_9mm" class="form-control fw-bold" placeholder="26.00" value="<?= htmlspecialchars($product['m2_usd_price_9mm'] ?? '26.00') ?>">
                            </div>
                            <small class="text-muted" style="font-size: 10.5px;">9mm Ekstra Ağır Dekota</small>
                        </div>
                    </div>

                    <!-- Dinamik Paketler Listesi -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label small fw-bold text-dark mb-0">Ürün Paketleri (Müşterinin Seçeceği Kartlar):</label>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 shadow-2xs" onclick="addPackageRow()">
                            <i class="bi bi-plus-circle me-1"></i> Yeni Paket Ekle
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle small mb-0" id="packagesTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 45px;" class="text-center">Aktif</th>
                                    <th style="width: 140px;">Paket Adı</th>
                                    <th style="width: 110px;">Kod (Key)</th>
                                    <th style="width: 145px;">Paket Fiyatı (₺)</th>
                                    <th>Paket Özellik Açıklaması</th>
                                    <th style="width: 110px;">Rozet (Badge)</th>
                                    <th style="width: 45px;" class="text-center">Sil</th>
                                </tr>
                            </thead>
                            <tbody id="packagesTbody">
                                <?php
                                $pkgIndex = 0;
                                $currentPresets = !empty($presets) ? $presets : [
                                    'ekonomik' => ['name' => 'Ekonomik', 'active' => 1, 'price' => round(($product['base_price'] ?? 900) * 0.85, 2), 'desc' => '250gr Bristol, Tek Yön Düz Baskı', 'badge' => 'Uygun'],
                                    'standart' => ['name' => 'Standart', 'active' => 1, 'price' => ($product['base_price'] ?? 900.00), 'desc' => '350gr Kuşe, Çift Taraf Mat Selefon', 'badge' => 'Popüler'],
                                    'premium'  => ['name' => 'Premium', 'active' => 1, 'price' => round(($product['base_price'] ?? 900) * 1.45, 2), 'desc' => 'Soft-Touch Kadife Selefon & Kabartma Lak', 'badge' => 'Özel Doku'],
                                    'vip'      => ['name' => 'VIP Prestij', 'active' => 1, 'price' => round(($product['base_price'] ?? 900) * 1.85, 2), 'desc' => 'Tuale Fantezi / Altın Varak Yaldız', 'badge' => 'Lüks Seri']
                                ];
                                foreach ($currentPresets as $pKey => $pData):
                                    $pkgIndex++;
                                ?>
                                <tr class="package-row" id="pkg_row_<?= $pkgIndex ?>">
                                    <td class="text-center">
                                        <input type="checkbox" name="packages[<?= $pkgIndex ?>][active]" value="1" class="form-check-input"
                                               <?= (!isset($pData['active']) || !empty($pData['active'])) ? 'checked' : '' ?>>
                                    </td>
                                    <td>
                                        <input type="text" name="packages[<?= $pkgIndex ?>][name]" class="form-control form-control-sm fw-bold"
                                               value="<?= htmlspecialchars($pData['name'] ?? ucfirst($pKey)) ?>" required placeholder="Paket Adı">
                                    </td>
                                    <td>
                                        <input type="text" name="packages[<?= $pkgIndex ?>][key]" class="form-control form-control-sm font-monospace text-muted"
                                               value="<?= htmlspecialchars($pKey) ?>" placeholder="kod">
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">₺</span>
                                            <input type="number" step="0.01" name="packages[<?= $pkgIndex ?>][price]" class="form-control font-monospace fw-bold pkg-price-inp"
                                                   value="<?= htmlspecialchars($pData['price'] ?? ($product['base_price'] ?? 900)) ?>" required>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="packages[<?= $pkgIndex ?>][desc]" class="form-control form-control-sm"
                                               placeholder="Kağıt, selefon, kesim detayları"
                                               value="<?= htmlspecialchars($pData['desc'] ?? '') ?>">
                                    </td>
                                    <td>
                                        <input type="text" name="packages[<?= $pkgIndex ?>][badge]" class="form-control form-control-sm"
                                               placeholder="Örn: Popüler"
                                               value="<?= htmlspecialchars($pData['badge'] ?? '') ?>">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-xs btn-outline-danger" onclick="removePackageRow('pkg_row_<?= $pkgIndex ?>')" title="Paketi Sil">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 3. Dinamik Adet / Tiraj İndirimleri Kartı -->
                <div class="apple-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">
                                <i class="bi bi-layers text-primary me-2"></i>3. Adet / Tiraj İndirim Kademeleri (%)
                            </h6>
                            <small class="text-muted" style="font-size: 11px;">Hem standart paketlerde hem de özel ölçülü levha baskılarında bu adet indirimleri otomatik uygulanır.</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 shadow-2xs" onclick="addTierRow()">
                            <i class="bi bi-plus-circle me-1"></i> Yeni Kademe Ekle
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle small mb-0" id="tiersTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 220px;">Baskı / Sipariş Adedi</th>
                                    <th style="width: 200px;">İndirim Oranı (%)</th>
                                    <th style="width: 60px;" class="text-center">Sil</th>
                                </tr>
                            </thead>
                            <tbody id="tiersTbody">
                                <?php
                                $tierIdx = 0;
                                $displayTiers = !empty($tiers) ? $tiers : [
                                    ['quantity' => 1000, 'discount_percent' => 0],
                                    ['quantity' => 2000, 'discount_percent' => 15],
                                    ['quantity' => 3000, 'discount_percent' => 22],
                                    ['quantity' => 5000, 'discount_percent' => 30],
                                    ['quantity' => 10000, 'discount_percent' => 38]
                                ];
                                foreach ($displayTiers as $tRow):
                                    $tierIdx++;
                                ?>
                                <tr class="tier-row" id="tier_row_<?= $tierIdx ?>">
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="dynamic_tiers[<?= $tierIdx ?>][quantity]" class="form-control font-monospace fw-bold text-center" value="<?= (int)$tRow['quantity'] ?>" required placeholder="Örn: 10">
                                            <span class="input-group-text">Adet</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="0.5" name="dynamic_tiers[<?= $tierIdx ?>][discount_percent]" class="form-control font-monospace fw-bold text-center" value="<?= (float)$tRow['discount_percent'] ?>" required placeholder="0">
                                            <span class="input-group-text">% İndirim</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-xs btn-outline-danger" onclick="removeTierRow('tier_row_<?= $tierIdx ?>')" title="Kademeyi Sil">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Sağ Kolon: Görsel & Özellikler & Kaydet Butonu -->
            <div class="col-lg-4">
                
                <!-- Kaydet Butonu & Durum Kartı -->
                <div class="apple-card p-4 mb-4 sticky-top" style="top: 20px; z-index: 10;">
                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm mb-3">
                        <i class="bi bi-save me-1"></i> Ürünü Kaydet &amp; Yayınla
                    </button>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="status" id="prodStatus" value="1"
                               <?= (!isset($product) || !empty($product['status'])) ? 'checked' : '' ?>>
                        <label class="form-check-label small fw-bold text-dark" for="prodStatus">Satışta (Yayında)</label>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_featured" id="prodFeatured" value="1"
                               <?= (!empty($product['is_featured'])) ? 'checked' : '' ?>>
                        <label class="form-check-label small fw-semibold text-dark" for="prodFeatured">🔥 Anasayfa Vitrininde Göster</label>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_urgent" id="prodUrgent" value="1"
                               <?= (!empty($product['is_urgent'])) ? 'checked' : '' ?>>
                        <label class="form-check-label small fw-semibold text-dark" for="prodUrgent">⚡ 24 Saatte Acil Baskı Rozeti</label>
                    </div>
                </div>

                <!-- Ürün Görseli & 3D Mockup Kartı -->
                <div class="apple-card p-4 mb-4">
                    <h6 class="fw-bold mb-3 border-bottom pb-2 text-dark">
                        <i class="bi bi-image text-primary me-2"></i>Kapak Görseli &amp; 3D Mockup
                    </h6>

                    <!-- Kapak Görseli -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark d-block">1. Kapak Görseli (Vitrin / Liste)</label>
                        <?php if (!empty($product['featured_image'])): ?>
                            <div class="mb-2 text-center bg-light p-2 rounded-3 border">
                                <img src="<?= SITE_URL . '/' . htmlspecialchars($product['featured_image']) ?>" 
                                     alt="Ürün Görseli" class="rounded-3 shadow-xs border img-fluid" style="max-height: 120px; object-fit: contain;">
                                <input type="hidden" name="existing_image" value="<?= htmlspecialchars($product['featured_image']) ?>">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
                        <small class="text-muted" style="font-size: 11px;">JPG, PNG veya WebP.</small>
                    </div>

                    <!-- 3D Mockup Görseli -->
                    <div class="pt-3 border-top">
                        <label class="form-label small fw-bold text-dark d-block">2. 3D Gerçekçi Mockup Görseli (Detay Sahnesi)</label>
                        <?php if (!empty($product['mockup_image'])): ?>
                            <div class="mb-2 text-center bg-dark p-2 rounded-3 border">
                                <img src="<?= SITE_URL . '/' . htmlspecialchars($product['mockup_image']) ?>" 
                                     alt="Mockup Görseli" class="rounded-3 shadow-xs img-fluid" style="max-height: 120px; object-fit: contain;">
                                <input type="hidden" name="existing_mockup_image" value="<?= htmlspecialchars($product['mockup_image']) ?>">
                                <small class="text-white-50 d-block mt-1" style="font-size: 10px;">Mevcut Gerçekçi Mockup</small>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="mockup_image" class="form-control form-control-sm mb-2" accept="image/*">
                        
                        <label class="form-label small text-muted mb-1" style="font-size: 11px;">veya Hazır TamBaskı Mockup'larından Seçin:</label>
                        <select name="selected_preset_mockup" class="form-select form-select-sm">
                            <option value="">-- Yeni Mockup Yükle / Mevcutu Koru --</option>
                            <option value="uploads/mockups/tambaski_kartvizit_vip_mockup.jpg" <?= (isset($product['mockup_image']) && $product['mockup_image'] == 'uploads/mockups/tambaski_kartvizit_vip_mockup.jpg') ? 'selected' : '' ?>>
                                ⭐ VIP Fantezi Tuale &amp; Altın Varak Stüdyo Mockup
                            </option>
                            <option value="uploads/mockups/tambaski_kartvizit_std_mockup.jpg" <?= (isset($product['mockup_image']) && $product['mockup_image'] == 'uploads/mockups/tambaski_kartvizit_std_mockup.jpg') ? 'selected' : '' ?>>
                                🖤 Mat Siyah Kadife Deste &amp; Turuncu Kenar Mockup
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Sipariş Seçenekleri & İzinler Kartı -->
                <div class="apple-card p-4">
                    <h6 class="fw-bold mb-3 border-bottom pb-2 text-dark">
                        <i class="bi bi-sliders text-primary me-2"></i>Tasarım &amp; Sipariş Yöntemleri
                    </h6>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="allow_online_editor" id="allowEditor" value="1"
                               <?= (!isset($product) || !empty($product['allow_online_editor'])) ? 'checked' : '' ?>>
                        <label class="form-check-label small fw-semibold text-dark" for="allowEditor">
                            🎨 Online Vektör Editörü (Canva Studio)
                        </label>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="allow_design_upload" id="allowUpload" value="1"
                               <?= (!isset($product) || !empty($product['allow_design_upload'])) ? 'checked' : '' ?>>
                        <label class="form-check-label small fw-semibold text-dark" for="allowUpload">
                            📤 Hazır Dosya Yükleme (PDF/AI/PSD)
                        </label>
                    </div>

                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="allow_design_service" id="allowSupport" value="1"
                               <?= (!isset($product) || !empty($product['allow_design_service'])) ? 'checked' : '' ?>>
                        <label class="form-check-label small fw-semibold text-dark" for="allowSupport">
                            💬 Grafik Tasarım Destek Hizmeti
                        </label>
                    </div>

                    <div class="mt-2">
                        <label class="form-label small text-muted" style="font-size: 11px;">Grafik Hizmeti Ek Ücreti (₺):</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">₺</span>
                            <input type="number" step="0.01" name="design_service_price" class="form-control"
                                   value="<?= htmlspecialchars($product['design_service_price'] ?? '150.00') ?>">
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>

    <script>
    function autoGenerateProductSlug(text) {
        const slugInput = document.getElementById('prodSlugInput');
        if (!slugInput.dataset.manual) {
            const trMap = {
                'ç':'c', 'Ç':'c', 'ğ':'g', 'Ğ':'g', 'ı':'i', 'İ':'i',
                'ö':'o', 'Ö':'o', 'ş':'s', 'Ş':'s', 'ü':'u', 'Ü':'u'
            };
            let slug = text.toLowerCase();
            for (let key in trMap) {
                slug = slug.split(key).join(trMap[key]);
            }
            slug = slug.replace(/[^a-z0-9 -]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-').trim();
            slugInput.value = slug;
        }
    }

    document.getElementById('prodSlugInput')?.addEventListener('input', function() {
        this.dataset.manual = 'true';
    });

    function updatePackagePriceSuggestions(val) {
        const base = parseFloat(val) || 0;
        const ekoInput = document.getElementById('pkgEkoPrice');
        const stdInput = document.getElementById('pkgStdPrice');
        const premInput = document.getElementById('pkgPremPrice');
        const vipInput = document.getElementById('pkgVipPrice');

        if (stdInput) stdInput.value = base.toFixed(2);
        if (ekoInput && !ekoInput.dataset.manual) ekoInput.value = (base * 0.85).toFixed(2);
        if (premInput && !premInput.dataset.manual) premInput.value = (base * 1.45).toFixed(2);
        if (vipInput && !vipInput.dataset.manual) vipInput.value = (base * 1.85).toFixed(2);
    }

    let packageCounter = <?= $pkgIndex ?>;

    function addPackageRow() {
        packageCounter++;
        const tbody = document.getElementById('packagesTbody');
        const basePrice = parseFloat(document.getElementById('basePriceInput')?.value || 900);
        const row = document.createElement('tr');
        row.className = 'package-row';
        row.id = 'pkg_row_' + packageCounter;
        row.innerHTML = `
            <td class="text-center">
                <input type="checkbox" name="packages[${packageCounter}][active]" value="1" class="form-check-input" checked>
            </td>
            <td>
                <input type="text" name="packages[${packageCounter}][name]" class="form-control form-control-sm fw-bold" required placeholder="Yeni Paket">
            </td>
            <td>
                <input type="text" name="packages[${packageCounter}][key]" class="form-control form-control-sm font-monospace text-muted" placeholder="kod_${packageCounter}">
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">₺</span>
                    <input type="number" step="0.01" name="packages[${packageCounter}][price]" class="form-control font-monospace fw-bold pkg-price-inp" value="${basePrice.toFixed(2)}" required>
                </div>
            </td>
            <td>
                <input type="text" name="packages[${packageCounter}][desc]" class="form-control form-control-sm" placeholder="Paket kağıt ve baskı detayları">
            </td>
            <td>
                <input type="text" name="packages[${packageCounter}][badge]" class="form-control form-control-sm" placeholder="Örn: Yeni">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-xs btn-outline-danger" onclick="removePackageRow('pkg_row_${packageCounter}')" title="Paketi Sil">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    }

    function removePackageRow(rowId) {
        const row = document.getElementById(rowId);
        if (row) {
            row.remove();
        }
    }

    let tierCounter = <?= (int)$tierIdx ?>;

    function addTierRow() {
        tierCounter++;
        const tbody = document.getElementById('tiersTbody');
        const row = document.createElement('tr');
        row.className = 'tier-row';
        row.id = 'tier_row_' + tierCounter;
        row.innerHTML = `
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" name="dynamic_tiers[${tierCounter}][quantity]" class="form-control font-monospace fw-bold text-center" value="" required placeholder="Örn: 25">
                    <span class="input-group-text">Adet</span>
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" step="0.5" name="dynamic_tiers[${tierCounter}][discount_percent]" class="form-control font-monospace fw-bold text-center" value="0" required placeholder="0">
                    <span class="input-group-text">% İndirim</span>
                </div>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-xs btn-outline-danger" onclick="removeTierRow('tier_row_${tierCounter}')" title="Kademeyi Sil">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    }

    function removeTierRow(rowId) {
        const row = document.getElementById(rowId);
        if (row) {
            row.remove();
        }
    }
    </script>

<?php
} else {
    // -------------------------------------------------------------------------
    // LİSTE GÖRÜNÜMÜ (action=list)
    // -------------------------------------------------------------------------
    $search = trim($_GET['q'] ?? '');
    $filterCat = (int)($_GET['category'] ?? 0);

    $sql = "SELECT p.*, c.name AS category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE 1=1";
    $params = [];

    if (!empty($search)) {
        $sql .= " AND (p.name LIKE ? OR p.sku LIKE ? OR p.slug LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if ($filterCat > 0) {
        $sql .= " AND p.category_id = ?";
        $params[] = $filterCat;
    }

    $sql .= " ORDER BY p.sort_order ASC, p.id DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();

    $pageTitle = 'Ürün Yönetimi (1. Faz)';
    require_once __DIR__ . '/header.php';
    ?>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-box-seam text-primary me-2"></i>Ürün Yönetimi</h4>
            <p class="text-muted small mb-0">1. Faz: Ürünlerinizi ekleyin, temel paket fiyatlarını ve tiraj indirimlerini yönetin.</p>
        </div>
        <a href="<?= SITE_URL ?>/admin/products.php?action=add" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-xs">
            <i class="bi bi-plus-lg me-1"></i> Yeni Ürün Ekle
        </a>
    </div>

    <!-- Filtre Çubuğu -->
    <div class="apple-card p-3 mb-4">
        <form action="<?= SITE_URL ?>/admin/products.php" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Ürün adı, stok kodu veya slug..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-4">
                <select name="category" class="form-select form-select-sm">
                    <option value="0">Tüm Kategoriler</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($filterCat == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-dark w-100 fw-semibold">Filtrele</button>
                <?php if (!empty($search) || $filterCat > 0): ?>
                    <a href="<?= SITE_URL ?>/admin/products.php" class="btn btn-sm btn-outline-secondary">Sıfırla</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Ürün Tablosu -->
    <div class="apple-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
            <h6 class="fw-bold mb-0 text-dark">Kayıtlı Ürünler (<?= count($products) ?>)</h6>
            <span class="text-muted small">100x70 hesabı olmadan doğrudan paket ve tiraj fiyatları aktiftir</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle small mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">Görsel</th>
                        <th>Ürün Adı</th>
                        <th>Kategori</th>
                        <th class="text-end">1.000 Adet Fiyatı</th>
                        <th class="text-center">Paketler</th>
                        <th class="text-center">Durum</th>
                        <th class="text-end">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-box fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Kayıtlı ürün bulunamadı. <a href="<?= SITE_URL ?>/admin/products.php?action=add">Hemen yeni ürün ekleyin →</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($p['featured_image'])): ?>
                                        <img src="<?= SITE_URL . '/' . htmlspecialchars($p['featured_image']) ?>" 
                                             alt="<?= htmlspecialchars($p['name']) ?>" 
                                             class="rounded-3 border shadow-2xs" 
                                             style="width: 48px; height: 48px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="rounded-3 bg-light border d-flex align-items-center justify-content-center text-muted" style="width: 48px; height: 48px;">
                                            <i class="bi bi-card-image fs-5"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($p['name']) ?></div>
                                    <div class="text-muted font-monospace" style="font-size: 11px;">
                                        slug: <?= htmlspecialchars($p['slug']) ?> 
                                        <?= !empty($p['sku']) ? ' | SKU: ' . htmlspecialchars($p['sku']) : '' ?>
                                    </div>
                                    <div class="mt-1 d-flex gap-1">
                                        <?php if (!empty($p['is_featured'])): ?>
                                            <span class="badge bg-warning-subtle text-dark" style="font-size: 9px;"><i class="bi bi-star-fill text-warning me-1"></i>Vitrin</span>
                                        <?php endif; ?>
                                        <?php if (!empty($p['is_urgent'])): ?>
                                            <span class="badge bg-danger-subtle text-danger" style="font-size: 9px;"><i class="bi bi-lightning-fill me-1"></i>Acil</span>
                                        <?php endif; ?>
                                        <?php if (!empty($p['allow_online_editor'])): ?>
                                            <span class="badge bg-primary-subtle text-primary" style="font-size: 9px;"><i class="bi bi-palette-fill me-1"></i>Canva</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= htmlspecialchars($p['category_name'] ?? 'Genel') ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="fw-bold text-dark fs-6"><?= Helper::formatPrice($p['base_price']) ?></div>
                                    <small class="text-muted" style="font-size: 10px;">+KDV / 1.000 Adet</small>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $pPresets = !empty($p['package_presets']) ? json_decode($p['package_presets'], true) : [];
                                    ?>
                                    <div class="d-flex justify-content-center gap-1">
                                        <span class="badge <?= (!isset($pPresets['ekonomik']) || !empty($pPresets['ekonomik']['active'])) ? 'bg-secondary' : 'bg-light text-muted border' ?>" style="font-size: 9px;" title="Ekonomik">E</span>
                                        <span class="badge <?= (!isset($pPresets['standart']) || !empty($pPresets['standart']['active'])) ? 'bg-primary' : 'bg-light text-muted border' ?>" style="font-size: 9px;" title="Standart">S</span>
                                        <span class="badge <?= (!isset($pPresets['premium']) || !empty($pPresets['premium']['active'])) ? 'text-white' : 'bg-light text-muted border' ?>" style="font-size: 9px; <?= (!isset($pPresets['premium']) || !empty($pPresets['premium']['active'])) ? 'background: #8b5cf6;' : '' ?>" title="Premium">P</span>
                                        <span class="badge <?= (!isset($pPresets['vip']) || !empty($pPresets['vip']['active'])) ? 'bg-warning text-dark' : 'bg-light text-muted border' ?>" style="font-size: 9px;" title="VIP">V</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php if (!empty($p['status'])): ?>
                                        <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">Yayında</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1">Pasif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= SITE_URL ?>/product.php?slug=<?= $p['slug'] ?>" target="_blank" class="btn btn-outline-secondary" title="Sitede Canlı İncele">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </a>
                                        <a href="<?= SITE_URL ?>/admin/products.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-outline-primary" title="Düzenle">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="<?= SITE_URL ?>/admin/products.php?action=delete&id=<?= $p['id'] ?>" 
                                           class="btn btn-outline-danger" 
                                           onclick="return confirm('Bu ürünü silmek istediğinize emin misiniz?');" 
                                           title="Sil">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php
}
require_once __DIR__ . '/footer.php';
?>
