<?php
require_once __DIR__ . '/config/config.php';

$cart = new Cart();

// POST istekleri (ÃœrÃ¼n Ekleme / Silme / Kupon)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 100);
        $options = $_POST['options'] ?? [];
        if (!is_array($options)) {
            $options = [];
        }
        $selectedPackage = $_POST['selected_package'] ?? 'standart';
        $options['selected_package'] = $selectedPackage;

        if (!empty($_POST['thickness'])) {
            $options['thickness'] = $_POST['thickness'];
        }
        if (!empty($_POST['mounting'])) {
            $options['mounting'] = $_POST['mounting'];
        }

        $customPaperId = (int)($_POST['custom_paper_id'] ?? 0);

        $sizeType = $_POST['size_type'] ?? 'standard';
        $selectedPackage = $_POST['selected_package'] ?? 'standart';
        $isCustomSize = ($sizeType === 'custom' || $selectedPackage === 'ozel' || !empty($_POST['is_custom_size']));

        if ($isCustomSize) {
            $customWidth = (float)(!empty($_POST['dekota_width_cm']) ? $_POST['dekota_width_cm'] : ($_POST['custom_width'] ?? 0));
            $customHeight = (float)(!empty($_POST['dekota_height_cm']) ? $_POST['dekota_height_cm'] : ($_POST['custom_height'] ?? 0));
        } else {
            $customWidth = 0;
            $customHeight = 0;
        }

        $customSize = ($isCustomSize && $customWidth > 0 && $customHeight > 0) ? [
            'width'     => $customWidth,
            'height'    => $customHeight,
            'is_custom' => true
        ] : null;

        $svgData = $_POST['design_svg'] ?? null;
        if (!empty($_POST['design_back_svg'])) {
            $svgData = json_encode([
                'front' => $_POST['design_svg'] ?? '',
                'back'  => $_POST['design_back_svg']
            ]);
        }

        $designData = [
            'type'    => $_POST['design_type'] ?? 'none',
            'file'    => $_POST['design_file'] ?? null,
            'svg'     => $svgData,
            'preview' => $_POST['design_preview'] ?? null,
            'notes'   => $_POST['design_notes'] ?? null
        ];

        $res = $cart->add($productId, $quantity, $options, $customSize, $designData, $selectedPackage, $customPaperId);
        if ($res['success']) {
            Helper::setFlash('success', 'ÃœrÃ¼n ve baskÄ± tercihiniz sepete eklendi.');
        } else {
            Helper::setFlash('danger', $res['error'] ?? 'Sepete eklenirken bir hata oluÅŸtu.');
        }
        header("Location: " . SITE_URL . "/cart.php");
        exit;
    }

    if ($action === 'upgrade_qty') {
        $itemId = (int)($_POST['item_id'] ?? 0);
        $newQty = (int)($_POST['new_quantity'] ?? 0);
        if ($itemId > 0 && $newQty > 0) {
            $upRes = $cart->updateQuantity($itemId, $newQty);
            if ($upRes) {
                Helper::setFlash('success', "SipariÅŸ adediniz baÅŸarÄ±yla {$newQty} Adet olarak gÃ¼ncellendi!");
            } else {
                Helper::setFlash('danger', 'Adet gÃ¼ncellenirken bir hata oluÅŸtu.');
            }
        }
        $redirect = (!empty($_POST['redirect_to']) && $_POST['redirect_to'] === 'checkout') ? (SITE_URL . '/checkout.php') : (SITE_URL . '/cart.php');
        header("Location: " . $redirect);
        exit;
    }

    if ($action === 'remove') {
        $itemId = (int)($_POST['item_id'] ?? 0);
        $cart->remove($itemId);
        Helper::setFlash('info', 'ÃœrÃ¼n sepetten kaldÄ±rÄ±ldÄ±.');
        header("Location: " . SITE_URL . "/cart.php");
        exit;
    }

    if ($action === 'apply_coupon') {
        $code = trim($_POST['coupon_code'] ?? '');
        $res = $cart->applyCoupon($code);
        if ($res['success']) {
            Helper::setFlash('success', 'Kupon kodu baÅŸarÄ±yla uygulandÄ±!');
        } else {
            Helper::setFlash('danger', $res['error'] ?? 'Kupon uygulanamadÄ±.');
        }
        header("Location: " . SITE_URL . "/cart.php");
        exit;
    }


    if ($action === 'remove_coupon') {
        $cart->removeCoupon();
        Helper::setFlash('info', 'Kupon kodu kaldÄ±rÄ±ldÄ±.');
        header("Location: " . SITE_URL . "/cart.php");
        exit;
    }
}

$summary = $cart->getSummary();
$pageTitle = 'AlÄ±ÅŸveriÅŸ Sepetim â€“ TamBaskÄ±';

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <h3 class="fw-bold mb-4"><i class="bi bi-bag-check-fill text-primary me-2"></i>AlÄ±ÅŸveriÅŸ Sepetim (<?= $summary['count'] ?> ÃœrÃ¼n)</h3>

    <?php if (empty($summary['items'])): ?>
        <div class="apple-card p-5 text-center my-4">
            <i class="bi bi-bag-x text-muted" style="font-size: 64px;"></i>
            <h4 class="fw-bold mt-3">Sepetinizde HenÃ¼z ÃœrÃ¼n Bulunmuyor</h4>
            <p class="text-muted small">Ä°htiyacÄ±nÄ±z olan matbaa Ã¼rÃ¼nlerini keÅŸfedip anÄ±nda sipariÅŸ oluÅŸturabilirsiniz.</p>
            <a href="<?= SITE_URL ?>/category.php" class="btn btn-apple btn-apple-pink mt-2">
                <i class="bi bi-grid-fill me-1"></i> ÃœrÃ¼nleri Ä°ncele
            </a>
        </div>
    <?php else: ?>

        <!-- Ãœcretsiz Kargo Ä°lerleme Ã‡ubuÄŸu -->
        <?php if ($summary['grand_total'] < $summary['free_shipping_limit']): 
            $remaining = $summary['free_shipping_limit'] - $summary['grand_total'];
            $percent = min(100, ($summary['grand_total'] / $summary['free_shipping_limit']) * 100);
        ?>
            <div class="p-3 bg-white rounded-4 border mb-4 shadow-sm">
                <div class="d-flex justify-content-between small fw-bold mb-2">
                    <span><i class="bi bi-truck text-primary me-1"></i> Ãœcretsiz Kargo Ä°Ã§in: <strong><?= Helper::formatPrice($remaining) ?></strong> daha ekleyin!</span>
                    <span><?= Helper::formatPrice($summary['free_shipping_limit']) ?></span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $percent ?>%"></div>
                </div>
            </div>
        <?php else: ?>
            <div class="p-3 bg-success-subtle text-success rounded-4 border border-success mb-4 d-flex align-items-center gap-2 fw-bold small">
                <i class="bi bi-check-circle-fill fs-5"></i> Tebrikler! Bu sipariÅŸinizde KARGO ÃœCRETSÄ°Z!
            </div>
        <?php endif; ?>

        <div class="row g-4">
            
            <!-- Sol: Sepet Kalemleri -->
            <div class="col-lg-8">
                <div class="apple-card p-4">
                    
                    <?php foreach ($summary['items'] as $item): ?>
                        <div class="row align-items-center py-3 border-bottom g-3">
                            
                            <!-- ÃœrÃ¼n GÃ¶rseli veya VektÃ¶rel Ã–nizleme -->
                            <div class="col-md-2 col-3 text-center">
                                <?php if (!empty($item['design_svg'])): ?>
                                    <?php 
                                    $svgData = $item['design_svg'];
                                    $isJson = (strpos(trim($svgData), '{') === 0);
                                    $decodedSvg = $isJson ? json_decode($svgData, true) : null;
                                    $displaySvg = $decodedSvg['front'] ?? $svgData;

                                    // Sadece kÃ¶k <svg> etiketini responsive yap, iÃ§ etiketlerin (rect, path vb.) width/height deÄŸerlerine dokunma
                                    $displaySvg = preg_replace_callback('/<svg\b([^>]*)>/i', function($m) {
                                        $attrs = $m[1];
                                        $attrs = preg_replace('/\b(width|height|preserveAspectRatio)=("[^"]*"|\'[^\']*\')/i', '', $attrs);
                                        if (!preg_match('/\bviewBox=/i', $attrs)) {
                                            $attrs .= ' viewBox="0 0 850 500"';
                                        }
                                        return '<svg ' . trim($attrs) . ' width="100%" height="100%" preserveAspectRatio="xMidYMid meet" style="width:100%;height:100%;display:block;border-radius:4px;">';
                                    }, $displaySvg, 1);
                                    ?>
                                    <div class="border rounded-3 p-1 shadow-sm d-flex align-items-center justify-content-center mx-auto cart-svg-thumb" style="width: 84px; height: 54px; background: #0f172a; overflow: hidden;">
                                        <?= $displaySvg ?>
                                    </div>
                                    <span class="badge bg-primary mt-1" style="font-size: 9px;"><?= !empty($decodedSvg['back']) ? 'Ã‡ift YÃ¶n VektÃ¶r' : 'Ã–zel VektÃ¶r' ?></span>
                                <?php elseif (!empty($item['featured_image'])): ?>
                                    <img src="<?= SITE_URL . '/' . htmlspecialchars($item['featured_image']) ?>" class="img-fluid rounded-3" style="max-height: 70px; object-fit: contain;">
                                <?php else: ?>
                                    <i class="bi bi-printer text-muted fs-1"></i>
                                <?php endif; ?>
                            </div>

                            <!-- ÃœrÃ¼n Bilgileri & SeÃ§ilen Varyantlar -->
                            <div class="col-md-6 col-9">
                                <h6 class="fw-bold mb-1">
                                    <a href="<?= SITE_URL ?>/product.php?slug=<?= $item['product_slug'] ?>" class="text-decoration-none text-dark">
                                        <?= htmlspecialchars($item['product_name']) ?>
                                    </a>
                                </h6>
                                <div class="text-muted small mb-1">
                                    <strong>Adet:</strong> <?= number_format($item['quantity'], 0, '', '.') ?> Adet
                                </div>
                                <?php if (!empty($item['options_labels'])): ?>
                                    <div class="small text-muted mb-1">
                                        <?= implode(' â€¢ ', array_map('htmlspecialchars', $item['options_labels'])) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- TasarÄ±m Durumu Rozeti -->
                                <div class="mt-1">
                                    <?php if ($item['design_type'] === 'uploaded'): ?>
                                        <span class="badge bg-info text-dark small"><i class="bi bi-file-earmark-arrow-up"></i> YÃ¼klenen Dosya: <?= htmlspecialchars(basename($item['design_file'])) ?></span>
                                    <?php elseif ($item['design_type'] === 'sign_studio'): ?>
                                        <span class="badge text-white small" style="background: linear-gradient(135deg, #d97706, #f59e0b);"><i class="bi bi-shield-shaded me-1"></i> Ä°SG VektÃ¶rel Levha TasarÄ±mÄ± (300 DPI)</span>
                                    <?php elseif ($item['design_type'] === 'canva_studio'): ?>
                                        <span class="badge text-white small" style="background: linear-gradient(135deg, #e11d48, #f43f5e);"><i class="bi bi-palette-fill me-1"></i> Canva VektÃ¶r TasarÄ±mÄ± (300 DPI)</span>
                                    <?php elseif ($item['design_type'] === 'ai_generated'): ?>
                                        <span class="badge text-white small" style="background: linear-gradient(135deg, #9333ea, #a855f7);"><i class="bi bi-stars me-1"></i> AI VektÃ¶r TasarÄ±mÄ±</span>
                                    <?php elseif ($item['design_type'] === 'online_editor'): ?>
                                        <span class="badge bg-success small"><i class="bi bi-vector-pen"></i> Online VektÃ¶rel TasarÄ±m KayÄ±tlÄ±</span>
                                    <?php elseif ($item['design_type'] === 'design_request'): ?>
                                        <span class="badge bg-warning text-dark small"><i class="bi bi-magic"></i> Grafik TasarÄ±m DesteÄŸi Talep Edildi</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Fiyat & Sil -->
                            <div class="col-md-4 col-12 text-md-end d-flex d-md-block justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold fs-5 text-primary"><?= $item['formatted_price'] ?></div>
                                    <small class="text-muted d-block">Birim: <?= Helper::formatPrice($item['unit_price']) ?></small>
                                </div>
                                <form action="<?= SITE_URL ?>/cart.php" method="POST" class="mt-2">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                    <button type="submit" class="btn btn-sm text-danger p-0 border-0" onclick="return confirm('Bu Ã¼rÃ¼nÃ¼ sepetten silmek istediÄŸinize emin misiniz?');">
                                        <i class="bi bi-trash"></i> KaldÄ±r
                                    </button>
                                </form>
                            </div>

                            <!-- ğŸ’¡ Sepet Ä°Ã§i AvantajlÄ± Adet YÃ¼kseltme Teklifi -->
                            <?php if (!empty($item['upsell']) && $item['upsell']['active']): ?>
                                <div class="col-12 mt-2 pt-2 border-top">
                                    <div class="p-3 rounded-4 border d-flex justify-content-between align-items-center flex-wrap gap-2 shadow-sm" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-color: #f59e0b !important;">
                                        <div class="d-flex align-items-center gap-2 flex-grow-1">
                                            <div class="bg-warning text-dark p-2 rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; flex-shrink: 0;">
                                                <i class="bi bi-stars fs-6 text-danger"></i>
                                            </div>
                                            <div class="small text-dark">
                                                <div class="fw-bold text-dark"><i class="bi bi-fire text-danger me-1"></i> AvantajlÄ± Ãœretim FÄ±rsatÄ±: +<?= $item['upsell']['added_quantity'] ?> Adet Daha Ekleyin!</div>
                                                <span>Sadece <strong><?= $item['upsell']['formatted_diff'] ?></strong> farkla bu Ã¼rÃ¼nÃ¼ <strong><?= number_format($item['quantity'], 0, '', '.') ?> yerine <?= number_format($item['upsell']['target_quantity'], 0, '', '.') ?> Adet</strong> olarak Ã¼retime verebilirsiniz. Birim fiyatÄ±nÄ±z <strong class="text-success"><?= $item['upsell']['formatted_target_unit_price'] ?></strong>'ye dÃ¼ÅŸer (%<?= $item['upsell']['unit_discount_pct'] ?> daha indirimli).</span>
                                            </div>
                                        </div>
                                        <form action="<?= SITE_URL ?>/cart.php" method="POST" class="m-0">
                                            <input type="hidden" name="action" value="upgrade_qty">
                                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                            <input type="hidden" name="new_quantity" value="<?= $item['upsell']['target_quantity'] ?>">
                                            <button type="submit" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3 py-2 shadow-sm text-nowrap">
                                                <i class="bi bi-arrow-up-circle-fill text-danger me-1"></i> <?= number_format($item['upsell']['target_quantity'], 0, '', '.') ?> Adete YÃ¼kselt (<?= $item['upsell']['formatted_diff'] ?>)
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>
                    <?php endforeach; ?>

                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2">
                        <a href="<?= SITE_URL ?>/category.php" class="btn btn-sm btn-apple-secondary">
                            <i class="bi bi-arrow-left me-1"></i> AlÄ±ÅŸveriÅŸe Devam Et
                        </a>
                    </div>

                </div>
            </div>

            <!-- SaÄŸ: Kupon & SipariÅŸ Ã–zeti -->
            <div class="col-lg-4">
                
                <!-- Kupon Kodu KartÄ± -->
                <div class="apple-card p-3 mb-3">
                    <h6 class="fw-bold mb-2 small"><i class="bi bi-ticket-perforated me-1 text-primary"></i> Ä°ndirim Kuponu</h6>
                    <?php if ($summary['coupon']): ?>
                        <div class="d-flex justify-content-between align-items-center p-2 bg-success-subtle text-success rounded-3 small">
                            <span>Kupon: <strong><?= htmlspecialchars($summary['coupon']['code']) ?></strong> uygulandÄ±</span>
                            <form action="<?= SITE_URL ?>/cart.php" method="POST" class="d-inline">
                                <input type="hidden" name="action" value="remove_coupon">
                                <button type="submit" class="btn-close btn-close-sm"></button>
                            </form>
                        </div>
                    <?php else: ?>
                        <form action="<?= SITE_URL ?>/cart.php" method="POST" class="d-flex gap-2">
                            <input type="hidden" name="action" value="apply_coupon">
                            <input type="text" name="coupon_code" class="form-control form-control-sm" placeholder="Kupon Kodunuz" required>
                            <button type="submit" class="btn btn-sm btn-dark">Uygula</button>
                        </form>
                    <?php endif; ?>
                </div>

                <!-- SipariÅŸ Ã–zeti KartÄ± -->
                <div class="apple-card p-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">SipariÅŸ Ã–zeti</h5>

                    <div class="d-flex justify-content-between small text-muted mb-2">
                        <span>Ara Toplam (KDV HariÃ§):</span>
                        <span class="text-dark fw-bold"><?= $summary['formatted_subtotal'] ?></span>
                    </div>

                    <div class="d-flex justify-content-between small text-muted mb-2">
                        <span>KDV ToplamÄ±:</span>
                        <span class="text-dark fw-bold"><?= $summary['formatted_tax'] ?></span>
                    </div>

                    <?php if ($summary['discount_amount'] > 0): ?>
                        <div class="d-flex justify-content-between small text-success fw-bold mb-2">
                            <span>Kupon / Ä°skonto Ä°ndirimi:</span>
                            <span>-<?= $summary['formatted_discount'] ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between small text-muted mb-3">
                        <span>Kargo Ãœcreti:</span>
                        <span class="text-dark fw-bold"><?= $summary['formatted_shipping'] ?></span>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex justify-content-between align-items-baseline mb-4">
                        <span class="fw-bold fs-6">Ã–denecek Toplam:</span>
                        <span class="price-display-lg text-primary"><?= $summary['formatted_grand_total'] ?></span>
                    </div>

                    <a href="<?= SITE_URL ?>/checkout.php" class="btn-apple btn-apple-pink w-100 py-3 fs-6 fw-bold shadow text-center text-white">
                        SipariÅŸi Tamamla <i class="bi bi-arrow-right ms-2"></i>
                    </a>

                    <div class="mt-3 text-center text-muted" style="font-size: 11px;">
                        <i class="bi bi-shield-lock-fill text-success me-1"></i> 256-Bit GÃ¼venli Ã–deme & SSL KorumasÄ±
                    </div>
                </div>

            </div>

        </div>


    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
