# 📖 TAMBASKI.COM.TR - TEKNİK DOKÜMAN

## 1. Proje Özeti
- **Alan Adı:** `tambaski.com.tr`
- **Tasarım Dili:** Apple Clean UI / Bootstrap 5 / Modern Dark Header
- **Tür:** E-Ticaret / Online Matbaa, Dekota & Pleksi Kesim, Folyo, Branda ve Promosyon Platformu

## 2. Dizin ve Dosya Yapısı
```text
tambaski.com.tr/
├── config/
│   ├── app.php                # Site sabitleri, kargo limitleri, flash mesajlar
│   └── db.php                 # PDO MySQL bağlantısı ve ayarları
├── includes/
│   ├── header.php             # Modern Apple header, kategori dropdown, arama, sepet rozeti
│   ├── footer.php             # Footer bağlantıları, iletişim, kargo & ödeme ikonları
│   └── functions.php          # Hibrit Fiyat Motoru (Paket, Özel Adet, m² Hesaplama), sepet ve veri yardımcıları
├── assets/
│   ├── css/
│   │   └── style.css          # Apple tarzı şık UI, glowing animasyonlar, kartlar, butonlar
│   ├── js/
│   │   └── main.js            # Canlı hesaplayıcı, paket seçici, dosya yükleme yöneticisi
│   └── img/
│       └── logo.svg           # Orijinal TamBaskı vektörel SVG logosu
├── database/
│   ├── schema.sql             # Tablo yapıları (ürünler, hazır paketler, siparişler, kullanıcılar)
│   └── seed.sql               # Geniş ürün yelpazesi, kategoriler ve sabit paket fiyatları
├── uploads/
│   ├── designs/               # Yüklenen müşteri tasarım dosyaları (PDF, AI, PSD, CDR, TIFF)
│   └── products/              # Ürün kapak görselleri
├── admin/
│   ├── auth_check.php         # Güvenlik ve oturum kontrolü
│   ├── login.php              # Şifreli admin giriş ekranı
│   ├── logout.php             # Güvenli çıkış işlemi
│   ├── header.php             # Admin ortak navigasyon ve menü
│   ├── footer.php             # Admin ortak altbilgi
│   ├── index.php              # Sipariş yönetimi, tasarım indirme, ciro ve durum takibi
│   ├── campaigns.php          # İndirim kuponları, otomatik sepet kurguları, duyuru bandı
│   └── settings.php           # Telefon, WhatsApp, adres, PayTR, banka hesapları, analitik
├── index.php                  # Anasayfa (Hero slider, çok satanlar, kategori grid, B2B alanı)
├── category.php               # Kategori ve arama filtreleme sayfası
├── product.php                # İnteraktif ürün yapılandırıcı & canlı hesaplayıcı
├── cart.php                   # Alışveriş sepeti, kargo tamamlama barı
├── checkout.php               # Teslimat & fatura adresi, PayTR / EFT ödeme akışı
├── order_tracking.php         # Canlı kargo ve üretim aşaması takip ekranı
├── dealer_apply.php           # %25 iskontolu B2B E-Bayilik başvuru ekranı
├── PROJECT_STATE.md           # Canlı kilit ve durum takibi
├── ACTIVITY_LOG.md            # Yapılan işlemler günlüğü
├── ARCHITECTURE.md            # Mermaid canlı mimari şeması
└── TECHNICAL_DOC.md           # Teknik dizin kılavuzu
```

## 3. Yönetim Paneli Giriş Bilgileri (Varsayılan)
- **Giriş URL:** `/admin/login.php`
- **Kullanıcı Adı:** `admin` (veya `arif`)
- **Şifre:** `admin123` (veya `tambaski2026`)
