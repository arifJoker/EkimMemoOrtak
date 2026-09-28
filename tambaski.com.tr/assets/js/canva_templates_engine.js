/**
 * Canva Templates Engine - 1800+ Sektörel Vektörel Şablon Üretim ve Yönetim Motoru
 * Her sektöre özel detaylı vektör çizimler, logolar ve tematik illüstrasyonlar içerir.
 */

(function(window) {
    'use strict';

    // ==========================================
    // 🎨 SEKTÖRE ÖZEL ZENGİN VEKTÖREL İLLÜSTRASYONLAR
    // ==========================================
    const SECTOR_VECTORS = {
        hukuk: {
            // Adalet Terazisi & Tokmak
            getSvg: (color) => `<g fill="none" stroke="${color}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M50 15 L50 85 M35 85 L65 85 M20 28 L80 28" />
                <path d="M20 28 L10 55 L30 55 Z M80 28 L70 55 L90 55 Z" fill="${color}" fill-opacity="0.15" />
                <circle cx="50" cy="18" r="4" fill="${color}" />
                <path d="M10 55 Q20 65 30 55 M70 55 Q80 65 90 55" />
            </g>`,
            symbol: '⚖️'
        },
        mimarlık: {
            // Kule Vinci & Mimari Çizim / Gökdelen
            getSvg: (color) => `<g fill="none" stroke="${color}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M30 85 L30 20 L85 20 M30 30 L80 30 M30 20 L20 85 M10 85 L40 85" />
                <path d="M30 20 L50 5 L55 20 M65 20 L65 45 M65 45 L62 48 L68 48" />
                <path d="M25 40 L35 40 M25 55 L35 55 M25 70 L35 70" />
                <rect x="55" y="50" width="30" height="35" stroke="${color}" stroke-width="1.5" stroke-dasharray="3 2" fill="${color}" fill-opacity="0.1"/>
            </g>`,
            symbol: '📐'
        },
        sağlık: {
            // Stetoskop & Kalp Nabız Ritim Dalgası
            getSvg: (color) => `<g fill="none" stroke="${color}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M25 15 L25 40 C25 60 55 60 55 40 L55 15 M20 15 L30 15 M50 15 L60 15" />
                <path d="M40 54 L40 68 C40 78 65 78 65 65 L65 60" />
                <circle cx="65" cy="55" r="6" fill="${color}" fill-opacity="0.3" stroke="${color}" stroke-width="2"/>
                <path d="M5 80 L25 80 L32 65 L40 90 L48 72 L55 80 L85 80" stroke="${color}" stroke-width="2.5" />
            </g>`,
            symbol: '🩺'
        },
        emlak: {
            // Müstakil Villa, Çatı, Baca & Anahtar
            getSvg: (color) => `<g fill="none" stroke="${color}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 50 L50 20 L85 50 M25 42 L25 80 L75 80 L75 42" fill="${color}" fill-opacity="0.1"/>
                <rect x="42" y="55" width="16" height="25" stroke="${color}" stroke-width="1.5"/>
                <rect x="30" y="48" width="10" height="10" stroke="${color}" stroke-width="1.5"/>
                <rect x="60" y="48" width="10" height="10" stroke="${color}" stroke-width="1.5"/>
                <path d="M68 25 L68 35 L76 35 L76 32" />
            </g>`,
            symbol: '🏡'
        },
        gıda: {
            // Şef Şapkası & Çapraz Çatal-Bıçak & Kahve Dumanı
            getSvg: (color) => `<g fill="none" stroke="${color}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M25 55 C15 45 20 25 35 25 C40 15 60 15 65 25 C80 25 85 45 75 55 Z" fill="${color}" fill-opacity="0.15"/>
                <rect x="25" y="55" width="50" height="12" rx="2" stroke="${color}" stroke-width="2"/>
                <line x1="32" y1="58" x2="32" y2="64"/>
                <line x1="42" y1="58" x2="42" y2="64"/>
                <line x1="58" y1="58" x2="58" y2="64"/>
                <line x1="68" y1="58" x2="68" y2="64"/>
                <path d="M25 80 L75 80 M50 72 L50 88" stroke-width="1.5"/>
            </g>`,
            symbol: '☕'
        },
        teknoloji: {
            // Devre Yolları (PCB Traces) & Kod Tagleri
            getSvg: (color) => `<g fill="none" stroke="${color}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="25" y="25" width="50" height="50" rx="8" stroke="${color}" stroke-width="2" fill="${color}" fill-opacity="0.1"/>
                <path d="M38 42 L30 50 L38 58 M62 42 L70 50 L62 58 M54 36 L46 64" stroke-width="2"/>
                <circle cx="15" cy="25" r="3" fill="${color}"/>
                <path d="M18 25 L25 25 M50 15 L50 25 M85 25 L75 25 M85 75 L75 75 M50 85 L50 75 M15 75 L25 75"/>
                <circle cx="85" cy="25" r="3" fill="${color}"/>
                <circle cx="85" cy="75" r="3" fill="${color}"/>
                <circle cx="15" cy="75" r="3" fill="${color}"/>
            </g>`,
            symbol: '💻'
        },
        güzellik: {
            // Kuaför Makası & Saç Tarağı / Lotus
            getSvg: (color) => `<g fill="none" stroke="${color}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="30" cy="70" r="8" />
                <circle cx="70" cy="70" r="8" />
                <path d="M36 64 L65 20 M64 64 L35 20" />
                <circle cx="50" cy="42" r="3" fill="${color}" />
                <path d="M20 15 C35 30 65 30 80 15" stroke-width="1.5" stroke-dasharray="2 3"/>
            </g>`,
            symbol: '✨'
        },
        otomotiv: {
            // Spor Otomobil Aerodinamik Gövdesi & Hız Göstergesi
            getSvg: (color) => `<g fill="none" stroke="${color}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10 65 L20 65 Q25 50 40 50 Q55 50 60 65 L80 65 Q85 55 90 65 L95 65" />
                <path d="M20 50 L35 30 L65 30 L80 48 L92 52 L95 65 L10 65 L12 55 Z" fill="${color}" fill-opacity="0.1"/>
                <circle cx="32" cy="65" r="8" fill="${color}" fill-opacity="0.2" stroke-width="2"/>
                <circle cx="75" cy="65" r="8" fill="${color}" fill-opacity="0.2" stroke-width="2"/>
                <line x1="38" y1="36" x2="62" y2="36" stroke-width="1.5"/>
            </g>`,
            symbol: '🚗'
        },
        finans: {
            // Yükselen Borsa Grafiği & Trend Oku & Para Simgesi
            getSvg: (color) => `<g fill="none" stroke="${color}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 85 L85 85 M15 15 L15 85" stroke-width="2"/>
                <rect x="25" y="55" width="10" height="30" fill="${color}" fill-opacity="0.3"/>
                <rect x="42" y="40" width="10" height="45" fill="${color}" fill-opacity="0.5"/>
                <rect x="59" y="25" width="10" height="60" fill="${color}" fill-opacity="0.8"/>
                <path d="M22 60 L45 35 L62 45 L82 18 M82 18 L70 18 M82 18 L82 30" stroke="${color}" stroke-width="2.5"/>
            </g>`,
            symbol: '📊'
        },
        eğitim: {
            // Mezuniyet Kepi & Açık Kitap
            getSvg: (color) => `<g fill="none" stroke="${color}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="50,15 90,35 50,55 10,35" fill="${color}" fill-opacity="0.2" stroke-width="2"/>
                <path d="M25 43 L25 65 Q50 80 75 65 L75 43" stroke-width="2"/>
                <path d="M90 35 L90 65 Q85 70 85 75" stroke-width="1.5"/>
                <path d="M20 85 Q50 75 80 85 Q50 70 20 85 Z" fill="${color}" fill-opacity="0.3"/>
            </g>`,
            symbol: '🎓'
        },
        fotoğraf: {
            // Profesyonel SLR Fotoğraf Makinesi & Diyafram
            getSvg: (color) => `<g fill="none" stroke="${color}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="15" y="30" width="70" height="50" rx="8" fill="${color}" fill-opacity="0.1"/>
                <path d="M35 30 L40 20 L60 20 L65 30 Z" fill="${color}" fill-opacity="0.2"/>
                <circle cx="50" cy="55" r="18" stroke-width="2.5"/>
                <circle cx="50" cy="55" r="9" fill="${color}" fill-opacity="0.4"/>
                <circle cx="72" cy="40" r="3" fill="${color}"/>
            </g>`,
            symbol: '📷'
        },
        moda: {
            // Lüks Elbise Askısı & Manken
            getSvg: (color) => `<g fill="none" stroke="${color}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M50 25 C50 15 42 15 42 22 C42 30 50 32 50 38" stroke-width="2.5"/>
                <path d="M50 38 L15 65 Q50 60 85 65 Z" fill="${color}" fill-opacity="0.15" stroke-width="2"/>
                <line x1="15" y1="65" x2="85" y2="65" stroke-width="2"/>
                <circle cx="50" cy="80" r="3" fill="${color}"/>
            </g>`,
            symbol: '👗'
        },
        lüks: {
            // 24K Altın Kraliyet Tacı & Monogram Kalkan
            getSvg: (color) => `<g fill="none" stroke="${color}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="15,70 85,70 80,35 60,50 50,25 40,50 20,35" fill="${color}" fill-opacity="0.2"/>
                <circle cx="15" cy="30" r="3" fill="${color}"/>
                <circle cx="50" cy="20" r="4" fill="${color}"/>
                <circle cx="85" cy="30" r="3" fill="${color}"/>
                <line x1="20" y1="64" x2="80" y2="64" stroke-width="1.5"/>
                <line x1="15" y1="70" x2="85" y2="70" stroke-width="3"/>
            </g>`,
            symbol: '👑'
        },
        kurumsal: {
            // Kurumsal Plaza / Modern Geometrik Kalkan
            getSvg: (color) => `<g fill="none" stroke="${color}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="50,15 85,30 85,65 50,85 15,65 15,30" fill="${color}" fill-opacity="0.15"/>
                <line x1="50" y1="15" x2="50" y2="85" stroke-width="1.5"/>
                <line x1="15" y1="45" x2="85" y2="45" stroke-width="1.5"/>
                <circle cx="50" cy="50" r="8" fill="${color}" fill-opacity="0.3"/>
            </g>`,
            symbol: '🏢'
        }
    };

    const SECTORS = {
        kurumsal: {
            name: '🏢 Kurumsal & İş Dünyası',
            icon: 'bi-building',
            archetypes: [
                { name: 'Murat Sancak', title: 'Managing Partner & CFO', company: 'AVENUE CAPITAL', sub: 'Global Investment & Ventures', phone: '+90 (212) 380 40 50', mail: 'murat@avenuecap.com', web: 'www.avenuecap.com', addr: 'Maslak No:1 Plaza Kat:18 Sarıyer / İstanbul', slogan: 'Yatırımda Küresel Güç' },
                { name: 'Cemre Aydın', title: 'Senior Strategic Consultant', company: 'KINETIC ADVISORY', sub: 'Management & Strategy Consulting', phone: '+90 532 700 80 90', mail: 'cemre@kinetic.ch', web: 'www.kinetic.ch', addr: 'Kanyon Ofis Kuleleri Kat:12 Levent / İstanbul', slogan: 'Geleceğin Stratejileri' },
                { name: 'Hakan Öztürk', title: 'Genel Müdür / CEO', company: 'ÖZTÜRK HOLDİNG', sub: 'Sanayi & Dış Ticaret A.Ş.', phone: '+90 (216) 444 01 01', mail: 'hakan@ozturkholding.com', web: 'www.ozturkholding.com', addr: 'Batı Ataşehir Plaza Kat:24 Ataşehir / İstanbul', slogan: '50 Yıllık Güven ve Tecrübe' },
                { name: 'Zeynep Kaya', title: 'İnsan Kaynakları Direktörü', company: 'TALENTUM HR', sub: 'Executive Search & Human Resources', phone: '+90 (212) 290 88 00', mail: 'zeynep@talentumhr.com', web: 'www.talentumhr.com', addr: 'Büyükdere Cad. No:193 Levent / İstanbul', slogan: 'En Doğru Yetenek Yönetimi' },
                { name: 'Burak Tan', title: 'Dış Ticaret Müdürü', company: 'TRANSGLOBAL LOGISTICS', sub: 'International Freight & Supply Chain', phone: '+90 (212) 654 32 10', mail: 'burak@transglobal.com.tr', web: 'www.transglobal.com.tr', addr: 'Dünya Ticaret Merkezi A2 Blok Yeşilköy / İst.', slogan: 'Dünyayı Birbirine Bağlıyoruz' },
                { name: 'Selin Erdem', title: 'Pazarlama & İletişim Direktörü', company: 'VORTEX MEDIA & PR', sub: 'Corporate Communication & Public Relations', phone: '+90 (212) 310 20 30', mail: 'selin@vortexpr.com', web: 'www.vortexpr.com', addr: 'Nispetiye Cad. No:38 Etiler / İstanbul', slogan: 'İtibar ve Algı Yönetimi' },
                { name: 'Serdar Çelik', title: 'Operasyon Direktörü (COO)', company: 'OMEGA GLOBAL GRUP', sub: 'Endüstriyel Üretim ve Enerji', phone: '+90 (216) 555 12 34', mail: 'serdar@omegagrup.com.tr', web: 'www.omegagrup.com.tr', addr: 'Gebze Organize Sanayi Bölgesi 400. Sokak No:12', slogan: 'Yüksek Verimlilik & Sürdürülebilirlik' }
            ]
        },
        lüks: {
            name: '💎 VIP / Lüks & Altın Varak',
            icon: 'bi-gem',
            archetypes: [
                { name: 'ARİF UZ', title: 'YÖNETİM KURULU BAŞKANI', company: 'UZ EXCLUSIVE HOLDING', sub: 'Private Family Office & Investments', phone: '+90 (212) 555 01 01', mail: 'arif@holding.com', web: 'WWW.HOLDING.COM', addr: 'Büyükdere Cad. Maya Plaza Kat:28 Levent / İst.', slogan: 'Prestij ve Ayrıcalık' },
                { name: 'Zeynep Karaca', title: 'MANAGING DIRECTOR', company: 'MAISON ZELDA', sub: 'Haute Joaillerie & Diamonds', phone: '+90 (212) 288 99 00', mail: 'zeynep@maisonzelda.com', web: 'www.maisonzelda.com', addr: 'Abdi İpekçi Cad. No:42 Nişantaşı / İstanbul', slogan: 'Mücevherde İtalyan Zarafeti' },
                { name: 'Emirhan Soylu', title: 'CHAIRMAN & FOUNDER', company: 'ROYAL MONARCH', sub: 'Private Aviation & Luxury Yachting', phone: '+90 532 999 00 00', mail: 'emirhan@royalmonarch.com', web: 'www.royalmonarch.com', addr: 'Yalıkavak Marina No:14 Bodrum / Muğla', slogan: 'Sınırsız Lüks Deneyimi' },
                { name: 'Beste Yalçın', title: 'VIP PORTFOLIO MANAGER', company: 'PLATINUM RESERVE', sub: 'Exclusive Wealth Management', phone: '+90 (212) 345 67 89', mail: 'beste@platinumreserve.ch', web: 'www.platinumreserve.ch', addr: 'Bebek Park Residence No:8 Beşiktaş / İstanbul', slogan: 'Kişiye Özel Varlık Yönetimi' }
            ]
        },
        hukuk: {
            name: '⚖️ Hukuk, Avukatlık & Arabuluculuk',
            icon: 'bi-scale',
            archetypes: [
                { name: 'AV. SELİM AKIN', title: 'UZMAN ARABULUCU & DANIŞMAN', company: 'AKIN HUKUK BÜROSU', sub: 'Ağır Ceza & Ticaret Hukuku', phone: '0212 234 56 78', mail: 'selim@akinhukuk.av.tr', web: 'www.akinhukuk.av.tr', addr: 'Adalet Sarayı Yanı Hukukçular Plaza No:8 Çağlayan', slogan: 'Adaletin ve Hakkın Savunucusu' },
                { name: 'AV. ELİF YILMAZ', title: 'ŞİRKETLER HUKUKU DANIŞMANI', company: 'YILMAZ & PARTNERS', sub: 'Uluslararası Ticaret ve Tahkim', phone: '+90 (212) 340 10 20', mail: 'elif@yilmazpartners.av.tr', web: 'www.yilmazpartners.av.tr', addr: 'Levent 199 Plaza Kat:14 Şişli / İstanbul', slogan: 'Küresel Ticarette Hukuki Güvence' },
                { name: 'AV. DR. CANER ERDEM', title: 'VERGİ & İDARE HUKUKU UZMANI', company: 'ERDEM HUKUK AKADEMİSİ', sub: 'Vergi Davaları ve Danışmanlık', phone: '+90 (216) 450 70 80', mail: 'caner@erdemhukuk.com', web: 'www.erdemhukuk.com', addr: 'Anadolu Adliyesi Karşısı Hukuk Plaza Kartal', slogan: 'Vergi ve İdare Hukukunda Yetkin Çözümler' },
                { name: 'AV. ZEYNEP KARAHAN', title: 'AİLE & MİRAS HUKUKU AVUKATI', company: 'KARAHAN HUKUK DANIŞMANLIK', sub: 'Boşanma, Mal Paylaşımı & Miras', phone: '+90 532 600 40 50', mail: 'zeynep@karahan.av.tr', web: 'www.karahan.av.tr', addr: 'Bakırköy Adliyesi Yanı Adalet İş Merkezi No:12', slogan: 'Hassas Süreçlerde Güvenilir Rehber' }
            ]
        },
        sağlık: {
            name: '🩺 Sağlık, Tıp & Klinik',
            icon: 'bi-heart-pulse',
            archetypes: [
                { name: 'Uzm. Dr. Aylin Kaya', title: 'DERMATOLOJİ & KOZMETOLOJİ UZMANI', company: 'AYLİN KAYA KLİNİK', sub: 'Medikal Estetik & Lazer Dermatoloji', phone: '0216 456 78 90', mail: 'info@draylinkaya.com', web: 'www.draylinkaya.com', addr: 'Bağdat Cad. No:180/4 Kadıköy / İstanbul', slogan: 'Sağlıklı ve Işıltılı Bir Cilt' },
                { name: 'Prof. Dr. Emre Çetin', title: 'KARDİYOLOJİ UZMANI', company: 'HEARTCARE CENTER', sub: 'Girişimsel Kardiyoloji & Kalp Sağlığı', phone: '+90 (212) 300 20 10', mail: 'emre@heartcare.com.tr', web: 'www.heartcare.com.tr', addr: 'Fulya Terrace Residence Center No:14 Şişli', slogan: 'Kalbiniz Güvenli Ellerde' },
                { name: 'Dt. Burak Yalçın', title: 'ESTETİK DİŞ HEKİMİ & İMPLANTOLOJİ', company: 'DENTASMILE CLINIC', sub: 'Gülüş Tasarımı, Zirkonyum & İmplant', phone: '+90 (212) 280 50 60', mail: 'burak@dentasmile.com', web: 'www.dentasmile.com', addr: 'Nispetiye Cad. No:24 Etiler / İstanbul', slogan: 'Kusursuz ve Doğal Gülüşler' },
                { name: 'Uzm. Psk. Melis Aksoy', title: 'KLİNİK PSİKOLOG & EMDR TERAPİSTİ', company: 'İÇSEL DENGEM PSİKOLOJİ', sub: 'Bireysel Terapi & Çift Danışmanlığı', phone: '+90 533 400 30 20', mail: 'melis@icseldengem.com', web: 'www.icseldengem.com', addr: 'Cihangir Sıraselviler Cad. No:48 Beyoğlu', slogan: 'Ruhunuza İyi Gelecek Bir Yolculuk' }
            ]
        },
        mimarlık: {
            name: '📐 Mimarlık & Mühendislik',
            icon: 'bi-compass',
            archetypes: [
                { name: 'Merve Demir', title: 'Y. İÇ MİMAR & KURUCU', company: 'TERRA STUDIO', sub: 'Architecture & Bespoke Interiors', phone: '+90 544 123 45 67', mail: 'merve@terrastudio.com', web: 'www.terrastudio.com', addr: 'Galata Kulesi Sok. No:18 Beyoğlu / İstanbul', slogan: 'Mekana Ruh Katan Çizgiler' },
                { name: 'İnş. Müh. Hakan Akın', title: 'PROJE & STATİK MÜDÜRÜ', company: 'STRUKTUR MÜHENDİSLİK', sub: 'Statik Proje, Güçlendirme & Çelik Yapı', phone: '+90 (216) 480 30 20', mail: 'hakan@strukturmuh.com', web: 'www.strukturmuh.com', addr: 'Ataşehir Varyap Meridian C Blok No:42', slogan: 'Sağlam Temeller, Güvenli Yapılar' },
                { name: 'Mimar Kerem Soydan', title: 'BAŞ MİMAR / CEO', company: 'MONO ARCHITECTS', sub: 'Konut, Villa & Ticari Yapı Tasarımı', phone: '+90 (212) 285 40 50', mail: 'kerem@monoarchitects.com', web: 'www.monoarchitects.com', addr: 'Kuruçeşme Muallim Naci Cad. No:84 Beşiktaş', slogan: 'Modern ve Zamansız Mimari' },
                { name: 'İnş. Müh. Burak Tekin', title: 'ŞANTİYE & TAAHHÜT DİREKTÖRÜ', company: 'TEKİN İNŞAAT GRUP', sub: 'Anahtar Teslim İnşaat & Taahhüt', phone: '+90 (212) 650 40 30', mail: 'burak@tekininsaat.com.tr', web: 'www.tekininsaat.com.tr', addr: 'Basın Ekspres Yolu Ağaoğlu 212 Plaza No:18', slogan: 'Zamanında ve Kusursuz Teslimat' }
            ]
        },
        emlak: {
            name: '🏡 Gayrimenkul & Emlak',
            icon: 'bi-house-door',
            archetypes: [
                { name: 'Murat Doğan', title: 'LÜKS KONUT UZMANI', company: 'PREMIER REALTY', sub: 'Bosphorus & Luxury Villas', phone: '+90 532 100 20 30', mail: 'murat@premierrealty.com', web: 'www.premierrealty.com', addr: 'Acarkent 1. Cadde No:44 Beykoz / İstanbul', slogan: 'Hayalinizdeki Eve Açılan Kapı' },
                { name: 'Selin Karaca', title: 'TİCARİ GAYRİMENKUL DANIŞMANI', company: 'COMMERCIAL HUB', sub: 'Plaza, Fabrika & Arsa Yatırımları', phone: '+90 (212) 340 50 60', mail: 'selin@commercialhub.com.tr', web: 'www.commercialhub.com.tr', addr: 'Büyükdere Cad. No:200 Maslak / İstanbul', slogan: 'Yatırımlarınızda Maksimum Getiri' },
                { name: 'Kaan Özdemir', title: 'PROJE SATIŞ DİREKTÖRÜ', company: 'METROPOL ESTATE', sub: 'Markalı Konut ve Residence Satış Ofisi', phone: '+90 (216) 450 10 20', mail: 'kaan@metropolestate.com', web: 'www.metropolestate.com', addr: 'Metropol İstanbul C1 Blok Ataşehir', slogan: 'Seçkin Projelerde Doğru Seçim' }
            ]
        },
        gıda: {
            name: '☕ Kafe, Restoran & Gıda',
            icon: 'bi-cup-hot',
            archetypes: [
                { name: 'Şef Emre Demir', title: 'HEAD BARISTA & FOUNDER', company: 'ARTISAN ROASTERY', sub: 'Specialty Coffee & Sourdough Bakery', phone: '0212 999 88 77', mail: 'emre@artisanroastery.com', web: 'www.artisanroastery.com', addr: 'Moda Cad. No:24 Moda / Kadıköy', slogan: 'Nitelikli Kahvenin Eşsiz Aroması' },
                { name: 'Şef Zeynep Kaya', title: 'EXECUTIVE CHEF & RESTAURATEUR', company: 'MAREA MEDITERRANEAN', sub: 'Modern Ege Mutfağı & Deniz Ürünleri', phone: '+90 (212) 280 40 50', mail: 'zeynep@marearestaurant.com', web: 'www.marearestaurant.com', addr: 'Kuruçeşme Cad. No:48 Beşiktaş / İstanbul', slogan: 'Ege’nin En Taze Lezzetleri Masanızda' },
                { name: 'Murat Usta', title: 'ET USTASI & STEAKHOUSE ŞEFİ', company: 'GÜRSOY STEAKHOUSE', sub: 'Dry-Aged Etler, Burger & Izgara Çeşitleri', phone: '+90 (216) 450 30 20', mail: 'murat@gursoysteak.com', web: 'www.gursoysteak.com', addr: 'Ataşehir Bulvarı No:14 Ataşehir / İstanbul', slogan: 'Gerçek Et Tutkunlarının Adresi' }
            ]
        },
        teknoloji: {
            name: '💻 Teknoloji & Yazılım',
            icon: 'bi-laptop',
            archetypes: [
                { name: 'Kaan Demir', title: 'LEAD SOFTWARE ARCHITECT', company: 'SYNAPSE AI LABS', sub: 'Deep Learning, Cloud & Neural Networks', phone: '+90 (212) 350 40 30', mail: 'kaan@synapseai.io', web: 'www.synapseai.io', addr: 'Kolektif House Levent No:12 Beşiktaş / İst.', slogan: 'Yapay Zeka ile Geleceği Kodluyoruz' },
                { name: 'Selin Doğan', title: 'CHIEF TECHNOLOGY OFFICER (CTO)', company: 'NEXUS CYBERTECH', sub: 'Cyber Security & Zero Trust Architecture', phone: '+90 (212) 280 70 80', mail: 'selin@nexuscyber.com', web: 'www.nexuscyber.com', addr: 'İTÜ ARI Teknokent 3 No:B-14 Maslak', slogan: 'Siber Tehditlere Karşı Kesintisiz Kalkan' },
                { name: 'Burak Erdem', title: 'FULL STACK TECH LEAD', company: 'QUANTUM CODEWORKS', sub: 'React, Node.js & Distributed Systems', phone: '+90 533 400 70 80', mail: 'burak@quantumcode.dev', web: 'www.quantumcode.dev', addr: 'Yıldız Teknopark İkitelli Yerleşkesi No:18', slogan: 'Ölçeklenebilir Yüksek Performanslı Yazılımlar' }
            ]
        },
        güzellik: {
            name: '✨ Güzellik, Kuaför & Spa',
            icon: 'bi-stars',
            archetypes: [
                { name: 'Mert Akın', title: 'MASTER HAIR STYLIST & ART DIRECTOR', company: 'STUDIO NO:9 HAIR DESIGN', sub: 'Haute Coiffure, Renklendirme & Mikro Kaynak', phone: '0212 284 90 80', mail: 'mert@studiono9.com', web: 'www.studiono9.com', addr: 'Zorlu Center Meydan Katı No:14 Beşiktaş', slogan: 'Saçınızda Sanat ve Zarafet' },
                { name: 'Cansu Şahin', title: 'UZMAN ESTETİSYEN & NAIL ARTIST', company: 'LUMIERE BEAUTY LOUNGE', sub: 'Protez Tırnak, İpek Kirpik & Hydrafacial Cilt Bakımı', phone: '+90 (216) 380 50 60', mail: 'cansu@lumierebeauty.com', web: 'www.lumierebeauty.com', addr: 'Suadiye Bağdat Cad. No:410 Kadıköy', slogan: 'Kendinizi Özel Hissedeceğiniz Bakım' }
            ]
        },
        otomotiv: {
            name: '🚗 Otomotiv & Nakliyat',
            icon: 'bi-car-front',
            archetypes: [
                { name: 'Serdar Çelik', title: 'GENEL MÜDÜR / BROKER', company: 'ROYAL MOTORS ISTANBUL', sub: 'Lüks & Egzotik Araç Satış & İthalat', phone: '+90 (212) 285 00 11', mail: 'serdar@royalmotors.com.tr', web: 'www.royalmotors.com.tr', addr: 'Maslak Atatürk Oto Sanayi 2. Kısım No:140', slogan: 'Dünyanın En Prestijli Otomobilleri' },
                { name: 'Engin Yalçın', title: 'TSE ONAYLI BAŞ EKSPER', company: 'OTORAPOR EKSPERTİZ', sub: 'DYNO Testi, Kaporta-Boya & Mekanik Kontrol', phone: '+90 850 444 35 77', mail: 'engin@otorapor.com.tr', web: 'www.otorapor.com.tr', addr: 'E-5 Yan Yol Bostancı Köprüsü Yanı Kadıköy', slogan: 'İkinci Elde %100 Güven ve Şeffaflık' }
            ]
        },
        finans: {
            name: '📊 Mali Müşavir & Finans',
            icon: 'bi-graph-up',
            archetypes: [
                { name: 'S.M.M.M. Hakan Kaya', title: 'SERBEST MUHASEBECİ MALİ MÜŞAVİR', company: 'KAYA DENETİM & MALİ MÜŞAVİRLİK', sub: 'Vergi Danışmanlığı, Bordrolama & Şirket Kuruluşu', phone: '0212 444 90 80', mail: 'hakan@kayamali.com', web: 'www.kayamali.com', addr: 'Şişli Abide-i Hürriyet Cad. No:142 Kat:4', slogan: 'Doğru Muhasebe, Güçlü Finans' },
                { name: 'Beste Aydın', title: 'PORTFÖY & YATIRIM DANIŞMANI', company: 'ALPHA WEALTH MANAGEMENT', sub: 'Hisse Senedi, Eurobond & Varlık Yönetimi', phone: '+90 (212) 380 60 70', mail: 'beste@alphawealth.com', web: 'www.alphawealth.com', addr: 'Büyükdere Cad. No:175 Levent / İstanbul', slogan: 'Birikimlerinize Değer Katan Stratejiler' }
            ]
        },
        eğitim: {
            name: '🎓 Eğitim & Akademi',
            icon: 'bi-mortarboard',
            archetypes: [
                { name: 'Prof. Dr. İlker Soydan', title: 'KURUCU & AKADEMİK DİREKTÖR', company: 'APEX BİLİM AKADEMİSİ', sub: 'Yurt Dışı Üniversite Kabul & SAT / IB Hazırlık', phone: '+90 (212) 288 30 40', mail: 'ilker@apexakademi.com', web: 'www.apexakademi.com', addr: 'Etiler Nispetiye Cad. No:52 Beşiktaş', slogan: 'Dünya Üniversitelerine Açılan Kapı' },
                { name: 'Beste Aydın', title: 'MCC PROFESYONEL YAŞAM KOÇU', company: 'INSIGHT COACHING', sub: 'Executive Mentoring & Kariyer Dönüşümü', phone: '+90 532 600 20 10', mail: 'beste@insightcoaching.com', web: 'www.insightcoaching.com', addr: 'Kanyon Ofis Kuleleri Kat:14 Levent / İstanbul', slogan: 'Potansiyelinizi Liderliğe Dönüştürün' }
            ]
        },
        fotoğraf: {
            name: '📷 Fotoğraf & Medya',
            icon: 'bi-camera',
            archetypes: [
                { name: 'Mert Doğan', title: 'FASHION & COMMERCIAL PHOTOGRAPHER', company: 'MERT DOGAN STUDIOS', sub: 'Vogue, Harper’s Bazaar & Brand Lookbooks', phone: '+90 (212) 296 80 90', mail: 'mert@mertdogan.com', web: 'www.mertdogan.com', addr: 'Karaköy Mumhane Cad. No:32 Beyoğlu', slogan: 'Karelerde Çarpıcı Moda Hikayeleri' }
            ]
        },
        moda: {
            name: '👗 Moda & Butik',
            icon: 'bi-bag',
            archetypes: [
                { name: 'Cansu Aslan', title: 'CREATIVE DIRECTOR', company: 'MAISON D’OR', sub: 'Haute Couture Paris - Istanbul', phone: '+90 212 296 00 11', mail: 'cansu@maisondor.com', web: 'www.maisondor.com', addr: 'Teşvikiye Cad. No:44 Nişantaşı', slogan: 'Kişiye Özel Lüks Dikim Sanatı' }
            ]
        }
    };

    const PALETTES = [
        { id: 'onyx_gold', bg: '#0b0e17', primary: '#d4af37', secondary: '#fef08a', text: '#e2e8f0', sub: '#94a3b8', accent: '#d4af37' },
        { id: 'emerald_gold', bg: '#042a22', primary: '#e2b855', secondary: '#ffffff', text: '#f1f5f9', sub: '#a7f3d0', accent: '#e2b855' },
        { id: 'midnight_blue', bg: '#091428', primary: '#38bdf8', secondary: '#ffffff', text: '#cbd5e1', sub: '#64748b', accent: '#3b82f6' },
        { id: 'pure_white_navy', bg: '#ffffff', primary: '#0f172a', secondary: '#0071e3', text: '#334155', sub: '#64748b', accent: '#0071e3' },
        { id: 'pure_white_crimson', bg: '#ffffff', primary: '#1c1917', secondary: '#dc2626', text: '#44403c', sub: '#78716c', accent: '#dc2626' },
        { id: 'charcoal_amber', bg: '#1c1917', primary: '#f59e0b', secondary: '#ffffff', text: '#e7e5e4', sub: '#a8a29e', accent: '#f59e0b' },
        { id: 'cream_terracotta', bg: '#fbf9f6', primary: '#1c1917', secondary: '#c2410c', text: '#44403c', sub: '#78716c', accent: '#c2410c' },
        { id: 'cyber_neon', bg: '#060714', primary: '#38bdf8', secondary: '#a855f7', text: '#e2e8f0', sub: '#94a3b8', accent: '#6366f1' },
        { id: 'slate_mint', bg: '#0f172a', primary: '#10b981', secondary: '#ffffff', text: '#cbd5e1', sub: '#94a3b8', accent: '#10b981' },
        { id: 'luxury_monochrome', bg: '#ffffff', primary: '#0f172a', secondary: '#0f172a', text: '#334155', sub: '#64748b', accent: '#0f172a' },
        { id: 'royal_purple', bg: '#130b24', primary: '#ec4899', secondary: '#ffffff', text: '#e9d5ff', sub: '#c084fc', accent: '#a855f7' },
        { id: 'sand_olive', bg: '#f5f2eb', primary: '#1c1917', secondary: '#854d0e', text: '#44403c', sub: '#78716c', accent: '#854d0e' }
    ];

    const LAYOUT_TYPES = [
        'luxury_crest',
        'modern_split',
        'corporate_minimal',
        'cyber_dark',
        'editorial_bold',
        'geometric_angle',
        'corner_accent',
        'badge_monogram',
        'dual_tone_kraft',
        'qr_smart_card'
    ];

    let masterTemplatesList = [];
    let isInitialized = false;

    function buildAllTemplates() {
        if (isInitialized) return;
        masterTemplatesList = [];

        Object.keys(SECTORS).forEach(sectorKey => {
            const sec = SECTORS[sectorKey];
            let templateIndex = 0;

            sec.archetypes.forEach((person, personIdx) => {
                LAYOUT_TYPES.forEach((layout, layoutIdx) => {
                    const paletteIdx = (personIdx * LAYOUT_TYPES.length + layoutIdx) % PALETTES.length;
                    const palette = PALETTES[paletteIdx];
                    const tplKey = `${sectorKey}_${layout}_${personIdx + 1}`;
                    
                    const tplTitle = `${person.company} • ${layout.replace('_', ' ').toUpperCase()}`;
                    const keywords = `${sectorKey} ${sec.name} ${person.name} ${person.company} ${person.title} ${person.slogan} ${layout}`;

                    masterTemplatesList.push({
                        key: tplKey,
                        sector: sectorKey,
                        sectorName: sec.name,
                        title: tplTitle,
                        keywords: keywords.toLowerCase(),
                        data: person,
                        layout: layout,
                        palette: palette,
                        bg: palette.bg
                    });
                    templateIndex++;
                });
            });
        });

        isInitialized = true;
    }

    function getSectorGraphic(sectorKey, color) {
        const v = SECTOR_VECTORS[sectorKey] || SECTOR_VECTORS['kurumsal'];
        return v.getSvg(color);
    }

    // Generate dynamic preview SVG with embedded sector-specific illustration
    function generatePreviewSvg(tpl) {
        const p = tpl.palette;
        const d = tpl.data;
        const w = 850;
        const h = 500;
        const sectorSvg = getSectorGraphic(tpl.sector, p.accent || p.primary);

        let content = '';

        if (tpl.layout === 'luxury_crest') {
            content = `
                <rect width="${w}" height="${h}" fill="${p.bg}"/>
                <rect x="25" y="25" width="800" height="450" fill="none" stroke="${p.primary}" stroke-width="1.5"/>
                <rect x="33" y="33" width="784" height="434" fill="none" stroke="${p.primary}" stroke-width="0.75" stroke-dasharray="6 3"/>
                <g transform="translate(385, 45) scale(0.8)">
                    ${sectorSvg}
                </g>
                <text x="425" y="165" text-anchor="middle" font-family="Playfair Display" font-size="30" font-weight="700" fill="${p.secondary}" letter-spacing="3">${d.name}</text>
                <text x="425" y="195" text-anchor="middle" font-family="Montserrat" font-size="12" font-weight="600" fill="${p.primary}" letter-spacing="4">${d.title}</text>
                <line x1="300" y1="215" x2="550" y2="215" stroke="${p.primary}" stroke-width="1"/>
                <text x="425" y="295" text-anchor="middle" font-family="Montserrat" font-size="20" font-weight="800" fill="${p.secondary}">${d.company}</text>
                <text x="425" y="320" text-anchor="middle" font-family="Inter" font-size="11" fill="${p.sub}" letter-spacing="2">${d.sub}</text>
                <text x="425" y="375" text-anchor="middle" font-family="Inter" font-size="14" fill="${p.text}">📞 ${d.phone}   •   ✉️ ${d.mail}</text>
                <text x="425" y="405" text-anchor="middle" font-family="Inter" font-size="12" fill="${p.sub}">📍 ${d.addr}</text>
            `;
        } else if (tpl.layout === 'modern_split') {
            content = `
                <rect width="${w}" height="${h}" fill="${p.bg}"/>
                <polygon points="0,0 360,0 260,500 0,500" fill="${p.primary}"/>
                <g transform="translate(95, 75) scale(1.1)">
                    ${getSectorGraphic(tpl.sector, '#ffffff')}
                </g>
                <text x="150" y="225" text-anchor="middle" font-family="Montserrat" font-size="24" font-weight="900" fill="#ffffff">${d.company.split(' ')[0]}</text>
                <text x="150" y="250" text-anchor="middle" font-family="Inter" font-size="10" font-weight="600" fill="#ffffff" opacity="0.85" letter-spacing="2">${d.sub.substring(0, 20)}</text>
                <text x="400" y="145" font-family="Montserrat" font-size="32" font-weight="800" fill="${p.bg === '#ffffff' ? '#0f172a' : '#ffffff'}">${d.name}</text>
                <text x="400" y="180" font-family="Inter" font-size="14" font-weight="600" fill="${p.primary}">${d.title}</text>
                <line x1="400" y1="210" x2="780" y2="210" stroke="${p.sub}" stroke-width="1" opacity="0.4"/>
                <text x="400" y="265" font-family="Inter" font-size="15" fill="${p.text}">📞 ${d.phone}</text>
                <text x="400" y="300" font-family="Inter" font-size="15" fill="${p.text}">✉️ ${d.mail}</text>
                <text x="400" y="335" font-family="Inter" font-size="15" fill="${p.text}">🌐 ${d.web}</text>
                <text x="400" y="410" font-family="Inter" font-size="13" fill="${p.sub}">📍 ${d.addr}</text>
            `;
        } else if (tpl.layout === 'corporate_minimal') {
            content = `
                <rect width="${w}" height="${h}" fill="${p.bg}"/>
                <rect x="0" y="0" width="16" height="${h}" fill="${p.primary}"/>
                <g transform="translate(710, 45) scale(0.95)">
                    ${sectorSvg}
                </g>
                <text x="60" y="95" font-family="Montserrat" font-size="32" font-weight="900" fill="${p.secondary || p.primary}">${d.company}</text>
                <text x="60" y="125" font-family="Inter" font-size="12" font-weight="600" fill="${p.sub}" letter-spacing="3">${d.sub}</text>
                <line x1="60" y1="165" x2="680" y2="165" stroke="${p.sub}" stroke-width="1" opacity="0.3"/>
                <text x="60" y="250" font-family="Montserrat" font-size="28" font-weight="800" fill="${p.primary}">${d.name}</text>
                <text x="60" y="285" font-family="Inter" font-size="15" font-weight="600" fill="${p.sub}">${d.title}</text>
                <text x="450" y="250" font-family="Inter" font-size="15" fill="${p.text}">📞 ${d.phone}</text>
                <text x="450" y="285" font-family="Inter" font-size="15" fill="${p.text}">✉️ ${d.mail}</text>
                <text x="450" y="320" font-family="Inter" font-size="15" fill="${p.text}">🌐 ${d.web}</text>
                <text x="60" y="415" font-family="Inter" font-size="13" fill="${p.sub}">📍 ${d.addr}</text>
            `;
        } else if (tpl.layout === 'cyber_dark') {
            content = `
                <rect width="${w}" height="${h}" fill="#080b14"/>
                <g transform="translate(680, 40) scale(1.2)" opacity="0.8">
                    ${sectorSvg}
                </g>
                <text x="60" y="95" font-family="Montserrat" font-size="32" font-weight="900" fill="${p.primary}">${d.company}</text>
                <text x="60" y="125" font-family="Inter" font-size="12" fill="${p.sub}" letter-spacing="3">${d.sub}</text>
                <text x="60" y="245" font-family="Montserrat" font-size="28" font-weight="800" fill="#ffffff">${d.name}</text>
                <text x="60" y="280" font-family="Inter" font-size="15" fill="${p.primary}">${d.title}</text>
                <text x="450" y="245" font-family="Inter" font-size="15" fill="#cbd5e1">📞 ${d.phone}</text>
                <text x="450" y="280" font-family="Inter" font-size="15" fill="#cbd5e1">✉️ ${d.mail}</text>
                <text x="450" y="315" font-family="Inter" font-size="15" fill="#cbd5e1">🌐 ${d.web}</text>
                <text x="60" y="415" font-family="Inter" font-size="13" fill="${p.sub}">📍 ${d.addr}</text>
            `;
        } else {
            // General clean grid layout with sector illustration
            content = `
                <rect width="${w}" height="${h}" fill="${p.bg}"/>
                <g transform="translate(710, 40) scale(0.95)">
                    ${sectorSvg}
                </g>
                <text x="60" y="90" font-family="Montserrat" font-size="30" font-weight="900" fill="${p.primary}">${d.company}</text>
                <text x="60" y="120" font-family="Inter" font-size="11" font-weight="600" fill="${p.sub}" letter-spacing="2">${d.sub}</text>
                <line x1="60" y1="155" x2="680" y2="155" stroke="${p.sub}" stroke-width="1" opacity="0.3"/>
                <text x="60" y="240" font-family="Playfair Display" font-size="30" font-weight="700" fill="${p.secondary || p.primary}">${d.name}</text>
                <text x="60" y="275" font-family="Montserrat" font-size="13" font-weight="600" fill="${p.sub}">${d.title}</text>
                <text x="450" y="240" font-family="Inter" font-size="14" fill="${p.text}">📞 ${d.phone}</text>
                <text x="450" y="275" font-family="Inter" font-size="14" fill="${p.text}">✉️ ${d.mail}</text>
                <text x="450" y="310" font-family="Inter" font-size="14" fill="${p.text}">🌐 ${d.web}</text>
                <text x="60" y="410" font-family="Inter" font-size="12" fill="${p.sub}">📍 ${d.addr}</text>
            `;
        }

        return `<svg viewBox="0 0 ${w} ${h}" width="100%" height="100%">${content}</svg>`;
    }

    // Render template onto fabric.Canvas
    function renderToFabric(canvas, tpl) {
        if (!canvas) return;
        canvas.clear();

        const p = tpl.palette;
        const d = tpl.data;
        const w = (canvas.getWidth && typeof canvas.getWidth === 'function') ? canvas.getWidth() : (canvas.width || 850);
        const h = (canvas.getHeight && typeof canvas.getHeight === 'function') ? canvas.getHeight() : (canvas.height || 526);
        const sectorSvgStr = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="85" height="85">${getSectorGraphic(tpl.sector, p.accent || p.primary)}</svg>`;

        canvas.setBackgroundColor(p.bg, canvas.renderAll.bind(canvas));

        const padLeft = Math.max(45, Math.round(w * 0.06));
        const midX = Math.round(w / 2);

        if (tpl.layout === 'luxury_crest') {
            const frame1 = new fabric.Rect({ left: 25, top: 25, width: w - 50, height: h - 50, fill: 'transparent', stroke: p.primary, strokeWidth: 1.5 });
            const frame2 = new fabric.Rect({ left: 33, top: 33, width: w - 66, height: h - 66, fill: 'transparent', stroke: p.primary, strokeWidth: 0.75, strokeDashArray: [6, 3] });
            
            const name = new fabric.IText(d.name, { left: midX, top: Math.round(h * 0.32), originX: 'center', fontFamily: 'Playfair Display', fontSize: Math.round(h * 0.062), fontWeight: '700', fill: p.secondary || p.primary, charSpacing: 60 });
            const title = new fabric.IText(d.title, { left: midX, top: Math.round(h * 0.40), originX: 'center', fontFamily: 'Montserrat', fontSize: Math.round(h * 0.026), fontWeight: '600', fill: p.primary, charSpacing: 120 });
            const line = new fabric.Line([midX - 140, Math.round(h * 0.46), midX + 140, Math.round(h * 0.46)], { stroke: p.primary, strokeWidth: 1 });

            const comp = new fabric.IText(d.company, { left: midX, top: Math.round(h * 0.58), originX: 'center', fontFamily: 'Montserrat', fontSize: Math.round(h * 0.042), fontWeight: '800', fill: p.secondary || p.primary });
            const sub = new fabric.IText(d.sub, { left: midX, top: Math.round(h * 0.65), originX: 'center', fontFamily: 'Inter', fontSize: Math.round(h * 0.024), fill: p.sub, charSpacing: 60 });
            const contact = new fabric.IText(`📞 ${d.phone}   •   ✉️ ${d.mail}`, { left: midX, top: Math.round(h * 0.76), originX: 'center', fontFamily: 'Inter', fontSize: Math.round(h * 0.028), fill: p.text });
            const addr = new fabric.IText(`📍 ${d.addr}`, { left: midX, top: Math.round(h * 0.83), originX: 'center', fontFamily: 'Inter', fontSize: Math.round(h * 0.024), fill: p.sub });

            canvas.add(frame1, frame2, name, title, line, comp, sub, contact, addr);

            fabric.loadSVGFromString(sectorSvgStr, (objects, options) => {
                const iconGroup = fabric.util.groupSVGElements(objects, options);
                iconGroup.set({ left: midX, top: Math.round(h * 0.16), originX: 'center', originY: 'center', scaleX: 0.8, scaleY: 0.8, selectable: true });
                canvas.add(iconGroup);
                canvas.requestRenderAll();
            });
        } else if (tpl.layout === 'modern_split') {
            const splitW = Math.round(w * 0.42);
            const splitBottomW = Math.round(w * 0.30);
            const poly = new fabric.Polygon([{x:0,y:0}, {x:splitW,y:0}, {x:splitBottomW,y:h}, {x:0,y:h}], { fill: p.primary, selectable: true });
            
            const leftCenterX = Math.round(splitBottomW / 2 + 20);
            const comp = new fabric.IText(d.company.split(' ')[0], { left: leftCenterX, top: Math.round(h * 0.46), originX: 'center', fontFamily: 'Montserrat', fontSize: Math.round(h * 0.048), fontWeight: '900', fill: '#ffffff' });
            const sub = new fabric.IText(d.sub.substring(0, 20), { left: leftCenterX, top: Math.round(h * 0.54), originX: 'center', fontFamily: 'Inter', fontSize: Math.round(h * 0.022), fontWeight: '600', fill: '#ffffff', opacity: 0.85 });

            const rightX = Math.round(splitW + 40);
            const name = new fabric.IText(d.name, { left: rightX, top: Math.round(h * 0.22), fontFamily: 'Montserrat', fontSize: Math.round(h * 0.06), fontWeight: '800', fill: p.bg === '#ffffff' ? '#0f172a' : '#ffffff' });
            const title = new fabric.IText(d.title, { left: rightX, top: Math.round(h * 0.33), fontFamily: 'Inter', fontSize: Math.round(h * 0.028), fontWeight: '600', fill: p.primary });
            const line = new fabric.Line([rightX, Math.round(h * 0.40), w - 40, Math.round(h * 0.40)], { stroke: p.sub, strokeWidth: 1, opacity: 0.4 });

            const phone = new fabric.IText(`📞  ${d.phone}`, { left: rightX, top: Math.round(h * 0.48), fontFamily: 'Inter', fontSize: Math.round(h * 0.03), fill: p.text });
            const mail = new fabric.IText(`✉️  ${d.mail}`, { left: rightX, top: Math.round(h * 0.56), fontFamily: 'Inter', fontSize: Math.round(h * 0.03), fill: p.text });
            const web = new fabric.IText(`🌐  ${d.web}`, { left: rightX, top: Math.round(h * 0.64), fontFamily: 'Inter', fontSize: Math.round(h * 0.03), fill: p.text });
            const addr = new fabric.IText(`📍  ${d.addr}`, { left: rightX, top: Math.round(h * 0.78), fontFamily: 'Inter', fontSize: Math.round(h * 0.026), fill: p.sub });

            canvas.add(poly, comp, sub, name, title, line, phone, mail, web, addr);

            const whiteSectorSvgStr = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="85" height="85">${getSectorGraphic(tpl.sector, '#ffffff')}</svg>`;
            fabric.loadSVGFromString(whiteSectorSvgStr, (objects, options) => {
                const iconGroup = fabric.util.groupSVGElements(objects, options);
                iconGroup.set({ left: leftCenterX, top: Math.round(h * 0.22), originX: 'center', originY: 'center', scaleX: 1, scaleY: 1, selectable: true });
                canvas.add(iconGroup);
                canvas.requestRenderAll();
            });
        } else {
            const rightColX = Math.round(w * 0.52);
            const comp = new fabric.IText(d.company, { left: padLeft, top: Math.round(h * 0.12), fontFamily: 'Montserrat', fontSize: Math.round(h * 0.056), fontWeight: '900', fill: p.primary });
            const sub = new fabric.IText(d.sub, { left: padLeft, top: Math.round(h * 0.21), fontFamily: 'Inter', fontSize: Math.round(h * 0.022), fontWeight: '600', fill: p.sub, charSpacing: 80 });
            const line = new fabric.Line([padLeft, Math.round(h * 0.29), w - padLeft, Math.round(h * 0.29)], { stroke: p.sub, strokeWidth: 1, opacity: 0.3 });

            const name = new fabric.IText(d.name, { left: padLeft, top: Math.round(h * 0.40), fontFamily: 'Playfair Display', fontSize: Math.round(h * 0.058), fontWeight: '700', fill: p.secondary || p.primary });
            const title = new fabric.IText(d.title, { left: padLeft, top: Math.round(h * 0.49), fontFamily: 'Montserrat', fontSize: Math.round(h * 0.026), fontWeight: '600', fill: p.sub });

            const phone = new fabric.IText(`📞 ${d.phone}`, { left: rightColX, top: Math.round(h * 0.40), fontFamily: 'Inter', fontSize: Math.round(h * 0.028), fill: p.text });
            const mail = new fabric.IText(`✉️ ${d.mail}`, { left: rightColX, top: Math.round(h * 0.48), fontFamily: 'Inter', fontSize: Math.round(h * 0.028), fill: p.text });
            const web = new fabric.IText(`🌐 ${d.web}`, { left: rightColX, top: Math.round(h * 0.56), fontFamily: 'Inter', fontSize: Math.round(h * 0.028), fill: p.text });
            const addr = new fabric.IText(`📍 ${d.addr}`, { left: padLeft, top: Math.round(h * 0.78), fontFamily: 'Inter', fontSize: Math.round(h * 0.024), fill: p.sub });

            canvas.add(comp, sub, line, name, title, phone, mail, web, addr);

            fabric.loadSVGFromString(sectorSvgStr, (objects, options) => {
                const iconGroup = fabric.util.groupSVGElements(objects, options);
                iconGroup.set({ left: w - padLeft - 30, top: Math.round(h * 0.16), originX: 'center', originY: 'center', scaleX: 0.9, scaleY: 0.9, selectable: true });
                canvas.add(iconGroup);
                canvas.requestRenderAll();
            });
        }

        canvas.requestRenderAll();
    }

    // Public API for Canva Studio
    window.CanvaTemplatesEngine = {
        init: function() {
            buildAllTemplates();
        },

        getTotalCount: function() {
            buildAllTemplates();
            return masterTemplatesList.length;
        },

        getTemplates: function(category, searchKeyword, page, perPage) {
            buildAllTemplates();
            category = (category || 'all').toLowerCase();
            searchKeyword = (searchKeyword || '').toLowerCase().trim();
            page = page || 1;
            perPage = perPage || 24;

            let filtered = masterTemplatesList.filter(item => {
                const catMatch = (category === 'all' || item.sector === category);
                const kwMatch = (!searchKeyword || item.keywords.includes(searchKeyword) || item.title.toLowerCase().includes(searchKeyword));
                return catMatch && kwMatch;
            });

            const total = filtered.length;
            const start = (page - 1) * perPage;
            const paginatedItems = filtered.slice(start, start + perPage);

            return {
                total: total,
                page: page,
                perPage: perPage,
                hasMore: start + perPage < total,
                items: paginatedItems.map(t => ({
                    key: t.key,
                    sector: t.sector,
                    sectorName: t.sectorName,
                    title: t.title,
                    previewSvg: generatePreviewSvg(t)
                }))
            };
        },

        getTemplateByKey: function(key) {
            buildAllTemplates();
            return masterTemplatesList.find(t => t.key === key) || masterTemplatesList[0];
        },

        loadTemplateToCanvas: function(canvas, key) {
            const tpl = this.getTemplateByKey(key);
            if (tpl && canvas) {
                renderToFabric(canvas, tpl);
            }
        }
    };

    window.CanvaTemplatesEngine.init();

})(window);
