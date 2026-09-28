<?php
/**
 * TAMBASKI.COM.TR - Admin Mockup & Vektörel Şablon Yönetimi
 */
$page_title = "3D Mockup & Baskı Şablonları";
require_once __DIR__ . '/header.php';

$mockups = [
    [
        'id' => 'bizcard',
        'title' => '3D Kurumsal Kartvizit Mockup',
        'category' => 'Kartvizitler',
        'icon' => 'bi-person-badge',
        'features' => ['350gr Mat Kuşe Dokusu', 'Altın Varak Parlaması', 'Kabartma Lak Yansıması', 'Yuvarlak/Düz Köşe'],
        'status' => 'Aktif',
        'preview_class' => 'stage-kartvizit'
    ],
    [
        'id' => 'flag',
        'title' => '3D Yelken Bayrak (Plaj Bayrağı) Mockup',
        'category' => 'Bayrak & Dış Mekan',
        'icon' => 'bi-flag-fill',
        'features' => ['Damla & L Tipi Direk', 'Rüzgar Dalgalanma Efekti', 'Çift Yön Kumaş Geçirgenliği'],
        'status' => 'Aktif',
        'preview_class' => 'stage-flag'
    ],
    [
        'id' => 'mug',
        'title' => '3D Porselen Kupa Bardak Mockup',
        'category' => 'Promosyon & Hediyelik',
        'icon' => 'bi-cup-hot-fill',
        'features' => ['Silindirik 360 Çevreleme', 'Porselen Işık Yansıması', 'İç & Kulp Renk Seçimi'],
        'status' => 'Aktif',
        'preview_class' => 'stage-mug'
    ],
    [
        'id' => 'folder',
        'title' => '3D Cepli Sunum Dosyası Mockup',
        'category' => 'Kurumsal Kırtasiye',
        'icon' => 'bi-folder2-open',
        'features' => ['Kartvizit Yuvası', 'A4 Belge Yerleşimi', 'Mat/Parlak Selefon'],
        'status' => 'Aktif',
        'preview_class' => 'stage-kurumsal'
    ],
    [
        'id' => 'signage',
        'title' => '3D Dekota (Foreks) & Pleksi Tabela Mockup',
        'category' => 'Dekota & Pleksi Kesim',
        'icon' => 'bi-layers-fill',
        'features' => ['Duvar Montaj Vidaları (Yükseltici)', 'Lazer Kesim Kenar Parlaklığı', 'UV Baskı Dokusu'],
        'status' => 'Aktif',
        'preview_class' => 'stage-signage'
    ],
    [
        'id' => 'tshirt',
        'title' => '3D Tekstil / Tişört Mockup',
        'category' => 'Tekstil & Çanta',
        'icon' => 'bi-bag-fill',
        'features' => ['Pamuk Kumaş Kıvrımları', 'DTF / Serigrafi Dokusu', 'Ön / Arka Baskı Alanı'],
        'status' => 'Aktif',
        'preview_class' => 'stage-tshirt'
    ]
];
?>

<div class="row g-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-layers-half text-info me-2"></i>3D Canlı Mockup & Şablon Motoru</h3>
            <p class="text-muted small mb-0">Müşterilerinizin tarayıcıda tasarladıkları veya yükledikleri tasarımları canlı 3D simülasyonda gösteren hazır şablonlar</p>
        </div>
        <button class="btn btn-apple btn-apple-orange" data-bs-toggle="modal" data-bs-target="#newMockupModal">
            <i class="bi bi-plus-lg me-1"></i> Yeni Mockup Şablonu Ekle
        </button>
    </div>

    <div class="col-12">
        <div class="row g-4">
            <?php foreach ($mockups as $m): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="apple-card p-4 bg-white h-100 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="p-3 bg-light rounded-circle text-primary">
                                <i class="bi <?= $m['icon'] ?> fs-3"></i>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success"><?= $m['status'] ?></span>
                        </div>

                        <h5 class="fw-bold mb-1"><?= htmlspecialchars($m['title']) ?></h5>
                        <small class="text-muted mb-3 d-block"><i class="bi bi-tag me-1"></i><?= htmlspecialchars($m['category']) ?></small>

                        <div class="p-3 bg-light rounded-3 border mb-3 flex-grow-1">
                            <small class="fw-bold text-dark d-block mb-2">Simülasyon Özellikleri:</small>
                            <ul class="list-unstyled mb-0 small text-muted">
                                <?php foreach ($m['features'] as $f): ?>
                                    <li class="mb-1"><i class="bi bi-check2 text-success me-1"></i><?= htmlspecialchars($f) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <div class="d-flex gap-2 mt-auto pt-2 border-top">
                            <a href="../designer.php?mockup=<?= $m['id'] ?>" target="_blank" class="btn btn-sm btn-apple btn-apple-orange w-100">
                                <i class="bi bi-eye me-1"></i> Canlı Önizle & Tasarla
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
