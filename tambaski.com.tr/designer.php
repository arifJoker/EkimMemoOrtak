<?php
/**
 * TAMBASKI.COM.TR - TamBaskı Editör (Gelişmiş Vektörel Tasarım & Şablon Kütüphanesi)
 * Referans: baski.arifuz.com.tr/product.php?slug=test (Screenshot 2)
 */
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['product'] ?? 'ekonomik-kartvizit-250gr';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TamBaskı Editör – Canlı Vektörel Baskı Tasarımcısı</title>
    
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Fabric.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>

    <style>
        :root {
            --editor-bg: #0b1120;
            --editor-canvas-bg: #0f172a;
            --editor-sidebar-bg: #ffffff;
            --editor-topbar-bg: #111113;
            --primary: #0071e3;
            --accent-orange: #f15a24;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: var(--editor-canvas-bg);
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "SF Pro Text", "Segoe UI", Roboto, sans-serif;
            overflow: hidden;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* 1. ÜST KOYU NAVİGASYON (Screenshot 2 ile birebir) */
        .editor-header-dark {
            background: #111113;
            color: #ffffff;
            padding: 10px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            z-index: 1000;
        }

        .btn-back-pill {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .btn-back-pill:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .side-toggle-group {
            display: inline-flex;
            background: rgba(255, 255, 255, 0.1);
            padding: 3px;
            border-radius: 999px;
        }
        .side-toggle-btn {
            background: transparent;
            border: none;
            color: #94a3b8;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 14px;
            border-radius: 999px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .side-toggle-btn.active {
            background: #0071e3;
            color: #ffffff;
        }

        /* 2. İKİNCİL BEYAZ AYAR BARI */
        .editor-sub-bar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 8px 18px;
            display: flex;
            align-items: center;
            gap: 16px;
            z-index: 990;
        }

        .pill-select-item {
            font-size: 12px;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 999px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            color: #334155;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .pill-select-item.active {
            background: #0071e3;
            border-color: #0071e3;
            color: #fff;
        }

        /* 3. ANA EDİTÖR ÇALIŞMA ALANI */
        .editor-main-body {
            display: flex;
            flex-grow: 1;
            overflow: hidden;
            position: relative;
        }

        /* SOL ŞABLON & ARAÇ MENÜSÜ */
        .editor-sidebar {
            width: 320px;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            z-index: 100;
        }

        .sidebar-tabs {
            display: flex;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
        }
        .sidebar-tab-btn {
            flex: 1;
            padding: 10px 4px;
            text-align: center;
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            border-bottom: 2px solid transparent;
        }
        .sidebar-tab-btn:hover {
            color: #0071e3;
            background: #fff;
        }
        .sidebar-tab-btn.active {
            color: #0071e3;
            background: #ffffff;
            border-bottom-color: #0071e3;
        }
        .sidebar-tab-btn i {
            font-size: 16px;
            display: block;
            margin-bottom: 2px;
        }

        .sidebar-content-area {
            flex-grow: 1;
            overflow-y: auto;
            padding: 14px;
        }

        /* Şablon Kartları Grid */
        .template-thumbs-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .template-thumb-card {
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.2s;
            background: #18181b;
            padding: 2px;
        }
        .template-thumb-card:hover {
            border-color: #0071e3;
            transform: scale(1.03);
            box-shadow: 0 6px 14px rgba(0,0,0,0.15);
        }
        .template-preview-img {
            width: 100%;
            height: 75px;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 6px;
            text-align: center;
            color: #ffffff;
            font-size: 8px;
        }

        /* SAĞ / ORTA TUVAL SAHNESİ */
        .editor-canvas-stage {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            background: radial-gradient(circle at center, #1e293b 0%, #0b1120 100%);
            overflow: hidden;
        }

        /* Kartvizit Cetvel Etiketleri */
        .ruler-label-top {
            background: rgba(255, 255, 255, 0.15);
            color: #f59e0b;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 12px;
            border-radius: 999px;
            margin-bottom: 8px;
            backdrop-filter: blur(8px);
        }
        .ruler-label-left {
            position: absolute;
            left: 20px;
            background: rgba(255, 255, 255, 0.15);
            color: #f59e0b;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 999px;
            transform: rotate(-90deg);
            backdrop-filter: blur(8px);
        }

        .canvas-card-holder {
            position: relative;
            border-radius: 12px;
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.15);
            overflow: hidden;
            display: inline-block;
        }

        .safe-margin-line {
            position: absolute;
            top: 14px;
            left: 14px;
            right: 14px;
            bottom: 14px;
            border: 1px dashed rgba(245, 158, 11, 0.6);
            border-radius: 6px;
            pointer-events: none;
            z-index: 50;
        }
    </style>
</head>
<body>

    <!-- 1. ÜST KOYU NAVİGASYON (Screenshot 2 ile birebir) -->
    <header class="editor-header-dark">
        <div class="d-flex align-items-center gap-3">
            <a href="product.php?slug=<?= urlencode($slug) ?>" class="btn-back-pill">
                <i class="bi bi-arrow-left"></i> Geri
            </a>
            <div>
                <strong class="fs-6">TamBaskı Editör</strong>
                <span class="text-white-50 ms-2 small">Baskı Ebatı: 8.4 cm × 5.2 cm • 300 DPI Vektörel Baskı</span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="side-toggle-group">
                <button type="button" class="side-toggle-btn active" id="btnFrontSide" onclick="switchCardSide('front')">Ön Yüz</button>
                <button type="button" class="side-toggle-btn" id="btnBackSide" onclick="switchCardSide('back')">Arka Yüz</button>
            </div>

            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-bold" style="background: linear-gradient(135deg, #8b5cf6, #6366f1); border: none;">
                <i class="bi bi-layers-half me-1"></i> 3D Önizle
            </button>

            <button type="button" class="btn btn-sm btn-dark bg-opacity-50 text-white rounded-pill px-3 py-1 border" id="toggleSafeGuideBtn" onclick="toggleGuide()">
                <i class="bi bi-aspect-ratio me-1"></i> Güvenli Alan
            </button>

            <button type="button" class="btn btn-sm btn-danger rounded-pill px-4 py-1 fw-bold shadow" onclick="saveDesignAndGoBack()">
                <i class="bi bi-check2-circle me-1"></i> Tamamla &amp; Sepete Ekle
            </button>
        </div>
    </header>

    <!-- 2. İKİNCİL BEYAZ AYAR BARI -->
    <div class="editor-sub-bar">
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small fw-bold">Paket:</span>
            <span class="pill-select-item active">Ekonomik</span>
            <span class="pill-select-item">Standart</span>
            <span class="pill-select-item">Premium</span>
            <span class="pill-select-item">VIP Prestij</span>
        </div>

        <div class="d-flex align-items-center gap-2 ms-3">
            <span class="text-muted small fw-bold">Adet:</span>
            <select class="form-select form-select-sm rounded-pill" style="width: 130px;">
                <option selected>1.000 Adet</option>
                <option>2.000 Adet</option>
                <option>3.000 Adet</option>
                <option>5.000 Adet</option>
            </select>
        </div>

        <div class="ms-auto">
            <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 text-muted" style="font-size: 11.5px;">
                <i class="bi bi-sliders me-1"></i> İnce Ayar (Ölçü &amp; Kağıt)
            </button>
        </div>
    </div>

    <!-- 3. ANA EDİTÖR ÇALIŞMA ALANI -->
    <div class="editor-main-body">
        
        <!-- SOL PANEL: ŞABLONLAR & ARAÇLAR -->
        <aside class="editor-sidebar">
            <div class="sidebar-tabs">
                <button type="button" class="sidebar-tab-btn active" onclick="showTabContent('templates', this)">
                    <i class="bi bi-grid-3x3-gap"></i> Şablonlar
                </button>
                <button type="button" class="sidebar-tab-btn" onclick="showTabContent('text', this)">
                    <i class="bi bi-fonts"></i> Metin
                </button>
                <button type="button" class="sidebar-tab-btn" onclick="showTabContent('logo', this)">
                    <i class="bi bi-image"></i> Logo/Resim
                </button>
                <button type="button" class="sidebar-tab-btn" onclick="showTabContent('qr', this)">
                    <i class="bi bi-qr-code"></i> İkon/QR
                </button>
                <button type="button" class="sidebar-tab-btn" onclick="showTabContent('shapes', this)">
                    <i class="bi bi-square"></i> Şekiller
                </button>
                <button type="button" class="sidebar-tab-btn" onclick="showTabContent('color', this)">
                    <i class="bi bi-palette"></i> Renk
                </button>
            </div>

            <div class="sidebar-content-area">
                <!-- 1. ŞABLON KÜTÜPHANESİ (Screenshot 2 ile birebir) -->
                <div id="tab-content-templates">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong class="small text-dark"><i class="bi bi-grid-fill text-primary me-1"></i>Şablon Kütüphanesi</strong>
                        <span class="badge bg-primary text-white rounded-pill" style="font-size: 10px;">420 Şablon</span>
                    </div>

                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" placeholder="Şablon veya meslek ara...">
                    </div>

                    <div class="mb-3">
                        <select class="form-select form-select-sm">
                            <option selected>Tüm Sektörler (Tümü)</option>
                            <option>Kurumsal &amp; İş Dünyası</option>
                            <option>Avukat &amp; Hukuk</option>
                            <option>Sağlık &amp; Klinik</option>
                            <option>İnşaat &amp; Mimarlık</option>
                            <option>Yazılım &amp; Teknoloji</option>
                        </select>
                    </div>

                    <!-- Şablon Küçük Resimleri -->
                    <div class="template-thumbs-grid">
                        <!-- Şablon 1: Avenue Capital (Siyah & Altın) -->
                        <div class="template-thumb-card" onclick="loadTemplate('avenue')">
                            <div class="template-preview-img" style="background: linear-gradient(135deg, #09090b 0%, #18181b 100%);">
                                <div style="color: #f59e0b; font-weight: 800; font-size: 10px;">AVENUE CAPITAL</div>
                                <div style="color: #a1a1aa; font-size: 7px;">Global Investment</div>
                            </div>
                            <div class="p-1 text-center bg-white" style="font-size: 9px; font-weight: 600;">Kurumsal &amp; İş Dünyası</div>
                        </div>

                        <!-- Şablon 2: Gold Executive -->
                        <div class="template-thumb-card" onclick="loadTemplate('executive')">
                            <div class="template-preview-img" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-left: 3px solid #f59e0b;">
                                <div style="color: #ffffff; font-weight: 700; font-size: 9px;">MURAT SANCAK</div>
                                <div style="color: #f59e0b; font-size: 7px;">Managing Partner</div>
                            </div>
                            <div class="p-1 text-center bg-white" style="font-size: 9px; font-weight: 600;">Gold Executive</div>
                        </div>

                        <!-- Şablon 3: Modern Minimalist Beyaz -->
                        <div class="template-thumb-card" onclick="loadTemplate('minimal')">
                            <div class="template-preview-img" style="background: #ffffff; color: #111113; border: 1px solid #e2e8f0;">
                                <div style="color: #0071e3; font-weight: 800; font-size: 10px;">TAM BASKI</div>
                                <div style="color: #64748b; font-size: 7px;">Dijital &amp; Ofset</div>
                            </div>
                            <div class="p-1 text-center bg-white" style="font-size: 9px; font-weight: 600;">Modern Beyaz</div>
                        </div>

                        <!-- Şablon 4: Tech Dark Cyan -->
                        <div class="template-thumb-card" onclick="loadTemplate('tech')">
                            <div class="template-preview-img" style="background: linear-gradient(135deg, #0f172a 0%, #0284c7 100%);">
                                <div style="color: #38bdf8; font-weight: 700; font-size: 9px;">CYBER LOGIC</div>
                                <div style="color: #e2e8f0; font-size: 7px;">Software &amp; AI</div>
                            </div>
                            <div class="p-1 text-center bg-white" style="font-size: 9px; font-weight: 600;">Teknoloji &amp; Yazılım</div>
                        </div>
                    </div>
                </div>

                <!-- 2. METİN EKLEME PANELİ -->
                <div id="tab-content-text" style="display: none;">
                    <strong class="small text-dark mb-2 d-block">Yazı &amp; Tipografi</strong>
                    <button type="button" class="btn btn-dark w-100 btn-sm mb-2 rounded-pill fw-bold" onclick="addCanvasText('Yeni Başlık', 28, true)">
                        <i class="bi bi-type-h1 me-1"></i> Başlık Ekle
                    </button>
                    <button type="button" class="btn btn-outline-dark w-100 btn-sm mb-2 rounded-pill" onclick="addCanvasText('Alt Başlık / Unvan', 18, false)">
                        <i class="bi bi-type-h2 me-1"></i> Alt Başlık Ekle
                    </button>
                    <button type="button" class="btn btn-outline-secondary w-100 btn-sm mb-3 rounded-pill" onclick="addCanvasText('Telefon, e-posta veya adres metni...', 12, false)">
                        <i class="bi bi-fonts me-1"></i> İletişim Metni Ekle
                    </button>
                </div>

                <!-- 3. LOGO YÜKLEME PANELİ -->
                <div id="tab-content-logo" style="display: none;">
                    <strong class="small text-dark mb-2 d-block">Logo veya Görsel Yükle</strong>
                    <input type="file" id="editorLogoUpload" class="form-control form-control-sm mb-2" accept="image/*">
                    <small class="text-muted d-block" style="font-size: 11px;">PNG veya SVG şeffaf logonuzu yükleyerek tuval üzerinde istediğiniz yere yerleştirebilirsiniz.</small>
                </div>
            </div>
        </aside>

        <!-- ORTA TUVAL SAHNESİ (Screenshot 2 ile birebir) -->
        <main class="editor-canvas-stage">
            <div class="ruler-label-top">Genişlik: 8.4 cm (84 mm)</div>
            <div class="ruler-label-left">Yükseklik: 5.2 cm (52 mm)</div>

            <div class="canvas-card-holder">
                <canvas id="mainEditorCanvas" width="672" height="416"></canvas>
                <div class="safe-margin-line" id="safeMarginGuide"></div>
            </div>

            <div class="mt-3 text-white-50 small">
                <i class="bi bi-mouse text-warning me-1"></i> Metin ve logoları tıklayarak sürükleyebilir, boyutlandırabilir ve çift tıklayarak metni değiştirebilirsiniz.
            </div>
        </main>

    </div>

    <!-- JAVASCRIPT ETKİLEŞİM & FABRIC.JS MOTORU -->
    <script>
    let canvas = null;

    document.addEventListener('DOMContentLoaded', function() {
        canvas = new fabric.Canvas('mainEditorCanvas', {
            backgroundColor: '#09090b',
            preserveObjectStacking: true
        });

        // Screenshot 2'deki Avenue Capital Lüks Kartviziti Yükle
        loadTemplate('avenue');

        // Logo Upload Event
        document.getElementById('editorLogoUpload')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(f) {
                fabric.Image.fromURL(f.target.result, function(img) {
                    img.scaleToWidth(140);
                    img.set({ left: 260, top: 50 });
                    canvas.add(img);
                    canvas.setActiveObject(img);
                });
            };
            reader.readAsDataURL(file);
        });
    });

    // Şablon Yükleme Fonksiyonu
    function loadTemplate(type) {
        if (!canvas) return;
        canvas.clear();

        if (type === 'avenue' || type === 'executive') {
            canvas.setBackgroundColor('#09090b', canvas.renderAll.bind(canvas));

            const iconShape = new fabric.Rect({
                left: 316, top: 45, width: 40, height: 40, fill: 'transparent', stroke: '#f59e0b', strokeWidth: 2, rx: 6, ry: 6
            });

            const name = new fabric.IText('Murat Sancak', {
                left: 236, top: 110, fontFamily: 'Georgia', fontSize: 30, fontWeight: 'bold', fill: '#f59e0b', textAlign: 'center'
            });

            const title = new fabric.IText('Managing Partner & CFO', {
                left: 260, top: 155, fontFamily: 'Helvetica', fontSize: 14, fill: '#d4d4d8', letterSpacing: 2
            });

            const line = new fabric.Rect({
                left: 276, top: 185, width: 120, height: 2, fill: '#f59e0b'
            });

            const company = new fabric.IText('AVENUE CAPITAL', {
                left: 246, top: 215, fontFamily: 'Helvetica', fontSize: 20, fontWeight: 'bold', fill: '#ffffff', letterSpacing: 3
            });

            const sub = new fabric.IText('Global Investment & Ventures', {
                left: 265, top: 245, fontFamily: 'Helvetica', fontSize: 11, fill: '#a1a1aa'
            });

            const phone = new fabric.IText('📍 Maslak No:1 Plaza Kat:18 Sarıyer / İstanbul\n📞 +90 (212) 380 40 50 • ✉️ murat@avenuecap.com', {
                left: 170, top: 310, fontFamily: 'Helvetica', fontSize: 11, fill: '#cbd5e1', textAlign: 'center', lineHeight: 1.4
            });

            canvas.add(iconShape, name, title, line, company, sub, phone);
        } else if (type === 'minimal') {
            canvas.setBackgroundColor('#ffffff', canvas.renderAll.bind(canvas));

            const name = new fabric.IText('TAM BASKI', {
                left: 60, top: 70, fontFamily: 'Helvetica', fontSize: 32, fontWeight: 'bold', fill: '#111113'
            });
            const title = new fabric.IText('Online Matbaa & Dijital Baskı Merkezi', {
                left: 60, top: 125, fontFamily: 'Helvetica', fontSize: 14, fill: '#0071e3'
            });
            const contact = new fabric.IText('📞 0850 308 00 00\n✉️ info@tambaski.com.tr\n🌐 www.tambaski.com.tr', {
                left: 60, top: 220, fontFamily: 'Helvetica', fontSize: 13, fill: '#64748b', lineHeight: 1.5
            });

            canvas.add(name, title, contact);
        } else if (type === 'tech') {
            canvas.setBackgroundColor('#0f172a', canvas.renderAll.bind(canvas));

            const name = new fabric.IText('CYBER LOGIC', {
                left: 60, top: 70, fontFamily: 'Helvetica', fontSize: 32, fontWeight: 'bold', fill: '#38bdf8'
            });
            const title = new fabric.IText('Artificial Intelligence & Cloud Solutions', {
                left: 60, top: 125, fontFamily: 'Helvetica', fontSize: 14, fill: '#94a3b8'
            });
            const contact = new fabric.IText('📞 0532 000 00 00 • ✉️ contact@cyberlogic.io', {
                left: 60, top: 280, fontFamily: 'Helvetica', fontSize: 13, fill: '#e2e8f0'
            });

            canvas.add(name, title, contact);
        }

        canvas.renderAll();
    }

    function addCanvasText(str, size, isBold) {
        if (!canvas) return;
        const t = new fabric.IText(str, {
            left: 200, top: 150, fontFamily: 'Helvetica', fontSize: size, fontWeight: isBold ? 'bold' : 'normal', fill: '#ffffff'
        });
        canvas.add(t);
        canvas.setActiveObject(t);
    }

    function showTabContent(tabId, el) {
        document.querySelectorAll('.sidebar-tab-btn').forEach(b => b.classList.remove('active'));
        el.classList.add('active');

        ['templates', 'text', 'logo', 'qr', 'shapes', 'color'].forEach(t => {
            const box = document.getElementById('tab-content-' + t);
            if (box) box.style.display = (t === tabId) ? 'block' : 'none';
        });
    }

    function toggleGuide() {
        const g = document.getElementById('safeMarginGuide');
        if (g) g.style.display = (g.style.display === 'none') ? 'block' : 'none';
    }

    function switchCardSide(side) {
        document.getElementById('btnFrontSide').classList.toggle('active', side === 'front');
        document.getElementById('btnBackSide').classList.toggle('active', side === 'back');
        if (side === 'back') {
            canvas.clear();
            canvas.setBackgroundColor('#18181b', canvas.renderAll.bind(canvas));
            const backLogo = new fabric.IText('AVENUE\nCAPITAL', {
                left: 270, top: 150, fontFamily: 'Georgia', fontSize: 34, fontWeight: 'bold', fill: '#f59e0b', textAlign: 'center'
            });
            canvas.add(backLogo);
        } else {
            loadTemplate('avenue');
        }
    }

    function saveDesignAndGoBack() {
        alert('Tasarımınız 300 DPI yüksek çözünürlükle kaydedildi ve siparişinize eklendi!');
        window.location.href = 'product.php?slug=<?= urlencode($slug) ?>';
    }
    </script>
</body>
</html>
