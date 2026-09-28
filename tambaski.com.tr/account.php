<?php
require_once __DIR__ . '/config/config.php';
Auth::requireLogin();

$user = Auth::user();
$dbConn = Database::getInstance()->getConnection();

// Kullanıcının siparişleri
$stmt = $dbConn->prepare("SELECT * FROM orders WHERE user_id = ? OR customer_email = ? ORDER BY id DESC");
$stmt->execute([$user['id'], $user['email']]);
$userOrders = $stmt->fetchAll();

$pageTitle = 'Hesabım & Siparişlerim – TamBaskı';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="row g-4">
        
        <!-- Sol Menü -->
        <div class="col-lg-3">
            <div class="apple-card p-4 text-center mb-3">
                <i class="bi bi-person-circle text-primary" style="font-size: 54px;"></i>
                <h5 class="fw-bold mt-2 mb-1"><?= htmlspecialchars($user['full_name']) ?></h5>
                <span class="badge <?= $user['role'] === 'dealer' ? 'bg-primary' : 'bg-secondary' ?> mb-2">
                    <?= $user['role'] === 'dealer' ? 'E-Bayi (%' . (int)$user['discount_rate'] . ' İskonto)' : 'Bireysel Üye' ?>
                </span>
                <p class="text-muted small mb-0"><?= htmlspecialchars($user['email']) ?></p>
            </div>

            <div class="list-group list-group-flush apple-card p-2 small">
                <a href="<?= SITE_URL ?>/account.php" class="list-group-item list-group-item-action active rounded-3 mb-1"><i class="bi bi-bag-check me-2"></i> Siparişlerim</a>
                <a href="<?= SITE_URL ?>/order_tracking.php" class="list-group-item list-group-item-action rounded-3 mb-1"><i class="bi bi-truck me-2"></i> Kargo Takip</a>
                <a href="<?= SITE_URL ?>/logout.php" class="list-group-item list-group-item-action rounded-3 text-danger"><i class="bi bi-box-arrow-right me-2"></i> Çıkış Yap</a>
            </div>
        </div>

        <!-- Sağ İçerik (Siparişler) -->
        <div class="col-lg-9">
            <div class="apple-card p-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Geçmiş Siparişlerim (<?= count($userOrders) ?>)</h5>

                <?php if (empty($userOrders)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-bag-x fs-1 d-block mb-2"></i>
                        <p class="mb-2">Henüz verilmiş bir siparişiniz bulunmuyor.</p>
                        <a href="<?= SITE_URL ?>/category.php" class="btn btn-sm btn-apple">Alışverişe Başla</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle small">
                            <thead class="table-light">
                                <tr>
                                    <th>Sipariş No</th>
                                    <th>Tarih</th>
                                    <th>Tutar</th>
                                    <th>Ödeme</th>
                                    <th>Durum</th>
                                    <th>Kargo</th>
                                    <th class="text-end">İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($userOrders as $ord): ?>
                                    <tr>
                                        <td class="fw-bold font-monospace"><?= htmlspecialchars($ord['order_number']) ?></td>
                                        <td><?= date('d.m.Y H:i', strtotime($ord['created_at'])) ?></td>
                                        <td class="fw-bold text-primary"><?= Helper::formatPrice($ord['total_amount']) ?></td>
                                        <td><?= Helper::getPaymentStatusBadge($ord['payment_status']) ?></td>
                                        <td><?= Helper::getOrderStatusBadge($ord['order_status']) ?></td>
                                        <td>
                                            <?php if (!empty($ord['cargo_tracking_code'])): ?>
                                                <a href="<?= Cargo::getTrackingLink($ord['cargo_company'], $ord['cargo_tracking_code']) ?>" target="_blank" class="badge bg-light text-primary border text-decoration-none">
                                                    <?= htmlspecialchars($ord['cargo_company']) ?> <i class="bi bi-box-arrow-up-right"></i>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?= SITE_URL ?>/success.php?order_number=<?= urlencode($ord['order_number']) ?>" class="btn btn-sm btn-outline-primary py-0">
                                                Detay
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
