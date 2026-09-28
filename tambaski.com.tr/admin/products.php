<?php
require_once __DIR__ . '/../config/config.php';
Auth::requireAdmin();

$db = Database::getInstance()->getConnection();
$action = $_GET['action'] ?? 'list';

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

    // 4 Paket Önayarları (JSON)
    $packagePresets = json_encode([
        'ekonomik' => [
            'active' => !empty($_POST['pkg_ekonomik_active']) ? 1 : 0,
            'price'  => (float)str_replace(',', '.', $_POST['pkg_ekonomik_price'] ?? ($basePrice * 0.85)),
            'desc'   => trim($_POST['pkg_ekonomik_desc'] ?? '250gr Bristol, Tek Yön Renkli')
        ],
        'standart' => [
            'active' => !empty($_POST['pkg_standart_active']) ? 1 : 0,
            'price'  => (float)str_replace(',', '.', $_POST['pkg_standart_price'] ?? $basePrice),
            'desc'   => trim($_POST['pkg_standart_desc'] ?? '350gr Kuşe, Çift Taraf Mat Selefon')
        ],
        'premium'  => [
            'active' => !empty($_POST['pkg_premium_active']) ? 1 : 0,
            'price'  => (float)str_replace(',', '.', $_POST['pkg_premium_price'] ?? ($basePrice * 1.45)),
            'desc'   => trim($_POST['pkg_premium_desc'] ?? 'Soft-Touch Kadife Selefon & Kabartma Lak')
        ],
        'vip'      => [
            'active' => !empty($_POST['pkg_vip_active']) ? 1 : 0,
            'price'  => (float)str_replace(',', '.', $_POST['pkg_vip_price'] ?? ($basePrice * 1.85)),
            'desc'   => trim($_POST['pkg_vip_desc'] ?? 'Tuale Fantezi / Altın Varak Yaldız')
        ],
    ], JSON_UNESCAPED_UNICODE);

    // İzinler ve Özellikler
    $allowOnlineEditor = !empty($_POST['allow_online_editor']) ? 1 : 0;
    $allowDesignUpload = !empty($_POST['allow_design_upload']) ? 1 : 0;
    $allowDesignService = !empty($_POST['allow_design_service']) ? 1 : 0;
    $designServicePrice = (float)str_replace(',', '.', $_POST['design_service_price'] ?? 150.00);
    $isFeatured = !empty($_POST['is_featured']) ? 1 : 0;
    $isUrgent = !empty($_POST['is_urgent']) ? 1 : 0;
    $status = !empty($_POST['status']) ? 1 : 0;

    // Görsel Yükleme
    $featuredImage = trim($_POST['existing_image'] ?? '');
    if (!empty($_FILES['image']['name'])) {
        $up = Helper::uploadImageAsWebp($_FILES['image'], 'products', 85);
        if ($up['success']) {
            $featuredImage = $up['file_path'];
        }
    }

    try {
        if ($productId > 0) {
            // Güncelle
            $stmt = $db->prepare("UPDATE products SET 
                category_id = ?, name = ?, slug = ?, sku = ?, short_description = ?, full_description = ?,
                base_price = ?, manual_base_price = ?, tax_rate = ?, package_presets = ?,
                allow_online_editor = ?, allow_design_upload = ?, allow_design_service = ?, design_service_price = ?,
                is_featured = ?, is_urgent = ?, status = ?, featured_image = ?
                WHERE id = ?");
            
            $stmt->execute([
                $categoryId, $name, $slug, $sku, $shortDesc, $fullDesc,
                $basePrice, $basePrice, $taxRate, $packagePresets,
                $allowOnlineEditor, $allowDesignUpload, $allowDesignService, $designServicePrice,
                $isFeatured, $isUrgent, $status, $featuredImage, $productId
            ]);

            Helper::setFlash('success', "<strong>{$name}</strong> ürünü başarıyla güncellendi.");
        } else {
            // Yeni Ekle
            $stmt = $db->prepare("INSERT INTO products (
                category_id, name, slug, sku, short_description, full_description,
                base_price, manual_base_price, tax_rate, package_presets,
                allow_online_editor, allow_design_upload, allow_design_service, design_service_price,
                is_featured, is_urgent, status, featured_image
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([
                $categoryId, $name, $slug, $sku, $shortDesc, $fullDesc,
                $basePrice, $basePrice, $taxRate, $packagePresets,
                $allowOnlineEditor, $allowDesignUpload, $allowDesignService, $designServicePrice,
                $isFeatured, $isUrgent, $status, $featuredImage
            ]);

            $productId = (int)$db->lastInsertId();
            Helper::setFlash('success', "Yeni ürün <strong>{$name}</strong> başarıyla eklendi.");
        }

        // Adet Tiraj Kademelerini Güncelle / Ekle
        $db->prepare("DELETE FROM product_quantity_tiers WHERE product_id = ?")->execute([$productId]);
        $tiersInput = $_POST['tiers'] ?? [
            1000  => 0,
            2000  => 15,
            3000  => 22,
            5000  => 30,
            10000 => 38
        ];
        foreach ($tiersInput as $qty => $discount) {
            $q = (int)$qty;
            $d = (float)$discount;
            if ($q > 0) {
                $m = max(0.1, 1 - ($d / 100));
                $tStmt = $db->prepare("INSERT INTO product_quantity_tiers (product_id, quantity, multiplier, discount_percent) VALUES (?, ?, ?, ?)");
                $tStmt->execute([$productId, $q, $m, $d]);
            }
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

                <!-- 2. Fiyatlandırma & 4 Hazır Paket Kartı -->
                <div class="apple-card p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-tag text-success me-2"></i>2. Fiyatlandırma &amp; 4 Standart Paket
                        </h6>
                        <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-pill">Net Fiyat Modeli</span>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">1.000 Adet Standart Taban Fiyatı (₺) *</label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold text-primary">₺</span>
                                <input type="number" step="0.01" name="base_price" id="basePriceInput" class="form-control form-control-lg fw-bold text-dark" required
                                       placeholder="900.00"
                                       value="<?= htmlspecialchars($product['base_price'] ?? '900.00') ?>"
                                       oninput="updatePackagePriceSuggestions(this.value)">
                            </div>
                            <small class="text-muted" style="font-size: 11px;">Müşteri standart paketi seçtiğinde 1.000 adet için geçerli baz fiyattır (+KDV).</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">KDV Oranı (%)</label>
                            <div class="input-group">
                                <input type="number" step="1" name="tax_rate" class="form-control form-control-lg" value="<?= htmlspecialchars($product['tax_rate'] ?? '20.00') ?>">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>

                    <!-- 4 Hazır Paket Tablosu -->
                    <label class="form-label small fw-bold text-dark mb-2">4 Hazır Paket Ayarları (Müşterinin Seçeceği Kartlar):</label>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle small mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;" class="text-center">Aktif</th>
                                    <th style="width: 130px;">Paket</th>
                                    <th style="width: 160px;">1.000 Adet Fiyatı (₺)</th>
                                    <th>Paket Özellik Açıklaması</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- 1. Ekonomik -->
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" name="pkg_ekonomik_active" value="1" class="form-check-input"
                                               <?= (!isset($presets['ekonomik']) || !empty($presets['ekonomik']['active'])) ? 'checked' : '' ?>>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary px-2 py-1 fw-bold">Ekonomik</span>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">₺</span>
                                            <input type="number" step="0.01" name="pkg_ekonomik_price" id="pkgEkoPrice" class="form-control font-monospace"
                                                   value="<?= htmlspecialchars($presets['ekonomik']['price'] ?? ($product['base_price'] ?? 750.00)) ?>">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="pkg_ekonomik_desc" class="form-control form-control-sm"
                                               placeholder="250gr Bristol, Tek Yön Renkli"
                                               value="<?= htmlspecialchars($presets['ekonomik']['desc'] ?? '250gr Bristol, Tek Yön Düz Baskı') ?>">
                                    </td>
                                </tr>

                                <!-- 2. Standart -->
                                <tr class="table-primary-subtle">
                                    <td class="text-center">
                                        <input type="checkbox" name="pkg_standart_active" value="1" class="form-check-input"
                                               <?= (!isset($presets['standart']) || !empty($presets['standart']['active'])) ? 'checked' : '' ?>>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary text-white px-2 py-1 fw-bold">Standart</span>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">₺</span>
                                            <input type="number" step="0.01" name="pkg_standart_price" id="pkgStdPrice" class="form-control font-monospace fw-bold"
                                                   value="<?= htmlspecialchars($presets['standart']['price'] ?? ($product['base_price'] ?? 900.00)) ?>">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="pkg_standart_desc" class="form-control form-control-sm"
                                               placeholder="350gr Kuşe, Çift Taraf Mat Selefon"
                                               value="<?= htmlspecialchars($presets['standart']['desc'] ?? '350gr Kuşe, Çift Taraf Mat Selefon') ?>">
                                    </td>
                                </tr>

                                <!-- 3. Premium -->
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" name="pkg_premium_active" value="1" class="form-check-input"
                                               <?= (!isset($presets['premium']) || !empty($presets['premium']['active'])) ? 'checked' : '' ?>>
                                    </td>
                                    <td>
                                        <span class="badge text-white px-2 py-1 fw-bold" style="background: #8b5cf6;">Premium</span>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">₺</span>
                                            <input type="number" step="0.01" name="pkg_premium_price" id="pkgPremPrice" class="form-control font-monospace"
                                                   value="<?= htmlspecialchars($presets['premium']['price'] ?? (round(($product['base_price'] ?? 900) * 1.45, 2))) ?>">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="pkg_premium_desc" class="form-control form-control-sm"
                                               placeholder="Soft-Touch Kadife Selefon & Kabartma Lak"
                                               value="<?= htmlspecialchars($presets['premium']['desc'] ?? 'Soft-Touch Kadife Selefon & Kabartma Lak') ?>">
                                    </td>
                                </tr>

                                <!-- 4. VIP -->
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" name="pkg_vip_active" value="1" class="form-check-input"
                                               <?= (!isset($presets['vip']) || !empty($presets['vip']['active'])) ? 'checked' : '' ?>>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning text-dark px-2 py-1 fw-bold">VIP Prestij</span>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">₺</span>
                                            <input type="number" step="0.01" name="pkg_vip_price" id="pkgVipPrice" class="form-control font-monospace"
                                                   value="<?= htmlspecialchars($presets['vip']['price'] ?? (round(($product['base_price'] ?? 900) * 1.85, 2))) ?>">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="pkg_vip_desc" class="form-control form-control-sm"
                                               placeholder="Tuale Fantezi / Altın Varak Yaldız"
                                               value="<?= htmlspecialchars($presets['vip']['desc'] ?? 'Tuale Fantezi / Altın Varak Yaldız') ?>">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 3. Adet Tiraj İndirimleri Kartı -->
                <div class="apple-card p-4">
                    <h6 class="fw-bold mb-3 border-bottom pb-2 text-dark">
                        <i class="bi bi-layers text-primary me-2"></i>3. Adet / Tiraj İndirim Oranları (%)
                    </h6>
                    <p class="text-muted small mb-3">Tiraj arttıkça birim fiyata uygulanacak indirim yüzdesidir. 1.000 adet fiyatı baz alınarak sistem otomatik çarpar.</p>

                    <?php
                    $tierMap = [];
                    foreach ($tiers as $t) {
                        $tierMap[(int)$t['quantity']] = (float)$t['discount_percent'];
                    }
                    ?>
                    <div class="row g-2">
                        <div class="col">
                            <label class="form-label small fw-bold text-center d-block mb-1">1.000 Adet</label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="tiers[1000]" class="form-control text-center font-monospace" value="<?= $tierMap[1000] ?? 0 ?>" readonly>
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="text-muted text-center d-block" style="font-size: 10px;">(Baz Fiyat)</small>
                        </div>
                        <div class="col">
                            <label class="form-label small fw-bold text-center d-block mb-1">2.000 Adet</label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="tiers[2000]" class="form-control text-center font-monospace" value="<?= $tierMap[2000] ?? 15 ?>">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label small fw-bold text-center d-block mb-1">3.000 Adet</label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="tiers[3000]" class="form-control text-center font-monospace" value="<?= $tierMap[3000] ?? 22 ?>">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label small fw-bold text-center d-block mb-1">5.000 Adet</label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="tiers[5000]" class="form-control text-center font-monospace" value="<?= $tierMap[5000] ?? 30 ?>">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label small fw-bold text-center d-block mb-1">10.000 Adet</label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="tiers[10000]" class="form-control text-center font-monospace" value="<?= $tierMap[10000] ?? 38 ?>">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
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

                <!-- Ürün Görseli Kartı -->
                <div class="apple-card p-4 mb-4">
                    <h6 class="fw-bold mb-3 border-bottom pb-2 text-dark">
                        <i class="bi bi-image text-primary me-2"></i>Ürün Görseli
                    </h6>

                    <?php if (!empty($product['featured_image'])): ?>
                        <div class="mb-3 text-center">
                            <img src="<?= SITE_URL . '/' . htmlspecialchars($product['featured_image']) ?>" 
                                 alt="Ürün Görseli" class="rounded-3 shadow-xs border img-fluid" style="max-height: 160px; object-fit: contain;">
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">Mevcut Görsel</small>
                        </div>
                    <?php endif; ?>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark">Kapak Görseli Yükle</label>
                        <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
                        <small class="text-muted" style="font-size: 11px;">JPG, PNG veya WebP. Otomatik optimize edilir.</small>
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

    ['pkgEkoPrice', 'pkgPremPrice', 'pkgVipPrice'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', function() {
            this.dataset.manual = 'true';
        });
    });
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
