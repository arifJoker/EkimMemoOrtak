<?php
/**
 * TAMBASKI.COM.TR - Admin Kategori Yönetimi
 */
$page_title = "Kategoriler";
require_once __DIR__ . '/header.php';

$categories = get_all_categories();
?>

<div class="row g-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-grid-fill text-primary me-2"></i>Kategori Yönetimi</h3>
            <p class="text-muted small mb-0">Toplam <?= count($categories) ?> adet ana ve alt kategori</p>
        </div>
        <button class="btn btn-apple btn-apple-orange" data-bs-toggle="modal" data-bs-target="#newCategoryModal">
            <i class="bi bi-plus-lg me-1"></i> Yeni Kategori Ekle
        </button>
    </div>

    <div class="col-12">
        <div class="apple-card p-4 bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light small">
                        <tr>
                            <th style="width: 60px;">İkon</th>
                            <th>Kategori Adı</th>
                            <th>URL Slug</th>
                            <th>Açıklama</th>
                            <th>Sıralama</th>
                            <th>Durum</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td>
                                    <div class="p-2 bg-light rounded text-center text-primary">
                                        <i class="bi <?= htmlspecialchars($cat['icon']) ?> fs-5"></i>
                                    </div>
                                </td>
                                <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
                                <td><code><?= htmlspecialchars($cat['slug']) ?></code></td>
                                <td class="small text-muted"><?= htmlspecialchars($cat['description'] ?? '') ?></td>
                                <td><?= $cat['sort_order'] ?? 1 ?></td>
                                <td><span class="badge bg-success">Aktif</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary border-0"><i class="bi bi-pencil"></i></button>
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
