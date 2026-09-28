<?php
/**
 * TAMBASKI.COM.TR - Admin E-Bayi & B2B Başvuru Yönetimi
 */
$page_title = "E-Bayi & B2B Yönetimi";
require_once __DIR__ . '/header.php';

$dealers = [
    [
        'id' => 1,
        'company' => 'Hedef Tasarım & Reklam Ajansı',
        'contact' => 'Ahmet Kaya',
        'email' => 'info@hedefreklam.com',
        'phone' => '0532 111 22 33',
        'tax_no' => '1234567890 (Beşiktaş V.D.)',
        'discount_rate' => 25,
        'status' => 'Onaylandı',
        'created_at' => '2026-09-28 14:30'
    ],
    [
        'id' => 2,
        'company' => 'Artı Matbaacılık & Promosyon',
        'contact' => 'Mustafa Demir',
        'email' => 'mustafa@artimatbaa.com',
        'phone' => '0542 444 55 66',
        'tax_no' => '9876543210 (Kadıköy V.D.)',
        'discount_rate' => 25,
        'status' => 'Onay Bekliyor',
        'created_at' => '2026-09-28 18:45'
    ]
];
?>

<div class="row g-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-briefcase-fill text-success me-2"></i>E-Bayi & B2B Ajans Yönetimi</h3>
            <p class="text-muted small mb-0">%25 toptan iskonto tanımlı reklam ajansı ve matbaacı bayi listesi</p>
        </div>
    </div>

    <div class="col-12">
        <div class="apple-card p-4 bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light small">
                        <tr>
                            <th>Firma / Ajans Adı</th>
                            <th>Yetkili</th>
                            <th>İletişim Bilgileri</th>
                            <th>Vergi Bilgileri</th>
                            <th>İskonto Oranı</th>
                            <th>Durum</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dealers as $d): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($d['company']) ?></strong></td>
                                <td><?= htmlspecialchars($d['contact']) ?></td>
                                <td>
                                    <div><?= htmlspecialchars($d['phone']) ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($d['email']) ?></small>
                                </td>
                                <td class="small text-muted"><?= htmlspecialchars($d['tax_no']) ?></td>
                                <td><span class="badge bg-danger-subtle text-danger fw-bold fs-6">%<?= $d['discount_rate'] ?></span></td>
                                <td>
                                    <?php if ($d['status'] === 'Onaylandı'): ?>
                                        <span class="badge bg-success">Onaylandı</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Onay Bekliyor</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($d['status'] !== 'Onaylandı'): ?>
                                        <button class="btn btn-sm btn-success rounded-pill px-3"><i class="bi bi-check-lg me-1"></i>Onayla</button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3"><i class="bi bi-pencil me-1"></i>Düzenle</button>
                                    <?php endif; ?>
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
