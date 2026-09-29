<?php
require_once __DIR__ . '/config/config.php';

$cart = new Cart();

// POST istekleri (Ürün Ekleme / Silme / Kupon)
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
        $isCustomSize = ($sizeType === 'custom' || $selectedPackage === 'ozel' || !empty($_POST['is_custom_size']));

        $customWidth = (float)($_POST['custom_width'] ?? $_POST['dekota_width_cm'] ?? 0);
        $customHeight = (float)($_POST['custom_height'] ?? $_POST['dekota_height_cm'] ?? 0);

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
            Helper::setFlash('success', 'Ürün ve baskı tercihiniz sepete eklendi.');
        } else {
            Helper::setFlash('danger', $res['error'] ?? 'Sepete eklenirken bir hata oluştu.');
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
                Helper::setFlash('success', "Sipariş adediniz başarıyla {$newQty} Adet olarak güncellendi!");
            } else {
                Helper::setFlash('danger', 'Adet güncellenirken bir hata oluştu.');
            }
        }
        $redirect = (!empty($_POST['redirect_to']) && $_POST['redirect_to'] === 'checkout') ? (SITE_URL . '/checkout.php') : (SITE_URL . '/cart.php');
        header("Location: " . $redirect);
        exit;
    }

    if ($action === 'remove') {
        $itemId = (int)($_POST['item_id'] ?? 0);
        $cart->remove($itemId);
        Helper::setFlash('info', 'Ürün sepetten kaldırıldı.');
        header("Location: " . SITE_URL . "/cart.php");
        exit;
    }

    if ($action === 'apply_coupon') {
        $code = trim($_POST['coupon_code'] ?? '');
        $res = $cart->applyCoupon($code);
        if ($res['success']) {
            Helper::setFlash('success', 'Kupon kodu başarıyla uygulandı!');
        } else {
            Helper::setFlash('danger', $res['error'] ?? 'Kupon uygulanamadı.');
        }
        header("Location: " . SITE_URL . "/cart.php");
        exit;
    }

    if ($action === 'add_cross_sell') {
        $slug = trim($_POST['product_slug'] ?? '');
        $qty = (int)($_POST['quantity'] ?? 1);
        $discountPrice = (float)($_POST['discount_price'] ?? 0);
        $prodName = trim($_POST['product_name'] ?? 'Tamamlayıcı Ürün');
        $designSvg = $_POST['design_svg'] ?? null;

        $db = Database::getInstance()->getConnection();
        $pStmt = $db->prepare("SELECT id, name FROM products WHERE slug = ?");
        $pStmt->execute([$slug]);
        $pRow = $pStmt->fetch();
        $pId = $pRow['id'] ?? 1;

        $userId = Auth::id();
        $stmt = $db->prepare("INSERT INTO cart_items (
            session_id, user_id, product_id, quantity, selected_options, custom_size,
            design_type, design_svg, unit_price, total_price
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $unitPrice = $qty > 0 ? ($discountPrice / $qty) : $discountPrice;
        $stmt->execute([
            session_id(),
            $userId,
            $pId,
            $qty,
            json_encode(['Fırsat' => '%25 Sepete Özel İndirim']),
            null,
            'canva_studio',
            $designSvg,
            $unitPrice,
            $discountPrice
        ]);

        Helper::setFlash('success', "🎉 {$prodName} tasarımınızla giydirilmiş ve indirimli olarak sepetinize eklendi!");
        header("Location: " . SITE_URL . "/cart.php");
        exit;
    }

    if ($action === 'remove_coupon') {
        $cart->removeCoupon();
        Helper::setFlash('info', 'Kupon kodu kaldırıldı.');
        header("Location: " . SITE_URL . "/cart.php");
        exit;
    }
}

$summary = $cart->getSummary();
$pageTitle = 'Alışveriş Sepetim – TamBaskı';

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <h3 class="fw-bold mb-4"><i class="bi bi-bag-check-fill text-primary me-2"></i>Alışveriş Sepetim (<?= $summary['count'] ?> Ürün)</h3>

    <?php if (empty($summary['items'])): ?>
        <div class="apple-card p-5 text-center my-4">
            <i class="bi bi-bag-x text-muted" style="font-size: 64px;"></i>
            <h4 class="fw-bold mt-3">Sepetinizde Henüz Ürün Bulunmuyor</h4>
            <p class="text-muted small">İhtiyacınız olan matbaa ürünlerini keşfedip anında sipariş oluşturabilirsiniz.</p>
            <a href="<?= SITE_URL ?>/category.php" class="btn btn-apple btn-apple-pink mt-2">
                <i class="bi bi-grid-fill me-1"></i> Ürünleri İncele
            </a>
        </div>
    <?php else: ?>

        <!-- Ücretsiz Kargo İlerleme Çubuğu -->
        <?php if ($summary['grand_total'] < $summary['free_shipping_limit']): 
            $remaining = $summary['free_shipping_limit'] - $summary['grand_total'];
            $percent = min(100, ($summary['grand_total'] / $summary['free_shipping_limit']) * 100);
        ?>
            <div class="p-3 bg-white rounded-4 border mb-4 shadow-sm">
                <div class="d-flex justify-content-between small fw-bold mb-2">
                    <span><i class="bi bi-truck text-primary me-1"></i> Ücretsiz Kargo İçin: <strong><?= Helper::formatPrice($remaining) ?></strong> daha ekleyin!</span>
                    <span><?= Helper::formatPrice($summary['free_shipping_limit']) ?></span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $percent ?>%"></div>
                </div>
            </div>
        <?php else: ?>
            <div class="p-3 bg-success-subtle text-success rounded-4 border border-success mb-4 d-flex align-items-center gap-2 fw-bold small">
                <i class="bi bi-check-circle-fill fs-5"></i> Tebrikler! Bu siparişinizde KARGO ÜCRETSİZ!
            </div>
        <?php endif; ?>

        <div class="row g-4">
            
            <!-- Sol: Sepet Kalemleri -->
            <div class="col-lg-8">
                <div class="apple-card p-4">
                    
                    <?php foreach ($summary['items'] as $item): ?>
                        <div class="row align-items-center py-3 border-bottom g-3">
                            
                            <!-- Ürün Görseli veya Vektörel Önizleme -->
                            <div class="col-md-2 col-3 text-center">
                                <?php if (!empty($item['design_svg'])): ?>
                                    <?php 
                                    $svgData = $item['design_svg'];
                                    $isJson = (strpos(trim($svgData), '{') === 0);
                                    $decodedSvg = $isJson ? json_decode($svgData, true) : null;
                                    $displaySvg = $decodedSvg['front'] ?? $svgData;

                                    // Sadece kök <svg> etiketini responsive yap, iç etiketlerin (rect, path vb.) width/height değerlerine dokunma
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
                                    <span class="badge bg-primary mt-1" style="font-size: 9px;"><?= !empty($decodedSvg['back']) ? 'Çift Yön Vektör' : 'Özel Vektör' ?></span>
                                <?php elseif (!empty($item['featured_image'])): ?>
                                    <img src="<?= SITE_URL . '/' . htmlspecialchars($item['featured_image']) ?>" class="img-fluid rounded-3" style="max-height: 70px; object-fit: contain;">
                                <?php else: ?>
                                    <i class="bi bi-printer text-muted fs-1"></i>
                                <?php endif; ?>
                            </div>

                            <!-- Ürün Bilgileri & Seçilen Varyantlar -->
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
                                        <?= implode(' • ', array_map('htmlspecialchars', $item['options_labels'])) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Tasarım Durumu Rozeti -->
                                <div class="mt-1">
                                    <?php if ($item['design_type'] === 'uploaded'): ?>
                                        <span class="badge bg-info text-dark small"><i class="bi bi-file-earmark-arrow-up"></i> Yüklenen Dosya: <?= htmlspecialchars(basename($item['design_file'])) ?></span>
                                    <?php elseif ($item['design_type'] === 'sign_studio'): ?>
                                        <span class="badge text-white small" style="background: linear-gradient(135deg, #d97706, #f59e0b);"><i class="bi bi-shield-shaded me-1"></i> İSG Vektörel Levha Tasarımı (300 DPI)</span>
                                    <?php elseif ($item['design_type'] === 'canva_studio'): ?>
                                        <span class="badge text-white small" style="background: linear-gradient(135deg, #e11d48, #f43f5e);"><i class="bi bi-palette-fill me-1"></i> Canva Vektör Tasarımı (300 DPI)</span>
                                    <?php elseif ($item['design_type'] === 'ai_generated'): ?>
                                        <span class="badge text-white small" style="background: linear-gradient(135deg, #9333ea, #a855f7);"><i class="bi bi-stars me-1"></i> AI Vektör Tasarımı</span>
                                    <?php elseif ($item['design_type'] === 'online_editor'): ?>
                                        <span class="badge bg-success small"><i class="bi bi-vector-pen"></i> Online Vektörel Tasarım Kayıtlı</span>
                                    <?php elseif ($item['design_type'] === 'design_request'): ?>
                                        <span class="badge bg-warning text-dark small"><i class="bi bi-magic"></i> Grafik Tasarım Desteği Talep Edildi</span>
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
                                    <button type="submit" class="btn btn-sm text-danger p-0 border-0" onclick="return confirm('Bu ürünü sepetten silmek istediğinize emin misiniz?');">
                                        <i class="bi bi-trash"></i> Kaldır
                                    </button>
                                </form>
                            </div>

                            <!-- 💡 Sepet İçi Avantajlı Adet Yükseltme Teklifi -->
                            <?php if (!empty($item['upsell']) && $item['upsell']['active']): ?>
                                <div class="col-12 mt-2 pt-2 border-top">
                                    <div class="p-3 rounded-4 border d-flex justify-content-between align-items-center flex-wrap gap-2 shadow-sm" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-color: #f59e0b !important;">
                                        <div class="d-flex align-items-center gap-2 flex-grow-1">
                                            <div class="bg-warning text-dark p-2 rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; flex-shrink: 0;">
                                                <i class="bi bi-stars fs-6 text-danger"></i>
                                            </div>
                                            <div class="small text-dark">
                                                <div class="fw-bold text-dark"><i class="bi bi-fire text-danger me-1"></i> Avantajlı Üretim Fırsatı: +<?= $item['upsell']['added_quantity'] ?> Adet Daha Ekleyin!</div>
                                                <span>Sadece <strong><?= $item['upsell']['formatted_diff'] ?></strong> farkla bu ürünü <strong><?= number_format($item['quantity'], 0, '', '.') ?> yerine <?= number_format($item['upsell']['target_quantity'], 0, '', '.') ?> Adet</strong> olarak üretime verebilirsiniz. Birim fiyatınız <strong class="text-success"><?= $item['upsell']['formatted_target_unit_price'] ?></strong>'ye düşer (%<?= $item['upsell']['unit_discount_pct'] ?> daha indirimli).</span>
                                            </div>
                                        </div>
                                        <form action="<?= SITE_URL ?>/cart.php" method="POST" class="m-0">
                                            <input type="hidden" name="action" value="upgrade_qty">
                                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                            <input type="hidden" name="new_quantity" value="<?= $item['upsell']['target_quantity'] ?>">
                                            <button type="submit" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3 py-2 shadow-sm text-nowrap">
                                                <i class="bi bi-arrow-up-circle-fill text-danger me-1"></i> <?= number_format($item['upsell']['target_quantity'], 0, '', '.') ?> Adete Yükselt (<?= $item['upsell']['formatted_diff'] ?>)
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>
                    <?php endforeach; ?>

                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2">
                        <a href="<?= SITE_URL ?>/category.php" class="btn btn-sm btn-apple-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Alışverişe Devam Et
                        </a>
                    </div>

                </div>
            </div>

            <!-- Sağ: Kupon & Sipariş Özeti -->
            <div class="col-lg-4">
                
                <!-- Kupon Kodu Kartı -->
                <div class="apple-card p-3 mb-3">
                    <h6 class="fw-bold mb-2 small"><i class="bi bi-ticket-perforated me-1 text-primary"></i> İndirim Kuponu</h6>
                    <?php if ($summary['coupon']): ?>
                        <div class="d-flex justify-content-between align-items-center p-2 bg-success-subtle text-success rounded-3 small">
                            <span>Kupon: <strong><?= htmlspecialchars($summary['coupon']['code']) ?></strong> uygulandı</span>
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

                <!-- Sipariş Özeti Kartı -->
                <div class="apple-card p-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Sipariş Özeti</h5>

                    <div class="d-flex justify-content-between small text-muted mb-2">
                        <span>Ara Toplam (KDV Hariç):</span>
                        <span class="text-dark fw-bold"><?= $summary['formatted_subtotal'] ?></span>
                    </div>

                    <div class="d-flex justify-content-between small text-muted mb-2">
                        <span>KDV Toplamı:</span>
                        <span class="text-dark fw-bold"><?= $summary['formatted_tax'] ?></span>
                    </div>

                    <?php if ($summary['discount_amount'] > 0): ?>
                        <div class="d-flex justify-content-between small text-success fw-bold mb-2">
                            <span>Kupon / İskonto İndirimi:</span>
                            <span>-<?= $summary['formatted_discount'] ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between small text-muted mb-3">
                        <span>Kargo Ücreti:</span>
                        <span class="text-dark fw-bold"><?= $summary['formatted_shipping'] ?></span>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex justify-content-between align-items-baseline mb-4">
                        <span class="fw-bold fs-6">Ödenecek Toplam:</span>
                        <span class="price-display-lg text-primary"><?= $summary['formatted_grand_total'] ?></span>
                    </div>

                    <a href="<?= SITE_URL ?>/checkout.php" class="btn-apple btn-apple-pink w-100 py-3 fs-6 fw-bold shadow text-center text-white">
                        Siparişi Tamamla <i class="bi bi-arrow-right ms-2"></i>
                    </a>

                    <div class="mt-3 text-center text-muted" style="font-size: 11px;">
                        <i class="bi bi-shield-lock-fill text-success me-1"></i> 256-Bit Güvenli Ödeme & SSL Koruması
                    </div>
                </div>

            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- 🎨 AKILLI TASARIM GİYDİRME & SEPETE ÖZEL İNDİRİMLİ TAMAMLAYICI ÜRÜNLER -->
        <!-- ========================================================================= -->
        <?php
        $primaryDesignSvg = '';
        $hasCustomDesign = false;
        foreach ($summary['items'] as $it) {
            if (!empty($it['design_svg'])) {
                $rawSvg = $it['design_svg'];
                if (strpos(trim($rawSvg), '{') === 0) {
                    $dec = json_decode($rawSvg, true);
                    $primaryDesignSvg = $dec['front'] ?? $rawSvg;
                } else {
                    $primaryDesignSvg = $rawSvg;
                }
                if (!empty($primaryDesignSvg)) {
                    $primaryDesignSvg = preg_replace_callback('/<svg\b([^>]*)>/i', function($m) {
                        $attrs = $m[1];
                        $attrs = preg_replace('/\b(width|height|preserveAspectRatio)=("[^"]*"|\'[^\']*\')/i', '', $attrs);
                        if (!preg_match('/\bviewBox=/i', $attrs)) {
                            $attrs .= ' viewBox="0 0 850 500"';
                        }
                        return '<svg ' . trim($attrs) . ' width="100%" height="100%" preserveAspectRatio="xMidYMid meet" style="width:100%;height:100%;display:block;">';
                    }, $primaryDesignSvg, 1);
                    $hasCustomDesign = true;
                    break;
                }
            }
        }

        $crossSellProducts = [
            [
                'key'            => 'flag',
                'title'          => 'Yelken Bayrak (Plaj Bayrağı)',
                'badge'          => 'Dış Mekan & Fuar',
                'desc'           => '75x300 cm Raşel Kumaş • Direk & 20L Su Dubası Dahil',
                'slug'           => 'yelken-bayrak',
                'quantity'       => 1,
                'qty_label'      => '1 Takım (Tam Set)',
                'regular_price'  => 950.00,
                'discount_price' => 690.00,
                'discount_pct'   => 27,
                'mockup_type'    => 'flag',
                'industries'     => ['all', 'emlak', 'gida', 'mimarlik', 'guzellik']
            ],
            [
                'key'            => 'folder',
                'title'          => 'Cepli Sunum Dosyası (Klasör)',
                'badge'          => 'Kurumsal Prestij',
                'desc'           => '350gr Mat Kuşe • Mat Selefon • Kartvizit Takma Yuvalı',
                'slug'           => 'cepli-dosya',
                'quantity'       => 250,
                'qty_label'      => '250 Adet',
                'regular_price'  => 1450.00,
                'discount_price' => 1050.00,
                'discount_pct'   => 28,
                'mockup_type'    => 'folder',
                'industries'     => ['all', 'hukuk', 'saglik', 'mimarlik', 'teknoloji', 'emlak']
            ],
            [
                'key'            => 'letterhead',
                'title'          => 'A4 Antetli Kağıt',
                'badge'          => 'Ofis & Yazışma',
                'desc'           => '21x29.7 cm A4 • 1. Hamur 80gr • Lazer Yazıcıya %100 Uyumlu',
                'slug'           => 'antetli-kagit',
                'quantity'       => 1000,
                'qty_label'      => '1.000 Adet',
                'regular_price'  => 850.00,
                'discount_price' => 620.00,
                'discount_pct'   => 27,
                'mockup_type'    => 'letterhead',
                'industries'     => ['all', 'hukuk', 'saglik', 'teknoloji', 'mimarlik', 'emlak']
            ],
            [
                'key'            => 'stamp',
                'title'          => 'Otomatik Sırdaş Kaşe',
                'badge'          => 'Hızlı Onay',
                'desc'           => 'Dayanıklı Metalik Mekanizma • Siyah Mürekkepli Hazır',
                'slug'           => 'kase',
                'quantity'       => 1,
                'qty_label'      => '1 Adet',
                'regular_price'  => 320.00,
                'discount_price' => 240.00,
                'discount_pct'   => 25,
                'mockup_type'    => 'stamp',
                'industries'     => ['all', 'hukuk', 'saglik', 'mimarlik', 'teknoloji']
            ],
            [
                'key'            => 'rollup',
                'title'          => 'Roll-Up Banner Mekanizması',
                'badge'          => 'Etkinlik & Tanıtım',
                'desc'           => '85x200 cm • Alüminyum Gövde • Özel Fermuarlı Taşıma Çantalı',
                'slug'           => 'roll-up',
                'quantity'       => 1,
                'qty_label'      => '1 Adet',
                'regular_price'  => 1200.00,
                'discount_price' => 890.00,
                'discount_pct'   => 26,
                'mockup_type'    => 'rollup',
                'industries'     => ['all', 'emlak', 'teknoloji', 'guzellik', 'mimarlik', 'gida']
            ]
        ];
        ?>

        <style>
        .cart-svg-thumb svg {
            width: 100% !important;
            height: 100% !important;
            display: block !important;
            object-fit: contain !important;
        }
        .cross-sell-hero-card {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            position: relative;
        }
        .cross-mockup-frame {
            height: 180px;
            background: #f8fafc;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .cross-mockup-frame:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        .flag-pole {
            width: 5px;
            height: 155px;
            background: linear-gradient(90deg, #64748b, #94a3b8, #475569);
            position: absolute;
            left: 28px;
            bottom: 12px;
            border-radius: 3px;
        }
        .flag-base {
            width: 32px;
            height: 10px;
            background: #1e293b;
            position: absolute;
            left: 14px;
            bottom: 6px;
            border-radius: 4px;
        }
        .flag-cloth {
            width: 78px;
            height: 140px;
            position: absolute;
            left: 33px;
            bottom: 22px;
            border-top-right-radius: 40px 60px;
            border-bottom-right-radius: 16px;
            overflow: hidden;
            box-shadow: 3px 4px 10px rgba(0,0,0,0.18);
            transform-origin: left center;
        }
        .folder-mockup {
            width: 130px;
            height: 155px;
            background: #0f172a;
            border-radius: 6px 12px 12px 6px;
            box-shadow: -4px 6px 16px rgba(0,0,0,0.2);
            position: relative;
            overflow: hidden;
            border-left: 5px solid #0071e3;
        }
        .letterhead-mockup {
            width: 110px;
            height: 155px;
            background: #ffffff;
            border-radius: 4px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.12);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            padding: 8px;
            display: flex;
            flex-direction: column;
        }
        .stamp-mockup {
            width: 100px;
            height: 145px;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .stamp-handle {
            width: 54px;
            height: 38px;
            background: linear-gradient(135deg, #e11d48, #be123c);
            border-radius: 8px 8px 4px 4px;
            box-shadow: 0 3px 8px rgba(225,29,72,0.3);
        }
        .stamp-body {
            width: 80px;
            height: 55px;
            background: #1e293b;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #334155;
            box-shadow: 0 6px 14px rgba(0,0,0,0.25);
            margin-top: 3px;
        }
        .stamp-imprint {
            width: 88px;
            height: 36px;
            border: 2px dashed #0071e3;
            border-radius: 6px;
            background: #ffffff;
            margin-top: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        }
        .rollup-mockup {
            width: 85px;
            height: 155px;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .rollup-base {
            width: 85px;
            height: 14px;
            background: linear-gradient(90deg, #94a3b8, #cbd5e1, #64748b);
            border-radius: 3px;
            margin-top: auto;
            box-shadow: 0 3px 6px rgba(0,0,0,0.2);
        }
        .rollup-screen {
            width: 76px;
            height: 135px;
            background: #ffffff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            overflow: hidden;
            border: 1px solid #cbd5e1;
        }
        </style>

        <div class="mt-4 mb-4">
            
            <!-- Hero Başlık Şeridi -->
            <div class="cross-sell-hero-card p-4 p-md-4 mb-3 shadow">
                <div class="row align-items-center g-3">
                    <div class="col-lg-8">
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-warning bg-opacity-25 border border-warning border-opacity-50 text-warning small fw-bold mb-2">
                            <i class="bi bi-stars"></i> OTOMATİK KURUMSAL GİYDİRME
                        </div>
                        <h4 class="fw-bold mb-1 text-white" id="crossSellHeroTitle">🎉 Tasarımınıza Özel Tamamlayıcı Ürün Fırsatları</h4>
                        <p class="text-white-50 small mb-0">
                            <?= $hasCustomDesign ? 'Az önce hazırladığınız vektörel tasarımınız aşağıdaki ürünlere <strong>otomatik olarak giydirildi</strong>. Tek tıkla %25 indirimle sepetinize ekleyebilirsiniz.' : 'Sepetinizdeki ürünlerle mükemmel uyumlu kurumsal matbaa setinizi <strong>%25 indirimle</strong> tamamlayın.' ?>
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <span class="badge bg-danger fs-6 px-3 py-2 rounded-pill shadow-sm">
                            <i class="bi bi-tag-fill me-1"></i> %25 - %28 Sepet İndirimi
                        </span>
                    </div>
                </div>
            </div>

            <!-- Sektör & Meslek Hızlı Filtre Kapsülleri (Pills) -->
            <div class="d-flex align-items-center gap-2 mb-3 overflow-auto py-1" style="white-space: nowrap;">
                <span class="small fw-bold text-muted me-1"><i class="bi bi-funnel-fill text-primary"></i> Sektörünüz:</span>
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 industry-filter-pill active" onclick="filterCartCrossSell('all', 'Genel Kurumsal', this)">🏢 Tümü / Kurumsal</button>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 industry-filter-pill" onclick="filterCartCrossSell('hukuk', 'Hukuk & Avukatlık', this)">⚖️ Hukuk &amp; Avukat</button>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 industry-filter-pill" onclick="filterCartCrossSell('saglik', 'Sağlık, Doktor & Klinik', this)">🩺 Sağlık &amp; Tıp</button>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 industry-filter-pill" onclick="filterCartCrossSell('emlak', 'Gayrimenkul & Emlak', this)">🏡 Gayrimenkul &amp; Emlak</button>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 industry-filter-pill" onclick="filterCartCrossSell('gida', 'Kafe, Restoran & Gıda', this)">☕ Kafe &amp; Restoran</button>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 industry-filter-pill" onclick="filterCartCrossSell('mimarlik', 'Mimarlık & İnşaat', this)">📐 Mimarlık &amp; İnşaat</button>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 industry-filter-pill" onclick="filterCartCrossSell('guzellik', 'Güzellik & Kuaför', this)">✨ Güzellik &amp; Kuaför</button>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 industry-filter-pill" onclick="filterCartCrossSell('teknoloji', 'Teknoloji & Yazılım', this)">💻 Teknoloji &amp; Yazılım</button>
            </div>

            <!-- Ürün Giydirme Kartları Izgarası (Grid) -->
            <div class="row g-3" id="cartCrossSellGrid">
                <?php foreach ($crossSellProducts as $p): ?>
                    <div class="col-lg-2 col-md-4 col-6 cross-sell-col" data-industries="<?= implode(',', $p['industries']) ?>" style="flex: 1 0 200px; transition: all 0.3s ease;">
                        <div class="apple-card p-3 h-100 d-flex flex-column border shadow-2xs position-relative bg-white" style="border-radius: 16px;">
                            
                            <!-- İndirim Rozeti -->
                            <span class="position-absolute top-0 end-0 translate-middle-y me-3 badge bg-danger rounded-pill shadow-xs" style="font-size: 10px; z-index: 5;">
                                -%<?= $p['discount_pct'] ?> İndirim
                            </span>

                            <!-- Giydirilmiş Mockup Alanı -->
                            <div class="cross-mockup-frame mb-2" onclick="openDressMockupModal('<?= $p['key'] ?>', '<?= htmlspecialchars($p['title'], ENT_QUOTES) ?>', '<?= Helper::formatPrice($p['discount_price']) ?>', '<?= Helper::formatPrice($p['regular_price']) ?>', '<?= $p['slug'] ?>', <?= $p['quantity'] ?>, '<?= $p['qty_label'] ?>', '<?= $p['desc'] ?>')">
                                
                                <?php if ($p['mockup_type'] === 'flag'): ?>
                                    <!-- Yelken Bayrak Mockup -->
                                    <div class="flag-pole"></div>
                                    <div class="flag-base"></div>
                                    <div class="flag-cloth bg-white">
                                        <?php if (!empty($primaryDesignSvg)): ?>
                                            <div style="width: 100%; height: 100%; transform: scale(0.35) rotate(-90deg); transform-origin: center center; display: flex; align-items: center; justify-content: center;">
                                                <?= $primaryDesignSvg ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-center p-1" style="background: linear-gradient(135deg, #0284c7, #0369a1); color: #fff;">
                                                <i class="bi bi-flag-fill fs-3 mb-1"></i>
                                                <div style="font-size: 8px; font-weight: 800;">BAYRAK</div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <span class="position-absolute bottom-0 start-0 m-1 badge bg-dark bg-opacity-75 text-white" style="font-size: 8.5px;"><i class="bi bi-zoom-in me-1"></i>İncele</span>

                                <?php elseif ($p['mockup_type'] === 'folder'): ?>
                                    <!-- Cepli Dosya Mockup -->
                                    <div class="folder-mockup">
                                        <?php if (!empty($primaryDesignSvg)): ?>
                                            <div style="width: 100%; height: 100%; transform: scale(0.42); transform-origin: top left; padding: 4px;">
                                                <?= $primaryDesignSvg ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-center p-2 text-white">
                                                <i class="bi bi-folder-fill fs-2 mb-1 text-primary"></i>
                                                <div style="font-size: 9px; font-weight: 800;">SUNUM DOSYASI</div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <span class="position-absolute bottom-0 start-0 m-1 badge bg-dark bg-opacity-75 text-white" style="font-size: 8.5px;"><i class="bi bi-zoom-in me-1"></i>İncele</span>

                                <?php elseif ($p['mockup_type'] === 'letterhead'): ?>
                                    <!-- Antetli Kağıt Mockup -->
                                    <div class="letterhead-mockup">
                                        <?php if (!empty($primaryDesignSvg)): ?>
                                            <div style="height: 38px; overflow: hidden; transform: scale(0.35); transform-origin: top left; width: 280%;">
                                                <?= $primaryDesignSvg ?>
                                            </div>
                                            <div class="mt-2" style="border-top: 1px dashed #cbd5e1; height: 50px;"></div>
                                        <?php else: ?>
                                            <div class="d-flex align-items-center gap-1 border-bottom pb-1">
                                                <i class="bi bi-building text-primary" style="font-size: 10px;"></i>
                                                <div style="font-size: 8px; font-weight: 700;">ANTETLİ LOGO</div>
                                            </div>
                                            <div class="mt-2" style="border-top: 1px dashed #cbd5e1; height: 50px;"></div>
                                        <?php endif; ?>
                                    </div>
                                    <span class="position-absolute bottom-0 start-0 m-1 badge bg-dark bg-opacity-75 text-white" style="font-size: 8.5px;"><i class="bi bi-zoom-in me-1"></i>İncele</span>

                                <?php elseif ($p['mockup_type'] === 'stamp'): ?>
                                    <!-- Sırdaş Kaşe Mockup -->
                                    <div class="stamp-mockup">
                                        <div class="stamp-handle"></div>
                                        <div class="stamp-body">
                                            <i class="bi bi-patch-check-fill text-white fs-5"></i>
                                        </div>
                                        <div class="stamp-imprint">
                                            <span class="text-primary fw-bold" style="font-size: 8.5px;"><i class="bi bi-check2-circle me-1"></i>ONAYLANDI</span>
                                        </div>
                                    </div>
                                    <span class="position-absolute bottom-0 start-0 m-1 badge bg-dark bg-opacity-75 text-white" style="font-size: 8.5px;"><i class="bi bi-zoom-in me-1"></i>İncele</span>

                                <?php elseif ($p['mockup_type'] === 'rollup'): ?>
                                    <!-- Roll-Up Banner Mockup -->
                                    <div class="rollup-mockup">
                                        <div class="rollup-screen">
                                            <?php if (!empty($primaryDesignSvg)): ?>
                                                <div style="width: 100%; height: 100%; transform: scale(0.32); transform-origin: top left;">
                                                    <?= $primaryDesignSvg ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-center p-1" style="background: linear-gradient(180deg, #0f172a, #334155); color: #fff;">
                                                    <i class="bi bi-aspect-ratio fs-4 mb-1 text-info"></i>
                                                    <div style="font-size: 8px; font-weight: 800;">ROLL-UP</div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="rollup-base"></div>
                                    </div>
                                    <span class="position-absolute bottom-0 start-0 m-1 badge bg-dark bg-opacity-75 text-white" style="font-size: 8.5px;"><i class="bi bi-zoom-in me-1"></i>İncele</span>

                                <?php endif; ?>

                            </div>

                            <!-- Bilgiler -->
                            <div class="fw-bold text-dark small text-truncate" title="<?= htmlspecialchars($p['title']) ?>" style="font-size: 11.5px;">
                                <?= htmlspecialchars($p['title']) ?>
                            </div>
                            <div class="text-muted mb-2" style="font-size: 10.5px; line-height: 1.2;">
                                <?= $p['qty_label'] ?>
                            </div>

                            <!-- Fiyatlar -->
                            <div class="mt-auto pt-1 border-top">
                                <div class="d-flex align-items-baseline justify-content-between mb-2">
                                    <span class="text-decoration-line-through text-muted" style="font-size: 10px;"><?= Helper::formatPrice($p['regular_price']) ?></span>
                                    <span class="fw-bold text-danger fs-6"><?= Helper::formatPrice($p['discount_price']) ?></span>
                                </div>

                                <!-- 1 Tıkla Sepete İndirimli Ekle -->
                                <form action="<?= SITE_URL ?>/cart.php" method="POST" class="m-0">
                                    <input type="hidden" name="action" value="add_cross_sell">
                                    <input type="hidden" name="product_slug" value="<?= $p['slug'] ?>">
                                    <input type="hidden" name="product_name" value="<?= htmlspecialchars($p['title']) ?>">
                                    <input type="hidden" name="quantity" value="<?= $p['quantity'] ?>">
                                    <input type="hidden" name="discount_price" value="<?= $p['discount_price'] ?>">
                                    <input type="hidden" name="design_svg" value="<?= htmlspecialchars($primaryDesignSvg) ?>">

                                    <button type="submit" class="btn btn-sm btn-primary w-100 rounded-pill py-1 fw-bold shadow-2xs d-flex align-items-center justify-content-center gap-1" style="font-size: 11px;">
                                        <i class="bi bi-bag-plus-fill"></i> Ekle
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- 🔍 BÜYÜK BOY CANLI 3D / GİYDİRME ÖNİZLEME MODALI -->
        <!-- ========================================================================= -->
        <div class="modal fade" id="crossSellMockupModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                    <div class="modal-header bg-dark text-white border-0 py-3 px-4">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-warning text-dark fw-bold px-2 py-1"><i class="bi bi-stars"></i> CANLI GİYDİRME</span>
                            <h5 class="modal-title fw-bold text-white mb-0" id="modalMockupTitle">Ürün Önizleme</h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>

                    <div class="modal-body p-4 bg-light">
                        <div class="row align-items-center g-4">
                            
                            <!-- Sol Büyük Canlı Mockup Sahnesi -->
                            <div class="col-md-6 text-center">
                                <div id="modalMockupStage" class="p-4 bg-white rounded-4 border shadow-sm d-flex align-items-center justify-content-center" style="min-height: 320px;"></div>
                            </div>

                            <!-- Sağ Özellikler & Sepete Ekle -->
                            <div class="col-md-6">
                                <span class="badge bg-danger-subtle text-danger fw-bold mb-2" id="modalMockupDiscountBadge">-%25 Sepet İndirimi</span>
                                <h4 class="fw-bold text-dark mb-1" id="modalMockupProdName">Yelken Bayrak</h4>
                                <p class="text-muted small mb-3" id="modalMockupDesc">Özellikler yükleniyor...</p>

                                <div class="p-3 bg-white rounded-3 border mb-3">
                                    <div class="d-flex justify-content-between mb-1 small text-muted">
                                        <span>Standart Fiyat:</span>
                                        <span class="text-decoration-line-through" id="modalRegularPrice">0,00 ₺</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1 small text-success fw-bold">
                                        <span>Sepete Özel İndirim:</span>
                                        <span id="modalDiscountAmount">-%25 Kazanç</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <span class="fw-bold text-dark">İndirimli Tutar:</span>
                                        <span class="fs-4 fw-bolder text-primary" id="modalDiscountPrice">0,00 ₺</span>
                                    </div>
                                    <div class="text-muted" style="font-size: 11px;" id="modalQtyNote">1 Adet / Takım</div>
                                </div>

                                <form action="<?= SITE_URL ?>/cart.php" method="POST" class="m-0">
                                    <input type="hidden" name="action" value="add_cross_sell">
                                    <input type="hidden" name="product_slug" id="modalInpSlug" value="">
                                    <input type="hidden" name="product_name" id="modalInpName" value="">
                                    <input type="hidden" name="quantity" id="modalInpQty" value="1">
                                    <input type="hidden" name="discount_price" id="modalInpPrice" value="0">
                                    <input type="hidden" name="design_svg" value="<?= htmlspecialchars($primaryDesignSvg) ?>">

                                    <button type="submit" class="btn btn-success w-100 py-3 fw-bold rounded-pill shadow fs-6 d-flex align-items-center justify-content-center gap-2">
                                        <i class="bi bi-bag-check-fill fs-5"></i>
                                        <span>Bu Tasarımla Sepetime Ekle</span>
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        const activeDesignSvg = <?= json_encode($primaryDesignSvg) ?>;

        function openDressMockupModal(key, title, discPrice, regPrice, slug, qty, qtyLabel, desc) {
            document.getElementById('modalMockupTitle').textContent = title + ' - Canlı Giydirme';
            document.getElementById('modalMockupProdName').textContent = title;
            document.getElementById('modalMockupDesc').textContent = desc + ' (' + qtyLabel + ')';
            document.getElementById('modalRegularPrice').textContent = regPrice;
            document.getElementById('modalDiscountPrice').textContent = discPrice;
            document.getElementById('modalQtyNote').textContent = qtyLabel;
            
            document.getElementById('modalInpSlug').value = slug;
            document.getElementById('modalInpName').value = title;
            document.getElementById('modalInpQty').value = qty;
            document.getElementById('modalInpPrice').value = parseFloat(discPrice.replace('.', '').replace(',', '.'));

            const stage = document.getElementById('modalMockupStage');
            if (stage) {
                if (key === 'flag') {
                    stage.innerHTML = `
                        <div style="position: relative; width: 180px; height: 320px; display: flex; align-items: center; justify-content: center;">
                            <div style="width: 8px; height: 290px; background: linear-gradient(90deg, #475569, #94a3b8, #334155); position: absolute; left: 30px; bottom: 20px; border-radius: 4px;"></div>
                            <div style="width: 60px; height: 18px; background: #0f172a; position: absolute; left: 4px; bottom: 8px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.3);"></div>
                            <div style="width: 130px; height: 250px; position: absolute; left: 38px; bottom: 40px; border-top-right-radius: 70px 100px; border-bottom-right-radius: 20px; overflow: hidden; box-shadow: 5px 8px 24px rgba(0,0,0,0.25); background: #ffffff;">
                                ${activeDesignSvg ? `<div style="width:100%; height:100%; transform: scale(0.6) rotate(-90deg); transform-origin: center center; display:flex; align-items:center; justify-content:center;">${activeDesignSvg}</div>` : `<div class="w-100 h-100 d-flex align-items-center justify-content-center text-white bg-primary fw-bold">YELKEN BAYRAK</div>`}
                            </div>
                        </div>
                    `;
                } else if (key === 'folder') {
                    stage.innerHTML = `
                        <div style="width: 220px; height: 280px; background: #0f172a; border-radius: 8px 16px 16px 8px; box-shadow: -8px 12px 30px rgba(0,0,0,0.3); border-left: 8px solid #0071e3; overflow: hidden; position: relative;">
                            ${activeDesignSvg ? `<div style="width:100%; height:100%; transform: scale(0.7); transform-origin: top left; padding: 10px;">${activeDesignSvg}</div>` : `<div class="w-100 h-100 d-flex align-items-center justify-content-center text-white fw-bold">CEPLİ DOSYA</div>`}
                        </div>
                    `;
                } else if (key === 'letterhead') {
                    stage.innerHTML = `
                        <div style="width: 200px; height: 280px; background: #ffffff; border-radius: 4px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); border: 1px solid #e2e8f0; overflow: hidden; padding: 14px; display: flex; flex-direction: column;">
                            ${activeDesignSvg ? `<div style="height: 60px; overflow: hidden; transform: scale(0.55); transform-origin: top left; width: 180%;">${activeDesignSvg}</div><div class="mt-3" style="border-top: 1px dashed #cbd5e1; height: 120px;"></div>` : `<div class="border-bottom pb-2 fw-bold text-dark">ANTETLİ KAĞIT</div><div class="mt-3" style="border-top: 1px dashed #cbd5e1; height: 120px;"></div>`}
                        </div>
                    `;
                } else if (key === 'stamp') {
                    stage.innerHTML = `
                        <div style="display: flex; flex-direction: column; align-items: center;">
                            <div style="width: 90px; height: 60px; background: linear-gradient(135deg, #e11d48, #be123c); border-radius: 12px 12px 6px 6px; box-shadow: 0 6px 16px rgba(225,29,72,0.3);"></div>
                            <div style="width: 140px; height: 95px; background: #1e293b; border-radius: 10px; display: flex; align-items: center; justify-content: center; border: 3px solid #334155; box-shadow: 0 10px 25px rgba(0,0,0,0.3); margin-top: 6px;">
                                <i class="bi bi-patch-check-fill text-white fs-1"></i>
                            </div>
                            <div style="width: 160px; height: 60px; border: 2px dashed #0071e3; border-radius: 8px; background: #ffffff; margin-top: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                                <span class="text-primary fw-bold fs-6"><i class="bi bi-check2-circle me-1"></i>RESMİ KAŞE ONAYI</span>
                            </div>
                        </div>
                    `;
                } else {
                    stage.innerHTML = `
                        <div style="width: 160px; height: 290px; display: flex; flex-direction: column; align-items: center;">
                            <div style="width: 140px; height: 260px; background: #ffffff; box-shadow: 0 8px 24px rgba(0,0,0,0.2); overflow: hidden; border: 1px solid #cbd5e1;">
                                ${activeDesignSvg ? `<div style="width:100%; height:100%; transform: scale(0.55); transform-origin: top left;">${activeDesignSvg}</div>` : `<div class="w-100 h-100 d-flex align-items-center justify-content-center text-white bg-dark fw-bold">ROLL-UP</div>`}
                            </div>
                            <div style="width: 160px; height: 22px; background: linear-gradient(90deg, #64748b, #cbd5e1, #475569); border-radius: 4px; margin-top: auto; box-shadow: 0 4px 10px rgba(0,0,0,0.3);"></div>
                        </div>
                    `;
                }
            }

            const modalEl = document.getElementById('crossSellMockupModal');
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        }

        function filterCartCrossSell(industry, title, btn) {
            // Aktif pill güncelle
            document.querySelectorAll('.industry-filter-pill').forEach(b => {
                b.classList.remove('btn-primary', 'active');
                b.classList.add('btn-outline-secondary');
            });
            if (btn) {
                btn.classList.remove('btn-outline-secondary');
                btn.classList.add('btn-primary', 'active');
            }

            // Başlık güncelle
            const heroTitle = document.getElementById('crossSellHeroTitle');
            if (heroTitle) {
                if (industry === 'all') {
                    heroTitle.innerHTML = '🎉 Tasarımınıza Özel Tamamlayıcı Ürün Fırsatları';
                } else {
                    heroTitle.innerHTML = `✨ <strong>${title}</strong> Sektörüne Özel Tamamlayıcı Ürünler`;
                }
            }

            // Kartları filtrele
            const items = document.querySelectorAll('.cross-sell-col');
            items.forEach(item => {
                const itemIndustries = (item.dataset.industries || '').split(',');
                if (industry === 'all' || itemIndustries.includes(industry)) {
                    item.style.display = '';
                    item.style.opacity = '1';
                } else {
                    item.style.display = 'none';
                    item.style.opacity = '0';
                }
            });
        }

        // Tasarımcıda seçilmiş sektör varsa otomatik olarak sepet filtrelerinde aktifleştir
        document.addEventListener('DOMContentLoaded', () => {
            const savedInd = localStorage.getItem('user_selected_industry');
            if (savedInd) {
                const mapInd = {
                    'law': 'hukuk',
                    'avukat': 'hukuk',
                    'hukuk': 'hukuk',
                    'doctor': 'saglik',
                    'saglik': 'saglik',
                    'health': 'saglik',
                    'real_estate': 'emlak',
                    'emlak': 'emlak',
                    'food': 'gida',
                    'gida': 'gida',
                    'restoran': 'gida',
                    'cafe': 'gida',
                    'architect': 'mimarlik',
                    'mimarlik': 'mimarlik',
                    'insaat': 'mimarlik',
                    'beauty': 'guzellik',
                    'guzellik': 'guzellik',
                    'kuafor': 'guzellik',
                    'tech': 'teknoloji',
                    'teknoloji': 'teknoloji',
                    'software': 'teknoloji'
                };
                const targetKey = mapInd[savedInd.toLowerCase()] || 'all';
                const targetBtn = document.querySelector(`button[onclick*="'${targetKey}'"]`);
                if (targetBtn && targetKey !== 'all') {
                    targetBtn.click();
                }
            }
        });
        </script>

    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
