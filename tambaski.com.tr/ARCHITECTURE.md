# 🏗️ TAMBASKI.COM.TR - MİMARİ VE CANLI ŞEMA

```mermaid
flowchart TD
    subgraph Frontend ["🎨 Kullanıcı Arayüzü & PWA (Apple Clean UI)"]
        Home["🏠 index.php (Hero Slider, Popüler Kategoriler, Çok Satanlar)"]
        Categories["📂 category.php (Kategori & Filtreli Arama)"]
        ProductPage["🎛️ product.php (İnteraktif Fiyat & Paket & m² Hesaplayıcı)"]
        DesignerPage["🖌️ designer.php (Canlı 'Kendin Tasarla' & 3D Mockup Simülatörü)"]
        CartPage["🛒 cart.php (Alışveriş Sepeti & Kargo Barı)"]
        CheckoutPage["💳 checkout.php (Adres, Fatura & Güvenli Ödeme)"]
        OrderTracking["🚚 order_tracking.php (Canlı Kargo & Baskı Takibi)"]
        DealerPortal["🤝 dealer_apply.php (B2B E-Bayilik & %25 İskonto)"]
        PWASystem["📱 manifest.json & sw.js (PWA Çevrimdışı & Mobil Kurulum)"]
    end

    subgraph PricingEngine ["🧮 Hibrit Fiyat Motoru (functions.php)"]
        PkgCalc["📦 Standart Hazır Paket Fiyatlayıcı (Sabit Panel Fiyatları)"]
        CustomQtyCalc["🔢 Özel / Ara Adet Hesaplayıcı (Taban + Birim x Katsayı)"]
        SqmCalc["📐 m² Boyut Hesaplayıcı (Dekota, Pleksi Kesim, Folyo, Branda)"]
    end

    subgraph AdminPanel ["⚙️ Admin Yönetim Paneli (Sol Sidebar Mimarisi)"]
        AdminAuth["🔒 login.php & auth_check.php (Güvenli Giriş)"]
        AdminDashboard["📊 index.php (Siparişler & İstatistikler)"]
        ProductManagement["📦 products.php & product_add.php (Görsel, Video, Varyant & Paketler)"]
        MockupManager["🎨 mockups.php (3D Kartvizit, Bayrak, Kupa, Tabela Mockupları)"]
        CategoryManager["🗂️ categories.php (Kategori & Ağaç Yönetimi)"]
        CampaignManager["🎟️ campaigns.php (Kuponlar, Otomatik Sepet İndirimi & Flash Bar)"]
        DealerManager["🤝 dealers.php (E-Bayi Başvuru Onayı)"]
        SettingsManager["⚙️ settings.php (İletişim, WhatsApp, Harita, PayTR, Banka)"]
    end

    subgraph Storage ["🗄️ Veri & Depolama"]
        DB[("🗄️ MySQL Veritabanı (schema.sql & seed.sql)")]
        Uploads[("📁 uploads/designs & uploads/products")]
    end

    Home --> Categories
    Categories --> ProductPage
    ProductPage --> DesignerPage
    DesignerPage --> CartPage
    ProductPage --> PricingEngine
    ProductPage --> CartPage
    CartPage --> CheckoutPage
    CheckoutPage --> DB
    AdminAuth --> AdminDashboard
    AdminDashboard --> ProductManagement
    AdminDashboard --> MockupManager
    AdminDashboard --> CategoryManager
    AdminDashboard --> CampaignManager
    AdminDashboard --> DealerManager
    AdminDashboard --> SettingsManager
    ProductManagement --> DB
```
