<?php
require_once __DIR__ . '/config/config.php';

$db = Database::getInstance()->getConnection();

echo "=== SEKTÖR & ŞABLON SİSTEMİ KURULUMU BAŞLIYOR ===\n";

// 1. Industries Tablosu
$db->exec("CREATE TABLE IF NOT EXISTS `industries` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(150) NOT NULL UNIQUE,
  `icon` VARCHAR(50) DEFAULT 'bi bi-briefcase',
  `description` VARCHAR(255) NULL,
  `sort_order` INT DEFAULT 0,
  `status` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

echo "1. Industries tablosu hazır.\n";

// 2. Products ve Design_Templates Tablolarına Kolonları Ekle
try {
    $db->exec("ALTER TABLE `products` ADD COLUMN `target_industries` TEXT NULL COMMENT 'JSON array of industry slugs' AFTER `allowed_templates`;");
    echo "2a. products.target_industries kolonu eklendi.\n";
} catch (Exception $e) {
    echo "2a. products.target_industries kolonu zaten mevcut.\n";
}

try {
    $db->exec("ALTER TABLE `design_templates` ADD COLUMN `industry_slug` VARCHAR(100) DEFAULT 'genel-kurumsal' AFTER `category`;");
    echo "2b. design_templates.industry_slug kolonu eklendi.\n";
} catch (Exception $e) {
    echo "2b. design_templates.industry_slug kolonu zaten mevcut.\n";
}

try {
    $db->exec("ALTER TABLE `design_templates` ADD COLUMN `industry_id` INT NULL AFTER `industry_slug`;");
    echo "2c. design_templates.industry_id kolonu eklendi.\n";
} catch (Exception $e) {
    echo "2c. design_templates.industry_id kolonu zaten mevcut.\n";
}

// 3. Sektörleri Tanımla
$industries = [
    [
        'name' => 'Hukuk, Avukatlık & Danışmanlık',
        'slug' => 'hukuk-avukatlik',
        'icon' => 'bi-bank',
        'desc' => 'Avukatlar, Hukuk Büroları, Arabulucular ve Hukuki Danışmanlıklar',
        'order' => 1
    ],
    [
        'name' => 'Sağlık, Tıp, Doktor & Diş Hekimi',
        'slug' => 'saglik-klinik',
        'icon' => 'bi-heart-pulse',
        'desc' => 'Klinikler, Doktorlar, Diş Hekimleri, Eczaneler ve Sağlık Merkezleri',
        'order' => 2
    ],
    [
        'name' => 'Gayrimenkul, Emlak & Değerleme',
        'slug' => 'gayrimenkul-emlak',
        'icon' => 'bi-building',
        'desc' => 'Emlak Ofisleri, Gayrimenkul Danışmanları ve Değerleme Uzmanları',
        'order' => 3
    ],
    [
        'name' => 'İnşaat, Mimarlık & Mühendislik',
        'slug' => 'insaat-mimarlik',
        'icon' => 'bi-cone-striped',
        'desc' => 'Müteahhitler, İç Mimarlar, İnşaat ve Harita Mühendisleri',
        'order' => 4
    ],
    [
        'name' => 'Restoran, Kafe, Fırın & Gıda',
        'slug' => 'restoran-kafe',
        'icon' => 'bi-cup-hot',
        'desc' => 'Restoranlar, Kafeler, Fast-Food, Fırınlar ve Catering Şirketleri',
        'order' => 5
    ],
    [
        'name' => 'Güzellik, Kuaför, Berber & Spa',
        'slug' => 'guzellik-kuafor',
        'icon' => 'bi-scissors',
        'desc' => 'Kuaförler, Güzellik Merkezleri, Berberler, Tırnak & Spa Salonları',
        'order' => 6
    ],
    [
        'name' => 'Otomotiv, Araç Servisi & Kiralama',
        'slug' => 'otomotiv-servis',
        'icon' => 'bi-car-front',
        'desc' => 'Oto Ekspertiz, Araç Kiralama (Rent a Car), Oto Servis ve Yedek Parça',
        'order' => 7
    ],
    [
        'name' => 'Finans, Muhasebe & Sigorta',
        'slug' => 'finans-muhasebe',
        'icon' => 'bi-calculator',
        'desc' => 'Mali Müşavirler, Muhasebeciler, Sigorta Acenteleri ve Finans Danışmanları',
        'order' => 8
    ],
    [
        'name' => 'Teknoloji, Yazılım, Ajans & Medya',
        'slug' => 'teknoloji-yazilim',
        'icon' => 'bi-laptop',
        'desc' => 'Yazılım Şirketleri, Dijital Ajanslar, Medya ve Bilişim Firmaları',
        'order' => 9
    ],
    [
        'name' => 'Eğitim, Kurs, Okul & Akademi',
        'slug' => 'egitim-kurs',
        'icon' => 'bi-mortarboard',
        'desc' => 'Özel Okullar, Dil Kursları, Sürücü Kursları, Kreşler ve Eğitmenler',
        'order' => 10
    ],
    [
        'name' => 'Turizm, Seyahat & Otelcilik',
        'slug' => 'turizm-otel',
        'icon' => 'bi-airplane',
        'desc' => 'Oteller, Pansiyonlar, Seyahat Acenteleri ve Turizm Rehberleri',
        'order' => 11
    ],
    [
        'name' => 'Moda, Tekstil, Butik & Giyim',
        'slug' => 'moda-tekstil',
        'icon' => 'bi-handbag',
        'desc' => 'Giyim Butikleri, Tekstil Üreticileri, Ayakkabı ve Aksesuar Mağazaları',
        'order' => 12
    ],
    [
        'name' => 'Teknik Servis, Tesisat & Usta',
        'slug' => 'teknik-servis',
        'icon' => 'bi-tools',
        'desc' => 'Kombi & Beyaz Eşya Servisi, Elektrikçiler, Tesisatçılar ve Ustalar',
        'order' => 13
    ],
    [
        'name' => 'Lojistik, Taşımacılık & Nakliyat',
        'slug' => 'lojistik-nakliyat',
        'icon' => 'bi-truck',
        'desc' => 'Evden Eve Nakliyat, Lojistik Şirketleri, Kargo ve Kurye Hizmetleri',
        'order' => 14
    ],
    [
        'name' => 'Genel Kurumsal & Ticaret',
        'slug' => 'genel-kurumsal',
        'icon' => 'bi-briefcase',
        'desc' => 'Her türlü kurumsal şirket, ihracat, ithalat ve genel ticaret işletmeleri',
        'order' => 15
    ]
];

$insStmt = $db->prepare("INSERT INTO industries (name, slug, icon, description, sort_order) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE name=VALUES(name), icon=VALUES(icon), description=VALUES(description), sort_order=VALUES(sort_order)");
foreach ($industries as $ind) {
    $insStmt->execute([$ind['name'], $ind['slug'], $ind['icon'], $ind['desc'], $ind['order']]);
}
echo "3. " . count($industries) . " sektör veritabanına kaydedildi.\n";

// Sektör ID eşleştirmeleri
$indMap = [];
$rows = $db->query("SELECT id, slug FROM industries")->fetchAll();
foreach ($rows as $r) {
    $indMap[$r['slug']] = $r['id'];
}

// 4. Varsayılan Ürünlerin Hedef Sektörlerini Güncelle
$products = $db->query("SELECT id, name, slug FROM products")->fetchAll();
$allIndustrySlugs = array_keys($indMap);

foreach ($products as $p) {
    $slug = $p['slug'];
    $targetInd = $allIndustrySlugs; // Varsayılan genel
    
    // Ürüne göre özel sektör hedeflemeleri
    if (strpos($slug, 'kartvizit') !== false || strpos($slug, 'kasa') !== false) {
        $targetInd = $allIndustrySlugs;
    } elseif (strpos($slug, 'brosur') !== false || strpos($slug, 'el-ilani') !== false) {
        $targetInd = ['restoran-kafe', 'saglik-klinik', 'guzellik-kuafor', 'gayrimenkul-emlak', 'egitim-kurs', 'turizm-otel', 'teknik-servis', 'genel-kurumsal'];
    } elseif (strpos($slug, 'kase') !== false) {
        $targetInd = ['hukuk-avukatlik', 'saglik-klinik', 'finans-muhasebe', 'insaat-mimarlik', 'genel-kurumsal', 'teknoloji-yazilim'];
    }
    
    $upProd = $db->prepare("UPDATE products SET target_industries = ?, allow_online_editor = 1 WHERE id = ?");
    $upProd->execute([json_encode($targetInd, JSON_UNESCAPED_UNICODE), $p['id']]);
}
echo "4. Ürünlerin hedef sektör atamaları yapıldı.\n";

// 5. Her Sektör İçin En Az 5 Adet Profesyonel Vektörel SVG Şablonu Oluştur
// Standart Canvas Boyutu: 850 x 500 px (300 DPI Kartvizit Oranı)
$firstProdId = $products[0]['id'] ?? 1;

function createSvgTemplate($styleType, $palette, $iconSvg, $tagText, $industryName) {
    $bg = $palette['bg'];
    $accent = $palette['accent'];
    $accent2 = $palette['accent2'] ?? '#ffffff';
    $textDark = $palette['textDark'] ?? '#1d1d1f';
    $textLight = $palette['textLight'] ?? '#86868b';
    $pattern = $palette['pattern'] ?? 'none';
    $isDark = !empty($palette['isDark']);

    $textColor = $isDark ? '#ffffff' : '#1e293b';
    $subTextColor = $isDark ? '#cbd5e1' : '#64748b';
    $mutedColor = $isDark ? '#94a3b8' : '#94a3b8';
    $borderStyle = $isDark ? 'rgba(255,255,255,0.15)' : 'rgba(0,0,0,0.08)';

    if ($styleType === 'modern_minimal') {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 850 500" width="850" height="500">
  <defs>
    <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="{$accent}" stop-opacity="0.9"/>
      <stop offset="100%" stop-color="{$accent2}" stop-opacity="1"/>
    </linearGradient>
  </defs>
  <rect width="850" height="500" fill="{$bg}" rx="16"/>
  <!-- Sol Vurgu Şeridi & Geometrik Çizgi -->
  <path d="M0,0 L20,0 L20,500 L0,500 Z" fill="url(#grad1)"/>
  <circle cx="780" cy="80" r="140" fill="{$accent}" opacity="0.06"/>
  <circle cx="820" cy="420" r="110" fill="{$accent2}" opacity="0.05"/>
  
  <!-- Logo & Sektör İkonu -->
  <g transform="translate(60, 50)">
    <rect width="60" height="60" rx="14" fill="url(#grad1)"/>
    <g transform="translate(14, 14) scale(1.3)" fill="#ffffff">
      {$iconSvg}
    </g>
    <text id="companyName" x="80" y="36" font-family="'Inter', -apple-system, sans-serif" font-weight="800" font-size="26" fill="{$textColor}" letter-spacing="0.5">ŞİRKETİNİZ &amp; CO.</text>
    <text id="tagline" x="80" y="56" font-family="'Inter', -apple-system, sans-serif" font-weight="600" font-size="12" fill="{$accent}" letter-spacing="1.5">{$tagText}</text>
  </g>
  
  <!-- Kişi Bilgisi -->
  <g transform="translate(60, 210)">
    <text id="personName" x="0" y="40" font-family="'Inter', -apple-system, sans-serif" font-weight="800" font-size="34" fill="{$textColor}">Ahmet Yılmaz</text>
    <text id="title" x="0" y="72" font-family="'Inter', -apple-system, sans-serif" font-weight="600" font-size="16" fill="{$accent}" letter-spacing="0.8">Kurucu / Uzman Direktör</text>
    <line x1="0" y1="95" x2="380" y2="95" stroke="{$borderStyle}" stroke-width="2"/>
  </g>
  
  <!-- İletişim Detayları -->
  <g transform="translate(60, 345)" font-family="'Inter', -apple-system, sans-serif" font-size="14" font-weight="500" fill="{$subTextColor}">
    <g transform="translate(0, 0)">
      <circle cx="12" cy="-4" r="14" fill="url(#grad1)" opacity="0.15"/>
      <text x="36" y="0" font-weight="600" fill="{$textColor}" id="phone">+90 (555) 123 45 67</text>
    </g>
    <g transform="translate(0, 45)">
      <circle cx="12" cy="-4" r="14" fill="url(#grad1)" opacity="0.15"/>
      <text x="36" y="0" id="email">bilgi@sirketiniz.com</text>
    </g>
    <g transform="translate(380, 0)">
      <circle cx="12" cy="-4" r="14" fill="url(#grad1)" opacity="0.15"/>
      <text x="36" y="0" id="website">www.sirketiniz.com</text>
    </g>
    <g transform="translate(380, 45)">
      <circle cx="12" cy="-4" r="14" fill="url(#grad1)" opacity="0.15"/>
      <text x="36" y="0" id="address">Levent Plaza Kat: 12 Beşiktaş / İstanbul</text>
    </g>
  </g>
</svg>
SVG;
    } elseif ($styleType === 'executive_gold') {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 850 500" width="850" height="500">
  <defs>
    <linearGradient id="goldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#f59e0b"/>
      <stop offset="50%" stop-color="#fbbf24"/>
      <stop offset="100%" stop-color="#d97706"/>
    </linearGradient>
    <pattern id="gridPattern" width="20" height="20" patternUnits="userSpaceOnUse">
      <path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(245,158,11,0.05)" stroke-width="1"/>
    </pattern>
  </defs>
  <rect width="850" height="500" fill="{$bg}" rx="16"/>
  <rect width="850" height="500" fill="url(#gridPattern)"/>
  
  <!-- Çerçeve Bordürü -->
  <rect x="25" y="25" width="800" height="450" rx="10" fill="none" stroke="url(#goldGrad)" stroke-width="1.5" opacity="0.6"/>
  <rect x="32" y="32" width="786" height="436" rx="6" fill="none" stroke="url(#goldGrad)" stroke-width="0.7" opacity="0.3"/>

  <!-- Logo Bölümü -->
  <g transform="translate(425, 80)" text-anchor="middle">
    <circle cx="0" cy="0" r="32" fill="none" stroke="url(#goldGrad)" stroke-width="2"/>
    <g transform="translate(-12, -12) scale(1.2)" fill="url(#goldGrad)">
      {$iconSvg}
    </g>
    <text id="companyName" x="0" y="55" font-family="'Cinzel', 'Playfair Display', serif" font-weight="700" font-size="24" fill="url(#goldGrad)" letter-spacing="3">ŞİRKETİNİZ &amp; PARTNERS</text>
    <text id="tagline" x="0" y="75" font-family="'Inter', sans-serif" font-size="11" font-weight="500" fill="{$subTextColor}" letter-spacing="2">{$tagText}</text>
  </g>

  <!-- İsim & Unvan -->
  <g transform="translate(425, 260)" text-anchor="middle">
    <text id="personName" x="0" y="0" font-family="'Cinzel', 'Playfair Display', serif" font-weight="700" font-size="32" fill="{$textColor}" letter-spacing="1.5">Av. Selim Karaca</text>
    <text id="title" x="0" y="30" font-family="'Inter', sans-serif" font-weight="500" font-size="14" fill="url(#goldGrad)" letter-spacing="2">YÖNETİCİ ORTAK &amp; DANIŞMAN</text>
    <line x1="-120" y1="50" x2="120" y2="50" stroke="url(#goldGrad)" stroke-width="1.5"/>
  </g>

  <!-- İletişim Alt Satır -->
  <g transform="translate(425, 380)" text-anchor="middle" font-family="'Inter', sans-serif" font-size="13" font-weight="400" fill="{$subTextColor}">
    <text x="0" y="0">
      <tspan id="phone" font-weight="600" fill="{$textColor}">+90 212 444 00 00</tspan>
      <tspan fill="url(#goldGrad)"> • </tspan>
      <tspan id="email">info@sirketiniz.com</tspan>
      <tspan fill="url(#goldGrad)"> • </tspan>
      <tspan id="website">www.sirketiniz.com</tspan>
    </text>
    <text id="address" x="0" y="28" font-size="12" fill="{$mutedColor}">Adalet Sarayı Karşısı, Hukukçular Towers No: 42 Şişli / İstanbul</text>
  </g>
</svg>
SVG;
    } elseif ($styleType === 'creative_split') {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 850 500" width="850" height="500">
  <defs>
    <linearGradient id="splitGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="{$accent}"/>
      <stop offset="100%" stop-color="{$accent2}"/>
    </linearGradient>
  </defs>
  <rect width="850" height="500" fill="{$bg}" rx="16"/>
  
  <!-- Sağ Diyagonal Panel -->
  <path d="M500,0 L850,0 L850,500 L420,500 Z" fill="url(#splitGrad)"/>
  <circle cx="700" cy="180" r="160" fill="#ffffff" opacity="0.08"/>
  <circle cx="580" cy="400" r="100" fill="#ffffff" opacity="0.06"/>

  <!-- Sol Kısım: Kişi & Şirket -->
  <g transform="translate(60, 80)">
    <g transform="translate(0, 0)">
      <rect width="50" height="50" rx="12" fill="url(#splitGrad)"/>
      <g transform="translate(12, 12) scale(1.1)" fill="#ffffff">
        {$iconSvg}
      </g>
      <text id="companyName" x="65" y="32" font-family="'Inter', sans-serif" font-weight="800" font-size="22" fill="{$textColor}">ŞİRKET ADI</text>
      <text id="tagline" x="65" y="48" font-family="'Inter', sans-serif" font-weight="600" font-size="11" fill="{$accent}">{$tagText}</text>
    </g>

    <g transform="translate(0, 160)">
      <text id="personName" x="0" y="0" font-family="'Inter', sans-serif" font-weight="800" font-size="32" fill="{$textColor}">Burak Demir</text>
      <text id="title" x="0" y="30" font-family="'Inter', sans-serif" font-weight="600" font-size="15" fill="{$accent}">Genel Müdür</text>
      <line x1="0" y1="50" x2="250" y2="50" stroke="{$borderStyle}" stroke-width="2"/>
    </g>
  </g>

  <!-- Sağ Kısım (Beyaz Yazılar): İletişim -->
  <g transform="translate(560, 150)" font-family="'Inter', sans-serif" font-size="15" font-weight="500" fill="#ffffff">
    <g transform="translate(0, 0)">
      <text x="35" y="0" font-weight="700" id="phone">+90 532 000 00 00</text>
    </g>
    <g transform="translate(0, 60)">
      <text x="35" y="0" id="email">burak@sirketiniz.com</text>
    </g>
    <g transform="translate(0, 120)">
      <text x="35" y="0" id="website">www.sirketiniz.com</text>
    </g>
    <g transform="translate(0, 180)">
      <text x="35" y="0" font-size="13" opacity="0.9" id="address">Teknokent B Blok No: 18 Maslak / İstanbul</text>
    </g>
  </g>
</svg>
SVG;
    } elseif ($styleType === 'dark_neon') {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 850 500" width="850" height="500">
  <defs>
    <linearGradient id="neonGrad" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="{$accent}"/>
      <stop offset="100%" stop-color="{$accent2}"/>
    </linearGradient>
    <filter id="glow" x="-20%" y="-20%" width="140%" height="140%">
      <feGaussianBlur stdDeviation="8" result="blur" />
      <feComposite in="SourceGraphic" in2="blur" operator="over" />
    </filter>
  </defs>
  <rect width="850" height="500" fill="#0f172a" rx="16"/>
  
  <!-- Arka Plan Işık Efektleri -->
  <circle cx="100" cy="100" r="220" fill="{$accent}" opacity="0.12" filter="url(#glow)"/>
  <circle cx="750" cy="400" r="200" fill="{$accent2}" opacity="0.12" filter="url(#glow)"/>

  <!-- Üst Bar -->
  <rect x="60" y="40" width="730" height="2" fill="url(#neonGrad)"/>

  <!-- Logo -->
  <g transform="translate(60, 80)">
    <rect width="54" height="54" rx="14" fill="url(#neonGrad)"/>
    <g transform="translate(13, 13) scale(1.2)" fill="#ffffff">
      {$iconSvg}
    </g>
    <text id="companyName" x="72" y="34" font-family="'Inter', sans-serif" font-weight="900" font-size="24" fill="#ffffff" letter-spacing="1">PRO STUDIO</text>
    <text id="tagline" x="72" y="52" font-family="'Inter', sans-serif" font-weight="600" font-size="11" fill="{$accent}" letter-spacing="2">{$tagText}</text>
  </g>

  <!-- İsim & Unvan -->
  <g transform="translate(60, 240)">
    <text id="personName" x="0" y="0" font-family="'Inter', sans-serif" font-weight="800" font-size="36" fill="#f8fafc">Cemil Arslan</text>
    <text id="title" x="0" y="36" font-family="'Inter', sans-serif" font-weight="700" font-size="16" fill="url(#neonGrad)" letter-spacing="1.5">BAŞ DANIŞMAN &amp; UZMAN</text>
  </g>

  <!-- İletişim Alt Grid -->
  <g transform="translate(60, 360)" font-family="'Inter', sans-serif" font-size="14" font-weight="500" fill="#94a3b8">
    <g transform="translate(0, 0)">
      <text x="0" y="0" font-weight="700" fill="#f1f5f9" id="phone">+90 544 111 22 33</text>
      <text x="0" y="36" id="email">iletisim@sirketiniz.com</text>
    </g>
    <g transform="translate(380, 0)">
      <text x="0" y="0" id="website">www.sirketiniz.com</text>
      <text x="0" y="36" font-size="13" id="address">Merkez Mah. İstiklal Cad. No: 104 Kadıköy / İstanbul</text>
    </g>
  </g>
  
  <rect x="60" y="460" width="730" height="2" fill="url(#neonGrad)"/>
</svg>
SVG;
    } else { // elegant_badge
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 850 500" width="850" height="500">
  <defs>
    <linearGradient id="badgeGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="{$accent}"/>
      <stop offset="100%" stop-color="{$accent2}"/>
    </linearGradient>
  </defs>
  <rect width="850" height="500" fill="{$bg}" rx="16"/>
  
  <!-- Üst Dalga Deseni -->
  <path d="M0,0 L850,0 L850,110 Q425,160 0,110 Z" fill="url(#badgeGrad)"/>
  
  <!-- Logo Rozeti -->
  <g transform="translate(425, 110)" text-anchor="middle">
    <circle cx="0" cy="0" r="45" fill="#ffffff" stroke="url(#badgeGrad)" stroke-width="4" filter="drop-shadow(0 4px 10px rgba(0,0,0,0.1))"/>
    <g transform="translate(-16, -16) scale(1.4)" fill="{$accent}">
      {$iconSvg}
    </g>
    <text id="companyName" x="0" y="75" font-family="'Inter', sans-serif" font-weight="800" font-size="24" fill="{$textColor}" letter-spacing="0.5">MARKA ADINIZ</text>
    <text id="tagline" x="0" y="96" font-family="'Inter', sans-serif" font-weight="600" font-size="12" fill="{$accent}" letter-spacing="1.5">{$tagText}</text>
  </g>

  <!-- İsim ve Unvan -->
  <g transform="translate(425, 290)" text-anchor="middle">
    <text id="personName" x="0" y="0" font-family="'Inter', sans-serif" font-weight="800" font-size="30" fill="{$textColor}">Zeynep Kaya</text>
    <text id="title" x="0" y="28" font-family="'Inter', sans-serif" font-weight="600" font-size="15" fill="{$subTextColor}">Kurumsal İletişim Sorumlusu</text>
    <line x1="-100" y1="48" x2="100" y2="48" stroke="{$accent}" stroke-width="2"/>
  </g>

  <!-- İletişim Bilgileri -->
  <g transform="translate(425, 385)" text-anchor="middle" font-family="'Inter', sans-serif" font-size="13" font-weight="500" fill="{$subTextColor}">
    <text x="0" y="0">
      <tspan id="phone" font-weight="700" fill="{$textColor}">+90 850 123 45 67</tspan>
      <tspan fill="{$accent}"> | </tspan>
      <tspan id="email">info@markaniz.com</tspan>
      <tspan fill="{$accent}"> | </tspan>
      <tspan id="website">www.markaniz.com</tspan>
    </text>
    <text id="address" x="0" y="28" font-size="12" fill="{$mutedColor}">Büyükdere Cad. No: 201 Şişli / İstanbul</text>
  </g>
</svg>
SVG;
    }
}

// Sektör İkonları SVG Path Listesi
$icons = [
    'hukuk-avukatlik' => '<path d="M16 2a1 1 0 0 1 .832.445l4.5 7A1 1 0 0 1 20.5 11h-1.618l-1.925 5.775A2 2 0 0 1 15.06 18H8.94a2 2 0 0 1-1.897-1.225L5.118 11H3.5a1 1 0 0 1-.832-1.555l4.5-7A1 1 0 0 1 8 2h8zM8.333 4L5.12 9h3.693l-1.48-5H8.333zm7.334 0h-1.006l-1.48 5h3.693l-1.207-5z"/>',
    'saglik-klinik' => '<path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm1 5h-2v4H7v2h4v4h2v-4h4v-2h-4V7z"/>',
    'gayrimenkul-emlak' => '<path d="M12 3L2 12h3v8h6v-6h2v6h6v-8h3L12 3z"/>',
    'insaat-mimarlik' => '<path d="M12 2l10 9h-3v11h-4v-7h-6v7H5V11H2l10-9zm0 3.84L7 8.34V20h3v-7h4v7h3V8.34L12 5.84z"/>',
    'restoran-kafe' => '<path d="M18 3v6a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4V3h2v6a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V3h2zm2 14v4h-2v-4a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v4H2v-4a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/>',
    'guzellik-kuafor' => '<path d="M10.5 3a2.5 2.5 0 0 0-2.45 2.05L4 16h3l3.5-9.5A2.5 2.5 0 0 0 10.5 3zm3 0a2.5 2.5 0 0 0-.05 4.55L17 16h3l-4.05-10.95A2.5 2.5 0 0 0 13.5 3zM6 18a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm12 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>',
    'otomotiv-servis' => '<path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.08 3.11H5.77L6.85 7zM19 17H5v-4.66l.12-.34h13.77l.11.34V17zM7.5 14a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zm9 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/>',
    'finans-muhasebe' => '<path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>',
    'teknoloji-yazilim' => '<path d="M20 18c1.1 0 1.99-.9 1.99-2L22 5c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v11c0 1.1.9 2 2 2H0c0 1.1.9 2 2 2h20c1.1 0 2-.9 2-2h-4zM4 5h16v11H4V5zm5 9l-3-3 3-3 1.41 1.41L8.83 11l1.58 1.59L9 14zm6 0l-1.41-1.41L15.17 11l-1.58-1.59L15 8l3 3-3 3z"/>',
    'egitim-kurs' => '<path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>',
    'turizm-otel' => '<path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/>',
    'moda-tekstil' => '<path d="M16 6V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H2v13c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6h-6zm-6-2h4v2h-4V4zm10 15H4V8h16v11z"/>',
    'teknik-servis' => '<path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.4 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/>',
    'lojistik-nakliyat' => '<path d="M20 8h-3V4H1v13h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>',
    'genel-kurumsal' => '<path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/>'
];

// Sektörlere Göre Özel Renk Paletleri
$palettes = [
    'hukuk-avukatlik' => [
        ['bg' => '#0f172a', 'accent' => '#f59e0b', 'accent2' => '#d97706', 'isDark' => true],
        ['bg' => '#ffffff', 'accent' => '#1e3a8a', 'accent2' => '#3b82f6', 'isDark' => false],
        ['bg' => '#18181b', 'accent' => '#e2e8f0', 'accent2' => '#94a3b8', 'isDark' => true],
        ['bg' => '#fdfbf7', 'accent' => '#b45309', 'accent2' => '#d97706', 'isDark' => false],
        ['bg' => '#1e293b', 'accent' => '#38bdf8', 'accent2' => '#0284c7', 'isDark' => true]
    ],
    'saglik-klinik' => [
        ['bg' => '#ffffff', 'accent' => '#0284c7', 'accent2' => '#0ea5e9', 'isDark' => false],
        ['bg' => '#f0fdfa', 'accent' => '#0d9488', 'accent2' => '#14b8a6', 'isDark' => false],
        ['bg' => '#0f172a', 'accent' => '#06b6d4', 'accent2' => '#3b82f6', 'isDark' => true],
        ['bg' => '#ffffff', 'accent' => '#2563eb', 'accent2' => '#1d4ed8', 'isDark' => false],
        ['bg' => '#f8fafc', 'accent' => '#10b981', 'accent2' => '#059669', 'isDark' => false]
    ],
    'gayrimenkul-emlak' => [
        ['bg' => '#ffffff', 'accent' => '#b45309', 'accent2' => '#f59e0b', 'isDark' => false],
        ['bg' => '#18181b', 'accent' => '#f59e0b', 'accent2' => '#fbbf24', 'isDark' => true],
        ['bg' => '#0f172a', 'accent' => '#3b82f6', 'accent2' => '#60a5fa', 'isDark' => true],
        ['bg' => '#ffffff', 'accent' => '#047857', 'accent2' => '#10b981', 'isDark' => false],
        ['bg' => '#f8fafc', 'accent' => '#ea580c', 'accent2' => '#f97316', 'isDark' => false]
    ],
    'insaat-mimarlik' => [
        ['bg' => '#1e293b', 'accent' => '#f97316', 'accent2' => '#ea580c', 'isDark' => true],
        ['bg' => '#ffffff', 'accent' => '#334155', 'accent2' => '#64748b', 'isDark' => false],
        ['bg' => '#09090b', 'accent' => '#eab308', 'accent2' => '#ca8a04', 'isDark' => true],
        ['bg' => '#ffffff', 'accent' => '#2563eb', 'accent2' => '#1d4ed8', 'isDark' => false],
        ['bg' => '#1c1917', 'accent' => '#d97706', 'accent2' => '#f59e0b', 'isDark' => true]
    ],
    'restoran-kafe' => [
        ['bg' => '#1c1917', 'accent' => '#f97316', 'accent2' => '#ea580c', 'isDark' => true],
        ['bg' => '#ffffff', 'accent' => '#dc2626', 'accent2' => '#ef4444', 'isDark' => false],
        ['bg' => '#27272a', 'accent' => '#fbbf24', 'accent2' => '#f59e0b', 'isDark' => true],
        ['bg' => '#fefce8', 'accent' => '#854d0e', 'accent2' => '#a16207', 'isDark' => false],
        ['bg' => '#18181b', 'accent' => '#10b981', 'accent2' => '#059669', 'isDark' => true]
    ],
    'guzellik-kuafor' => [
        ['bg' => '#ffffff', 'accent' => '#ec4899', 'accent2' => '#f43f5e', 'isDark' => false],
        ['bg' => '#18181b', 'accent' => '#f472b6', 'accent2' => '#fb7185', 'isDark' => true],
        ['bg' => '#fdf4ff', 'accent' => '#c026d3', 'accent2' => '#d946ef', 'isDark' => false],
        ['bg' => '#ffffff', 'accent' => '#d97706', 'accent2' => '#f59e0b', 'isDark' => false],
        ['bg' => '#0f172a', 'accent' => '#e879f9', 'accent2' => '#a855f7', 'isDark' => true]
    ],
    'otomotiv-servis' => [
        ['bg' => '#0f172a', 'accent' => '#ef4444', 'accent2' => '#dc2626', 'isDark' => true],
        ['bg' => '#ffffff', 'accent' => '#1e293b', 'accent2' => '#3b82f6', 'isDark' => false],
        ['bg' => '#18181b', 'accent' => '#3b82f6', 'accent2' => '#60a5fa', 'isDark' => true],
        ['bg' => '#ffffff', 'accent' => '#f97316', 'accent2' => '#ea580c', 'isDark' => false],
        ['bg' => '#09090b', 'accent' => '#eab308', 'accent2' => '#ca8a04', 'isDark' => true]
    ],
    'finans-muhasebe' => [
        ['bg' => '#ffffff', 'accent' => '#047857', 'accent2' => '#059669', 'isDark' => false],
        ['bg' => '#0f172a', 'accent' => '#10b981', 'accent2' => '#34d399', 'isDark' => true],
        ['bg' => '#ffffff', 'accent' => '#1e3a8a', 'accent2' => '#2563eb', 'isDark' => false],
        ['bg' => '#18181b', 'accent' => '#f59e0b', 'accent2' => '#fbbf24', 'isDark' => true],
        ['bg' => '#f8fafc', 'accent' => '#0f766e', 'accent2' => '#14b8a6', 'isDark' => false]
    ],
    'teknoloji-yazilim' => [
        ['bg' => '#0f172a', 'accent' => '#3b82f6', 'accent2' => '#8b5cf6', 'isDark' => true],
        ['bg' => '#ffffff', 'accent' => '#2563eb', 'accent2' => '#06b6d4', 'isDark' => false],
        ['bg' => '#020617', 'accent' => '#06b6d4', 'accent2' => '#3b82f6', 'isDark' => true],
        ['bg' => '#18181b', 'accent' => '#10b981', 'accent2' => '#06b6d4', 'isDark' => true],
        ['bg' => '#ffffff', 'accent' => '#8b5cf6', 'accent2' => '#ec4899', 'isDark' => false]
    ],
    'egitim-kurs' => [
        ['bg' => '#ffffff', 'accent' => '#2563eb', 'accent2' => '#3b82f6', 'isDark' => false],
        ['bg' => '#0f172a', 'accent' => '#f59e0b', 'accent2' => '#fbbf24', 'isDark' => true],
        ['bg' => '#f0fdf4', 'accent' => '#16a34a', 'accent2' => '#22c55e', 'isDark' => false],
        ['bg' => '#ffffff', 'accent' => '#7c3aed', 'accent2' => '#8b5cf6', 'isDark' => false],
        ['bg' => '#1e293b', 'accent' => '#06b6d4', 'accent2' => '#38bdf8', 'isDark' => true]
    ],
    'turizm-otel' => [
        ['bg' => '#0f172a', 'accent' => '#0284c7', 'accent2' => '#38bdf8', 'isDark' => true],
        ['bg' => '#ffffff', 'accent' => '#0891b2', 'accent2' => '#06b6d4', 'isDark' => false],
        ['bg' => '#18181b', 'accent' => '#f59e0b', 'accent2' => '#fbbf24', 'isDark' => true],
        ['bg' => '#f0fdfa', 'accent' => '#0d9488', 'accent2' => '#14b8a6', 'isDark' => false],
        ['bg' => '#ffffff', 'accent' => '#4f46e5', 'accent2' => '#6366f1', 'isDark' => false]
    ],
    'moda-tekstil' => [
        ['bg' => '#ffffff', 'accent' => '#18181b', 'accent2' => '#71717a', 'isDark' => false],
        ['bg' => '#18181b', 'accent' => '#f472b6', 'accent2' => '#e879f9', 'isDark' => true],
        ['bg' => '#faf5ff', 'accent' => '#9333ea', 'accent2' => '#c084fc', 'isDark' => false],
        ['bg' => '#0f172a', 'accent' => '#fbbf24', 'accent2' => '#f59e0b', 'isDark' => true],
        ['bg' => '#ffffff', 'accent' => '#be185d', 'accent2' => '#db2777', 'isDark' => false]
    ],
    'teknik-servis' => [
        ['bg' => '#0f172a', 'accent' => '#f97316', 'accent2' => '#fb923c', 'isDark' => true],
        ['bg' => '#ffffff', 'accent' => '#2563eb', 'accent2' => '#1d4ed8', 'isDark' => false],
        ['bg' => '#18181b', 'accent' => '#eab308', 'accent2' => '#facc15', 'isDark' => true],
        ['bg' => '#ffffff', 'accent' => '#dc2626', 'accent2' => '#ef4444', 'isDark' => false],
        ['bg' => '#1e293b', 'accent' => '#10b981', 'accent2' => '#34d399', 'isDark' => true]
    ],
    'lojistik-nakliyat' => [
        ['bg' => '#ffffff', 'accent' => '#2563eb', 'accent2' => '#1d4ed8', 'isDark' => false],
        ['bg' => '#0f172a', 'accent' => '#f97316', 'accent2' => '#ea580c', 'isDark' => true],
        ['bg' => '#18181b', 'accent' => '#3b82f6', 'accent2' => '#60a5fa', 'isDark' => true],
        ['bg' => '#ffffff', 'accent' => '#16a34a', 'accent2' => '#15803d', 'isDark' => false],
        ['bg' => '#09090b', 'accent' => '#eab308', 'accent2' => '#ca8a04', 'isDark' => true]
    ],
    'genel-kurumsal' => [
        ['bg' => '#ffffff', 'accent' => '#0071e3', 'accent2' => '#005bb5', 'isDark' => false],
        ['bg' => '#0f172a', 'accent' => '#38bdf8', 'accent2' => '#0284c7', 'isDark' => true],
        ['bg' => '#18181b', 'accent' => '#f59e0b', 'accent2' => '#fbbf24', 'isDark' => true],
        ['bg' => '#f8fafc', 'accent' => '#475569', 'accent2' => '#334155', 'isDark' => false],
        ['bg' => '#ffffff', 'accent' => '#10b981', 'accent2' => '#059669', 'isDark' => false]
    ]
];

$styleTypes = ['modern_minimal', 'executive_gold', 'creative_split', 'dark_neon', 'elegant_badge'];
$styleNames = [
    'modern_minimal' => 'Modern Geometrik',
    'executive_gold' => 'VIP Prestij Yaldızlı',
    'creative_split' => 'Dinamik Split Renk',
    'dark_neon' => 'Dark Glow Gece',
    'elegant_badge' => 'Rozetli Kurumsal'
];

$templateFieldsJson = json_encode([
    'fields' => [
        ['id' => 'personName', 'label' => 'Ad Soyad', 'default' => 'Ahmet Yılmaz'],
        ['id' => 'title', 'label' => 'Unvan / Görev', 'default' => 'Genel Müdür & Kurucu'],
        ['id' => 'companyName', 'label' => 'Şirket / Marka Adı', 'default' => 'ŞİRKETİNİZ A.Ş.'],
        ['id' => 'tagline', 'label' => 'Slogan / Alt Başlık', 'default' => 'GÜVENİLİR & PROFESYONEL ÇÖZÜMLER'],
        ['id' => 'phone', 'label' => 'Telefon Numarası', 'default' => '+90 (555) 123 45 67'],
        ['id' => 'email', 'label' => 'E-Posta Adresi', 'default' => 'info@sirketiniz.com'],
        ['id' => 'website', 'label' => 'Web Sitesi', 'default' => 'www.sirketiniz.com'],
        ['id' => 'address', 'label' => 'Adres Bilgisi', 'default' => 'Merkez Plaza Kat: 8 Beşiktaş / İstanbul']
    ]
], JSON_UNESCAPED_UNICODE);

// Şablon Ekleme Sorgusu
$insTpl = $db->prepare("INSERT INTO design_templates (product_id, title, slug, category, industry_slug, industry_id, canvas_width, canvas_height, default_svg, template_data, status, sort_order) 
                        VALUES (?, ?, ?, ?, ?, ?, 850, 500, ?, ?, 1, ?)
                        ON DUPLICATE KEY UPDATE default_svg=VALUES(default_svg), industry_slug=VALUES(industry_slug), industry_id=VALUES(industry_id)");

$totalCreated = 0;
foreach ($industries as $ind) {
    $indSlug = $ind['slug'];
    $indName = $ind['name'];
    $indId = $indMap[$indSlug] ?? 0;
    $iconSvg = $icons[$indSlug] ?? $icons['genel-kurumsal'];
    $indPalettes = $palettes[$indSlug] ?? $palettes['genel-kurumsal'];

    for ($i = 0; $i < 5; $i++) {
        $st = $styleTypes[$i];
        $stName = $styleNames[$st];
        $palette = $indPalettes[$i];
        
        $title = $ind['name'] . ' – ' . $stName . ' Tema ' . ($i + 1);
        $slug = Helper::slugify($title);
        $tagText = mb_strtoupper($ind['desc'], 'UTF-8');
        if (mb_strlen($tagText, 'UTF-8') > 30) {
            $tagText = mb_substr($tagText, 0, 28, 'UTF-8') . '...';
        }

        $svg = createSvgTemplate($st, $palette, $iconSvg, $tagText, $indName);

        $insTpl->execute([
            $firstProdId,
            $title,
            $slug,
            $stName,
            $indSlug,
            $indId,
            $svg,
            $templateFieldsJson,
            $i + 1
        ]);
        $totalCreated++;
    }
}

echo "5. Toplam {$totalCreated} adet profesyonel sektör SVG şablonu veritabanına başarıyla yüklendi.\n";
echo "=== SEKTÖR & ŞABLON SİSTEMİ KURULUMU TAMAMLANDI ===\n";
