<?php
require_once __DIR__ . '/../config/config.php';
Auth::requireAdmin();

$db = Database::getInstance()->getConnection();
$action = $_GET['action'] ?? 'list';

// Bayi Onay / Ret / İskonto Güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_dealer'])) {
    $userId = (int)($_POST['user_id'] ?? 0);
    $status = $_POST['dealer_status'] ?? 'pending';
    $discount = (float)($_POST['discount_rate'] ?? 0);

    $stmt = $db->prepare("UPDATE users SET dealer_status = ?, discount_rate = ? WHERE id = ?");
    $stmt->execute([$status, $discount, $userId]);

    Helper::setFlash('success', 'Bayi durumu ve iskonto oranı güncellendi.');
    header("Location: " . SITE_URL . "/admin/dealers.php");
    exit;
}

$users = $db->query("SELECT * FROM users ORDER BY role ASC, id DESC")->fetchAll();
$pageTitle = 'Müşteri & E-Bayi Yönetimi (B2B)';
require_once __DIR__ . '/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Müşteriler & E-Bayiler (B2B)</h4>
        <p class="text-muted small mb-0">Ajans ve kurumsal bayi başvurularını onaylayın, özel iskonto oranları belirleyin.</p>
    </div>
</div>

<div class="apple-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle small">
            <thead class="table-light">
                <tr>
                    <th>Kullanıcı / Yetkili</th>
                    <th>Firma / Vergi Bilgileri</th>
                    <th>Rol</th>
                    <th>Bayi Durumu</th>
                    <th>İskonto Oranı</th>
                    <th>Kayıt Tarihi</th>
                    <th class="text-end">İşlem / Düzenle</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td>
                            <div class="fw-bold"><?= htmlspecialchars($u['full_name']) ?></div>
                            <div class="text-muted" style="font-size: 11px;"><?= htmlspecialchars($u['email']) ?> • <?= htmlspecialchars($u['phone'] ?? '') ?></div>
                        </td>
                        <td>
                            <?php if ($u['role'] === 'dealer'): ?>
                                <div class="fw-bold"><?= htmlspecialchars($u['dealer_company'] ?? '-') ?></div>
                                <div class="text-muted" style="font-size: 11px;">V.No: <?= htmlspecialchars($u['tax_number'] ?? '-') ?> (<?= htmlspecialchars($u['tax_office'] ?? '-') ?>)</div>
                            <?php else: ?>
                                <span class="text-muted">Bireysel</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($u['role'] === 'admin'): ?>
                                <span class="badge bg-danger">Yönetici</span>
                            <?php elseif ($u['role'] === 'dealer'): ?>
                                <span class="badge bg-primary">E-Bayi</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Müşteri</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($u['role'] === 'dealer'): ?>
                                <?php if ($u['dealer_status'] === 'approved'): ?>
                                    <span class="badge bg-success">Onaylandı</span>
                                <?php elseif ($u['dealer_status'] === 'pending'): ?>
                                    <span class="badge bg-warning text-dark">Onay Bekliyor</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Reddedildi</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="fw-bold text-success">
                            <?= $u['role'] === 'dealer' ? '%' . (int)$u['discount_rate'] : '-' ?>
                        </td>
                        <td><?= date('d.m.Y', strtotime($u['created_at'])) ?></td>
                        <td class="text-end">
                            <?php if ($u['role'] === 'dealer'): ?>
                                <button type="button" class="btn btn-sm btn-outline-primary py-0" data-bs-toggle="modal" data-bs-target="#dealerModal<?= $u['id'] ?>">
                                    İskonto / Onay
                                </button>

                                <!-- Bayi Düzenleme Modalı -->
                                <div class="modal fade" id="dealerModal<?= $u['id'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 text-start">
                                            <form action="<?= SITE_URL ?>/admin/dealers.php" method="POST">
                                                <input type="hidden" name="update_dealer" value="1">
                                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                                <div class="modal-header border-bottom">
                                                    <h6 class="modal-title fw-bold">Bayi Düzenle: <?= htmlspecialchars($u['dealer_company']) ?></h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">Bayilik Durumu</label>
                                                        <select name="dealer_status" class="form-select">
                                                            <option value="approved" <?= $u['dealer_status'] === 'approved' ? 'selected' : '' ?>>Onayla (Aktif Bayi)</option>
                                                            <option value="pending" <?= $u['dealer_status'] === 'pending' ? 'selected' : '' ?>>Onay Beklet</option>
                                                            <option value="rejected" <?= $u['dealer_status'] === 'rejected' ? 'selected' : '' ?>>Reddet</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">Özel Bayi İskonto Oranı (%)</label>
                                                        <input type="number" step="0.5" name="discount_rate" class="form-control" value="<?= (float)$u['discount_rate'] ?>">
                                                        <small class="text-muted">Örn: 15 yazarsanız sepet toplamında otomatik %15 indirim uygulanır.</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">İptal</button>
                                                    <button type="submit" class="btn btn-sm btn-primary">Kaydet</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
