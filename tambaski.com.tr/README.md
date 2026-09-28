# BaskıMatbaa – Online Matbaa, Web-to-Print & E-Ticaret Platformu

Paylaşımlı hosting (cPanel, Plesk, DirectAdmin, LiteSpeed, Apache) ortamlarında sıfır derleme adımıyla, doğrudan FTP ile yüklenip çalışacak şekilde **PHP (OOP/PDO) + MySQL + HTML5 + CSS (Apple Sadeliğinde UI) + Vanilla JS / jQuery** mimarisiyle inşa edilmiştir.

---

## 🚀 Öne Çıkan Özellikler

1. **Apple Sadeliğinde Modern & Güven Veren Arayüz:**
   - SF Pro / Modern tipografi, frosted-glass header, temiz kartlar ve mikro-animasyonlar.
   - 24 saatte Acil Baskı rozetleri, mobil alt navigasyon barı ve canlı WhatsApp iletişim butonu.

2. **İnteraktif Matbaa Fiyat & Varyant Hesaplayıcı:**
   - Kağıt türü (350gr Mat Kuşe, Bristol, Tuale Dokulu vb.), selefon/laminasyon (Mat, Parlak, Kadife Soft-Touch), köşe kesimleri ve özel ebat (cm x cm / metrekare) hesaplama motoru.
   - Adet kademeleri (100, 250, 500, 1.000, 2.000...) ile tiraj arttıkça dinamik düşen birim fiyat hesaplama.

3. **3 Yönlü Tasarım & Web-to-Print İş Akışı:**
   - **1. Kendi Tasarımımı Yükle:** Sürükle-bırak ile büyük boyutlu (150 MB'a kadar) PDF, AI, PSD, CDR, EPS, TIFF, ZIP dosya yükleyicisi.
   - **2. Hazır Şablonu Düzenle (Vektörel):** Müşteri hazır temalardan birini seçer (Tema 1, Tema 2 vb.), ad-soyad, unvan, telefon, şirket, adres gibi alanları canlı değiştirir.
   - **3. Tasarım Desteği İstiyorum:** Grafik destek ücreti ve müşteri talep notu ekleme.

4. **Yönetim Paneli & Vektörel Baskı Çıktısı (SVG Download):**
   - Admin panelinde sipariş detayında müşterinin canlı hazırladığı tasarım **Vektörel SVG** olarak tek tıkla indirilir. Adobe Illustrator, CorelDraw veya RIP yazılımlarında doğrudan baskıya gönderilebilir.
   - Müşterinin yüklediği PDF/AI dosyaları da tek tıkla indirilebilir.

5. **AI Ajanı & Entegrasyonlar İçin REST API:**
   - AI araçları (ChatGPT, Claude, Gemini, Cursor) için `/api/v1/` endpoint'i ve otomatik keşif şeması (`/api/v1/index.php`).
   - AI'a sadece API anahtarınızı ve ürün talimatını verdiğinizde, AI tek bir JSON isteği ile **Ürün + Kategori + Varyantlar + Fiyat Matrisi + Vektörel SVG Şablonlarını** otomatik olarak oluşturur!
   - Admin panelinde kopyalanabilir hazır sistem promptu mevcuttur.

6. **Ödeme & Kargo Entegrasyonları:**
   - **PayTR:** iFrame POS token üretimi ve otomatik onaylı webhook callback.
   - **iyzico:** 3D Secure checkout form ve sonuç doğrulama.
   - **Banka Havalesi / EFT:** Otomatik hesap bilgileri sunumu.
   - **Kargo Anlaşmaları:** Yurtiçi, Aras, MNG, Sürat, PTT, HepsiJET, Sendeo otomatik takip linki oluşturucu.

7. **B2B / E-Bayi & Kampanya Sistemi:**
   - Ajanslar ve matbaacılar için özel iskonto oranları (%10, %15, %20, %25 vb.).
   - Kupon kodları, sepet indirim kuralları ve "750 ₺ Üzeri Ücretsiz Kargo" ilerleme çubuğu.

---

## 🛠️ Kurulum Adımları (5 Dakikada Canlıya Alma)

### 1. Dosyaları Sunucuya Yükleyin
Tüm proje klasörünü FTP (FileZilla vb.) veya cPanel Dosya Yöneticisi ile `public_html` (veya ilgili dizine) yükleyin.

### 2. Veritabanını İçe Aktarın
- cPanel / Plesk üzerinden bir MySQL veritabanı ve kullanıcısı oluşturun.
- phpMyAdmin'e girip projedeki `database.sql` dosyasını içe aktarın (Import).

### 3. Veritabanı Bilgilerini Girin
`config/config.php` dosyasını açıp veritabanı bilgilerinizi güncelleyin:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'veritabani_adiniz');
define('DB_USER', 'kullanici_adiniz');
define('DB_PASS', 'sifreniz');
```

### 4. Admin Paneline Giriş Yapın
- **Admin Giriş Adresi:** `https://siteniz.com/login.php`
- **E-Posta:** `admin@matbaa.com`
- **Şifre:** `123456`

---

## 🤖 AI Ajanına Ürün Ekletme Talimatı

Admin panelindeki **"AI & REST API Yönetimi"** sayfasından API Anahtarınızı kopyalayın ve ChatGPT / Claude / Gemini'ye şu promptu iletin:

> *"Sen bir Matbaa & E-Ticaret Uzmanısın. Sitemin API Base URL'i: `https://siteniz.com/api/v1` ve X-API-Key: `baski_key_...`. Şimdi bana 3 farklı kağıt türü, altın/gümüş yaldız seçenekleri ve 2 adet modern hazır SVG kartvizit şablonu olan 'Lüks Varak Kartvizit' ürününü `/api/v1/products.php` endpointine POST ederek ekle."*

AI, şemayı okuyup tüm varyantları ve hazır şablonları saniyeler içinde mağazanıza yükleyecektir!
