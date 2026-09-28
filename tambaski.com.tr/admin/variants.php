<?php
require_once __DIR__ . '/../config/config.php';
Auth::requireAdmin();

$db = Database::getInstance()->getConnection();
$action = $_GET['action'] ?? 'list';

// 1. Grup Kaydet (Ekle / Güncelle)
if (isset($_POST['action']) && $_POST['action'] === 'save_group') {
    $groupId = (int)($_POST['group_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $slug = Helper::slugify($name);
    $description = trim($_POST['description'] ?? '');
    $inputType = $_POST['input_type'] ?? 'radio';
    $isRequired = !empty($_POST['is_required']) ? 1 : 0;
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $status = !empty($_POST['status']) ? 1 : 0;

    if ($groupId > 0) {
        $stmt = $db->prepare("UPDATE variant_groups SET name = ?, slug = ?, description = ?, input_type = ?, is_required = ?, sort_order = ?, status = ? WHERE id = ?");
        $stmt->execute([$name, $slug, $description, $inputType, $isRequired, $sortOrder, $status, $groupId]);
        Helper::setFlash('success', 'Varyant grubu güncellendi.');
    } else {
        $stmt = $db->prepare("INSERT INTO variant_groups (name, slug, description, input_type, is_required, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $slug, $description, $inputType, $isRequired, $sortOrder, $status]);
        Helper::setFlash('success', 'Yeni varyant grubu oluşturuldu.');
    }
    header("Location: " . SITE_URL . "/admin/variants.php");
    exit;
}

// 2. Grup Sil
if (isset($_GET['delete_group'])) {
    $groupId = (int)$_GET['delete_group'];
    $db->prepare("DELETE FROM variant_options WHERE group_id = ?")->execute([$groupId]);
    $db->prepare("DELETE FROM variant_groups WHERE id = ?")->execute([$groupId]);
    Helper::setFlash('success', 'Varyant grubu ve alt seçenekleri silindi.');
    header("Location: " . SITE_URL . "/admin/variants.php");
    exit;
}

// 3. Seçenek Kaydet (Ekle / Güncelle)
if (isset($_POST['action']) && $_POST['action'] === 'save_option') {
    $optId = (int)($_POST['option_id'] ?? 0);
    $groupId = (int)($_POST['group_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $calcType = $_POST['calc_type'] ?? 'per_unit_try';
    $supplierCost1000 = (float)str_replace(',', '.', $_POST['supplier_cost_1000'] ?? 0.00);
    $priceUsd = (float)str_replace(',', '.', $_POST['price_usd_70x100'] ?? 0.00);
    $fixedFeeUsd = (float)str_replace(',', '.', $_POST['fixed_fee_usd'] ?? 0.00);
    $percentFee = (float)str_replace(',', '.', $_POST['percent_fee'] ?? 0.00);
    $perUnitFeeTry = (float)str_replace(',', '.', $_POST['per_unit_fee_try'] ?? 0.00);
    $fixedFeeTry = (float)str_replace(',', '.', $_POST['fixed_fee_try'] ?? 0.00);
    $isDefault = !empty($_POST['is_default']) ? 1 : 0;
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $status = !empty($_POST['status']) ? 1 : 0;

    // Eğer bu seçenek varsayılan yapıldıysa gruptaki diğerlerinin varsayılanlığını sıfırla
    if ($isDefault && $groupId > 0) {
        $db->prepare("UPDATE variant_options SET is_default = 0 WHERE group_id = ?")->execute([$groupId]);
    }

    if ($optId > 0) {
        $stmt = $db->prepare("UPDATE variant_options SET group_id = ?, name = ?, calc_type = ?, supplier_cost_1000 = ?, price_usd_70x100 = ?, fixed_fee_usd = ?, percent_fee = ?, per_unit_fee_try = ?, fixed_fee_try = ?, is_default = ?, sort_order = ?, status = ? WHERE id = ?");
        $stmt->execute([$groupId, $name, $calcType, $supplierCost1000, $priceUsd, $fixedFeeUsd, $percentFee, $perUnitFeeTry, $fixedFeeTry, $isDefault, $sortOrder, $status, $optId]);
        Helper::setFlash('success', 'Varyant seçeneği güncellendi.');
    } else {
        $stmt = $db->prepare("INSERT INTO variant_options (group_id, name, calc_type, supplier_cost_1000, price_usd_70x100, fixed_fee_usd, percent_fee, per_unit_fee_try, fixed_fee_try, is_default, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$groupId, $name, $calcType, $supplierCost1000, $priceUsd, $fixedFeeUsd, $percentFee, $perUnitFeeTry, $fixedFeeTry, $isDefault, $sortOrder, $status]);
        Helper::setFlash('success', 'Gruba yeni seçenek eklendi.');
    }
    header("Location: " . SITE_URL . "/admin/variants.php");
    exit;
}

// 4. Seçenek Sil
if (isset($_GET['delete_option'])) {
    $optId = (int)$_GET['delete_option'];
    $db->prepare("DELETE FROM variant_options WHERE id = ?")->execute([$optId]);
    Helper::setFlash('success', 'Varyant seçeneği silindi.');
    header("Location: " . SITE_URL . "/admin/variants.php");
    exit;
}

// Tüm Grupları ve Alt Seçeneklerini Çek
$groups = $db->query("SELECT * FROM variant_groups ORDER BY sort_order ASC, id ASC")->fetchAll();
foreach ($groups as &$grp) {
    $optStmt = $db->prepare("SELECT * FROM variant_options WHERE group_id = ? ORDER BY sort_order ASC, id ASC");
    $optStmt->execute([$grp['id']]);
    $grp['options'] = $optStmt->fetchAll();
}

$pageTitle = 'Varyant Grupları & Seçenekleri';
require_once __DIR__ . '/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-diagram-3-fill text-primary me-2"></i>Varyant Grupları & Seçenek Yönetimi</h4>
        <p class="text-muted small mb-0">Kesim türü, baskı yönü, selefon veya ekstra efekt gibi gruplar oluşturup içlerine seçenekler ekleyin.</p>
    </div>
    <button type="button" class="btn btn-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalGroup">
        <i class="bi bi-plus-lg me-1"></i> Yeni Varyant Grubu Ekle
    </button>
</div>

<!-- Varyant Grupları Listesi (Kartlar / Akordeon) -->
<div class="row g-4">
    <?php if (empty($groups)): ?>
        <div class="col-12">
            <div class="apple-card p-5 text-center text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-3 text-secondary"></i>
                <h5>Henüz Tanımlı Varyant Grubu Yok</h5>
                <p class="small">"Yeni Varyant Grubu Ekle" butonuna basarak ilk grubunuzu (örn: Kesim Türü) oluşturabilirsiniz.</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($groups as $group): ?>
            <div class="col-lg-6">
                <div class="apple-card p-4 h-100 border">
                    
                    <!-- Grup Başlığı & Yönetim -->
                    <div class="d-flex justify-content-between align-items-start mb-3 pb-2 border-bottom">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">
                                <i class="bi bi-folder2-open text-primary me-1"></i> <?= htmlspecialchars($group['name']) ?>
                            </h5>
                            <span class="text-muted small"><?= htmlspecialchars($group['description'] ?? 'Seçenek listesi') ?></span>
                        </div>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-light border" onclick="editGroup(<?= htmlspecialchars(json_encode($group)) ?>)" title="Grubu Düzenle">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="<?= SITE_URL ?>/admin/variants.php?delete_group=<?= $group['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bu varyant grubunu ve içindeki tüm seçenekleri silmek istediğinize emin misiniz?')" title="Grubu Sil">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Gruptaki Seçenekler Tablosu -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold small text-muted">Seçenekler (<?= count($group['options']) ?> Adet)</span>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-0 px-2" style="font-size: 11px;" onclick="addOptionToGroup(<?= $group['id'] ?>, '<?= htmlspecialchars($group['name'], ENT_QUOTES) ?>')">
                            <i class="bi bi-plus-lg me-1"></i> Seçenek Ekle
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light small text-muted">
                                <tr>
                                    <th>Seçenek Adı</th>
                                    <th>Fiyatlandırma Mantığı</th>
                                    <th>Ek Tutar / Oran</th>
                                    <th class="text-end">İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($group['options'])): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted small">Bu grupta henüz seçenek yok.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($group['options'] as $opt): 
                                        $cType = $opt['calc_type'];
                                        $badge = '';
                                        $priceText = '';
                                        if ($cType === 'percent') {
                                            $badge = '<span class="badge bg-purple-subtle text-purple" style="background:#f3e8ff; color:#7e22ce; font-size:10px;">% Yüzde</span>';
                                            $priceText = (float)$opt['percent_fee'] > 0 ? '<strong style="color:#7e22ce;">+%' . (float)$opt['percent_fee'] . '</strong>' : '<span class="text-muted">Ücretsiz</span>';
                                        } elseif ($cType === 'per_unit_try') {
                                            $badge = '<span class="badge bg-primary-subtle text-primary" style="font-size:10px;">Adet Başı (₺)</span>';
                                            $priceText = (float)$opt['per_unit_fee_try'] > 0 ? '<strong class="text-primary">+' . number_format((float)$opt['per_unit_fee_try'], 2, ',', '.') . ' ₺ / adet</strong>' : '<span class="text-muted">Ücretsiz (0 ₺)</span>';
                                        } elseif ($cType === 'fixed_try') {
                                            $badge = '<span class="badge bg-dark-subtle text-dark" style="font-size:10px;">Sabit (₺)</span>';
                                            $priceText = '<strong class="text-dark">' . number_format((float)$opt['fixed_fee_try'], 2, ',', '.') . ' ₺ sabit</strong>';
                                        } elseif ($cType === 'fixed_usd') {
                                            $badge = '<span class="badge bg-warning-subtle text-dark" style="font-size:10px;">Sabit ($ USD)</span>';
                                            $priceText = '<strong class="text-warning-emphasis">$' . number_format((float)$opt['fixed_fee_usd'], 2) . ' sabit</strong>';
                                        } else {
                                            // sheet_usd
                                            $badge = '<span class="badge bg-success-subtle text-success" style="font-size:10px;">70x100 Tabaka ($)</span>';
                                            $priceText = (float)$opt['price_usd_70x100'] > 0 ? '<strong class="text-success">$' . number_format((float)$opt['price_usd_70x100'], 2) . ' / tabaka</strong>' : '<span class="text-muted">Ücretsiz ($0)</span>';
                                            if ((float)$opt['fixed_fee_usd'] > 0) {
                                                $priceText .= ' <small class="text-muted">(+$' . number_format((float)$opt['fixed_fee_usd'], 2) . ' kalıp)</small>';
                                            }
                                        }
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark" style="font-size: 13px;">
                                                    <?= htmlspecialchars($opt['name']) ?>
                                                    <?php if (!empty($opt['is_default'])): ?>
                                                        <span class="badge bg-success-subtle text-success" style="font-size: 9px;">Varsayılan</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td><?= $badge ?></td>
                                            <td><?= $priceText ?></td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-light border py-0 px-1" onclick="editOption(<?= htmlspecialchars(json_encode($opt)) ?>)">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <a href="<?= SITE_URL ?>/admin/variants.php?delete_option=<?= $opt['id'] ?>" class="btn btn-sm btn-outline-danger py-0 px-1" onclick="return confirm('Bu seçeneği silmek istediğinize emin misiniz?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- ========================================================================= -->
<!-- MODAL: VARYANT GRUBU EKLE / DÜZENLE -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalGroup" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="<?= SITE_URL ?>/admin/variants.php" method="POST">
                <input type="hidden" name="action" value="save_group">
                <input type="hidden" name="group_id" id="group_id" value="0">
                <div class="modal-header border-bottom py-3">
                    <h6 class="modal-title fw-bold" id="modalGroupTitle">Yeni Varyant Grubu Ekle</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Grup Adı *</label>
                        <input type="text" name="name" id="grp_name" class="form-control" placeholder="Örn: Kesim Türü, Baskı Yönü, Selefon Kaplama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kısa Açıklama</label>
                        <input type="text" name="description" id="grp_desc" class="form-control" placeholder="Örn: Köşe ve bıçak kesim seçenekleri">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Görünüm / Seçim Tipi</label>
                            <select name="input_type" id="grp_input_type" class="form-select">
                                <option value="radio">Radio Buton (Tekli Seçim)</option>
                                <option value="select">Açılır Liste (Dropdown)</option>
                                <option value="checkbox">Çoklu Seçim (Opsiyonel)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Sıra Numarası</label>
                            <input type="number" name="sort_order" id="grp_sort_order" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_required" id="grp_is_required" value="1" checked>
                            <label class="form-check-label small" for="grp_is_required">Zorunlu Seçim</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" id="grp_status" value="1" checked>
                            <label class="form-check-label small" for="grp_status">Aktif</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Vazgeç</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: VARYANT SEÇENEĞİ EKLE / DÜZENLE -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalOption" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="<?= SITE_URL ?>/admin/variants.php" method="POST">
                <input type="hidden" name="action" value="save_option">
                <input type="hidden" name="option_id" id="opt_id" value="0">
                <div class="modal-header border-bottom py-3">
                    <h6 class="modal-title fw-bold" id="modalOptionTitle">Yeni Seçenek Ekle</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Ait Olduğu Grup *</label>
                        <select name="group_id" id="opt_group_id" class="form-select" required>
                            <?php foreach ($groups as $g): ?>
                                <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Seçenek Adı *</label>
                        <input type="text" name="name" id="opt_name" class="form-control" placeholder="Örn: Oval Radyus Köşe, Çift Yön, Mat Selefon" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Fiyatlandırma Mantığı *</label>
                        <select name="calc_type" id="opt_calc_type" class="form-select" onchange="toggleOptCalcInputs()">
                            <option value="per_unit_try">Adet Başı Ek Ücret (+₺ / Adet)</option>
                            <option value="percent">Yüzdesel Artış (% +)</option>
                            <option value="sheet_usd">70x100 Tabaka Başı ($ USD)</option>
                            <option value="fixed_try">Sipariş Başı Sabit Ücret (Sabit ₺)</option>
                            <option value="fixed_usd">Sipariş Başı Sabit Kalıp (Sabit $ USD)</option>
                        </select>
                    </div>

                    <div class="mb-3 p-3 bg-light rounded-3 border">
                        <label class="form-label small fw-bold text-primary"><i class="bi bi-lightning-charge-fill me-1"></i>Tedarikçi (Türmatsan) 1.000 Adet Ek Maliyeti (₺)</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">₺</span>
                            <input type="number" step="0.5" name="supplier_cost_1000" id="opt_supplier_cost_1000" class="form-control" value="0.00">
                        </div>
                        <span class="text-muted" style="font-size: 11px;">Otomatik modda toptancı baz maliyetine eklenir veya çıkarılır (Örn: Kabartma Lak +200 ₺, 250gr Bristol -100 ₺).</span>
                    </div>

                    <!-- 1. Adet Başı Sabit TL Girişi -->
                    <div id="optbox_per_unit_try" class="opt-calc-box mb-3">
                        <label class="form-label small fw-bold">Manuel Satış Ek Tutarı (₺ / Adet)</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="per_unit_fee_try" id="opt_per_unit_fee_try" class="form-control" value="0.00">
                            <span class="input-group-text">₺ / Adet</span>
                        </div>
                        <span class="text-muted" style="font-size: 11px;">Manuel fiyat modunda sipariş adedi ile çarpılarak satış fiyatına eklenir.</span>
                    </div>


                    <!-- 2. Yüzdesel Artış (%) Girişi -->
                    <div id="optbox_percent" class="opt-calc-box mb-3" style="display: none;">
                        <label class="form-label small fw-bold">Yüzdesel Artış Oranı (%)</label>
                        <div class="input-group">
                            <span class="input-group-text">%</span>
                            <input type="number" step="0.1" name="percent_fee" id="opt_percent_fee" class="form-control" value="25.0">
                        </div>
                        <span class="text-muted" style="font-size: 11px;">Toplam maliyete % oranında eklenir (Örn: Çift Yön Baskı +%25).</span>
                    </div>

                    <!-- 3. Tabaka Başı USD Girişi -->
                    <div id="optbox_sheet_usd" class="opt-calc-box mb-3" style="display: none;">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small fw-bold">70x100 Tabaka Başı ($ USD)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" name="price_usd_70x100" id="opt_price_usd" class="form-control" value="1.50">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">Sabit Kalıp ($ USD)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" name="fixed_fee_usd" id="opt_fixed_usd" class="form-control" value="0.00">
                                </div>
                            </div>
                        </div>
                        <span class="text-muted" style="font-size: 11px;">Baskıda gereken 70x100 tabaka adedi ile çarpılır (Selefon, Lak vb. için).</span>
                    </div>

                    <!-- 4. Sipariş Başı Sabit TL Girişi -->
                    <div id="optbox_fixed_try" class="opt-calc-box mb-3" style="display: none;">
                        <label class="form-label small fw-bold">Sipariş Başı Sabit Ücret (Sabit ₺)</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="fixed_fee_try" id="opt_fixed_fee_try" class="form-control" value="150.00">
                            <span class="input-group-text">₺ (Tek Seferlik)</span>
                        </div>
                        <span class="text-muted" style="font-size: 11px;">Adetten bağımsız olarak siparişe tek bir sabit ücret ekler (Örn: 150 ₺ Bıçak Kalıbı).</span>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Sıra Numarası</label>
                            <input type="number" name="sort_order" id="opt_sort_order" class="form-control" value="0">
                        </div>
                        <div class="col-6 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="is_default" id="opt_is_default" value="1">
                                <label class="form-check-label small" for="opt_is_default">Grupta Varsayılan</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="status" id="opt_status" value="1" checked>
                        <label class="form-check-label small" for="opt_status">Aktif / Kullanımda</label>
                    </div>

                </div>
                <div class="modal-footer border-top py-2">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Vazgeç</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleOptCalcInputs() {
    const calcType = document.getElementById('opt_calc_type').value;
    document.querySelectorAll('.opt-calc-box').forEach(b => b.style.display = 'none');
    
    if (calcType === 'percent') {
        document.getElementById('optbox_percent').style.display = 'block';
    } else if (calcType === 'sheet_usd' || calcType === 'fixed_usd') {
        document.getElementById('optbox_sheet_usd').style.display = 'block';
    } else if (calcType === 'fixed_try') {
        document.getElementById('optbox_fixed_try').style.display = 'block';
    } else {
        document.getElementById('optbox_per_unit_try').style.display = 'block';
    }
}

function editGroup(g) {
    document.getElementById('modalGroupTitle').textContent = 'Grubu Düzenle: ' + g.name;
    document.getElementById('group_id').value = g.id;
    document.getElementById('grp_name').value = g.name;
    document.getElementById('grp_desc').value = g.description || '';
    document.getElementById('grp_input_type').value = g.input_type || 'radio';
    document.getElementById('grp_sort_order').value = g.sort_order || 0;
    document.getElementById('grp_is_required').checked = g.is_required == 1;
    document.getElementById('grp_status').checked = g.status == 1;
    new bootstrap.Modal(document.getElementById('modalGroup')).show();
}

function addOptionToGroup(groupId, groupName) {
    document.getElementById('modalOptionTitle').textContent = 'Yeni Seçenek Ekle: ' + groupName;
    document.getElementById('opt_id').value = '0';
    document.getElementById('opt_group_id').value = groupId;
    document.getElementById('opt_name').value = '';
    document.getElementById('opt_calc_type').value = 'per_unit_try';
    document.getElementById('opt_supplier_cost_1000').value = '0.00';
    document.getElementById('opt_price_usd').value = '0.00';
    document.getElementById('opt_fixed_usd').value = '0.00';
    document.getElementById('opt_percent_fee').value = '0.00';
    document.getElementById('opt_per_unit_fee_try').value = '0.00';
    document.getElementById('opt_fixed_fee_try').value = '0.00';
    document.getElementById('opt_is_default').checked = false;
    document.getElementById('opt_status').checked = true;
    toggleOptCalcInputs();
    new bootstrap.Modal(document.getElementById('modalOption')).show();
}

function editOption(opt) {
    document.getElementById('modalOptionTitle').textContent = 'Seçenek Düzenle: ' + opt.name;
    document.getElementById('opt_id').value = opt.id;
    document.getElementById('opt_group_id').value = opt.group_id;
    document.getElementById('opt_name').value = opt.name;
    
    const calcType = opt.calc_type || 'per_unit_try';
    document.getElementById('opt_calc_type').value = calcType;
    document.getElementById('opt_supplier_cost_1000').value = opt.supplier_cost_1000 || 0;
    document.getElementById('opt_price_usd').value = opt.price_usd_70x100 || 0;
    document.getElementById('opt_fixed_usd').value = opt.fixed_fee_usd || 0;
    document.getElementById('opt_percent_fee').value = opt.percent_fee || 0;
    document.getElementById('opt_per_unit_fee_try').value = opt.per_unit_fee_try || 0;
    document.getElementById('opt_is_default').checked = opt.is_default == 1;
    document.getElementById('opt_status').checked = opt.status == 1;
    toggleOptCalcInputs();
    new bootstrap.Modal(document.getElementById('modalOption')).show();
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>

