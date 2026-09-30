<?php
/**
 * TAMBASKI.COM.TR - Dinamik API Kural & Kategori Şeması Üreteci
 * 
 * Bu dosya canlı veritabanını tarar, tüm aktif kategorileri ve onların
 * hesaplama modellerini (package_tier, rigid_board, m2_calculator vb.) dinamik olarak
 * okur ve her zaman %100 güncel dokümantasyon üretir.
 * 
 * Formatlar:
 * - ?format=markdown  -> Antigravity ve LLM ajanları için optimize edilmiş canlı Markdown
 * - ?format=json      -> Programatik API / REST tüketicileri için JSON formatı
 * - ?format=download  -> Doğrudan MEMO_RULES.md olarak indirme başlatır
 */

require_once __DIR__ . '/../../config/config.php';

$db = Database::getInstance()->getConnection();
$format = $_GET['format'] ?? 'markdown';

// Aktif kategorileri veritabanından çek
$stmt = $db->query("SELECT id, name, slug, icon, pricing_model, sort_order FROM categories WHERE status = 1 ORDER BY sort_order ASC, id ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// API Bilgileri
$apiBaseUrl = SITE_URL . '/api/v1';
$sampleApiKey = 'tb_live_memo_7f9b2c4e1a8d5063';
$timestamp = date('Y-m-d H:i:s');

// JSON Formatı İstendiyse
if ($format === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success'      => true,
        'platform'     => 'TamBaskı E-Ticaret REST API',
        'generated_at' => $timestamp,
        'base_url'     => $apiBaseUrl,
        'auth'         => [
            'type'        => 'API Key',
            'header'      => 'X-API-KEY: <YOUR_API_KEY>',
            'bearer'      => 'Authorization: Bearer <YOUR_API_KEY>'
        ],
        'categories'   => array_map(function($c) {
            return [
                'id'            => (int)$c['id'],
                'name'          => $c['name'],
                'slug'          => $c['slug'],
                'pricing_model' => $c['pricing_model'] ?: 'package_tier',
                'icon'          => $c['icon']
            ];
        }, $categories),
        'endpoints'    => [
            'upload_image'    => ['method' => 'POST', 'url' => $apiBaseUrl . '/upload.php'],
            'list_categories' => ['method' => 'GET',  'url' => $apiBaseUrl . '/categories.php'],
            'list_products'   => ['method' => 'GET',  'url' => $apiBaseUrl . '/products.php'],
            'create_product'  => ['method' => 'POST', 'url' => $apiBaseUrl . '/products.php'],
            'update_product'  => ['method' => 'PUT',  'url' => $apiBaseUrl . '/products.php?id={id}'],
            'delete_product'  => ['method' => 'DELETE','url' => $apiBaseUrl . '/products.php?id={id}']
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// Markdown Oluşturma (İndirme veya Doğrudan Görüntüleme)
if ($format === 'download') {
    header('Content-Type: text/markdown; charset=utf-8');
    header('Content-Disposition: attachment; filename="MEMO_RULES.md"');
} else {
    header('Content-Type: text/markdown; charset=utf-8');
}

?>
# 🤖 TAMBASKI.COM.TR - MEMO ÜRÜN YÜKLEME VE YÖNETİM KURALLARI (ANTIGRAVITY SİSTEM İSTEMİ)

> ⚡ **BU BELGE CANLI SUNUCU TARAFINDAN OTOMATİK OLUŞTURULMUŞTUR.**
> 📅 **Son Senkronizasyon Zamanı:** <?= $timestamp ?>  
> 🌐 **API Base URL:** `<?= $apiBaseUrl ?>`  
> 🔑 **Yetkilendirme:** Tüm isteklerde `X-API-KEY: <?= $sampleApiKey ?>` header'ı zorunludur.

---

## 🛑 AI AJAN VE ROL SINIRI (KESİNLİKLE UYULACAK KURAL)
1. Sen **Memo'nun Ürün ve İçerik Yöneticisi Asistanısın**.
2. Doğrudan yerel PHP/HTML/CSS dosyalarına veya veritabanına kod yazma, düzenleme veya silme **YETKİN YOKTUR**.
3. Tüm ürün ekleme, fiyatlandırma, görsel bağlama ve içerik girişlerini **SADECE bu kılavuzda tanımlanan REST API uç noktaları üzerinden** yapacaksın.
4. **SOHBET BAŞLANGIÇ PROTOKOLÜ (OTOMATİK VE SESSİZ):**
   - Kullanıcı (Memo) sana bir ürün veya kategori yükleme talimatı verdiğinde:
   - Kullanıcıya **ASLA** "dokümanı incele", "kurallara bakayım", "API'yi aç" **DEME**.
   - Her işlemden önce güncel kategorileri ve modelleri teyit etmek için sessizce arka planda:
     `GET <?= $apiBaseUrl ?>/categories.php` veya `GET <?= SITE_URL ?>/docs/api/rules.json`
     çağrısı yapıp kategorileri doğrula ve doğrudan ürünleri yüklemeye başla!

---

## 🏷️ SİSTEMDE ŞU ANDA AKTİF OLAN KATEGORİLER VE MODELLERİ

Aşağıdaki tablo canlı veritabanından çekilmiştir. Arif yeni bir kategori açtığında bu liste anında güncellenir:

| ID | Kategori Adı | Slug | Hesaplama Modeli | Açıklama |
| :---: | :--- | :--- | :--- | :--- |
<?php foreach ($categories as $cat): ?>
| **<?= $cat['id'] ?>** | <?= htmlspecialchars($cat['name']) ?> | `<?= $cat['slug'] ?>` | `<?= $cat['pricing_model'] ?: 'package_tier' ?>` | <?php
    switch ($cat['pricing_model']) {
        case 'rigid_board':
            echo '🛡️ Dekota / Levha: USD m² fiyatı (3mm/5mm/9mm), montaj ve ebat motoru.';
            break;
        case 'm2_calculator':
            echo '📐 Folyo / Sticker: m² başına dinamik fiyat hesaplama.';
            break;
        case 'tiered_qty':
            echo '🎁 Promosyon: Kademeli parça başı adet iskontosu.';
            break;
        default:
            echo '🎨 Paket & Tiraj: 4 Hazır Paket (Ekonomik, Standart, Premium, VIP) + 5 Tiraj İndirimi.';
            break;
    }
?> |
<?php endforeach; ?>

---

## 🔌 1. ADIM: GÖRSEL / MOCKUP YÜKLEME API'Sİ

Ürünü oluşturmadan önce görselini sisteme yükleyin. API görseli otomatik WebP'ye dönüştürür ve optimize eder.

- **Uç Nokta:** `POST <?= $apiBaseUrl ?>/upload.php`
- **Header:** `X-API-KEY: <?= $sampleApiKey ?>`

### Seçenek A: Doğrudan Dosya Yükleme (`multipart/form-data`)
```bash
curl -X POST "<?= $apiBaseUrl ?>/upload.php" \
  -H "X-API-KEY: <?= $sampleApiKey ?>" \
  -F "file=@ornek_gorsel.jpg"
```

### Seçenek B: Harici URL veya Base64 ile Yükleme (`application/json`)
```json
{
  "image_url": "https://example.com/gorseller/dekota_baret_levhasi.jpg"
}
```

### Dönen Başarılı Yanıt:
```json
{
  "success": true,
  "file_path": "uploads/products/20260930_isg_levha.webp",
  "url": "<?= SITE_URL ?>/uploads/products/20260930_isg_levha.webp"
}
```
> 📌 *Dönen `file_path` değerini aşağıdaki ürün ekleme isteğinde `featured_image` veya `mockup_image` olarak kullanacaksınız.*

---

## 📦 2. ADIM: YENİ ÜRÜN EKLEME API'Sİ

- **Uç Nokta:** `POST <?= $apiBaseUrl ?>/products.php`
- **Header:** `X-API-KEY: <?= $sampleApiKey ?>`
- **Content-Type:** `application/json; charset=utf-8`

### MODEL 1: KARTVİZİT / STANDART MATBAA (`package_tier`)
4 Paket (Ekonomik, Standart, Premium, VIP) ve 5 Sabit Tiraj (1.000, 2.000, 3.000, 5.000, 10.000):

```json
{
  "category_id": 2,
  "name": "Soft Touch Kabartma Laklı VIP Kartvizit",
  "short_description": "350gr mat kuşe, kadife dokulu Soft Touch selefon ve lokal kabartma parlak laklı prestij kartvizit.",
  "full_description": "<p>Kurumsal kimliğinizi zirveye taşıyan özel dokulu matbaa üretimi.</p>",
  "featured_image": "uploads/products/kartvizit_lakli.webp",
  "mockup_image": "uploads/mockups/tambaski_kartvizit_desk_mockup.jpg",
  "base_price": 950.00,
  "tax_rate": 20.00,
  "packages": {
    "ekonomik": {
      "name": "Ekonomik",
      "price": 850.00,
      "desc": "250gr Bristol, Tek Yön Renkli",
      "badge": "Fırsat"
    },
    "standart": {
      "name": "Standart",
      "price": 950.00,
      "desc": "350gr Kuşe, Çift Yön Mat Selefon",
      "badge": "En Çok Satan"
    },
    "premium": {
      "name": "Premium",
      "price": 1450.00,
      "desc": "Soft Touch Kadife Selefon & Kabartma Lak",
      "badge": "Özel Doku"
    },
    "vip": {
      "name": "VIP Prestij",
      "price": 1850.00,
      "desc": "Tuale Fantezi / Altın Varak Yaldız",
      "badge": "Lüks Seri"
    }
  },
  "tiers": {
    "1000": 0,
    "2000": 15,
    "3000": 25,
    "5000": 35,
    "10000": 45
  },
  "allow_online_editor": 1,
  "allow_design_upload": 1,
  "allow_design_service": 1,
  "design_service_price": 150.00,
  "is_featured": 1,
  "status": 1
}
```

---

### MODEL 2: DEKOTA & SERT ZEMİN İSG LEVHALARI (`rigid_board`)
Dolar m² Fiyat Motoru + Kalınlık Farkları (3mm, 5mm, 9mm) + Montaj Opsiyonları:

```json
{
  "category_id": 1,
  "name": "Baret Takmak Mecburidir Dekota İSG Levhası",
  "short_description": "İç ve dış mekan şartlarına dayanıklı, solmaz 300 DPI endüstriyel UV baskılı sert dekota ikaz levhası.",
  "full_description": "<p>6331 sayılı İSG mevzuatına tam uyumlu piktogramlı güvenlik levhası.</p>",
  "featured_image": "uploads/products/isg_baret_levha.webp",
  "mockup_image": "uploads/mockups/tambaski_dekota_mockup.jpg",
  "base_price": 95.00,
  "tax_rate": 20.00,
  "m2_usd_price_3mm": 14.50,
  "m2_usd_price_5mm": 18.50,
  "m2_usd_price_9mm": 26.00,
  "allow_online_editor": 1,
  "allow_design_upload": 1,
  "is_featured": 1,
  "status": 1
}
```

---

## 🔍 3. ADIM: MEVCUT ÜRÜNLERİ LİSTELEME VE DÜZENLEME

### Ürünleri Listele:
- `GET <?= $apiBaseUrl ?>/products.php` (Tüm ürünler)
- `GET <?= $apiBaseUrl ?>/products.php?category_id=1` (Sadece Dekota ürünleri)
- `GET <?= $apiBaseUrl ?>/products.php?id=42` (ID'si 42 olan ürünün tüm detayları)

### Ürün Güncelle:
- **Uç Nokta:** `PUT <?= $apiBaseUrl ?>/products.php?id={product_id}`
- **Body:** Güncellenecek alanları içeren JSON (Örn: sadece `"base_price": 1100.00` veya açıklama).

### Ürün Silme / Pasife Alma:
- **Uç Nokta:** `DELETE <?= $apiBaseUrl ?>/products.php?id={product_id}`

---

## 💡 MEMO İÇİN ANTIGRAVITY PROMPT ÖRNEKLERİ

Memo kendi bilgisayarında Antigravity'ye sadece şunu söyleyecektir:

> *"Klasördeki 5 adet dekota görselini al, sisteme yükle ve her biri için 'Yangın Tüpü', 'Acil Çıkış', 'Toplanma Alanı' başlıklarıyla 3mm USD 14.50, 5mm USD 18.50 m² fiyatıyla TamBaskı Dekota kategorisine API üzerinden ekle."*

Antigravity bu dokümandaki kuralları bildiği için başka hiçbir şey sormadan doğrudan API çağrılarını yapıp ürünleri canlıya alacaktır.
