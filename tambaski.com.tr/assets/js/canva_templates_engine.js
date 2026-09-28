/**
 * TAMBASKI.COM.TR - Hazır Vektörel Şablon Kütüphanesi & Sektör Motoru
 * PDF Tasarımına %100 Birebir Uygun Hazır Şablonlar
 */

window.CanvaTemplatesEngine = {
    industries: [
        { id: 'all', name: 'Tümü' },
        { id: 'kurumsal', name: 'Kurumsal' },
        { id: 'avukat', name: 'Avukat' },
        { id: 'emlak', name: 'Emlak' },
        { id: 'kafe', name: 'Kafe' },
        { id: 'saglik', name: 'Sağlık' },
        { id: 'mimarlik', name: 'Mimarlık' },
        { id: 'finans', name: 'Finans' },
        { id: 'teknoloji', name: 'Teknoloji' }
    ],

    templates: [
        {
            id: 'tpl_kurumsal_lacivert',
            name: 'Kurumsal Lacivert',
            industry: 'kurumsal',
            bg: '#0f172a',
            textColor: '#ffffff',
            accentColor: '#38bdf8',
            previewSvg: `<svg viewBox="0 0 840 520" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                <rect width="840" height="520" fill="#0f172a" rx="16"/>
                <rect x="50" y="50" width="90" height="90" rx="20" fill="#2563eb"/>
                <text x="160" y="110" fill="#ffffff" font-family="Inter, sans-serif" font-size="34" font-weight="800" letter-spacing="2">FİRMA ADI</text>
                <text x="50" y="270" fill="#ffffff" font-family="Inter, sans-serif" font-size="44" font-weight="800">Ad Soyad</text>
                <text x="50" y="320" fill="#38bdf8" font-family="Inter, sans-serif" font-size="24" font-weight="600">Genel Müdür / CEO</text>
                <line x1="50" y1="360" x2="790" y2="360" stroke="#1e293b" stroke-width="2"/>
                <text x="50" y="410" fill="#94a3b8" font-family="Inter, sans-serif" font-size="20">+90 5XX XXX XX XX</text>
                <text x="50" y="445" fill="#94a3b8" font-family="Inter, sans-serif" font-size="20">ornek@firma.com</text>
                <text x="50" y="480" fill="#38bdf8" font-family="Inter, sans-serif" font-size="20" font-weight="600">www.firma.com</text>
            </svg>`,
            elements: [
                { type: 'rect', left: 50, top: 50, width: 90, height: 90, rx: 20, fill: '#2563eb' },
                { type: 'i-text', left: 160, top: 75, text: 'FİRMA ADI', fill: '#ffffff', fontSize: 34, fontWeight: '800', fontFamily: 'Inter' },
                { type: 'i-text', left: 50, top: 230, text: 'Ad Soyad', fill: '#ffffff', fontSize: 44, fontWeight: '800', fontFamily: 'Inter' },
                { type: 'i-text', left: 50, top: 290, text: 'Genel Müdür / CEO', fill: '#38bdf8', fontSize: 24, fontWeight: '600', fontFamily: 'Inter' },
                { type: 'line', x1: 50, y1: 360, x2: 790, y2: 360, stroke: '#334155', strokeWidth: 2 },
                { type: 'i-text', left: 50, top: 385, text: '+90 5XX XXX XX XX\nornek@firma.com\nwww.firma.com', fill: '#cbd5e1', fontSize: 20, lineHeight: 1.4, fontFamily: 'Inter' }
            ]
        },
        {
            id: 'tpl_beyaz_minimal',
            name: 'Beyaz Minimal',
            industry: 'kurumsal',
            bg: '#ffffff',
            textColor: '#0f172a',
            accentColor: '#2563eb',
            previewSvg: `<svg viewBox="0 0 840 520" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                <rect width="840" height="520" fill="#ffffff" rx="16" stroke="#e2e8f0" stroke-width="4"/>
                <circle cx="95" cy="95" r="45" fill="#2563eb"/>
                <text x="160" y="110" fill="#0f172a" font-family="Inter, sans-serif" font-size="34" font-weight="800">STUDIO CO.</text>
                <text x="50" y="270" fill="#0f172a" font-family="Inter, sans-serif" font-size="44" font-weight="800">Kemal Erdem</text>
                <text x="50" y="320" fill="#64748b" font-family="Inter, sans-serif" font-size="24" font-weight="500">Kreatif Direktör</text>
                <line x1="50" y1="360" x2="790" y2="360" stroke="#e2e8f0" stroke-width="2"/>
                <text x="50" y="420" fill="#475569" font-family="Inter, sans-serif" font-size="20">+90 212 000 00 00  •  info@studioco.com</text>
                <text x="50" y="460" fill="#2563eb" font-family="Inter, sans-serif" font-size="20" font-weight="600">www.studioco.com</text>
            </svg>`,
            elements: [
                { type: 'circle', left: 50, top: 50, radius: 45, fill: '#2563eb' },
                { type: 'i-text', left: 160, top: 75, text: 'STUDIO CO.', fill: '#0f172a', fontSize: 34, fontWeight: '800', fontFamily: 'Inter' },
                { type: 'i-text', left: 50, top: 230, text: 'Kemal Erdem', fill: '#0f172a', fontSize: 44, fontWeight: '800', fontFamily: 'Inter' },
                { type: 'i-text', left: 50, top: 290, text: 'Kreatif Direktör', fill: '#64748b', fontSize: 24, fontWeight: '500', fontFamily: 'Inter' },
                { type: 'line', x1: 50, y1: 360, x2: 790, y2: 360, stroke: '#e2e8f0', strokeWidth: 2 },
                { type: 'i-text', left: 50, top: 390, text: '+90 212 000 00 00  •  info@studioco.com\nwww.studioco.com', fill: '#475569', fontSize: 20, lineHeight: 1.4, fontFamily: 'Inter' }
            ]
        },
        {
            id: 'tpl_altin_prestij',
            name: 'Altın Prestij',
            industry: 'kurumsal',
            bg: '#111827',
            textColor: '#fef3c7',
            accentColor: '#fbbf24',
            previewSvg: `<svg viewBox="0 0 840 520" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                <rect width="840" height="520" fill="#111827" rx="16"/>
                <rect x="30" y="30" width="780" height="460" fill="none" stroke="#d97706" stroke-width="2" rx="12"/>
                <text x="420" y="110" fill="#fbbf24" font-family="Playfair Display, serif" font-size="36" font-weight="700" text-anchor="middle" letter-spacing="4">PRESTIGE GROUP</text>
                <line x1="280" y1="135" x2="560" y2="135" stroke="#d97706" stroke-width="1.5"/>
                <text x="420" y="260" fill="#ffffff" font-family="Inter, sans-serif" font-size="42" font-weight="800" text-anchor="middle">Volkan Yıldırım</text>
                <text x="420" y="310" fill="#fbbf24" font-family="Inter, sans-serif" font-size="22" font-weight="500" text-anchor="middle">Yönetim Kurulu Üyesi</text>
                <text x="420" y="410" fill="#9ca3af" font-family="Inter, sans-serif" font-size="18" text-anchor="middle">+90 532 000 00 00  |  volkan@prestige.com</text>
                <text x="420" y="445" fill="#fbbf24" font-family="Inter, sans-serif" font-size="18" font-weight="700" text-anchor="middle">www.prestigegroup.com.tr</text>
            </svg>`,
            elements: [
                { type: 'rect', left: 30, top: 30, width: 780, height: 460, fill: 'transparent', stroke: '#d97706', strokeWidth: 2, rx: 12 },
                { type: 'i-text', left: 420, top: 80, text: 'PRESTIGE GROUP', fill: '#fbbf24', fontSize: 36, fontWeight: '700', fontFamily: 'Playfair Display', originX: 'center' },
                { type: 'i-text', left: 420, top: 230, text: 'Volkan Yıldırım', fill: '#ffffff', fontSize: 42, fontWeight: '800', fontFamily: 'Inter', originX: 'center' },
                { type: 'i-text', left: 420, top: 285, text: 'Yönetim Kurulu Üyesi', fill: '#fbbf24', fontSize: 22, fontWeight: '500', fontFamily: 'Inter', originX: 'center' },
                { type: 'i-text', left: 420, top: 380, text: '+90 532 000 00 00  |  volkan@prestige.com\nwww.prestigegroup.com.tr', fill: '#cbd5e1', fontSize: 18, lineHeight: 1.4, fontFamily: 'Inter', originX: 'center' }
            ]
        },
        {
            id: 'tpl_hukuk_bordo',
            name: 'Hukuk Bordo',
            industry: 'avukat',
            bg: '#450a0a',
            textColor: '#ffffff',
            accentColor: '#fbbf24',
            previewSvg: `<svg viewBox="0 0 840 520" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                <rect width="840" height="520" fill="#450a0a" rx="16"/>
                <text x="50" y="90" fill="#fbbf24" font-family="Playfair Display, serif" font-size="30" font-weight="700">⚖️ ADALET HUKUK BÜROSU</text>
                <text x="50" y="240" fill="#ffffff" font-family="Playfair Display, serif" font-size="46" font-weight="700">Av. Selin Karaca</text>
                <text x="50" y="290" fill="#fca5a5" font-family="Inter, sans-serif" font-size="22">Arabulucu & Ceza Hukuku Uzmanı</text>
                <line x1="50" y1="330" x2="790" y2="330" stroke="#7f1d1d" stroke-width="2"/>
                <text x="50" y="380" fill="#ffffff" font-family="Inter, sans-serif" font-size="19">Tel: +90 216 000 00 00  •  Gsm: +90 533 000 00 00</text>
                <text x="50" y="420" fill="#fca5a5" font-family="Inter, sans-serif" font-size="18">Adres: Adliye Sarayı Karşısı No:12 Kat:4 Kartal/İST</text>
                <text x="50" y="460" fill="#fbbf24" font-family="Inter, sans-serif" font-size="19" font-weight="600">av.selinkaraca@adalethukuk.com</text>
            </svg>`,
            elements: [
                { type: 'i-text', left: 50, top: 60, text: '⚖️ ADALET HUKUK BÜROSU', fill: '#fbbf24', fontSize: 30, fontWeight: '700', fontFamily: 'Playfair Display' },
                { type: 'i-text', left: 50, top: 200, text: 'Av. Selin Karaca', fill: '#ffffff', fontSize: 46, fontWeight: '700', fontFamily: 'Playfair Display' },
                { type: 'i-text', left: 50, top: 260, text: 'Arabulucu & Ceza Hukuku Uzmanı', fill: '#fca5a5', fontSize: 22, fontFamily: 'Inter' },
                { type: 'line', x1: 50, y1: 330, x2: 790, y2: 330, stroke: '#7f1d1d', strokeWidth: 2 },
                { type: 'i-text', left: 50, top: 360, text: 'Tel: +90 216 000 00 00  •  Gsm: +90 533 000 00 00\nAdres: Adliye Sarayı Karşısı No:12 Kat:4 Kartal/İST\nav.selinkaraca@adalethukuk.com', fill: '#ffffff', fontSize: 19, lineHeight: 1.4, fontFamily: 'Inter' }
            ]
        },
        {
            id: 'tpl_emlak_yesil',
            name: 'Emlak Yeşil',
            industry: 'emlak',
            bg: '#064e3b',
            textColor: '#ffffff',
            accentColor: '#34d399',
            previewSvg: `<svg viewBox="0 0 840 520" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                <rect width="840" height="520" fill="#064e3b" rx="16"/>
                <text x="50" y="90" fill="#34d399" font-family="Inter, sans-serif" font-size="32" font-weight="800">🏡 VİLLA & ARSA GAYRİMENKUL</text>
                <text x="50" y="240" fill="#ffffff" font-family="Inter, sans-serif" font-size="44" font-weight="800">Burak Çetin</text>
                <text x="50" y="290" fill="#a7f3d0" font-family="Inter, sans-serif" font-size="22">Lüks Konut & Yatırım Danışmanı</text>
                <line x1="50" y1="335" x2="790" y2="335" stroke="#047857" stroke-width="2"/>
                <text x="50" y="390" fill="#ffffff" font-family="Inter, sans-serif" font-size="20">📞 +90 542 000 00 00  •  ✉️ burak@villagayrimenkul.com</text>
                <text x="50" y="440" fill="#34d399" font-family="Inter, sans-serif" font-size="20" font-weight="700">🌐 www.villagayrimenkul.com</text>
            </svg>`,
            elements: [
                { type: 'i-text', left: 50, top: 60, text: '🏡 VİLLA & ARSA GAYRİMENKUL', fill: '#34d399', fontSize: 32, fontWeight: '800', fontFamily: 'Inter' },
                { type: 'i-text', left: 50, top: 200, text: 'Burak Çetin', fill: '#ffffff', fontSize: 44, fontWeight: '800', fontFamily: 'Inter' },
                { type: 'i-text', left: 50, top: 260, text: 'Lüks Konut & Yatırım Danışmanı', fill: '#a7f3d0', fontSize: 22, fontFamily: 'Inter' },
                { type: 'line', x1: 50, y1: 335, x2: 790, y2: 335, stroke: '#047857', strokeWidth: 2 },
                { type: 'i-text', left: 50, top: 370, text: '📞 +90 542 000 00 00  •  ✉️ burak@villagayrimenkul.com\n🌐 www.villagayrimenkul.com', fill: '#ffffff', fontSize: 20, lineHeight: 1.5, fontFamily: 'Inter' }
            ]
        },
        {
            id: 'tpl_kafe_kahve',
            name: 'Kafe Kahve',
            industry: 'kafe',
            bg: '#3c2415',
            textColor: '#fef3c7',
            accentColor: '#d97706',
            previewSvg: `<svg viewBox="0 0 840 520" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                <rect width="840" height="520" fill="#3c2415" rx="16"/>
                <text x="420" y="100" fill="#f59e0b" font-family="Playfair Display, serif" font-size="36" font-weight="700" text-anchor="middle">☕ ROASTERY & COFFEE CO.</text>
                <text x="420" y="240" fill="#ffffff" font-family="Inter, sans-serif" font-size="40" font-weight="700" text-anchor="middle">Emre Yılmaz</text>
                <text x="420" y="290" fill="#fde68a" font-family="Inter, sans-serif" font-size="22" text-anchor="middle">Kurucu & Head Barista</text>
                <line x1="250" y1="330" x2="590" y2="330" stroke="#78350f" stroke-width="2"/>
                <text x="420" y="390" fill="#fed7aa" font-family="Inter, sans-serif" font-size="20" text-anchor="middle">Sipariş & Rezervasyon: +90 535 000 00 00</text>
                <text x="420" y="440" fill="#f59e0b" font-family="Inter, sans-serif" font-size="20" font-weight="600" text-anchor="middle">@roasterycoffee  •  www.roasterycoffee.com</text>
            </svg>`,
            elements: [
                { type: 'i-text', left: 420, top: 70, text: '☕ ROASTERY & COFFEE CO.', fill: '#f59e0b', fontSize: 36, fontWeight: '700', fontFamily: 'Playfair Display', originX: 'center' },
                { type: 'i-text', left: 420, top: 200, text: 'Emre Yılmaz', fill: '#ffffff', fontSize: 40, fontWeight: '700', fontFamily: 'Inter', originX: 'center' },
                { type: 'i-text', left: 420, top: 260, text: 'Kurucu & Head Barista', fill: '#fde68a', fontSize: 22, fontFamily: 'Inter', originX: 'center' },
                { type: 'line', x1: 250, y1: 330, x2: 590, y2: 330, stroke: '#78350f', strokeWidth: 2 },
                { type: 'i-text', left: 420, top: 370, text: 'Sipariş & Rezervasyon: +90 535 000 00 00\n@roasterycoffee  •  www.roasterycoffee.com', fill: '#fed7aa', fontSize: 20, lineHeight: 1.5, fontFamily: 'Inter', originX: 'center' }
            ]
        },
        {
            id: 'tpl_klinik_mavi',
            name: 'Klinik Mavi',
            industry: 'saglik',
            bg: '#f0f9ff',
            textColor: '#0369a1',
            accentColor: '#0284c7',
            previewSvg: `<svg viewBox="0 0 840 520" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                <rect width="840" height="520" fill="#f0f9ff" rx="16" stroke="#bae6fd" stroke-width="3"/>
                <text x="50" y="90" fill="#0284c7" font-family="Inter, sans-serif" font-size="30" font-weight="800">🩺 MEDİKAL PARK KLİNİĞİ</text>
                <text x="50" y="240" fill="#0f172a" font-family="Inter, sans-serif" font-size="44" font-weight="800">Dr. Mehmet Aydın</text>
                <text x="50" y="290" fill="#0284c7" font-family="Inter, sans-serif" font-size="22" font-weight="600">Kardiyoloji & Dahiliye Uzmanı</text>
                <line x1="50" y1="335" x2="790" y2="335" stroke="#bae6fd" stroke-width="2"/>
                <text x="50" y="390" fill="#334155" font-family="Inter, sans-serif" font-size="20">Randevu Tel: 0850 000 00 00  •  Gsm: +90 532 000 00 00</text>
                <text x="50" y="440" fill="#0284c7" font-family="Inter, sans-serif" font-size="20" font-weight="700">www.mehmetaydin.com.tr</text>
            </svg>`,
            elements: [
                { type: 'i-text', left: 50, top: 60, text: '🩺 MEDİKAL PARK KLİNİĞİ', fill: '#0284c7', fontSize: 30, fontWeight: '800', fontFamily: 'Inter' },
                { type: 'i-text', left: 50, top: 200, text: 'Dr. Mehmet Aydın', fill: '#0f172a', fontSize: 44, fontWeight: '800', fontFamily: 'Inter' },
                { type: 'i-text', left: 50, top: 260, text: 'Kardiyoloji & Dahiliye Uzmanı', fill: '#0284c7', fontSize: 22, fontWeight: '600', fontFamily: 'Inter' },
                { type: 'line', x1: 50, y1: 335, x2: 790, y2: 335, stroke: '#bae6fd', strokeWidth: 2 },
                { type: 'i-text', left: 50, top: 370, text: 'Randevu Tel: 0850 000 00 00  •  Gsm: +90 532 000 00 00\nwww.mehmetaydin.com.tr', fill: '#334155', fontSize: 20, lineHeight: 1.5, fontFamily: 'Inter' }
            ]
        }
    ],

    renderTemplateGrid: function(containerId, filterIndustry = 'all', searchTerm = '') {
        const container = document.getElementById(containerId);
        if (!container) return;

        let filtered = this.templates.filter(t => {
            const matchInd = (filterIndustry === 'all' || t.industry === filterIndustry);
            const matchSearch = !searchTerm || t.name.toLowerCase().includes(searchTerm.toLowerCase());
            return matchInd && matchSearch;
        });

        if (filtered.length === 0) {
            container.innerHTML = `<div class="col-12 text-center py-4 text-muted small"><i class="bi bi-search fs-4 d-block mb-1"></i>Bu filtreye uygun şablon bulunamadı.</div>`;
            return;
        }

        let html = '';
        filtered.forEach(tpl => {
            html += `
            <div class="col-6 mb-2">
                <div class="card h-100 border rounded-3 overflow-hidden shadow-2xs cursor-pointer template-choice-item" onclick="CanvaStudio.loadTemplateById('${tpl.id}')" style="transition: transform 0.15s ease, border-color 0.15s ease;">
                    <div class="p-1 bg-light border-bottom d-flex align-items-center justify-content-center" style="aspect-ratio: 84/52;">
                        ${tpl.previewSvg}
                    </div>
                    <div class="p-1 px-2 text-center bg-white">
                        <div class="fw-bold text-dark text-truncate" style="font-size: 11px;">${tpl.name}</div>
                    </div>
                </div>
            </div>`;
        });

        container.innerHTML = html;
    }
};
