# 📐 TamBaskı - Sistem ve Mimari Şeması (Architecture & Project Blueprint)

Bu belge, **TamBaskı** e-ticaret ve online matbaa platformunun tüm mimarisini, veri akışını, dosya yapısını ve iş mantığını içermektedir. Projede çalışan geliştiriciler ve **Antigravity AI Ajanları** için merkezi kılavuzdur.

---

## 🌐 1. Proje Genel Bakış & Vizyon
* **Proje Adı:** TamBaskı (Online Matbaa & E-Ticaret Platformu)
* **Canlı Demo Adresi:** `http://baski.arifuz.com.tr`
* **Test Ürünü URL:** `http://baski.arifuz.com.tr/product.php?slug=test`
* **Ana Hedef:** Kullanıcılara matbaa ürünlerinde (kartvizit, broşür, bayrak, etiket vb.) canlı dinamik fiyatlandırma, 4 farklı yöntemle tasarım oluşturma (TamBaskı Vektör Editörü, Yapay Zeka AI Logo, Hazır Dosya Yükleme, Profesyonel Tasarım Desteği) ve sipariş tamamlama altyapısı sunmak.

---

## 🛠️ 2. Teknoloji Yığını (Tech Stack)
| Katman | Teknolojiler |
| :--- | :--- |
| **Backend** | PHP 8.x (OOP, PDO Veritabanı Sürücüsü, Güvenli Session Yönetimi) |
| **Veritabanı** | MySQL / MariaDB (İlişkisel Matbaa ve Ürün Tabloları, `database.sql`) |
| **Frontend** | HTML5, CSS3, JavaScript (ES6+), Bootstrap 5.3, Bootstrap Icons |
| **Vektör & Tasarım Motoru** | [Fabric.js v5.3](file:///c:/Users/Arif/Downloads/eticaret1/assets/js/fabric.min.js), [ImageTracer.js](file:///c:/Users/Arif/Downloads/eticaret1/assets/js/imagetracer.js) (Raster -> SVG Vektör Dönüştürücü) |
| **Bileşenler & UI** | SweetAlert2, 3D CSS Preserve-3D Mockup Renderer |
| **Ödeme Entegrasyonları** | Iyzico Checkout Form / API, PayTR İframe / API, Havale / EFT |
| **Dağıtım (DevOps)** | Python cPanel UAPI Dağıtım Betiği (`deploy_cpanel.py`) |

---

## 📁 3. Dizin ve Dosya Yapısı

```text
eticaret1/
│
├── ARCHITECTURE.md                  # 📌 Bu mimari ve sistem şeması dosyası
├── index.php                        # 🏠 Anasayfa (Kategoriler, Popüler Ürünler, Hızlı Hesaplayıcı)
├── product.php                      # 🛍️ Detaylı Ürün Sayfası, Canlı Hesaplayıcı & TamBaskı Editör Modalı
├── cart.php                         # 🛒 Sepet Sayfası ve Tasarım Önizlemeleri
├── checkout.php                     # 💳 Sipariş & Fatura Bilgileri Giriş Ekranı
├── payment.php                      # 🔒 Ödeme Ağ Geçidi (Iyzico / PayTR / Havale)
├── success.php                      # ✅ Başarılı Sipariş Sonuç Ekranı
├── account.php                      # 👤 Müşteri & Bayi Profil / Sipariş Takip Paneli
├── dealer_apply.php                 # 🏢 B2B Bayilik Başvuru Formu
├── order_tracking.php               # 📦 Misafir Sipariş Takip Sayfası
├── login.php / register.php         # 🔑 Müşteri Giriş & Kayıt Sayfaları
├── deploy_cpanel.py                 # 🚀 Tek Tuşla Canlı Sunucuya Dağıtım Betiği
├── database.sql                     # 🗄️ MySQL Veritabanı Şeması ve Başlangıç Verileri
│
├── classes/                         # 🧠 Backend Çekirdek Sınıfları (OOP)
│   ├── Database.php                 # PDO Singleton Veritabanı Bağlantısı
│   ├── Product.php                  # Ürün, Kağıt, Matbaa Varyant ve Fiyat Hesaplama Motoru
│   ├── Cart.php                     # Sepet İşlemleri, Tasarım Eşleme ve B2B/B2C İndirimleri
│   ├── Order.php                    # Sipariş Oluşturma, Stok & Durum Yönetimi
│   ├── Auth.php                     # Kullanıcı ve Bayi Yetkilendirme / Güvenlik
│   ├── Helper.php                   # Fiyat formatlama, SEO URL, JSON ve yardımcı fonksiyonlar
│   ├── Iyzico.php                   # Iyzico API Ödeme Entegrasyon Sınıfı
│   ├── PayTR.php                    # PayTR Token & Callback Sınıfı
│   └── Cargo.php                    # Desi & Ağırlık Bazlı Kargo Hesaplama
│
├── config/
│   └── config.php                   # ⚙️ Sistem Ayarları, DB Bilgileri, Sabitler ve SITE_URL
│
├── admin/                           # 👑 Gelişmiş Yönetim Paneli
│   ├── index.php                    # Admin Dashboard (Ciro, Sipariş Grafikleri, İstatistikler)
│   ├── products.php                 # Ürün Yönetimi (Gelişmiş Form & Hızlı Oluşturucu)
│   ├── pricing_engine.php           # 🖨️ Matbaa Fiyatlandırma Motoru & Formül Yönetimi
│   ├── orders.php                   # Sipariş Yönetimi (Tasarım SVG/JSON İndirme, Üretim Durumları)
│   ├── templates.php                # Hazır Şablon Kütüphanesi Yönetimi
│   ├── variants.php                 # Varyant ve Ek Seçenek Grupları (Kesim, Selefon, Yaldız)
│   ├── categories.php               # Kategori ve Alt Kategori Yönetimi
│   ├── dealers.php                  # B2B Bayilik Onay ve Komisyon Ayarları
│   └── payment_settings.php         # Ödeme Ağ Geçidi Ayarları
│
├── api/                             # ⚡ Asenkron AJAX Endpoint'leri
│   ├── calculate_price.php          # Anlık Ürün Fiyatı Hesaplama API
│   ├── cart_actions.php             # Sepete Ekle / Sil / Güncelle
│   ├── upload_design.php            # Kullanıcı Tasarım / Logo Yükleme API
│   ├── get_industry_recommendations.php # Sektöre Göre Şablon & Ürün Tavsiye API
│   └── v1/                          # REST API v1 Servisleri
│
├── assets/                          # 🎨 Statik Dosyalar (JS / CSS / Şablonlar)
│   ├── js/
│   │   ├── canva_studio.js          # 🌟 TamBaskı Vektörel Tasarım Editörü Motoru (Fabric.js)
│   │   ├── canva_templates_engine.js# 📚 1.800+ Sektörel Vektör Şablon Kütüphanesi & Render
│   │   ├── calculator.js            # 🧮 Canlı Fiyat Hesaplayıcı, Form Senkronizasyonu & İnce Ayar
│   │   ├── ai_vector.js             # 🤖 AI Prompt ile Logo Üretimi & Vektörizasyon
│   │   ├── fabric.min.js            # Fabric.js Canvas Kütüphanesi
│   │   └── imagetracer.js           # Görüntüden Vektöre Trace Motoru
│   └── css/
│       └── style.css                # Genel Stil Tanımları, UI İyileştirmeleri & Animasyonlar
│
└── includes/
    ├── header.php                   # Genel Üst Menü, Sepet Özeti, PWA Tanımları
    └── footer.php                   # Genel Alt Menü, İletişim, WhatsApp Butonu
```

---

## 🖨️ 4. Temel Modüller ve Çalışma Mantığı

### 1. Matbaa Fiyat Hesaplama Motoru (`pricing_engine.php` & `Product.php`)
Matbaacılıkta fiyat sadece adetle çarpılmaz. Sistem şu 5 ana faktöre göre gerçek zamanlı dinamik maliyet hesaplar:
1. **Kağıt ve Ebat Maliyeti:** Standart ebatlar veya özel en/boy ($\text{cm}$) girildiğinde $\text{m}^2$ ve 70x100 tabaka kullanım payı hesaplanır.
2. **Baskı Paketi (Tier):** Ekonomik (Tek Yön), Standart (Çift Yön), Premium (Lüks Selefon / Lak), VIP (Altın Yaldız).
3. **Adet Kademeleri (Quantity Tiers):** 1.000, 2.000, 5.000 gibi adetlerde birim maliyet düşer; özel adet girildiğinde dinamik ölçekleme devreye girer.
4. **Matbaa Varyantları (Finishing):**
   - *Yüzde Bazlı:* Örn: Özel Kesim (+%15)
   - *Birim Başına:* Örn: Kabartma Lak (+0.12 ₺/adet)
   - *Tabaka Başına:* Örn: Yaldız Baskı (+$45 / tabaka)
   - *Sabit Ücret:* Örn: Özel Bıçak Kalıbı (+350 ₺)
5. **Kargo & KDV:** Canlı hesaplamada kullanıcının kafa karışıklığını önlemek için **Net Fiyat (Kargo & KDV Hariç)** ve **Brüt Toplam** ayrılmıştır.

---

### 2. TamBaskı Tasarım Editörü (`canva_studio.js` & `canva_templates_engine.js`)
* **Tuval & Güvenli Alan:** Standart kartvizit (8.4 x 5.2 cm) veya özel ebat oranına göre piksel tuval oluşturulur. Kesim payı (Bleed) ve iç güvenli alan kılavuz çizgileri çizilir. **Nesneler güvenli alan dışına taşamaz (Fiziksel Clamp Kilit Motoru).**
* **Ön / Arka Yüz Desteği:** Tek tuşla `Ön Yüz` ve `Arka Yüz` geçişi yapılır. Her yüzün Fabric.js JSON ve SVG verisi bellekte ayrı saklanır. Paket Tek Yön ise arka yüz otomatik kilitlenir.
* **1.800+ Sektörel Şablon:** Hukuk, Mimarlık, Sağlık/Doktor, Lüks/VIP, Kurumsal, Teknoloji, Kafe/Gıda, Güzellik gibi 15 farklı sektöre özel vektör şablonlar anında filtrelenebilir.
* **40+ Vektörel SVG İkon:** WhatsApp, Telefon, Mail, Adres, Web, Instagram, Terazi, Baret, Stetoskop, Diş vb. anlık aranabilir ve tuvale eklenebilir.
* **3D Canlı Döner Mockup:** Ön ve arka yüz SVG çıktısı alınıp 3 boyutlu kartvizit sahnesinde canlı döndürülerek gerçekçi ışık efektleriyle önizlenir.
* **Mobil-First Ergonomi (Canva/Procreate Tarzı):** 
  - Geniş merkez tuval.
  - Kompakt tek satır mobil başlık.
  - Alt yüzen dock bar ve alttan açılan çekmece (slide-up sheet). Şablon/metin seçildiğinde çekmece otomatik kapanıp tam ekran tuvale döner.

---

### 3. Yapay Zeka AI Logo & Vektörizasyon (`ai_vector.js`)
* Kullanıcı prompt girerek ("Modern inşaat logosu, minimalist baret") AI ile vektör amblem üretir.
* Çıkan görseller `ImageTracer.js` ile saf SVG yollarına (`<path>`) dönüştürülür ve doğrudan TamBaskı editörüne aktarılır.

---

## 🚀 5. Canlıya Dağıtım (Deployment) Kuralları
Değişiklik yapıldığında canlı sunucuya aktarmak için:

```powershell
python deploy_cpanel.py
```
* Bu betik güncellenen dosyaları otomatik zip'ler, cPanel UAPI üzerinden sunucuya gönderir ve saniyeler içinde canlıya alır.

---

## 🤝 6. İki Kişi / Ortak Çalışma Rehberi (Team Collaboration)
Yan yana iki bilgisayardan çalışırken çakışmaları önlemek için iş bölümü tavsiyesi:

1. **Geliştirici A (Tasarım & Frontend):** `product.php`, `assets/js/canva_studio.js`, `assets/js/canva_templates_engine.js`, `assets/css/style.css`
2. **Geliştirici B (Yönetim, Sepet & Backend):** `admin/*`, `classes/*`, `cart.php`, `checkout.php`, `payment.php`, `api/*`
3. **Kural:** Aynı anda aynı dosya üzerinde değişiklik yapmaktan kaçının. İşlem tamamlandığında `python deploy_cpanel.py` ile canlıya aktarın.
