/**
 * TAMBASKI.COM.TR - Vektörel İSG, Güvenlik & Dekota Uyarı Levhaları Tasarım Stüdyosu (Sign & Safety Studio)
 * Binlerce Vektörel Çizim, Dinamik Ebat Tuvali, İSG Piktogramları & Canlı Özel Levha Oluşturucu
 */

window.SignStudio = (function() {
    let canvas = null;
    let currentWidthCm = 35;
    let currentHeightCm = 50;
    let currentOrientation = 'vertical'; // 'vertical' | 'horizontal'
    let currentZeminColor = '#ffffff';

    // =========================================================================
    // 🎨 BİNLERCE VEKTÖREL ÇİZİM & İSG PİKTOGRAM VERİTABANI
    // =========================================================================
    const vectorLibrary = [
        // 1. İSG & KİŞİSEL KORUYUCU DONANIM (MAVİ / BEYAZ)
        {
            id: 'ppe_helmet',
            name: 'Baret Takınız',
            category: 'ppe',
            tags: 'baret kask guvenlik isg insaat santiye',
            color: '#0055aa',
            svg: `<svg viewBox="0 0 100 100" fill="currentColor"><path d="M50 15 C30 15 15 30 15 48 C15 52 18 55 22 55 L78 55 C82 55 85 52 85 48 C85 30 70 15 50 15 Z M20 60 C18 60 10 63 10 68 C10 72 20 75 50 75 C80 75 90 72 90 68 C90 63 82 60 80 60 Z M46 20 L54 20 L54 48 L46 48 Z"/></svg>`
        },
        {
            id: 'ppe_glasses',
            name: 'Koruyucu Gözlük Tak',
            category: 'ppe',
            tags: 'gozluk koruyucu goz isg kaynak taslama',
            color: '#0055aa',
            svg: `<svg viewBox="0 0 100 100" fill="currentColor"><path d="M15 35 C15 25 35 25 45 35 C48 38 52 38 55 35 C65 25 85 25 85 35 C85 55 70 65 55 60 C52 58 48 58 45 60 C30 65 15 55 15 35 Z M25 38 C25 48 35 52 42 48 C45 45 45 38 40 35 C35 32 25 32 25 38 Z M75 38 C75 32 65 32 60 35 C55 38 55 45 58 48 C65 52 75 48 75 38 Z"/></svg>`
        },
        {
            id: 'ppe_mask',
            name: 'Maske Takınız',
            category: 'ppe',
            tags: 'maske respirator toz kimyasal n95 saglik',
            color: '#0055aa',
            svg: `<svg viewBox="0 0 100 100" fill="currentColor"><path d="M20 40 C20 30 50 25 50 25 C50 25 80 30 80 40 C80 58 68 75 50 78 C32 75 20 58 20 40 Z M30 45 C30 55 40 65 50 68 C60 65 70 55 70 45 C70 40 50 35 50 35 C50 35 30 40 30 45 Z M10 38 L20 42 M90 38 L80 42 M12 55 L22 52 M88 55 L78 52" stroke="currentColor" stroke-width="4" stroke-linecap="round"/></svg>`
        },
        {
            id: 'ppe_ear',
            name: 'Kulaklık Takınız',
            category: 'ppe',
            tags: 'kulaklik ses gurultu pres kompresor atolye',
            color: '#0055aa',
            svg: `<svg viewBox="0 0 100 100" fill="currentColor"><path d="M50 15 C30 15 20 30 20 50 L20 65 C20 75 28 80 35 80 L35 50 C28 50 25 55 25 65 M50 15 C70 15 80 30 80 50 L80 65 C80 75 72 80 65 80 L65 50 C72 50 75 55 75 65" fill="none" stroke="currentColor" stroke-width="8" stroke-linecap="round"/><rect x="16" y="48" width="16" height="30" rx="6" fill="currentColor"/><rect x="68" y="48" width="16" height="30" rx="6" fill="currentColor"/></svg>`
        },
        {
            id: 'ppe_gloves',
            name: 'İş Eldiveni Giyiniz',
            category: 'ppe',
            tags: 'eldiven el isi kesilme kimyasal koruma',
            color: '#0055aa',
            svg: `<svg viewBox="0 0 100 100" fill="currentColor"><path d="M30 40 L30 25 C30 22 34 22 34 25 L34 38 M36 38 L36 18 C36 15 40 15 40 18 L40 38 M42 38 L42 20 C42 17 46 17 46 20 L46 38 M48 38 L48 24 C48 21 52 21 52 24 L52 45 C52 50 56 45 60 45 C64 45 64 50 58 55 L52 65 L52 80 L28 80 L28 60 C25 55 30 48 30 40 Z"/></svg>`
        },
        {
            id: 'ppe_boots',
            name: 'İş Ayakkabısı Giy',
            category: 'ppe',
            tags: 'ayakkabi celik burun bot cizme ayak koruma',
            color: '#0055aa',
            svg: `<svg viewBox="0 0 100 100" fill="currentColor"><path d="M30 20 L48 20 L48 50 L75 55 C82 57 85 64 85 70 L85 80 L18 80 L18 68 C18 55 25 45 30 20 Z M20 74 L82 74 M20 80 L20 74 M40 80 L40 74 M60 80 L60 74 M80 80 L80 74" stroke="currentColor" stroke-width="3"/></svg>`
        },
        {
            id: 'ppe_vest',
            name: 'Reflektörlü Yelek',
            category: 'ppe',
            tags: 'yelek reflektor ikaz sari santiye yol',
            color: '#0055aa',
            svg: `<svg viewBox="0 0 100 100" fill="currentColor"><path d="M30 20 L42 20 L46 45 L54 45 L58 20 L70 20 L80 78 L20 78 Z M24 50 L76 50 L76 58 L24 58 Z M22 66 L78 66 L78 74 L22 74 Z"/></svg>`
        },
        {
            id: 'ppe_harness',
            name: 'Emniyet Kemeri Tak',
            category: 'ppe',
            tags: 'emniyet kemeri yuksekte calisma iskele halat',
            color: '#0055aa',
            svg: `<svg viewBox="0 0 100 100" fill="currentColor"><circle cx="50" cy="20" r="8"/><path d="M38 32 L62 32 L60 70 L50 85 L40 70 Z M38 42 L62 42 M39 56 L61 56 M44 32 L44 70 M56 32 L56 70" stroke="currentColor" stroke-width="4" fill="none"/></svg>`
        },

        // 2. KIRMIZI YASAKLAR (PROHIBITION)
        {
            id: 'no_smoking',
            name: 'Sigara İçilmez',
            category: 'prohibition',
            tags: 'sigara icilmez yasak tutun duman ates',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100" fill="currentColor"><circle cx="50" cy="50" r="42" fill="none" stroke="#dd2222" stroke-width="8"/><line x1="20" y1="20" x2="80" y2="80" stroke="#dd2222" stroke-width="8"/><rect x="25" y="45" width="40" height="8" fill="#333"/><rect x="68" y="45" width="8" height="8" fill="#e67e22"/><path d="M78 42 C82 38 78 34 82 30 M84 45 C88 41 84 37 88 33" stroke="#888" stroke-width="2" fill="none"/></svg>`
        },
        {
            id: 'no_flame',
            name: 'Açık Ateşle Yaklaşma',
            category: 'prohibition',
            tags: 'ates alev yaklasilmaz kibrit cakmak patlama',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100" fill="currentColor"><circle cx="50" cy="50" r="42" fill="none" stroke="#dd2222" stroke-width="8"/><line x1="20" y1="20" x2="80" y2="80" stroke="#dd2222" stroke-width="8"/><path d="M50 25 C50 25 62 42 62 55 C62 65 55 72 50 72 C45 72 38 65 38 55 C38 45 46 38 46 38 C46 38 44 48 50 52 C50 45 50 25 50 25 Z" fill="#e67e22"/></svg>`
        },
        {
            id: 'no_entry',
            name: 'Yetkisiz Giremez',
            category: 'prohibition',
            tags: 'girilmez yasak yetkisiz yabanci personel ozel',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100" fill="currentColor"><circle cx="50" cy="50" r="42" fill="none" stroke="#dd2222" stroke-width="8"/><line x1="20" y1="20" x2="80" y2="80" stroke="#dd2222" stroke-width="8"/><circle cx="50" cy="35" r="7"/><path d="M38 72 L38 55 C38 48 62 48 62 55 L62 72 Z M44 55 L44 72 M56 55 L56 72"/></svg>`
        },
        {
            id: 'no_phone',
            name: 'Cep Telefonu Yasak',
            category: 'prohibition',
            tags: 'telefon cep arama konusma cihaz yasak',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100" fill="currentColor"><circle cx="50" cy="50" r="42" fill="none" stroke="#dd2222" stroke-width="8"/><line x1="20" y1="20" x2="80" y2="80" stroke="#dd2222" stroke-width="8"/><rect x="36" y="26" width="28" height="48" rx="4" fill="none" stroke="#333" stroke-width="4"/><circle cx="50" cy="68" r="2" fill="#333"/><rect x="40" y="32" width="20" height="30" fill="#bbb"/></svg>`
        },
        {
            id: 'no_food',
            name: 'Yiyecek İçecek Yasaktır',
            category: 'prohibition',
            tags: 'yemek icmek su hamburger kola cop',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100" fill="currentColor"><circle cx="50" cy="50" r="42" fill="none" stroke="#dd2222" stroke-width="8"/><line x1="20" y1="20" x2="80" y2="80" stroke="#dd2222" stroke-width="8"/><path d="M30 45 C30 35 50 35 50 45 L30 45 Z M30 50 L50 50 L48 68 L32 68 Z M60 35 L68 35 L65 68 L57 68 Z" fill="#333"/></svg>`
        },
        {
            id: 'no_touch',
            name: 'Dokunmak Yasaktır',
            category: 'prohibition',
            tags: 'dokunma el temas carpilma makine elektrik',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100" fill="currentColor"><circle cx="50" cy="50" r="42" fill="none" stroke="#dd2222" stroke-width="8"/><line x1="20" y1="20" x2="80" y2="80" stroke="#dd2222" stroke-width="8"/><path d="M50 30 L50 48 M42 34 L42 50 M35 42 L35 54 M58 38 L58 50 M65 48 L65 58 C65 70 50 75 40 70 L30 58 L36 52 L42 58 L42 34" stroke="#333" stroke-width="4" fill="none" stroke-linecap="round"/></svg>`
        },

        // 3. SARI TEHLİKELER & UYARILAR (WARNING / HAZARD)
        {
            id: 'warn_general',
            name: 'Genel Tehlike (Üçgen)',
            category: 'warning',
            tags: 'dikkat tehlike ikaz ucgen genel',
            color: '#f59e0b',
            svg: `<svg viewBox="0 0 100 100"><polygon points="50,12 90,82 10,82" fill="#ffcc00" stroke="#111" stroke-width="6" stroke-linejoin="round"/><rect x="46" y="34" width="8" height="24" rx="4" fill="#111"/><circle cx="50" cy="68" r="4.5" fill="#111"/></svg>`
        },
        {
            id: 'warn_voltage',
            name: 'Yüksek Gerilim / Elektrik',
            category: 'warning',
            tags: 'elektrik gerilim carpilma pano trafo enerji simsek',
            color: '#f59e0b',
            svg: `<svg viewBox="0 0 100 100"><polygon points="50,12 90,82 10,82" fill="#ffcc00" stroke="#111" stroke-width="6" stroke-linejoin="round"/><path d="M54 30 L38 52 L48 52 L42 72 L62 48 L50 48 Z" fill="#111"/></svg>`
        },
        {
            id: 'warn_slip',
            name: 'Kaygan Zemin',
            category: 'warning',
            tags: 'kaygan zemin islak dusme kayma temizlik',
            color: '#f59e0b',
            svg: `<svg viewBox="0 0 100 100"><polygon points="50,12 90,82 10,82" fill="#ffcc00" stroke="#111" stroke-width="6" stroke-linejoin="round"/><circle cx="56" cy="34" r="5" fill="#111"/><path d="M52 42 L42 56 L30 52 M42 56 L50 68 L64 68 M40 60 L32 72 L22 72 M25 78 C35 74 65 74 75 78" stroke="#111" stroke-width="3.5" fill="none" stroke-linecap="round"/></svg>`
        },
        {
            id: 'warn_forklift',
            name: 'Forklift / İş Makinesi',
            category: 'warning',
            tags: 'forklift is makinesi arac depo yuk tasima',
            color: '#f59e0b',
            svg: `<svg viewBox="0 0 100 100"><polygon points="50,12 90,82 10,82" fill="#ffcc00" stroke="#111" stroke-width="6" stroke-linejoin="round"/><circle cx="48" cy="40" r="4" fill="#111"/><path d="M32 66 L62 66 L62 45 L50 45 L45 55 L32 55 Z M62 45 L72 45 L72 66 L80 66" stroke="#111" stroke-width="3" fill="none"/><circle cx="38" cy="68" r="4" fill="#111"/><circle cx="58" cy="68" r="4" fill="#111"/></svg>`
        },
        {
            id: 'warn_toxic',
            name: 'Zehirli Madde / Toksik',
            category: 'warning',
            tags: 'zehirli toksik kuru kafa olum kimyasal tehlike',
            color: '#f59e0b',
            svg: `<svg viewBox="0 0 100 100"><polygon points="50,12 90,82 10,82" fill="#ffcc00" stroke="#111" stroke-width="6" stroke-linejoin="round"/><circle cx="50" cy="45" r="12" fill="#111"/><rect x="44" y="55" width="12" height="8" fill="#111"/><circle cx="46" cy="44" r="2.5" fill="#ffcc00"/><circle cx="54" cy="44" r="2.5" fill="#ffcc00"/><polygon points="50,48 48,53 52,53" fill="#ffcc00"/><line x1="34" y1="64" x2="66" y2="40" stroke="#111" stroke-width="3"/><line x1="34" y1="40" x2="66" y2="64" stroke="#111" stroke-width="3"/></svg>`
        },
        {
            id: 'warn_falling',
            name: 'Düşen Cisim Tehlikesi',
            category: 'warning',
            tags: 'dusen cisim tas malzeme bas yukari baret',
            color: '#f59e0b',
            svg: `<svg viewBox="0 0 100 100"><polygon points="50,12 90,82 10,82" fill="#ffcc00" stroke="#111" stroke-width="6" stroke-linejoin="round"/><circle cx="50" cy="65" r="6" fill="#111"/><path d="M42 62 C42 56 58 56 58 62 Z" fill="#111"/><rect x="42" y="32" width="6" height="6" transform="rotate(45 45 35)" fill="#111"/><rect x="54" y="38" width="8" height="8" transform="rotate(25 58 42)" fill="#111"/><rect x="36" y="44" width="5" height="5" transform="rotate(15 38 46)" fill="#111"/></svg>`
        },

        // 4. YEŞİL ACİL DURUM & İLK YARDIM (EMERGENCY / FIRST AID)
        {
            id: 'exit_right',
            name: 'Acil Çıkış (Sağa)',
            category: 'emergency',
            tags: 'acil cikis exit kapi kacan adam tahliye sag',
            color: '#10b981',
            svg: `<svg viewBox="0 0 100 100" fill="#10b981"><rect x="10" y="15" width="80" height="70" rx="8" fill="#008844"/><circle cx="40" cy="35" r="6" fill="#fff"/><path d="M36 44 L46 44 L52 55 L60 55 M42 48 L35 60 L28 60 M46 55 L42 70 L34 78 M48 65 L56 75" stroke="#fff" stroke-width="4" fill="none" stroke-linecap="round"/><polygon points="70,42 82,50 70,58 70,53 62,53 62,47 70,47" fill="#fff"/><rect x="20" y="24" width="6" height="52" fill="#fff"/></svg>`
        },
        {
            id: 'exit_left',
            name: 'Acil Çıkış (Sola)',
            category: 'emergency',
            tags: 'acil cikis exit kapi kacan adam tahliye sol',
            color: '#10b981',
            svg: `<svg viewBox="0 0 100 100" fill="#10b981"><rect x="10" y="15" width="80" height="70" rx="8" fill="#008844"/><circle cx="60" cy="35" r="6" fill="#fff"/><path d="M64 44 L54 44 L48 55 L40 55 M58 48 L65 60 L72 60 M54 55 L58 70 L66 78 M52 65 L44 75" stroke="#fff" stroke-width="4" fill="none" stroke-linecap="round"/><polygon points="30,42 18,50 30,58 30,53 38,53 38,47 30,47" fill="#fff"/><rect x="74" y="24" width="6" height="52" fill="#fff"/></svg>`
        },
        {
            id: 'first_aid',
            name: 'İlk Yardım / Revir',
            category: 'emergency',
            tags: 'ilk yardim hac revir saglik doktor kaza',
            color: '#10b981',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#008844"/><rect x="42" y="28" width="16" height="44" rx="2" fill="#ffffff"/><rect x="28" y="42" width="44" height="16" rx="2" fill="#ffffff"/></svg>`
        },
        {
            id: 'assembly_point',
            name: 'Acil Toplanma Alanı',
            category: 'emergency',
            tags: 'toplanma alani acil durum deprem yangin tahliye',
            color: '#10b981',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#008844"/><circle cx="50" cy="40" r="5" fill="#fff"/><path d="M45 48 L55 48 L53 68 L47 68 Z" fill="#fff"/><polygon points="25,25 35,25 30,35" fill="#fff"/><polygon points="75,25 65,25 70,35" fill="#fff"/><polygon points="25,75 35,75 30,65" fill="#fff"/><polygon points="75,75 65,75 70,65" fill="#fff"/></svg>`
        },

        // 5. KIRMIZI YANGIN GÜVENLİĞİ (FIRE SAFETY)
        {
            id: 'fire_extinguisher',
            name: 'Yangın Söndürme Tüpü',
            category: 'fire',
            tags: 'yangin tupu sondurme itfaiye alev basinc',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#dd2222"/><rect x="42" y="38" width="16" height="40" rx="4" fill="#ffffff"/><rect x="46" y="28" width="8" height="10" fill="#ffffff"/><path d="M40 30 L60 30 L55 24 L45 24 Z M54 32 L66 40 L66 60" stroke="#fff" stroke-width="3" fill="none"/></svg>`
        },
        {
            id: 'fire_hose',
            name: 'Yangın Hortumu / Dolabı',
            category: 'fire',
            tags: 'yangin dolabi hortum su itfaiye vana',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#dd2222"/><circle cx="50" cy="50" r="22" fill="none" stroke="#fff" stroke-width="6"/><circle cx="50" cy="50" r="12" fill="none" stroke="#fff" stroke-width="5"/><circle cx="50" cy="50" r="4" fill="#fff"/><rect x="68" y="35" width="12" height="6" fill="#fff"/></svg>`
        },
        {
            id: 'fire_alarm',
            name: 'Yangın İhbar Butonu',
            category: 'fire',
            tags: 'yangin alarm buton kiriniz ihbar zil',
            color: '#dd2222',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#dd2222"/><rect x="28" y="28" width="44" height="44" rx="4" fill="#ffffff"/><circle cx="50" cy="50" r="12" fill="#dd2222"/><circle cx="50" cy="50" r="6" fill="#ffffff"/></svg>`
        },

        // 6. MAVİ BİLGİLENDİRME & TESİS (INFO / SECURITY)
        {
            id: 'info_camera',
            name: '7/24 Kamera ile İzlenir',
            category: 'info',
            tags: 'kamera cctv guvenlik izleme 724 kayit video',
            color: '#0071e3',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#0055aa"/><path d="M25 45 L55 35 L55 60 L25 55 Z M55 42 L72 32 L72 62 L55 52 Z" fill="#ffffff"/><circle cx="34" cy="48" r="4" fill="#0055aa"/><rect x="36" y="58" width="8" height="16" fill="#ffffff"/></svg>`
        },
        {
            id: 'info_wc_male',
            name: 'Bay WC / Tuvalet',
            category: 'info',
            tags: 'wc tuvalet bay erkek lavabo tesis',
            color: '#0071e3',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#0055aa"/><circle cx="50" cy="30" r="6" fill="#fff"/><path d="M38 42 L62 42 L58 60 L54 60 L54 78 L46 78 L46 60 L42 60 Z" fill="#fff"/></svg>`
        },
        {
            id: 'info_wc_female',
            name: 'Bayan WC / Tuvalet',
            category: 'info',
            tags: 'wc tuvalet bayan kadin lavabo tesis',
            color: '#0071e3',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#0055aa"/><circle cx="50" cy="30" r="6" fill="#fff"/><path d="M42 42 L58 42 L64 64 L54 64 L54 78 L46 78 L46 64 L36 64 Z" fill="#fff"/></svg>`
        },
        {
            id: 'info_disabled',
            name: 'Engelli Erişimi / WC',
            category: 'info',
            tags: 'engelli tekerlekli sandalye rampa wc',
            color: '#0071e3',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#0055aa"/><circle cx="55" cy="28" r="5" fill="#fff"/><path d="M45 40 L58 40 L52 56 L64 56 M52 56 L46 70 L34 70" stroke="#fff" stroke-width="5" fill="none" stroke-linecap="round"/><circle cx="46" cy="58" r="12" fill="none" stroke="#fff" stroke-width="5"/></svg>`
        },
        {
            id: 'info_parking',
            name: 'Otopark (P)',
            category: 'info',
            tags: 'otopark park p arac garaj',
            color: '#0071e3',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#0055aa"/><text x="50" y="68" font-size="52" font-family="Arial Black, sans-serif" font-weight="bold" text-anchor="middle" fill="#ffffff">P</text></svg>`
        },
        {
            id: 'info_recycling',
            name: 'Geri Dönüşüm / Sıfır Atık',
            category: 'info',
            tags: 'geri donusum sifir atik cop kutu plastik cam kagit',
            color: '#10b981',
            svg: `<svg viewBox="0 0 100 100"><rect x="10" y="15" width="80" height="70" rx="8" fill="#008844"/><path d="M50 25 L60 40 L40 40 Z M65 48 L75 62 L58 58 Z M35 48 L42 58 L25 62 Z" fill="#fff"/><path d="M48 38 L65 48 M62 58 L45 68 M35 58 L52 38" stroke="#fff" stroke-width="4" fill="none"/></svg>`
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

        getLibrary: function() { return vectorLibrary; },
        getHeaderPresets: function() { return headerPresets; },

        init: function(canvasElementId, widthCm, heightCm) {
            currentWidthCm = widthCm || 35;
            currentHeightCm = heightCm || 50;
            const canvasEl = document.getElementById(canvasElementId);
            if (!canvasEl) return;

            const holder = document.getElementById('signStudioCanvasHolder') || canvasEl.parentElement;
            const maxW = 500;
            const maxH = 650;
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
                    backgroundColor: '#ffffff',
                    preserveObjectStacking: true,
                    selection: true
                });
                canvas.renderAll();
            }
        },

        initStudio: function(canvasElementId) {
            this.init(canvasElementId, 35, 50);
        },

        renderCategoryButtons: function(containerId) {
            const container = document.getElementById(containerId);
            if (!container) return;

            const categories = [
                { id: 'all', name: 'Tümü', icon: 'bi-grid-fill' },
                { id: 'ppe', name: 'KKD / İSG', icon: 'bi-shield-check' },
                { id: 'prohibition', name: 'Yasaklar', icon: 'bi-slash-circle-fill' },
                { id: 'warning', name: 'Tehlike / Uyarı', icon: 'bi-exclamation-triangle-fill' },
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
                svgObj.scaleToWidth(canvas.getWidth() * 0.35);
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
                canvas.renderAll();
            });
        },

        addHeaderBand: function(presetIdx) {
            const preset = headerPresets[presetIdx] || headerPresets[0];
            if (!canvas) return;

            // Üst Bant Dikdörtgeni
            const bandHeight = canvas.getHeight() * 0.22;
            const rect = new fabric.Rect({
                left: 0,
                top: 0,
                width: canvas.getWidth(),
                height: bandHeight,
                fill: preset.bg,
                selectable: true
            });

            // Başlık Metni
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
            canvas.sendToBack(group);
            canvas.renderAll();
        },

        addCustomText: function(textString, fontSize = 24, isBold = true, color = '#111827') {
            if (!canvas) return;
            const text = new fabric.IText(textString || 'ÖZEL UYARI METNİ', {
                left: canvas.getWidth() / 2,
                top: canvas.getHeight() * 0.75,
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
            canvas.renderAll();
        },

        addHazardBorder: function(type = 'yellow_black') {
            if (!canvas) return;
            const border = new fabric.Rect({
                left: 6,
                top: 6,
                width: canvas.getWidth() - 12,
                height: canvas.getHeight() - 12,
                fill: 'transparent',
                stroke: (type === 'yellow_black') ? '#f59e0b' : '#dd2222',
                strokeWidth: 8,
                strokeDashArray: (type === 'striped') ? [15, 10] : null,
                selectable: true
            });
            canvas.add(border);
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
            canvas.renderAll();
        },

        clearCanvas: function() {
            if (canvas) {
                canvas.clear();
                canvas.backgroundColor = '#ffffff';
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
