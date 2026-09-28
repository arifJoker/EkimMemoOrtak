/**
 * Online Vektörel Tasarım Editörü (Apple Sadeliğinde Canlı SVG Düzenleyici)
 */
window.PrintEditor = {
    currentSvg: '',
    templateData: null,
    canvasWidth: 850,
    canvasHeight: 500,

    init: function(template) {
        this.currentSvg = template.default_svg;
        this.templateData = template.fields || { fields: [] };
        this.canvasWidth = template.canvas_width || 850;
        this.canvasHeight = template.canvas_height || 500;

        this.renderCanvas();
        this.renderFormFields();
    },

    renderCanvas: function() {
        const previewContainer = document.getElementById('editorSvgContainer');
        if (!previewContainer) return;
        previewContainer.innerHTML = this.currentSvg;
        this.updateSvgValues();
    },

    renderFormFields: function() {
        const fieldsContainer = document.getElementById('editorDynamicFields');
        if (!fieldsContainer) return;

        fieldsContainer.innerHTML = '';
        const fields = this.templateData.fields || [];

        fields.forEach(field => {
            const group = document.createElement('div');
            group.className = 'mb-3';
            group.innerHTML = `
                <label class="form-label small fw-bold text-muted">${field.label || field.id}</label>
                <input type="text" class="form-control form-control-sm editor-input-field" 
                       data-field-id="${field.id}" 
                       value="${field.default || ''}" 
                       placeholder="${field.label || ''}">
            `;
            fieldsContainer.appendChild(group);
        });

        // Canlı dinleyiciler
        fieldsContainer.querySelectorAll('.editor-input-field').forEach(input => {
            input.addEventListener('input', () => {
                this.updateSvgValues();
            });
        });
    },

    updateSvgValues: function() {
        const previewContainer = document.getElementById('editorSvgContainer');
        if (!previewContainer) return;

        const inputs = document.querySelectorAll('.editor-input-field');
        inputs.forEach(input => {
            const fieldId = input.dataset.fieldId;
            const val = input.value;
            const targetEl = previewContainer.querySelector('#' + fieldId);
            if (targetEl) {
                targetEl.textContent = val;
            }
        });

        // Final SVG çıktısını al
        const finalSvg = previewContainer.innerHTML;
        const hiddenSvgInput = document.getElementById('selectedDesignSvg');
        if (hiddenSvgInput) {
            hiddenSvgInput.value = finalSvg;
        }
    },

    saveAndApply: function() {
        this.updateSvgValues();
        const previewContainer = document.getElementById('editorSvgContainer');
        const finalSvg = previewContainer.innerHTML;

        // Ürün sayfasındaki önizleme kutusunu güncelle
        const pagePreview = document.getElementById('chosenTemplatePreview');
        if (pagePreview) {
            pagePreview.innerHTML = finalSvg;
            pagePreview.style.display = 'block';
        }

        const designTypeInput = document.getElementById('designTypeInput');
        if (designTypeInput) {
            designTypeInput.value = 'online_editor';
        }

        const badge = document.getElementById('chosenTemplateBadge');
        if (badge) {
            badge.style.display = 'inline-block';
            badge.textContent = 'Özel Tasarımınız Hazırlandı (Vektörel)';
        }

        // Modalı kapat
        const modalEl = document.getElementById('templateEditorModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        }
    }
};
