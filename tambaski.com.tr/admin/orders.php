<?php
require_once __DIR__ . '/../config/config.php';
Auth::requireAdmin();

$orderModel = new Order();
$db = Database::getInstance()->getConnection();

$action = $_GET['action'] ?? 'list';

// 1. Vektörel SVG İndirme Aksiyonu (Operatör ve Matbaa için)
if ($action === 'download_svg') {
    $itemId = (int)($_GET['item_id'] ?? 0);
    $stmt = $db->prepare("SELECT * FROM order_items WHERE id = ?");
    $stmt->execute([$itemId]);
    $item = $stmt->fetch();

    if ($item && !empty($item['design_svg'])) {
        $filename = 'baski_vektorel_siparis_' . $item['order_id'] . '_item_' . $item['id'] . '.svg';
        header('Content-Type: image/svg+xml');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $item['design_svg'];
        exit;
    } else {
        Helper::setFlash('danger', 'Bu kaleme ait vektörel SVG tasarımı bulunamadı.');
        header("Location: " . SITE_URL . "/admin/orders.php");
        exit;
    }
}

// 2. Sipariş Silme Aksiyonu
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = (int)($_POST['order_id'] ?? 0);
    if ($orderId > 0) {
        $del = $orderModel->delete($orderId);
        if ($del) {
            Helper::setFlash('success', 'Sipariş ve bağlı tüm baskı kalemleri başarıyla silindi.');
        } else {
            Helper::setFlash('danger', 'Sipariş silinirken teknik bir hata oluştu.');
        }
    }
    header("Location: " . SITE_URL . "/admin/orders.php");
    exit;
}

// 3. Hızlı Durum Güncelleme (Tablodan veya Detaydan Tek Tıkla)
if ($action === 'quick_status' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = (int)($_POST['order_id'] ?? 0);
    $type = $_POST['type'] ?? 'order_status'; // 'order_status' | 'payment_status'
    $status = trim($_POST['status'] ?? '');

    if ($orderId > 0 && !empty($status)) {
        if ($type === 'payment_status') {
            $orderModel->updatePaymentStatus($orderId, $status);
            Helper::setFlash('success', 'Ödeme durumu başarıyla güncellendi.');
        } else {
            $orderModel->updateStatus($orderId, $status);
            Helper::setFlash('success', 'Sipariş durumu başarıyla güncellendi.');
        }
    }

    $redirect = !empty($_POST['redirect_to']) ? $_POST['redirect_to'] : (SITE_URL . "/admin/orders.php");
    header("Location: " . $redirect);
    exit;
}

// 4. Detaylı Durum ve Kargo Güncelleme Formu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
    $orderId = (int)($_POST['order_id'] ?? 0);
    $orderStatus = $_POST['order_status'] ?? 'pending_payment';
    $paymentStatus = $_POST['payment_status'] ?? 'pending';
    $cargoCompany = trim($_POST['cargo_company'] ?? '');
    $cargoTracking = trim($_POST['cargo_tracking_code'] ?? '');

    $orderModel->updateStatus($orderId, $orderStatus);
    $orderModel->updatePaymentStatus($orderId, $paymentStatus);
    if (!empty($cargoTracking)) {
        $orderModel->updateCargo($orderId, $cargoCompany, $cargoTracking);
    }

    Helper::setFlash('success', 'Sipariş durumu ve kargo bilgileri başarıyla kaydedildi.');
    header("Location: " . SITE_URL . "/admin/orders.php?action=view&id=" . $orderId);
    exit;
}

$pageTitle = 'Sipariş ve Üretim Yönetimi';
require_once __DIR__ . '/header.php';

// =========================================================================
// GÖRÜNÜM 1: SİPARİŞ DETAY EKRANI (action=view)
// =========================================================================
if ($action === 'view'):
    $orderId = (int)($_GET['id'] ?? 0);
    $order = $orderModel->getById($orderId);
    if (!$order) {
        echo '<div class="alert alert-danger rounded-4 p-4 my-4"><i class="bi bi-exclamation-triangle-fill me-2"></i>Sipariş bulunamadı veya silinmiş.</div>';
        require_once __DIR__ . '/footer.php';
        exit;
    }
?>
    <!-- Üst Başlık & Hızlı Navigasyon -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <a href="<?= SITE_URL ?>/admin/orders.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3 mb-2">
                <i class="bi bi-arrow-left me-1"></i> Sipariş Listesine Dön
            </a>
            <div class="d-flex align-items-center gap-3">
                <h3 class="fw-bold mb-0 font-monospace text-dark">#<?= htmlspecialchars($order['order_number']) ?></h3>
                <span class="text-muted small"><i class="bi bi-calendar3 me-1"></i><?= date('d.m.Y - H:i', strtotime($order['created_at'])) ?></span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-dark rounded-pill px-3 btn-sm fw-bold shadow-2xs" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Yazdır / Fiş
            </button>
            <span class="fs-6"><?= Helper::getPaymentStatusBadge($order['payment_status']) ?></span>
            <span class="fs-6"><?= Helper::getOrderStatusBadge($order['order_status']) ?></span>
            
            <!-- Siparişi Sil Butonu -->
            <button type="button" class="btn btn-outline-danger rounded-pill px-3 btn-sm fw-bold shadow-2xs" data-bs-toggle="modal" data-bs-target="#deleteOrderModal">
                <i class="bi bi-trash3 me-1"></i> Siparişi Sil
            </button>
        </div>
    </div>

    <!-- Hızlı Eylem Çubuğu -->
    <div class="p-3 bg-white rounded-4 border shadow-2xs mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <span class="small fw-bold text-muted"><i class="bi bi-lightning-charge-fill text-warning me-1"></i>Hızlı İşlemler:</span>
            <?php if ($order['payment_status'] !== 'paid'): ?>
                <form action="<?= SITE_URL ?>/admin/orders.php?action=quick_status" method="POST" class="d-inline">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <input type="hidden" name="type" value="payment_status">
                    <input type="hidden" name="status" value="paid">
                    <input type="hidden" name="redirect_to" value="<?= SITE_URL ?>/admin/orders.php?action=view&id=<?= $order['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-bold">
                        <i class="bi bi-check2-circle me-1"></i> Ödemeyi "Ödendi" Yap
                    </button>
                </form>
            <?php endif; ?>

            <?php if ($order['order_status'] !== 'in_production'): ?>
                <form action="<?= SITE_URL ?>/admin/orders.php?action=quick_status" method="POST" class="d-inline">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <input type="hidden" name="type" value="order_status">
                    <input type="hidden" name="status" value="in_production">
                    <input type="hidden" name="redirect_to" value="<?= SITE_URL ?>/admin/orders.php?action=view&id=<?= $order['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">
                        <i class="bi bi-printer me-1"></i> "Baskıya Alındı" Yap
                    </button>
                </form>
            <?php endif; ?>

            <?php if ($order['order_status'] !== 'delivered'): ?>
                <form action="<?= SITE_URL ?>/admin/orders.php?action=quick_status" method="POST" class="d-inline">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <input type="hidden" name="type" value="order_status">
                    <input type="hidden" name="status" value="delivered">
                    <input type="hidden" name="redirect_to" value="<?= SITE_URL ?>/admin/orders.php?action=view&id=<?= $order['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold">
                        <i class="bi bi-box2-heart me-1"></i> "Teslim Edildi" Yap
                    </button>
                </form>
            <?php endif; ?>

            <?php if ($order['order_status'] !== 'cancelled'): ?>
                <form action="<?= SITE_URL ?>/admin/orders.php?action=quick_status" method="POST" class="d-inline" onsubmit="return confirm('Bu siparişi iptal etmek istediğinize emin misiniz?');">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <input type="hidden" name="type" value="order_status">
                    <input type="hidden" name="status" value="cancelled">
                    <input type="hidden" name="redirect_to" value="<?= SITE_URL ?>/admin/orders.php?action=view&id=<?= $order['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold">
                        <i class="bi bi-x-circle me-1"></i> İptal Et
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <div>
            <span class="badge bg-light text-dark border px-3 py-2 fs-6">
                Ödeme Yöntemi: <strong class="text-uppercase"><?= htmlspecialchars($order['payment_method']) ?></strong>
                <?php if (!empty($order['payment_transaction_id'])): ?>
                    <span class="text-muted ms-1">(Pos Ref: <?= htmlspecialchars($order['payment_transaction_id']) ?>)</span>
                <?php endif; ?>
            </span>
        </div>
    </div>

    <div class="row g-4">
        
        <!-- Sol Kolon: Baskı Kalemleri & Tasarım Dosyaları -->
        <div class="col-lg-8">
            <div class="apple-card p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-layers text-primary me-2"></i>Baskı Kalemleri & Tasarım Dosyaları</h5>
                    <span class="badge bg-light text-secondary border"><?= count($order['items']) ?> Kalem</span>
                </div>
                
                <?php foreach ($order['items'] as $item): 
                    // Yanlış kaydedilmiş hatalı Özel Boyut etiketlerini filtrele (Paket seçiliyken çıkan 8.4x5.2 gibi)
                    $cleanOptions = [];
                    $hasPackage = false;
                    if (!empty($item['options_array'])) {
                        foreach ($item['options_array'] as $optStr) {
                            if (stripos($optStr, 'Paket:') !== false) {
                                $hasPackage = true;
                            }
                        }
                        foreach ($item['options_array'] as $optStr) {
                            // Eğer standart paket zaten seçiliyse ve 8.4x5.2 veya default kartvizit boyutu varsa gizle
                            if ($hasPackage && stripos($optStr, 'Özel Boyut:') !== false && (stripos($optStr, '8.4') !== false || stripos($optStr, '5.2') !== false)) {
                                continue;
                            }
                            $cleanOptions[] = $optStr;
                        }
                    }
                ?>
                    <div class="p-3 bg-light rounded-4 border mb-3">
                        <div class="row align-items-center g-3">
                            <!-- Önizleme ve İndirme Butonu -->
                            <div class="col-md-3 text-center">
                                <?php if (!empty($item['design_svg'])): ?>
                                    <div class="border rounded-3 p-1 bg-white shadow-2xs mb-2 d-flex align-items-center justify-content-center" style="height: 100px; overflow: hidden;">
                                        <?= $item['design_svg'] ?>
                                    </div>
                                    <a href="<?= SITE_URL ?>/admin/orders.php?action=download_svg&item_id=<?= $item['id'] ?>" class="btn btn-sm btn-primary w-100 py-1.5 fw-bold shadow-2xs rounded-pill">
                                        <i class="bi bi-download me-1"></i> Vektörel SVG İndir
                                    </a>
                                <?php elseif (!empty($item['design_file'])): ?>
                                    <div class="p-3 bg-white rounded-3 border mb-2 text-center">
                                        <i class="bi bi-file-earmark-arrow-up text-primary fs-2 d-block mb-1"></i>
                                        <span class="badge bg-light text-dark border small"><?= strtoupper(pathinfo($item['design_file'], PATHINFO_EXTENSION)) ?></span>
                                    </div>
                                    <a href="<?= SITE_URL . '/' . htmlspecialchars($item['design_file']) ?>" target="_blank" class="btn btn-sm btn-dark w-100 py-1.5 fw-bold shadow-2xs rounded-pill">
                                        <i class="bi bi-cloud-arrow-down me-1"></i> Dosyayı İndir
                                    </a>
                                <?php else: ?>
                                    <div class="p-3 bg-white rounded-3 border text-center text-muted">
                                        <i class="bi bi-card-image fs-2 d-block mb-1 opacity-50"></i>
                                        <span class="badge bg-secondary">Dosya Yok</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Kalem Bilgileri -->
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($item['product_name']) ?></h6>
                                <div class="badge bg-primary text-white rounded-pill px-3 py-1 mb-2">
                                    Adet: <?= number_format($item['quantity'], 0, '', '.') ?> Adet
                                </div>

                                <?php if (!empty($cleanOptions)): ?>
                                    <div class="small bg-white p-2.5 rounded-3 border text-dark mb-2 lh-base">
                                        <?php foreach ($cleanOptions as $optLine): ?>
                                            <div><i class="bi bi-check2 text-primary me-1"></i><?= htmlspecialchars($optLine) ?></div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($item['design_notes'])): ?>
                                    <div class="p-2.5 bg-warning-subtle text-dark rounded-3 border border-warning small mt-2">
                                        <strong><i class="bi bi-chat-left-dots me-1"></i>Müşteri Notu:</strong> <?= nl2br(htmlspecialchars($item['design_notes'])) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Fiyat Kolonu -->
                            <div class="col-md-3 text-md-end">
                                <div class="fw-bold text-primary fs-4"><?= Helper::formatPrice($item['total_price']) ?></div>
                                <small class="text-muted d-block">Birim: <?= Helper::formatPrice($item['unit_price']) ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Finansal Toplamlar Özeti -->
                <div class="text-end pt-3 border-top">
                    <div class="row justify-content-end">
                        <div class="col-md-5">
                            <div class="d-flex justify-content-between text-muted small mb-1">
                                <span>Ara Toplam:</span>
                                <strong class="text-dark"><?= Helper::formatPrice($order['subtotal']) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between text-muted small mb-1">
                                <span>KDV (%20):</span>
                                <strong class="text-dark"><?= Helper::formatPrice($order['tax_amount']) ?></strong>
                            </div>
                            <?php if ($order['discount_amount'] > 0): ?>
                                <div class="d-flex justify-content-between text-success small mb-1">
                                    <span>İndirim Tutarı:</span>
                                    <strong>-<?= Helper::formatPrice($order['discount_amount']) ?></strong>
                                </div>
                            <?php endif; ?>
                            <div class="d-flex justify-content-between text-muted small mb-2">
                                <span>Kargo & Teslimat:</span>
                                <strong class="text-dark"><?= Helper::formatPrice($order['shipping_fee']) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between fs-5 fw-bold text-dark pt-2 border-top">
                                <span>Genel Toplam:</span>
                                <span class="text-primary"><?= Helper::formatPrice($order['total_amount']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Adres ve Müşteri Kartı -->
            <div class="apple-card p-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2 text-dark"><i class="bi bi-person-lines-fill text-primary me-2"></i>Müşteri & Teslimat Bilgileri</h5>
                <div class="row g-4 small">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-4 border h-100">
                            <h6 class="fw-bold text-muted small mb-3 text-uppercase">İletişim Bilgileri</h6>
                            <div class="mb-2">
                                <span class="text-muted d-block" style="font-size: 11px;">Müşteri Adı:</span>
                                <strong class="text-dark fs-6"><?= htmlspecialchars($order['customer_name']) ?></strong>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted d-block" style="font-size: 11px;">Telefon:</span>
                                <a href="tel:<?= htmlspecialchars($order['customer_phone']) ?>" class="fw-bold text-decoration-none">
                                    <i class="bi bi-telephone text-primary me-1"></i><?= htmlspecialchars($order['customer_phone']) ?>
                                </a>
                                <a href="https://wa.me/90<?= preg_replace('/[^0-9]/', '', $order['customer_phone']) ?>" target="_blank" class="badge bg-success text-white ms-2 text-decoration-none py-1">
                                    <i class="bi bi-whatsapp"></i> WhatsApp
                                </a>
                            </div>
                            <div>
                                <span class="text-muted d-block" style="font-size: 11px;">E-Posta:</span>
                                <a href="mailto:<?= htmlspecialchars($order['customer_email']) ?>" class="text-dark text-decoration-none">
                                    <i class="bi bi-envelope text-primary me-1"></i><?= htmlspecialchars($order['customer_email']) ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-4 border h-100">
                            <h6 class="fw-bold text-muted small mb-3 text-uppercase">Teslimat & Fatura</h6>
                            <div class="mb-2">
                                <span class="text-muted d-block" style="font-size: 11px;">Teslimat Adresi:</span>
                                <div class="fw-bold text-dark"><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></div>
                                <div class="text-primary fw-bold mt-1"><?= htmlspecialchars($order['shipping_district']) ?> / <?= htmlspecialchars($order['shipping_city']) ?></div>
                            </div>
                            <?php if ($order['billing_type'] === 'corporate'): ?>
                                <div class="mt-3 p-2.5 bg-white rounded-3 border">
                                    <div class="badge bg-dark mb-1">Kurumsal Fatura</div>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($order['billing_company']) ?></div>
                                    <div class="text-muted small">Vergi No: <?= htmlspecialchars($order['tax_number']) ?> (<?= htmlspecialchars($order['tax_office']) ?>)</div>
                                </div>
                            <?php else: ?>
                                <div class="text-muted small mt-2"><i class="bi bi-person me-1"></i>Bireysel Fatura</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sağ Kolon: Durum & Kargo Güncelleme Formu -->
        <div class="col-lg-4">
            <div class="apple-card p-4 sticky-top shadow-sm" style="top: 20px;">
                <h5 class="fw-bold mb-3 border-bottom pb-2 text-dark"><i class="bi bi-gear text-primary me-2"></i>Durum ve Kargo İşlemleri</h5>
                
                <form action="<?= SITE_URL ?>/admin/orders.php?action=update" method="POST">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Sipariş / Üretim Durumu</label>
                        <select name="order_status" class="form-select rounded-3 py-2 fw-bold text-dark">
                            <option value="pending_payment" <?= $order['order_status'] === 'pending_payment' ? 'selected' : '' ?>>⏳ Ödeme Bekleniyor</option>
                            <option value="payment_received" <?= $order['order_status'] === 'payment_received' ? 'selected' : '' ?>>✅ Ödeme Alındı</option>
                            <option value="design_approval" <?= $order['order_status'] === 'design_approval' ? 'selected' : '' ?>>🎨 Tasarım Onayında</option>
                            <option value="preparing" <?= $order['order_status'] === 'preparing' ? 'selected' : '' ?>>⚙️ Hazırlanıyor</option>
                            <option value="in_production" <?= $order['order_status'] === 'in_production' ? 'selected' : '' ?>>🏭 Baskıda / Üretimde</option>
                            <option value="packaged" <?= $order['order_status'] === 'packaged' ? 'selected' : '' ?>>📦 Paketlendi</option>
                            <option value="shipped" <?= $order['order_status'] === 'shipped' ? 'selected' : '' ?>>🚚 Kargoya Verildi</option>
                            <option value="delivered" <?= $order['order_status'] === 'delivered' ? 'selected' : '' ?>>🎉 Teslim Edildi</option>
                            <option value="cancelled" <?= $order['order_status'] === 'cancelled' ? 'selected' : '' ?>>❌ İptal Edildi</option>
                            <option value="refunded" <?= $order['order_status'] === 'refunded' ? 'selected' : '' ?>>↩️ İade Edildi</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Ödeme Durumu</label>
                        <select name="payment_status" class="form-select rounded-3 py-2 fw-bold text-dark">
                            <option value="pending" <?= $order['payment_status'] === 'pending' ? 'selected' : '' ?>>⏳ Ödeme Bekliyor</option>
                            <option value="paid" <?= $order['payment_status'] === 'paid' ? 'selected' : '' ?>>✅ Ödendi (Tahsil Edildi)</option>
                            <option value="failed" <?= $order['payment_status'] === 'failed' ? 'selected' : '' ?>>⚠️ Başarısız</option>
                            <option value="refunded" <?= $order['payment_status'] === 'refunded' ? 'selected' : '' ?>>↩️ İade Edildi</option>
                        </select>
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-bold mb-3 small text-primary"><i class="bi bi-truck me-1"></i> Kargo Gönderi Bilgileri</h6>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kargo Firması</label>
                        <select name="cargo_company" class="form-select rounded-3">
                            <option value="">Kargo Firması Seçin</option>
                            <?php foreach (Cargo::getCompanies() as $key => $c): ?>
                                <option value="<?= $key ?>" <?= ($order['cargo_company'] === $key) ? 'selected' : '' ?>><?= $c['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Kargo Takip No</label>
                        <input type="text" name="cargo_tracking_code" class="form-control rounded-3" placeholder="Örn: 123456789012" value="<?= htmlspecialchars($order['cargo_tracking_code'] ?? '') ?>">
                        <?php if (!empty($order['cargo_tracking_code'])): ?>
                            <a href="<?= Cargo::getTrackingLink($order['cargo_company'], $order['cargo_tracking_code']) ?>" target="_blank" class="small mt-2 d-inline-block text-decoration-none fw-bold text-primary">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Kargo Sitesinde Takip Et &rarr;
                            </a>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow">
                        <i class="bi bi-save me-1"></i> Değişiklikleri Kaydet
                    </button>
                </form>

            </div>
        </div>

    </div>

    <!-- Sipariş Silme Onay Modal'ı -->
    <div class="modal fade" id="deleteOrderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Siparişi Kalıcı Olarak Sil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>
                <div class="modal-body py-4">
                    <p class="mb-2"><strong>#<?= htmlspecialchars($order['order_number']) ?></strong> numaralı siparişi ve bu siparişe ait tüm baskı kalemlerini silmek üzeresiniz.</p>
                    <p class="text-danger small mb-0"><strong>Uyarı:</strong> Bu işlem geri alınamaz!</p>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Vazgeç</button>
                    <form action="<?= SITE_URL ?>/admin/orders.php?action=delete" method="POST" class="d-inline">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">
                            <i class="bi bi-trash3 me-1"></i> Evet, Kalıcı Olarak Sil
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php 
// =========================================================================
// GÖRÜNÜM 2: SİPARİŞ LİSTESİ (action=list)
// =========================================================================
else: 
    $statusFilter = $_GET['status'] ?? null;
    $paymentFilter = $_GET['payment_status'] ?? null;
    $search = $_GET['search'] ?? null;
    $activeTab = $_GET['tab'] ?? 'all';

    // Tab mantığına göre filtreleri ayarla
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
    <!-- Üst Başlık -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-dark"><i class="bi bi-box-seam text-primary me-2"></i>Sipariş ve Üretim Yönetimi</h4>
            <p class="text-muted small mb-0">Tüm baskı siparişlerinizi, ödeme durumlarını, üretim aşamalarını ve kargo takiplerini buradan yönetin.</p>
        </div>
    </div>

    <!-- 📊 KPI İstatistik Kartları -->
    <div class="row g-3 mb-4">
        <!-- Toplam Sipariş -->
        <div class="col-6 col-lg-3">
            <div class="apple-card p-3 h-100 d-flex align-items-center gap-3">
                <div class="rounded-4 bg-primary-subtle text-primary p-3 fs-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                    <i class="bi bi-receipt"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">Toplam Sipariş</span>
                    <h4 class="fw-bold mb-0 text-dark"><?= number_format($stats['total_orders'], 0, '', '.') ?></h4>
                </div>
            </div>
        </div>

        <!-- Başarılı Ödemeler / Ciro -->
        <div class="col-6 col-lg-3">
            <div class="apple-card p-3 h-100 d-flex align-items-center gap-3">
                <div class="rounded-4 bg-success-subtle text-success p-3 fs-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">Ödenen Ciro (<?= $stats['paid_orders'] ?> Sipariş)</span>
                    <h4 class="fw-bold mb-0 text-success"><?= Helper::formatPrice($stats['paid_sum']) ?></h4>
                </div>
            </div>
        </div>

        <!-- Ödeme Bekleyenler -->
        <div class="col-6 col-lg-3">
            <div class="apple-card p-3 h-100 d-flex align-items-center gap-3">
                <div class="rounded-4 bg-warning-subtle text-warning p-3 fs-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">Ödeme Bekleyenler</span>
                    <h4 class="fw-bold mb-0 text-dark"><?= number_format($stats['pending_payment'], 0, '', '.') ?></h4>
                </div>
            </div>
        </div>

        <!-- Baskı ve Üretimde Olanlar -->
        <div class="col-6 col-lg-3">
            <div class="apple-card p-3 h-100 d-flex align-items-center gap-3">
                <div class="rounded-4 bg-info-subtle text-info p-3 fs-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                    <i class="bi bi-printer-fill"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">Baskı / Hazırlıkta</span>
                    <h4 class="fw-bold mb-0 text-primary"><?= number_format($stats['in_production'], 0, '', '.') ?></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- 📑 Hızlı Filtre Sekmeleri (Apple Tabs) -->
    <div class="apple-card p-2 mb-4">
        <ul class="nav nav-pills flex-nowrap overflow-auto gap-2" style="white-space: nowrap;">
            <li class="nav-item">
                <a class="nav-link rounded-pill px-3 py-2 fw-bold <?= ($activeTab === 'all' && empty($statusFilter) && empty($paymentFilter)) ? 'active' : '' ?>" href="<?= SITE_URL ?>/admin/orders.php?tab=all">
                    Tümü <span class="badge bg-secondary ms-1 rounded-pill"><?= $stats['total_orders'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-3 py-2 fw-bold <?= ($activeTab === 'paid') ? 'active bg-success text-white' : 'text-success' ?>" href="<?= SITE_URL ?>/admin/orders.php?tab=paid">
                    <i class="bi bi-check2-circle me-1"></i>Ödemesi Yapılanlar <span class="badge bg-success-subtle text-success ms-1 rounded-pill"><?= $stats['paid_orders'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-3 py-2 fw-bold <?= ($activeTab === 'pending_payment') ? 'active bg-warning text-dark' : 'text-warning-emphasis' ?>" href="<?= SITE_URL ?>/admin/orders.php?tab=pending_payment">
                    <i class="bi bi-hourglass-split me-1"></i>Ödeme Bekleyenler <span class="badge bg-warning-subtle text-dark ms-1 rounded-pill"><?= $stats['pending_payment'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-3 py-2 fw-bold <?= ($activeTab === 'in_production') ? 'active bg-primary text-white' : 'text-primary' ?>" href="<?= SITE_URL ?>/admin/orders.php?tab=in_production">
                    <i class="bi bi-printer me-1"></i>Baskıda / Hazırlanıyor <span class="badge bg-primary-subtle text-primary ms-1 rounded-pill"><?= $stats['in_production'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-3 py-2 fw-bold <?= ($activeTab === 'shipped') ? 'active' : '' ?>" href="<?= SITE_URL ?>/admin/orders.php?tab=shipped">
                    <i class="bi bi-truck me-1"></i>Kargodakiler <span class="badge bg-secondary ms-1 rounded-pill"><?= $stats['shipped'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-3 py-2 fw-bold <?= ($activeTab === 'delivered') ? 'active' : '' ?>" href="<?= SITE_URL ?>/admin/orders.php?tab=delivered">
                    <i class="bi bi-box2-heart me-1"></i>Teslim Edilenler <span class="badge bg-secondary ms-1 rounded-pill"><?= $stats['delivered'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-3 py-2 fw-bold <?= ($activeTab === 'cancelled') ? 'active bg-danger text-white' : 'text-danger' ?>" href="<?= SITE_URL ?>/admin/orders.php?tab=cancelled">
                    <i class="bi bi-x-circle me-1"></i>İptal / İade <span class="badge bg-danger-subtle text-danger ms-1 rounded-pill"><?= $stats['cancelled'] ?></span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Arama ve Ekstra Filtreler -->
    <div class="apple-card p-3 mb-4">
        <form action="<?= SITE_URL ?>/admin/orders.php" method="GET" class="row g-2 align-items-center">
            <input type="hidden" name="tab" value="<?= htmlspecialchars($activeTab) ?>">
            
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Sipariş No (Örn: BM2609...), Müşteri Adı, Telefon veya E-Posta..." value="<?= htmlspecialchars($search ?? '') ?>">
                </div>
            </div>

            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Tüm Sipariş Durumları</option>
                    <option value="pending_payment" <?= $statusFilter === 'pending_payment' ? 'selected' : '' ?>>⏳ Ödeme Bekleniyor</option>
                    <option value="payment_received" <?= $statusFilter === 'payment_received' ? 'selected' : '' ?>>✅ Ödeme Alındı</option>
                    <option value="design_approval" <?= $statusFilter === 'design_approval' ? 'selected' : '' ?>>🎨 Tasarım Onayında</option>
                    <option value="preparing" <?= $statusFilter === 'preparing' ? 'selected' : '' ?>>⚙️ Hazırlanıyor</option>
                    <option value="in_production" <?= $statusFilter === 'in_production' ? 'selected' : '' ?>>🏭 Baskıda / Üretimde</option>
                    <option value="packaged" <?= $statusFilter === 'packaged' ? 'selected' : '' ?>>📦 Paketlendi</option>
                    <option value="shipped" <?= $statusFilter === 'shipped' ? 'selected' : '' ?>>🚚 Kargoya Verildi</option>
                    <option value="delivered" <?= $statusFilter === 'delivered' ? 'selected' : '' ?>>🎉 Teslim Edildi</option>
                    <option value="cancelled" <?= $statusFilter === 'cancelled' ? 'selected' : '' ?>>❌ İptal Edildi</option>
                </select>
            </div>

            <div class="col-md-2">
                <select name="payment_status" class="form-select">
                    <option value="">Tüm Ödemeler</option>
                    <option value="paid" <?= $paymentFilter === 'paid' ? 'selected' : '' ?>>✅ Ödendi</option>
                    <option value="pending" <?= $paymentFilter === 'pending' ? 'selected' : '' ?>>⏳ Bekliyor</option>
                    <option value="failed" <?= $paymentFilter === 'failed' ? 'selected' : '' ?>>⚠️ Başarısız</option>
                    <option value="refunded" <?= $paymentFilter === 'refunded' ? 'selected' : '' ?>>↩️ İade Edildi</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100 fw-bold"><i class="bi bi-filter"></i> Filtrele</button>
                <?php if (!empty($search) || !empty($statusFilter) || !empty($paymentFilter) || $activeTab !== 'all'): ?>
                    <a href="<?= SITE_URL ?>/admin/orders.php" class="btn btn-outline-secondary" title="Sıfırla"><i class="bi bi-x-lg"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- 📦 Sipariş Tablosu -->
    <div class="apple-card p-4">
        <?php if (empty($orders)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-3 text-secondary opacity-50"></i>
                <h5 class="fw-bold text-dark">Kriterlere Uygun Sipariş Bulunamadı</h5>
                <p class="small text-muted mb-3">Arama filtrenizi temizleyebilir veya diğer sekmelere göz atabilirsiniz.</p>
                <a href="<?= SITE_URL ?>/admin/orders.php" class="btn btn-sm btn-primary rounded-pill px-4">Tüm Siparişleri Göster</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width: 130px;">Sipariş No / Tarih</th>
                            <th style="min-width: 160px;">Müşteri Bilgisi</th>
                            <th style="min-width: 140px;">Baskı Kalemleri</th>
                            <th style="min-width: 110px;">Toplam Tutar</th>
                            <th style="min-width: 130px;">Ödeme Durumu</th>
                            <th style="min-width: 140px;">Sipariş / Üretim</th>
                            <th style="min-width: 130px;">Kargo Durumu</th>
                            <th class="text-end" style="min-width: 130px;">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $ord): 
                            $items = $orderModel->getOrderItems($ord['id']);
                        ?>
                            <tr>
                                <!-- Sipariş No ve Tarih -->
                                <td>
                                    <a href="<?= SITE_URL ?>/admin/orders.php?action=view&id=<?= $ord['id'] ?>" class="fw-bold font-monospace text-primary text-decoration-none fs-6 d-block">
                                        #<?= htmlspecialchars($ord['order_number']) ?>
                                    </a>
                                    <span class="text-muted" style="font-size: 11px;">
                                        <i class="bi bi-clock me-1"></i><?= date('d.m.Y H:i', strtotime($ord['created_at'])) ?>
                                    </span>
                                </td>

                                <!-- Müşteri Bilgisi -->
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($ord['customer_name']) ?></div>
                                    <div class="text-muted" style="font-size: 11px;">
                                        <i class="bi bi-telephone text-primary me-1"></i><?= htmlspecialchars($ord['customer_phone']) ?>
                                    </div>
                                    <div class="text-muted text-truncate" style="font-size: 11px; max-width: 150px;">
                                        <?= htmlspecialchars($ord['shipping_district'] . ' / ' . $ord['shipping_city']) ?>
                                    </div>
                                </td>

                                <!-- Kalemler ve Vektör Dosyaları -->
                                <td>
                                    <div class="small fw-semibold text-dark mb-1">
                                        <?= count($items) ?> Kalem
                                    </div>
                                    <?php foreach ($items as $it): ?>
                                        <div class="text-truncate mb-1 text-muted" style="max-width: 180px; font-size: 11px;" title="<?= htmlspecialchars($it['product_name']) ?>">
                                            • <?= htmlspecialchars($it['product_name']) ?> (<?= $it['quantity'] ?> Adet)
                                        </div>
                                        <?php if (!empty($it['design_svg'])): ?>
                                            <a href="<?= SITE_URL ?>/admin/orders.php?action=download_svg&item_id=<?= $it['id'] ?>" class="badge bg-primary text-white text-decoration-none py-1 px-2 d-inline-block mb-1 shadow-2xs">
                                                <i class="bi bi-download me-1"></i> SVG İndir
                                            </a>
                                        <?php elseif (!empty($it['design_file'])): ?>
                                            <a href="<?= SITE_URL . '/' . htmlspecialchars($it['design_file']) ?>" target="_blank" class="badge bg-dark text-white text-decoration-none py-1 px-2 d-inline-block mb-1 shadow-2xs">
                                                <i class="bi bi-cloud-arrow-down me-1"></i> Dosyayı İndir
                                            </a>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </td>

                                <!-- Tutar ve Yöntem -->
                                <td>
                                    <div class="fw-bold text-primary fs-6"><?= Helper::formatPrice($ord['total_amount']) ?></div>
                                    <span class="badge bg-light text-dark border" style="font-size: 10px;"><?= strtoupper($ord['payment_method']) ?></span>
                                </td>

                                <!-- Ödeme Durumu (Hızlı Değiştirme Açılır Menüsü) -->
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm p-0 border-0 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <?= Helper::getPaymentStatusBadge($ord['payment_status']) ?>
                                        </button>
                                        <ul class="dropdown-menu shadow border-0 small">
                                            <li class="dropdown-header small text-muted">Ödeme Durumunu Değiştir</li>
                                            <li>
                                                <form action="<?= SITE_URL ?>/admin/orders.php?action=quick_status" method="POST">
                                                    <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                                    <input type="hidden" name="type" value="payment_status">
                                                    <input type="hidden" name="status" value="paid">
                                                    <button type="submit" class="dropdown-item text-success"><i class="bi bi-check2-circle me-1"></i> Ödendi</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="<?= SITE_URL ?>/admin/orders.php?action=quick_status" method="POST">
                                                    <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                                    <input type="hidden" name="type" value="payment_status">
                                                    <input type="hidden" name="status" value="pending">
                                                    <button type="submit" class="dropdown-item text-warning"><i class="bi bi-hourglass-split me-1"></i> Ödeme Bekliyor</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="<?= SITE_URL ?>/admin/orders.php?action=quick_status" method="POST">
                                                    <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                                    <input type="hidden" name="type" value="payment_status">
                                                    <input type="hidden" name="status" value="refunded">
                                                    <button type="submit" class="dropdown-item text-dark"><i class="bi bi-arrow-return-left me-1"></i> İade Edildi</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>

                                <!-- Sipariş Durumu (Hızlı Değiştirme Açılır Menüsü) -->
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm p-0 border-0 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <?= Helper::getOrderStatusBadge($ord['order_status']) ?>
                                        </button>
                                        <ul class="dropdown-menu shadow border-0 small">
                                            <li class="dropdown-header small text-muted">Aşamayı Değiştir</li>
                                            <li>
                                                <form action="<?= SITE_URL ?>/admin/orders.php?action=quick_status" method="POST">
                                                    <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                                    <input type="hidden" name="type" value="order_status">
                                                    <input type="hidden" name="status" value="payment_received">
                                                    <button type="submit" class="dropdown-item">✅ Ödeme Alındı</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="<?= SITE_URL ?>/admin/orders.php?action=quick_status" method="POST">
                                                    <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                                    <input type="hidden" name="type" value="order_status">
                                                    <input type="hidden" name="status" value="in_production">
                                                    <button type="submit" class="dropdown-item text-primary">🏭 Baskıda / Üretimde</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="<?= SITE_URL ?>/admin/orders.php?action=quick_status" method="POST">
                                                    <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                                    <input type="hidden" name="type" value="order_status">
                                                    <input type="hidden" name="status" value="shipped">
                                                    <button type="submit" class="dropdown-item text-indigo">🚚 Kargoya Verildi</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="<?= SITE_URL ?>/admin/orders.php?action=quick_status" method="POST">
                                                    <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                                    <input type="hidden" name="type" value="order_status">
                                                    <input type="hidden" name="status" value="delivered">
                                                    <button type="submit" class="dropdown-item text-success">🎉 Teslim Edildi</button>
                                                </form>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="<?= SITE_URL ?>/admin/orders.php?action=quick_status" method="POST">
                                                    <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                                    <input type="hidden" name="type" value="order_status">
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <button type="submit" class="dropdown-item text-danger">❌ İptal Edildi</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>

                                <!-- Kargo Takip -->
                                <td>
                                    <?php if (!empty($ord['cargo_tracking_code'])): ?>
                                        <div class="fw-bold text-dark" style="font-size: 11px;"><?= htmlspecialchars($ord['cargo_company'] ?: 'Kargo') ?></div>
                                        <a href="<?= Cargo::getTrackingLink($ord['cargo_company'], $ord['cargo_tracking_code']) ?>" target="_blank" class="text-primary text-decoration-none small d-block font-monospace" title="Kargo Takip">
                                            <?= htmlspecialchars($ord['cargo_tracking_code']) ?> <i class="bi bi-box-arrow-up-right" style="font-size: 10px;"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size: 11px;">Henüz Girilmedi</span>
                                    <?php endif; ?>
                                </td>

                                <!-- İşlemler Butonları -->
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <a href="<?= SITE_URL ?>/admin/orders.php?action=view&id=<?= $ord['id'] ?>" class="btn btn-sm btn-apple-secondary py-1 px-2.5 rounded-pill fw-bold" title="Detay / Yönet">
                                            <i class="bi bi-eye"></i> İncele
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 rounded-circle" data-bs-toggle="modal" data-bs-target="#delModal<?= $ord['id'] ?>" title="Siparişi Sil">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div>

                                    <!-- Satır Bazlı Silme Modal'ı -->
                                    <div class="modal fade" id="delModal<?= $ord['id'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content rounded-4 border-0 shadow text-start">
                                                <div class="modal-header border-bottom-0 pb-0">
                                                    <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Siparişi Kalıcı Olarak Sil</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body py-4">
                                                    <p class="mb-1"><strong>#<?= htmlspecialchars($ord['order_number']) ?></strong> numaralı siparişi silmek istediğinize emin misiniz?</p>
                                                    <p class="text-muted small mb-0">Müşteri: <strong><?= htmlspecialchars($ord['customer_name']) ?></strong> | Tutar: <strong><?= Helper::formatPrice($ord['total_amount']) ?></strong></p>
                                                </div>
                                                <div class="modal-footer border-top-0 pt-0">
                                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Vazgeç</button>
                                                    <form action="<?= SITE_URL ?>/admin/orders.php?action=delete" method="POST" class="d-inline">
                                                        <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">
                                                            <i class="bi bi-trash3 me-1"></i> Evet, Sil
                                                        </button>
                                                    </form>
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
