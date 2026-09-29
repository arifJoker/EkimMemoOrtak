<?php
require_once __DIR__ . '/../config/config.php';
Auth::requireAdmin();

$db = Database::getInstance()->getConnection();
$action = $_GET['action'] ?? 'list';

// pricing_model kolon kontrolü ve ekleme
try {
    $colCheck = $db->query("SHOW COLUMNS FROM categories LIKE 'pricing_model'")->fetch();
    if (!$colCheck) {
        $db->exec("ALTER TABLE categories ADD COLUMN pricing_model VARCHAR(50) DEFAULT 'package_tier'");
    }
} catch (Exception $ex) {}

// Otomatik Temizlik: Kartvizit ürünlerini Kartvizit kategorisine bağla
$kartvizitCat = $db->query("SELECT id FROM categories WHERE slug = 'kartvizit' LIMIT 1")->fetch();
if ($kartvizitCat) {
    $db->prepare("UPDATE products SET category_id = ? WHERE (name LIKE '%Kartvizit%' OR slug LIKE '%kartvizit%') AND category_id != ?")
       ->execute([$kartvizitCat['id'], $kartvizitCat['id']]);
    $db->prepare("UPDATE categories SET pricing_model = 'package_tier' WHERE id = ?")->execute([$kartvizitCat['id']]);
}

// 1. Silme İşlemi
if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    // Kategoriye bağlı ürün var mı kontrol et
    $countStmt = $db->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $countStmt->execute([$id]);
    $prodCount = (int)$countStmt->fetchColumn();

    if ($prodCount > 0) {
        Helper::setFlash('danger', "Bu kategoriye bağlı {$prodCount} adet ürün bulunmaktadır. Önce ürünlerin kategorisini değiştirin veya ürünleri silin.");
    } else {
        $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        Helper::setFlash('success', 'Kategori başarıyla silindi.');
    }
    header("Location: " . SITE_URL . "/admin/categories.php");
    exit;
}

// 2. Ekleme / Güncelleme POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $slug = !empty($_POST['slug']) ? Helper::slugify($_POST['slug']) : Helper::slugify($name);
    $icon = trim($_POST['icon'] ?? 'bi bi-grid');
    $pricingModel = trim($_POST['pricing_model'] ?? 'package_tier');
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $status = !empty($_POST['status']) ? 1 : 0;
    $catId = (int)($_POST['category_id'] ?? 0);

    if (empty($name)) {
        Helper::setFlash('danger', 'Kategori adı boş bırakılamaz.');
        header("Location: " . SITE_URL . "/admin/categories.php");
        exit;
    }

    if ($catId > 0) {
        $stmt = $db->prepare("UPDATE categories SET name = ?, slug = ?, icon = ?, pricing_model = ?, sort_order = ?, status = ? WHERE id = ?");
        $stmt->execute([$name, $slug, $icon, $pricingModel, $sortOrder, $status, $catId]);
        Helper::setFlash('success', "<strong>{$name}</strong> kategorisi güncellendi.");
    } else {
        $stmt = $db->prepare("INSERT INTO categories (name, slug, icon, pricing_model, sort_order, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $slug, $icon, $pricingModel, $sortOrder, $status]);
        Helper::setFlash('success', "Yeni kategori <strong>{$name}</strong> başarıyla eklendi.");
    }
    header("Location: " . SITE_URL . "/admin/categories.php");
    exit;
}

// Düzenleme Modu Kontrolü
$editCategory = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$editId]);
    $editCategory = $stmt->fetch();
}

// Tüm Kategorileri ve Ürün Sayılarını Çek
$categories = $db->query("
    SELECT c.*, COUNT(p.id) AS product_count 
    FROM categories c 
    LEFT JOIN products p ON p.category_id = c.id 
    GROUP BY c.id 
    ORDER BY c.sort_order ASC, c.id ASC
")->fetchAll();

$pageTitle = 'Kategori Yönetimi (1. Faz)';
require_once __DIR__ . '/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-folder2-open text-primary me-2"></i>Kategori Yönetimi</h4>
        <p class="text-muted small mb-0">Ürün kategorilerini ekleyin, menü ikonlarını belirleyin ve sıralamayı yönetin.</p>
    </div>
    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill font-monospace">
        Toplam: <?= count($categories) ?> Kategori
    </span>
</div>

<div class="row g-4">
    <!-- Sol: Kategori Ekleme / Düzenleme Formu -->
    <div class="col-lg-4">
        <div class="apple-card p-4 sticky-top" style="top: 20px;">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                <h6 class="fw-bold mb-0 text-dark">
                    <?php if ($editCategory): ?>
                        <i class="bi bi-pencil-square text-warning me-1"></i> Kategoriyi Düzenle
                    <?php else: ?>
                        <i class="bi bi-plus-circle-fill text-success me-1"></i> Yeni Kategori Ekle
                    <?php endif; ?>
                </h6>
                <?php if ($editCategory): ?>
                    <a href="<?= SITE_URL ?>/admin/categories.php" class="btn btn-sm btn-outline-secondary py-0">İptal</a>
                <?php endif; ?>
            </div>

            <form action="<?= SITE_URL ?>/admin/categories.php" method="POST">
                <input type="hidden" name="category_id" value="<?= $editCategory['id'] ?? 0 ?>">

                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Kategori Adı *</label>
                    <input type="text" name="name" id="catNameInput" class="form-control" required 
                           placeholder="Örn: Kartvizitler, Broşürler, Tabelalar" 
                           value="<?= htmlspecialchars($editCategory['name'] ?? '') ?>"
                           oninput="autoGenerateSlug(this.value)">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Slug (URL)</label>
                    <input type="text" name="slug" id="catSlugInput" class="form-control font-monospace" 
                           placeholder="kartvizitler" 
                           value="<?= htmlspecialchars($editCategory['slug'] ?? '') ?>">
                    <small class="text-muted" style="font-size: 11px;">Boş bırakılırsa isimden otomatik üretilir.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark d-flex justify-content-between">
                        <span>Bootstrap İkon Sınıfı</span>
                        <span id="iconPreview" class="text-primary"><i class="<?= htmlspecialchars($editCategory['icon'] ?? 'bi bi-tag') ?> fs-6"></i></span>
                    </label>
                    <input type="text" name="icon" id="catIconInput" class="form-control font-monospace" 
                           value="<?= htmlspecialchars($editCategory['icon'] ?? 'bi bi-tag') ?>" 
                           placeholder="bi bi-tag"
                           oninput="updateIconPreview(this.value)">
                    
                    <!-- Hızlı İkon Seçici -->
                    <div class="mt-2">
                        <small class="text-muted d-block mb-1" style="font-size: 11px;">Hızlı İkon Seç:</small>
                        <div class="d-flex flex-wrap gap-1">
                            <?php 
                            $quickIcons = [
                                'bi bi-person-badge', 'bi bi-file-earmark-text', 'bi bi-tag', 
                                'bi bi-card-text', 'bi bi-flag', 'bi bi-stamp', 'bi bi-cup-hot', 
                                'bi bi-scissors', 'bi bi-box-seam', 'bi bi-grid', 'bi bi-printer', 'bi bi-stars'
                            ];
                            foreach ($quickIcons as $qIcon):
                            ?>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" 
                                        onclick="selectQuickIcon('<?= $qIcon ?>')" title="<?= $qIcon ?>">
                                    <i class="<?= $qIcon ?>"></i>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Hesaplama &amp; Fiyatlandırma Modeli *</label>
                    <select name="pricing_model" class="form-select" required>
                        <option value="package_tier" <?= (($editCategory['pricing_model'] ?? 'package_tier') === 'package_tier') ? 'selected' : '' ?>>
                            🎨 Paket &amp; Tiraj Modeli (Kartvizit, Broşür - 4 Paket + Canva)
                        </option>
                        <option value="m2_calculator" <?= (($editCategory['pricing_model'] ?? '') === 'm2_calculator') ? 'selected' : '' ?>>
                            📐 Dinamik Metrekare (m²) Modeli (Araç Sticker, Branda, Folyo)
                        </option>
                        <option value="rigid_board" <?= (($editCategory['pricing_model'] ?? '') === 'rigid_board') ? 'selected' : '' ?>>
                            🛡️ Sert Zemin &amp; Levha Modeli (Dekota, Pleksi, Fotoblok)
                        </option>
                        <option value="tiered_qty" <?= (($editCategory['pricing_model'] ?? '') === 'tiered_qty') ? 'selected' : '' ?>>
                            🎁 Kademeli Parça/Adet Modeli (Promosyon, Tişört, Magnet)
                        </option>
                    </select>
                    <small class="text-muted" style="font-size: 11px;">Bu kategorideki ürünlerin hesaplama ve varyant motorunu belirler.</small>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark">Sıralama</label>
                        <input type="number" name="sort_order" class="form-control" 
                               value="<?= (int)($editCategory['sort_order'] ?? 0) ?>">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark">Durum</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="status" id="catStatus" value="1" 
                                   <?= (!isset($editCategory) || !empty($editCategory['status'])) ? 'checked' : '' ?>>
                            <label class="form-check-label small fw-semibold" for="catStatus">Aktif</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold shadow-xs">
                    <i class="bi bi-check-lg me-1"></i> 
                    <?= $editCategory ? 'Değişiklikleri Kaydet' : 'Kategoriyi Ekle' ?>
                </button>
            </form>
        </div>
    </div>

    <!-- Sağ: Kategori Listesi -->
    <div class="col-lg-8">
        <div class="apple-card p-4">
            <h6 class="fw-bold mb-3 border-bottom pb-2">Kayıtlı Kategoriler</h6>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">İkon</th>
                            <th>Kategori Adı</th>
                            <th>Hesaplama &amp; Varyant Modeli</th>
                            <th>Slug (URL)</th>
                            <th class="text-center">Ürün Sayısı</th>
                            <th class="text-center">Sıra</th>
                            <th class="text-center">Durum</th>
                            <th class="text-end">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Henüz kayıtlı kategori bulunmuyor.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categories as $cat): 
                                $pModel = $cat['pricing_model'] ?? 'package_tier';
                            ?>
                                <tr class="<?= (isset($editCategory) && $editCategory['id'] == $cat['id']) ? 'table-warning' : '' ?>">
                                    <td class="text-center">
                                        <div class="rounded-3 bg-light p-2 d-inline-flex align-items-center justify-content-center text-primary shadow-xs" style="width: 36px; height: 36px;">
                                            <i class="<?= htmlspecialchars($cat['icon']) ?> fs-5"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($cat['name']) ?></div>
                                        <a href="<?= SITE_URL ?>/category.php?slug=<?= $cat['slug'] ?>" target="_blank" class="text-muted" style="font-size: 11px;">
                                            <i class="bi bi-box-arrow-up-right me-1"></i>Sitede Gör
                                        </a>
                                    </td>
                                    <td>
                                        <?php if ($pModel === 'package_tier'): ?>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                                <i class="bi bi-box-seam me-1"></i>4 Paket &amp; Tiraj Modeli
                                            </span>
                                        <?php elseif ($pModel === 'm2_calculator'): ?>
                                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                                                <i class="bi bi-aspect-ratio me-1"></i>Dinamik Metrekare (m²)
                                            </span>
                                        <?php elseif ($pModel === 'rigid_board'): ?>
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                                <i class="bi bi-layers me-1"></i>Sert Zemin &amp; Levha
                                            </span>
                                        <?php elseif ($pModel === 'tiered_qty'): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                <i class="bi bi-tags me-1"></i>Kademeli Parça/Adet
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary px-2 py-1">Standart</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <code class="text-secondary"><?= htmlspecialchars($cat['slug']) ?></code>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border font-monospace px-2.5 py-1">
                                            <?= (int)$cat['product_count'] ?> Ürün
                                        </span>
                                    </td>
                                    <td class="text-center font-monospace">
                                        <?= (int)$cat['sort_order'] ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($cat['status'])): ?>
                                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1">Pasif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= SITE_URL ?>/admin/categories.php?edit=<?= $cat['id'] ?>" class="btn btn-outline-primary" title="Düzenle">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="<?= SITE_URL ?>/admin/categories.php?action=delete&id=<?= $cat['id'] ?>" 
                                               class="btn btn-outline-danger" 
                                               onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?');" 
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
    </div>
</div>

<script>
function autoGenerateSlug(text) {
    const slugInput = document.getElementById('catSlugInput');
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

document.getElementById('catSlugInput')?.addEventListener('input', function() {
    this.dataset.manual = 'true';
});

function updateIconPreview(iconClass) {
    const preview = document.getElementById('iconPreview');
    if (preview) {
        preview.innerHTML = '<i class="' + iconClass + ' fs-6"></i>';
    }
}

function selectQuickIcon(iconClass) {
    const input = document.getElementById('catIconInput');
    if (input) {
        input.value = iconClass;
        updateIconPreview(iconClass);
    }
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
