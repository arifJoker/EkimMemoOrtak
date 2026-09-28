/**
 * Canva-Style Interactive Vector Design Studio Pro
 * Baskı ve Matbaa Özel: Ön/Arka Yüz Desteği, Dinamik Ebat & Cetveller, Canlı 3D Mockup ve Zengin Şablon Kütüphanesi
 */

const CanvaStudio = {
    canvas: null,
    width: 850,
    height: 500,
    widthCm: 8.4,
    heightCm: 5.2,
    productName: 'Kartvizit',
    unit: 'mm',
    bleedMm: 3,
    safeMm: 4,
    showGuides: true,
    
    // Ön / Arka Yüz Durum Yönetimi
    currentSide: 'front', // 'front' | 'back'
    isDoubleSided: true,
    sidesData: {
        front: { json: null, svg: '' },
        back: { json: null, svg: '' }
    },

    activeCategory: 'all',
    searchKeyword: '',
    history: [],
    historyIndex: -1,
    isHistoryProcessing: false,

    // Vektörel İkon Kütüphanesi
    icons: {
        phone: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79a15.053 15.053 0 006.59 6.59l2.2-2.2a1 1 0 011.02-.24c1.12.37 2.33.57 3.57.57a1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.25.2 2.45.57 3.57a1 1 0 01-.25 1.02l-2.2 2.2z"/></svg>',
        mobile: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M17 1.01L7 1c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-1.99-2-1.99zM17 19H7V5h10v14zm-5 1a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/></svg>',
        whatsapp: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-5.46-4.45-9.92-9.91-9.92zm5.78 14.07c-.24.68-1.2 1.25-1.66 1.28-.43.03-.98.15-3.15-.74-2.78-1.15-4.57-3.98-4.71-4.17-.14-.19-1.12-1.49-1.12-2.84 0-1.35.7-2.02.95-2.29.25-.27.55-.34.73-.34.18 0 .37 0 .53.01.17.01.4.06.61.57.24.57.82 2.01.89 2.16.07.15.12.33.02.53-.1.2-.15.32-.3.49-.15.17-.31.38-.45.51-.15.15-.31.31-.13.62.18.31.8 1.32 1.72 2.14 1.18 1.05 2.18 1.37 2.49 1.52.31.15.49.13.67-.08.18-.21.78-.91.99-1.22.21-.31.42-.26.71-.15.29.11 1.83.86 2.15 1.02.32.16.53.24.61.37.08.13.08.76-.16 1.44z"/></svg>',
        mail: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>',
        location: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z"/></svg>',
        globe: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm7.93 9h-3.18a15.7 15.7 0 00-1.39-5.08A8.02 8.02 0 0119.93 11zM12 4.07c.83 1.2 1.5 2.59 1.95 4.93H10.05c.45-2.34 1.12-3.73 1.95-4.93zM4.07 13h3.18c.24 1.8.74 3.54 1.39 5.08A8.02 8.02 0 014.07 13zm3.18-2H4.07a8.02 8.02 0 014.57-5.08c-.65 1.54-1.15 3.28-1.39 5.08zM12 19.93c-.83-1.2-1.5-2.59-1.95-4.93h3.9c-.45 2.34-1.12 3.73-1.95 4.93zm2.64-6.93H9.36a13.78 13.78 0 010-2h5.28c0 .67.04 1.34 0 2zm.72 5.08c.65-1.54 1.15-3.28 1.39-5.08h3.18a8.02 8.02 0 01-4.57 5.08z"/></svg>',
        instagram: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>',
        linkedin: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>',
        facebook: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95C18.05 21.45 22 17.19 22 12z"/></svg>',
        twitter: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
        telegram: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.75-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/></svg>',
        youtube: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M21.58 7.19a2.504 2.504 0 0 0-1.77-1.77C18.25 5 12 5 12 5s-6.25 0-7.81.42a2.504 2.504 0 0 0-1.77 1.77C2 8.75 2 12 2 12s0 3.25.42 4.81c.24.89.94 1.58 1.77 1.77C5.75 19 12 19 12 19s6.25 0 7.81-.42a2.504 2.504 0 0 0 1.77-1.77C22 15.25 22 12 22 12s0-3.25-.42-4.81zM10 15V9l5.2 3-5.2 3z"/></svg>',
        scale: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M5 7l7-4 7 4M2 17l3-6 3 6a3 3 0 0 1-6 0zm14 0l3-6 3 6a3 3 0 0 1-6 0zM5 22h14"/></svg>',
        helmet: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 3a9 9 0 00-9 9v3a1 1 0 001 1h16a1 1 0 001-1v-3a9 9 0 00-9-9zm-1 3h2v8h-2V6zm-6 8c.3-4.1 3.5-7.3 7.6-7.5V14H5zm14 0h-5.6V6.5c4.1.2 7.3 3.4 7.6 7.5zM2 18h20v2H2z"/></svg>',
        building: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 2H9c-1.1 0-2 .9-2 2v3H5c-1.1 0-2 .9-2 2v11c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM5 20V9h2v11H5zm8 0h-4V4h4v16zm6 0h-4V4h4v16zm-5-14h-2V4h2v2zm0 4h-2V8h2v2zm0 4h-2v-2h2v2zm0 4h-2v-2h2v2zm4-12h-2V4h2v2zm0 4h-2V8h2v2zm0 4h-2v-2h2v2zm0 4h-2v-2h2v2z"/></svg>',
        house: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>',
        key: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M7 14A5 5 0 0112 9h7v2h2v2h-2v2h-2v2h-4.08A5.002 5.002 0 017 14zm0-3a3 3 0 100 6 3 3 0 000-6z"/></svg>',
        stethoscope: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 8c-.55 0-1 .45-1 1v4c0 2.76-2.24 5-5 5s-5-2.24-5-5V4.5a2.5 2.5 0 015 0V7h2V4.5a4.5 4.5 0 00-9 0V13c0 3.86 3.14 7 7 7s7-3.14 7-7V9c0-.55-.45-1-1-1zm1 5c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/></svg>',
        tooth: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8 2 6 4.5 6 8c0 3.2 1.5 8 2.5 12 .5 2 1.5 2 2.5 1 1-1 1-3 1-5 0 2 0 4 1 5s2 1 2.5-1c1-4 2.5-8.8 2.5-12 0-3.5-2-6-6-6zm-2 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/></svg>',
        restaurant: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/></svg>',
        coffee: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M2 19h18v2H2v-2zm2-17h13c1.1 0 2 .9 2 2v4c0 2.21-1.79 4-4 4h-2.1c-.48 2.29-2.51 4-4.9 4H8c-2.76 0-5-2.24-5-5V3c0-.55.45-1 1-1zm11 6h2c1.1 0 2-.9 2-2V4h-4v4z"/></svg>',
        scissors: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M6 15c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3c0-.36-.07-.7-.18-1.02L12 14.5l3.18 2.48c-.11.32-.18.66-.18 1.02 0 1.66 1.34 3 3 3s3-1.34 3-3-1.34-3-3-3c-.93 0-1.75.43-2.31 1.09L12 13.24l-3.69 2.85C7.75 15.43 6.93 15 6 15zm0 4.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm12 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM9.64 7.64L12 9.5l7.55-5.87-1.22-1.57L12 6.8 5.67 2.06 4.45 3.63l5.19 4.01z"/></svg>',
        car: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.22.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.04 3H5.81l1.04-3zM19 17H5v-4.66l.12-.34h13.77l.11.34V17zM7.5 13c-.83 0-1.5.67-1.5 1.5S6.67 16 7.5 16s1.5-.67 1.5-1.5S8.33 13 7.5 13zm9 0c-.83 0-1.5.67-1.5 1.5s.67 1.5 1.5 1.5 1.5-.67 1.5-1.5-.67-1.5-1.5-1.5z"/></svg>',
        truck: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zm-14 9c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm12 0c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm2-7.5l2 2.5h-4v-2.5h2z"/></svg>',
        chart: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M3.5 18.49l6-6.01 4 4L22 6.92l-1.41-1.41-7.09 7.97-4-4L2 16.99z"/><path d="M5 21h16v2H3V3h2z"/></svg>',
        code: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M9.4 16.6L4.8 12l4.6-4.6L8 6l-6 6 6 6 1.4-1.4zm5.2 0l4.6-4.6-4.6-4.6L16 6l6 6-6 6-1.4-1.4z"/></svg>',
        camera: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 2L7.17 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2h-3.17L15 2H9zm3 15c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/></svg>',
        education: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zm0 13c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>',
        crown: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .55-.45 1-1 1H6c-.55 0-1-.45-1-1v-1h14v1z"/></svg>',
        star: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>',
        diamond: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 2H8L3 8l9 14 9-14-5-6zm-1.4 2l3.33 4h-4.47l.86-4h.28zM9.12 4h5.76l-.86 4H9.98l-.86-4zm-3.05 4l3.33-4h.28l.86 4H6.07zM5.1 9.5h4.14l2.76 8.5L5.1 9.5zm5.66 0h2.48L12 17.18 10.76 9.5zm3.9 0h4.24l-6.9 8.5 2.66-8.5z"/></svg>',
        badge: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/></svg>',
        shield: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1 14.5l-3.5-3.5 1.41-1.41L11 12.67l5.59-5.59L18 8.5l-7 7z"/></svg>',
        heart: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>',
        trophy: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94A5.01 5.01 0 0011 15.9V19H7v2h10v-2h-4v-3.1c1.94-.37 3.5-1.89 3.61-3.96C19.08 11.63 21 9.55 21 8V7c0-1.1-.9-2-2-2zM5 8V7h2v3.82C5.84 10.4 5 9.3 5 8zm14 0c0 1.3-.84 2.4-2 2.82V7h2v1z"/></svg>',
        bag: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 6h-2c0-2.21-1.79-4-4-4S8 3.79 8 6H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-6-2c1.1 0 2 .9 2 2h-4c0-1.1.9-2 2-2zm6 16H6V8h2v2c0 .55.45 1 1 1s1-.45 1-1V8h4v2c0 .55.45 1 1 1s1-.45 1-1V8h2v12z"/></svg>'
    },

    init: function(options) {
        options = options || {};
        this.widthCm = parseFloat(options.widthCm) || 8.4;
        this.heightCm = parseFloat(options.heightCm) || 5.2;
        this.productName = options.productName || 'Matbaa Baskı';
        this.isDoubleSided = (options.isDoubleSided !== false);
        this.currentSide = 'front';
        this.sidesData = {
            front: { json: null, svg: '' },
            back: { json: null, svg: '' }
        };

        // En / Boy Oranına Göre Dinamik Piksel Ebatı Hesapla
        const aspect = this.widthCm / this.heightCm;
        if (aspect >= 1) {
            // Yatay ebat (örn: Kartvizit 8.4 x 5.2, A4 Yatay, Banner)
            this.width = 850;
            this.height = Math.round(850 / aspect);
        } else {
            // Dikey ebat (örn: A5 Broşür 14.8 x 21, A4 Dikey, Yelken Bayrak 75 x 300)
            this.height = 700;
            this.width = Math.round(700 * aspect);
            if (this.width < 220) this.width = 220; // Aşırı dar bayraklar için min genişlik
        }

        const canvasEl = document.getElementById('canvaMainCanvas');
        if (!canvasEl) return;

        if (this.canvas) {
            try { this.canvas.dispose(); } catch(e){}
        }

        canvasEl.width = this.width;
        canvasEl.height = this.height;

        this.canvas = new fabric.Canvas('canvaMainCanvas', {
            width: this.width,
            height: this.height,
            backgroundColor: '#ffffff',
            preserveObjectStacking: true,
            selection: true
        });

        // Canlı Ölçü Cetvellerini & Ebat Yazılarını Güncelle
        this.updateDimensionRulers();
        this.updateSideSwitchUI();
        this.drawGuides();

        this.canvas.on('selection:created', (e) => this.handleSelection(e));
        this.canvas.on('selection:updated', (e) => this.handleSelection(e));
        this.canvas.on('selection:cleared', () => this.handleSelectionClear());
        this.canvas.on('object:moving', (e) => {
            if (e.target) this.clampObjectInsideBounds(e.target);
        });
        this.canvas.on('object:scaling', (e) => {
            if (e.target) this.clampObjectScaling(e.target);
        });
        this.canvas.on('object:modified', (e) => {
            if (e.target) this.clampObjectInsideBounds(e.target);
            this.saveState();
        });
        this.canvas.on('object:added', (e) => {
            if (!e.target.isGuide) this.saveState();
        });

        this.renderTemplatesSidebar();

        if (options.templateKey) {
            this.loadBuiltinTemplate(options.templateKey);
        } else if (options.initialSvg) {
            this.loadSvgString(options.initialSvg);
        } else {
            this.loadBuiltinTemplate('vip_onyx_gold');
        }

        this.bindEvents();
        this.bindKeyboardShortcuts();
        this.syncFromMainForm();
        if (window.innerWidth < 768) {
            this.closeMobileDrawer();
        }
        this.updateResponsiveScale();
        window.addEventListener('resize', () => this.updateResponsiveScale());
    },

    // ==========================================
    // ⚡ CANVA İÇİ HIZLI BASKI AYARLARI & İNCE AYAR
    // ==========================================
    syncFromMainForm: function() {
        // 1. Paket Senkronizasyonu
        const activePkgRadio = document.querySelector('input[name="selected_package"]:checked');
        const currentPkg = activePkgRadio ? activePkgRadio.value : (window.currentSelectedPackageKey || 'ekonomik');
        document.querySelectorAll('.canva-pkg-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.pkg === currentPkg || btn.id === 'canva_btn_pkg_' + currentPkg);
        });

        // 2. Adet Senkronizasyonu
        const activeQtyRadio = document.querySelector('input[name="quantity"]:checked');
        const qtySelect = document.getElementById('canvaStudioQtySelect');
        const customQtyInputWrap = document.getElementById('canvaCustomQtyInputWrap');
        const customQtyInput = document.getElementById('canvaStudioCustomQtyInput');
        
        if (activeQtyRadio && qtySelect) {
            const qtyVal = activeQtyRadio.value;
            let foundOption = false;
            for (let opt of qtySelect.options) {
                if (opt.value === qtyVal) {
                    qtySelect.value = qtyVal;
                    foundOption = true;
                    break;
                }
            }
            if (!foundOption) {
                qtySelect.value = 'custom';
                if (customQtyInputWrap) customQtyInputWrap.style.display = 'block';
                if (customQtyInput) customQtyInput.value = qtyVal;
            } else {
                if (customQtyInputWrap) customQtyInputWrap.style.display = 'none';
            }
        }

        // 3. Ebat / Ölçü Senkronizasyonu
        const isCustomSizeRadio = document.getElementById('sizeCustom');
        const canvaSizeCustomRadio = document.getElementById('canvaSizeCustom');
        const canvaSizeStdRadio = document.getElementById('canvaSizeStd');
        const customDimInputs = document.getElementById('canvaCustomDimInputs');
        const customWInp = document.getElementById('customWidth');
        const customHInp = document.getElementById('customHeight');
        const canvaInpW = document.getElementById('canvaInpWidth');
        const canvaInpH = document.getElementById('canvaInpHeight');

        if (isCustomSizeRadio && isCustomSizeRadio.checked) {
            if (canvaSizeCustomRadio) canvaSizeCustomRadio.checked = true;
            if (customDimInputs) customDimInputs.style.display = 'flex';
            if (customWInp && canvaInpW) canvaInpW.value = customWInp.value;
            if (customHInp && canvaInpH) canvaInpH.value = customHInp.value;
        } else {
            if (canvaSizeStdRadio) canvaSizeStdRadio.checked = true;
            if (customDimInputs) customDimInputs.style.display = 'none';
        }

        // 4. Net Fiyatı Canlı Güncelle
        if (typeof window.calculateLivePrice === 'function') {
            window.calculateLivePrice();
        }
    },

    checkDoubleSidedState: function() {
        let isDouble = (this.selectedPackage !== 'ekonomik' && window.currentSelectedPackageKey !== 'ekonomik');

        // Formdaki ve fine-tune çekmecesindeki tüm aktif seçimleri tara
        document.querySelectorAll('#printConfigForm input:checked, #printConfigForm select, #canvaStudioFineTuneDrawer input:checked').forEach(el => {
            const text = (el.selectedOptions ? el.selectedOptions[0]?.text : (el.nextElementSibling?.textContent || el.value || '')).toLowerCase();
            if (text.includes('tek yön') || text.includes('tek taraf') || text.includes('sadece ön') || text.includes('arka boş')) {
                isDouble = false;
            } else if (text.includes('çift yön') || text.includes('çift taraf') || text.includes('arkalı önlü') || text.includes('ön & arka')) {
                isDouble = true;
            }
        });

        this.isDoubleSided = isDouble;
        this.updateSideSwitchUI();

        // Tek yön seçildiyse ve şu anda arka yüzdeyse hemen ön yüze geç
        if (!this.isDoubleSided && this.currentSide === 'back') {
            this.currentSide = 'front';
            this.updateSideSwitchUI();
            if (this.sidesData.front.json) {
                this.canvas.clear();
                this.canvas.loadFromJSON(this.sidesData.front.json, () => {
                    this.drawGuides();
                    this.canvas.renderAll();
                    this.checkObjectBoundaries();
                });
            }
        }

        return this.isDoubleSided;
    },

    setPackage: function(pkgKey) {
        window.currentSelectedPackageKey = pkgKey;
        this.selectedPackage = pkgKey;
        
        // Canva Studio butonlarını güncelle
        document.querySelectorAll('.canva-pkg-btn').forEach(btn => {
            btn.classList.toggle('active', btn.id === 'canva_btn_pkg_' + pkgKey || btn.dataset.pkg === pkgKey);
        });

        // Ana formdaki paket seçimini tetikle
        const mainPkgCard = document.getElementById('card_pkg_' + pkgKey);
        if (typeof selectPackage === 'function') {
            selectPackage(pkgKey, mainPkgCard);
        }

        this.checkDoubleSidedState();

        if (typeof window.calculateLivePrice === 'function') {
            window.calculateLivePrice();
        }
    },

    setQuantity: function(qtyVal) {
        const customWrap = document.getElementById('canvaCustomQtyInputWrap');
        const customInput = document.getElementById('canvaStudioCustomQtyInput');

        if (qtyVal === 'custom') {
            if (customWrap) customWrap.style.display = 'block';
            if (customInput) {
                customInput.focus();
                this.setCustomQuantity(customInput.value || 500);
            }
        } else {
            if (customWrap) customWrap.style.display = 'none';
            const matchingBox = document.getElementById('qty_box_' + qtyVal);
            if (typeof selectQuantity === 'function') {
                selectQuantity(qtyVal, matchingBox);
            }
            if (typeof window.calculateLivePrice === 'function') {
                window.calculateLivePrice();
            }
        }
    },

    setCustomQuantity: function(val) {
        const numVal = parseInt(val) || 10;
        const customBox = document.getElementById('qty_box_custom');
        const manualInput = document.getElementById('manualCustomQtyInput');
        const customRadio = document.getElementById('customQtyRadio');

        if (typeof activateCustomQty === 'function') {
            activateCustomQty(customBox);
        }
        if (manualInput) manualInput.value = numVal;
        if (customRadio) {
            customRadio.value = numVal;
            customRadio.checked = true;
            customRadio.dispatchEvent(new Event('change'));
        }

        if (typeof window.calculateLivePrice === 'function') {
            window.calculateLivePrice();
        }
    },

    toggleFineTunePanel: function() {
        const drawer = document.getElementById('canvaStudioFineTuneDrawer');
        const chevron = document.getElementById('canvaFineTuneChevron');
        if (!drawer) return;

        if (drawer.style.display === 'none' || drawer.style.display === '') {
            drawer.style.display = 'block';
            if (chevron) chevron.style.transform = 'rotate(180deg)';
        } else {
            drawer.style.display = 'none';
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        }
    },

    onSizeModeChange: function(mode) {
        const customDimInputs = document.getElementById('canvaCustomDimInputs');
        const isCustom = (mode === 'custom');
        
        if (customDimInputs) {
            customDimInputs.style.display = isCustom ? 'flex' : 'none';
        }

        const sizeStdRadio = document.getElementById('sizeStandard');
        const sizeCustomRadio = document.getElementById('sizeCustom');
        if (isCustom && sizeCustomRadio) {
            sizeCustomRadio.checked = true;
            if (typeof toggleCustomSizeInput === 'function') toggleCustomSizeInput(true);
            this.onDimensionChange();
        } else if (!isCustom && sizeStdRadio) {
            sizeStdRadio.checked = true;
            if (typeof toggleCustomSizeInput === 'function') toggleCustomSizeInput(false);
            
            // Standart ebatlara dön
            const stdW = parseFloat(document.getElementById('canvaInpWidth')?.defaultValue) || 8.4;
            const stdH = parseFloat(document.getElementById('canvaInpHeight')?.defaultValue) || 5.2;
            this.resizeCanvasDimension(stdW, stdH);
        }

        if (typeof window.calculateLivePrice === 'function') {
            window.calculateLivePrice();
        }
    },

    onDimensionChange: function() {
        const inpW = document.getElementById('canvaInpWidth');
        const inpH = document.getElementById('canvaInpHeight');
        if (!inpW || !inpH) return;

        const w = parseFloat(inpW.value) || 8.4;
        const h = parseFloat(inpH.value) || 5.2;

        // Ana formdaki inputları güncelle
        const mainW = document.getElementById('customWidth');
        const mainH = document.getElementById('customHeight');
        if (mainW) mainW.value = w;
        if (mainH) mainH.value = h;

        this.resizeCanvasDimension(w, h);

        if (typeof window.calculateLivePrice === 'function') {
            window.calculateLivePrice();
        }
    },

    resizeCanvasDimension: function(wCm, hCm) {
        this.widthCm = Math.max(1, wCm);
        this.heightCm = Math.max(1, hCm);

        const aspect = this.widthCm / this.heightCm;
        if (aspect >= 1) {
            this.width = 850;
            this.height = Math.round(850 / aspect);
        } else {
            this.height = 700;
            this.width = Math.round(700 * aspect);
            if (this.width < 220) this.width = 220;
        }

        const canvasEl = document.getElementById('canvaMainCanvas');
        if (canvasEl && this.canvas) {
            this.canvas.setWidth(this.width);
            this.canvas.setHeight(this.height);
            this.canvas.renderAll();
        }

        this.updateDimensionRulers();
        this.drawGuides();
        this.updateResponsiveScale();
    },

    onPaperChange: function(paperId) {
        const pRadio = document.getElementById('paper_opt_' + paperId) || document.querySelector(`input[name="paper_id"][value="${paperId}"]`);
        if (pRadio) {
            pRadio.checked = true;
            pRadio.dispatchEvent(new Event('change', { bubbles: true }));
        }
        if (typeof window.calculateLivePrice === 'function') {
            window.calculateLivePrice();
        }
    },

    onFineTuneOptionChange: function(name, val) {
        const mainInput = document.querySelector(`#printConfigForm input[name="${name}"][value="${val}"]`);
        if (mainInput) {
            mainInput.checked = true;
            mainInput.dispatchEvent(new Event('change', { bubbles: true }));
        }

        // Çift / Tek yön durumunu anında güncelle
        this.checkDoubleSidedState();

        if (typeof window.calculateLivePrice === 'function') {
            window.calculateLivePrice();
        }
    },

    onFinishingChange: function(finKey) {
        if (finKey === 'mat_cift') {
            this.setPackage('standart');
        } else if (finKey === 'kadife' || finKey === 'lak') {
            this.setPackage('premium');
        } else if (finKey === 'altin') {
            this.setPackage('vip');
        }
    },

    // ==========================================
    // 📏 CANLI KENAR CETVELLERİ & EBAT ETİKETLERİ
    // ==========================================
    updateDimensionRulers: function() {
        const topRulerVal = document.getElementById('rulerWidthValue');
        const leftRulerVal = document.getElementById('rulerHeightValue');
        const dimLabel = document.getElementById('rulerTotalDim');
        const headerDim = document.getElementById('canvaHeaderDimInfo');

        const wStr = this.widthCm.toFixed(1) + ' cm';
        const hStr = this.heightCm.toFixed(1) + ' cm';
        const totalStr = `${wStr} × ${hStr}`;

        if (topRulerVal) topRulerVal.textContent = `${wStr} (${Math.round(this.widthCm * 10)} mm)`;
        if (leftRulerVal) leftRulerVal.textContent = `${hStr} (${Math.round(this.heightCm * 10)} mm)`;
        if (dimLabel) dimLabel.textContent = totalStr;
        if (headerDim) headerDim.textContent = `Baskı Ebatı: ${totalStr} • 300 DPI Vektörel Baskı`;
    },

    // ==========================================
    // 📄 ÖN / ARKA YÜZ YÖNETİMİ
    // ==========================================
    updateSideSwitchUI: function() {
        const btnFront = document.getElementById('btnSideFront');
        const btnBack = document.getElementById('btnSideBack');
        const lockIcon = document.getElementById('backSideLockIcon');

        if (btnFront) {
            btnFront.classList.toggle('btn-primary', this.currentSide === 'front');
            btnFront.classList.toggle('btn-dark', this.currentSide !== 'front');
        }

        if (btnBack) {
            if (!this.isDoubleSided) {
                // Tek Yön baskıda Arka Yüz butonunu kilitli & soluk yap
                btnBack.classList.remove('btn-primary');
                btnBack.classList.add('btn-secondary');
                btnBack.style.opacity = '0.45';
                btnBack.style.cursor = 'not-allowed';
                btnBack.setAttribute('title', 'Tek Yön Baskı Seçili (Arka Yüz Yok)');
                if (lockIcon) lockIcon.style.display = 'inline-block';
            } else {
                btnBack.style.opacity = '1';
                btnBack.style.cursor = 'pointer';
                btnBack.removeAttribute('title');
                btnBack.classList.toggle('btn-primary', this.currentSide === 'back');
                btnBack.classList.toggle('btn-dark', this.currentSide !== 'back');
                btnBack.classList.remove('btn-secondary');
                if (lockIcon) lockIcon.style.display = 'none';
            }
        }
    },

    switchSide: function(targetSide) {
        if (this.currentSide === targetSide) return;

        // Tek Yön baskı seçilmişse arka yüze geçişi engelle ve uyar
        if (targetSide === 'back' && !this.checkDoubleSidedState()) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'Tek Yön Baskı Seçili 🔒',
                    html: `Mevcut baskı paketiniz <strong>Tek Yön (Sadece Ön)</strong> olarak yapılandırılmıştır.<br><br>Arka yüzü de tasarlamak için ürün seçeneklerinden <strong>Çift Yön Baskı</strong> seçeneğini tercih edebilirsiniz.`,
                    confirmButtonText: 'Anladım',
                    confirmButtonColor: '#0071e3'
                });
            } else {
                alert("Seçili paketiniz Tek Yön baskıdır. Çift yön tasarlamak için çift yönlü paket seçiniz.");
            }
            return;
        }

        // 1. Mevcut yüzün durumunu kaydet
        this.sidesData[this.currentSide].json = JSON.stringify(this.canvas.toJSON(['isGuide']));
        this.sidesData[this.currentSide].svg = this.exportCleanSvg();

        // 2. Yüzü değiştir
        this.currentSide = targetSide;
        this.updateSideSwitchUI();

        // 3. Hedef yüzün durumunu yükle
        this.canvas.clear();
        if (this.sidesData[targetSide].json) {
            this.canvas.loadFromJSON(this.sidesData[targetSide].json, () => {
                this.drawGuides();
                this.canvas.renderAll();
                this.checkObjectBoundaries();
            });
        } else {
            // İlk kez arka yüze geçiliyorsa temiz zarif bir arka plan oluştur
            this.generateDefaultBackSide();
        }
    },

    generateDefaultBackSide: function() {
        const bg = this.canvas.backgroundColor || '#0b0e17';
        this.canvas.setBackgroundColor(bg, this.canvas.renderAll.bind(this.canvas));

        // Şık ortalanmış marka logosu veya QR alanı
        const centerLogo = new fabric.IText('FİRMA ADI', {
            left: this.width / 2,
            top: (this.height / 2) - 30,
            originX: 'center',
            originY: 'center',
            fontFamily: 'Montserrat',
            fontSize: 34,
            fontWeight: '900',
            fill: (bg === '#ffffff' || bg === '#f8fafc' || bg === '#fff5f5') ? '#0f172a' : '#ffffff'
        });

        const centerSlogan = new fabric.IText('www.firmaniz.com', {
            left: this.width / 2,
            top: (this.height / 2) + 20,
            originX: 'center',
            originY: 'center',
            fontFamily: 'Inter',
            fontSize: 14,
            fill: (bg === '#ffffff' || bg === '#f8fafc') ? '#0071e3' : '#d4af37',
            charSpacing: 80
        });

        this.canvas.add(centerLogo, centerSlogan);
        this.drawGuides();
        this.canvas.requestRenderAll();
        this.saveState();
    },

    // ==========================================
    // 👁️ 3D & MOCKUP CANLI ÖNİZLEME
    // ==========================================
    open3dMockup: function() {
        let currentSvg = '';
        if (this.canvas) {
            currentSvg = this.exportCleanSvg();
            if (this.sidesData && this.sidesData[this.currentSide]) {
                this.sidesData[this.currentSide].svg = currentSvg;
            }
        }

        const modalEl = document.getElementById('canva3dMockupModal');
        const cardInner = document.getElementById('mockup3dCardInner');
        const frontContainer = document.getElementById('mockup3dFrontFace');
        const backContainer = document.getElementById('mockup3dBackFace');
        const flipBtn = document.getElementById('btnFlip3dMockup');

        let frontSvg = (this.sidesData && this.sidesData.front.svg) || currentSvg || window.savedFrontSvg || document.getElementById('selectedDesignSvg')?.value || '';
        let backSvg = (this.sidesData && this.sidesData.back.svg) || window.savedBackSvg || document.getElementById('selectedDesignBackSvg')?.value || '';

        // Ensure SVG has responsive width/height
        if (frontSvg) {
            frontSvg = frontSvg.replace(/<svg\b([^>]*)>/i, '<svg$1 style="width:100%;height:100%;object-fit:contain;display:block;" preserveAspectRatio="xMidYMid meet">');
            frontSvg = frontSvg.replace(/(\s+width="[^"]*")|(\s+height="[^"]*")/i, '');
        }
        if (backSvg) {
            backSvg = backSvg.replace(/<svg\b([^>]*)>/i, '<svg$1 style="width:100%;height:100%;object-fit:contain;display:block;" preserveAspectRatio="xMidYMid meet">');
            backSvg = backSvg.replace(/(\s+width="[^"]*")|(\s+height="[^"]*")/i, '');
        }

        // Orantılı kart ebatı hesaplama (kenarlarda boşluk / beyaz letterbox kalmasın)
        if (cardInner) {
            const aspect = (this.width || 850) / (this.height || 500);
            let targetW = 460;
            let targetH = Math.round(460 / aspect);
            
            if (aspect < 1) {
                // Dikey (ör. Broşür, Yelken Bayrak)
                targetH = 400;
                targetW = Math.round(400 * aspect);
                if (targetW < 180) targetW = 180;
            } else if (targetH > 320) {
                targetH = 320;
                targetW = Math.round(320 * aspect);
            }
            cardInner.style.width = targetW + 'px';
            cardInner.style.height = targetH + 'px';
            cardInner.classList.remove('flipped');
        }

        if (frontContainer) {
            frontContainer.className = 'mockup-face position-absolute w-100 h-100 rounded-3 shadow-lg overflow-hidden border';
            frontContainer.innerHTML = frontSvg || '<div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">Tasarım Yok</div>';
        }

        if (backContainer) {
            backContainer.className = 'mockup-face position-absolute w-100 h-100 rounded-3 shadow-lg overflow-hidden border';
            if (backSvg) {
                backContainer.innerHTML = backSvg;
                if (flipBtn) flipBtn.style.display = 'inline-flex';
            } else {
                backContainer.innerHTML = `<div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted" style="background:#f1f5f9; font-size:13px; font-weight:500;"><span>Tek Yön Baskı (Arka Yüz Boş)</span></div>`;
                if (flipBtn) flipBtn.style.display = (this.isDoubleSided && backSvg) ? 'inline-flex' : 'none';
            }
        }

        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    },

    toggle3dFlip: function() {
        const cardInner = document.getElementById('mockup3dCardInner');
        const flipLabel = document.getElementById('btn3dFlipLabel');
        if (cardInner) {
            cardInner.classList.toggle('flipped');
            if (flipLabel) {
                flipLabel.textContent = cardInner.classList.contains('flipped') ? 'Ön Yüzü Göster' : 'Arka Yüzü Göster';
            }
        }
    },

    saveFromMockup: function() {
        const modalEl = document.getElementById('canva3dMockupModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        }
        this.saveAndApplyToOrder();
    },

    bindEvents: function() {
        const zoomRange = document.getElementById('canvaZoomRange');
        if (zoomRange) {
            zoomRange.addEventListener('input', (e) => {
                this.setZoom(parseFloat(e.target.value));
            });
        }

        const fontSelect = document.getElementById('canvaFontFamily');
        if (fontSelect) {
            fontSelect.addEventListener('change', (e) => {
                this.setFontFamily(e.target.value);
            });
        }

        const fontSizeInput = document.getElementById('canvaFontSize');
        if (fontSizeInput) {
            fontSizeInput.addEventListener('input', (e) => {
                this.setFontSize(parseInt(e.target.value) || 16);
            });
        }

        const colorPicker = document.getElementById('canvaTextColorPicker');
        if (colorPicker) {
            colorPicker.addEventListener('input', (e) => {
                this.setTextColor(e.target.value);
            });
        }

        const bgColorPicker = document.getElementById('canvaBgColorPicker');
        if (bgColorPicker) {
            bgColorPicker.addEventListener('input', (e) => {
                this.setBackgroundColor(e.target.value);
            });
        }

        const logoInput = document.getElementById('canvaLogoUploadInput');
        if (logoInput) {
            logoInput.addEventListener('change', (e) => {
                if (e.target.files && e.target.files.length) {
                    this.addUploadedImage(e.target.files[0]);
                }
            });
        }

        // Mobil Alt Sekmeler İçin Çekmece (Bottom Sheet) Açma/Kapama Dinleyicisi
        const tabLinks = document.querySelectorAll('#canvaSidebarTabs .nav-link');
        tabLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                if (window.innerWidth < 768) {
                    const target = link.getAttribute('data-bs-target') || link.getAttribute('href');
                    const tabContent = document.querySelector('.canva-sidebar .tab-content');
                    const isOpen = tabContent && tabContent.classList.contains('mobile-drawer-open');
                    const isAlreadyActive = link.classList.contains('active');

                    if (isOpen && isAlreadyActive) {
                        e.preventDefault();
                        e.stopPropagation();
                        this.closeMobileDrawer();
                    } else {
                        this.openMobileDrawer(target, link);
                    }
                }
            });
        });
    },

    bindKeyboardShortcuts: function() {
        document.removeEventListener('keydown', this.onKeyDownHandler);
        this.onKeyDownHandler = (e) => {
            const active = this.canvas ? this.canvas.getActiveObject() : null;
            if (!active || active.isEditing) return;

            if (e.key === 'Delete' || e.key === 'Backspace') {
                e.preventDefault();
                this.deleteSelected();
            } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'z') {
                e.preventDefault();
                if (e.shiftKey) this.redo();
                else this.undo();
            } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'y') {
                e.preventDefault();
                this.redo();
            } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'd') {
                e.preventDefault();
                this.duplicateSelected();
            }
        };
        document.addEventListener('keydown', this.onKeyDownHandler);
    },

    setBackgroundColor: function(color) {
        this.canvas.setBackgroundColor(color, this.canvas.renderAll.bind(this.canvas));
        this.saveState();
    },

    updateResponsiveScale: function() {
        const wrapper = document.getElementById('canvaCanvasWrapper');
        if (!wrapper || !this.canvas) return;

        const isMobile = window.innerWidth < 768;
        const paddingX = isMobile ? 16 : 48;
        const paddingY = isMobile ? 24 : 70;

        const wrapperWidth = (wrapper.clientWidth || window.innerWidth) - paddingX;
        const wrapperHeight = (wrapper.clientHeight || (window.innerHeight - 150)) - paddingY;
        
        const scaleX = wrapperWidth / this.width;
        const scaleY = (wrapperHeight > 0) ? (wrapperHeight / this.height) : scaleX;
        
        // Ekranın hem enine hem boyuna tam sığdır
        let scale = Math.min(scaleX, scaleY);
        if (scale > 1.0) scale = 1.0;
        if (scale < 0.25) scale = 0.25;

        const holder = document.getElementById('canvaCanvasHolder');
        if (holder) {
            holder.style.width = Math.round(this.width * scale) + 'px';
            holder.style.height = Math.round(this.height * scale) + 'px';
        }

        const container = wrapper.querySelector('.canvas-container');
        if (container) {
            container.style.transform = `scale(${scale})`;
            container.style.transformOrigin = 'top left';
        }
        
        const zoomRange = document.getElementById('canvaZoomRange');
        if (zoomRange) zoomRange.value = scale.toFixed(2);
    },

    closeMobileDrawer: function() {
        const tabContent = document.querySelector('.canva-sidebar .tab-content');
        if (tabContent) tabContent.classList.remove('mobile-drawer-open');

        const tabPanes = document.querySelectorAll('.canva-sidebar .tab-pane');
        const tabBtns = document.querySelectorAll('#canvaSidebarTabs .nav-link');
        tabPanes.forEach(p => p.classList.remove('show', 'active'));
        tabBtns.forEach(b => b.classList.remove('active'));
        this.updateResponsiveScale();
    },

    openMobileDrawer: function(targetPaneId, activeBtn) {
        const tabContent = document.querySelector('.canva-sidebar .tab-content');
        if (tabContent) tabContent.classList.add('mobile-drawer-open');

        const tabPanes = document.querySelectorAll('.canva-sidebar .tab-pane');
        const tabBtns = document.querySelectorAll('#canvaSidebarTabs .nav-link');
        tabPanes.forEach(p => p.classList.remove('show', 'active'));
        tabBtns.forEach(b => b.classList.remove('active'));

        if (targetPaneId) {
            const targetPane = document.querySelector(targetPaneId);
            if (targetPane) targetPane.classList.add('show', 'active');
        }
        if (activeBtn) activeBtn.classList.add('active');
        this.updateResponsiveScale();
    },

    filterIcons: function(keyword) {
        const query = (keyword || '').toLowerCase().trim();
        const iconBtns = document.querySelectorAll('.canva-icon-btn');
        const groups = document.querySelectorAll('.canva-icon-category-group');

        iconBtns.forEach(btn => {
            const tags = (btn.getAttribute('data-tags') || '').toLowerCase();
            const title = (btn.getAttribute('title') || '').toLowerCase();
            const text = btn.textContent.toLowerCase();
            const isMatch = !query || tags.includes(query) || title.includes(query) || text.includes(query);
            if (btn.parentElement) {
                btn.parentElement.style.display = isMatch ? 'block' : 'none';
            }
        });

        groups.forEach(group => {
            const visibleBtns = group.querySelectorAll('.canva-icon-btn');
            let hasVisible = false;
            visibleBtns.forEach(btn => {
                if (btn.parentElement && btn.parentElement.style.display !== 'none') hasVisible = true;
            });
            group.style.display = hasVisible ? 'block' : 'none';
        });
    },

    setZoom: function(scale) {
        const wrapper = document.getElementById('canvaCanvasWrapper');
        if (!wrapper) return;
        const holder = document.getElementById('canvaCanvasHolder');
        if (holder) {
            holder.style.width = Math.round(this.width * scale) + 'px';
            holder.style.height = Math.round(this.height * scale) + 'px';
        }
        const container = wrapper.querySelector('.canvas-container');
        if (container) {
            container.style.transform = `scale(${scale})`;
            container.style.transformOrigin = 'top left';
        }
    },

    trimPreviewMode: false,

    drawGuides: function() {
        const objects = this.canvas.getObjects();
        objects.forEach(obj => {
            if (obj.isGuide) this.canvas.remove(obj);
        });

        if (!this.showGuides) {
            this.canvas.requestRenderAll();
            return;
        }

        const safePx = 28; // ~3.5mm iç güvenli alan marjı

        // Şık, zarif ve tasarımda göz yormayan Güvenli Metin Kılavuzu
        const safeLine = new fabric.Rect({
            left: safePx,
            top: safePx,
            width: this.width - (safePx * 2),
            height: this.height - (safePx * 2),
            fill: 'transparent',
            stroke: 'rgba(2, 132, 199, 0.4)',
            strokeWidth: 1.2,
            strokeDashArray: [6, 4],
            selectable: false,
            evented: false,
            isGuide: true
        });

        this.canvas.add(safeLine);
        this.canvas.bringToFront(safeLine);
        this.canvas.requestRenderAll();
    },

    toggleGuides: function() {
        this.showGuides = !this.showGuides;
        this.drawGuides();
        const btn = document.getElementById('btnToggleCanvaGuides');
        if (btn) {
            btn.classList.toggle('active', this.showGuides);
        }
    },

    // ==========================================
    // 🔒 FİZİKSEL GÜVENLİ ALAN KİLİT MOTORU
    // (Kullanıcı istese de nesneler güvenli alan dışına çıkamaz)
    // ==========================================
    clampObjectInsideBounds: function(obj) {
        if (!obj || obj.isGuide || obj.isBackground) return;

        const margin = 20; // 2.5mm güvenli kenar payı sınırı
        const objWidth = obj.getScaledWidth ? obj.getScaledWidth() : (obj.width * (obj.scaleX || 1));
        const objHeight = obj.getScaledHeight ? obj.getScaledHeight() : (obj.height * (obj.scaleY || 1));

        let minLeft = margin;
        let maxLeft = this.width - objWidth - margin;
        let minTop = margin;
        let maxTop = this.height - objHeight - margin;

        if (obj.originX === 'center') {
            minLeft = margin + (objWidth / 2);
            maxLeft = this.width - margin - (objWidth / 2);
        }
        if (obj.originY === 'center') {
            minTop = margin + (objHeight / 2);
            maxTop = this.height - margin - (objHeight / 2);
        }

        if (maxLeft >= minLeft) {
            if (obj.left < minLeft) obj.left = minLeft;
            if (obj.left > maxLeft) obj.left = maxLeft;
        } else {
            obj.left = this.width / 2;
        }

        if (maxTop >= minTop) {
            if (obj.top < minTop) obj.top = minTop;
            if (obj.top > maxTop) obj.top = maxTop;
        } else {
            obj.top = this.height / 2;
        }

        obj.setCoords();
    },

    clampObjectScaling: function(obj) {
        if (!obj || obj.isGuide || obj.isBackground) return;
        const margin = 20;

        const maxAllowedWidth = this.width - (margin * 2);
        const maxAllowedHeight = this.height - (margin * 2);

        const currentW = obj.getScaledWidth();
        const currentH = obj.getScaledHeight();

        if (currentW > maxAllowedWidth) {
            const ratio = maxAllowedWidth / (obj.width || 1);
            obj.scaleX = ratio;
            obj.scaleY = ratio;
        }
        if (currentH > maxAllowedHeight) {
            const ratio = maxAllowedHeight / (obj.height || 1);
            obj.scaleX = ratio;
            obj.scaleY = ratio;
        }

        this.clampObjectInsideBounds(obj);
    },

    checkObjectBoundaries: function() {
        const active = this.canvas.getActiveObject();
        if (active) {
            this.clampObjectInsideBounds(active);
        }
        const alertBox = document.getElementById('canvaBleedAlert');
        if (alertBox) alertBox.style.display = 'none';
    },

    // ==========================================
    // 🌟 ULTRA ŞIK HAZIR ŞABLON KÜTÜPHANESİ
    // ==========================================
    builtinTemplates: {
        vip_onyx_gold: {
            title: 'Onyx Black & 24K Gold Foil',
            category: 'lüks',
            keywords: 'vip lüks altın gold yaldız siyah exclusive elit holding monogram',
            bg: '#0a0d14',
            previewSvg: `<svg viewBox="0 0 850 500" width="100%" height="100%"><rect width="850" height="500" fill="#0b0e17"/><rect x="30" y="30" width="790" height="440" fill="none" stroke="#d4af37" stroke-width="1.5"/><rect x="38" y="38" width="774" height="424" fill="none" stroke="#d4af37" stroke-width="0.75" stroke-dasharray="6 3"/><polygon points="425,75 450,110 425,145 400,110" fill="none" stroke="#d4af37" stroke-width="1.5"/><text x="425" y="118" text-anchor="middle" font-family="Playfair Display" font-size="20" font-weight="700" fill="#d4af37">A</text><text x="425" y="185" text-anchor="middle" font-family="Playfair Display" font-size="34" font-weight="700" fill="#fef08a" letter-spacing="4">ARİF UZ</text><text x="425" y="215" text-anchor="middle" font-family="Montserrat" font-size="13" font-weight="600" fill="#d4af37" letter-spacing="6">YÖNETİM KURULU BAŞKANI</text><line x1="280" y1="245" x2="570" y2="245" stroke="#d4af37" stroke-width="1"/><text x="425" y="340" text-anchor="middle" font-family="Inter" font-size="16" fill="#e2e8f0">+90 (212) 555 01 01   •   arif@holding.com</text><text x="425" y="375" text-anchor="middle" font-family="Inter" font-size="14" fill="#94a3b8">Büyükdere Cad. Maya Plaza Kat:28 Levent / İstanbul</text><text x="425" y="425" text-anchor="middle" font-family="Montserrat" font-size="12" font-weight="600" fill="#d4af37" letter-spacing="4">WWW.HOLDING.COM</text></svg>`,
            render: function(canvas) {
                canvas.setBackgroundColor('#0b0e17', canvas.renderAll.bind(canvas));
                const frame1 = new fabric.Rect({ left: 30, top: 30, width: 790, height: 440, fill: 'transparent', stroke: '#d4af37', strokeWidth: 1.5 });
                const frame2 = new fabric.Rect({ left: 38, top: 38, width: 774, height: 424, fill: 'transparent', stroke: '#d4af37', strokeWidth: 0.75, strokeDashArray: [6, 3] });
                
                const diamond = new fabric.Polygon([{x:425,y:65}, {x:450,y:100}, {x:425,y:135}, {x:400,y:100}], { fill: 'transparent', stroke: '#d4af37', strokeWidth: 1.5, originX:'center', left: 425 });
                const emblem = new fabric.IText('A', { left: 425, top: 85, originX: 'center', fontFamily: 'Playfair Display', fontSize: 24, fontWeight: '700', fill: '#d4af37' });
                
                const name = new fabric.IText('ARİF UZ', { left: 425, top: 160, originX: 'center', fontFamily: 'Playfair Display', fontSize: 36, fontWeight: '700', fill: '#fef08a', charSpacing: 80 });
                const title = new fabric.IText('YÖNETİM KURULU BAŞKANI', { left: 425, top: 208, originX: 'center', fontFamily: 'Montserrat', fontSize: 13, fontWeight: '600', fill: '#d4af37', charSpacing: 160 });
                const sep = new fabric.Line([280, 245, 570, 245], { stroke: '#d4af37', strokeWidth: 1 });
                
                const phoneMail = new fabric.IText('+90 (212) 555 01 01   •   arif@holding.com', { left: 425, top: 310, originX: 'center', fontFamily: 'Inter', fontSize: 15, fill: '#e2e8f0' });
                const addr = new fabric.IText('Büyükdere Cad. Maya Plaza Kat:28 Levent / İstanbul', { left: 425, top: 345, originX: 'center', fontFamily: 'Inter', fontSize: 13, fill: '#94a3b8' });
                const web = new fabric.IText('WWW.HOLDING.COM', { left: 425, top: 400, originX: 'center', fontFamily: 'Montserrat', fontSize: 12, fontWeight: '600', fill: '#d4af37', charSpacing: 140 });

                canvas.add(frame1, frame2, diamond, emblem, name, title, sep, phoneMail, addr, web);
            }
        },

        vip_emerald_creme: {
            title: 'Emerald Luxury & Cream',
            category: 'lüks',
            keywords: 'zümrüt krem yeşil altın lüks varak kuyumcu prestij',
            bg: '#042a22',
            previewSvg: `<svg viewBox="0 0 850 500" width="100%" height="100%"><rect width="850" height="500" fill="#052e25"/><polygon points="0,0 360,0 260,500 0,500" fill="#fdfbf7"/><circle cx="160" cy="160" r="50" fill="none" stroke="#d4af37" stroke-width="2"/><text x="160" y="172" text-anchor="middle" font-family="Playfair Display" font-size="34" font-weight="700" fill="#052e25">MZ</text><text x="160" y="250" text-anchor="middle" font-family="Playfair Display" font-size="24" font-weight="700" fill="#052e25">MAISON ZELDA</text><text x="160" y="275" text-anchor="middle" font-family="Montserrat" font-size="10" font-weight="600" fill="#d4af37" letter-spacing="4">HAUTE JOAILLERIE</text><text x="400" y="150" font-family="Playfair Display" font-size="36" font-weight="700" fill="#fdfbf7">Zeynep Karaca</text><text x="400" y="188" font-family="Montserrat" font-size="14" font-weight="600" fill="#d4af37" letter-spacing="4">MANAGING DIRECTOR</text><line x1="400" y1="215" x2="780" y2="215" stroke="#d4af37" stroke-width="1"/><text x="400" y="280" font-family="Inter" font-size="16" fill="#e2e8f0">📞 +90 (212) 288 99 00</text><text x="400" y="315" font-family="Inter" font-size="16" fill="#e2e8f0">✉️ zeynep@maisonzelda.com</text><text x="400" y="350" font-family="Inter" font-size="16" fill="#e2e8f0">🌐 www.maisonzelda.com</text><text x="400" y="420" font-family="Inter" font-size="14" fill="#a7f3d0">📍 Abdi İpekçi Cad. No:42 Nişantaşı / İst.</text></svg>`,
            render: function(canvas) {
                canvas.setBackgroundColor('#052e25', canvas.renderAll.bind(canvas));
                const poly = new fabric.Polygon([{x:0,y:0}, {x:340,y:0}, {x:240,y:500}, {x:0,y:500}], { fill: '#fdfbf7', selectable: true });
                
                const circle = new fabric.Circle({ left: 110, top: 110, radius: 45, fill: 'transparent', stroke: '#d4af37', strokeWidth: 2 });
                const crest = new fabric.IText('MZ', { left: 155, top: 135, originX: 'center', fontFamily: 'Playfair Display', fontSize: 32, fontWeight: '700', fill: '#052e25' });
                const brand = new fabric.IText('MAISON ZELDA', { left: 155, top: 220, originX: 'center', fontFamily: 'Playfair Display', fontSize: 20, fontWeight: '700', fill: '#052e25' });
                const tag = new fabric.IText('HAUTE JOAILLERIE', { left: 155, top: 250, originX: 'center', fontFamily: 'Montserrat', fontSize: 9, fontWeight: '600', fill: '#d4af37', charSpacing: 140 });

                const person = new fabric.IText('Zeynep Karaca', { left: 390, top: 120, fontFamily: 'Playfair Display', fontSize: 34, fontWeight: '700', fill: '#fdfbf7' });
                const title = new fabric.IText('MANAGING DIRECTOR', { left: 390, top: 165, fontFamily: 'Montserrat', fontSize: 13, fontWeight: '600', fill: '#d4af37', charSpacing: 140 });
                const line = new fabric.Line([390, 200, 780, 200], { stroke: '#d4af37', strokeWidth: 1 });

                const phone = new fabric.IText('📞  +90 (212) 288 99 00', { left: 390, top: 250, fontFamily: 'Inter', fontSize: 15, fill: '#e2e8f0' });
                const mail = new fabric.IText('✉️  zeynep@maisonzelda.com', { left: 390, top: 285, fontFamily: 'Inter', fontSize: 15, fill: '#e2e8f0' });
                const web = new fabric.IText('🌐  www.maisonzelda.com', { left: 390, top: 320, fontFamily: 'Inter', fontSize: 15, fill: '#e2e8f0' });
                const addr = new fabric.IText('📍  Abdi İpekçi Cad. No:42 Nişantaşı / İst.', { left: 390, top: 390, fontFamily: 'Inter', fontSize: 13, fill: '#a7f3d0' });

                canvas.add(poly, circle, crest, brand, tag, person, title, line, phone, mail, web, addr);
            }
        },

        corp_executive_navy: {
            title: 'Executive Midnight & Electric Blue',
            category: 'kurumsal',
            keywords: 'kurumsal yönetici ceo lacivert mavi holding executive startup',
            bg: '#081325',
            previewSvg: `<svg viewBox="0 0 850 500" width="100%" height="100%"><rect width="850" height="500" fill="#081325"/><rect x="0" y="0" width="16" height="500" fill="#3b82f6"/><text x="60" y="110" font-family="Montserrat" font-size="38" font-weight="900" fill="#ffffff">AVENUE <tspan fill="#3b82f6">CAPITAL</tspan></text><text x="60" y="145" font-family="Inter" font-size="14" font-weight="500" fill="#94a3b8" letter-spacing="3">GLOBAL INVESTMENT &amp; VENTURES</text><text x="60" y="270" font-family="Montserrat" font-size="30" font-weight="800" fill="#ffffff">Murat Sancak</text><text x="60" y="305" font-family="Inter" font-size="16" font-weight="600" fill="#60a5fa">Managing Partner &amp; CFO</text><text x="500" y="270" font-family="Inter" font-size="16" fill="#cbd5e1">📞 +90 (212) 380 40 50</text><text x="500" y="305" font-family="Inter" font-size="16" fill="#cbd5e1">✉️ murat@avenuecap.com</text><text x="500" y="340" font-family="Inter" font-size="16" fill="#cbd5e1">🌐 www.avenuecap.com</text><text x="60" y="420" font-family="Inter" font-size="14" fill="#64748b">📍 Maslak No:1 Plaza Kat:18 Sarıyer / İstanbul</text></svg>`,
            render: function(canvas) {
                canvas.setBackgroundColor('#081325', canvas.renderAll.bind(canvas));
                const bar = new fabric.Rect({ left: 0, top: 0, width: 14, height: 500, fill: '#3b82f6' });
                const comp = new fabric.IText('AVENUE CAPITAL', { left: 60, top: 80, fontFamily: 'Montserrat', fontSize: 32, fontWeight: '900', fill: '#ffffff' });
                const sub = new fabric.IText('GLOBAL INVESTMENT & VENTURES', { left: 60, top: 122, fontFamily: 'Inter', fontSize: 11, fill: '#94a3b8', charSpacing: 90 });
                
                const person = new fabric.IText('Murat Sancak', { left: 60, top: 230, fontFamily: 'Montserrat', fontSize: 26, fontWeight: '800', fill: '#ffffff' });
                const title = new fabric.IText('Managing Partner & CFO', { left: 60, top: 268, fontFamily: 'Inter', fontSize: 13, fontWeight: '600', fill: '#60a5fa' });

                const phone = new fabric.IText('📞  +90 (212) 380 40 50', { left: 520, top: 230, fontFamily: 'Inter', fontSize: 13, fill: '#cbd5e1' });
                const mail = new fabric.IText('✉️  murat@avenuecap.com', { left: 520, top: 265, fontFamily: 'Inter', fontSize: 13, fill: '#cbd5e1' });
                const web = new fabric.IText('🌐  www.avenuecap.com', { left: 520, top: 300, fontFamily: 'Inter', fontSize: 13, fill: '#cbd5e1' });
                const addr = new fabric.IText('📍  Maslak No:1 Plaza Kat:18 Sarıyer / İstanbul', { left: 60, top: 400, fontFamily: 'Inter', fontSize: 12, fill: '#64748b' });

                canvas.add(bar, comp, sub, person, title, phone, mail, web, addr);
            }
        },

        corp_nordic_minimal: {
            title: 'Nordic Pure Minimalist',
            category: 'kurumsal',
            keywords: 'nordic sade minimal isviçre beyaz modern kurumsal',
            bg: '#fcfcfd',
            previewSvg: `<svg viewBox="0 0 850 500" width="100%" height="100%"><rect width="850" height="500" fill="#fcfcfd"/><rect x="60" y="60" width="44" height="44" fill="#0f172a"/><circle cx="82" cy="82" r="10" fill="#fcfcfd"/><text x="125" y="90" font-family="Montserrat" font-size="28" font-weight="900" fill="#0f172a">KINETIC</text><text x="125" y="115" font-family="Inter" font-size="12" font-weight="600" fill="#64748b" letter-spacing="3">STRATEGY &amp; ADVISORY</text><line x1="60" y1="180" x2="790" y2="180" stroke="#e2e8f0" stroke-width="1.5"/><text x="60" y="270" font-family="Montserrat" font-size="28" font-weight="800" fill="#0f172a">Cemre Aydın</text><text x="60" y="305" font-family="Inter" font-size="15" fill="#64748b">Senior Strategic Consultant</text><text x="500" y="270" font-family="Inter" font-size="15" font-weight="500" fill="#334155">+90 532 700 80 90</text><text x="500" y="300" font-family="Inter" font-size="15" font-weight="500" fill="#334155">cemre@kinetic.ch</text><text x="500" y="330" font-family="Inter" font-size="15" font-weight="500" fill="#0f172a">www.kinetic.ch</text><text x="60" y="420" font-family="Inter" font-size="13" fill="#94a3b8">Kanyon Ofis Kuleleri Kat:12 Levent / İstanbul</text></svg>`,
            render: function(canvas) {
                canvas.setBackgroundColor('#fcfcfd', canvas.renderAll.bind(canvas));
                const iconBox = new fabric.Rect({ left: 60, top: 60, width: 40, height: 40, fill: '#0f172a' });
                const iconDot = new fabric.Circle({ left: 75, top: 75, radius: 5, fill: '#ffffff' });
                const comp = new fabric.IText('KINETIC', { left: 115, top: 65, fontFamily: 'Montserrat', fontSize: 24, fontWeight: '900', fill: '#0f172a' });
                const sub = new fabric.IText('STRATEGY & ADVISORY', { left: 115, top: 93, fontFamily: 'Inter', fontSize: 9, fontWeight: '600', fill: '#64748b', charSpacing: 90 });
                const line = new fabric.Line([60, 150, 790, 150], { stroke: '#e2e8f0', strokeWidth: 1.5 });

                const person = new fabric.IText('Cemre Aydın', { left: 60, top: 230, fontFamily: 'Montserrat', fontSize: 24, fontWeight: '800', fill: '#0f172a' });
                const title = new fabric.IText('Senior Strategic Consultant', { left: 60, top: 265, fontFamily: 'Inter', fontSize: 13, fill: '#64748b' });

                const phone = new fabric.IText('+90 532 700 80 90', { left: 520, top: 230, fontFamily: 'Inter', fontSize: 13, fill: '#334155' });
                const mail = new fabric.IText('cemre@kinetic.ch', { left: 520, top: 260, fontFamily: 'Inter', fontSize: 13, fill: '#334155' });
                const web = new fabric.IText('www.kinetic.ch', { left: 520, top: 290, fontFamily: 'Inter', fontSize: 13, fontWeight: '600', fill: '#0f172a' });
                const addr = new fabric.IText('Kanyon Ofis Kuleleri Kat:12 Levent / İstanbul', { left: 60, top: 400, fontFamily: 'Inter', fontSize: 12, fill: '#94a3b8' });

                canvas.add(iconBox, iconDot, comp, sub, line, person, title, phone, mail, web, addr);
            }
        },

        law_prestige_seal: {
            title: 'Hukuk & Arabuluculuk Klasik Mühür',
            category: 'hukuk',
            keywords: 'hukuk avukat arabuluculuk mühür adalet büro baro dava mahkeme',
            bg: '#ffffff',
            previewSvg: `<svg viewBox="0 0 850 500" width="100%" height="100%"><rect width="850" height="500" fill="#ffffff"/><rect width="850" height="12" fill="#7c2d12"/><circle cx="425" cy="110" r="35" fill="none" stroke="#7c2d12" stroke-width="1.5"/><text x="425" y="117" text-anchor="middle" font-family="Playfair Display" font-size="20" font-weight="700" fill="#7c2d12">⚖️</text><text x="425" y="180" text-anchor="middle" font-family="Playfair Display" font-size="34" font-weight="700" fill="#1c1917">AV. SELİM AKIN</text><text x="425" y="212" text-anchor="middle" font-family="Montserrat" font-size="12" font-weight="600" fill="#7c2d12" letter-spacing="4">UZMAN ARABULUCU &amp; DANIŞMAN</text><line x1="280" y1="240" x2="570" y2="240" stroke="#e7e5e4" stroke-width="1.5"/><text x="425" y="320" text-anchor="middle" font-family="Inter" font-size="16" fill="#44403c">İstanbul Barosu Sicil No: 45210   •   Arabuluculuk Sicil: 12890</text><text x="425" y="360" text-anchor="middle" font-family="Inter" font-size="15" fill="#1c1917">📞 0212 234 56 78   •   ✉️ selim@akinhukuk.av.tr</text><text x="425" y="415" text-anchor="middle" font-family="Inter" font-size="13" fill="#78716c">📍 İstanbul Adalet Sarayı Yanı Hukukçular Plaza No:8 Çağlayan</text></svg>`,
            render: function(canvas) {
                canvas.setBackgroundColor('#ffffff', canvas.renderAll.bind(canvas));
                const topBar = new fabric.Rect({ left: 0, top: 0, width: 850, height: 10, fill: '#7c2d12' });
                const sealCircle = new fabric.Circle({ left: 395, top: 60, radius: 30, fill: 'transparent', stroke: '#7c2d12', strokeWidth: 1.5 });
                const sealIcon = new fabric.IText('⚖️', { left: 425, top: 68, originX: 'center', fontSize: 24 });
                
                const person = new fabric.IText('AV. SELİM AKIN', { left: 425, top: 155, originX: 'center', fontFamily: 'Playfair Display', fontSize: 32, fontWeight: '700', fill: '#1c1917' });
                const title = new fabric.IText('UZMAN ARABULUCU & DANIŞMAN', { left: 425, top: 198, originX: 'center', fontFamily: 'Montserrat', fontSize: 12, fontWeight: '600', fill: '#7c2d12', charSpacing: 120 });
                const line = new fabric.Line([280, 235, 570, 235], { stroke: '#e7e5e4', strokeWidth: 1.5 });

                const baro = new fabric.IText('İstanbul Barosu Sicil No: 45210   •   Arabuluculuk Sicil: 12890', { left: 425, top: 290, originX: 'center', fontFamily: 'Inter', fontSize: 13, fill: '#44403c' });
                const contact = new fabric.IText('📞 0212 234 56 78   •   ✉️ selim@akinhukuk.av.tr', { left: 425, top: 330, originX: 'center', fontFamily: 'Inter', fontSize: 14, fill: '#1c1917' });
                const addr = new fabric.IText('📍 İstanbul Adalet Sarayı Yanı Hukukçular Plaza No:8 Çağlayan', { left: 425, top: 390, originX: 'center', fontFamily: 'Inter', fontSize: 12, fill: '#78716c' });

                canvas.add(topBar, sealCircle, sealIcon, person, title, line, baro, contact, addr);
            }
        },

        med_aesthetic_teal: {
            title: 'Klinik & Medikal Estetik',
            category: 'sağlık',
            keywords: 'doktor medikal estetik klinik sağlık tıp uzman cerrah dermatoloji',
            bg: '#ffffff',
            previewSvg: `<svg viewBox="0 0 850 500" width="100%" height="100%"><rect width="850" height="500" fill="#ffffff"/><rect width="850" height="14" fill="#0d9488"/><text x="60" y="110" font-family="Playfair Display" font-size="36" font-weight="700" fill="#134e4a">Uzm. Dr. Aylin Kaya</text><text x="60" y="145" font-family="Montserrat" font-size="14" font-weight="600" fill="#0d9488" letter-spacing="3">DERMATOLOJİ &amp; MEDİKAL ESTETİK UZMANI</text><line x1="60" y1="180" x2="790" y2="180" stroke="#ccfbf1" stroke-width="1.5"/><text x="60" y="270" font-family="Montserrat" font-size="20" font-weight="700" fill="#1e293b">AYLİN KAYA CLINIC</text><text x="60" y="305" font-family="Inter" font-size="16" fill="#334155">📞 Randevu &amp; Danışma: 0216 456 78 90</text><text x="60" y="340" font-family="Inter" font-size="16" fill="#334155">✉️ info@draylinkaya.com   •   🌐 www.draylinkaya.com</text><text x="60" y="420" font-family="Inter" font-size="14" fill="#64748b">📍 Bağdat Cad. No:180/4 Kadıköy / İstanbul</text></svg>`,
            render: function(canvas) {
                canvas.setBackgroundColor('#ffffff', canvas.renderAll.bind(canvas));
                const topBar = new fabric.Rect({ left: 0, top: 0, width: 850, height: 12, fill: '#0d9488' });
                const person = new fabric.IText('Uzm. Dr. Aylin Kaya', { left: 60, top: 80, fontFamily: 'Playfair Display', fontSize: 32, fontWeight: '700', fill: '#134e4a' });
                const title = new fabric.IText('DERMATOLOJİ & MEDİKAL ESTETİK UZMANI', { left: 60, top: 125, fontFamily: 'Montserrat', fontSize: 12, fontWeight: '600', fill: '#0d9488', charSpacing: 90 });
                const line = new fabric.Line([60, 160, 790, 160], { stroke: '#ccfbf1', strokeWidth: 1.5 });

                const clinic = new fabric.IText('AYLİN KAYA CLINIC', { left: 60, top: 230, fontFamily: 'Montserrat', fontSize: 18, fontWeight: '700', fill: '#1e293b' });
                const phone = new fabric.IText('📞 Randevu & Danışma: 0216 456 78 90', { left: 60, top: 268, fontFamily: 'Inter', fontSize: 14, fill: '#334155' });
                const mail = new fabric.IText('✉️ info@draylinkaya.com   •   🌐 www.draylinkaya.com', { left: 60, top: 300, fontFamily: 'Inter', fontSize: 14, fill: '#334155' });
                const addr = new fabric.IText('📍 Bağdat Cad. No:180/4 Kadıköy / İstanbul', { left: 60, top: 390, fontFamily: 'Inter', fontSize: 12, fill: '#64748b' });

                canvas.add(topBar, person, title, line, clinic, phone, mail, addr);
            }
        },

        arch_studio_terracotta: {
            title: 'Terracotta & Sand Architecture',
            category: 'mimarlık',
            keywords: 'mimarlık iç mimar dekorasyon terracotta tasarım stüdyo villa',
            bg: '#fbf9f6',
            previewSvg: `<svg viewBox="0 0 850 500" width="100%" height="100%"><rect width="850" height="500" fill="#fbf9f6"/><rect x="50" y="50" width="14" height="400" fill="#c2410c"/><text x="85" y="110" font-family="Playfair Display" font-size="34" font-weight="700" fill="#1c1917">TERRA STUDIO</text><text x="85" y="140" font-family="Montserrat" font-size="12" font-weight="600" fill="#c2410c" letter-spacing="4">ARCHITECTURE &amp; INTERIORS</text><text x="85" y="270" font-family="Playfair Display" font-size="28" font-weight="700" fill="#1c1917">Merve Demir</text><text x="85" y="305" font-family="Inter" font-size="15" fill="#78716c">Y. İç Mimar &amp; Kurucu</text><text x="500" y="270" font-family="Inter" font-size="15" fill="#44403c">📞 +90 544 123 45 67</text><text x="500" y="305" font-family="Inter" font-size="15" fill="#44403c">✉️ merve@terrastudio.com</text><text x="500" y="340" font-family="Inter" font-size="15" fill="#44403c">🌐 www.terrastudio.com</text><text x="85" y="420" font-family="Inter" font-size="13" fill="#a8a29e">📍 Galata Kulesi Sok. No:18 Beyoğlu / İstanbul</text></svg>`,
            render: function(canvas) {
                canvas.setBackgroundColor('#fbf9f6', canvas.renderAll.bind(canvas));
                const stripe = new fabric.Rect({ left: 50, top: 50, width: 12, height: 400, fill: '#c2410c' });
                const comp = new fabric.IText('TERRA STUDIO', { left: 80, top: 80, fontFamily: 'Playfair Display', fontSize: 30, fontWeight: '700', fill: '#1c1917' });
                const sub = new fabric.IText('ARCHITECTURE & INTERIORS', { left: 80, top: 118, fontFamily: 'Montserrat', fontSize: 11, fontWeight: '600', fill: '#c2410c', charSpacing: 120 });

                const person = new fabric.IText('Merve Demir', { left: 80, top: 230, fontFamily: 'Playfair Display', fontSize: 26, fontWeight: '700', fill: '#1c1917' });
                const title = new fabric.IText('Y. İç Mimar & Kurucu', { left: 80, top: 268, fontFamily: 'Inter', fontSize: 13, fill: '#78716c' });

                const phone = new fabric.IText('📞  +90 544 123 45 67', { left: 520, top: 230, fontFamily: 'Inter', fontSize: 13, fill: '#44403c' });
                const mail = new fabric.IText('✉️  merve@terrastudio.com', { left: 520, top: 265, fontFamily: 'Inter', fontSize: 13, fill: '#44403c' });
                const web = new fabric.IText('🌐  www.terrastudio.com', { left: 520, top: 300, fontFamily: 'Inter', fontSize: 13, fill: '#44403c' });
                const addr = new fabric.IText('📍  Galata Kulesi Sok. No:18 Beyoğlu / İstanbul', { left: 80, top: 400, fontFamily: 'Inter', fontSize: 12, fill: '#a8a29e' });

                canvas.add(stripe, comp, sub, person, title, phone, mail, web, addr);
            }
        },

        tech_ai_startup: {
            title: 'Cyber Dark & Neon Gradient',
            category: 'teknoloji',
            keywords: 'teknoloji yazılım siber ai yapay zeka startup mobil web kod',
            bg: '#050711',
            previewSvg: `<svg viewBox="0 0 850 500" width="100%" height="100%"><rect width="850" height="500" fill="#050711"/><circle cx="750" cy="80" r="140" fill="#6366f1" opacity="0.35"/><text x="60" y="110" font-family="Montserrat" font-size="38" font-weight="900" fill="#38bdf8">NEXUS<tspan fill="#6366f1">.AI</tspan></text><text x="60" y="145" font-family="Inter" font-size="14" fill="#94a3b8" letter-spacing="3">ENTERPRISE ARTIFICIAL INTELLIGENCE</text><text x="60" y="270" font-family="Montserrat" font-size="30" font-weight="800" fill="#ffffff">Arda Berke Soylu</text><text x="60" y="305" font-family="Inter" font-size="16" fill="#38bdf8">Co-Founder &amp; Chief AI Architect</text><text x="500" y="270" font-family="Inter" font-size="16" fill="#cbd5e1">📞 +90 850 888 90 00</text><text x="500" y="305" font-family="Inter" font-size="16" fill="#cbd5e1">✉️ arda@nexusai.dev</text><text x="500" y="340" font-family="Inter" font-size="16" fill="#cbd5e1">🌐 www.nexusai.dev</text><text x="60" y="420" font-family="Inter" font-size="14" fill="#64748b">📍 ITU ARI Teknokent 3 Maslak / İstanbul</text></svg>`,
            render: function(canvas) {
                canvas.setBackgroundColor('#050711', canvas.renderAll.bind(canvas));
                const glow = new fabric.Circle({ left: 630, top: -40, radius: 120, fill: 'rgba(99, 102, 241, 0.35)', selectable: true });
                const comp = new fabric.IText('NEXUS.AI', { left: 60, top: 80, fontFamily: 'Montserrat', fontSize: 32, fontWeight: '900', fill: '#38bdf8' });
                const sub = new fabric.IText('ENTERPRISE ARTIFICIAL INTELLIGENCE', { left: 60, top: 122, fontFamily: 'Inter', fontSize: 11, fill: '#94a3b8', charSpacing: 90 });

                const person = new fabric.IText('Arda Berke Soylu', { left: 60, top: 230, fontFamily: 'Montserrat', fontSize: 26, fontWeight: '800', fill: '#ffffff' });
                const title = new fabric.IText('Co-Founder & Chief AI Architect', { left: 60, top: 268, fontFamily: 'Inter', fontSize: 13, fill: '#38bdf8' });

                const phone = new fabric.IText('📞  +90 850 888 90 00', { left: 520, top: 230, fontFamily: 'Inter', fontSize: 13, fill: '#cbd5e1' });
                const mail = new fabric.IText('✉️  arda@nexusai.dev', { left: 520, top: 265, fontFamily: 'Inter', fontSize: 13, fill: '#cbd5e1' });
                const web = new fabric.IText('🌐  www.nexusai.dev', { left: 520, top: 300, fontFamily: 'Inter', fontSize: 13, fill: '#cbd5e1' });
                const addr = new fabric.IText('📍  ITU ARI Teknokent 3 Maslak / İstanbul', { left: 60, top: 400, fontFamily: 'Inter', fontSize: 12, fill: '#64748b' });

                canvas.add(glow, comp, sub, person, title, phone, mail, web, addr);
            }
        },

        resto_artisan_kraft: {
            title: 'Artisan Cafe & Gourmet Roastery',
            category: 'gıda',
            keywords: 'cafe kahve fırın restoran lezzet şef pasta tatlı gastronomi',
            bg: '#1c1917',
            previewSvg: `<svg viewBox="0 0 850 500" width="100%" height="100%"><rect width="850" height="500" fill="#1c1917"/><circle cx="425" cy="110" r="45" fill="none" stroke="#d97706" stroke-width="1.5" stroke-dasharray="6 3"/><text x="425" y="117" text-anchor="middle" font-family="Playfair Display" font-size="24" font-weight="700" fill="#f59e0b">☕</text><text x="425" y="195" text-anchor="middle" font-family="Playfair Display" font-size="34" font-weight="700" fill="#fef3c7">ARTISAN ROASTERY</text><text x="425" y="225" text-anchor="middle" font-family="Montserrat" font-size="12" font-weight="600" fill="#d97706" letter-spacing="4">SPECIALTY COFFEE &amp; BAKERY</text><line x1="280" y1="255" x2="570" y2="255" stroke="#78350f" stroke-width="1"/><text x="425" y="320" text-anchor="middle" font-family="Montserrat" font-size="24" font-weight="700" fill="#ffffff">Şef Emre Demir</text><text x="425" y="350" text-anchor="middle" font-family="Inter" font-size="14" fill="#d97706">Head Barista &amp; Founder</text><text x="425" y="415" text-anchor="middle" font-family="Inter" font-size="14" fill="#fde68a">📞 0212 999 88 77   •   📍 Moda Cad. No:24 Moda / Kadıköy</text></svg>`,
            render: function(canvas) {
                canvas.setBackgroundColor('#1c1917', canvas.renderAll.bind(canvas));
                const circle = new fabric.Circle({ left: 385, top: 60, radius: 40, fill: 'transparent', stroke: '#d97706', strokeWidth: 1.5, strokeDashArray: [6, 3] });
                const icon = new fabric.IText('☕', { left: 425, top: 72, originX: 'center', fontSize: 28 });

                const comp = new fabric.IText('ARTISAN ROASTERY', { left: 425, top: 165, originX: 'center', fontFamily: 'Playfair Display', fontSize: 32, fontWeight: '700', fill: '#fef3c7' });
                const sub = new fabric.IText('SPECIALTY COFFEE & BAKERY', { left: 425, top: 205, originX: 'center', fontFamily: 'Montserrat', fontSize: 11, fontWeight: '600', fill: '#d97706', charSpacing: 120 });
                const line = new fabric.Line([280, 245, 570, 245], { stroke: '#78350f', strokeWidth: 1 });

                const person = new fabric.IText('Şef Emre Demir', { left: 425, top: 295, originX: 'center', fontFamily: 'Montserrat', fontSize: 22, fontWeight: '700', fill: '#ffffff' });
                const title = new fabric.IText('Head Barista & Founder', { left: 425, top: 328, originX: 'center', fontFamily: 'Inter', fontSize: 13, fill: '#d97706' });
                const contact = new fabric.IText('📞 0212 999 88 77   •   📍 Moda Cad. No:24 Moda / Kadıköy', { left: 425, top: 400, originX: 'center', fontFamily: 'Inter', fontSize: 13, fill: '#fde68a' });

                canvas.add(circle, icon, comp, sub, line, person, title, contact);
            }
        },

        fashion_haute_couture: {
            title: 'Maison Couture & High Fashion',
            category: 'moda',
            keywords: 'moda butik giyim couture terzi stilist moda evi takı',
            bg: '#fcfbf9',
            previewSvg: `<svg viewBox="0 0 850 500" width="100%" height="100%"><rect width="850" height="500" fill="#fcfbf9"/><rect x="40" y="40" width="770" height="420" fill="none" stroke="#292524" stroke-width="1"/><text x="425" y="140" text-anchor="middle" font-family="Playfair Display" font-size="44" font-weight="700" fill="#1c1917" letter-spacing="6">M A I S O N</text><text x="425" y="175" text-anchor="middle" font-family="Montserrat" font-size="12" font-weight="600" fill="#78716c" letter-spacing="6">HAUTE COUTURE PARIS - ISTANBUL</text><line x1="320" y1="210" x2="530" y2="210" stroke="#d6d3d1" stroke-width="1"/><text x="425" y="290" text-anchor="middle" font-family="Playfair Display" font-size="30" font-weight="700" fill="#1c1917">Cansu Aslan</text><text x="425" y="325" text-anchor="middle" font-family="Montserrat" font-size="13" font-weight="600" fill="#a8a29e" letter-spacing="4">CREATIVE DIRECTOR</text><text x="425" y="415" text-anchor="middle" font-family="Inter" font-size="14" fill="#44403c">📞 +90 212 296 00 11   •   Teşvikiye Cad. No:44 Nişantaşı</text></svg>`,
            render: function(canvas) {
                canvas.setBackgroundColor('#fcfbf9', canvas.renderAll.bind(canvas));
                const frame = new fabric.Rect({ left: 40, top: 40, width: 770, height: 420, fill: 'transparent', stroke: '#292524', strokeWidth: 1 });
                const comp = new fabric.IText('M A I S O N', { left: 425, top: 100, originX: 'center', fontFamily: 'Playfair Display', fontSize: 38, fontWeight: '700', fill: '#1c1917', charSpacing: 180 });
                const sub = new fabric.IText('HAUTE COUTURE PARIS - ISTANBUL', { left: 425, top: 150, originX: 'center', fontFamily: 'Montserrat', fontSize: 11, fontWeight: '600', fill: '#78716c', charSpacing: 160 });
                const line = new fabric.Line([320, 190, 530, 190], { stroke: '#d6d3d1', strokeWidth: 1 });

                const person = new fabric.IText('Cansu Aslan', { left: 425, top: 255, originX: 'center', fontFamily: 'Playfair Display', fontSize: 26, fontWeight: '700', fill: '#1c1917' });
                const title = new fabric.IText('CREATIVE DIRECTOR', { left: 425, top: 295, originX: 'center', fontFamily: 'Montserrat', fontSize: 12, fontWeight: '600', fill: '#a8a29e', charSpacing: 140 });
                const contact = new fabric.IText('📞 +90 212 296 00 11   •   📍 Teşvikiye Cad. No:44 Nişantaşı', { left: 425, top: 390, originX: 'center', fontFamily: 'Inter', fontSize: 13, fill: '#44403c' });

                canvas.add(frame, comp, sub, line, person, title, contact);
            }
        }
    },

    loadBuiltinTemplate: function(key) {
        if (window.innerWidth < 768) {
            this.closeMobileDrawer();
        }

        if (window.CanvaTemplatesEngine) {
            this.canvas.clear();
            window.CanvaTemplatesEngine.loadTemplateToCanvas(this.canvas, key);
            this.drawGuides();
            this.checkObjectBoundaries();
            this.saveState();
            return;
        }

        const tpl = this.builtinTemplates[key];
        if (!tpl) return;

        this.canvas.clear();
        tpl.render(this.canvas);
        this.drawGuides();
        this.checkObjectBoundaries();
        this.saveState();

        const titleEl = document.getElementById('canvaActiveTemplateTitle');
        if (titleEl) titleEl.textContent = tpl.title;
    },

    loadSvgString: function(svgString) {
        if (!svgString) return;
        this.canvas.clear();

        fabric.loadSVGFromString(svgString, (objects, options) => {
            if (!objects || !objects.length) return;

            if (options && options.background) {
                this.canvas.setBackgroundColor(options.background, () => {});
            } else {
                this.canvas.setBackgroundColor('#ffffff', () => {});
            }

            let scaleX = 1;
            let scaleY = 1;
            if (options && options.width && options.height && (options.width !== this.width || options.height !== this.height)) {
                scaleX = this.width / options.width;
                scaleY = this.height / options.height;
            }

            objects.forEach((obj) => {
                if (scaleX !== 1 || scaleY !== 1) {
                    obj.left = (obj.left || 0) * scaleX;
                    obj.top = (obj.top || 0) * scaleY;
                    obj.scaleX = (obj.scaleX || 1) * scaleX;
                    obj.scaleY = (obj.scaleY || 1) * scaleY;
                }

                if (obj.type === 'text' || obj.type === 'i-text' || obj.text) {
                    const itext = new fabric.IText(obj.text || '', {
                        left: obj.left,
                        top: obj.top,
                        fontFamily: obj.fontFamily || 'Inter',
                        fontSize: (obj.fontSize || 16) * (scaleY || 1),
                        fill: obj.fill || '#000000',
                        fontWeight: obj.fontWeight || 'normal',
                        fontStyle: obj.fontStyle || 'normal',
                        underline: !!obj.underline,
                        textAlign: obj.textAlign || 'left',
                        originX: obj.originX || 'left',
                        originY: obj.originY || 'top',
                        charSpacing: obj.charSpacing || 0,
                        lineHeight: obj.lineHeight || 1.15,
                        editable: true,
                        selectable: true,
                        hasControls: true
                    });
                    this.canvas.add(itext);
                } else {
                    obj.set({
                        selectable: true,
                        hasControls: true,
                        evented: true
                    });
                    this.canvas.add(obj);
                }
            });

            this.drawGuides();
            this.canvas.requestRenderAll();
            this.checkObjectBoundaries();
            this.saveState();
        });
    },

    currentTemplatePage: 1,
    templatesPerPage: 24,

    renderTemplatesSidebar: function(append = false) {
        const listContainer = document.getElementById('canvaTemplatesList');
        if (!listContainer) return;

        if (!append) {
            listContainer.innerHTML = '';
            this.currentTemplatePage = 1;
        }

        const cat = this.activeCategory || 'all';
        const kw = this.searchKeyword || '';

        // Remove old 'Load More' button if present
        const oldLoadMore = document.getElementById('canvaLoadMoreTemplatesBtn');
        if (oldLoadMore) oldLoadMore.remove();

        let result = { items: [], total: 0, hasMore: false };

        if (window.CanvaTemplatesEngine) {
            result = window.CanvaTemplatesEngine.getTemplates(cat, kw, this.currentTemplatePage, this.templatesPerPage);
        }

        // Update badge count in header
        const totalBadge = document.getElementById('canvaTotalTemplatesBadge');
        if (totalBadge) {
            totalBadge.textContent = `${result.total} Şablon`;
        }

        // Handle No Results
        const noResult = document.getElementById('canvaNoTemplatesAlert');
        if (noResult) {
            noResult.style.display = (result.total === 0) ? 'block' : 'none';
        }

        // Render each template card
        result.items.forEach(tpl => {
            const item = document.createElement('div');
            item.className = 'col-6 mb-2 template-sidebar-item';
            item.innerHTML = `
                <div class="p-2 border rounded-3 text-center bg-white shadow-2xs h-100 d-flex flex-column cursor-pointer hover-card" onclick="CanvaStudio.loadBuiltinTemplate('${tpl.key}')" style="transition: all 0.2s ease;">
                    <div class="rounded-2 mb-2 border overflow-hidden d-flex align-items-center justify-content-center shadow-xs" style="height: 76px; background: #0f172a;">
                        ${tpl.previewSvg}
                    </div>
                    <div class="fw-bold text-dark text-truncate small" style="font-size: 11px;" title="${tpl.title}">${tpl.title}</div>
                    <span class="badge bg-light text-muted border mt-auto text-capitalize" style="font-size: 9.5px;">${tpl.sectorName || tpl.sector}</span>
                </div>
            `;
            listContainer.appendChild(item);
        });

        // Add 'Load More' button if there are more templates
        if (result.hasMore) {
            const loadMoreWrapper = document.createElement('div');
            loadMoreWrapper.className = 'col-12 text-center my-3';
            loadMoreWrapper.id = 'canvaLoadMoreTemplatesBtn';
            loadMoreWrapper.innerHTML = `
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold shadow-xs" onclick="CanvaStudio.loadMoreTemplates()">
                    <i class="bi bi-arrow-down-circle me-1"></i> Daha Fazla Şablon Yükle (+${this.templatesPerPage})
                </button>
            `;
            listContainer.appendChild(loadMoreWrapper);
        }
    },

    loadMoreTemplates: function() {
        this.currentTemplatePage++;
        this.renderTemplatesSidebar(true);
    },

    filterTemplates: function(category) {
        this.activeCategory = category || 'all';
        this.currentTemplatePage = 1;
        this.renderTemplatesSidebar(false);
    },

    onSearchTemplates: function(keyword) {
        this.searchKeyword = (keyword || '').toLowerCase().trim();
        this.currentTemplatePage = 1;
        this.renderTemplatesSidebar(false);
    },

    addHeading: function() {
        if (window.innerWidth < 768) this.closeMobileDrawer();
        const text = new fabric.IText('BÜYÜK BAŞLIK', {
            left: 100,
            top: 100,
            fontFamily: 'Montserrat',
            fontSize: 32,
            fontWeight: '800',
            fill: '#0f172a'
        });
        this.canvas.add(text);
        this.canvas.setActiveObject(text);
        this.canvas.requestRenderAll();
        this.checkObjectBoundaries();
        this.saveState();
    },

    addSubheading: function() {
        if (window.innerWidth < 768) this.closeMobileDrawer();
        const text = new fabric.IText('Alt Başlık / Unvan', {
            left: 100,
            top: 150,
            fontFamily: 'Inter',
            fontSize: 18,
            fontWeight: '600',
            fill: '#475569'
        });
        this.canvas.add(text);
        this.canvas.setActiveObject(text);
        this.canvas.requestRenderAll();
        this.checkObjectBoundaries();
        this.saveState();
    },

    addBodyText: function() {
        if (window.innerWidth < 768) this.closeMobileDrawer();
        const text = new fabric.IText('📞 0532 123 45 67 • info@sirket.com', {
            left: 100,
            top: 200,
            fontFamily: 'Inter',
            fontSize: 14,
            fill: '#64748b'
        });
        this.canvas.add(text);
        this.canvas.setActiveObject(text);
        this.canvas.requestRenderAll();
        this.checkObjectBoundaries();
        this.saveState();
    },

    addUploadedImage: function(file) {
        if (window.innerWidth < 768) this.closeMobileDrawer();
        const reader = new FileReader();
        reader.onload = (e) => {
            fabric.Image.fromURL(e.target.result, (img) => {
                const maxDim = 200;
                if (img.width > maxDim || img.height > maxDim) {
                    const scale = maxDim / Math.max(img.width, img.height);
                    img.scale(scale);
                }
                img.set({
                    left: 100,
                    top: 100
                });
                this.canvas.add(img);
                this.canvas.setActiveObject(img);
                this.canvas.requestRenderAll();
                this.checkObjectBoundaries();
                this.saveState();
            });
        };
        reader.readAsDataURL(file);
    },

    addShape: function(type) {
        if (window.innerWidth < 768) this.closeMobileDrawer();
        let shape;
        const color = '#0071e3';

        if (type === 'rect') {
            shape = new fabric.Rect({
                left: 150,
                top: 150,
                width: 140,
                height: 90,
                fill: color,
                rx: 4,
                ry: 4
            });
        } else if (type === 'circle') {
            shape = new fabric.Circle({
                left: 150,
                top: 150,
                radius: 45,
                fill: color
            });
        } else if (type === 'line') {
            shape = new fabric.Line([50, 50, 250, 50], {
                left: 100,
                top: 200,
                stroke: color,
                strokeWidth: 2.5
            });
        }

        if (shape) {
            this.canvas.add(shape);
            this.canvas.setActiveObject(shape);
            this.canvas.requestRenderAll();
            this.checkObjectBoundaries();
            this.saveState();
        }
    },

    addIcon: function(iconKey) {
        if (window.innerWidth < 768) this.closeMobileDrawer();
        const svgString = this.icons[iconKey];
        if (!svgString) return;

        fabric.loadSVGFromString(svgString, (objects, options) => {
            const icon = fabric.util.groupSVGElements(objects, options);
            icon.set({
                left: 150,
                top: 150,
                scaleX: 1.4,
                scaleY: 1.4,
                fill: '#0071e3'
            });
            this.canvas.add(icon);
            this.canvas.setActiveObject(icon);
            this.canvas.requestRenderAll();
            this.checkObjectBoundaries();
            this.saveState();
        });
    },

    addQrCode: function(urlOrText) {
        if (window.innerWidth < 768) this.closeMobileDrawer();
        if (!urlOrText) urlOrText = 'https://baski.arifuz.com.tr';
        const qrImgUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(urlOrText)}`;
        
        fabric.Image.fromURL(qrImgUrl, (img) => {
            img.set({
                left: this.width - 160,
                top: this.height - 160,
                scaleX: 0.45,
                scaleY: 0.45
            });
            this.canvas.add(img);
            this.canvas.setActiveObject(img);
            this.canvas.requestRenderAll();
            this.checkObjectBoundaries();
            this.saveState();
        }, { crossOrigin: 'anonymous' });
    },

    setFontFamily: function(font) {
        const active = this.canvas.getActiveObject();
        if (active && (active.type === 'i-text' || active.type === 'text')) {
            active.set('fontFamily', font);
            this.canvas.requestRenderAll();
            this.saveState();
        }
    },

    setFontSize: function(size) {
        const active = this.canvas.getActiveObject();
        if (active && (active.type === 'i-text' || active.type === 'text')) {
            active.set('fontSize', parseInt(size) || 16);
            const input = document.getElementById('canvaFontSize');
            if (input) input.value = Math.round(active.fontSize);
            this.canvas.requestRenderAll();
            this.checkObjectBoundaries();
            this.saveState();
        }
    },

    changeFontSize: function(delta) {
        const active = this.canvas.getActiveObject();
        if (active && (active.type === 'i-text' || active.type === 'text')) {
            const newSize = Math.max(6, Math.min(150, (active.fontSize || 16) + delta));
            this.setFontSize(newSize);
        }
    },

    setTextColor: function(color) {
        const active = this.canvas.getActiveObject();
        if (!active) return;

        if (active.type === 'i-text' || active.type === 'text') {
            active.set('fill', color);
        } else if (active.type === 'path' || active.type === 'group') {
            this.setGroupFill(active, color);
        } else {
            active.set('fill', color);
        }

        const colorInput = document.getElementById('canvaTextColorPicker');
        if (colorInput) colorInput.value = color;

        this.canvas.requestRenderAll();
        this.saveState();
    },

    toggleBold: function() {
        const active = this.canvas.getActiveObject();
        if (active && (active.type === 'i-text' || active.type === 'text')) {
            const isBold = active.fontWeight === 'bold' || active.fontWeight === '700' || active.fontWeight === '800';
            active.set('fontWeight', isBold ? 'normal' : 'bold');
            this.updateFormatButtons();
            this.canvas.requestRenderAll();
            this.saveState();
        }
    },

    toggleItalic: function() {
        const active = this.canvas.getActiveObject();
        if (active && (active.type === 'i-text' || active.type === 'text')) {
            const isItalic = active.fontStyle === 'italic';
            active.set('fontStyle', isItalic ? 'normal' : 'italic');
            this.updateFormatButtons();
            this.canvas.requestRenderAll();
            this.saveState();
        }
    },

    toggleUnderline: function() {
        const active = this.canvas.getActiveObject();
        if (active && (active.type === 'i-text' || active.type === 'text')) {
            active.set('underline', !active.underline);
            this.updateFormatButtons();
            this.canvas.requestRenderAll();
            this.saveState();
        }
    },

    setTextAlign: function(alignment) {
        const active = this.canvas.getActiveObject();
        if (active && (active.type === 'i-text' || active.type === 'text')) {
            active.set('textAlign', alignment);
            this.updateFormatButtons();
            this.canvas.requestRenderAll();
            this.saveState();
        }
    },

    updateFormatButtons: function() {
        const active = this.canvas.getActiveObject();
        if (!active || (active.type !== 'i-text' && active.type !== 'text')) return;

        const btnBold = document.getElementById('canvaBtnBold');
        const btnItalic = document.getElementById('canvaBtnItalic');
        const btnUnderline = document.getElementById('canvaBtnUnderline');
        const btnAlignLeft = document.getElementById('canvaBtnAlignLeft');
        const btnAlignCenter = document.getElementById('canvaBtnAlignCenter');
        const btnAlignRight = document.getElementById('canvaBtnAlignRight');

        const isBold = active.fontWeight === 'bold' || active.fontWeight === '700' || active.fontWeight === '800';
        if (btnBold) btnBold.classList.toggle('active', isBold);

        const isItalic = active.fontStyle === 'italic';
        if (btnItalic) btnItalic.classList.toggle('active', isItalic);

        if (btnUnderline) btnUnderline.classList.toggle('active', !!active.underline);

        if (btnAlignLeft) btnAlignLeft.classList.toggle('active', active.textAlign === 'left');
        if (btnAlignCenter) btnAlignCenter.classList.toggle('active', active.textAlign === 'center');
        if (btnAlignRight) btnAlignRight.classList.toggle('active', active.textAlign === 'right');
    },

    handleSelection: function(e) {
        const active = this.canvas.getActiveObject();
        const propBar = document.getElementById('canvaPropertiesBar');
        const textControls = document.getElementById('canvaTextControls');

        if (!active || active.isGuide) {
            if (propBar) propBar.style.display = 'none';
            return;
        }

        if (propBar) propBar.style.display = 'flex';
        this.checkObjectBoundaries();

        if (active.type === 'i-text' || active.type === 'text') {
            if (textControls) textControls.style.display = 'flex';

            const fontSelect = document.getElementById('canvaFontFamily');
            if (fontSelect && active.fontFamily) fontSelect.value = active.fontFamily;

            const fontSizeInput = document.getElementById('canvaFontSize');
            if (fontSizeInput && active.fontSize) fontSizeInput.value = Math.round(active.fontSize);

            const colorPicker = document.getElementById('canvaTextColorPicker');
            if (colorPicker && active.fill && typeof active.fill === 'string') colorPicker.value = active.fill;

            this.updateFormatButtons();
        } else {
            if (textControls) textControls.style.display = 'none';

            const colorPicker = document.getElementById('canvaTextColorPicker');
            if (colorPicker && active.fill && typeof active.fill === 'string') colorPicker.value = active.fill;
        }
    },

    handleSelectionClear: function() {
        const propBar = document.getElementById('canvaPropertiesBar');
        if (propBar) propBar.style.display = 'none';
        const alertBox = document.getElementById('canvaBleedAlert');
        if (alertBox) alertBox.style.display = 'none';
    },

    duplicateSelected: function() {
        const active = this.canvas.getActiveObject();
        if (!active || active.isGuide) return;

        active.clone((cloned) => {
            this.canvas.discardActiveObject();
            cloned.set({
                left: cloned.left + 20,
                top: cloned.top + 20,
                evented: true,
            });
            if (cloned.type === 'activeSelection') {
                cloned.canvas = this.canvas;
                cloned.forEachObject((obj) => {
                    this.canvas.add(obj);
                });
                cloned.setCoords();
            } else {
                this.canvas.add(cloned);
            }
            this.canvas.setActiveObject(cloned);
            this.canvas.requestRenderAll();
            this.checkObjectBoundaries();
            this.saveState();
        });
    },

    deleteSelected: function() {
        const active = this.canvas.getActiveObject();
        if (active && !active.isGuide) {
            this.canvas.remove(active);
            this.canvas.discardActiveObject();
            this.canvas.requestRenderAll();
            this.checkObjectBoundaries();
            this.saveState();
        }
    },

    bringForward: function() {
        const active = this.canvas.getActiveObject();
        if (active) {
            this.canvas.bringForward(active);
            this.canvas.requestRenderAll();
        }
    },

    sendBackward: function() {
        const active = this.canvas.getActiveObject();
        if (active) {
            this.canvas.sendBackwards(active);
            this.drawGuides();
            this.canvas.requestRenderAll();
        }
    },

    alignObject: function(alignment) {
        const active = this.canvas.getActiveObject();
        if (!active) return;

        if (alignment === 'center-h') {
            active.centerH();
        } else if (alignment === 'center-v') {
            active.centerV();
        } else if (alignment === 'left') {
            active.set('left', 60);
        } else if (alignment === 'right') {
            active.set('left', this.width - (active.getScaledWidth()) - 60);
        }
        active.setCoords();
        this.canvas.requestRenderAll();
        this.checkObjectBoundaries();
        this.saveState();
    },

    setGroupFill: function(group, color) {
        if (group.getObjects) {
            group.getObjects().forEach(obj => {
                if (obj.set) obj.set('fill', color);
            });
        } else {
            group.set('fill', color);
        }
    },

    saveState: function() {
        if (this.isHistoryProcessing) return;
        
        const json = JSON.stringify(this.canvas.toJSON(['isGuide']));
        if (this.historyIndex < this.history.length - 1) {
            this.history = this.history.slice(0, this.historyIndex + 1);
        }
        this.history.push(json);
        this.historyIndex++;
    },

    undo: function() {
        if (this.historyIndex > 0) {
            this.isHistoryProcessing = true;
            this.historyIndex--;
            this.canvas.loadFromJSON(this.history[this.historyIndex], () => {
                this.drawGuides();
                this.canvas.renderAll();
                this.checkObjectBoundaries();
                this.isHistoryProcessing = false;
            });
        }
    },

    redo: function() {
        if (this.historyIndex < this.history.length - 1) {
            this.historyIndex++;
            this.canvas.loadFromJSON(this.history[this.historyIndex], () => {
                this.drawGuides();
                this.canvas.renderAll();
                this.checkObjectBoundaries();
                this.isHistoryProcessing = false;
            });
        }
    },

    exportCleanSvg: function() {
        const guides = [];
        this.canvas.getObjects().forEach(obj => {
            if (obj.isGuide) {
                guides.push(obj);
                this.canvas.remove(obj);
            }
        });

        let svgOutput = this.canvas.toSVG({
            viewBox: {
                x: 0,
                y: 0,
                width: this.width,
                height: this.height
            }
        });

        // Ensure root <svg> has responsive width/height and proper viewBox
        svgOutput = svgOutput.replace(/<svg\b([^>]*)>/i, (match, attrs) => {
            let cleanAttrs = attrs
                .replace(/\bwidth="[^"]*"/gi, '')
                .replace(/\bheight="[^"]*"/gi, '')
                .replace(/\bviewBox="[^"]*"/gi, '')
                .replace(/\bpreserveAspectRatio="[^"]*"/gi, '')
                .replace(/\bstyle="[^"]*"/gi, '');
            return `<svg ${cleanAttrs} width="100%" height="100%" viewBox="0 0 ${this.width} ${this.height}" preserveAspectRatio="xMidYMid meet" style="width:100%;height:100%;display:block;">`;
        });

        guides.forEach(g => this.canvas.add(g));
        this.canvas.requestRenderAll();

        return svgOutput;
    },

    saveAndApplyToOrder: function() {
        this.checkDoubleSidedState();

        // Mevcut yüzün çıktısını ve JSON durumunu al
        const currentSvg = this.exportCleanSvg();
        this.sidesData[this.currentSide].svg = currentSvg;
        this.sidesData[this.currentSide].json = JSON.stringify(this.canvas.toJSON(['isGuide']));

        let frontSvg = '';
        let backSvg = '';

        if (this.currentSide === 'front') {
            frontSvg = currentSvg;
            backSvg = this.isDoubleSided ? (this.sidesData.back.svg || '') : '';
        } else {
            backSvg = this.isDoubleSided ? currentSvg : '';
            frontSvg = this.sidesData.front.svg || currentSvg;
        }

        // Tek yön ise arka yüzü kesinlikle boşalt
        if (!this.isDoubleSided) {
            backSvg = '';
            this.sidesData.back.svg = '';
            this.sidesData.back.json = null;
        }

        // 1. Gizli Form Alanlarını Doldur
        const hiddenSvgInput = document.getElementById('selectedDesignSvg');
        if (hiddenSvgInput) hiddenSvgInput.value = frontSvg;

        const hiddenBackSvgInput = document.getElementById('selectedDesignBackSvg');
        if (hiddenBackSvgInput) hiddenBackSvgInput.value = backSvg;

        const designTypeInput = document.getElementById('designTypeInput');
        if (designTypeInput) designTypeInput.value = 'canva_studio';

        // 2. 3. Tasarım Adımını "Tasarım Kaydedildi" Kartına Dönüştür
        if (typeof window.showSavedDesignPanel === 'function') {
            window.showSavedDesignPanel(frontSvg, backSvg, this.isDoubleSided && !!backSvg);
        }

        // 3. Modalı Kapat
        const modalEl = document.getElementById('canvaStudioModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        }

        // 4. Bildirim
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Tasarımınız Kaydedildi!',
                text: `${this.isDoubleSided && backSvg ? 'Ön ve Arka yüz tasarımınız' : 'Tasarımınız'} %100 baskıya uygun olarak siparişinize eklendi.`,
                timer: 2000,
                showConfirmButton: false
            });
        }
    }
};

window.CanvaStudio = CanvaStudio;
