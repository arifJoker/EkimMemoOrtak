/**
 * 🤖 BASKIMATBAA AI PRE-FLIGHT & VEKTÖREL TASARIM MOTORU
 * AI görsellerindeki çözünürlük sorununu çözen, 300 DPI kontrolü yapan ve metinden %100 vektörel SVG üreten modül.
 */
window.AiVectorEngine = {
    // -------------------------------------------------------------------------
    // 1. 🔍 AI PRE-FLIGHT 300 DPI ÇÖZÜNÜRLÜK KONTROLÜ
    // -------------------------------------------------------------------------
    checkResolution: function(file, stdWidthCm = 8.4, stdHeightCm = 5.2, callback) {
        if (!file || !file.type.startsWith('image/')) {
            if (callback) callback({ isImage: false });
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                const pxWidth = img.naturalWidth || img.width;
                const pxHeight = img.naturalHeight || img.height;

                // cm -> inch çevirimi (1 inch = 2.54 cm)
                const widthInches = stdWidthCm / 2.54;
                const heightInches = stdHeightCm / 2.54;

                const dpiX = Math.round(pxWidth / widthInches);
                const dpiY = Math.round(pxHeight / heightInches);
                const effectiveDpi = Math.min(dpiX, dpiY);

                // Matbaa Kalite Değerlendirmesi
                let status = 'high'; // 250+ DPI -> Mükemmel
                let message = '';
                let isAiRaster = false;

                if (effectiveDpi < 150) {
                    status = 'low'; // Genellikle 72-96 DPI AI çıktısı
                    isAiRaster = true;
                    message = `⚠️ <strong>Düşük Çözünürlük Uyarısı (~${effectiveDpi} DPI):</strong> Yüklediğiniz görsel yapay zeka (Midjourney / DALL-E) veya web formatındadır. Matbaada net ve keskin çıkması için <strong>300 DPI</strong> gereklidir. Doğrudan basılırsa yazılar ve logolar bulanık/pikselli çıkabilir.`;
                } else if (effectiveDpi < 250) {
                    status = 'medium';
                    message = `ℹ️ <strong>Orta Çözünürlük (~${effectiveDpi} DPI):</strong> Görsel standart baskı için kabul edilebilir ancak küçük metinlerde hafif kırılma olabilir.`;
                } else {
                    status = 'high';
                    message = `✅ <strong>300 DPI Ultra HD Baskı Kalitesi (~${effectiveDpi} DPI):</strong> Görseliniz ofset matbaa baskısına mükemmel uygundur.`;
                }

                if (callback) {
                    callback({
                        isImage: true,
                        width: pxWidth,
                        height: pxHeight,
                        dpi: effectiveDpi,
                        status: status,
                        isAiRaster: isAiRaster,
                        message: message,
                        dataUrl: e.target.result
                    });
                }
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    },

    // -------------------------------------------------------------------------
    // 2. ✨ METİNDEN %100 VEKTÖREL SVG KARTVİZİT / BROŞÜR ÜRETİCİSİ
    // -------------------------------------------------------------------------
    generateVectorDesigns: function(data) {
        const company = (data.company || 'TamBaskı').trim();
        const industry = (data.industry || 'genel').trim();
        const person = (data.person || 'Arif Uz').trim();
        const title = (data.title || 'Yönetici').trim();
        const phone = (data.phone || '0850 308 00 00').trim();
        const email = (data.email || 'info@tambaski.com.tr').trim();
        const address = (data.address || 'Topkapı Matbaacılar Sitesi Zeytinburnu / İstanbul').trim();
        const website = (data.website || 'tambaski.com.tr').trim();
        const style = data.style || 'gold_navy';

        const safeCompany = this.escapeHtml(company);
        const safePerson = this.escapeHtml(person);
        const safeTitle = this.escapeHtml(title);
        const safePhone = this.escapeHtml(phone);
        const safeEmail = this.escapeHtml(email);
        const safeAddress = this.escapeHtml(address);
        const safeWebsite = this.escapeHtml(website);

        const designs = [];

        // VARYANT 1: 💎 VIP Gold & Luxury Navy (Altın Varak Efektli & Prestij)
        designs.push({
            id: 'ai_v1',
            title: '1. VIP Gold & Derin Lacivert (Yönetici & Prestij)',
            badge: '👑 En Popüler Yönetici Konsepti',
            svg: `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 850 500" width="100%" height="100%" style="font-family:'Montserrat','Segoe UI',Helvetica,sans-serif; background:#0a1128;">
  <defs>
    <linearGradient id="goldGrad1" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#f6d365" />
      <stop offset="50%" stop-color="#fda085" />
      <stop offset="100%" stop-color="#d4af37" />
    </linearGradient>
    <linearGradient id="bgGrad1" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#091428" />
      <stop offset="100%" stop-color="#020b18" />
    </linearGradient>
  </defs>
  <rect width="850" height="500" fill="url(#bgGrad1)"/>
  <path d="M0,0 L350,0 L260,500 L0,500 Z" fill="#040d1c" opacity="0.9"/>
  <line x1="350" y1="0" x2="260" y2="500" stroke="url(#goldGrad1)" stroke-width="4" />
  
  <!-- Logo / Geometri İkonu -->
  <g transform="translate(90, 180)">
    <circle cx="50" cy="50" r="45" fill="none" stroke="url(#goldGrad1)" stroke-width="3"/>
    <polygon points="50,20 75,65 25,65" fill="url(#goldGrad1)" opacity="0.9"/>
    <text x="50" y="130" fill="#ffffff" font-size="18" font-weight="700" letter-spacing="3" text-anchor="middle" id="field_company_v1">${safeCompany}</text>
    <text x="50" y="152" fill="#d4af37" font-size="11" letter-spacing="2" text-anchor="middle">PRESTIGE COLLECTION</text>
  </g>

  <!-- Kişi & İletişim Bilgileri (Vektör) -->
  <g transform="translate(380, 140)">
    <text x="0" y="40" fill="url(#goldGrad1)" font-size="32" font-weight="800" letter-spacing="1" id="field_person_v1">${safePerson}</text>
    <text x="0" y="70" fill="#cbd5e1" font-size="15" font-weight="600" letter-spacing="2" text-transform="uppercase" id="field_title_v1">${safeTitle}</text>
    <line x1="0" y1="95" x2="380" y2="95" stroke="#334155" stroke-width="1.5"/>

    <!-- İletişim Satırları -->
    <g transform="translate(0, 135)" fill="#e2e8f0" font-size="14" font-weight="500">
      <!-- Telefon -->
      <g>
        <circle cx="12" cy="-5" r="14" fill="#0f1f38" stroke="url(#goldGrad1)" stroke-width="1"/>
        <path d="M8,-10 C7,-8 10,-3 14,0 C17,3 19,1 20,-1" fill="none" stroke="#d4af37" stroke-width="2"/>
        <text x="40" y="0" id="field_phone_v1">${safePhone}</text>
      </g>
      <!-- E-Posta -->
      <g transform="translate(0, 45)">
        <circle cx="12" cy="-5" r="14" fill="#0f1f38" stroke="url(#goldGrad1)" stroke-width="1"/>
        <rect x="4" y="-12" width="16" height="12" rx="2" fill="none" stroke="#d4af37" stroke-width="1.5"/>
        <polyline points="4,-10 12,-4 20,-10" fill="none" stroke="#d4af37" stroke-width="1.5"/>
        <text x="40" y="0" id="field_email_v1">${safeEmail}</text>
      </g>
      <!-- Adres & Web -->
      <g transform="translate(0, 90)">
        <circle cx="12" cy="-5" r="14" fill="#0f1f38" stroke="url(#goldGrad1)" stroke-width="1"/>
        <circle cx="12" cy="-5" r="6" fill="none" stroke="#d4af37" stroke-width="1.5"/>
        <text x="40" y="0" id="field_address_v1">${safeAddress} • ${safeWebsite}</text>
      </g>
    </g>
  </g>
</svg>`
        });

        // VARYANT 2: 🖤 Minimalist Asil Siyah & Mat Beyaz (Mimarlık, Ajans & Modern)
        designs.push({
            id: 'ai_v2',
            title: '2. Minimalist Monokrom & Modern Tipografi (Ajans, Mimarlık)',
            badge: '⚡ Ultra Sade & Net',
            svg: `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 850 500" width="100%" height="100%" style="font-family:'Helvetica Neue',Arial,sans-serif; background:#ffffff;">
  <rect width="850" height="500" fill="#ffffff"/>
  <rect x="35" y="35" width="780" height="430" fill="none" stroke="#111827" stroke-width="2"/>
  <rect x="45" y="45" width="760" height="410" fill="none" stroke="#e5e7eb" stroke-width="1"/>

  <!-- Logo & Başlık -->
  <g transform="translate(80, 110)">
    <rect x="0" y="0" width="40" height="40" fill="#111827"/>
    <polygon points="12,12 28,12 28,28 12,28" fill="#ffffff"/>
    <text x="55" y="28" fill="#111827" font-size="24" font-weight="900" letter-spacing="3" text-transform="uppercase" id="field_company_v2">${safeCompany}</text>
    <text x="55" y="48" fill="#6b7280" font-size="11" font-weight="600" letter-spacing="4">STUDIO & DESIGN</text>
  </g>

  <!-- İsim & Unvan -->
  <g transform="translate(80, 260)">
    <text x="0" y="0" fill="#111827" font-size="34" font-weight="800" letter-spacing="-0.5" id="field_person_v2">${safePerson}</text>
    <text x="0" y="28" fill="#4b5563" font-size="14" font-weight="500" letter-spacing="1" id="field_title_v2">${safeTitle}</text>
  </g>

  <!-- İletişim Sağ Blok -->
  <g transform="translate(520, 260)" fill="#374151" font-size="13.5" font-weight="500">
    <text x="0" y="-15" font-weight="700" fill="#111827" id="field_phone_v2">T. ${safePhone}</text>
    <text x="0" y="15" id="field_email_v2">M. ${safeEmail}</text>
    <text x="0" y="45" id="field_website_v2">W. ${safeWebsite}</text>
    <text x="0" y="75" fill="#6b7280" font-size="12" id="field_address_v2">${safeAddress}</text>
  </g>
</svg>`
        });

        // VARYANT 3: 🚀 Modern Teknoloji & Canlı Gradyan (Hizmet, Yazılım, Start-up)
        designs.push({
            id: 'ai_v3',
            title: '3. Modern Gradyan & Dinamik Mavi (Start-up, Teknoloji)',
            badge: '🚀 Dinamik & Çarpıcı',
            svg: `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 850 500" width="100%" height="100%" style="font-family:'Segoe UI',Roboto,sans-serif; background:#0f172a;">
  <defs>
    <linearGradient id="techGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#06b6d4" />
      <stop offset="100%" stop-color="#3b82f6" />
    </linearGradient>
  </defs>
  <rect width="850" height="500" fill="#0f172a"/>
  
  <!-- Dinamik Vektör Şeritler -->
  <path d="M0,0 L400,0 L280,500 L0,500 Z" fill="#1e293b"/>
  <circle cx="800" cy="50" r="180" fill="url(#techGrad)" opacity="0.15"/>
  <circle cx="70" cy="450" r="120" fill="url(#techGrad)" opacity="0.1"/>

  <!-- Logo Sol Bölüm -->
  <g transform="translate(70, 200)">
    <rect x="0" y="0" width="60" height="60" rx="16" fill="url(#techGrad)"/>
    <path d="M20,30 L30,40 L42,20" fill="none" stroke="#ffffff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
    <text x="75" y="32" fill="#ffffff" font-size="22" font-weight="800" id="field_company_v3">${safeCompany}</text>
    <text x="75" y="52" fill="#38bdf8" font-size="12" font-weight="600">INNOVATION & TECH</text>
  </g>

  <!-- Sağ Kişi & İletişim -->
  <g transform="translate(420, 150)">
    <rect x="0" y="0" width="4" height="60" fill="url(#techGrad)" rx="2"/>
    <text x="20" y="28" fill="#ffffff" font-size="30" font-weight="800" id="field_person_v3">${safePerson}</text>
    <text x="20" y="52" fill="#94a3b8" font-size="14" font-weight="600" text-transform="uppercase" id="field_title_v3">${safeTitle}</text>

    <g transform="translate(20, 110)" fill="#cbd5e1" font-size="14">
      <text x="0" y="0" id="field_phone_v3"><tspan fill="#38bdf8" font-weight="700">TEL: </tspan>${safePhone}</text>
      <text x="0" y="35" id="field_email_v3"><tspan fill="#38bdf8" font-weight="700">MAIL: </tspan>${safeEmail}</text>
      <text x="0" y="70" id="field_website_v3"><tspan fill="#38bdf8" font-weight="700">WEB: </tspan>${safeWebsite}</text>
      <text x="0" y="105" id="field_address_v3"><tspan fill="#38bdf8" font-weight="700">ADR: </tspan>${safeAddress}</text>
    </g>
  </g>
</svg>`
        });

        return designs;
    },

    escapeHtml: function(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
};
