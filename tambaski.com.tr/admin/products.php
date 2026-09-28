<?php
/**
 * TAMBASKI.COM.TR - Admin Tüm Ürünler Listesi
 */
$page_title = "Ürün Yönetimi";
require_once __DIR__ . '/header.php';

$products = get_all_products();
?>

<div class="row g-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-box-seam text-primary me-2"></i>Tüm Ürünler & Fiyat Matrisleri</h3>
            <p class="text-muted small mb-0">Toplam <?= count($products) ?> adet aktif baskı, reklam ve kesim ürünü</p>
        </div>
        <a href="product_add.php" class="btn btn-apple btn-apple-orange">
            <i class="bi bi-plus-lg me-1"></i> Yeni Ürün Ekle
        </a>
    </div>

    <div class="col-12">
        <div class="apple-card p-4 bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light small">
                        <tr>
                            <th style="width: 70px;">Görsel</th>
                            <th>Ürün Adı</th>
                            <th>Kategori</th>
                            <th>Fiyatlandırma Modeli</th>
                            <th>Başlangıç Fiyatı</th>
                            <th>3D Mockup</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td>
                                    <div class="p-2 bg-light rounded-3 text-center text-primary">
                                        <i class="bi <?= ($p['pricing_type'] === 'sqm_calculator' ? 'bi-layers' : 'bi-box-seam') ?> fs-4"></i>
                                    </div>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($p['name']) ?></strong>
                                    <small class="text-muted d-block"><?= htmlspecialchars($p['short_desc']) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?= ucfirst(str_replace('-', ' ', $p['category_slug'])) ?></span>
                                </td>
                                <td>
                                    <?php if ($p['pricing_type'] === 'sqm_calculator'): ?>
                                        <span class="badge bg-info-subtle text-info border border-info">m² Hesaplayıcı</span>
                                    <?php else: ?>
                                        <span class="badge bg-success-subtle text-success border border-success">Hazır Paket + Özel Adet</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($p['pricing_type'] === 'sqm_calculator'): ?>
                                        <strong class="text-dark"><?= format_price($p['base_sqm_price']) ?> / m²</strong>
                                    <?php elseif (!empty($p['packages'])): ?>
                                        <strong class="text-danger"><?= format_price($p['packages'][0]['price']) ?></strong>
                                        <small class="text-muted d-block" style="font-size: 10px;"><?= $p['packages'][0]['quantity'] ?> Adet</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-primary rounded-pill"><i class="bi bi-layers-half me-1"></i>3D Aktif</span>
                                </td>
                                <td>
                                    <span class="badge bg-success">Yayında</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="../product.php?slug=<?= urlencode($p['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="Sitede Gör">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="product_add.php?edit=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary" title="Düzenle">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
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
