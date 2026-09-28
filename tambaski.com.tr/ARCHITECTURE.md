# 🏗️ TAMBASKI.COM.TR - MİMARİ VE CANLI ŞEMA

```mermaid
flowchart TD
    subgraph Frontend ["🎨 Kullanıcı Arayüzü (Apple Clean UI)"]
        Home["🏠 index.php (Hero Slider, Popüler Kategoriler, Çok Satanlar)"]
        Categories["📂 category.php (Kategori & Filtreli Arama)"]
        ProductPage["🎛️ product.php (İnteraktif Fiyat & Paket & m² Hesaplayıcı)"]
        CartPage["🛒 cart.php (Alışveriş Sepeti & Kargo Barı)"]
        CheckoutPage["💳 checkout.php (Adres, Fatura & Güvenli Ödeme)"]
        OrderTracking["🚚 order_tracking.php (Canlı Kargo & Baskı Takibi)"]
        DealerPortal["🤝 dealer_apply.php (B2B E-Bayilik & %25 İskonto)"]
    end

    subgraph PricingEngine ["🧮 Hibrit Fiyat Motoru (functions.php)"]
        PkgCalc["📦 Standart Hazır Paket Fiyatlayıcı (Sabit Panel Fiyatları)"]
        CustomQtyCalc["🔢 Özel / Ara Adet Hesaplayıcı (Taban + Birim x Katsayı)"]
        SqmCalc["📐 m² Boyut Hesaplayıcı (Dekota, Pleksi Kesim, Folyo, Branda)"]
    end

    subgraph AdminPanel ["⚙️ Admin Yönetim Paneli (admin/)"]
        AdminAuth["🔒 login.php & auth_check.php (Güvenli Şifreli Giriş)"]
        AdminDashboard["📊 index.php (Siparişler & Tasarım İndirme)"]
        CampaignManager["🎟️ campaigns.php (Kuponlar, Otomatik Sepet İndirimi & Flash Bar)"]
        SettingsManager["⚙️ settings.php (İletişim, WhatsApp, Harita, PayTR, Banka Hesapları)"]
    end

    subgraph Storage ["🗄️ Veri & Depolama"]
        DB[("🗄️ MySQL Veritabanı (schema.sql & seed.sql)")]
        Uploads[("📁 uploads/designs (Müşteri Tasarım Dosyaları)")]
    end

    Home --> Categories
    Categories --> ProductPage
    ProductPage --> PricingEngine
    ProductPage --> CartPage
    CartPage --> CheckoutPage
    CheckoutPage --> DB
    AdminAuth --> AdminDashboard
    AdminDashboard --> CampaignManager
    AdminDashboard --> SettingsManager
    AdminDashboard --> DB
    AdminDashboard --> Uploads
```
