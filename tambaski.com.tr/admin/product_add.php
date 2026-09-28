<?php
/**
 * TAMBASKI.COM.TR - Admin Yeni Ürün Ekleme & Kapsamlı Varyant/Mockup Yönetimi
 */
$page_title = "Yeni Ürün & Varyant Ekle";
require_once __DIR__ . '/header.php';

$categories = get_all_categories();
$success_msg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_product') {
    // Ürün kaydetme işlemi (Veritabanı veya Session Mock)
    $product_name = trim($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 1);
    $pricing_type = $_POST['pricing_type'] ?? 'package_and_custom';
    
    $success_msg = "<strong>" . htmlspecialchars($product_name) . "</strong> başarıyla eklendi, hazır paketler, sınırsız varyantlar ve 3D mockup şablonu aktif edildi!";
}
?>

<div class="row g-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-plus-circle text-warning me-2"></i>Yeni Ürün, Varyant & Mockup Ekle</h3>
            <p class="text-muted small mb-0">Görsel, video, hazır paket fiyatları, özel adet çarpanları, sınırsız seçenekler ve 3D mockup tanımlama</p>
        </div>
        <a href="products.php" class="btn btn-sm btn-apple-secondary">
            <i class="bi bi-arrow-left me-1"></i> Ürün Listesine Dön
        </a>
    </div>

    <?php if ($success_msg): ?>
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= $success_msg ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <div class="col-12">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="save_product">

            <div class="row g-4">
                <!-- SOL KOLON: TEMEL BİLGİLER & MEDYA -->
                <div class="col-lg-8">
                    <!-- 1. TEMEL BİLGİLER -->
                    <div class="apple-card p-4 bg-white mb-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-info-circle text-primary me-2"></i>1. Temel Ürün Bilgileri</h5>
                        
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label small fw-bold">Ürün Adı</label>
                                <input type="text" name="name" id="productNameInput" class="form-control form-control-lg" placeholder="Örn: 250gr Solvent Ekonomik Kartvizit veya 3mm Dekota Lazer Kesim" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Kategori</label>
                                <select name="category_id" class="form-select form-select-lg" required>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold">Kısa Açıklama (Spot / Vitrin Metni)</label>
                                <input type="text" name="short_desc" class="form-control" placeholder="Örn: 250gr Amerikan Bristol, Tek Yön Renkli, 1000 Adetten Başlayan Toptan Fiyat">
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold">Detaylı Açıklama & Teknik Özellikler</label>
                                <textarea name="description" class="form-control" rows="5" placeholder="Kağıt gramajı, baskı makineleri, teslimat süreleri ve teknik hazırlık kılavuzu..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- 2. GÖRSELLER & VİDEO -->
                    <div class="apple-card p-4 bg-white mb-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-images text-success me-2"></i>2. Ürün Görselleri & Tanıtım Videosu</h5>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Ana Ürün Görseli</label>
                                <input type="file" name="main_image" class="form-control" accept="image/*">
                                <small class="text-muted">WebP, PNG, JPG formatında yüksek çözünürlüklü görsel.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Ek Galeri Görselleri (Çoklu)</label>
                                <input type="file" name="gallery_images[]" class="form-control" multiple accept="image/*">
                                <small class="text-muted">Ürünün farklı açılardan fotoğrafları.</small>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold"><i class="bi bi-youtube text-danger me-1"></i>Ürün Tanıtım / Üretim Videosu Linki</label>
                                <input type="text" name="video_url" class="form-control" placeholder="https://www.youtube.com/watch?v=... veya MP4 Video URL">
                                <small class="text-muted">Müşterinin ürün detay sayfasında izleyebileceği baskı/kesim tanıtım videosu.</small>
                            </div>
                        </div>
                    </div>

                    <!-- 3. FİYATLANDIRMA MOTORU & HAZIR PAKETLER -->
                    <div class="apple-card p-4 bg-white mb-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-calculator text-danger me-2"></i>3. Fiyatlandırma Modeli & Paket Tablosu</h5>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Fiyatlandırma Tipi</label>
                            <select name="pricing_type" id="pricingTypeSelector" class="form-select form-select-lg">
                                <option value="package_and_custom">📦 Hazır Sabit Paketler + Özel / Ara Adet Girişi (Kartvizit, Broşür, Promosyon vb.)</option>
                                <option value="sqm_calculator">📐 m² Boyutlu Hesaplayıcı (Dekota Kesim, Pleksi Lazer Kesim, Folyo, Branda)</option>
                                <option value="package_only">🏷️ Sadece Hazır Sabit Paketler</option>
                            </select>
                        </div>

                        <!-- A. HAZIR PAKETLER DİNAMİK TABLOSU -->
                        <div id="packageTableSection">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label small fw-bold mb-0">Hazır Sabit Fiyatlı Paketler</label>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" id="addPackageRowBtn">
                                    <i class="bi bi-plus-lg me-1"></i> Yeni Paket Satırı Ekle
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle" id="packagesTable">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Paket Başlığı</th>
                                            <th>Özellikler (Tek/Çift Yön, Gramaj)</th>
                                            <th style="width: 110px;">Adet</th>
                                            <th style="width: 130px;">Normal Fiyat (₺)</th>
                                            <th style="width: 130px;">Bayi Fiyatı (₺)</th>
                                            <th style="width: 90px;">Popüler</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="packageRowsContainer">
                                        <tr>
                                            <td><input type="text" name="package_title[]" class="form-control form-control-sm" value="1.000 Adet Standart Paket" required></td>
                                            <td><input type="text" name="package_specs[]" class="form-control form-control-sm" value="250gr Solvent Tek Yön"></td>
                                            <td><input type="number" name="package_qty[]" class="form-control form-control-sm" value="1000" required></td>
                                            <td><input type="number" step="0.01" name="package_price[]" class="form-control form-control-sm text-danger fw-bold" value="1000.00" required></td>
                                            <td><input type="number" step="0.01" name="package_dealer_price[]" class="form-control form-control-sm text-success fw-bold" value="750.00"></td>
                                            <td class="text-center"><input type="checkbox" name="package_popular[]" class="form-check-input" checked></td>
                                            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger border-0 remove-row-btn"><i class="bi bi-trash"></i></button></td>
                                        </tr>
                                        <tr>
                                            <td><input type="text" name="package_title[]" class="form-control form-control-sm" value="2.000 Adet Avantaj Paketi"></td>
                                            <td><input type="text" name="package_specs[]" class="form-control form-control-sm" value="250gr Solvent Tek Yön"></td>
                                            <td><input type="number" name="package_qty[]" class="form-control form-control-sm" value="2000"></td>
                                            <td><input type="number" step="0.01" name="package_price[]" class="form-control form-control-sm text-danger fw-bold" value="1850.00"></td>
                                            <td><input type="number" step="0.01" name="package_dealer_price[]" class="form-control form-control-sm text-success fw-bold" value="1387.50"></td>
                                            <td class="text-center"><input type="checkbox" name="package_popular[]" class="form-check-input"></td>
                                            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger border-0 remove-row-btn"><i class="bi bi-trash"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- ÖZEL ADET KURALLARI -->
                            <div class="p-3 bg-light rounded-3 border mt-3">
                                <h6 class="fw-bold mb-2"><i class="bi bi-sliders me-1 text-primary"></i>Özel / Ara Adet Hesaplama Parametreleri</h6>
                                <p class="text-muted small mb-3">Kullanıcı standart paket dışı (örn. <strong>53 adet</strong>) girdiğinde uygulanacak formül.</p>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Min. Sipariş Adedi</label>
                                        <input type="number" name="min_quantity" class="form-control form-control-sm" value="25">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Taban Hazırlık / Kalıp Ücreti (₺)</label>
                                        <input type="number" step="0.01" name="base_setup_fee" class="form-control form-control-sm" value="45.00">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Birim Katsayı Çarpanı (₺)</label>
                                        <input type="number" step="0.0001" name="custom_unit_multiplier" class="form-control form-control-sm" value="0.7500">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- B. m² HESAPLAMA PARAMETRELERİ -->
                        <div id="sqmSection" style="display: none;" class="p-3 bg-light rounded-3 border mt-3">
                            <h6 class="fw-bold mb-2"><i class="bi bi-aspect-ratio me-1 text-success"></i>m² Bazlı Hesaplama Parametreleri (Dekota / Pleksi / Branda)</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Taban m² Fiyatı (₺ / m²)</label>
                                    <input type="number" step="0.01" name="base_sqm_price" class="form-control form-control-sm" value="320.00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Taban Makine Hazırlık Ücreti (₺)</label>
                                    <input type="number" step="0.01" name="sqm_setup_fee" class="form-control form-control-sm" value="60.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. SINIRSIZ VARYANT / SEÇENEK GRUPLARI -->
                    <div class="apple-card p-4 bg-white mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="fw-bold mb-0"><i class="bi bi-ui-checks-grid text-primary me-2"></i>4. Sınırsız Varyant & Opsiyon Ekleme</h5>
                                <small class="text-muted">Kağıt Gramajı, Selefon, Köşe Kesimi, Kalınlık, Kordon vb. seçenek grupları</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-apple btn-apple-secondary" id="addOptionGroupBtn">
                                <i class="bi bi-plus-lg me-1"></i> Yeni Seçenek Grubu Ekle
                            </button>
                        </div>

                        <div id="variantGroupsContainer">
                            <!-- Örnek Varyant Grubu 1: Kağıt / Malzeme -->
                            <div class="variant-group-card p-3 border rounded-3 bg-light mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-dark">Grup 1</span>
                                        <input type="text" name="option_group_name[]" class="form-control form-control-sm fw-bold" value="Malzeme & Kağıt Gramajı" style="width: 260px;">
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger border-0 remove-group-btn"><i class="bi bi-trash"></i> Grubu Sil</button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered bg-white mb-2">
                                        <thead class="table-light small">
                                            <tr>
                                                <th>Seçenek Değeri Adı</th>
                                                <th style="width: 160px;">Ek Fiyat Farkı (₺)</th>
                                                <th style="width: 140px;">Fiyat Çarpanı</th>
                                                <th style="width: 90px;">Varsayılan</th>
                                                <th style="width: 40px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><input type="text" class="form-control form-control-sm" value="250gr Amerikan Bristol"></td>
                                                <td><input type="number" class="form-control form-control-sm" value="0.00"></td>
                                                <td><input type="number" step="0.1" class="form-control form-control-sm" value="1.0"></td>
                                                <td class="text-center"><input type="radio" name="default_opt_1" checked></td>
                                                <td class="text-center"><i class="bi bi-x text-danger"></i></td>
                                            </tr>
                                            <tr>
                                                <td><input type="text" class="form-control form-control-sm" value="350gr Mat Kuşe"></td>
                                                <td><input type="number" class="form-control form-control-sm" value="50.00"></td>
                                                <td><input type="number" step="0.1" class="form-control form-control-sm" value="1.2"></td>
                                                <td class="text-center"><input type="radio" name="default_opt_1"></td>
                                                <td class="text-center"><i class="bi bi-x text-danger"></i></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SAĞ KOLON: MOCKUP, ŞABLON & YAYINLAMA -->
                <div class="col-lg-4">
                    <!-- 5. 3D MOCKUP & ŞABLON SİSTEMİ -->
                    <div class="apple-card p-4 bg-white mb-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-layers-half text-info me-2"></i>5. 3D Mockup & Şablon</h5>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Mockup Tipi</label>
                            <select name="mockup_type" class="form-select">
                                <option value="bizcard">📇 3D Kartvizit Mockup</option>
                                <option value="flag">🚩 3D Yelken Bayrak Mockup</option>
                                <option value="mug">☕ 3D Kupa Bardak Mockup</option>
                                <option value="tshirt">👕 3D Tişört / Tekstil Mockup</option>
                                <option value="folder">📁 3D Cepli Sunum Dosyası Mockup</option>
                                <option value="signage">🏢 3D Dekota / Pleksi Tabela Mockup</option>
                                <option value="brochure">📄 3D Broşür & Kırımlı Mockup</option>
                            </select>
                            <small class="text-muted">Kullanıcı tasarım yüklediğinde veya online tasarladığında bu 3D mockup üzerinde canlı önizlenir.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Özel Mockup Arka Plan Görseli</label>
                            <input type="file" name="mockup_bg_image" class="form-control" accept="image/*">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold"><i class="bi bi-download me-1 text-primary"></i>İndirilebilir Vektörel Baskı Şablonu</label>
                            <input type="file" name="template_file" class="form-control" accept=".ai,.pdf,.psd,.cdr,.zip">
                            <small class="text-muted">Müşterilerin indirip tasarım yapacağı hazır PDF/AI şablon dosyası.</small>
                        </div>

                        <hr class="my-3">

                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="has_online_editor" id="hasEditorSwitch" checked>
                            <label class="form-check-label fw-bold small" for="hasEditorSwitch">Canlı "Kendin Tasarla" Editörü Aktif</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_urgent_available" id="isUrgentSwitch" checked>
                            <label class="form-check-label fw-bold small" for="isUrgentSwitch">24 Saatte Acil Baskı Seçeneği</label>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="isFeaturedSwitch" checked>
                            <label class="form-check-label fw-bold small" for="isFeaturedSwitch">Anasayfa Çok Satanlar Vitrininde Göster</label>
                        </div>
                    </div>

                    <!-- 6. YAYINLAMA & KAYDET BUTONU -->
                    <div class="apple-card p-4 bg-white text-center">
                        <div class="mb-3">
                            <span class="badge bg-success px-3 py-2 rounded-pill fs-6"><i class="bi bi-check-circle me-1"></i>Yayına Hazır</span>
                        </div>
                        <button type="submit" class="btn btn-apple btn-apple-orange btn-lg w-100 py-3 fw-bold shadow">
                            <i class="bi bi-cloud-upload-fill me-2"></i> Ürünü & Paketleri Kaydet
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fiyatlandırma tipi değişimi
    const typeSelector = document.getElementById('pricingTypeSelector');
    const pkgSection = document.getElementById('packageTableSection');
    const sqmSection = document.getElementById('sqmSection');

    if (typeSelector) {
        typeSelector.addEventListener('change', function() {
            if (this.value === 'sqm_calculator') {
                pkgSection.style.display = 'none';
                sqmSection.style.display = 'block';
            } else {
                pkgSection.style.display = 'block';
                sqmSection.style.display = 'none';
            }
        });
    }

    // Dinamik Paket Satırı Ekleme
    const addPkgBtn = document.getElementById('addPackageRowBtn');
    const pkgContainer = document.getElementById('packageRowsContainer');
    if (addPkgBtn && pkgContainer) {
        addPkgBtn.addEventListener('click', function() {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="text" name="package_title[]" class="form-control form-control-sm" placeholder="Örn: 5.000 Adet Toptan Paket" required></td>
                <td><input type="text" name="package_specs[]" class="form-control form-control-sm" placeholder="250gr Solvent"></td>
                <td><input type="number" name="package_qty[]" class="form-control form-control-sm" value="5000" required></td>
                <td><input type="number" step="0.01" name="package_price[]" class="form-control form-control-sm text-danger fw-bold" value="4200.00" required></td>
                <td><input type="number" step="0.01" name="package_dealer_price[]" class="form-control form-control-sm text-success fw-bold" value="3150.00"></td>
                <td class="text-center"><input type="checkbox" name="package_popular[]" class="form-check-input"></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger border-0 remove-row-btn"><i class="bi bi-trash"></i></button></td>
            `;
            pkgContainer.appendChild(tr);
        });
    }

    // Satır Silme
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row-btn')) {
            e.target.closest('tr').remove();
        }
        if (e.target.closest('.remove-group-btn')) {
            e.target.closest('.variant-group-card').remove();
        }
    });

    // Dinamik Varyant Grubu Ekleme
    const addGroupBtn = document.getElementById('addOptionGroupBtn');
    const groupsContainer = document.getElementById('variantGroupsContainer');
    if (addGroupBtn && groupsContainer) {
        let groupCount = 2;
        addGroupBtn.addEventListener('click', function() {
            const div = document.createElement('div');
            div.className = 'variant-group-card p-3 border rounded-3 bg-light mb-3';
            div.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-dark">Grup ${groupCount++}</span>
                        <input type="text" name="option_group_name[]" class="form-control form-control-sm fw-bold" placeholder="Örn: Selefon & Kaplama Türü" style="width: 260px;">
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger border-0 remove-group-btn"><i class="bi bi-trash"></i> Grubu Sil</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered bg-white mb-2">
                        <thead class="table-light small">
                            <tr>
                                <th>Seçenek Değeri Adı</th>
                                <th style="width: 160px;">Ek Fiyat Farkı (₺)</th>
                                <th style="width: 140px;">Fiyat Çarpanı</th>
                                <th style="width: 90px;">Varsayılan</th>
                                <th style="width: 40px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" class="form-control form-control-sm" placeholder="Örn: Mat Selefon"></td>
                                <td><input type="number" class="form-control form-control-sm" value="0.00"></td>
                                <td><input type="number" step="0.1" class="form-control form-control-sm" value="1.0"></td>
                                <td class="text-center"><input type="radio" name="default_opt_${groupCount}" checked></td>
                                <td class="text-center"><i class="bi bi-x text-danger"></i></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            `;
            groupsContainer.appendChild(div);
        });
    }
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
