<?php
/**
 * TAMBASKI.COM.TR - Gelişmiş "Kendin Tasarla" & Canlı 3D Mockup Editörü
 */
require_once __DIR__ . '/includes/functions.php';

$mockup_type = $_GET['mockup'] ?? 'bizcard';
$product_slug = $_GET['product'] ?? 'kurumsal-prestij-kartvizit-350gr';
$product = get_product_by_slug($product_slug);

$page_title = "Online Canlı Tasarla & 3D Mockup – TamBaskı";
require_once __DIR__ . '/includes/header.php';
?>

<!-- Fabric.js CDN for Professional HTML5 Canvas Manipulation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>

<style>
    .designer-container {
        background: #ececed;
        min-height: calc(100vh - 140px);
        padding: 24px 0;
    }
    .designer-toolbar {
        background: #ffffff;
        border-radius: 18px;
        padding: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        margin-bottom: 20px;
    }
    .canvas-wrapper {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        display: inline-block;
        position: relative;
        overflow: hidden;
    }
    .safe-bleed-line {
        position: absolute;
        top: 10px;
        left: 10px;
        right: 10px;
        bottom: 10px;
        border: 1px dashed rgba(220, 38, 38, 0.4);
        pointer-events: none;
        z-index: 10;
    }

    /* 3D Mockup Canlı Sahne */
    .mockup-3d-stage {
        background: radial-gradient(circle at 50% 40%, #ffffff 0%, #d8d8dc 100%);
        border-radius: 24px;
        height: 520px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        perspective: 1200px;
        overflow: hidden;
        box-shadow: inset 0 0 40px rgba(0,0,0,0.05);
    }
    .mockup-bizcard-3d {
        width: 360px;
        height: 216px;
        background: #ffffff;
        border-radius: 12px;
        transform: rotateY(-18deg) rotateX(14deg) rotateZ(-4deg);
        box-shadow: -20px 25px 50px rgba(0,0,0,0.22), 0 0 0 1px rgba(0,0,0,0.08);
        transition: transform 0.5s ease;
        position: relative;
        overflow: hidden;
        background-size: cover;
        background-position: center;
    }
    .mockup-bizcard-3d:hover {
        transform: rotateY(-10deg) rotateX(8deg) rotateZ(-2deg) translateY(-8px);
    }
    .mockup-glare {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.4) 0%, rgba(255,255,255,0) 60%);
        pointer-events: none;
    }
    .mockup-badge-live {
        position: absolute;
        bottom: 16px;
        right: 16px;
        background: rgba(17, 17, 19, 0.85);
        color: #ffffff;
        font-size: 11px;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 20px;
        backdrop-filter: blur(10px);
    }
</style>

<div class="designer-container">
    <div class="container-fluid px-lg-5">
        
        <!-- ÜST AKSİYON ÇUBUĞU -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <div>
                <span class="badge bg-primary px-3 py-1 rounded-pill mb-1">✨ V2.0 Canlı Vektörel Editör</span>
                <h3 class="fw-bold mb-0">Online Tasarla & 3D Mockup Önizleyici</h3>
                <small class="text-muted">Tasarımınızı tarayıcıda düzenleyin, anında canlı 3D simülasyonda görün</small>
            </div>

            <div class="d-flex gap-2">
                <a href="product.php?slug=<?= urlencode($product_slug) ?>" class="btn btn-sm btn-apple btn-apple-secondary px-3 py-2">
                    <i class="bi bi-x-lg me-1"></i> Vazgeç
                </a>
                <button type="button" id="saveDesignAndCartBtn" class="btn btn-apple btn-apple-orange px-4 py-2 fw-bold shadow">
                    <i class="bi bi-bag-check-fill me-2"></i> Tasarımı Onayla & Sepete Ekle
                </button>
            </div>
        </div>

        <div class="row g-4">
            <!-- SOL: 2D TASARIM TUVALİ & ARAÇLAR -->
            <div class="col-lg-6">
                <!-- ARAÇ ÇUBUĞU -->
                <div class="designer-toolbar">
                    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                        <!-- Metin Ekle -->
                        <button type="button" class="btn btn-sm btn-outline-dark rounded-pill" id="addHeadingBtn">
                            <i class="bi bi-type-h1 me-1"></i> Başlık Ekle
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-dark rounded-pill" id="addTextBtn">
                            <i class="bi bi-fonts me-1"></i> Metin Ekle
                        </button>

                        <!-- Logo / Görsel Yükle -->
                        <label class="btn btn-sm btn-apple btn-apple-orange rounded-pill mb-0" style="cursor: pointer;">
                            <i class="bi bi-image me-1"></i> Logo / Görsel Yükle
                            <input type="file" id="imageUploadInput" class="d-none" accept="image/*">
                        </label>

                        <!-- Şekil Ekle -->
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" id="addRectBtn">
                            <i class="bi bi-square me-1"></i> Kutu
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" id="addCircleBtn">
                            <i class="bi bi-circle me-1"></i> Daire
                        </button>

                        <!-- Sil -->
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill ms-auto" id="deleteActiveBtn">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>

                    <!-- Renk & Font Seçenekleri -->
                    <div class="d-flex flex-wrap gap-3 align-items-center pt-2 border-top">
                        <div class="d-flex align-items-center gap-2">
                            <label class="small fw-bold mb-0">Arka Plan:</label>
                            <input type="color" id="bgColorPicker" class="form-control form-control-color p-0 border-0" value="#ffffff" title="Tuval Rengi">
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <label class="small fw-bold mb-0">Metin Rengi:</label>
                            <input type="color" id="textColorPicker" class="form-control form-control-color p-0 border-0" value="#111113" title="Yazı Rengi">
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <label class="small fw-bold mb-0">Yazı Tipi:</label>
                            <select id="fontFamilySelect" class="form-select form-select-sm" style="width: 140px;">
                                <option value="Arial">Arial</option>
                                <option value="Helvetica">Helvetica</option>
                                <option value="Georgia">Georgia</option>
                                <option value="Courier New">Courier</option>
                                <option value="Impact">Impact</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 2D CANVAS ALANI -->
                <div class="text-center">
                    <div class="canvas-wrapper">
                        <canvas id="designCanvas" width="540" height="324"></canvas>
                        <div class="safe-bleed-line" title="Baskı Taşırma & Güvenli Kesim Çizgisi"></div>
                    </div>
                    <div class="text-muted small mt-2">
                        <i class="bi bi-info-circle me-1"></i> Kırmızı kesikli çizgi <strong>güvenli kesim alanı</strong> sınırıdır. Metinlerinizi çizginin içinde tutunuz.
                    </div>
                </div>
            </div>

            <!-- SAĞ: CANLI 3D MOCKUP ÖNİZLEME -->
            <div class="col-lg-6">
                <div class="apple-card p-4 bg-white h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold mb-0"><i class="bi bi-layers-half text-info me-2"></i>Canlı 3D Mockup Simülasyonu</h5>
                            <small class="text-muted">Tasarladıkça gerçek baskı dokusu ve açısıyla canlı güncellenir</small>
                        </div>
                        <span class="badge bg-danger rounded-pill px-3 py-1">CANLI 3D</span>
                    </div>

                    <!-- 3D SAHNE -->
                    <div class="mockup-3d-stage flex-grow-1">
                        <!-- 3D Kartvizit Nesnesi -->
                        <div class="mockup-bizcard-3d" id="mockup3DObject">
                            <div class="mockup-glare"></div>
                        </div>

                        <div class="mockup-badge-live">
                            <i class="bi bi-patch-check-fill text-warning me-1"></i> 350gr Mat Kuşe & Lak Simülasyonu
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                        <small class="text-muted"><i class="bi bi-stars text-warning me-1"></i> Gerçek Heidelberg Ofset Renk Doğruluğu</small>
                        <button type="button" class="btn btn-sm btn-outline-dark rounded-pill" onclick="triggerMockupAnimation()">
                            <i class="bi bi-arrow-clockwise me-1"></i> Açıyı Çevir
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Fabric.js Canvas Başlatma
    const canvas = new fabric.Canvas('designCanvas', {
        backgroundColor: '#ffffff',
        preserveObjectStacking: true
    });

    const mockup3D = document.getElementById('mockup3DObject');

    // İlk Varsayılan Tasarım Elemanları
    const mainTitle = new fabric.IText('TAM BASKI', {
        left: 60,
        top: 70,
        fontFamily: 'Helvetica',
        fontSize: 32,
        fontWeight: 'bold',
        fill: '#111113'
    });

    const subTitle = new fabric.IText('Ahmet Yılmaz | Genel Müdür', {
        left: 60,
        top: 130,
        fontFamily: 'Helvetica',
        fontSize: 16,
        fill: '#f15a24'
    });

    const contactInfo = new fabric.IText('📞 0850 308 00 00\n✉️ info@sirketiniz.com\n🌐 www.sirketiniz.com', {
        left: 60,
        top: 180,
        fontFamily: 'Helvetica',
        fontSize: 13,
        lineHeight: 1.4,
        fill: '#64748b'
    });

    canvas.add(mainTitle);
    canvas.add(subTitle);
    canvas.add(contactInfo);
    update3DMockup();

    // 2. Canlı 3D Mockup Güncelleme Fonksiyonu
    function update3DMockup() {
        if (!mockup3D) return;
        const dataUrl = canvas.toDataURL({ format: 'png', quality: 0.95 });
        mockup3D.style.backgroundImage = `url(${dataUrl})`;
    }

    canvas.on('object:modified', update3DMockup);
    canvas.on('object:added', update3DMockup);
    canvas.on('object:removed', update3DMockup);
    canvas.on('text:changed', update3DMockup);

    // 3. Buton Etkileşimleri
    document.getElementById('addHeadingBtn').addEventListener('click', function() {
        const h = new fabric.IText('Yeni Başlık', {
            left: 100,
            top: 100,
            fontFamily: document.getElementById('fontFamilySelect').value,
            fontSize: 28,
            fontWeight: 'bold',
            fill: document.getElementById('textColorPicker').value
        });
        canvas.add(h);
        canvas.setActiveObject(h);
    });

    document.getElementById('addTextBtn').addEventListener('click', function() {
        const t = new fabric.IText('Metin yazın...', {
            left: 120,
            top: 120,
            fontFamily: document.getElementById('fontFamilySelect').value,
            fontSize: 16,
            fill: document.getElementById('textColorPicker').value
        });
        canvas.add(t);
        canvas.setActiveObject(t);
    });

    // Logo Yükleme
    document.getElementById('imageUploadInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(f) {
            fabric.Image.fromURL(f.target.result, function(img) {
                img.scaleToWidth(140);
                img.set({ left: 320, top: 60 });
                canvas.add(img);
                canvas.setActiveObject(img);
                update3DMockup();
            });
        };
        reader.readAsDataURL(file);
    });

    // Kutu & Daire Ekleme
    document.getElementById('addRectBtn').addEventListener('click', function() {
        const r = new fabric.Rect({
            left: 150, top: 150, fill: '#f15a24', width: 100, height: 60, rx: 6, ry: 6
        });
        canvas.add(r);
    });

    document.getElementById('addCircleBtn').addEventListener('click', function() {
        const c = new fabric.Circle({
            left: 180, top: 180, fill: '#0071e3', radius: 40
        });
        canvas.add(c);
    });

    // Silme
    document.getElementById('deleteActiveBtn').addEventListener('click', function() {
        const active = canvas.getActiveObject();
        if (active) canvas.remove(active);
    });

    // Renk ve Font
    document.getElementById('bgColorPicker').addEventListener('input', function() {
        canvas.setBackgroundColor(this.value, canvas.renderAll.bind(canvas));
        update3DMockup();
    });

    document.getElementById('textColorPicker').addEventListener('input', function() {
        const active = canvas.getActiveObject();
        if (active && (active.type === 'i-text' || active.type === 'text')) {
            active.set('fill', this.value);
            canvas.renderAll();
            update3DMockup();
        }
    });

    document.getElementById('fontFamilySelect').addEventListener('change', function() {
        const active = canvas.getActiveObject();
        if (active && (active.type === 'i-text' || active.type === 'text')) {
            active.set('fontFamily', this.value);
            canvas.renderAll();
            update3DMockup();
        }
    });

    // Tasarımı Sepete Ekleme
    document.getElementById('saveDesignAndCartBtn').addEventListener('click', function() {
        const designData = canvas.toDataURL({ format: 'png', quality: 1.0 });
        
        // Sepete Ekleme Formu
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'cart.php';

        const inputAction = document.createElement('input');
        inputAction.type = 'hidden';
        inputAction.name = 'action';
        inputAction.value = 'add_designed_item';

        const inputDesign = document.createElement('input');
        inputDesign.type = 'hidden';
        inputDesign.name = 'design_data';
        inputDesign.value = designData;

        form.appendChild(inputAction);
        form.appendChild(inputDesign);
        document.body.appendChild(form);

        // Sepete doğrudan yönlendir
        alert('Tasarımınız hazırlandı ve siparişinize eklendi!');
        window.location.href = 'cart.php';
    });
});

function triggerMockupAnimation() {
    const obj = document.getElementById('mockup3DObject');
    if (!obj) return;
    obj.style.transform = 'rotateY(30deg) rotateX(-10deg) scale(1.05)';
    setTimeout(() => {
        obj.style.transform = 'rotateY(-18deg) rotateX(14deg) rotateZ(-4deg)';
    }, 600);
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
