/**
 * TAMBASKI.COM.TR - Vektörel İSG, Güvenlik & Dekota Uyarı Levhaları Tasarım Stüdyosu (Sign & Safety Studio)
 * 65+ Vektörel İSG/Trafik Çizimi, Canlı Ebat/Özel Boyut Tuvali, Zemin Rengi & İkaz Çerçevesi Motoru
 */

window.SignStudio = (function() {
    let canvas = null;
    let currentWidthCm = 35;
    let currentHeightCm = 50;
    let currentOrientation = 'vertical'; // 'vertical' | 'horizontal'
    let currentZeminColor = '#ffffff';
    let currentBorderObj = null;

    let borderConfig = {
        enabled: false,
        color: '#ffcc00',
        width: 10,
        style: 'solid', // 'solid' | 'dashed'
        type: 'yellow_black'
    };

    // =========================================================================
    // 🎨 GENİŞLETİLMİŞ VEKTÖREL ÇİZİM & İSG PİKTOGRAM KÜTÜPHANESİ (65+ İKON)
    // =========================================================================
    const vectorLibrary = [
        // ---------------------------------------------------------------------
        // 1. KIRMIZI YASAKLAR & TRAFİK (PROHIBITION & NO PARKING)
        // ---------------------------------------------------------------------
        {
            id: 'no_parking',
            name: 'Park Yapılmaz (P)',
            category: 'prohibition',
            tags: 'park yapilmaz yasak otopark arac durma no parking garaj giris',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#0055aa" stroke="#dd2222" stroke-width="8"/><line x1="20" y1="20" x2="80" y2="80" stroke="#dd2222" stroke-width="8"/><text x="50" y="68" font-size="52" font-family="Arial Black, sans-serif" font-weight="bold" text-anchor="middle" fill="#ffffff">P</text></svg>`
        },
        {
            id: 'no_stopping',
            name: 'Duraklama & Park Yasak',
            category: 'prohibition',
            tags: 'duraklama durmak park yasak carpi x trafik',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#0055aa" stroke="#dd2222" stroke-width="8"/><line x1="20" y1="20" x2="80" y2="80" stroke="#dd2222" stroke-width="8"/><line x1="80" y1="20" x2="20" y2="80" stroke="#dd2222" stroke-width="8"/></svg>`
        },
        {
            id: 'no_smoking',
            name: 'Sigara İçilmez',
            category: 'prohibition',
            tags: 'sigara icilmez yasak tutun duman ates no smoking',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#ffffff" stroke="#dd2222" stroke-width="8"/><line x1="20" y1="20" x2="80" y2="80" stroke="#dd2222" stroke-width="8"/><rect x="25" y="46" width="38" height="8" rx="1" fill="#333"/><rect x="65" y="46" width="10" height="8" rx="1" fill="#e67e22"/><path d="M77 42 C81 38 77 34 81 30 M83 45 C87 41 83 37 87 33" stroke="#888" stroke-width="2.5" fill="none"/></svg>`
        },
        {
            id: 'no_flame',
            name: 'Açık Ateşle Yaklaşma',
            category: 'prohibition',
            tags: 'ates alev yaklasilmaz kibrit cakmak patlama yangin',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#ffffff" stroke="#dd2222" stroke-width="8"/><line x1="20" y1="20" x2="80" y2="80" stroke="#dd2222" stroke-width="8"/><path d="M50 25 C50 25 62 42 62 55 C62 65 55 72 50 72 C45 72 38 65 38 55 C38 45 46 38 46 38 C46 38 44 48 50 52 C50 45 50 25 50 25 Z" fill="#e67e22"/></svg>`
        },
        {
            id: 'no_entry',
            name: 'Yetkisiz Giremez',
            category: 'prohibition',
            tags: 'girilmez yasak yetkisiz yabanci personel ozel giris',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#ffffff" stroke="#dd2222" stroke-width="8"/><line x1="20" y1="20" x2="80" y2="80" stroke="#dd2222" stroke-width="8"/><circle cx="50" cy="35" r="7" fill="#111"/><path d="M38 72 L38 55 C38 48 62 48 62 55 L62 72 Z M44 55 L44 72 M56 55 L56 72" fill="#111"/></svg>`
        },
        {
            id: 'no_entry_sign',
            name: 'Giriş Yasaktır (Trafik)',
            category: 'prohibition',
            tags: 'giris yasaktir tek yon kirmizi serit trafik',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#dd2222"/><rect x="20" y="43" width="60" height="14" rx="3" fill="#ffffff"/></svg>`
        },
        {
            id: 'no_phone',
            name: 'Cep Telefonu Yasak',
            category: 'prohibition',
            tags: 'telefon cep arama konusma cihaz yasak akilli telefon',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#ffffff" stroke="#dd2222" stroke-width="8"/><line x1="20" y1="20" x2="80" y2="80" stroke="#dd2222" stroke-width="8"/><rect x="36" y="26" width="28" height="48" rx="4" fill="none" stroke="#222" stroke-width="4"/><circle cx="50" cy="68" r="2.5" fill="#222"/><rect x="40" y="32" width="20" height="30" fill="#94a3b8"/></svg>`
        },
        {
            id: 'no_pedestrian',
            name: 'Yaya Giremez',
            category: 'prohibition',
            tags: 'yaya giremez yurumez insan yol saha santiyede',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#ffffff" stroke="#dd2222" stroke-width="8"/><line x1="20" y1="20" x2="80" y2="80" stroke="#dd2222" stroke-width="8"/><circle cx="50" cy="30" r="6" fill="#111"/><path d="M46 38 L54 38 L58 54 L52 54 L50 72 L44 72 L42 54 L38 54 Z" fill="#111"/></svg>`
        },
        {
            id: 'no_touch',
            name: 'Dokunmak Yasaktır',
            category: 'prohibition',
            tags: 'dokunma dokunulmaz el temas etme tehlikeli cihaz',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#ffffff" stroke="#dd2222" stroke-width="8"/><line x1="20" y1="20" x2="80" y2="80" stroke="#dd2222" stroke-width="8"/><path d="M46 26 L46 45 M52 30 L52 45 M58 35 L58 46 M40 34 L40 48 M36 46 L36 54 L44 68 L56 68 L64 56 L64 45" stroke="#111" stroke-width="4" stroke-linecap="round" fill="none"/></svg>`
        },
        {
            id: 'no_trash',
            name: 'Çöp Atmak Yasaktır',
            category: 'prohibition',
            tags: 'cop atilmaz temizlik cevre yasak kirletme',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#ffffff" stroke="#dd2222" stroke-width="8"/><line x1="20" y1="20" x2="80" y2="80" stroke="#dd2222" stroke-width="8"/><circle cx="42" cy="32" r="5" fill="#111"/><path d="M38 40 L46 40 L54 50 L48 54 L44 70 L38 70 Z" fill="#111"/><rect x="60" y="48" width="16" height="24" rx="2" fill="#555"/><circle cx="54" cy="42" r="2.5" fill="#111"/></svg>`
        },
        {
            id: 'no_horn',
            name: 'Korna Çalınmaz',
            category: 'prohibition',
            tags: 'korna gurultu calinmaz sessizlik ses klakson',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#ffffff" stroke="#dd2222" stroke-width="8"/><line x1="20" y1="20" x2="80" y2="80" stroke="#dd2222" stroke-width="8"/><path d="M30 45 L42 45 L54 32 L54 68 L42 55 L30 55 Z" fill="#111"/><path d="M62 40 C66 45 66 55 62 60" stroke="#111" stroke-width="4" fill="none"/></svg>`
        },
        {
            id: 'no_food',
            name: 'Yiyecek & İçecek Yasak',
            category: 'prohibition',
            tags: 'yiyecek icecek yemek hamburger bardak yasak',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#ffffff" stroke="#dd2222" stroke-width="8"/><line x1="20" y1="20" x2="80" y2="80" stroke="#dd2222" stroke-width="8"/><path d="M30 44 C30 36 50 36 50 44 Z M30 48 L50 48 M30 52 L50 52 C50 58 30 58 30 52 Z" fill="#111"/><path d="M56 34 L70 34 L66 64 L60 64 Z M63 26 L63 34" stroke="#111" stroke-width="3" fill="none"/></svg>`
        },
        {
            id: 'no_camera',
            name: 'Fotoğraf Çekilmez',
            category: 'prohibition',
            tags: 'fotograf kamera cekim video flash yasak gizlilik',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#ffffff" stroke="#dd2222" stroke-width="8"/><line x1="20" y1="20" x2="80" y2="80" stroke="#dd2222" stroke-width="8"/><rect x="28" y="38" width="44" height="28" rx="4" fill="none" stroke="#111" stroke-width="4"/><polygon points="40,38 44,32 56,32 60,38" fill="#111"/><circle cx="50" cy="52" r="8" fill="none" stroke="#111" stroke-width="3.5"/></svg>`
        },
        {
            id: 'no_pet',
            name: 'Evcil Hayvan Giremez',
            category: 'prohibition',
            tags: 'kopek kedi evcil hayvan giremez yasak',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#ffffff" stroke="#dd2222" stroke-width="8"/><line x1="20" y1="20" x2="80" y2="80" stroke="#dd2222" stroke-width="8"/><path d="M35 52 C35 44 42 40 48 44 L58 40 L60 48 C64 54 62 62 52 64 L50 72 L42 72 L40 64 C35 62 35 56 35 52 Z" fill="#111"/></svg>`
        },
        {
            id: 'no_water',
            name: 'İçilmez Su',
            category: 'prohibition',
            tags: 'icilmez su kuyu sanayi aritilmamis musluk',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#ffffff" stroke="#dd2222" stroke-width="8"/><line x1="20" y1="20" x2="80" y2="80" stroke="#dd2222" stroke-width="8"/><path d="M35 35 L50 35 L50 48 L42 48" stroke="#111" stroke-width="4" fill="none"/><path d="M42 54 L42 66 L58 66 L58 54" stroke="#111" stroke-width="4" fill="none"/></svg>`
        },
        {
            id: 'no_run',
            name: 'Koşmak Yasaktır',
            category: 'prohibition',
            tags: 'kosmak kosma yasak yavas yuru kayma tehlike',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#ffffff" stroke="#dd2222" stroke-width="8"/><line x1="20" y1="20" x2="80" y2="80" stroke="#dd2222" stroke-width="8"/><circle cx="56" cy="30" r="5" fill="#111"/><path d="M62 42 L52 46 L42 38 M52 46 L44 60 L32 62 M48 56 L58 66 L72 66" stroke="#111" stroke-width="4" fill="none" stroke-linecap="round"/></svg>`
        },

        // ---------------------------------------------------------------------
        // 2. SARI TEHLİKELER & UYARILAR (WARNING / HAZARD)
        // ---------------------------------------------------------------------
        {
            id: 'warn_general',
            name: 'Genel Tehlike (Üçgen)',
            category: 'warning',
            tags: 'dikkat tehlike ikaz ucgen genel onemli',
            color: '#f59e0b',
            svg: `<svg viewBox="0 0 100 100"><polygon points="50,12 90,82 10,82" fill="#ffcc00" stroke="#111" stroke-width="6" stroke-linejoin="round"/><rect x="46" y="34" width="8" height="24" rx="4" fill="#111"/><circle cx="50" cy="68" r="4.5" fill="#111"/></svg>`
        },
        {
            id: 'warn_voltage',
            name: 'Yüksek Gerilim / Elektrik',
            category: 'warning',
            tags: 'elektrik gerilim carpilma pano trafo enerji simsek olum tehlikesi',
            color: '#f59e0b',
            svg: `<svg viewBox="0 0 100 100"><polygon points="50,12 90,82 10,82" fill="#ffcc00" stroke="#111" stroke-width="6" stroke-linejoin="round"/><path d="M54 30 L38 52 L48 52 L42 72 L62 48 L50 48 Z" fill="#111"/></svg>`
        },
        {
            id: 'warn_slip',
            name: 'Kaygan Zemin',
            category: 'warning',
            tags: 'kaygan zemin islak dusme kayma temizlik dikkat',
            color: '#f59e0b',
            svg: `<svg viewBox="0 0 100 100"><polygon points="50,12 90,82 10,82" fill="#ffcc00" stroke="#111" stroke-width="6" stroke-linejoin="round"/><circle cx="56" cy="34" r="5" fill="#111"/><path d="M52 42 L42 56 L30 52 M42 56 L50 68 L64 68 M40 60 L32 72 L22 72 M25 78 C35 74 65 74 75 78" stroke="#111" stroke-width="3.5" fill="none" stroke-linecap="round"/></svg>`
        },
        {
            id: 'warn_forklift',
            name: 'Forklift / İş Makinesi',
            category: 'warning',
            tags: 'forklift is makinesi arac depo yuk tasima saha',
            color: '#f59e0b',
            svg: `<svg viewBox="0 0 100 100"><polygon points="50,12 90,82 10,82" fill="#ffcc00" stroke="#111" stroke-width="6" stroke-linejoin="round"/><circle cx="48" cy="40" r="4" fill="#111"/><path d="M32 66 L62 66 L62 45 L50 45 L45 55 L32 55 Z M62 45 L72 45 L72 66 L80 66" stroke="#111" stroke-width="3" fill="none"/><circle cx="38" cy="68" r="4" fill="#111"/><circle cx="58" cy="68" r="4" fill="#111"/></svg>`
        },
        {
            id: 'warn_dog',
            name: 'Dikkat Köpek Var',
            category: 'warning',
            tags: 'kopek dikkat bekci hayvan tehlike dis',
            color: '#f59e0b',
            svg: `<svg viewBox="0 0 100 100"><polygon points="50,12 90,82 10,82" fill="#ffcc00" stroke="#111" stroke-width="6" stroke-linejoin="round"/><path d="M36 50 C36 40 46 36 54 40 L64 36 L66 46 C70 52 68 62 58 64 L56 72 L46 72 L44 62 C38 60 36 55 36 50 Z" fill="#111"/><circle cx="48" cy="48" r="2" fill="#ffcc00"/></svg>`
        },
        {
            id: 'warn_toxic',
            name: 'Zehirli / Toksik Madde',
            category: 'warning',
            tags: 'zehirli toksik kuru kafa olum kimyasal tehlike asit',
            color: '#f59e0b',
            svg: `<svg viewBox="0 0 100 100"><polygon points="50,12 90,82 10,82" fill="#ffcc00" stroke="#111" stroke-width="6" stroke-linejoin="round"/><circle cx="50" cy="45" r="12" fill="#111"/><rect x="44" y="55" width="12" height="8" fill="#111"/><circle cx="46" cy="44" r="2.5" fill="#ffcc00"/><circle cx="54" cy="44" r="2.5" fill="#ffcc00"/><line x1="34" y1="64" x2="66" y2="40" stroke="#111" stroke-width="3"/><line x1="34" y1="40" x2="66" y2="64" stroke="#111" stroke-width="3"/></svg>`
        },
        {
            id: 'warn_falling',
            name: 'Düşen Cisim Tehlikesi',
            category: 'warning',
            tags: 'dusen cisim tas malzeme bas yukari baret santiye',
            color: '#f59e0b',
            svg: `<svg viewBox="0 0 100 100"><polygon points="50,12 90,82 10,82" fill="#ffcc00" stroke="#111" stroke-width="6" stroke-linejoin="round"/><circle cx="50" cy="65" r="6" fill="#111"/><path d="M42 62 C42 56 58 56 58 62 Z" fill="#111"/><rect x="42" y="32" width="6" height="6" transform="rotate(45 45 35)" fill="#111"/><rect x="54" y="38" width="8" height="8" transform="rotate(25 58 42)" fill="#111"/></svg>`
        },
        {
            id: 'warn_hot',
            name: 'Sıcak Yüzey / Yanma',
            category: 'warning',
            tags: 'sicak yuzey yanma kazan buhar dokunma yuksek isi',
            color: '#f59e0b',
            svg: `<svg viewBox="0 0 100 100"><polygon points="50,12 90,82 10,82" fill="#ffcc00" stroke="#111" stroke-width="6" stroke-linejoin="round"/><line x1="30" y1="72" x2="70" y2="72" stroke="#111" stroke-width="5"/><path d="M38 64 C36 56 42 52 40 44 M50 64 C48 56 54 52 52 44 M62 64 C60 56 66 52 64 44" stroke="#111" stroke-width="3.5" fill="none" stroke-linecap="round"/></svg>`
        },
        {
            id: 'warn_corrosive',
            name: 'Aşındırıcı / Asit',
            category: 'warning',
            tags: 'asindirici asit kimyasal tahris korozif el yuzey',
            color: '#f59e0b',
            svg: `<svg viewBox="0 0 100 100"><polygon points="50,12 90,82 10,82" fill="#ffcc00" stroke="#111" stroke-width="6" stroke-linejoin="round"/><rect x="30" y="44" width="16" height="6" transform="rotate(-30 38 47)" fill="#111"/><rect x="54" y="36" width="16" height="6" transform="rotate(30 62 39)" fill="#111"/><line x1="30" y1="68" x2="70" y2="68" stroke="#111" stroke-width="4"/><path d="M42 56 L42 62 M58 56 L58 62" stroke="#111" stroke-width="3"/></svg>`
        },
        {
            id: 'warn_explosive',
            name: 'Patlayıcı Madde',
            category: 'warning',
            tags: 'patlayici bomba patlama infilak dinamit gaz barut',
            color: '#f59e0b',
            svg: `<svg viewBox="0 0 100 100"><polygon points="50,12 90,82 10,82" fill="#ffcc00" stroke="#111" stroke-width="6" stroke-linejoin="round"/><circle cx="50" cy="55" r="10" fill="#111"/><line x1="50" y1="36" x2="50" y2="42" stroke="#111" stroke-width="3.5"/><line x1="36" y1="44" x2="42" y2="48" stroke="#111" stroke-width="3.5"/><line x1="64" y1="44" x2="58" y2="48" stroke="#111" stroke-width="3.5"/></svg>`
        },
        {
            id: 'warn_flammable',
            name: 'Yanıcı Madde (Alev)',
            category: 'warning',
            tags: 'yanici parlayici tiner yakit benzin gaz alev',
            color: '#f59e0b',
            svg: `<svg viewBox="0 0 100 100"><polygon points="50,12 90,82 10,82" fill="#ffcc00" stroke="#111" stroke-width="6" stroke-linejoin="round"/><path d="M50 32 C50 32 58 45 58 54 C58 62 54 68 50 68 C46 68 42 62 42 54 C42 46 48 40 48 40 C48 40 46 48 50 50 C50 44 50 32 50 32 Z" fill="#111"/></svg>`
        },
        {
            id: 'warn_biohazard',
            name: 'Biyolojik Tehlike',
            category: 'warning',
            tags: 'biyolojik biohazard virus mikrop tibbi atik saglik',
            color: '#f59e0b',
            svg: `<svg viewBox="0 0 100 100"><polygon points="50,12 90,82 10,82" fill="#ffcc00" stroke="#111" stroke-width="6" stroke-linejoin="round"/><circle cx="50" cy="46" r="6" fill="none" stroke="#111" stroke-width="3"/><circle cx="43" cy="58" r="6" fill="none" stroke="#111" stroke-width="3"/><circle cx="57" cy="58" r="6" fill="none" stroke="#111" stroke-width="3"/><circle cx="50" cy="54" r="3" fill="#111"/></svg>`
        },
        {
            id: 'warn_suspended_load',
            name: 'Asılı Yük / Vinç Tehlikesi',
            category: 'warning',
            tags: 'asili yuk vinc kanca altinda durma santiye fabrika',
            color: '#f59e0b',
            svg: `<svg viewBox="0 0 100 100"><polygon points="50,12 90,82 10,82" fill="#ffcc00" stroke="#111" stroke-width="6" stroke-linejoin="round"/><line x1="50" y1="28" x2="50" y2="42" stroke="#111" stroke-width="3"/><path d="M50 42 C45 42 45 48 50 48 C54 48 54 44 50 44" stroke="#111" stroke-width="3" fill="none"/><rect x="36" y="50" width="28" height="16" rx="2" fill="#111"/></svg>`
        },
        {
            id: 'warn_crush',
            name: 'Sıkışma / Dişli Tehlikesi',
            category: 'warning',
            tags: 'sikisma disli pres merdane el ezilme makine',
            color: '#f59e0b',
            svg: `<svg viewBox="0 0 100 100"><polygon points="50,12 90,82 10,82" fill="#ffcc00" stroke="#111" stroke-width="6" stroke-linejoin="round"/><circle cx="42" cy="52" r="9" fill="none" stroke="#111" stroke-width="4"/><circle cx="58" cy="52" r="9" fill="none" stroke="#111" stroke-width="4"/><path d="M48 42 L52 62" stroke="#111" stroke-width="3"/></svg>`
        },

        // ---------------------------------------------------------------------
        // 3. İSG & KİŞİSEL KORUYUCU DONANIM (PPE - MAVİ / BEYAZ)
        // ---------------------------------------------------------------------
        {
            id: 'ppe_helmet',
            name: 'Baret Takınız',
            category: 'ppe',
            tags: 'baret kask guvenlik isg insaat santiye bas koruma',
            color: '#0055aa',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#0055aa"/><path d="M50 24 C34 24 24 36 24 50 L76 50 C76 36 66 24 50 24 Z" fill="#ffffff"/><rect x="20" y="50" width="60" height="8" rx="2" fill="#ffffff"/><rect x="46" y="22" width="8" height="28" fill="#0055aa"/></svg>`
        },
        {
            id: 'ppe_glasses',
            name: 'Koruyucu Gözlük Tak',
            category: 'ppe',
            tags: 'gozluk koruyucu goz isg kaynak taslama capak',
            color: '#0055aa',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#0055aa"/><path d="M22 42 C22 34 36 34 44 42 C47 45 53 45 56 42 C64 34 78 34 78 42 C78 58 66 66 54 62 C51 60 49 60 46 62 C34 66 22 58 22 42 Z" fill="#ffffff"/><circle cx="35" cy="48" r="7" fill="#0055aa"/><circle cx="65" cy="48" r="7" fill="#0055aa"/></svg>`
        },
        {
            id: 'ppe_shield',
            name: 'Yüz Siperliği Tak',
            category: 'ppe',
            tags: 'siperlik vizor yuz koruma kaynak taslama kivilcim',
            color: '#0055aa',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#0055aa"/><path d="M50 20 C34 20 28 32 28 45 L28 65 C28 78 40 82 50 82 C60 82 72 78 72 65 L72 45 C72 32 66 20 50 20 Z" fill="#ffffff"/><path d="M34 38 L66 38 L66 46 L34 46 Z" fill="#0055aa"/><path d="M36 52 C36 70 42 75 50 75 C58 75 64 70 64 52 Z" fill="#0055aa" opacity="0.3"/></svg>`
        },
        {
            id: 'ppe_mask',
            name: 'Maske Takınız',
            category: 'ppe',
            tags: 'maske respirator toz kimyasal n95 saglik solunum',
            color: '#0055aa',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#0055aa"/><path d="M26 44 C26 36 50 32 50 32 C50 32 74 36 74 44 C74 58 64 70 50 72 C36 70 26 58 26 44 Z" fill="#ffffff"/><circle cx="50" cy="52" r="5" fill="#0055aa"/></svg>`
        },
        {
            id: 'ppe_ear',
            name: 'Kulaklık Takınız',
            category: 'ppe',
            tags: 'kulaklik ses gurultu pres kompresor atolye isitme',
            color: '#0055aa',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#0055aa"/><path d="M50 22 C34 22 26 34 26 48 L26 62 M50 22 C66 22 74 34 74 48 L74 62" stroke="#ffffff" stroke-width="6" fill="none" stroke-linecap="round"/><rect x="20" y="46" width="14" height="24" rx="4" fill="#ffffff"/><rect x="66" y="46" width="14" height="24" rx="4" fill="#ffffff"/></svg>`
        },
        {
            id: 'ppe_gloves',
            name: 'İş Eldiveni Giyiniz',
            category: 'ppe',
            tags: 'eldiven el isi kesilme kimyasal koruma isi mekanik',
            color: '#0055aa',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#0055aa"/><path d="M34 40 L34 28 C34 25 38 25 38 28 L38 38 M40 38 L40 22 C40 19 44 19 44 22 L44 38 M46 38 L46 25 C46 22 50 22 50 25 L50 42 C50 48 54 44 58 44 C62 44 62 48 56 52 L50 62 L50 74 L32 74 L32 56 C28 52 34 46 34 40 Z" fill="#ffffff"/></svg>`
        },
        {
            id: 'ppe_boots',
            name: 'İş Ayakkabısı Giy',
            category: 'ppe',
            tags: 'ayakkabi celik burun bot cizme ayak koruma taban',
            color: '#0055aa',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#0055aa"/><path d="M34 28 L48 28 L48 50 L72 55 C78 57 80 62 80 68 L80 74 L24 74 L24 64 C24 52 30 42 34 28 Z" fill="#ffffff"/></svg>`
        },
        {
            id: 'ppe_vest',
            name: 'Reflektörlü Yelek',
            category: 'ppe',
            tags: 'yelek reflektor ikaz sari santiye yol fosforlu gorunur',
            color: '#0055aa',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#0055aa"/><path d="M32 25 L42 25 L46 45 L54 45 L58 25 L68 25 L76 75 L24 75 Z" fill="#ffffff"/><rect x="28" y="50" width="44" height="6" fill="#0055aa"/><rect x="26" y="62" width="48" height="6" fill="#0055aa"/></svg>`
        },
        {
            id: 'ppe_harness',
            name: 'Emniyet Kemeri Tak',
            category: 'ppe',
            tags: 'emniyet kemeri yuksekte calisma iskele halat parasut',
            color: '#0055aa',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#0055aa"/><circle cx="50" cy="26" r="6" fill="#ffffff"/><path d="M40 36 L60 36 L58 66 L50 78 L42 66 Z" stroke="#ffffff" stroke-width="4" fill="none"/><line x1="40" y1="46" x2="60" y2="46" stroke="#ffffff" stroke-width="4"/><line x1="42" y1="58" x2="58" y2="58" stroke="#ffffff" stroke-width="4"/></svg>`
        },
        {
            id: 'ppe_sanitize',
            name: 'Elleri Dezenfekte Et',
            category: 'ppe',
            tags: 'el yikama dezenfektan hijyen temizlik saglik sabun',
            color: '#0055aa',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#0055aa"/><path d="M50 25 C50 25 38 42 38 52 C38 60 44 66 50 66 C56 66 62 60 62 52 C62 42 50 25 50 25 Z" fill="#ffffff"/><circle cx="50" cy="74" r="3" fill="#ffffff"/></svg>`
        },
        {
            id: 'ppe_overall',
            name: 'Koruyucu Tulum Giy',
            category: 'ppe',
            tags: 'tulum onluk elbise kimyasal toz koruyucu giysi',
            color: '#0055aa',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#0055aa"/><circle cx="50" cy="24" r="5" fill="#ffffff"/><path d="M40 32 L60 32 L66 52 L58 54 L56 78 L44 78 L42 54 L34 52 Z" fill="#ffffff"/></svg>`
        },

        // ---------------------------------------------------------------------
        // 4. YEŞİL ACİL DURUM & İLK YARDIM (EMERGENCY)
        // ---------------------------------------------------------------------
        {
            id: 'exit_right',
            name: 'Acil Çıkış (Sağa)',
            category: 'emergency',
            tags: 'acil cikis exit kapi kacan adam tahliye sag',
            color: '#008844',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#008844"/><circle cx="40" cy="35" r="6" fill="#fff"/><path d="M36 44 L46 44 L52 55 L60 55 M42 48 L35 60 L28 60 M46 55 L42 70 L34 78 M48 65 L56 75" stroke="#fff" stroke-width="4" fill="none" stroke-linecap="round"/><polygon points="70,42 82,50 70,58 70,53 62,53 62,47 70,47" fill="#fff"/><rect x="20" y="24" width="6" height="52" fill="#fff"/></svg>`
        },
        {
            id: 'exit_left',
            name: 'Acil Çıkış (Sola)',
            category: 'emergency',
            tags: 'acil cikis exit kapi kacan adam tahliye sol',
            color: '#008844',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#008844"/><circle cx="60" cy="35" r="6" fill="#fff"/><path d="M64 44 L54 44 L48 55 L40 55 M58 48 L65 60 L72 60 M54 55 L58 70 L66 78 M52 65 L44 75" stroke="#fff" stroke-width="4" fill="none" stroke-linecap="round"/><polygon points="30,42 18,50 30,58 30,53 38,53 38,47 30,47" fill="#fff"/><rect x="74" y="24" width="6" height="52" fill="#fff"/></svg>`
        },
        {
            id: 'first_aid',
            name: 'İlk Yardım / Revir',
            category: 'emergency',
            tags: 'ilk yardim hac revir saglik doktor kaza acil mudahale',
            color: '#008844',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#008844"/><rect x="42" y="28" width="16" height="44" rx="2" fill="#ffffff"/><rect x="28" y="42" width="44" height="16" rx="2" fill="#ffffff"/></svg>`
        },
        {
            id: 'assembly_point',
            name: 'Acil Toplanma Alanı',
            category: 'emergency',
            tags: 'toplanma alani acil durum deprem yangin tahliye toplanma',
            color: '#008844',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#008844"/><circle cx="50" cy="40" r="5" fill="#fff"/><path d="M45 48 L55 48 L53 68 L47 68 Z" fill="#fff"/><polygon points="25,25 35,25 30,35" fill="#fff"/><polygon points="75,25 65,25 70,35" fill="#fff"/><polygon points="25,75 35,75 30,65" fill="#fff"/><polygon points="75,75 65,75 70,65" fill="#fff"/></svg>`
        },
        {
            id: 'eye_wash',
            name: 'Acil Göz Duşu',
            category: 'emergency',
            tags: 'goz dusu kimyasal su fıskiye acil yikama',
            color: '#008844',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#008844"/><circle cx="50" cy="44" r="8" fill="#fff"/><path d="M30 44 C35 34 65 34 70 44 C65 54 35 54 30 44 Z" fill="none" stroke="#fff" stroke-width="4"/><path d="M40 68 C40 56 46 54 48 54 M60 68 C60 56 54 54 52 54" stroke="#fff" stroke-width="3" fill="none"/></svg>`
        },

        // ---------------------------------------------------------------------
        // 5. KIRMIZI YANGIN GÜVENLİĞİ (FIRE SAFETY)
        // ---------------------------------------------------------------------
        {
            id: 'fire_extinguisher',
            name: 'Yangın Söndürme Tüpü',
            category: 'fire',
            tags: 'yangin tupu sondurme itfaiye alev basinc kirmizi',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#dd2222"/><rect x="42" y="38" width="16" height="40" rx="4" fill="#ffffff"/><rect x="46" y="28" width="8" height="10" fill="#ffffff"/><path d="M40 30 L60 30 L55 24 L45 24 Z M54 32 L66 40 L66 60" stroke="#fff" stroke-width="3" fill="none"/></svg>`
        },
        {
            id: 'fire_hose',
            name: 'Yangın Hortumu / Dolabı',
            category: 'fire',
            tags: 'yangin dolabi hortum su itfaiye vana makara',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#dd2222"/><circle cx="50" cy="50" r="22" fill="none" stroke="#fff" stroke-width="6"/><circle cx="50" cy="50" r="12" fill="none" stroke="#fff" stroke-width="5"/><circle cx="50" cy="50" r="4" fill="#fff"/><rect x="68" y="35" width="12" height="6" fill="#fff"/></svg>`
        },
        {
            id: 'fire_alarm',
            name: 'Yangın İhbar Butonu',
            category: 'fire',
            tags: 'yangin alarm buton kiriniz ihbar zil cami kir',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#dd2222"/><rect x="28" y="28" width="44" height="44" rx="4" fill="#ffffff"/><circle cx="50" cy="50" r="12" fill="#dd2222"/><circle cx="50" cy="50" r="6" fill="#ffffff"/></svg>`
        },
        {
            id: 'fire_ladder',
            name: 'Yangın Merdiveni',
            category: 'fire',
            tags: 'yangin merdiveni tahliye bina disi kirmizi',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#dd2222"/><line x1="36" y1="25" x2="36" y2="75" stroke="#fff" stroke-width="4"/><line x1="64" y1="25" x2="64" y2="75" stroke="#fff" stroke-width="4"/><line x1="36" y1="35" x2="64" y2="35" stroke="#fff" stroke-width="3"/><line x1="36" y1="45" x2="64" y2="45" stroke="#fff" stroke-width="3"/><line x1="36" y1="55" x2="64" y2="55" stroke="#fff" stroke-width="3"/><line x1="36" y1="65" x2="64" y2="65" stroke="#fff" stroke-width="3"/></svg>`
        },

        // ---------------------------------------------------------------------
        // 6. MAVİ BİLGİLENDİRME, TRAFİK & TESİS (INFO / TRAFFIC)
        // ---------------------------------------------------------------------
        {
            id: 'info_camera',
            name: '7/24 Kamera ile İzlenir',
            category: 'info',
            tags: 'kamera cctv guvenlik izleme 724 kayit video guvenlik',
            color: '#0055aa',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#0055aa"/><path d="M25 45 L55 35 L55 60 L25 55 Z M55 42 L72 32 L72 62 L55 52 Z" fill="#ffffff"/><circle cx="34" cy="48" r="4" fill="#0055aa"/><rect x="36" y="58" width="8" height="16" fill="#ffffff"/></svg>`
        },
        {
            id: 'info_parking',
            name: 'Otopark (P)',
            category: 'info',
            tags: 'otopark park p arac garaj park alani',
            color: '#0055aa',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#0055aa"/><text x="50" y="68" font-size="52" font-family="Arial Black, sans-serif" font-weight="bold" text-anchor="middle" fill="#ffffff">P</text></svg>`
        },
        {
            id: 'info_wc_male',
            name: 'Bay WC / Tuvalet',
            category: 'info',
            tags: 'wc tuvalet bay erkek lavabo tesis',
            color: '#0055aa',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#0055aa"/><circle cx="50" cy="30" r="6" fill="#fff"/><path d="M38 42 L62 42 L58 60 L54 60 L54 78 L46 78 L46 60 L42 60 Z" fill="#fff"/></svg>`
        },
        {
            id: 'info_wc_female',
            name: 'Bayan WC / Tuvalet',
            category: 'info',
            tags: 'wc tuvalet bayan kadin lavabo tesis',
            color: '#0055aa',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#0055aa"/><circle cx="50" cy="30" r="6" fill="#fff"/><path d="M42 42 L58 42 L64 64 L54 64 L54 78 L46 78 L46 64 L36 64 Z" fill="#fff"/></svg>`
        },
        {
            id: 'info_disabled',
            name: 'Engelli Erişimi / WC',
            category: 'info',
            tags: 'engelli tekerlekli sandalye rampa wc erisim',
            color: '#0055aa',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#0055aa"/><circle cx="55" cy="28" r="5" fill="#fff"/><path d="M45 40 L58 40 L52 56 L64 56 M52 56 L46 70 L34 70" stroke="#fff" stroke-width="5" fill="none" stroke-linecap="round"/><circle cx="46" cy="58" r="12" fill="none" stroke="#fff" stroke-width="5"/></svg>`
        },
        {
            id: 'info_stop',
            name: 'DUR / STOP Levhası',
            category: 'info',
            tags: 'dur stop kirmizi sekizgen trafik kontrol guvenlik',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100"><polygon points="30,10 70,10 90,30 90,70 70,90 30,90 10,70 10,30" fill="#dd2222" stroke="#fff" stroke-width="3"/><text x="50" y="60" font-size="28" font-family="Arial Black, sans-serif" font-weight="bold" text-anchor="middle" fill="#ffffff">DUR</text></svg>`
        },
        {
            id: 'info_pedestrian_cross',
            name: 'Yaya Geçidi',
            category: 'info',
            tags: 'yaya gecidi yol cizgi trafik dikkat gecis',
            color: '#0055aa',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#0055aa"/><circle cx="50" cy="32" r="6" fill="#fff"/><path d="M44 42 L56 42 L62 58 L56 58 L54 74 L46 74 L44 58 L38 58 Z" fill="#fff"/><line x1="22" y1="78" x2="78" y2="78" stroke="#fff" stroke-width="5" stroke-dasharray="10 5"/></svg>`
        },
        {
            id: 'info_speed_10',
            name: 'Hız Limiti (10 km/h)',
            category: 'info',
            tags: 'hiz limiti 10 kmh yavas arac saha fabrika otopark',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#fff" stroke="#dd2222" stroke-width="8"/><text x="50" y="62" font-size="36" font-family="Impact, Arial Black, sans-serif" font-weight="bold" text-anchor="middle" fill="#111">10</text></svg>`
        },
        {
            id: 'info_speed_20',
            name: 'Hız Limiti (20 km/h)',
            category: 'info',
            tags: 'hiz limiti 20 kmh yavas arac saha santiye tesis',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#fff" stroke="#dd2222" stroke-width="8"/><text x="50" y="62" font-size="36" font-family="Impact, Arial Black, sans-serif" font-weight="bold" text-anchor="middle" fill="#111">20</text></svg>`
        },
        {
            id: 'info_speed_30',
            name: 'Hız Limiti (30 km/h)',
            category: 'info',
            tags: 'hiz limiti 30 kmh cadde yol arac',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="42" fill="#fff" stroke="#dd2222" stroke-width="8"/><text x="50" y="62" font-size="36" font-family="Impact, Arial Black, sans-serif" font-weight="bold" text-anchor="middle" fill="#111">30</text></svg>`
        },
        {
            id: 'info_smoking_area',
            name: 'Sigara İçme Alanı (İzinli)',
            category: 'info',
            tags: 'sigara icme alani serbest yesil izin tutun',
            color: '#008844',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#008844"/><rect x="25" y="48" width="40" height="8" rx="1" fill="#fff"/><rect x="67" y="48" width="10" height="8" rx="1" fill="#facc15"/><path d="M78 44 C82 40 78 36 82 32" stroke="#fff" stroke-width="3" fill="none"/></svg>`
        },
        {
            id: 'info_recycling',
            name: 'Geri Dönüşüm / Sıfır Atık',
            category: 'info',
            tags: 'geri donusum sifir atik cop kutu plastik cam kagit cevre',
            color: '#008844',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#008844"/><path d="M50 25 L60 40 L40 40 Z M65 48 L75 62 L58 58 Z M35 48 L42 58 L25 62 Z" fill="#fff"/><path d="M48 38 L65 48 M62 58 L45 68 M35 58 L52 38" stroke="#fff" stroke-width="4" fill="none"/></svg>`
        },
        {
            id: 'info_wifi',
            name: 'Ücretsiz Wi-Fi Alanı',
            category: 'info',
            tags: 'wifi internet kablosuz ag iletisim baglanti',
            color: '#0055aa',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#0055aa"/><circle cx="50" cy="70" r="5" fill="#fff"/><path d="M36 58 C44 50 56 50 64 58 M26 46 C40 32 60 32 74 46 M16 34 C36 14 64 14 84 34" stroke="#fff" stroke-width="5" fill="none" stroke-linecap="round"/></svg>`
        }
    ];

    // =========================================================================
    // 🏷️ HAZIR LEVHA BAŞLIK ŞABLONLARI
    // =========================================================================
    const headerPresets = [
        { title: 'DİKKAT', bg: '#f59e0b', text: '#000000', icon: 'warn_general' },
        { title: 'TEHLİKE', bg: '#dd2222', text: '#ffffff', icon: 'warn_general' },
        { title: 'UYARI', bg: '#f59e0b', text: '#000000', icon: 'warn_general' },
        { title: 'YASAKTIR', bg: '#dd2222', text: '#ffffff', icon: 'no_entry' },
        { title: 'GÜVENLİK', bg: '#0055aa', text: '#ffffff', icon: 'ppe_helmet' },
        { title: 'ACİL DURUM', bg: '#008844', text: '#ffffff', icon: 'exit_right' },
        { title: 'ÖNEMLİ DUYURU', bg: '#111827', text: '#ffffff', icon: 'info_camera' }
    ];

    // Dahili Yardımcı: Çerçevenin her zaman en arkada kalmasını sağlar
    function ensureBorderAtBack() {
        if (canvas && currentBorderObj) {
            canvas.sendToBack(currentBorderObj);
        }
    }

    return {
        getLibrary: function() { return vectorLibrary; },
        getHeaderPresets: function() { return headerPresets; },
        get canvas() { return canvas; },
        get widthCm() { return currentWidthCm; },
        get heightCm() { return currentHeightCm; },
        get orientation() { return currentOrientation; },
        get borderConfig() { return borderConfig; },

        init: function(canvasElementId, widthCm = 35, heightCm = 50) {
            currentWidthCm = parseFloat(widthCm) || 35;
            currentHeightCm = parseFloat(heightCm) || 50;
            currentOrientation = (currentWidthCm >= currentHeightCm) ? 'horizontal' : 'vertical';

            const canvasEl = document.getElementById(canvasElementId);
            if (!canvasEl) return;

            const maxW = 520;
            const maxH = 680;
            const ratio = currentWidthCm / currentHeightCm;

            let displayW = maxW;
            let displayH = Math.round(maxW / ratio);

            if (displayH > maxH) {
                displayH = maxH;
                displayW = Math.round(maxH * ratio);
            }

            canvasEl.width = displayW;
            canvasEl.height = displayH;

            if (canvas) {
                try { canvas.dispose(); } catch(e) {}
            }

            if (typeof fabric !== 'undefined') {
                canvas = new fabric.Canvas(canvasElementId, {
                    width: displayW,
                    height: displayH,
                    backgroundColor: currentZeminColor || '#ffffff',
                    preserveObjectStacking: true,
                    selection: true
                });

                // Yeni nesne eklendiğinde çerçevenin daima altta kalmasını sağla
                canvas.on('object:added', function(e) {
                    if (currentBorderObj && e.target !== currentBorderObj) {
                        ensureBorderAtBack();
                    }
                });

                canvas.renderAll();
            }

            this.updateDimensionBadge();
        },

        initStudio: function(canvasElementId) {
            this.init(canvasElementId, 35, 50);
        },

        setDimensions: function(widthCm, heightCm) {
            currentWidthCm = parseFloat(widthCm) || 35;
            currentHeightCm = parseFloat(heightCm) || 50;
            currentOrientation = (currentWidthCm >= currentHeightCm) ? 'horizontal' : 'vertical';

            if (canvas) {
                const maxW = 520;
                const maxH = 680;
                const ratio = currentWidthCm / currentHeightCm;

                let displayW = maxW;
                let displayH = Math.round(maxW / ratio);

                if (displayH > maxH) {
                    displayH = maxH;
                    displayW = Math.round(maxH * ratio);
                }

                canvas.setWidth(displayW);
                canvas.setHeight(displayH);

                // Eğer çerçeve varsa yeni tuval boyutuna göre yeniden boyutlandır
                if (currentBorderObj) {
                    const strokeWidth = borderConfig.width || 10;
                    const inset = (borderConfig.inset !== undefined) ? borderConfig.inset : 8;
                    currentBorderObj.set({
                        originX: 'center',
                        originY: 'center',
                        left: displayW / 2,
                        top: displayH / 2,
                        width: Math.max(10, displayW - (inset * 2) - strokeWidth),
                        height: Math.max(10, displayH - (inset * 2) - strokeWidth),
                        strokeUniform: true
                    });
                    ensureBorderAtBack();
                }

                canvas.renderAll();
            }

            this.updateDimensionBadge();
        },

        toggleOrientation: function() {
            const temp = currentWidthCm;
            currentWidthCm = currentHeightCm;
            currentHeightCm = temp;
            
            // Eğer custom box açıksa oradaki inputları da güncelle
            const wInput = document.getElementById('signStudioCustomW');
            const hInput = document.getElementById('signStudioCustomH');
            if (wInput) wInput.value = currentWidthCm;
            if (hInput) hInput.value = currentHeightCm;

            this.setDimensions(currentWidthCm, currentHeightCm);
        },

        updateDimensionBadge: function() {
            const dimBadge = document.getElementById('signStudioDimBadge');
            if (dimBadge) {
                dimBadge.textContent = `${currentWidthCm} x ${currentHeightCm} cm (${currentOrientation === 'horizontal' ? 'Yatay' : 'Dikey'})`;
            }
        },

        // =====================================================================
        // 🎨 ZEMİN & ARKA PLAN RENGİ
        // =====================================================================
        setBackgroundColor: function(colorHex) {
            currentZeminColor = colorHex;
            if (canvas) {
                canvas.backgroundColor = colorHex;
                canvas.renderAll();
            }
            // Zemin renk picker inputunu güncelle
            const picker = document.getElementById('signBgColorPicker');
            if (picker) picker.value = colorHex;
        },

        // =====================================================================
        // 🛡️ İKAZ KENAR ÇERÇEVESİ (KUSURSUZ ORTALAMA & ASLA NESNELERİ BLOKE ETMEZ)
        // =====================================================================
        applyHazardBorder: function(options = {}) {
            if (!canvas) return;

            if (options.color) borderConfig.color = options.color;
            if (options.width) borderConfig.width = parseInt(options.width);
            if (options.inset !== undefined) borderConfig.inset = parseInt(options.inset);
            if (options.style) borderConfig.style = options.style;
            if (options.type) borderConfig.type = options.type;
            borderConfig.enabled = true;

            if (currentBorderObj) {
                canvas.remove(currentBorderObj);
                currentBorderObj = null;
            }

            const strokeWidth = borderConfig.width || 10;
            const strokeColor = borderConfig.color || '#ffcc00';
            const inset = (borderConfig.inset !== undefined) ? borderConfig.inset : 8;
            let strokeDash = null;

            if (borderConfig.style === 'dashed' || borderConfig.type === 'striped') {
                strokeDash = [strokeWidth * 1.5, strokeWidth * 0.8];
            }

            const w = canvas.getWidth();
            const h = canvas.getHeight();

            currentBorderObj = new fabric.Rect({
                originX: 'center',
                originY: 'center',
                left: w / 2,
                top: h / 2,
                width: Math.max(10, w - (inset * 2) - strokeWidth),
                height: Math.max(10, h - (inset * 2) - strokeWidth),
                fill: 'transparent',
                stroke: strokeColor,
                strokeWidth: strokeWidth,
                strokeDashArray: strokeDash,
                strokeUniform: true,
                rx: 4,
                ry: 4,
                selectable: false,       // 🔒 TUVALDE ASLA SEÇİLEMEZ
                evented: false,          // 🖱️ TIKLAMALAR TAMAMEN ALTTAKİ NESNELERE GEÇER
                hasControls: false,
                hasBorders: false,
                lockMovementX: true,
                lockMovementY: true,
                hoverCursor: 'default'
            });

            canvas.add(currentBorderObj);
            ensureBorderAtBack();
            canvas.renderAll();

            // Renk picker inputunu senkronize et
            const borderPicker = document.getElementById('signBorderColorPicker');
            if (borderPicker) borderPicker.value = strokeColor;
        },

        addHazardBorder: function(type = 'yellow_black') {
            let color = '#f59e0b';
            let style = 'solid';
            let width = 10;

            if (type === 'red_white' || type === 'red') {
                color = '#dd2222';
            } else if (type === 'striped') {
                color = '#f59e0b';
                style = 'dashed';
                width = 12;
            } else if (type === 'solid_black' || type === 'black') {
                color = '#111827';
                width = 8;
            } else if (type === 'blue') {
                color = '#0055aa';
            } else if (type === 'green') {
                color = '#008844';
            }

            this.applyHazardBorder({ type, color, style, width });
        },

        setBorderColor: function(colorHex) {
            this.applyHazardBorder({ color: colorHex });
        },

        setBorderWidth: function(widthPx) {
            this.applyHazardBorder({ width: parseInt(widthPx) });
        },

        setBorderInset: function(insetPx) {
            this.applyHazardBorder({ inset: parseInt(insetPx) });
        },

        setBorderStyle: function(style) {
            this.applyHazardBorder({ style: style });
        },

        removeBorder: function() {
            if (canvas && currentBorderObj) {
                canvas.remove(currentBorderObj);
                currentBorderObj = null;
            }
            borderConfig.enabled = false;
            if (canvas) canvas.renderAll();
        },

        // =====================================================================
        // 🗂️ KATEGORİ VE PİKTOGRAM LİSTELEME
        // =====================================================================
        renderCategoryButtons: function(containerId) {
            const container = document.getElementById(containerId);
            if (!container) return;

            const categories = [
                { id: 'all', name: 'Tümü (65+)', icon: 'bi-grid-fill' },
                { id: 'prohibition', name: 'Yasak & Park', icon: 'bi-slash-circle-fill' },
                { id: 'warning', name: 'Tehlike & İkaz', icon: 'bi-exclamation-triangle-fill' },
                { id: 'ppe', name: 'KKD / İSG', icon: 'bi-shield-check' },
                { id: 'emergency', name: 'Acil Çıkış', icon: 'bi-box-arrow-right' },
                { id: 'fire', name: 'Yangın', icon: 'bi-fire' },
                { id: 'info', name: 'Bilgi & Tesis', icon: 'bi-info-circle-fill' }
            ];

            let html = '';
            categories.forEach((cat, idx) => {
                const activeCls = (idx === 0) ? 'btn-primary active' : 'btn-outline-secondary';
                html += `<button type="button" class="btn btn-xs rounded-pill px-2 py-1 sign-cat-btn ${activeCls}" style="font-size: 10.5px;" onclick="SignStudio.onCategoryClick('${cat.id}', this)">
                    <i class="bi ${cat.icon} me-1"></i>${cat.name}
                </button>`;
            });
            container.innerHTML = html;
        },

        onCategoryClick: function(categoryId, btnEl) {
            document.querySelectorAll('.sign-cat-btn').forEach(b => {
                b.classList.remove('btn-primary', 'active');
                b.classList.add('btn-outline-secondary');
            });
            if (btnEl) {
                btnEl.classList.remove('btn-outline-secondary');
                btnEl.classList.add('btn-primary', 'active');
            }
            this.renderVectorPicker('signStudioVectorList', categoryId);
        },

        renderVectorPicker: function(containerId, categoryFilter = 'all', searchTerm = '') {
            const container = document.getElementById(containerId);
            if (!container) return;

            let filtered = vectorLibrary;
            if (categoryFilter && categoryFilter !== 'all') {
                filtered = filtered.filter(item => item.category === categoryFilter);
            }
            if (searchTerm && searchTerm.trim() !== '') {
                const term = searchTerm.toLowerCase().trim();
                filtered = filtered.filter(item => item.name.toLowerCase().includes(term) || item.tags.toLowerCase().includes(term));
            }

            if (filtered.length === 0) {
                container.innerHTML = `<div class="col-12 text-center text-muted py-4 small"><i class="bi bi-search me-1"></i> Aradığınız kriterde piktogram bulunamadı.</div>`;
                return;
            }

            let html = '';
            filtered.forEach(item => {
                html += `
                <div class="col-4 col-sm-3 col-md-4">
                    <div class="card h-100 p-2 text-center border rounded-3 shadow-2xs hover-lift cursor-pointer bg-white" 
                         onclick="SignStudio.addVectorIcon('${item.id}')" 
                         title="${item.name} - Levhaya Ekle"
                         style="transition: all 0.2s ease;">
                        <div class="d-flex align-items-center justify-content-center p-1 mb-1" style="height: 52px;">
                            <div style="width: 44px; height: 44px; color: ${item.color || '#333'};">
                                ${item.svg}
                            </div>
                        </div>
                        <div class="text-truncate fw-bold text-dark" style="font-size: 9.5px;" title="${item.name}">${item.name}</div>
                    </div>
                </div>`;
            });

            container.innerHTML = html;
        },

        renderHeaderPicker: function(containerId) {
            const container = document.getElementById(containerId);
            if (!container) return;

            let html = '';
            headerPresets.forEach((hdr, idx) => {
                html += `
                <div class="card border p-2 rounded-3 shadow-2xs hover-lift cursor-pointer" 
                     onclick="SignStudio.addHeaderBand(${idx})"
                     style="background: #ffffff; transition: all 0.2s ease;">
                    <div class="d-flex align-items-center justify-content-between p-2 rounded-2" style="background: ${hdr.bg}; color: ${hdr.text};">
                        <span class="fw-bolder fs-6 tracking-wide" style="font-family: Impact, Arial Black, sans-serif;">${hdr.title}</span>
                        <span class="badge bg-dark bg-opacity-50 text-white" style="font-size: 10px;"><i class="bi bi-plus-lg me-1"></i>Üste Ekle</span>
                    </div>
                </div>`;
            });

            container.innerHTML = html;
        },

        addVectorIcon: function(iconId) {
            const iconData = vectorLibrary.find(i => i.id === iconId);
            if (!iconData || !canvas) return;

            fabric.loadSVGFromString(iconData.svg, function(objects, options) {
                const svgObj = fabric.util.groupSVGElements(objects, options);
                svgObj.scaleToWidth(canvas.getWidth() * 0.38);
                svgObj.set({
                    left: canvas.getWidth() / 2,
                    top: canvas.getHeight() * 0.45,
                    originX: 'center',
                    originY: 'center',
                    cornerColor: '#2563eb',
                    cornerSize: 8,
                    transparentCorners: false
                });
                canvas.add(svgObj);
                canvas.setActiveObject(svgObj);
                ensureBorderAtBack();
                canvas.renderAll();
            });
        },

        addHeaderBand: function(presetIdx) {
            const preset = headerPresets[presetIdx] || headerPresets[0];
            if (!canvas) return;

            const bandHeight = canvas.getHeight() * 0.20;
            const rect = new fabric.Rect({
                left: 0,
                top: 0,
                width: canvas.getWidth(),
                height: bandHeight,
                fill: preset.bg,
                selectable: true
            });

            const text = new fabric.IText(preset.title, {
                left: canvas.getWidth() / 2,
                top: bandHeight / 2,
                originX: 'center',
                originY: 'center',
                fontFamily: 'Impact, Arial Black, sans-serif',
                fontSize: Math.round(bandHeight * 0.55),
                fontWeight: 'bold',
                fill: preset.text,
                selectable: true
            });

            const group = new fabric.Group([rect, text], {
                left: 0,
                top: 0,
                selectable: true
            });

            canvas.add(group);
            ensureBorderAtBack();
            canvas.renderAll();
        },

        addCustomText: function(textString, fontSize = 24, isBold = true, color = '#111827') {
            if (!canvas) return;
            const text = new fabric.IText(textString || 'ÖZEL UYARI METNİ', {
                left: canvas.getWidth() / 2,
                top: canvas.getHeight() * 0.78,
                originX: 'center',
                originY: 'center',
                fontFamily: 'Arial, Helvetica, sans-serif',
                fontSize: fontSize,
                fontWeight: isBold ? 'bold' : 'normal',
                fill: color,
                textAlign: 'center',
                cornerColor: '#2563eb'
            });
            canvas.add(text);
            canvas.setActiveObject(text);
            ensureBorderAtBack();
            canvas.renderAll();
        },

        loadCompanyLogo: function(imgElement) {
            if (!canvas || !imgElement) return;
            const imgInstance = new fabric.Image(imgElement, {
                left: canvas.getWidth() / 2,
                top: canvas.getHeight() * 0.88,
                originX: 'center',
                originY: 'center',
                cornerColor: '#2563eb'
            });
            imgInstance.scaleToWidth(canvas.getWidth() * 0.35);
            canvas.add(imgInstance);
            canvas.setActiveObject(imgInstance);
            ensureBorderAtBack();
            canvas.renderAll();
        },

        clearCanvas: function() {
            if (canvas) {
                canvas.clear();
                canvas.backgroundColor = currentZeminColor || '#ffffff';
                currentBorderObj = null;
                borderConfig.enabled = false;
                canvas.renderAll();
            }
        },

        exportSvg: function() {
            return canvas ? canvas.toSVG() : '';
        },

        exportPngDataUrl: function() {
            return canvas ? canvas.toDataURL({ format: 'png', quality: 1.0 }) : '';
        }
    };
})();
