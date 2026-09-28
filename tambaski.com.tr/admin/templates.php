<?php
require_once __DIR__ . '/../config/config.php';
Auth::requireAdmin();

$db = Database::getInstance()->getConnection();
$action = $_GET['action'] ?? 'list';
$selectedIndustry = $_GET['industry'] ?? 'all';
$selectedType = $_GET['type'] ?? 'all';

// -----------------------------------------------------------------------------
// 1. Silme İşlemi
// -----------------------------------------------------------------------------
if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $db->prepare("DELETE FROM design_templates WHERE id = ?");
    $stmt->execute([$id]);
    Helper::setFlash('success', 'Şablon başarıyla silindi.');
    
    $redirect = SITE_URL . "/admin/templates.php?";
    if ($selectedIndustry !== 'all') $redirect .= "industry=" . urlencode($selectedIndustry) . "&";
    if ($selectedType !== 'all') $redirect .= "type=" . urlencode($selectedType);
    header("Location: " . rtrim($redirect, '?&'));
    exit;
}

// -----------------------------------------------------------------------------
// 2. Kaydetme / Güncelleme İşlemi (POST)
// -----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $templateId = (int)($_POST['template_id'] ?? 0);
    $productId = (int)($_POST['product_id'] ?? 0);
    $productType = trim($_POST['product_type'] ?? 'kartvizit');
    $orientation = trim($_POST['orientation'] ?? 'horizontal');
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? 'Modern Minimal');
    $industrySlug = trim($_POST['industry_slug'] ?? 'genel-kurumsal');
    $canvasWidth = (int)($_POST['canvas_width'] ?? ($productType === 'brosur' ? 500 : 850));
    $canvasHeight = (int)($_POST['canvas_height'] ?? ($productType === 'brosur' ? 700 : 500));
    $svg = trim($_POST['default_svg'] ?? '');
    $fieldsJson = trim($_POST['template_data'] ?? '');
    $slug = Helper::slugify($title);

    // Sektör ID bul
    $indRow = $db->prepare("SELECT id FROM industries WHERE slug = ?");
    $indRow->execute([$industrySlug]);
    $indId = $indRow->fetchColumn() ?: null;

    if ($templateId > 0) {
        $stmt = $db->prepare("UPDATE design_templates SET 
            product_id = ?, product_type = ?, orientation = ?, title = ?, slug = ?, category = ?, industry_slug = ?, industry_id = ?, 
            canvas_width = ?, canvas_height = ?, default_svg = ?, template_data = ? 
            WHERE id = ?");
        $stmt->execute([
            $productId ?: null, $productType, $orientation, $title, $slug, $category, $industrySlug, $indId,
            $canvasWidth, $canvasHeight, $svg, $fieldsJson, $templateId
        ]);
        Helper::setFlash('success', 'Şablon başarıyla güncellendi.');
    } else {
        $stmt = $db->prepare("INSERT INTO design_templates (
            product_id, product_type, orientation, title, slug, category, industry_slug, industry_id, 
            canvas_width, canvas_height, default_svg, template_data, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->execute([
            $productId ?: null, $productType, $orientation, $title, $slug, $category, $industrySlug, $indId,
            $canvasWidth, $canvasHeight, $svg, $fieldsJson
        ]);
        Helper::setFlash('success', 'Yeni sektörel şablon eklendi.');
    }

    header("Location: " . SITE_URL . "/admin/templates.php?industry=" . urlencode($industrySlug) . "&type=" . urlencode($productType));
    exit;
}

// -----------------------------------------------------------------------------
// 3. Veri Hazırlığı
// -----------------------------------------------------------------------------
$industries = $db->query("SELECT * FROM industries WHERE status = 1 ORDER BY sort_order ASC, id ASC")->fetchAll();
$products = $db->query("SELECT id, name, slug, category_id FROM products WHERE allow_online_editor = 1 ORDER BY name ASC")->fetchAll();

$editTemplate = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $stmtEdit = $db->prepare("SELECT * FROM design_templates WHERE id = ?");
    $stmtEdit->execute([(int)$_GET['id']]);
    $editTemplate = $stmtEdit->fetch();
}

$sqlTpl = "SELECT dt.*, p.name AS product_name, i.name AS industry_name, i.icon AS industry_icon 
           FROM design_templates dt 
           LEFT JOIN products p ON dt.product_id = p.id 
           LEFT JOIN industries i ON dt.industry_slug = i.slug 
           WHERE dt.status = 1";

$params = [];
if ($selectedIndustry !== 'all') {
    $sqlTpl .= " AND dt.industry_slug = ?";
    $params[] = $selectedIndustry;
}
if ($selectedType !== 'all') {
    $sqlTpl .= " AND dt.product_type = ?";
    $params[] = $selectedType;
}
$sqlTpl .= " ORDER BY dt.id DESC";

$stmt = $db->prepare($sqlTpl);
$stmt->execute($params);
$templates = $stmt->fetchAll();

$productTypeLabels = [
    'kartvizit'   => ['name' => 'Kartvizit', 'icon' => 'bi-card-heading', 'desc' => '85x50 mm Yatay / 50x85 mm Dikey'],
    'brosur'      => ['name' => 'El İlanı / Broşür', 'icon' => 'bi-file-earmark-text', 'desc' => 'A5 Dikey (148x210 mm)'],
    'cepli-dosya' => ['name' => 'Cepli Dosya', 'icon' => 'bi-folder', 'desc' => 'A4 Sunum Dosyası'],
    'kase'        => ['name' => 'Kaşe', 'icon' => 'bi-stamp', 'desc' => 'Otomatik Kaşe'],
    'genel'       => ['name' => 'Genel Matbaa', 'icon' => 'bi-printer', 'desc' => 'Tüm Ürünler']
];

$pageTitle = 'Sektörel Vektörel Şablon Yönetimi';
require_once __DIR__ . '/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-vector-pen text-primary me-2"></i>Sektörel Vektörel Şablon Yönetimi</h4>
        <p class="text-muted small mb-0">Ürün tipine (Kartvizit, Dikey Broşür, Dosya vb.) ve sektörlere göre SVG formatındaki hazır tasarımları yönetin.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="#templateFormCard" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
            <i class="bi bi-plus-lg me-1"></i> Yeni Şablon Ekle
        </a>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 🔍 FİLTRELEME ÇUBUĞU (Ürün Türü & Sektör Filtreleri) -->
<!-- ========================================================================= -->
<div class="card border-0 shadow-xs rounded-4 p-3 bg-white mb-4">
    <!-- 1. Ürün Türü Filtresi (Kartvizit vs Broşür vs Dosya) -->
    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom flex-wrap">
        <span class="small fw-bold text-dark"><i class="bi bi-grid-fill text-primary me-1"></i> Ürün Türü:</span>
        <a href="<?= SITE_URL ?>/admin/templates.php?type=all<?= $selectedIndustry !== 'all' ? '&industry=' . urlencode($selectedIndustry) : '' ?>" class="btn btn-sm <?= $selectedType === 'all' ? 'btn-dark' : 'btn-outline-secondary' ?> rounded-pill px-3">
            Tüm Ürün Türleri (<?= $db->query("SELECT COUNT(*) FROM design_templates WHERE status = 1")->fetchColumn() ?>)
        </a>
        <?php foreach ($productTypeLabels as $pTypeKey => $pTypeCfg): 
            $typeCount = $db->query("SELECT COUNT(*) FROM design_templates WHERE product_type = '{$pTypeKey}' AND status = 1")->fetchColumn();
        ?>
            <a href="<?= SITE_URL ?>/admin/templates.php?type=<?= $pTypeKey ?><?= $selectedIndustry !== 'all' ? '&industry=' . urlencode($selectedIndustry) : '' ?>" class="btn btn-sm <?= $selectedType === $pTypeKey ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3 text-nowrap">
                <i class="bi <?= $pTypeCfg['icon'] ?> me-1"></i> <?= $pTypeCfg['name'] ?> (<?= $typeCount ?>)
            </a>
        <?php endforeach; ?>
    </div>

    <!-- 2. Sektör Filtresi -->
    <div class="d-flex align-items-center gap-1 overflow-x-auto pb-1" style="white-space: nowrap;">
        <span class="small fw-bold text-dark me-2"><i class="bi bi-funnel-fill text-primary me-1"></i> Sektör:</span>
        <a href="<?= SITE_URL ?>/admin/templates.php?industry=all<?= $selectedType !== 'all' ? '&type=' . urlencode($selectedType) : '' ?>" class="btn btn-sm <?= $selectedIndustry === 'all' ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3">
            Tüm Sektörler
        </a>
        <?php foreach ($industries as $ind): 
            $indCount = $db->query("SELECT COUNT(*) FROM design_templates WHERE industry_slug = '{$ind['slug']}' AND status = 1" . ($selectedType !== 'all' ? " AND product_type = '{$selectedType}'" : ""))->fetchColumn();
        ?>
            <a href="<?= SITE_URL ?>/admin/templates.php?industry=<?= $ind['slug'] ?><?= $selectedType !== 'all' ? '&type=' . urlencode($selectedType) : '' ?>" class="btn btn-sm <?= $selectedIndustry === $ind['slug'] ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3 text-nowrap">
                <i class="bi <?= $ind['icon'] ?> me-1"></i> <?= htmlspecialchars($ind['name']) ?> (<?= $indCount ?>)
            </a>
        <?php endforeach; ?>
    </div>
</div>

<div class="row g-4">
    
    <!-- ========================================================================= -->
    <!-- ✏️ SOL KOLON: YENİ / DÜZENLEME ŞABLON FORMU -->
    <!-- ========================================================================= -->
    <div class="col-xl-5 col-lg-6">
        <div class="card border-0 shadow-xs rounded-4 p-4 bg-white sticky-top" style="top: 25px;" id="templateFormCard">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi <?= $editTemplate ? 'bi-pencil-square text-warning' : 'bi-plus-circle text-primary' ?> me-2"></i>
                    <?= $editTemplate ? 'Şablonu Düzenle (ID: #' . $editTemplate['id'] . ')' : 'Yeni Sektörel Şablon Ekle' ?>
                </h6>
                <?php if ($editTemplate): ?>
                    <a href="<?= SITE_URL ?>/admin/templates.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Yeni Ekle Modu</a>
                <?php endif; ?>
            </div>
            
            <form action="<?= SITE_URL ?>/admin/templates.php" method="POST" id="templateCreateForm">
                <input type="hidden" name="template_id" value="<?= $editTemplate['id'] ?? 0 ?>">

                <div class="row g-3 mb-3">
                    <!-- 1. Ürün Türü & Ebat Seçimi -->
                    <div class="col-sm-6">
                        <label class="form-label small fw-bold">1. Şablon Ürün Türü <span class="text-danger">*</span></label>
                        <select name="product_type" id="formProductType" class="form-select" required onchange="onProductTypeChange(this.value)">
                            <option value="kartvizit" <?= ($editTemplate['product_type'] ?? 'kartvizit') === 'kartvizit' ? 'selected' : '' ?>>📇 Kartvizit (85x50 mm)</option>
                            <option value="brosur" <?= ($editTemplate['product_type'] ?? '') === 'brosur' ? 'selected' : '' ?>>📄 El İlanı & Broşür (A5 Dikey)</option>
                            <option value="cepli-dosya" <?= ($editTemplate['product_type'] ?? '') === 'cepli-dosya' ? 'selected' : '' ?>>📁 Cepli Dosya (A4)</option>
                            <option value="kase" <?= ($editTemplate['product_type'] ?? '') === 'kase' ? 'selected' : '' ?>>🔲 Kaşe Şablonu</option>
                            <option value="genel" <?= ($editTemplate['product_type'] ?? '') === 'genel' ? 'selected' : '' ?>>🌐 Genel Kurumsal</option>
                        </select>
                    </div>

                    <!-- 2. Hedef Sektör -->
                    <div class="col-sm-6">
                        <label class="form-label small fw-bold">2. Hedef Sektör <span class="text-danger">*</span></label>
                        <select name="industry_slug" class="form-select" required>
                            <?php foreach ($industries as $ind): ?>
                                <option value="<?= $ind['slug'] ?>" <?= (($editTemplate['industry_slug'] ?? $selectedIndustry) === $ind['slug']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ind['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- 3. Spesifik Bağlı Ürün (İsteğe Bağlı) -->
                    <div class="col-12">
                        <label class="form-label small fw-bold">Bağlı Olduğu Ürün (İsteğe Bağlı)</label>
                        <select name="product_id" class="form-select form-select-sm">
                            <option value="0">Tüm Uygun Ürünlerde Göster (Varsayılan)</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= (($editTemplate['product_id'] ?? 0) == $p['id']) ? 'selected' : '' ?>>
                                    Sadece: <?= htmlspecialchars($p['name']) ?> (ID: #<?= $p['id'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- 4. Şablon Başlığı & Stil -->
                    <div class="col-sm-7">
                        <label class="form-label small fw-bold">Şablon Başlığı <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="formTemplateTitle" class="form-control" placeholder="Örn: Hukuk VIP Altın Prestij" value="<?= htmlspecialchars($editTemplate['title'] ?? '') ?>" required>
                    </div>

                    <div class="col-sm-5">
                        <label class="form-label small fw-bold">Tasarım Stili</label>
                        <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($editTemplate['category'] ?? 'Modern Minimal') ?>" placeholder="VIP, Minimal, Dark...">
                    </div>

                    <!-- 5. Tuval Ebatları (Canvas Width / Height) -->
                    <div class="col-sm-6">
                        <label class="form-label small fw-bold">Genişlik (px)</label>
                        <input type="number" name="canvas_width" id="formCanvasWidth" class="form-control form-control-sm" value="<?= $editTemplate['canvas_width'] ?? 850 ?>" required>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label small fw-bold">Yükseklik (px)</label>
                        <input type="number" name="canvas_height" id="formCanvasHeight" class="form-control form-control-sm" value="<?= $editTemplate['canvas_height'] ?? 500 ?>" required>
                    </div>

                    <!-- ⚡ Hızlı Örnek Taslak Doldurma Butonu -->
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded-3 border">
                            <span class="small fw-semibold text-muted" style="font-size: 11px;">Hızlı Başlangıç Taslağı:</span>
                            <button type="button" class="btn btn-xs btn-outline-primary py-1 px-3 rounded-pill" style="font-size: 11px;" onclick="loadSampleSvgTemplate()">
                                <i class="bi bi-magic me-1"></i> Bu Ürün Tipine Göre Örnek SVG Doldur
                            </button>
                        </div>
                    </div>

                    <!-- 6. Vektörel SVG Kodu -->
                    <div class="col-12">
                        <label class="form-label small fw-bold">Vektörel SVG Kodu <span class="text-danger">*</span></label>
                        <textarea name="default_svg" id="formSvgCode" class="form-control font-monospace small" rows="7" placeholder='<svg viewBox="0 0 850 500">...</svg>' required><?= htmlspecialchars($editTemplate['default_svg'] ?? '') ?></textarea>
                        <small class="text-muted d-block mt-1" style="font-size: 11px;">Metin etiketlerine düzenlenebilmesi için <code>id="personName"</code>, <code>id="companyName"</code>, <code>id="phone"</code> gibi ID'ler verin.</small>
                    </div>

                    <!-- 7. Dinamik Alanlar JSON -->
                    <div class="col-12">
                        <label class="form-label small fw-bold">Dinamik Alanlar (JSON Tanımı)</label>
                        <textarea name="template_data" id="formTemplateData" class="form-control font-monospace small" rows="3" placeholder='{"fields":[{"id":"personName","label":"Ad Soyad","default":"Ahmet Yılmaz"},{"id":"phone","label":"Telefon","default":"+90 555 123 45 67"}]}'><?= htmlspecialchars($editTemplate['template_data'] ?? '') ?></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm rounded-pill">
                    <i class="bi bi-save me-1"></i> <?= $editTemplate ? 'Değişiklikleri Kaydet' : 'Şablonu Oluştur & Yayınla' ?>
                </button>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 📋 SAĞ KOLON: KAYITLI ŞABLONLAR KART LİSTESİ -->
    <!-- ========================================================================= -->
    <div class="col-xl-7 col-lg-6">
        <div class="card border-0 shadow-xs rounded-4 p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2 flex-wrap gap-2">
                <h6 class="fw-bold mb-0 text-dark">
                    <span>Kayıtlı Şablonlar</span>
                    <span class="badge bg-primary-subtle text-primary border rounded-pill ms-2"><?= count($templates) ?> Şablon</span>
                </h6>
                <div class="text-muted small">
                    <?php if ($selectedIndustry !== 'all'): ?>
                        Sektör: <strong><?= htmlspecialchars($templates[0]['industry_name'] ?? $selectedIndustry) ?></strong>
                    <?php endif; ?>
                    <?php if ($selectedType !== 'all'): ?>
                        • Tür: <strong><?= htmlspecialchars($productTypeLabels[$selectedType]['name'] ?? $selectedType) ?></strong>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (empty($templates)): ?>
                <div class="alert alert-info py-4 text-center rounded-4 border">
                    <i class="bi bi-info-circle fs-3 d-block mb-2 text-info"></i>
                    Seçilen filtre kriterlerine uygun şablon bulunamadı.<br>
                    Soldaki formdan hemen yeni bir şablon oluşturabilirsiniz.
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($templates as $tpl): 
                        $pType = $tpl['product_type'] ?? 'kartvizit';
                        $typeLabel = $productTypeLabels[$pType]['name'] ?? ucfirst($pType);
                    ?>
                        <div class="col-md-6 col-12">
                            <div class="card border rounded-4 p-3 bg-light h-100 d-flex flex-column shadow-xs hover-shadow" style="transition: all 0.2s;">
                                
                                <!-- Canlı SVG Önizleme Alanı -->
                                <div class="template-preview-box rounded-3 mb-2 bg-white p-2 border shadow-2xs position-relative" style="height: 140px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                                        <?= $tpl['default_svg'] ?>
                                    </div>
                                    <span class="badge bg-dark position-absolute top-0 end-0 m-2 opacity-75" style="font-size: 9px;">
                                        <?= $tpl['canvas_width'] ?>x<?= $tpl['canvas_height'] ?>
                                    </span>
                                </div>

                                <!-- Başlık & Rozetler -->
                                <div class="fw-bold text-dark text-truncate mb-1" title="<?= htmlspecialchars($tpl['title']) ?>">
                                    <?= htmlspecialchars($tpl['title']) ?>
                                </div>

                                <div class="d-flex flex-wrap gap-1 mb-2">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 10px;">
                                        <i class="bi <?= $tpl['industry_icon'] ?? 'bi-briefcase' ?> me-1"></i> <?= htmlspecialchars($tpl['industry_name'] ?? $tpl['industry_slug']) ?>
                                    </span>
                                    <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 10px;">
                                        <i class="bi <?= $productTypeLabels[$pType]['icon'] ?? 'bi-tag' ?> me-1"></i> <?= $typeLabel ?>
                                    </span>
                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size: 10px;">
                                        <?= htmlspecialchars($tpl['category'] ?: 'Standart') ?>
                                    </span>
                                </div>
                                
                                <!-- Alt Bilgi & Aksiyonlar -->
                                <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                                    <span class="small text-muted" style="font-size: 11px;">
                                        <?= !empty($tpl['product_name']) ? htmlspecialchars($tpl['product_name']) : 'Tüm ' . $typeLabel ?>
                                    </span>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= SITE_URL ?>/admin/templates.php?action=edit&id=<?= $tpl['id'] ?>&industry=<?= urlencode($selectedIndustry) ?>&type=<?= urlencode($selectedType) ?>" class="btn btn-outline-primary py-0 px-2" title="Düzenle">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="<?= SITE_URL ?>/admin/templates.php?action=delete&id=<?= $tpl['id'] ?>&industry=<?= urlencode($selectedIndustry) ?>&type=<?= urlencode($selectedType) ?>" class="btn btn-outline-danger py-0 px-2" onclick="return confirm('Bu şablonu silmek istediğinize emin misiniz?');" title="Sil">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- ========================================================================= -->
<!-- 🚀 JS: OTOMATİK SVG ÖRNEK TASLAKLARI DOLDURUCU -->
<!-- ========================================================================= -->
<script>
function onProductTypeChange(type) {
    const widthInput = document.getElementById('formCanvasWidth');
    const heightInput = document.getElementById('formCanvasHeight');
    
    if (type === 'brosur') {
        widthInput.value = 500;
        heightInput.value = 700;
    } else if (type === 'cepli-dosya') {
        widthInput.value = 600;
        heightInput.value = 850;
    } else if (type === 'kase') {
        widthInput.value = 500;
        heightInput.value = 300;
    } else {
        // kartvizit
        widthInput.value = 850;
        heightInput.value = 500;
    }
}

function loadSampleSvgTemplate() {
    const type = document.getElementById('formProductType').value;
    const svgArea = document.getElementById('formSvgCode');
    const dataArea = document.getElementById('formTemplateData');
    const titleInput = document.getElementById('formTemplateTitle');

    if (type === 'brosur') {
        titleInput.value = titleInput.value || 'Örnek A5 Dikey Tanıtım Broşürü';
        svgArea.value = `<svg viewBox="0 0 500 700" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
  <rect width="500" height="700" fill="#0f172a" rx="8" />
  <path d="M 0 0 L 500 0 L 500 160 L 0 220 Z" fill="#0071e3" opacity="0.9" />
  <text id="companyName" x="40" y="80" fill="#ffffff" font-size="28" font-family="sans-serif" font-weight="bold">ŞİRKETİNİZ &amp; MARKA</text>
  <text id="tagline" x="40" y="115" fill="#ffffff" font-size="14" font-family="sans-serif">Kurumsal Hizmetler &amp; Çözümler</text>
  <rect x="35" y="250" width="430" height="180" rx="12" fill="#ffffff" opacity="0.08" />
  <text id="campaignTitle" x="250" y="300" fill="#38bdf8" font-size="22" font-family="sans-serif" font-weight="bold" text-anchor="middle">ÖZEL FIRSAT KAMPANYASI</text>
  <text id="campaignDesc1" x="250" y="340" fill="#e2e8f0" font-size="14" font-family="sans-serif" text-anchor="middle">• Hızlı ve Garantili Hizmet</text>
  <text id="campaignDesc2" x="250" y="370" fill="#e2e8f0" font-size="14" font-family="sans-serif" text-anchor="middle">• %100 Müşteri Memnuniyeti</text>
  <rect x="35" y="460" width="430" height="190" rx="12" fill="#000000" opacity="0.3" />
  <text id="phone" x="250" y="540" fill="#ffffff" font-size="20" font-family="sans-serif" font-weight="bold" text-anchor="middle">0555 123 45 67</text>
  <text id="website" x="250" y="580" fill="#38bdf8" font-size="14" font-family="sans-serif" text-anchor="middle">www.sirketiniz.com</text>
</svg>`;
        dataArea.value = JSON.stringify({
            fields: [
                { id: 'companyName', label: 'Şirket Adı', default: 'ŞİRKETİNİZ & MARKA' },
                { id: 'tagline', label: 'Slogan', default: 'Kurumsal Hizmetler & Çözümler' },
                { id: 'campaignTitle', label: 'Kampanya Başlığı', default: 'ÖZEL FIRSAT KAMPANYASI' },
                { id: 'campaignDesc1', label: 'Madde 1', default: '• Hızlı ve Garantili Hizmet' },
                { id: 'campaignDesc2', label: 'Madde 2', default: '• %100 Müşteri Memnuniyeti' },
                { id: 'phone', label: 'Telefon', default: '0555 123 45 67' },
                { id: 'website', label: 'Web Sitesi', default: 'www.sirketiniz.com' }
            ]
        }, null, 2);
    } else {
        // Yatay Kartvizit
        titleInput.value = titleInput.value || 'Örnek Yatay Kartvizit Tasarımı';
        svgArea.value = `<svg viewBox="0 0 850 500" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
  <rect width="850" height="500" fill="#ffffff" rx="8" />
  <path d="M 0 0 L 260 0 L 160 500 L 0 500 Z" fill="#0071e3" />
  <text id="companyName" x="300" y="120" fill="#0f172a" font-size="36" font-family="sans-serif" font-weight="bold">ŞİRKETİNİZ</text>
  <text id="personName" x="300" y="240" fill="#0f172a" font-size="28" font-family="sans-serif" font-weight="bold">Ad Soyad</text>
  <text id="personTitle" x="300" y="275" fill="#64748b" font-size="16" font-family="sans-serif">Genel Müdür / Kurucu</text>
  <line x1="300" y1="310" x2="780" y2="310" stroke="#e2e8f0" stroke-width="2" />
  <text id="phone" x="300" y="360" fill="#334155" font-size="16" font-family="sans-serif">📞 0555 123 45 67</text>
  <text id="email" x="300" y="400" fill="#334155" font-size="16" font-family="sans-serif">✉️ info@sirketiniz.com</text>
  <text id="website" x="300" y="440" fill="#0071e3" font-size="16" font-family="sans-serif">🌐 www.sirketiniz.com</text>
</svg>`;
        dataArea.value = JSON.stringify({
            fields: [
                { id: 'companyName', label: 'Şirket Adı', default: 'ŞİRKETİNİZ' },
                { id: 'personName', label: 'Ad Soyad', default: 'Ad Soyad' },
                { id: 'personTitle', label: 'Unvan', default: 'Genel Müdür / Kurucu' },
                { id: 'phone', label: 'Telefon', default: '📞 0555 123 45 67' },
                { id: 'email', label: 'E-Posta', default: '✉️ info@sirketiniz.com' },
                { id: 'website', label: 'Web Sitesi', default: '🌐 www.sirketiniz.com' }
            ]
        }, null, 2);
    }
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
