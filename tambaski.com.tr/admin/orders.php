<?php
require_once __DIR__ . '/../config/config.php';
Auth::requireAdmin();

$orderModel = new Order();
$db = Database::getInstance()->getConnection();

$action = $_GET['action'] ?? 'list';

// 1. Vektörel SVG İndirme
if ($action === 'download_svg') {
    $itemId = (int)($_GET['item_id'] ?? 0);
    $stmt = $db->prepare("SELECT * FROM order_items WHERE id = ?");
    $stmt->execute([$itemId]);
    $item = $stmt->fetch();

    if ($item && !empty($item['design_svg'])) {
        $filename = 'baski_siparis_' . $item['order_id'] . '_kalem_' . $item['id'] . '.svg';
        header('Content-Type: image/svg+xml');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $item['design_svg'];
        exit;
    } else {
        Helper::setFlash('danger', 'Vektörel SVG dosyası bulunamadı.');
        header("Location: " . SITE_URL . "/admin/orders.php");
        exit;
    }
}

// 2. Sipariş Silme
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = (int)($_POST['order_id'] ?? 0);
    if ($orderId > 0) {
        $orderModel->delete($orderId);
        Helper::setFlash('success', 'Sipariş başarıyla silindi.');
    }
    header("Location: " . SITE_URL . "/admin/orders.php");
    exit;
}

// 3. Adım Adım İlerleme (Tek Tıkla Bir Sonraki Aşamaya Geçiş)
if ($action === 'step_status' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = (int)($_POST['order_id'] ?? 0);
    $nextStep = trim($_POST['next_step'] ?? '');

    if ($orderId > 0 && !empty($nextStep)) {
        if ($nextStep === 'payment_received') {
            $orderModel->updatePaymentStatus($orderId, 'paid');
            $orderModel->updateStatus($orderId, 'payment_received');
            Helper::setFlash('success', 'Ödeme onaylandı: Sipariş "Ödeme Alındı" durumuna geçti.');
        } elseif ($nextStep === 'in_production') {
            $orderModel->updateStatus($orderId, 'in_production');
            Helper::setFlash('success', 'Sipariş "Hazırlanıyor / Baskıda" aşamasına alındı.');
        } elseif ($nextStep === 'delivered') {
            $orderModel->updateStatus($orderId, 'delivered');
            Helper::setFlash('success', 'Sipariş başarıyla "Teslim Edildi" olarak tamamlandı.');
        } elseif ($nextStep === 'reopen') {
            $orderModel->updateStatus($orderId, 'payment_received');
            Helper::setFlash('success', 'Sipariş tekrar aktif sürece alındı.');
        } elseif ($nextStep === 'cancelled') {
            $orderModel->updateStatus($orderId, 'cancelled');
            Helper::setFlash('warning', 'Sipariş iptal edildi.');
        }
    }

    $redirect = !empty($_POST['redirect_to']) ? $_POST['redirect_to'] : (SITE_URL . "/admin/orders.php");
    header("Location: " . $redirect);
    exit;
}

// 4. Kargo Bilgisi Girerek Kargoya Verme (ZORUNLU Kargo Firması ve Takip Kodu)
if ($action === 'ship_order' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = (int)($_POST['order_id'] ?? 0);
    $cargoCompany = trim($_POST['cargo_company'] ?? 'Yurtiçi Kargo');
    $cargoTracking = trim($_POST['cargo_tracking_code'] ?? '');

    if ($orderId <= 0) {
        Helper::setFlash('danger', 'Geçersiz sipariş.');
    } elseif (empty($cargoTracking)) {
        Helper::setFlash('danger', 'Kargoya verildi durumuna geçmek için Kargo Takip Numarası girmek zorunludur.');
    } else {
        $orderModel->updateCargo($orderId, $cargoCompany, $cargoTracking);
        Helper::setFlash('success', 'Kargo bilgisi kaydedildi ve sipariş "Kargoya Verildi" olarak güncellendi.');
    }

    $redirect = !empty($_POST['redirect_to']) ? $_POST['redirect_to'] : (SITE_URL . "/admin/orders.php");
    header("Location: " . $redirect);
    exit;
}

// 5. Detaylı Form Güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
    $orderId = (int)($_POST['order_id'] ?? 0);
    $orderStatus = $_POST['order_status'] ?? 'pending_payment';
    $paymentStatus = $_POST['payment_status'] ?? 'pending';
    $cargoCompany = trim($_POST['cargo_company'] ?? 'Yurtiçi Kargo');
    $cargoTracking = trim($_POST['cargo_tracking_code'] ?? '');

    if ($orderStatus === 'shipped' && empty($cargoTracking)) {
        Helper::setFlash('danger', '⚠️ Siparişi "Kargoya Verildi" yapmak için Kargo Takip No girmelisiniz.');
        header("Location: " . SITE_URL . "/admin/orders.php?action=view&id=" . $orderId);
        exit;
    }

    $orderModel->updateStatus($orderId, $orderStatus);
    $orderModel->updatePaymentStatus($orderId, $paymentStatus);
    
    if (!empty($cargoTracking)) {
        $orderModel->updateCargo($orderId, $cargoCompany, $cargoTracking);
    }

    Helper::setFlash('success', 'Sipariş bilgileri başarıyla güncellendi.');
    header("Location: " . SITE_URL . "/admin/orders.php?action=view&id=" . $orderId);
    exit;
}

$pageTitle = 'Siparişler';
require_once __DIR__ . '/header.php';

// =========================================================================
// 1. SİPARİŞ DETAY EKRANI (action=view)
// =========================================================================
if ($action === 'view'):
    $orderId = (int)($_GET['id'] ?? 0);
    $order = $orderModel->getById($orderId);
    if (!$order) {
        echo '<div class="alert alert-danger rounded-3 p-4 my-4">Sipariş bulunamadı.</div>';
        require_once __DIR__ . '/footer.php';
        exit;
    }

    $status = $order['order_status'];
    $isPaid = $order['payment_status'] === 'paid';
?>
    <!-- Sade Başlık -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="<?= SITE_URL ?>/admin/orders.php" class="btn btn-sm btn-outline-secondary py-1 px-2.5">
                <i class="bi bi-arrow-left"></i> Geri
            </a>
            <h4 class="fw-bold mb-0 font-monospace text-dark">Sipariş #<?= htmlspecialchars($order['order_number']) ?></h4>
            <span class="text-muted small ms-2"><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-dark" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Yazdır
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteOrderModal">
                <i class="bi bi-trash3 me-1"></i> Sil
            </button>
        </div>
    </div>

    <!-- 🟢 ADIM ADIM İLERLEME ÇUBUĞU & TEK TIKLA SONRAKİ AŞAMA -->
    <div class="bg-white border rounded-3 p-3 mb-3 d-flex flex-wrap align-items-center justify-content-between gap-3 shadow-2xs">
        <div>
            <span class="text-muted small d-block mb-1">Mevcut Durum:</span>
            <div class="d-flex align-items-center gap-2">
                <span class="fs-6"><?= Helper::getPaymentStatusBadge($order['payment_status']) ?></span>
                <span class="fs-6"><?= Helper::getOrderStatusBadge($order['order_status']) ?></span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- 1. Adım: Ödeme Bekliyor ise -> Ödeme Alındı Yap -->
            <?php if (!$isPaid || $status === 'pending_payment'): ?>
                <form action="<?= SITE_URL ?>/admin/orders.php?action=step_status" method="POST">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <input type="hidden" name="next_step" value="payment_received">
                    <input type="hidden" name="redirect_to" value="<?= SITE_URL ?>/admin/orders.php?action=view&id=<?= $order['id'] ?>">
                    <button type="submit" class="btn btn-success py-2 px-3 fw-bold rounded-pill shadow-2xs">
                        <i class="bi bi-check-circle me-1"></i> 1. Adım: Ödemeyi Onayla &rarr;
                    </button>
                </form>

            <!-- 2. Adım: Ödeme Alındı ise -> Hazırlanıyor / Baskıya Al -->
            <?php elseif ($status === 'payment_received'): ?>
                <form action="<?= SITE_URL ?>/admin/orders.php?action=step_status" method="POST">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <input type="hidden" name="next_step" value="in_production">
                    <input type="hidden" name="redirect_to" value="<?= SITE_URL ?>/admin/orders.php?action=view&id=<?= $order['id'] ?>">
                    <button type="submit" class="btn btn-primary py-2 px-3 fw-bold rounded-pill shadow-2xs">
                        <i class="bi bi-gear-fill me-1"></i> 2. Adım: Hazırlanıyor'a Al (Baskıya Başla) &rarr;
                    </button>
                </form>

            <!-- 3. Adım: Hazırlanıyor ise -> Kargoya Ver (Takip No Zorunlu Modal) -->
            <?php elseif ($status === 'in_production' || $status === 'preparing' || $status === 'design_approval' || $status === 'packaged'): ?>
                <button type="button" class="btn btn-dark py-2 px-3 fw-bold rounded-pill shadow-2xs" data-bs-toggle="modal" data-bs-target="#shipModalDetail">
                    <i class="bi bi-truck me-1"></i> 3. Adım: Kargoya Ver & Takip No Gir &rarr;
                </button>

            <!-- 4. Adım: Kargoda ise -> Teslim Edildi Yap -->
            <?php elseif ($status === 'shipped'): ?>
                <form action="<?= SITE_URL ?>/admin/orders.php?action=step_status" method="POST">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <input type="hidden" name="next_step" value="delivered">
                    <input type="hidden" name="redirect_to" value="<?= SITE_URL ?>/admin/orders.php?action=view&id=<?= $order['id'] ?>">
                    <button type="submit" class="btn btn-outline-success py-2 px-3 fw-bold rounded-pill shadow-2xs">
                        <i class="bi bi-box2-heart me-1"></i> 4. Adım: Siparişi "Teslim Edildi" Yap ✓
                    </button>
                </form>

            <!-- 5. Adım: Tamamlandı -->
            <?php elseif ($status === 'delivered'): ?>
                <div class="badge bg-success-subtle text-success border border-success p-2 px-3 rounded-pill fs-6">
                    <i class="bi bi-check-circle-fill me-1"></i> Sipariş Başarıyla Tamamlandı
                </div>

            <!-- İptal ise: Yeniden Aç -->
            <?php elseif ($status === 'cancelled'): ?>
                <form action="<?= SITE_URL ?>/admin/orders.php?action=step_status" method="POST">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <input type="hidden" name="next_step" value="reopen">
                    <input type="hidden" name="redirect_to" value="<?= SITE_URL ?>/admin/orders.php?action=view&id=<?= $order['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> İptali Geri Al (Yeniden Aç)
                    </button>
                </form>
            <?php endif; ?>

            <?php if ($status !== 'cancelled' && $status !== 'delivered'): ?>
                <form action="<?= SITE_URL ?>/admin/orders.php?action=step_status" method="POST" class="d-inline" onsubmit="return confirm('Bu siparişi iptal etmek istediğinize emin misiniz?');">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <input type="hidden" name="next_step" value="cancelled">
                    <input type="hidden" name="redirect_to" value="<?= SITE_URL ?>/admin/orders.php?action=view&id=<?= $order['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5">
                        İptal Et
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-3">
        <!-- Sol Kolon: Kalemler ve Müşteri -->
        <div class="col-lg-8">
            <!-- Kalemler -->
            <div class="bg-white rounded-3 border p-3 mb-3">
                <div class="fw-bold text-dark mb-2 pb-1 border-bottom d-flex justify-content-between">
                    <span>Baskı Kalemleri</span>
                    <span class="text-muted small"><?= count($order['items']) ?> Adet Ürün</span>
                </div>

                <?php foreach ($order['items'] as $item): 
                    $cleanOptions = [];
                    $hasPackage = false;
                    if (!empty($item['options_array'])) {
                        foreach ($item['options_array'] as $optStr) {
                            if (stripos($optStr, 'Paket:') !== false) $hasPackage = true;
                        }
                        foreach ($item['options_array'] as $optStr) {
                            if ($hasPackage && stripos($optStr, 'Özel Boyut:') !== false && (stripos($optStr, '8.4') !== false || stripos($optStr, '5.2') !== false)) {
                                continue;
                            }
                            $cleanOptions[] = $optStr;
                        }
                    }
                ?>
                    <div class="d-flex flex-wrap align-items-center justify-content-between p-2.5 bg-light rounded-2 border mb-2 gap-2">
                        <div class="d-flex align-items-center gap-3" style="min-width: 250px;">
                            <?php if (!empty($item['design_svg'])): ?>
                                <div class="bg-white border rounded p-1 text-center" style="width: 55px; height: 55px; overflow: hidden;">
                                    <?= $item['design_svg'] ?>
                                </div>
                            <?php elseif (!empty($item['design_file'])): ?>
                                <div class="bg-white border rounded p-1 text-center d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                                    <i class="bi bi-file-earmark-arrow-up text-primary fs-4"></i>
                                </div>
                            <?php else: ?>
                                <div class="bg-white border rounded p-1 text-center d-flex align-items-center justify-content-center text-muted" style="width: 55px; height: 55px;">
                                    <i class="bi bi-card-image fs-4"></i>
                                </div>
                            <?php endif; ?>

                            <div>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($item['product_name']) ?></div>
                                <div class="text-muted small">Miktar: <strong><?= $item['quantity'] ?> Adet</strong></div>
                                <?php if (!empty($cleanOptions)): ?>
                                    <div class="text-secondary small mt-0.5" style="font-size: 11.5px;">
                                        <?= implode(' • ', array_map('htmlspecialchars', $cleanOptions)) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3 ms-auto text-end">
                            <div>
                                <div class="fw-bold text-primary fs-6"><?= Helper::formatPrice($item['total_price']) ?></div>
                                <div class="text-muted" style="font-size: 11px;">Birim: <?= Helper::formatPrice($item['unit_price']) ?></div>
                            </div>
                            <?php if (!empty($item['design_svg'])): ?>
                                <a href="<?= SITE_URL ?>/admin/orders.php?action=download_svg&item_id=<?= $item['id'] ?>" class="btn btn-sm btn-primary py-1 px-2.5 text-nowrap">
                                    <i class="bi bi-download me-1"></i> SVG İndir
                                </a>
                            <?php elseif (!empty($item['design_file'])): ?>
                                <a href="<?= SITE_URL . '/' . htmlspecialchars($item['design_file']) ?>" target="_blank" class="btn btn-sm btn-dark py-1 px-2.5 text-nowrap">
                                    <i class="bi bi-download me-1"></i> Dosyayı İndir
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Tutar Özeti -->
                <div class="d-flex justify-content-end pt-2 border-top">
                    <div style="min-width: 200px;" class="small text-end">
                        <div class="text-muted mb-1">Ara Toplam: <strong><?= Helper::formatPrice($order['subtotal']) ?></strong></div>
                        <div class="text-muted mb-1">KDV: <strong><?= Helper::formatPrice($order['tax_amount']) ?></strong></div>
                        <div class="text-muted mb-1">Kargo: <strong><?= Helper::formatPrice($order['shipping_fee']) ?></strong></div>
                        <div class="fw-bold fs-6 text-dark pt-1 border-top">Toplam: <span class="text-primary"><?= Helper::formatPrice($order['total_amount']) ?></span></div>
                    </div>
                </div>
            </div>

            <!-- Müşteri ve Teslimat -->
            <div class="bg-white rounded-3 border p-3">
                <div class="fw-bold text-dark mb-2 pb-1 border-bottom">Müşteri & Teslimat Bilgisi</div>
                <div class="row g-2 small">
                    <div class="col-md-6">
                        <div class="text-muted">Ad Soyad: <strong class="text-dark"><?= htmlspecialchars($order['customer_name']) ?></strong></div>
                        <div class="text-muted mt-1">Telefon: 
                            <a href="tel:<?= htmlspecialchars($order['customer_phone']) ?>" class="fw-bold text-decoration-none"><?= htmlspecialchars($order['customer_phone']) ?></a>
                            <a href="https://wa.me/90<?= preg_replace('/[^0-9]/', '', $order['customer_phone']) ?>" target="_blank" class="badge bg-success ms-1 text-decoration-none py-0.5">WhatsApp</a>
                        </div>
                        <div class="text-muted mt-1">E-Posta: <?= htmlspecialchars($order['customer_email']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted">Teslimat Adresi:</div>
                        <div class="fw-semibold text-dark"><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></div>
                        <div class="text-primary fw-bold"><?= htmlspecialchars($order['shipping_district']) ?> / <?= htmlspecialchars($order['shipping_city']) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sağ Kolon: Manuel Durum ve Kargo İşlemleri -->
        <div class="col-lg-4">
            <div class="bg-white rounded-3 border p-3">
                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">Manuel Ayar & Kargo Bilgisi</h6>

                <form action="<?= SITE_URL ?>/admin/orders.php?action=update" method="POST" id="orderUpdateForm">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-bold mb-1">Ödeme Durumu</label>
                        <select name="payment_status" class="form-select form-select-sm">
                            <option value="pending" <?= $order['payment_status'] === 'pending' ? 'selected' : '' ?>>⏳ Ödeme Bekliyor</option>
                            <option value="paid" <?= $order['payment_status'] === 'paid' ? 'selected' : '' ?>>✅ Ödendi</option>
                            <option value="failed" <?= $order['payment_status'] === 'failed' ? 'selected' : '' ?>>⚠️ Başarısız</option>
                            <option value="refunded" <?= $order['payment_status'] === 'refunded' ? 'selected' : '' ?>>↩️ İade Edildi</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold mb-1">Sipariş Aşaması</label>
                        <select name="order_status" id="orderStatusSelect" class="form-select form-select-sm">
                            <option value="pending_payment" <?= $order['order_status'] === 'pending_payment' ? 'selected' : '' ?>>⏳ Ödeme Bekleniyor</option>
                            <option value="payment_received" <?= $order['order_status'] === 'payment_received' ? 'selected' : '' ?>>✅ Ödeme Alındı</option>
                            <option value="in_production" <?= $order['order_status'] === 'in_production' || $order['order_status'] === 'preparing' ? 'selected' : '' ?>>⚙️ Hazırlanıyor / Baskıda</option>
                            <option value="shipped" <?= $order['order_status'] === 'shipped' ? 'selected' : '' ?>>🚚 Kargoya Verildi</option>
                            <option value="delivered" <?= $order['order_status'] === 'delivered' ? 'selected' : '' ?>>🎉 Teslim Edildi</option>
                            <option value="cancelled" <?= $order['order_status'] === 'cancelled' ? 'selected' : '' ?>>❌ İptal Edildi</option>
                        </select>
                    </div>

                    <div class="p-2.5 bg-light rounded-2 border mb-3">
                        <div class="fw-bold small text-dark mb-2"><i class="bi bi-truck me-1"></i>Kargo Bilgileri</div>
                        
                        <div class="mb-2">
                            <label class="form-label text-muted" style="font-size: 11px;">Kargo Firması</label>
                            <select name="cargo_company" class="form-select form-select-sm">
                                <?php foreach (Cargo::getCompanies() as $key => $c): ?>
                                    <option value="<?= $key ?>" <?= ($order['cargo_company'] === $key) ? 'selected' : '' ?>><?= $c['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-1">
                            <label class="form-label text-muted" style="font-size: 11px;">Takip Numarası</label>
                            <input type="text" name="cargo_tracking_code" id="cargoTrackingInput" class="form-control form-control-sm font-monospace" placeholder="Örn: 1234567890" value="<?= htmlspecialchars($order['cargo_tracking_code'] ?? '') ?>">
                        </div>

                        <?php if (!empty($order['cargo_tracking_code'])): ?>
                            <div class="mt-2">
                                <a href="<?= Cargo::getTrackingLink($order['cargo_company'], $order['cargo_tracking_code']) ?>" target="_blank" class="small text-decoration-none fw-bold text-primary">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Kargo Sitesinde Gör &rarr;
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-sm btn-primary w-100 py-2 fw-bold">
                        <i class="bi bi-check2 me-1"></i> Değişiklikleri Kaydet
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Kargoya Ver Modal (Detail İçin) -->
    <div class="modal fade" id="shipModalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm text-start">
            <div class="modal-content rounded-3 border-0 shadow">
                <form action="<?= SITE_URL ?>/admin/orders.php?action=ship_order" method="POST">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <input type="hidden" name="redirect_to" value="<?= SITE_URL ?>/admin/orders.php?action=view&id=<?= $order['id'] ?>">

                    <div class="modal-header border-bottom py-2">
                        <h6 class="modal-title fw-bold text-dark"><i class="bi bi-truck me-1"></i>Kargo Takip Bilgisi Gir</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body py-3">
                        <div class="small text-muted mb-2">
                            Siparişi kargoya vermek için takip numarasını giriniz:
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold mb-1">Kargo Firması</label>
                            <select name="cargo_company" class="form-select form-select-sm">
                                <?php foreach (Cargo::getCompanies() as $key => $c): ?>
                                    <option value="<?= $key ?>" <?= ($order['cargo_company'] === $key) ? 'selected' : '' ?>><?= $c['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="form-label small fw-bold mb-1">Kargo Takip Numarası *</label>
                            <input type="text" name="cargo_tracking_code" class="form-control form-control-sm font-monospace" placeholder="Örn: 1234567890" value="<?= htmlspecialchars($order['cargo_tracking_code'] ?? '') ?>" required autofocus>
                        </div>
                    </div>
                    <div class="modal-footer border-top py-2">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-sm btn-primary fw-bold">
                            <i class="bi bi-check2 me-1"></i> Kargoya Verildi Olarak İşaretle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Silme Modal -->
    <div class="modal fade" id="deleteOrderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-3 border-0 shadow">
                <div class="modal-body text-center p-4">
                    <i class="bi bi-exclamation-triangle text-danger fs-1 mb-2 d-block"></i>
                    <h6 class="fw-bold mb-1">Siparişi Sil</h6>
                    <p class="small text-muted mb-3">Bu sipariş ve baskı dosyaları kalıcı olarak silinecek.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-sm btn-light px-3" data-bs-dismiss="modal">Vazgeç</button>
                        <form action="<?= SITE_URL ?>/admin/orders.php?action=delete" method="POST" class="d-inline">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger px-3 fw-bold">Evet, Sil</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php 
// =========================================================================
// 2. SİPARİŞ LİSTESİ EKRANI (action=list)
// =========================================================================
else: 
    $statusFilter = $_GET['status'] ?? null;
    $paymentFilter = $_GET['payment_status'] ?? null;
    $search = $_GET['search'] ?? null;
    $activeTab = $_GET['tab'] ?? 'all';

    if ($activeTab === 'paid') {
        $paymentFilter = 'paid';
    } elseif ($activeTab === 'pending_payment') {
        $paymentFilter = 'pending';
    } elseif ($activeTab === 'in_production') {
        $statusFilter = 'in_production_group';
    } elseif ($activeTab === 'shipped') {
        $statusFilter = 'shipped';
    } elseif ($activeTab === 'delivered') {
        $statusFilter = 'delivered';
    } elseif ($activeTab === 'cancelled') {
        $statusFilter = 'cancelled_group';
    }

    $orders = $orderModel->getAll(100, $statusFilter, $paymentFilter, $search);
    $stats = $orderModel->getStats();
?>
    <!-- Üst Satır: Başlık & Sade Özet Barı -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Siparişler</h4>
            <span class="text-muted small">Toplam <?= $stats['total_orders'] ?> sipariş kayıtlı</span>
        </div>
        <div class="d-flex align-items-center gap-3 small bg-white px-3 py-2 rounded-2 border">
            <div>Ödenen Ciro: <strong class="text-success"><?= Helper::formatPrice($stats['paid_sum']) ?></strong> (<?= $stats['paid_orders'] ?>)</div>
            <div class="text-muted">|</div>
            <div>Bekleyen: <strong class="text-warning"><?= $stats['pending_payment'] ?></strong></div>
            <div class="text-muted">|</div>
            <div>Baskıda: <strong class="text-primary"><?= $stats['in_production'] ?></strong></div>
            <div class="text-muted">|</div>
            <div>Kargoda: <strong class="text-dark"><?= $stats['shipped'] ?></strong></div>
        </div>
    </div>

    <!-- Sade Filtre Sekmeleri -->
    <div class="border-bottom mb-3">
        <ul class="nav nav-tabs border-bottom-0 gap-1 small">
            <li class="nav-item">
                <a class="nav-link <?= ($activeTab === 'all' && empty($statusFilter) && empty($paymentFilter)) ? 'active fw-bold' : 'text-muted' ?>" href="<?= SITE_URL ?>/admin/orders.php?tab=all">
                    Tümü (<?= $stats['total_orders'] ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($activeTab === 'paid') ? 'active fw-bold text-success' : 'text-muted' ?>" href="<?= SITE_URL ?>/admin/orders.php?tab=paid">
                    Ödemesi Yapılanlar (<?= $stats['paid_orders'] ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($activeTab === 'pending_payment') ? 'active fw-bold text-warning-emphasis' : 'text-muted' ?>" href="<?= SITE_URL ?>/admin/orders.php?tab=pending_payment">
                    Ödeme Bekleyenler (<?= $stats['pending_payment'] ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($activeTab === 'in_production') ? 'active fw-bold text-primary' : 'text-muted' ?>" href="<?= SITE_URL ?>/admin/orders.php?tab=in_production">
                    Baskıda / Hazırlanıyor (<?= $stats['in_production'] ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($activeTab === 'shipped') ? 'active fw-bold' : 'text-muted' ?>" href="<?= SITE_URL ?>/admin/orders.php?tab=shipped">
                    Kargodakiler (<?= $stats['shipped'] ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($activeTab === 'delivered') ? 'active fw-bold' : 'text-muted' ?>" href="<?= SITE_URL ?>/admin/orders.php?tab=delivered">
                    Teslim Edilenler (<?= $stats['delivered'] ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($activeTab === 'cancelled') ? 'active fw-bold text-danger' : 'text-muted' ?>" href="<?= SITE_URL ?>/admin/orders.php?tab=cancelled">
                    İptal / İade (<?= $stats['cancelled'] ?>)
                </a>
            </li>
        </ul>
    </div>

    <!-- Arama Çubuğu -->
    <div class="mb-3">
        <form action="<?= SITE_URL ?>/admin/orders.php" method="GET" class="d-flex gap-2">
            <input type="hidden" name="tab" value="<?= htmlspecialchars($activeTab) ?>">
            <div class="input-group input-group-sm" style="max-width: 400px;">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Sipariş no, müşteri veya tel ara..." value="<?= htmlspecialchars($search ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-sm btn-dark px-3">Ara</button>
            <?php if (!empty($search)): ?>
                <a href="<?= SITE_URL ?>/admin/orders.php?tab=<?= urlencode($activeTab) ?>" class="btn btn-sm btn-outline-secondary">Temizle</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Sipariş Tablosu -->
    <div class="bg-white rounded-3 border">
        <?php if (empty($orders)): ?>
            <div class="text-center py-5 text-muted small">
                Bu sekmede gösterilecek sipariş bulunamadı.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle small mb-0">
                    <thead class="table-light text-secondary" style="font-size: 12px;">
                        <tr>
                            <th>Sipariş No</th>
                            <th>Tarih</th>
                            <th>Müşteri</th>
                            <th>Kalemler</th>
                            <th>Tutar</th>
                            <th>Mevcut Durum</th>
                            <th style="min-width: 170px;">Sonraki Adım (Tek Tık)</th>
                            <th>Kargo Takip</th>
                            <th class="text-end">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $ord): 
                            $items = $orderModel->getOrderItems($ord['id']);
                            $ordStatus = $ord['order_status'];
                            $isOrdPaid = $ord['payment_status'] === 'paid';
                        ?>
                            <tr>
                                <!-- No -->
                                <td class="fw-bold font-monospace">
                                    <a href="<?= SITE_URL ?>/admin/orders.php?action=view&id=<?= $ord['id'] ?>" class="text-decoration-none">
                                        #<?= htmlspecialchars($ord['order_number']) ?>
                                    </a>
                                </td>

                                <!-- Tarih -->
                                <td class="text-muted" style="font-size: 11.5px;">
                                    <?= date('d.m.Y H:i', strtotime($ord['created_at'])) ?>
                                </td>

                                <!-- Müşteri -->
                                <td>
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($ord['customer_name']) ?></div>
                                    <div class="text-muted" style="font-size: 11px;"><?= htmlspecialchars($ord['customer_phone']) ?></div>
                                </td>

                                <!-- Kalemler -->
                                <td>
                                    <?php foreach ($items as $it): ?>
                                        <div class="text-truncate" style="max-width: 160px;" title="<?= htmlspecialchars($it['product_name']) ?>">
                                            <?= htmlspecialchars($it['product_name']) ?> <span class="text-muted">(<?= $it['quantity'] ?>)</span>
                                        </div>
                                        <?php if (!empty($it['design_svg'])): ?>
                                            <a href="<?= SITE_URL ?>/admin/orders.php?action=download_svg&item_id=<?= $it['id'] ?>" class="badge bg-primary-subtle text-primary text-decoration-none me-1" style="font-size: 10px;">
                                                <i class="bi bi-download"></i> SVG
                                            </a>
                                        <?php elseif (!empty($it['design_file'])): ?>
                                            <a href="<?= SITE_URL . '/' . htmlspecialchars($it['design_file']) ?>" target="_blank" class="badge bg-secondary text-decoration-none me-1" style="font-size: 10px;">
                                                <i class="bi bi-download"></i> Dosya
                                            </a>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </td>

                                <!-- Tutar -->
                                <td>
                                    <div class="fw-bold text-dark"><?= Helper::formatPrice($ord['total_amount']) ?></div>
                                    <span class="text-muted" style="font-size: 10.5px;"><?= strtoupper($ord['payment_method']) ?></span>
                                </td>

                                <!-- Mevcut Durum Rozetleri -->
                                <td>
                                    <div><?= Helper::getPaymentStatusBadge($ord['payment_status']) ?></div>
                                    <div class="mt-1"><?= Helper::getOrderStatusBadge($ord['order_status']) ?></div>
                                </td>

                                <!-- 🟢 SONRAKİ ADIM (TEK TIKLA İLERLEME BUTONU) -->
                                <td>
                                    <!-- Durum 1: Ödeme Bekliyor -> Ödeme Onayla -->
                                    <?php if (!$isOrdPaid || $ordStatus === 'pending_payment'): ?>
                                        <form action="<?= SITE_URL ?>/admin/orders.php?action=step_status" method="POST">
                                            <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                            <input type="hidden" name="next_step" value="payment_received">
                                            <input type="hidden" name="redirect_to" value="<?= SITE_URL ?>/admin/orders.php?tab=<?= urlencode($activeTab) ?>">
                                            <button type="submit" class="btn btn-xs btn-success py-1 px-2.5 rounded-pill fw-bold text-nowrap shadow-2xs">
                                                <i class="bi bi-check2-circle me-1"></i> Ödemeyi Onayla &rarr;
                                            </button>
                                        </form>

                                    <!-- Durum 2: Ödeme Alındı -> Hazırlanıyor'a Al -->
                                    <?php elseif ($ordStatus === 'payment_received'): ?>
                                        <form action="<?= SITE_URL ?>/admin/orders.php?action=step_status" method="POST">
                                            <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                            <input type="hidden" name="next_step" value="in_production">
                                            <input type="hidden" name="redirect_to" value="<?= SITE_URL ?>/admin/orders.php?tab=<?= urlencode($activeTab) ?>">
                                            <button type="submit" class="btn btn-xs btn-primary py-1 px-2.5 rounded-pill fw-bold text-nowrap shadow-2xs">
                                                <i class="bi bi-gear-fill me-1"></i> Hazırlanıyor'a Al &rarr;
                                            </button>
                                        </form>

                                    <!-- Durum 3: Hazırlanıyor / Baskıda -> Kargoya Ver (Modal Takip No Zorunlu) -->
                                    <?php elseif ($ordStatus === 'in_production' || $ordStatus === 'preparing' || $ordStatus === 'design_approval' || $ordStatus === 'packaged'): ?>
                                        <button type="button" class="btn btn-xs btn-dark py-1 px-2.5 rounded-pill fw-bold text-nowrap shadow-2xs" data-bs-toggle="modal" data-bs-target="#shipModal<?= $ord['id'] ?>">
                                            <i class="bi bi-truck me-1"></i> Kargoya Ver...
                                        </button>

                                    <!-- Durum 4: Kargoda -> Teslim Edildi Yap -->
                                    <?php elseif ($ordStatus === 'shipped'): ?>
                                        <form action="<?= SITE_URL ?>/admin/orders.php?action=step_status" method="POST">
                                            <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                            <input type="hidden" name="next_step" value="delivered">
                                            <input type="hidden" name="redirect_to" value="<?= SITE_URL ?>/admin/orders.php?tab=<?= urlencode($activeTab) ?>">
                                            <button type="submit" class="btn btn-xs btn-outline-success py-1 px-2.5 rounded-pill fw-bold text-nowrap">
                                                <i class="bi bi-box2-heart me-1"></i> Teslim Edildi Yap ✓
                                            </button>
                                        </form>

                                    <!-- Durum 5: Teslim Edildi (Tamamlandı) -->
                                    <?php elseif ($ordStatus === 'delivered'): ?>
                                        <span class="badge bg-success-subtle text-success py-1 px-2 rounded-pill fw-bold">
                                            <i class="bi bi-check-all me-1"></i> Teslim Edildi
                                        </span>

                                    <!-- Durum 6: İptal / İade -->
                                    <?php elseif ($ordStatus === 'cancelled' || $ordStatus === 'refunded'): ?>
                                        <form action="<?= SITE_URL ?>/admin/orders.php?action=step_status" method="POST">
                                            <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                            <input type="hidden" name="next_step" value="reopen">
                                            <input type="hidden" name="redirect_to" value="<?= SITE_URL ?>/admin/orders.php?tab=<?= urlencode($activeTab) ?>">
                                            <button type="submit" class="btn btn-xs btn-outline-secondary py-0.5 px-2 rounded-pill" title="Yeniden Aktif Et">
                                                <i class="bi bi-arrow-counterclockwise"></i> Yeniden Aç
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>

                                <!-- Kargo Takip Kolonu -->
                                <td>
                                    <?php if (!empty($ord['cargo_tracking_code'])): ?>
                                        <div style="font-size: 11px;" class="fw-bold text-dark"><?= htmlspecialchars($ord['cargo_company'] ?: 'Kargo') ?></div>
                                        <a href="<?= Cargo::getTrackingLink($ord['cargo_company'], $ord['cargo_tracking_code']) ?>" target="_blank" class="font-monospace small text-primary text-decoration-none">
                                            <?= htmlspecialchars($ord['cargo_tracking_code']) ?> <i class="bi bi-box-arrow-up-right" style="font-size: 9px;"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size: 11px;">—</span>
                                    <?php endif; ?>
                                </td>

                                <!-- İşlemler -->
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="<?= SITE_URL ?>/admin/orders.php?action=view&id=<?= $ord['id'] ?>" class="btn btn-sm btn-light py-0.5 px-2 border" title="Detaylı İncele">
                                            İncele
                                        </a>
                                        <button type="button" class="btn btn-sm btn-light text-danger py-0.5 px-1.5 border" data-bs-toggle="modal" data-bs-target="#delModal<?= $ord['id'] ?>" title="Sil">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div>

                                    <!-- Kargo Bilgisi Girme Modalı (ZORUNLU Takip No) -->
                                    <div class="modal fade" id="shipModal<?= $ord['id'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-sm text-start">
                                            <div class="modal-content rounded-3 border-0 shadow">
                                                <form action="<?= SITE_URL ?>/admin/orders.php?action=ship_order" method="POST">
                                                    <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                                    <input type="hidden" name="redirect_to" value="<?= SITE_URL ?>/admin/orders.php?tab=<?= urlencode($activeTab) ?>">

                                                    <div class="modal-header border-bottom py-2">
                                                        <h6 class="modal-title fw-bold text-dark"><i class="bi bi-truck me-1"></i>Kargo Bilgilerini Gir</h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body py-3">
                                                        <div class="small text-muted mb-2">
                                                            Sipariş <strong>#<?= htmlspecialchars($ord['order_number']) ?></strong> için takip numarası girmeden kargoda durumuna geçilemez.
                                                        </div>

                                                        <div class="mb-2">
                                                            <label class="form-label small fw-bold mb-1">Kargo Firması</label>
                                                            <select name="cargo_company" class="form-select form-select-sm">
                                                                <?php foreach (Cargo::getCompanies() as $key => $c): ?>
                                                                    <option value="<?= $key ?>" <?= ($ord['cargo_company'] === $key) ? 'selected' : '' ?>><?= $c['name'] ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>

                                                        <div>
                                                            <label class="form-label small fw-bold mb-1">Kargo Takip Numarası *</label>
                                                            <input type="text" name="cargo_tracking_code" class="form-control form-control-sm font-monospace" placeholder="Örn: 1234567890" value="<?= htmlspecialchars($ord['cargo_tracking_code'] ?? '') ?>" required autofocus>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top py-2">
                                                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">İptal</button>
                                                        <button type="submit" class="btn btn-sm btn-primary fw-bold">
                                                            <i class="bi bi-check2 me-1"></i> Kargoya Verildi Yap
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Silme Onay Modal -->
                                    <div class="modal fade" id="delModal<?= $ord['id'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-sm text-start">
                                            <div class="modal-content rounded-3 border-0 shadow">
                                                <div class="modal-body text-center p-4">
                                                    <i class="bi bi-trash3 text-danger fs-2 mb-2 d-block"></i>
                                                    <h6 class="fw-bold mb-1">Siparişi Sil</h6>
                                                    <p class="small text-muted mb-3"><strong>#<?= htmlspecialchars($ord['order_number']) ?></strong> numaralı sipariş silinsin mi?</p>
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <button type="button" class="btn btn-sm btn-light px-3" data-bs-dismiss="modal">Vazgeç</button>
                                                        <form action="<?= SITE_URL ?>/admin/orders.php?action=delete" method="POST" class="d-inline">
                                                            <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                                            <button type="submit" class="btn btn-sm btn-danger px-3 fw-bold">Evet, Sil</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/footer.php'; ?>
