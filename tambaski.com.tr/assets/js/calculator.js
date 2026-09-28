/**
 * Matbaa Canlı Fiyat, Paket & Akıllı Jest Hesaplayıcı
 */
document.addEventListener('DOMContentLoaded', function() {
    const configForm = document.getElementById('printConfigForm');
    if (!configForm) return;

    const productId = configForm.dataset.productId;
    const priceDisplay = document.getElementById('calcGrandTotal');
    const subtotalDisplay = document.getElementById('calcSubtotal');
    const taxDisplay = document.getElementById('calcTax');
    const unitPriceDisplay = document.getElementById('calcUnitPrice');
    const customWidthInput = document.getElementById('customWidth');
    const customHeightInput = document.getElementById('customHeight');
    const designServiceCheckbox = document.getElementById('includeDesignService');

    let latestCalculation = null;
    let giftOfferDecisionMade = false;

    function calculateLivePrice() {
        giftOfferDecisionMade = false; // Ayarlar değiştiğinde kararı sıfırla

        const formData = new FormData(configForm);
        formData.append('product_id', productId);

        if (designServiceCheckbox && designServiceCheckbox.checked) {
            formData.append('design_service', '1');
        }

        fetch(SITE_URL + '/api/calculate_price.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                latestCalculation = data;

                if (priceDisplay) priceDisplay.textContent = data.formatted_total;
                if (subtotalDisplay) subtotalDisplay.textContent = data.formatted_subtotal;
                if (taxDisplay) taxDisplay.textContent = data.tax_amount.toFixed(2) + ' ₺';
                if (unitPriceDisplay) unitPriceDisplay.textContent = 'Birim: ' + data.formatted_unit_price;

                // ⚡ Canva Studio Net Fiyat (KDV & Kargo Hariç) Canlı Güncelleme
                const canvaNetPrice = document.getElementById('canvaStudioNetPrice');
                const canvaUnitPrice = document.getElementById('canvaStudioUnitPriceText');
                if (canvaNetPrice) canvaNetPrice.textContent = data.formatted_subtotal;
                if (canvaUnitPrice) canvaUnitPrice.textContent = 'Birim: ' + data.formatted_unit_price;

                // 📱 Mobildeki Net Fiyat & Paket / Adet Rozetleri
                const mobileNetPrice = document.getElementById('mobileNetPrice');
                if (mobileNetPrice) mobileNetPrice.textContent = data.formatted_subtotal;

                const activePkgRadio = document.querySelector('input[name="selected_package"]:checked');
                const mobilePkgBadge = document.getElementById('mobilePkgBadge');
                if (mobilePkgBadge && activePkgRadio) {
                    const pkgLabels = { 'ekonomik': 'Ekonomik', 'standart': 'Standart', 'premium': 'Premium', 'vip': 'VIP' };
                    mobilePkgBadge.textContent = pkgLabels[activePkgRadio.value] || activePkgRadio.value;
                }

                const mobileQtyBadge = document.getElementById('mobileQtyBadge');
                if (mobileQtyBadge && data.quantity) {
                    mobileQtyBadge.textContent = Number(data.quantity).toLocaleString('tr-TR') + ' Adet';
                }

                const stickyTotal = document.getElementById('stickyGrandTotal');
                if (stickyTotal) stickyTotal.textContent = data.formatted_total;
                if (typeof updateStickyBar === 'function') updateStickyBar();

                // 💡 Akıllı Tabaka Doldurma & Avantajlı Adet Öneri Kutusu
                const upsellCard = document.getElementById('liveUpsellCard');
                const upsellMsg = document.getElementById('upsellMessageText');
                const btnUpsell = document.getElementById('btnApplyUpsell');

                if (data.upsell && data.upsell.active) {
                    if (upsellCard && upsellMsg) {
                        upsellMsg.innerHTML = data.upsell.message;
                        upsellCard.style.display = 'block';

                        if (btnUpsell) {
                            btnUpsell.innerHTML = '<i class="bi bi-stars me-1 text-danger"></i> ' + data.upsell.target_quantity + ' Adet Olarak Seç (' + data.upsell.formatted_diff + ')';
                            btnUpsell.onclick = function() {
                                applyUpsellQuantity(data.upsell.target_quantity);
                            };
                        }
                    }
                } else if (upsellCard) {
                    upsellCard.style.display = 'none';
                }
            }
        })
        .catch(err => console.error('Hesaplama hatası:', err));
    }

    function applyUpsellQuantity(targetQty) {
        giftOfferDecisionMade = true;

        // Standart kutularda bu adet var mı kontrol et
        const matchingBox = document.getElementById('qty_box_' + targetQty);
        if (matchingBox && typeof selectQuantity === 'function') {
            selectQuantity(targetQty, matchingBox);
        } else {
            // Özel adet kutusuna uygula
            const customBox = document.getElementById('qty_box_custom');
            const manualInput = document.getElementById('manualCustomQtyInput');
            const customRadio = document.getElementById('customQtyRadio');
            if (typeof activateCustomQty === 'function') {
                activateCustomQty(customBox);
            }
            if (manualInput) manualInput.value = targetQty;
            if (customRadio) {
                customRadio.value = targetQty;
                customRadio.checked = true;
                customRadio.dispatchEvent(new Event('change'));
            }
        }
        
        // Fiyat alanını vurgula
        if (priceDisplay) {
            priceDisplay.classList.add('text-success');
            priceDisplay.style.transform = 'scale(1.08)';
            setTimeout(() => {
                priceDisplay.style.transform = 'scale(1)';
            }, 300);
        }
    }



    // Paket Seçimi Dinleyicisi
    const packageRadios = document.querySelectorAll('input[name="selected_package"]');
    packageRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const pkg = this.value;
            applyPackagePreset(pkg);
            calculateLivePrice();
        });
    });

    function applyPackagePreset(pkg) {
        if (pkg === 'ekonomik') {
            selectOptionByKeyword(['mat kuşe', 'parlak kuşe', 'standart', 'düz']);
        } else if (pkg === 'standart') {
            selectOptionByKeyword(['mat kuşe', 'çift taraf mat', 'oval']);
        } else if (pkg === 'premium') {
            selectOptionByKeyword(['kadife', 'soft-touch', 'lak', 'parlak']);
        } else if (pkg === 'vip') {
            selectOptionByKeyword(['tuale', 'fantezi', 'dokulu', 'yaldız', 'gold']);
        }
    }

    function selectOptionByKeyword(keywords) {
        const options = configForm.querySelectorAll('.segmented-option');
        options.forEach(opt => {
            const title = (opt.querySelector('.opt-title') ? opt.querySelector('.opt-title').textContent : '').toLowerCase();
            keywords.forEach(kw => {
                if (title.includes(kw)) {
                    const radio = opt.querySelector('input[type="radio"]');
                    if (radio) radio.checked = true;
                }
            });
        });
    }

    // Seçenekler veya Adet değiştikçe anlık hesapla
    configForm.querySelectorAll('input[type="radio"], select').forEach(el => {
        el.addEventListener('change', calculateLivePrice);
    });

    if (customWidthInput && customHeightInput) {
        customWidthInput.addEventListener('input', calculateLivePrice);
        customHeightInput.addEventListener('input', calculateLivePrice);
    }

    if (designServiceCheckbox) {
        designServiceCheckbox.addEventListener('change', calculateLivePrice);
    }

    // Dışarıdan tetiklenebilmesi için window'a bağla
    window.calculateLivePrice = calculateLivePrice;

    // İlk yüklemede hesapla
    calculateLivePrice();
});
