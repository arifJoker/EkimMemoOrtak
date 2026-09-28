-- E-Ticaret & Online Matbaa Veritabanı Şeması
-- PayTR, iyzico, Kargo Anlaşmaları, Online Tasarım Editörü (SVG Vektörel), AI REST API Desteği

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Kullanıcılar & Bayiler (B2B)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(30) NULL,
  `role` ENUM('admin', 'customer', 'dealer') DEFAULT 'customer',
  `dealer_company` VARCHAR(200) NULL,
  `tax_number` VARCHAR(50) NULL,
  `tax_office` VARCHAR(100) NULL,
  `dealer_status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
  `discount_rate` DECIMAL(5,2) DEFAULT 0.00,
  `address` TEXT NULL,
  `city` VARCHAR(50) NULL,
  `district` VARCHAR(50) NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. AI & Entegrasyon API Anahtarları
CREATE TABLE IF NOT EXISTS `api_keys` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `name` VARCHAR(100) NOT NULL,
  `api_key` VARCHAR(64) NOT NULL UNIQUE,
  `api_secret` VARCHAR(64) NOT NULL,
  `permissions` TEXT NULL COMMENT 'JSON array: ["products:create", "products:read", "orders:read", etc.]',
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `last_used_at` DATETIME NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Kategoriler
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `parent_id` INT NULL DEFAULT 0,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(180) NOT NULL UNIQUE,
  `description` TEXT NULL,
  `image` VARCHAR(255) NULL,
  `icon` VARCHAR(100) DEFAULT 'bi bi-grid',
  `sort_order` INT DEFAULT 0,
  `status` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Ürünler
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(220) NOT NULL UNIQUE,
  `sku` VARCHAR(60) NULL,
  `short_description` VARCHAR(350) NULL,
  `full_description` LONGTEXT NULL,
  `base_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `tax_rate` DECIMAL(5,2) DEFAULT 20.00,
  `is_custom_size` TINYINT(1) DEFAULT 0,
  `min_width` DECIMAL(8,2) DEFAULT 0.00,
  `max_width` DECIMAL(8,2) DEFAULT 0.00,
  `min_height` DECIMAL(8,2) DEFAULT 0.00,
  `max_height` DECIMAL(8,2) DEFAULT 0.00,
  `price_per_sqm` DECIMAL(10,2) DEFAULT 0.00,
  `allow_design_upload` TINYINT(1) DEFAULT 1,
  `allow_online_editor` TINYINT(1) DEFAULT 1,
  `allow_design_service` TINYINT(1) DEFAULT 1,
  `design_service_price` DECIMAL(10,2) DEFAULT 150.00,
  `featured_image` VARCHAR(255) NULL,
  `gallery` TEXT NULL COMMENT 'JSON array of image paths',
  `is_featured` TINYINT(1) DEFAULT 0,
  `is_urgent` TINYINT(1) DEFAULT 0,
  `status` TINYINT(1) DEFAULT 1,
  `sort_order` INT DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Ürün Nitelikleri (Kağıt, Ebat, Selefon vb.)
CREATE TABLE IF NOT EXISTS `product_attributes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `type` ENUM('select', 'radio', 'checkbox') DEFAULT 'select',
  `is_required` TINYINT(1) DEFAULT 1,
  `sort_order` INT DEFAULT 0,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Ürün Nitelik Değerleri & Ek Fiyatları
CREATE TABLE IF NOT EXISTS `product_attribute_values` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `attribute_id` INT NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `price_extra` DECIMAL(10,2) DEFAULT 0.00,
  `price_multiplier` DECIMAL(5,2) DEFAULT 1.00,
  `is_default` TINYINT(1) DEFAULT 0,
  `sort_order` INT DEFAULT 0,
  FOREIGN KEY (`attribute_id`) REFERENCES `product_attributes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Adet Kademeleri & İndirim Tablosu
CREATE TABLE IF NOT EXISTS `product_quantity_tiers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `quantity` INT NOT NULL,
  `multiplier` DECIMAL(8,4) NOT NULL DEFAULT 1.0000,
  `fixed_price` DECIMAL(10,2) NULL,
  `discount_percent` DECIMAL(5,2) DEFAULT 0.00,
  `sort_order` INT DEFAULT 0,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Hazır Online Tasarım Şablonları (HTML5/SVG/Canvas)
CREATE TABLE IF NOT EXISTS `design_templates` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(180) NOT NULL,
  `thumbnail` VARCHAR(255) NULL,
  `category` VARCHAR(100) DEFAULT 'Kurumsal',
  `canvas_width` INT DEFAULT 850,
  `canvas_height` INT DEFAULT 500,
  `default_svg` LONGTEXT NULL,
  `template_data` LONGTEXT NULL COMMENT 'JSON layers/fields definition',
  `status` TINYINT(1) DEFAULT 1,
  `sort_order` INT DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Sepet Tablosu
CREATE TABLE IF NOT EXISTS `cart_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `session_id` VARCHAR(100) NOT NULL,
  `user_id` INT NULL,
  `product_id` INT NOT NULL,
  `quantity` INT NOT NULL DEFAULT 100,
  `selected_options` TEXT NULL COMMENT 'JSON encoded attribute selections',
  `custom_size` TEXT NULL COMMENT 'JSON {width, height, sqm}',
  `design_type` ENUM('uploaded', 'online_editor', 'design_request', 'none') DEFAULT 'none',
  `design_file` VARCHAR(255) NULL,
  `design_svg` LONGTEXT NULL,
  `design_preview` VARCHAR(255) NULL,
  `design_notes` TEXT NULL,
  `unit_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Siparişler
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_number` VARCHAR(30) NOT NULL UNIQUE,
  `user_id` INT NULL,
  `customer_name` VARCHAR(150) NOT NULL,
  `customer_email` VARCHAR(150) NOT NULL,
  `customer_phone` VARCHAR(30) NOT NULL,
  `shipping_address` TEXT NOT NULL,
  `shipping_city` VARCHAR(50) NOT NULL,
  `shipping_district` VARCHAR(50) NOT NULL,
  `billing_type` ENUM('individual', 'corporate') DEFAULT 'individual',
  `billing_company` VARCHAR(200) NULL,
  `tax_number` VARCHAR(50) NULL,
  `tax_office` VARCHAR(100) NULL,
  `payment_method` ENUM('paytr', 'iyzico', 'bank_transfer', 'door_payment') DEFAULT 'paytr',
  `payment_status` ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
  `order_status` ENUM('pending_payment', 'payment_received', 'design_approval', 'in_production', 'packaged', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending_payment',
  `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `tax_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` DECIMAL(10,2) DEFAULT 0.00,
  `shipping_fee` DECIMAL(10,2) DEFAULT 0.00,
  `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `coupon_code` VARCHAR(50) NULL,
  `cargo_company` VARCHAR(50) NULL,
  `cargo_tracking_code` VARCHAR(100) NULL,
  `notes` TEXT NULL,
  `paytr_token` VARCHAR(255) NULL,
  `payment_transaction_id` VARCHAR(150) NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Sipariş Kalemleri
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `product_name` VARCHAR(200) NOT NULL,
  `quantity` INT NOT NULL,
  `unit_price` DECIMAL(10,2) NOT NULL,
  `total_price` DECIMAL(10,2) NOT NULL,
  `selected_options` TEXT NULL COMMENT 'JSON string',
  `custom_size` TEXT NULL,
  `design_type` ENUM('uploaded', 'online_editor', 'design_request', 'none') DEFAULT 'none',
  `design_file` VARCHAR(255) NULL,
  `design_svg` LONGTEXT NULL,
  `design_preview` VARCHAR(255) NULL,
  `design_notes` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Kampanyalar & Sepet Kuralları / Kuponlar
CREATE TABLE IF NOT EXISTS `campaigns` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `type` ENUM('percent', 'fixed', 'free_shipping', 'cart_rule') DEFAULT 'percent',
  `value` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `min_cart_amount` DECIMAL(10,2) DEFAULT 0.00,
  `max_discount_amount` DECIMAL(10,2) DEFAULT 0.00,
  `start_date` DATE NULL,
  `end_date` DATE NULL,
  `usage_limit` INT DEFAULT 1000,
  `usage_count` INT DEFAULT 0,
  `status` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Site Ayarları
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` LONGTEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- BAŞLANGIÇ VERİLERİ (Örnek Kategoriler, Matbaa Ürünleri, Şablonlar & Ayarlar)
-- =========================================================================

-- Varsayılan Yönetici (1@1.com / 123456)
INSERT INTO `users` (`full_name`, `email`, `password`, `phone`, `role`, `dealer_status`)
VALUES ('Sistem Yöneticisi', '1@1.com', '$2y$10$gO091LqKk79b4zH8b8P7/eHhYm3nsu406E982w.jNqQ0Lw6aE0kYq', '05550000000', 'admin', 'approved')
ON DUPLICATE KEY UPDATE `full_name`=VALUES(`full_name`);

-- Varsayılan API Anahtarı (AI Araçları ve Entegrasyonlar için)
INSERT INTO `api_keys` (`name`, `api_key`, `api_secret`, `permissions`, `status`)
VALUES ('AI Entegrasyon Anahtarı', 'ai_matbaa_key_998877665544332211', 'ai_secret_11223344556677889900aabbcc', '["products:all", "categories:all", "orders:all", "templates:all"]', 'active')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Kategoriler
INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `description`, `icon`, `sort_order`, `status`) VALUES
(1, 0, 'Acil Baskı', 'acil-baski', 'Aynı gün veya 24 saat içinde teslim edilen acil matbaa ve dijital baskı çözümleri.', 'bi bi-lightning-charge-fill', 1, 1),
(2, 0, 'Kartvizit', 'kartvizit', 'Bristol, tuale fantezi, altın yaldızlı ve kabartma laklı özel kartvizitler.', 'bi bi-person-badge', 2, 1),
(3, 0, 'El İlanı - Broşür', 'el-ilani-brosur', 'Tek kırım, katlamalı ve kuşe kağıt renkli el ilanları ve broşürler.', 'bi bi-file-earmark-richtext', 3, 1),
(4, 0, 'Kurumsal Ürünler', 'kurumsal-urunler', 'Cepli sunum dosyası, diplomat zarf, antetli kağıt ve kurumsal setler.', 'bi bi-briefcase', 4, 1),
(5, 0, 'Kaşe Çeşitleri', 'kase-cesitleri', 'Otomatik mekanizmalı, cep kaşesi ve yuvarlak mühür kaşeler.', 'bi bi-stamp', 5, 1),
(6, 0, 'Bayrak & Reklam', 'bayrak-reklam', 'Masa bayrağı, gönder bayrağı, makam bayrağı ve roll-up banner ürünleri.', 'bi bi-flag', 6, 1)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Örnek Ürünler
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `sku`, `short_description`, `full_description`, `base_price`, `tax_rate`, `is_custom_size`, `allow_design_upload`, `allow_online_editor`, `allow_design_service`, `design_service_price`, `is_featured`, `is_urgent`, `status`, `sort_order`) VALUES
(1, 2, 'Premium Mat Kuşe Kartvizit', 'premium-mat-kuse-kartvizit', 'KV-001', '350 gr. Mat Kuşe Kağıt, Çift Yön Renkli Baskı ve Mat Selefon Kaplamalı.', '<p>Firmanızı en profesyonel şekilde temsil eden standart boyutlu (84x52 mm) mat selefon kaplamalı dayanıklı kartvizit.</p><ul><li>350 gr. Kuşe Kağıt</li><li>Çift Taraf Mat Selefon</li><li>Ofset ve Dijital Yüksek Çözünürlük</li></ul>', 280.00, 20.00, 0, 1, 1, 1, 150.00, 1, 1, 1, 1),
(2, 2, 'Lüks Kabartma Laklı Kartvizit', 'luks-kabartma-lakli-kartvizit', 'KV-002', '350 gr. Mat Selefon üzerine tek veya çift taraf parlak kabartma lak uygulaması.', '<p>Logonuzun veya istediğiniz detayların kabartılarak parlatıldığı prestijli kartvizit seçeneği.</p>', 480.00, 20.00, 0, 1, 1, 1, 150.00, 1, 0, 1, 2),
(3, 3, 'A5 Renkli El İlanı / Broşür', 'a5-renkli-el-ilani-brosur', 'BR-101', '130 gr. veya 170 gr. Parlak Kuşe Kağıda Yüksek Kalite Ofset Baskı.', '<p>Kampanya ve tanıtımlarınız için en çok tercih edilen A5 (14.8 x 21 cm) boyutunda renkli el ilanı.</p>', 450.00, 20.00, 0, 1, 1, 1, 200.00, 1, 0, 1, 3),
(4, 4, 'Cepli Sunum Dosyası', 'cepli-sunum-dosyasi', 'CD-201', '350 gr. Mat Kuşe, Kartvizit Geçme Bölmeli Cepli Kurumsal Sunum Dosyası.', '<p>Teklif ve sözleşmelerinizi müşterilerinize sunabileceğiniz kaliteli cepli dosya baskısı.</p>', 1250.00, 20.00, 0, 1, 0, 1, 250.00, 1, 0, 1, 4),
(5, 5, 'Sırdaş Otomatik Kaşe', 'sirdas-otomatik-kase', 'KS-301', 'Yüksek kaliteli mürekkepli hazır otomatik metin kaşesi.', '<p>Şirket unvanı, vergi dairesi ve imza yetkilisi için hazır otomatik kaşe mekanizması.</p>', 180.00, 20.00, 0, 1, 1, 0, 0.00, 1, 1, 1, 5)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Kartvizit Nitelikleri (Ürün ID: 1)
INSERT INTO `product_attributes` (`id`, `product_id`, `name`, `type`, `is_required`, `sort_order`) VALUES
(1, 1, 'Kağıt Türü', 'radio', 1, 1),
(2, 1, 'Laminasyon / Selefon', 'radio', 1, 2),
(3, 1, 'Köşe Kesimi', 'radio', 1, 3)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

INSERT INTO `product_attribute_values` (`attribute_id`, `title`, `price_extra`, `price_multiplier`, `is_default`, `sort_order`) VALUES
(1, '350 gr. Mat Kuşe', 0.00, 1.00, 1, 1),
(1, '350 gr. Parlak Kuşe', 30.00, 1.00, 0, 2),
(1, '300 gr. Tuale / Fantezi Dokulu', 120.00, 1.15, 0, 3),
(2, 'Çift Taraf Mat Selefon', 0.00, 1.00, 1, 1),
(2, 'Çift Taraf Parlak Selefon', 0.00, 1.00, 0, 2),
(2, 'Soft-Touch Kadife Selefon', 80.00, 1.10, 0, 3),
(3, 'Düz Kesim (Standart)', 0.00, 1.00, 1, 1),
(3, 'Oval Köşe Kesimli (4 Köşe)', 50.00, 1.00, 0, 2)
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);

-- Adet Kademeleri (Ürün ID: 1 - Kartvizit)
INSERT INTO `product_quantity_tiers` (`product_id`, `quantity`, `multiplier`, `fixed_price`, `discount_percent`, `sort_order`) VALUES
(1, 100, 1.0000, 280.00, 0.00, 1),
(1, 250, 1.4500, 390.00, 15.00, 2),
(1, 500, 1.8500, 490.00, 25.00, 3),
(1, 1000, 2.5000, 680.00, 40.00, 4),
(1, 2000, 4.2000, 1150.00, 50.00, 5)
ON DUPLICATE KEY UPDATE `multiplier`=VALUES(`multiplier`);

-- Online Kartvizit Şablonları (HTML5 & SVG Vektörel)
INSERT INTO `design_templates` (`id`, `product_id`, `title`, `slug`, `category`, `canvas_width`, `canvas_height`, `default_svg`, `template_data`, `status`, `sort_order`) VALUES
(1, 1, 'Minimalist Apple Stil Kartvizit', 'minimalist-apple-stil-kartvizit', 'Modern Minimal', 850, 500, 
'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 850 500" width="850" height="500">
  <defs>
    <linearGradient id="bgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#0f172a" />
      <stop offset="100%" stop-color="#1e293b" />
    </linearGradient>
  </defs>
  <rect width="850" height="500" fill="url(#bgGrad)" rx="20"/>
  <circle cx="750" cy="80" r="180" fill="#38bdf8" opacity="0.08"/>
  <circle cx="100" cy="420" r="120" fill="#f43f5e" opacity="0.05"/>
  <text id="companyName" x="70" y="110" fill="#ffffff" font-family="system-ui, -apple-system, sans-serif" font-size="28" font-weight="700" letter-spacing="1.5">ŞİRKETİNİZ A.Ş.</text>
  <text id="slogan" x="70" y="140" fill="#94a3b8" font-family="system-ui, -apple-system, sans-serif" font-size="14" font-weight="400">Yaratıcı & Dijital Çözümler</text>
  <line x1="70" y1="170" x2="220" y2="170" stroke="#38bdf8" stroke-width="3"/>
  <text id="personName" x="70" y="270" fill="#ffffff" font-family="system-ui, -apple-system, sans-serif" font-size="32" font-weight="600">Ahmet Yılmaz</text>
  <text id="personTitle" x="70" y="305" fill="#38bdf8" font-family="system-ui, -apple-system, sans-serif" font-size="16" font-weight="500">Genel Müdür / Kurucu</text>
  <g transform="translate(480, 240)" fill="#cbd5e1" font-family="system-ui, -apple-system, sans-serif" font-size="15">
    <text id="phone" x="30" y="25">+90 (555) 123 45 67</text>
    <text id="email" x="30" y="65">ahmet@sirketiniz.com</text>
    <text id="website" x="30" y="105">www.sirketiniz.com</text>
    <text id="address" x="30" y="145">Levent Mah. Büyükdere Cad. No:1 İstanbul</text>
  </g>
</svg>',
'{"fields":[{"id":"companyName","label":"Şirket Adı","default":"ŞİRKETİNİZ A.Ş."},{"id":"slogan","label":"Slogan","default":"Yaratıcı & Dijital Çözümler"},{"id":"personName","label":"Ad Soyad","default":"Ahmet Yılmaz"},{"id":"personTitle","label":"Unvan","default":"Genel Müdür / Kurucu"},{"id":"phone","label":"Telefon","default":"+90 (555) 123 45 67"},{"id":"email","label":"E-Posta","default":"ahmet@sirketiniz.com"},{"id":"website","label":"Web Sitesi","default":"www.sirketiniz.com"},{"id":"address","label":"Adres","default":"Levent Mah. Büyükdere Cad. No:1 İstanbul"}]}',
1, 1),
(2, 1, 'Zarif Beyaz & Altın Kurumsal Kartvizit', 'zarif-beyaz-altin-kartvizit', 'Lüks & Prestij', 850, 500,
'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 850 500" width="850" height="500">
  <rect width="850" height="500" fill="#fafafa" rx="20"/>
  <rect x="25" y="25" width="800" height="450" fill="none" stroke="#d4af37" stroke-width="1.5" rx="12" opacity="0.6"/>
  <text id="companyName" x="425" y="120" text-anchor="middle" fill="#111827" font-family="serif" font-size="30" font-weight="700" letter-spacing="3">MONARCH HOLDİNG</text>
  <text id="slogan" x="425" y="150" text-anchor="middle" fill="#d4af37" font-family="system-ui, sans-serif" font-size="13" letter-spacing="2">PREMİUM HİZMETLER</text>
  <circle cx="425" cy="180" r="3" fill="#d4af37"/>
  <text id="personName" x="425" y="270" text-anchor="middle" fill="#1f2937" font-family="system-ui, sans-serif" font-size="28" font-weight="600">Selin Karaca</text>
  <text id="personTitle" x="425" y="300" text-anchor="middle" fill="#4b5563" font-family="system-ui, sans-serif" font-size="15">Mimar & Tasarım Direktörü</text>
  <text id="phone" x="425" y="360" text-anchor="middle" fill="#6b7280" font-family="system-ui, sans-serif" font-size="14">+90 212 987 65 43</text>
  <text id="email" x="425" y="390" text-anchor="middle" fill="#6b7280" font-family="system-ui, sans-serif" font-size="14">selin@monarch.com</text>
  <text id="website" x="425" y="420" text-anchor="middle" fill="#d4af37" font-family="system-ui, sans-serif" font-size="14" font-weight="600">www.monarch.com.tr</text>
</svg>',
'{"fields":[{"id":"companyName","label":"Şirket Adı","default":"MONARCH HOLDİNG"},{"id":"slogan","label":"Slogan","default":"PREMİUM HİZMETLER"},{"id":"personName","label":"Ad Soyad","default":"Selin Karaca"},{"id":"personTitle","label":"Unvan","default":"Mimar & Tasarım Direktörü"},{"id":"phone","label":"Telefon","default":"+90 212 987 65 43"},{"id":"email","label":"E-Posta","default":"selin@monarch.com"},{"id":"website","label":"Web Sitesi","default":"www.monarch.com.tr"}]}',
1, 2)
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);

-- Örnek Kampanyalar & Sepet Kuralları
INSERT INTO `campaigns` (`title`, `code`, `type`, `value`, `min_cart_amount`, `max_discount_amount`, `usage_limit`, `status`) VALUES
('Yeni Üye İndirimi %10', 'HOSGELDIN10', 'percent', 10.00, 200.00, 250.00, 500, 1),
('1000 TL Üzeri 100 TL Sepet İndirimi', 'SEPET100', 'fixed', 100.00, 1000.00, 100.00, 200, 1),
('Ücretsiz Kargo Kampanyası', 'BEDAVAKARGO', 'free_shipping', 0.00, 500.00, 0.00, 1000, 1)
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);

-- Temel Site Ayarları (PayTR, iyzico, Kargo, İletişim vb.)
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_title', 'BaskıMatbaa – Online Matbaa & Dijital Baskı Merkezi'),
('site_slogan', 'Türkiye''nin En Hızlı ve Kaliteli Online Matbaası'),
('site_logo', ''),
('site_phone', '0850 123 45 67'),
('site_whatsapp', '905550000000'),
('site_email', 'destek@baskimatbaa.com'),
('site_address', 'Maslak Mah. Büyükdere Cad. No:123 Sarıyer / İstanbul'),
('free_shipping_limit', '750.00'),
('default_shipping_fee', '79.90'),
-- PayTR Ayarları
('paytr_active', '1'),
('paytr_merchant_id', 'TEST_MERCHANT_ID'),
('paytr_merchant_key', 'TEST_MERCHANT_KEY'),
('paytr_merchant_salt', 'TEST_MERCHANT_SALT'),
('paytr_test_mode', '1'),
-- iyzico Ayarları
('iyzico_active', '1'),
('iyzico_api_key', 'sandbox-TEST_API_KEY'),
('iyzico_secret_key', 'sandbox-TEST_SECRET_KEY'),
('iyzico_base_url', 'https://sandbox-api.iyzipay.com'),
-- Kargo Ayarları
('cargo_default_company', 'Yurtiçi Kargo'),
('cargo_api_url', ''),
('cargo_customer_code', ''),
('cargo_api_key', '')
ON DUPLICATE KEY UPDATE `setting_value`=VALUES(`setting_value`);

SET FOREIGN_KEY_CHECKS = 1;

