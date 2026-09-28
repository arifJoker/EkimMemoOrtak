/**
 * TAMBASKI.COM.TR - Frontend Etkileşim & Canlı Fiyatlandırma Scripti
 */

document.addEventListener('DOMContentLoaded', function() {
    initProductCalculator();
    initFileUpload();
    initCartHandlers();
});

/**
 * Ürün Fiyat & Varyant Hesaplayıcı
 */
function initProductCalculator() {
    const configForm = document.getElementById('productConfigForm');
    if (!configForm) return;

    const packageCards = document.querySelectorAll('.package-select-card');
    const customQtyInput = document.getElementById('customQuantityInput');
    const widthInput = document.getElementById('sqmWidthInput');
    const heightInput = document.getElementById('sqmHeightInput');
    const sqmQtyInput = document.getElementById('sqmQuantityInput');
    const thicknessSelect = document.getElementById('thicknessSelect');
    const cuttingSelect = document.getElementById('cuttingSelect');

    const displayTotalPrice = document.getElementById('displayTotalPrice');
    const displayUnitPrice = document.getElementById('displayUnitPrice');
    const displaySpecs = document.getElementById('displaySpecs');

    // Paket Kartları Tıklaması
    packageCards.forEach(card => {
        card.addEventListener('click', function() {
            packageCards.forEach(c => c.classList.remove('active'));
            this.classList.add('active');

            const pkgId = this.dataset.packageId;
            const pkgPrice = parseFloat(this.dataset.price);
            const pkgQty = parseInt(this.dataset.qty);
            const pkgSpecs = this.dataset.specs;

            if (document.getElementById('selectedPackageId')) {
                document.getElementById('selectedPackageId').value = pkgId;
            }
            if (document.getElementById('pricingMode')) {
                document.getElementById('pricingMode').value = 'package';
            }

            updatePriceDisplay(pkgPrice, pkgPrice / pkgQty, pkgSpecs);
        });
    });

    // Özel Adet Girişi
    if (customQtyInput) {
        customQtyInput.addEventListener('input', function() {
            packageCards.forEach(c => c.classList.remove('active'));
            if (document.getElementById('pricingMode')) {
                document.getElementById('pricingMode').value = 'custom';
            }
            if (document.getElementById('selectedPackageId')) {
                document.getElementById('selectedPackageId').value = '';
            }
            recalcCustomPrice();
        });
    }

    // m² Boyut Girişleri (Dekota, Pleksi, Folyo, Branda)
    [widthInput, heightInput, sqmQtyInput, thicknessSelect, cuttingSelect].forEach(el => {
        if (el) {
            el.addEventListener('input', recalcSqmPrice);
            el.addEventListener('change', recalcSqmPrice);
        }
    });

    function recalcCustomPrice() {
        const qty = parseInt(customQtyInput.value) || 1;
        const setupFee = parseFloat(configForm.dataset.setupFee || 45);
        const unitMultiplier = parseFloat(configForm.dataset.unitMultiplier || 0.75);

        let discountFactor = 1.0;
        if (qty >= 5000) discountFactor = 0.60;
        else if (qty >= 2000) discountFactor = 0.70;
        else if (qty >= 1000) discountFactor = 0.80;
        else if (qty >= 500) discountFactor = 0.90;

        const total = setupFee + (qty * unitMultiplier * discountFactor);
        const unit = total / qty;

        updatePriceDisplay(total, unit, `Özel Adet: ${qty} Adet`);
    }

    function recalcSqmPrice() {
        const width = parseFloat(widthInput?.value || 100);
        const height = parseFloat(heightInput?.value || 100);
        const qty = parseInt(sqmQtyInput?.value || 1);
        const baseSqm = parseFloat(configForm.dataset.baseSqm || 320);
        const setupFee = parseFloat(configForm.dataset.setupFee || 60);

        const thicknessMult = parseFloat(thicknessSelect?.value || 1.0);
        const cuttingExtra = parseFloat(cuttingSelect?.value || 0.0);

        const sqm = (width * height) / 10000;
        const billableSqm = Math.max(0.20, sqm);

        const unit = (billableSqm * baseSqm * thicknessMult) + cuttingExtra;
        const total = (unit * qty) + setupFee;

        updatePriceDisplay(total, total / qty, `${width}x${height} cm (${sqm.toFixed(2)} m²) - ${qty} Adet`);
    }

    function updatePriceDisplay(total, unit, specs) {
        if (displayTotalPrice) {
            displayTotalPrice.textContent = total.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ₺';
        }
        if (displayUnitPrice && unit) {
            displayUnitPrice.textContent = 'Birim Fiyat: ' + unit.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ₺';
        }
        if (displaySpecs && specs) {
            displaySpecs.textContent = specs;
        }
    }
}

/**
 * Dosya Yükleme Alanı
 */
function initFileUpload() {
    const dropZone = document.getElementById('designDropZone');
    const fileInput = document.getElementById('designFileInput');
    const fileNameDisplay = document.getElementById('uploadedFileName');

    if (!dropZone || !fileInput) return;

    dropZone.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            if (fileNameDisplay) {
                fileNameDisplay.innerHTML = `<span class="badge bg-success"><i class="bi bi-file-earmark-check"></i> ${this.files[0].name} (${(this.files[0].size / (1024*1024)).toFixed(2)} MB)</span>`;
            }
        }
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropZone.classList.add('border-primary');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-primary');
        }, false);
    });

    dropZone.addEventListener('drop', (e) => {
        if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            if (fileNameDisplay) {
                fileNameDisplay.innerHTML = `<span class="badge bg-success"><i class="bi bi-file-earmark-check"></i> ${e.dataTransfer.files[0].name} (${(e.dataTransfer.files[0].size / (1024*1024)).toFixed(2)} MB)</span>`;
            }
        }
    });
}

/**
 * Sepet İşlemleri & AJAX
 */
function initCartHandlers() {
    // Sepet Sayacı Güncelleme
}
