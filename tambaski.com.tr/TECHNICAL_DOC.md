# 📖 TAMBASKI.COM.TR - TEKNİK DOKÜMAN

## 1. Proje Özeti
- **Alan Adı:** `tambaski.com.tr`
- **Tasarım Dili:** Apple Clean UI / Bootstrap 5 / Modern Dark Header
- **Mobil Deneyim:** PWA (Progressive Web App - Service Worker + Manifest)
- **Tasarım & Mockup Motoru:** Fabric.js Canlı Vektörel Editör + 3D Perspektif Mockup Önizleme
- **Resmi Mockup Kaynağı:** [Mockups-Design Free Mockups](https://mockups-design.com/free-mockups/) (Kartvizit, Bayrak, Kupa, Tabela, Kutu & Broşür Mockupları)
- **Tür:** E-Ticaret / Online Matbaa, Dekota & Pleksi Kesim, Folyo, Branda ve Promosyon Platformu

## 2. Dizin ve Dosya Yapısı
```text
tambaski.com.tr/
├── config/
│   ├── app.php                # Site sabitleri, kargo limitleri, flash mesajlar
│   └── db.php                 # PDO MySQL bağlantısı ve ayarları
├── includes/
│   ├── header.php             # Modern Apple header, PWA meta etiketleri, kategori menüsü, sepet rozeti
│   ├── footer.php             # Footer bağlantıları, iletişim, kargo & ödeme ikonları
│   └── functions.php          # Hibrit Fiyat Motoru (Paket, Özel Adet, m² Hesaplama), sepet ve veri yardımcıları
├── assets/
│   ├── css/
│   │   └── style.css          # Apple tarzı şık UI, 3D mockup gölgeleri, kartlar, butonlar
│   ├── js/
│   │   └── main.js            # Canlı hesaplayıcı, paket seçici, dosya yükleme yöneticisi
│   └── img/
│       └── logo.svg           # Orijinal TamBaskı vektörel SVG logosu
├── database/
│   ├── schema.sql             # Tablo yapıları (ürünler, hazır paketler, siparişler, kullanıcılar)
│   └── seed.sql               # Geniş ürün yelpazesi, kategoriler ve sabit paket fiyatları
├── uploads/
│   ├── designs/               # Yüklenen müşteri tasarım dosyaları (PDF, AI, PSD, CDR, TIFF)
│   └── products/              # Ürün kapak ve galeri görselleri
├── admin/
│   ├── auth_check.php         # Güvenlik ve oturum kontrolü
│   ├── login.php              # Şifreli admin giriş ekranı
│   ├── logout.php             # Güvenli çıkış işlemi
│   ├── header.php             # Sol sabit sidebar navigasyon şablonu
│   ├── footer.php             # Admin ortak altbilgi ve sidebar scripti
│   ├── index.php              # Sipariş yönetimi, tasarım indirme, ciro ve durum takibi
│   ├── products.php           # Tüm ürünler listesi, filtreleme ve durum yönetimi
│   ├── product_add.php        # Kapsamlı ürün, video, hazır paket, özel adet, varyant ve mockup ekleme
│   ├── mockups.php            # 3D Mockup şablonları yönetimi (mockups-design.com kaynaklı)
│   ├── categories.php         # Kategori yönetimi
│   ├── dealers.php            # E-Bayi ve B2B ajans başvuru onayı
│   ├── campaigns.php          # İndirim kuponları, otomatik sepet kurguları, duyuru bandı
│   └── settings.php           # Telefon, WhatsApp, adres, PayTR, banka hesapları, analitik
├── manifest.json              # PWA Manifest dosyası
├── sw.js                      # PWA Service Worker (çevrimdışı önbellekleme)
├── designer.php               # Standalone "Kendin Tasarla" & Canlı 3D Mockup editörü
├── index.php                  # Anasayfa (Hero slider, çok satanlar, kategori grid, B2B alanı)
├── category.php               # Kategori ve arama filtreleme sayfası
├── product.php                # Gelişmiş ürün sayfası, 3D interaktif kart, entegre tasarımcı ve hesaplayıcı
├── cart.php                   # Alışveriş sepeti, kargo tamamlama barı
├── checkout.php               # Teslimat & fatura adresi, PayTR / EFT ödeme akışı
├── order_tracking.php         # Canlı kargo ve üretim aşaması takip ekranı
├── dealer_apply.php           # %25 iskontolu B2B E-Bayilik başvuru ekranı
├── PROJECT_STATE.md           # Canlı kilit ve durum takibi
├── ACTIVITY_LOG.md            # Yapılan işlemler günlüğü
├── ARCHITECTURE.md            # Mermaid canlı mimari şeması
└── TECHNICAL_DOC.md           # Teknik dizin kılavuzu
```

## 3. Yönetim Paneli Giriş Bilgileri
- **Giriş URL:** `/admin/login.php`
- **Kullanıcı Adı:** `admin` (veya `arif`)
- **Şifre:** `admin123` (veya `tambaski2026`)
