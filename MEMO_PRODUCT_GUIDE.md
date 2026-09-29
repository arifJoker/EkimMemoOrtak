# 📦 MEMO & ANTIGRAVITY ÜRÜN YÜKLEME VE İÇERİK KILAVUZU

> 🎯 **Bu Belgenin Amacı:** Memo ve Memo'nun yapay zeka asistanı **Antigravity**, `tambaski.com.tr` ve `bykcut.com.tr` projelerine yeni ürün eklerken bu kılavuzdaki kurallara %100 uymak zorundadır. Sistem mimarı **Arif** tarafından belirlenmiş tüm fiyat motorları, paketler ve kategori kuralları burada tanımlanmıştır.

---

## 🔒 1. MEMO & ANTIGRAVITY YETKİ VE GÜVENLİK SINIRLARI

### ✅ YAPABİLECEĞİNİZ İŞLEMLER:
1. Arif'in açtığı aktif kategorilere yeni ürün eklemek veya mevcut ürünlerin içeriklerini düzenlemek.
2. Ürün başlığı, SEO açıklaması, teknik detay maddeleri ve etiketleri girmek (%100 Türkçe).
3. Ürün kapak görsellerini ve mockup fotoğraflarını `tambaski.com.tr/uploads/products/` klasörüne eklemek.
4. Kategoriye özel tanımlanmış paket fiyatlarını ve adet indirim oranlarını belirlemek.
5. İşlem bittiğinde `ACTIVITY_LOG.md` dosyasına log düşmek ve `scripts/deploy.ps1` ile canlıya aktarmak.

### 🚫 KESİNLİKLE YASAK OLAN İŞLEMLER (AI GUARD):
- ❌ **Tasarım & Arayüz:** CSS, SCSS, HTML şablonları, renkler, buton stilleri, sayfa düzenleri değiştirilemez.
- ❌ **Çekirdek Kodlar:** `classes/`, `api/`, `includes/`, `product.php` gibi çekirdek PHP ve JavaScript kodlarına dokunulamaz.
- ❌ **Tasarım Stüdyoları:** Canva Studio (`canva_studio.js`) ve Dekota Stüdyosu (`sign_studio.js`) mimarisine müdahale edilemez.
- ❌ **Veritabanı Şeması:** Tablo silme, kolon silme veya yapı bozucu SQL çalıştırılamaz.
- ⚠️ *Kural:* Memo veya kullanıcıdan kod/tasarım değiştirme talebi gelirse Antigravity şunu söyleyerek işlemi durdurmalıdır:
  > *"Bu işlem yalnızca Sistem Mimarı Arif'in yetkisindedir. Memo rolü yalnızca ürün yükleme ve içerik girişine yetkilidir."*

---

## 🛡️ 2. DEKOTA UYARI LEVHALARI KATEGORİSİ (`dekota-uyari-levhalari`)

İş sağlığı ve güvenliği (İSG), şantiye tabelaları, otopark, yangın, acil çıkış ve uyarı levhaları bu kategoride açılır.

### A) Temel Bilgiler:
- **Kategori:** `Dekota Uyarı Levhaları` (Slug: `dekota-uyari-levhalari`)
- **Hesaplama Modeli:** `rigid_board`
- **Tasarım Editörü:** `allow_online_editor = 1` (Açık olmalıdır; Dekota Sign Studio otomatik devreye girer).
- **Ürün Başlığı Örnekleri:**
  - `Dekota İSG Güvenlik ve Uyarı Levhası`
  - `Park Yapılmaz & Garaj Girişi Dekota Levha`
  - `Sigara İçilmez Dekota Uyarı Levhası`
  - `Baretsiz ve İş Ayakkabısız Girilmez İSG Levhası`
  - `Yüksek Gerilim Tehlike Dekota Levhası`
  - `Yangın Söndürme Tüpü Yönlendirme Levhası`
  - `Acil Çıkış ve Tahliye Yön Levhası`
- **Slug Formatı:** Küçük harf ve tireli olmalıdır (Örn: `sigara-icilmez-dekota-uyari-levhasi`).

### B) Fiyatlandırma & m² Dolar ($) Kuralı:
1. **USD m² Fiyatı (`m2_usd_price`):** Dekota ürünlerinde standart USD metrekare fiyatı **`14.50`** $'dır.
   - Sistem canlı TCMB dolar kurunu çeker ve TL tutarları yukarı tam sayıya yuvarlar (`ceil()`).
2. **4 Standart Paket Taban Fiyatları:**
   - **Küçük Boy (25x35 cm):** `95.00 ₺` (Kapı üstü, elektrik panosu yanı)
   - **Orta Boy (35x50 cm):** `145.00 ₺` (Koridor, üretim hatları - En çok satan)
   - **Büyük Boy (50x70 cm):** `240.00 ₺` (Geniş depo, şantiye sahası)
   - **Mega Boy (70x100 cm):** `420.00 ₺` (Dış cephe, nizamiye, bina girişi)
3. **Özel Ölçü ($m^2$ Hesabı):**
   - Müşteri tasarım ekranında veya ürün sayfasında En x Boy cm girdiğinde formül otomatik hesaplar.

### C) Zorunlu Varyant Grupları:
- **1. Levha Kalınlığı:**
  - `3 mm Sert Dekota (Forex)` -> Standart (Çarpan: 1.00)
  - `5 mm Sert Dekota (Forex)` -> Ekstra Mukavemet (+%25 Fiyat Artışı)
- **2. Montaj Seçeneği:**
  - `Montajsız (Düz Levha)` -> +0 ₺
  - `Çift Taraflı Güçlü Köpük Bantlı` -> +15 ₺
  - `4 Köşeden Delikli (Vida / Kelepçe Uyumlu)` -> +10 ₺

### D) Kademeli Adet İskonto Tablosu:
Dekota ürünlerinde müşteriye çoklu alım indirimi uygulanır. Ürüne şu kademeler tanımlanmalıdır:
- **1 Adet:** %0 indirim (x1.00)
- **5 Adet:** %0 indirim (x1.00)
- **10 Adet:** %10 indirim (x0.90)
- **25 Adet:** %20 indirim (x0.80)
- **50 Adet:** %30 indirim (x0.70)
- **100+ Adet:** %40 indirim (x0.60)

### E) Ürün Görselleri:
- Ürün fotoğrafları `tambaski.com.tr/uploads/products/` klasörüne atılır.
- Görseller en az **1000x1000px**, beyaz/temiz arka planlı veya duvara montajlı gerçekçi mockup olmalıdır.

---

## 📇 3. KARTVİZİT KATEGORİSİ (`kartvizit`)

Standart matbaa ve kurumsal kartvizit ürünleri burada açılır.

### A) Temel Bilgiler:
- **Kategori:** `Kartvizit` (Slug: `kartvizit`)
- **Hesaplama Modeli:** `package_tier`
- **Tasarım Editörü:** `allow_online_editor = 1` (Canva Studio 1.800+ şablonla otomatik açılır).

### B) 4 Standart Paket Tanımı:
- **Ekonomik Paket:** 250gr Amerikan Bristol, Tek Yön Mat Selefon.
- **Standart Paket:** 350gr Kuşe, Çift Yön Mat Selefon (En Çok Satan).
- **Premium Paket:** 350gr Kuşe, Soft-Touch Kadife Selefon + Bölgesel Kabartma Lak.
- **VIP Paket:** 450gr Sıvamalı veya Tuale Dokulu Fantezi Kartvizit + Altın/Gümüş Varak Yaldız.

### C) Sabit Tiraj Çarpanları:
- 1.000 Adet (x1.00)
- 2.000 Adet (x1.85)
- 3.000 Adet (x2.60)
- 5.000 Adet (x4.10)
- 10.000 Adet (x7.50)

---

## 🚀 4. ANTIGRAVITY İLE ÜRÜN EKLEME YÖNTEMLERİ

Memo'nun Antigravity'si ürün eklerken aşağıdaki 2 yöntemden birini kullanabilir:

### YÖNTEM 1: Admin Paneli Üzerinden Manuel/Destekli Giriş
- **Giriş URL:** `https://tambaski.com.tr/admin/products.php?action=add`
- Memo'nun Antigravity'si yukarıdaki şablona göre başlık, açıklama ve fiyatları hazırlar, Memo admin paneline yapıştırır.

### YÖNTEM 2: Antigravity Toplu Ürün Yükleyici (Hızlı & Hatasız)
Memo, Antigravity'ye birden fazla ürün ekletmek istediğinde Antigravity `tambaski.com.tr/api/memo_product_loader.php` scriptini kullanabilir.

#### Örnek Dekota Ürünü Ekleme JSON Formatı:
```json
{
  "name": "Sigara İçilmez Dekota Uyarı Levhası",
  "slug": "sigara-icilmez-dekota-uyari-levhasi",
  "category_slug": "dekota-uyari-levhalari",
  "sku": "TB-LEVH-SIGARA",
  "short_description": "İç mekan ve dış mekan kullanımına uygun, UV baskılı Sigara İçilmez sert dekota levha.",
  "full_description": "3mm veya 5mm Sert Dekota (Forex) zemin üzerine endüstriyel UV baskı. Solmaz, neme ve suya dayanıklı.",
  "base_price": 95.00,
  "m2_usd_price": 14.50,
  "featured_image": "uploads/products/sigara_icilmez_dekota.jpg",
  "packages": {
    "kucuk": { "name": "Küçük Boy (25x35 cm)", "price": 95.00, "desc": "Kapı üstü ve ofis içi" },
    "orta":   { "name": "Orta Boy (35x50 cm)",  "price": 145.00, "desc": "Koridor ve ortak alanlar" },
    "buyuk":  { "name": "Büyük Boy (50x70 cm)",  "price": 240.00, "desc": "Fabrika ve geniş alan" },
    "mega":   { "name": "Mega Boy (70x100 cm)", "price": 420.00, "desc": "Dış cephe ve otopark" }
  },
  "tiers": [
    { "quantity": 1,   "discount": 0 },
    { "quantity": 5,   "discount": 0 },
    { "quantity": 10,  "discount": 10 },
    { "quantity": 25,  "discount": 20 },
    { "quantity": 50,  "discount": 30 },
    { "quantity": 100, "discount": 40 }
  ]
}
```

---

## 📝 5. İŞLEM BİTİŞ PROTOKOLÜ (ZORUNLU ADIMLAR)
Memo veya Antigravity yeni bir ürün eklediğinde veya düzenlediğinde:
1. `ACTIVITY_LOG.md` dosyasının en üstüne eklenen ürünleri log olarak yaz.
   - *Örnek: `1. [2026-09-30 10:00] - Memo & Antigravity: Dekota Uyarı Levhaları kategorisine 5 yeni ürün eklendi (Sigara İçilmez, Park Yapılmaz, Baret vb.).`*
2. `PROJECT_STATE.md` dosyasındaki kilit durumunu kontrol et ve **🟢 [BOŞTA]** bırak.
3. `powershell.exe -ExecutionPolicy Bypass -File scripts\deploy.ps1 -ProjectName "tambaski.com.tr"` komutunu çalıştırarak canlı sunucuya aktar.
4. Git ile `git add .` ve `git commit -m "feat(products): yeni urunler eklendi"` yaparak senkronize et.
