<?php
require_once __DIR__ . '/../config/config.php';
Auth::requireAdmin();

$db = Database::getInstance()->getConnection();
$action = $_GET['action'] ?? 'list';

if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    Helper::setFlash('success', 'Kategori silindi.');
    header("Location: " . SITE_URL . "/admin/categories.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $slug = !empty($_POST['slug']) ? Helper::slugify($_POST['slug']) : Helper::slugify($name);
    $icon = trim($_POST['icon'] ?? 'bi bi-grid');
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $catId = (int)($_POST['category_id'] ?? 0);

    if ($catId > 0) {
        $stmt = $db->prepare("UPDATE categories SET name = ?, slug = ?, icon = ?, sort_order = ? WHERE id = ?");
        $stmt->execute([$name, $slug, $icon, $sortOrder, $catId]);
        Helper::setFlash('success', 'Kategori güncellendi.');
    } else {
        $stmt = $db->prepare("INSERT INTO categories (name, slug, icon, sort_order) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $slug, $icon, $sortOrder]);
        Helper::setFlash('success', 'Yeni kategori eklendi.');
    }
    header("Location: " . SITE_URL . "/admin/categories.php");
    exit;
}

$categories = $db->query("SELECT * FROM categories ORDER BY sort_order ASC, id ASC")->fetchAll();
$pageTitle = 'Kategori Yönetimi';
require_once __DIR__ . '/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Kategoriler</h4>
        <p class="text-muted small mb-0">Matbaa ürün kategorilerini ve menü simgelerini yönetin.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Sol: Kategori Ekleme Formu -->
    <div class="col-lg-4">
        <div class="apple-card p-4">
            <h6 class="fw-bold mb-3 border-bottom pb-2">Yeni Kategori Ekle</h6>
            <form action="<?= SITE_URL ?>/admin/categories.php" method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Kategori Adı *</label>
                    <input type="text" name="name" class="form-control" required placeholder="Örn: Etiket & Sticker">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Bootstrap İkon Sınıfı</label>
                    <input type="text" name="icon" class="form-control" value="bi bi-tag" placeholder="bi bi-tag">
                    <small class="text-muted" style="font-size: 11px;">Örn: bi bi-person-badge, bi bi-flag, bi bi-stamp</small>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Sıralama</label>
                    <input type="number" name="sort_order" class="form-control" value="0">
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                    <i class="bi bi-plus-lg me-1"></i> Kategoriyi Kaydet
                </button>
            </form>
        </div>
    </div>

    <!-- Sağ: Kategori Listesi -->
    <div class="col-lg-8">
        <div class="apple-card p-4">
            <h6 class="fw-bold mb-3 border-bottom pb-2">Kayıtlı Kategoriler (<?= count($categories) ?>)</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle small">
                    <thead class="table-light">
                        <tr>
                            <th>İkon</th>
                            <th>Kategori Adı</th>
                            <th>Slug (URL)</th>
                            <th>Sıra</th>
                            <th class="text-end">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td style="width: 40px;"><i class="<?= $cat['icon'] ?> fs-5 text-primary"></i></td>
                                <td class="fw-bold"><?= htmlspecialchars($cat['name']) ?></td>
                                <td><code><?= htmlspecialchars($cat['slug']) ?></code></td>
                                <td><?= $cat['sort_order'] ?></td>
                                <td class="text-end">
                                    <a href="<?= SITE_URL ?>/admin/categories.php?action=delete&id=<?= $cat['id'] ?>" class="btn btn-sm btn-outline-danger py-0" onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?');">
                                        Sil
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

<?php require_once __DIR__ . '/footer.php'; ?>
