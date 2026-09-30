/**
 * 🚗 TAMBASKI / BYKCUT - 3D/2.5D ARAÇ GİYDİRME & 1:1 STICKER SİMÜLATÖRÜ MOTORU
 * 1990-2026 Model Araç Veritabanı, 0-4 Numara Cam Filmi, Canlı Boya ve Milimetrik Fiyatlandırma
 */

(function () {
    'use strict';

    class VehicleCustomizerStudio {
        constructor() {
            this.vehicleDb = {};
            this.stickersDb = {};
            
            // Aktif Araç Durumu
            this.currentBrand = 'fiat';
            this.currentModel = 'egea_sedan';
            this.currentAngle = 'side_left';
            this.currentColor = '#e2e8f0';
            this.currentFinish = 'gloss';
            this.currentTintLevel = 2; // Varsayılan 2 Numara Orta Ton
            
            // Aktif Sticker & Malzeme Durumu
            this.currentMaterial = 'cast_premium';
            this.activeSticker = null;
            this.stickerWidthCm = 45;
            this.stickerHeightCm = 20;
            this.scaleFactorMmPerPx = 4.92; // 1px = X mm
            
            // Fabric.js Tuval
            this.fabricCanvas = null;
            this.init();
        }

        async init() {
            console.log('🚗 Araç Simülatörü Motoru Başlatılıyor...');
            await this.loadData();
            this.initDomElements();
            this.initFabricCanvas();
            this.bindEvents();
            this.renderVehicle();
        }

        async loadData() {
            try {
                const [vehiclesRes, stickersRes] = await Promise.all([
                    fetch('assets/vehicles/data/vehicles.json').then(r => r.json()),
                    fetch('assets/vehicles/data/stickers.json').then(r => r.json())
                ]);
                this.vehicleDb = vehiclesRes;
                this.stickersDb = stickersRes;
                this.populateBrandDropdown();
                this.populateStickerCategories();
                this.populateMaterials();
            } catch (err) {
                console.error('Veri yükleme hatası:', err);
            }
        }

        initDomElements() {
            this.brandSelect = document.getElementById('vehicleBrandSelect');
            this.modelSelect = document.getElementById('vehicleModelSelect');
            this.yearBadge = document.getElementById('vehicleYearBadge');
            this.lengthBadge = document.getElementById('vehicleLengthBadge');
            this.categoryBadge = document.getElementById('vehicleCategoryBadge');
            this.colorPicker = document.getElementById('vehicleColorPicker');
            this.vehicleContainer = document.getElementById('vehicleStageSvg');
            
            // HUD Göstergeleri
            this.hudWidth = document.getElementById('hudWidthCm');
            this.hudHeight = document.getElementById('hudHeightCm');
            this.hudArea = document.getElementById('hudAreaM2');
            this.hudPrice = document.getElementById('hudLivePrice');
            this.materialSelect = document.getElementById('stickerMaterialSelect');
            this.addToCartBtn = document.getElementById('btnVehicleAddToCart');
        }

        populateBrandDropdown() {
            if (!this.brandSelect) return;
            this.brandSelect.innerHTML = '';
            for (const [key, brand] of Object.entries(this.vehicleDb)) {
                const opt = document.createElement('option');
                opt.value = key;
                opt.textContent = brand.name;
                if (key === this.currentBrand) opt.selected = true;
                this.brandSelect.appendChild(opt);
            }
            this.populateModelDropdown();
        }

        populateModelDropdown() {
            if (!this.modelSelect) return;
            this.modelSelect.innerHTML = '';
            const brand = this.vehicleDb[this.currentBrand];
            if (!brand) return;

            let firstModel = null;
            for (const [key, model] of Object.entries(brand.models)) {
                if (!firstModel) firstModel = key;
                const opt = document.createElement('option');
                opt.value = key;
                opt.textContent = `${model.name} (${model.years})`;
                if (key === this.currentModel) opt.selected = true;
                this.modelSelect.appendChild(opt);
            }

            if (!brand.models[this.currentModel]) {
                this.currentModel = firstModel;
            }
            this.updateVehicleBadges();
        }

        updateVehicleBadges() {
            const model = this.vehicleDb[this.currentBrand]?.models[this.currentModel];
            if (!model) return;

            if (this.yearBadge) this.yearBadge.textContent = model.years;
            if (this.lengthBadge) this.lengthBadge.textContent = `${(model.length_mm / 10).toFixed(0)} cm (${model.length_mm} mm)`;
            if (this.categoryBadge) this.categoryBadge.textContent = model.category;
            
            // Ölçek faktörünü güncelle (Araç uzunluğu / 920px çizim boyu)
            this.scaleFactorMmPerPx = model.length_mm / 920.0;
        }

        populateStickerCategories() {
            const container = document.getElementById('stickerCategoriesAccordion');
            if (!container || !this.stickersDb.categories) return;

            container.innerHTML = '';
            this.stickersDb.categories.forEach((cat, idx) => {
                const catDiv = document.createElement('div');
                catDiv.className = 'mb-4';
                catDiv.innerHTML = `
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">${cat.name}</div>
                    <div class="grid grid-cols-3 gap-2">
                        ${cat.items.map(item => `
                            <button type="button" class="sticker-item-btn p-2 rounded-lg border border-slate-700 bg-slate-800/60 hover:bg-orange-500/10 hover:border-orange-500 transition-all text-center group flex flex-col items-center justify-between"
                                data-url="${item.url}" data-name="${item.name}" data-w="${item.default_width_cm}" data-h="${item.default_height_cm}">
                                <div class="w-full h-12 flex items-center justify-center bg-slate-900/80 rounded p-1 mb-1 group-hover:scale-105 transition-transform">
                                    <img src="${item.url}" class="max-h-full max-w-full object-contain filter invert-0" alt="${item.name}">
                                </div>
                                <span class="text-[10px] text-slate-300 font-medium truncate w-full">${item.name}</span>
                            </button>
                        `).join('')}
                    </div>
                `;
                container.appendChild(catDiv);
            });

            // Sticker Ekleme Olayları
            container.querySelectorAll('.sticker-item-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const url = btn.getAttribute('data-url');
                    const name = btn.getAttribute('data-name');
                    const w = parseFloat(btn.getAttribute('data-w')) || 50;
                    const h = parseFloat(btn.getAttribute('data-h')) || 20;
                    this.addStickerToVehicle(url, name, w, h);
                });
            });
        }

        populateMaterials() {
            if (!this.materialSelect || !this.stickersDb.materials) return;
            this.materialSelect.innerHTML = '';
            this.stickersDb.materials.forEach(mat => {
                const opt = document.createElement('option');
                opt.value = mat.id;
                opt.textContent = `${mat.name} (+${mat.price_per_m2} ₺/m²)`;
                if (mat.id === this.currentMaterial) opt.selected = true;
                this.materialSelect.appendChild(opt);
            });
        }

        initFabricCanvas() {
            const canvasEl = document.getElementById('vehicleFabricCanvas');
            if (!canvasEl) return;

            this.fabricCanvas = new fabric.Canvas('vehicleFabricCanvas', {
                width: 1020,
                height: 440,
                selection: true,
                preserveObjectStacking: true
            });

            // Nesne Değiştiğinde Canlı Ölçü & Fiyat Güncelle
            this.fabricCanvas.on('object:scaling', (e) => this.onStickerTransform(e.target));
            this.fabricCanvas.on('object:moving', (e) => this.onStickerTransform(e.target));
            this.fabricCanvas.on('object:modified', (e) => this.onStickerTransform(e.target));
            this.fabricCanvas.on('selection:created', (e) => this.onStickerSelected(e.selected[0]));
            this.fabricCanvas.on('selection:updated', (e) => this.onStickerSelected(e.selected[0]));
            this.fabricCanvas.on('selection:cleared', () => this.onStickerDeselected());
        }

        async renderVehicle() {
            if (!this.vehicleContainer) return;
            
            const svgPath = `assets/vehicles/svg/${this.currentBrand}/${this.currentModel}_${this.currentAngle}.svg`;
            try {
                const response = await fetch(svgPath);
                if (!response.ok) throw new Error('SVG bulunamadı');
                const svgText = await response.text();
                this.vehicleContainer.innerHTML = svgText;
                
                // Renk ve Cam Filmini Uygula
                this.applyVehicleColor(this.currentColor);
                this.applyWindowTint(this.currentTintLevel);
                this.updateVehicleBadges();
                this.calculatePrice();
            } catch (err) {
                console.warn('SVG yüklenemedi, varsayılan oluşturuluyor:', err);
            }
        }

        applyVehicleColor(hex) {
            this.currentColor = hex;
            const bodyPath = document.getElementById('vehicle-body-path');
            if (bodyPath) {
                bodyPath.setAttribute('fill', hex);
            }
        }

        applyWindowTint(level) {
            this.currentTintLevel = parseInt(level, 10);
            const tintEl = document.getElementById('vehicle-window-tint');
            if (!tintEl) return;

            const tintConfig = this.stickersDb.tint_levels?.find(t => t.level === this.currentTintLevel);
            const opacity = tintConfig ? tintConfig.opacity : 0.35;
            tintEl.setAttribute('opacity', opacity);

            // Buton aktiflik sınıfları
            document.querySelectorAll('.tint-btn').forEach(btn => {
                const btnLevel = parseInt(btn.getAttribute('data-tint'), 10);
                if (btnLevel === this.currentTintLevel) {
                    btn.classList.add('bg-orange-500', 'text-white', 'border-orange-500');
                    btn.classList.remove('bg-slate-800', 'text-slate-300', 'border-slate-700');
                } else {
                    btn.classList.remove('bg-orange-500', 'text-white', 'border-orange-500');
                    btn.classList.add('bg-slate-800', 'text-slate-300', 'border-slate-700');
                }
            });
        }

        addStickerToVehicle(svgUrl, name, defaultWidthCm, defaultHeightCm) {
            fabric.loadSVGFromURL(svgUrl, (objects, options) => {
                const obj = fabric.util.groupSVGElements(objects, options);
                
                // Gerçek cm ölçüsüne göre piksel hesaplama
                // scaleFactorMmPerPx = mm / px  => px = (cm * 10) / scaleFactor
                const targetWidthPx = (defaultWidthCm * 10) / this.scaleFactorMmPerPx;
                const scale = targetWidthPx / (obj.width || 100);

                obj.set({
                    left: 450,
                    top: 220,
                    originX: 'center',
                    originY: 'center',
                    scaleX: scale,
                    scaleY: scale,
                    cornerColor: '#f15a24',
                    cornerStrokeColor: '#ffffff',
                    cornerSize: 10,
                    transparentCorners: false,
                    stickerName: name,
                    stickerUrl: svgUrl
                });

                this.fabricCanvas.add(obj);
                this.fabricCanvas.setActiveObject(obj);
                this.fabricCanvas.renderAll();
                this.onStickerTransform(obj);
            });
        }

        onStickerTransform(obj) {
            if (!obj) return;
            this.activeSticker = obj;

            // Piksel boyutunu milimetre/santimetreye dönüştür
            const pxWidth = obj.getScaledWidth();
            const pxHeight = obj.getScaledHeight();

            const mmWidth = pxWidth * this.scaleFactorMmPerPx;
            const mmHeight = pxHeight * this.scaleFactorMmPerPx;

            this.stickerWidthCm = (mmWidth / 10).toFixed(1);
            this.stickerHeightCm = (mmHeight / 10).toFixed(1);

            if (this.hudWidth) this.hudWidth.textContent = `${this.stickerWidthCm} cm`;
            if (this.hudHeight) this.hudHeight.textContent = `${this.stickerHeightCm} cm`;

            const areaM2 = (parseFloat(this.stickerWidthCm) * parseFloat(this.stickerHeightCm)) / 10000;
            if (this.hudArea) this.hudArea.textContent = `${areaM2.toFixed(3)} m²`;

            this.calculatePrice();
        }

        onStickerSelected(obj) {
            this.activeSticker = obj;
            this.onStickerTransform(obj);
            const deleteBtn = document.getElementById('btnDeleteSelectedSticker');
            if (deleteBtn) deleteBtn.style.display = 'inline-flex';
        }

        onStickerDeselected() {
            this.activeSticker = null;
            const deleteBtn = document.getElementById('btnDeleteSelectedSticker');
            if (deleteBtn) deleteBtn.style.display = 'none';
        }

        calculatePrice() {
            const mat = this.stickersDb.materials?.find(m => m.id === this.currentMaterial) || { price_per_m2: 500 };
            const areaM2 = (parseFloat(this.stickerWidthCm) * parseFloat(this.stickerHeightCm)) / 10000;
            
            // Taban kesim & ayar ücreti + Malzeme m² fiyatı
            const baseSetupFee = 45.0; 
            let totalPrice = baseSetupFee + (areaM2 * mat.price_per_m2);
            
            // Minimum sipariş tabanı 75 TL
            if (totalPrice < 75) totalPrice = 75;

            if (this.hudPrice) {
                this.hudPrice.textContent = `${Math.round(totalPrice)},00 ₺`;
            }
            return Math.round(totalPrice);
        }

        bindEvents() {
            // Marka Seçimi
            if (this.brandSelect) {
                this.brandSelect.addEventListener('change', (e) => {
                    this.currentBrand = e.target.value;
                    this.populateModelDropdown();
                    this.renderVehicle();
                });
            }

            // Model Seçimi
            if (this.modelSelect) {
                this.modelSelect.addEventListener('change', (e) => {
                    this.currentModel = e.target.value;
                    this.updateVehicleBadges();
                    this.renderVehicle();
                });
            }

            // Renk Seçimi
            if (this.colorPicker) {
                this.colorPicker.addEventListener('input', (e) => {
                    this.applyVehicleColor(e.target.value);
                });
            }

            // Hızlı Renk Palet Butonları
            document.querySelectorAll('.car-color-preset').forEach(btn => {
                btn.addEventListener('click', () => {
                    const color = btn.getAttribute('data-color');
                    if (this.colorPicker) this.colorPicker.value = color;
                    this.applyVehicleColor(color);
                });
            });

            // Cam Filmi Butonları
            document.querySelectorAll('.tint-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const tint = btn.getAttribute('data-tint');
                    this.applyWindowTint(tint);
                });
            });

            // Malzeme Seçimi
            if (this.materialSelect) {
                this.materialSelect.addEventListener('change', (e) => {
                    this.currentMaterial = e.target.value;
                    this.calculatePrice();
                });
            }

            // Seçili Stickerı Sil
            const deleteBtn = document.getElementById('btnDeleteSelectedSticker');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', () => {
                    if (this.activeSticker && this.fabricCanvas) {
                        this.fabricCanvas.remove(this.activeSticker);
                        this.fabricCanvas.discardActiveObject();
                        this.fabricCanvas.renderAll();
                        this.onStickerDeselected();
                    }
                });
            }

            // Özel Görsel / Logo Yükleme
            const customUploadInput = document.getElementById('customStickerUpload');
            if (customUploadInput) {
                customUploadInput.addEventListener('change', (e) => {
                    const file = e.target.files[0];
                    if (!file) return;

                    const reader = new FileReader();
                    reader.onload = (f) => {
                        const data = f.target.result;
                        fabric.Image.fromURL(data, (img) => {
                            img.set({
                                left: 450,
                                top: 220,
                                originX: 'center',
                                originY: 'center',
                                scaleX: 0.35,
                                scaleY: 0.35,
                                cornerColor: '#f15a24',
                                cornerSize: 10,
                                transparentCorners: false,
                                stickerName: file.name
                            });
                            this.fabricCanvas.add(img);
                            this.fabricCanvas.setActiveObject(img);
                            this.fabricCanvas.renderAll();
                            this.onStickerTransform(img);
                        });
                    };
                    reader.readAsDataURL(file);
                });
            }

            // Sepete Ekle
            if (this.addToCartBtn) {
                this.addToCartBtn.addEventListener('click', () => this.handleAddToCart());
            }
        }

        handleAddToCart() {
            const modelObj = this.vehicleDb[this.currentBrand]?.models[this.currentModel];
            const matObj = this.stickersDb.materials?.find(m => m.id === this.currentMaterial);
            const tintObj = this.stickersDb.tint_levels?.find(t => t.level === this.currentTintLevel);
            const price = this.calculatePrice();

            const orderPayload = {
                product_type: 'vehicle_custom_sticker',
                brand: this.vehicleDb[this.currentBrand]?.name || this.currentBrand,
                model: modelObj?.name || this.currentModel,
                year: modelObj?.years,
                vehicle_color: this.currentColor,
                window_tint: tintObj?.name,
                material: matObj?.name,
                width_cm: this.stickerWidthCm,
                height_cm: this.stickerHeightCm,
                area_m2: ((parseFloat(this.stickerWidthCm) * parseFloat(this.stickerHeightCm)) / 10000).toFixed(3),
                price: price,
                sticker_count: this.fabricCanvas ? this.fabricCanvas.getObjects().length : 1
            };

            console.log('🛒 Sepete Eklendi:', orderPayload);
            
            // Kullanıcıya bildirim göster
            const modalTitle = document.getElementById('cartSuccessModalTitle');
            if (modalTitle) modalTitle.textContent = `${orderPayload.brand} ${orderPayload.model} Stickerı Sepete Eklendi!`;
            
            // Sepet post işlemi
            fetch('cart.php?action=add_vehicle_sticker', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(orderPayload)
            }).then(() => {
                window.location.href = 'cart.php';
            }).catch(() => {
                window.location.href = 'cart.php';
            });
        }
    }

    // Sayfa Yüklendiğinde Başlat
    document.addEventListener('DOMContentLoaded', () => {
        window.vehicleStudio = new VehicleCustomizerStudio();
    });
})();
