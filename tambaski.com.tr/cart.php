<?php
/**
 * TAMBASKI.COM.TR - Sepetim Sayfası
 */
require_once __DIR__ . '/includes/functions.php';

// Ürün Silme
if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] === $remove_id) {
                unset($_SESSION['cart'][$key]);
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                set_flash_message('success', 'Ürün sepetten kaldırıldı.');
                break;
            }
        }
    }
    header("Location: cart.php");
    exit;
}

$page_title = "Sepetim – TamBaskı";
require_once __DIR__ . '/includes/header.php';

$cart = get_cart();
$totals = get_cart_totals();
?>

<div class="container py-4">
    <h3 class="fw-bold mb-4"><i class="bi bi-bag-fill text-primary me-2"></i>Alışveriş Sepetiniz</h3>

    <?php if (empty($cart)): ?>
        <div class="apple-card p-5 text-center my-4">
            <i class="bi bi-cart-x text-muted" style="font-size: 80px;"></i>
            <h4 class="fw-bold mt-3 mb-2">Sepetiniz Boş</h4>
            <p class="text-muted small mb-4">Sepetinizde henüz bir ürün bulunmuyor. İhtiyacınıza uygun baskı veya kesim ürününü hemen seçebilirsiniz.</p>
            <a href="category.php" class="btn btn-apple btn-apple-orange px-4 py-2">
                <i class="bi bi-grid me-2"></i> Ürünleri Keşfet
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <!-- Sol: Sepet Ürün Listesi -->
            <div class="col-lg-8">
                <div class="apple-card p-4">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr class="text-muted small">
                                    <th>Ürün & Özellikler</th>
                                    <th>Tasarım</th>
                                    <th>Adet</th>
                                    <th>Tutar</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($item['product_name']) ?></div>
                                            <small class="text-muted d-block"><?= htmlspecialchars($item['specs']) ?></small>
                                            <?php if (!empty($item['design_note'])): ?>
                                                <small class="text-info d-block"><i class="bi bi-chat-left-text me-1"></i>Not: <?= htmlspecialchars($item['design_note']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($item['design_file'])): ?>
                                                <span class="badge bg-success-subtle text-success border border-success"><i class="bi bi-check-circle me-1"></i>Dosya Yüklendi</span>
                                            <?php elseif ($item['design_source'] === 'graphic_support'): ?>
                                                <span class="badge bg-info-subtle text-info border border-info"><i class="bi bi-palette me-1"></i>Tasarım Desteği</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary">Tasarım Yok</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="fw-bold"><?= number_format($item['quantity']) ?></span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-danger"><?= format_price($item['total_price']) ?></span>
                                            <small class="text-muted d-block" style="font-size:10px;">+KDV</small>
                                        </td>
                                        <td>
                                            <a href="cart.php?remove=<?= urlencode($item['id']) ?>" class="btn btn-sm btn-outline-danger border-0" title="Sil" onclick="return confirm('Bu ürünü sepetten kaldırmak istediğinize emin misiniz?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                        <a href="category.php" class="btn btn-sm btn-apple-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Alışverişe Devam Et
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sağ: Sipariş Özeti & Ödeme Butonu -->
            <div class="col-lg-4">
                <div class="apple-card p-4">
                    <h5 class="fw-bold mb-3">Sipariş Özeti</h5>

                    <!-- Kargo Barı -->
                    <?php if ($totals['free_shipping_eligible']): ?>
                        <div class="alert alert-success py-2 px-3 small rounded-3 mb-3">
                            <i class="bi bi-check-circle-fill me-1"></i> Tebrikler! <strong>Ücretsiz Kargo</strong> kazandınız.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning py-2 px-3 small rounded-3 mb-3">
                            <i class="bi bi-truck me-1"></i> <strong><?= format_price(FREE_SHIPPING_LIMIT - $totals['subtotal']) ?></strong> daha ekleyin, kargo <strong>BEDAVA</strong> olsun!
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Ara Toplam:</span>
                        <span class="fw-bold"><?= format_price($totals['subtotal']) ?></span>
                    </div>

                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Kargo Ücreti:</span>
                        <span class="fw-bold"><?= $totals['shipping'] > 0 ? format_price($totals['shipping']) : '<span class="text-success">ÜCRETSİZ</span>' ?></span>
                    </div>

                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">KDV (%20):</span>
                        <span class="fw-bold"><?= format_price($totals['subtotal'] * 0.20) ?></span>
                    </div>

                    <div class="d-flex justify-content-between mt-3 pt-3 border-top">
                        <span class="fw-bold fs-6">Genel Toplam:</span>
                        <span class="fw-extrabold text-danger fs-4"><?= format_price($totals['grand_total'] * 1.20) ?></span>
                    </div>

                    <a href="checkout.php" class="btn btn-apple btn-apple-orange w-100 py-3 mt-4 fw-bold shadow">
                        Siparişi Tamamla <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
