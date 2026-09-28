-- TAMBASKI.COM.TR BAŞLANGIÇ VERİLERİ (SEED DATA)
-- Tüm Baskı, Dekota/Pleksi Kesim, Folyo, Branda, Kartvizit, Promosyon ve Kurumsal Ürünler

-- Kategoriler
INSERT INTO `categories` (`id`, `name`, `slug`, `icon`, `description`, `sort_order`, `is_active`) VALUES
(1, 'Kartvizit', 'kartvizit', 'bi-person-badge', 'Standart, Sıvamalı, Laklı, Varaklı ve Özel Kesim Kartvizitler', 1, 1),
(2, 'El İlanı & Broşür', 'el-ilani-brosur', 'bi-file-earmark-richtext', 'A4, A5, A6 ve Kırımlı Tanıtım Broşürleri', 2, 1),
(3, 'Kurumsal Ürünler', 'kurumsal-urunler', 'bi-briefcase', 'Cepli Dosya, Antetli Kağıt, Diplomat Zarf, Bloknot', 3, 1),
(4, 'Dekota & Pleksi Kesim', 'dekota-pleksi-kesim', 'bi-layers', '3mm / 5mm Dekota Foreks Baskı ve Pleksi Lazer Kesim', 4, 1),
(5, 'Folyo & Branda Reklam', 'folyo-branda-reklam', 'bi-badge-ad', 'Cast Folyo, Kuşe Folyo, Dökme Branda, Roll-up Banner, One Way Vision', 5, 1),
(6, 'Etiket & Sticker', 'etiket-sticker', 'bi-tags', 'Rulo Etiket, Kuşe Sticker, Şeffaf & Kraft Özel Kesim Etiketler', 6, 1),
(7, 'Promosyon & Hediyelik', 'promosyon-hediyelik', 'bi-gift', 'Baskılı Kupa, Oto Kokusu, Kalem, Ajanda, Çakmak', 7, 1),
(8, 'Tekstil & Çanta', 'tekstil-canta', 'bi-bag', 'DTF Baskılı Tişört, Bez Çanta, Tela Çanta, Şapka', 8, 1),
(9, 'Ambalaj & Kutu', 'ambalaj-kutu', 'bi-box-seam', 'Karton Çanta, Kargo Kutusu, Kese Kağıdı, Pizza Kutusu', 9, 1),
(10, 'Kaşe Çeşitleri', 'kase-cesitleri', 'bi-stamp', 'Otomatik Kaşe, Tarih Kaşesi, Cep Kaşesi, Mühür', 10, 1),
(11, 'Acil Baskı (24 Saat)', 'acil-baski', 'bi-lightning-charge-fill', 'Aynı Gün Üretim ve 24 Saatte Hızlı Teslimat', 11, 1)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Ürünler
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `short_desc`, `description`, `pricing_type`, `min_quantity`, `base_setup_fee`, `custom_unit_multiplier`, `base_sqm_price`, `image`, `is_featured`, `is_urgent_available`, `has_template`, `is_active`) VALUES
-- Kartvizit
(1, 1, 'Ekonomik Kartvizit (250gr Solvent)', 'ekonomik-kartvizit-250gr', '250gr Amerikan Bristol / Kuşe, Tek Yön Renkli Solvent Baskı', 'Uygun fiyatlı ve yüksek tirajlı tanıtımlarınız için ideal ekonomik kartvizit seçeneği. 1.000 adetten başlayan hazır paketler ve özel adet seçeneği.', 'package_and_custom', 25, 45.00, 0.7500, 0.00, 'assets/img/products/kartvizit_eko.webp', 1, 1, 1, 1),
(2, 1, 'Kurumsal Prestij Kartvizit (350gr Mat Kuşe + Lak)', 'kurumsal-prestij-kartvizit-350gr', '350gr Mat Kuşe, Çift Yön Renkli, Mat Selefon + Bölgesel Kabartma Lak', 'Markanızı en üst seviyede temsil edecek şık, tok ve kabartma lak dokulu lüks kartvizit.', 'package_and_custom', 50, 75.00, 1.2000, 0.00, 'assets/img/products/kartvizit_prestij.webp', 1, 1, 1, 1),
(3, 1, 'Şeffaf / PVC Kartvizit', 'seffaf-pvc-kartvizit', '500 Mikron Şeffaf / Opak PVC, Yuvarlak Köşe Kesimli', 'Yırtılmaz, suya dayanıklı, modern şeffaf plastik kartvizitler.', 'package_and_custom', 100, 120.00, 1.8000, 0.00, 'assets/img/products/kartvizit_pvc.webp', 0, 1, 1, 1),

-- Broşür
(4, 2, 'A5 Tanıtım Broşürü (135gr Parlak Kuşe)', 'a5-tanitim-brosuru-135gr', 'A5 Ebat (14.8x21cm), 135gr Parlak Kuşe, Çift Yön Ofset Baskı', 'Restoran, emlak, klinik ve mağaza kampanyaları için canlı renkli ve tiraj indirimli broşür.', 'package_and_custom', 50, 80.00, 0.8500, 0.00, 'assets/img/products/brosur_a5.webp', 1, 1, 1, 1),
(5, 2, 'A4 Kırımlı / Katlamalı Broşür (Z-Kırım)', 'a4-kirimli-brosur-170gr', 'A4 Ebat (21x29.7cm), 170gr Kuşe, 2 Kırım 6 Sayfa Z-Katlama', 'Kapsamlı ürün katalogları ve kurumsal tanıtımlar için katlamalı ofset broşür.', 'package_and_custom', 100, 150.00, 1.4000, 0.00, 'assets/img/products/brosur_a4.webp', 1, 1, 1, 1),

-- Dekota & Pleksi Kesim (m² ve Adet Hesabı)
(6, 4, 'Dekota (Foreks) Baskı & Özel Lazer Kesim', 'dekota-foreks-baski-kesim', '3mm ve 5mm Sert Dekota Üzerine UV Baskı ve Özel CNC/Lazer Şekilli Kesim', 'Mağaza içi görseller, menü panoları, yönlendirmeler ve fuar stantları için hafif ve dayanıklı.', 'sqm_calculator', 1, 60.00, 0.0000, 320.00, 'assets/img/products/dekota_baski.webp', 1, 1, 1, 1),
(7, 4, 'Pleksi Lazer Kesim & UV Baskı', 'pleksi-lazer-kesim-baski', '2.8mm - 5mm Şeffaf, Siyah, Beyaz ve Renkli Pleksi Üzerine Özel Kesim ve Logo Baskısı', 'Işıklı/ışıksız tabelalar, masa üstü standlar, kapı isimlikleri ve mimari dekorasyonlar için kusursuz lazer kesim.', 'sqm_calculator', 1, 90.00, 0.0000, 580.00, 'assets/img/products/pleksi_kesim.webp', 1, 1, 1, 1),

-- Folyo & Branda
(8, 5, 'Dökme Branda / Vinil Afiş', 'dokme-branda-vinil-afis', '440gr Avrupa Dökme Branda, Dört Kenar Dikiş & Kuşgözü Kapsül', 'Bina cepheleri, inşaat brandaları, açılış ve seçim afişleri için yüksek mukavemetli dış mekan baskı.', 'sqm_calculator', 1, 50.00, 0.0000, 180.00, 'assets/img/products/branda_afis.webp', 1, 1, 1, 1),
(9, 5, 'Folyo Baskı & Laminasyon (Bıçak Kesimli)', 'folyo-baski-laminasyon', '100 Mikron Parlak/Mat Yapışkanlı Folyo, Mat Laminasyon Kaplama', 'Vitrin, araç kaplama, duvar giydirme ve yönlendirme etiketleri için suya ve güneşe dayanıklı.', 'sqm_calculator', 1, 40.00, 0.0000, 210.00, 'assets/img/products/folyo_baski.webp', 1, 1, 1, 1),
(10, 5, 'Roll-up Banner (Mekanizmalı Stand)', 'roll-up-banner-85x200', '85x200cm Alüminyum Gövde, Taşıma Çantalı, Saten Kumaş / Vinil Baskı', 'Fuar, seminer ve etkinlikler için kolay taşınabilir şık tanıtım standı.', 'package_and_custom', 1, 100.00, 650.0000, 0.00, 'assets/img/products/rollup_stand.webp', 1, 1, 1, 1),

-- Etiket & Sticker
(11, 6, 'Rulo & Tabaka Kuşe Etiket (Özel Lazer Kesim)', 'rulo-tabaka-kuse-etiket', 'Kuşe, Şeffaf ve Kraft Yapışkanlı Etiket, İstenen Geometrik veya Şekilli Kesim', 'Kavanoz, kutu, ambalaj ve koli etiketleri için hızlı yapışan canlı baskılı etiketler.', 'package_and_custom', 50, 50.00, 0.6000, 0.00, 'assets/img/products/etiket_rulo.webp', 1, 1, 1, 1),

-- Promosyon
(12, 7, 'Baskılı Porselen Kupa Bardak', 'baskili-porselen-kupa-bardak', 'Süblimasyon Tam Renkli Çevreleme Baskı, Bulaşık Makinesine Dayanıklı', 'Kurumsal logolu hediye, etkinlik ve şirket içi kullanım için birinci sınıf seramik kupa.', 'package_and_custom', 10, 40.00, 48.0000, 0.00, 'assets/img/products/kupa_bardak.webp', 1, 1, 1, 1),
(13, 7, 'Özel Kesimli Baskılı Oto Kokusu', 'baskili-oto-kokusu', '2mm Özel Emici Karton, Çift Yön Renkli Baskı, İstenen Özel Bıçak Şeklinde Kesim', 'Esanslı özel koku seçenekleri ile firmanızın reklamını araçlarda aylarca yaşatın.', 'package_and_custom', 250, 150.00, 2.2000, 0.00, 'assets/img/products/oto_kokusu.webp', 1, 1, 1, 1),

-- Kurumsal
(14, 3, 'Cepli Sunum Dosyası (350gr Bristol)', 'cepli-sunum-dosyasi-350gr', 'A4 Uyumlu, 350gr Amerikan Bristol, Mat Selefon, Kartvizit Yuvalı Özel Cep', 'Teklif, sözleşme ve kurumsal evraklarınızı sunabileceğiniz prestijli sunum dosyası.', 'package_and_custom', 50, 180.00, 6.5000, 0.00, 'assets/img/products/cepli_dosya.webp', 1, 1, 1, 1)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Hazır Sabit Fiyatlı Paketler (Standart Tirajlar)
INSERT INTO `product_packages` (`product_id`, `title`, `specs`, `quantity`, `price`, `dealer_price`, `is_popular`, `sort_order`) VALUES
-- Kartvizit 250gr Solvent
(1, '500 Adet Paket', '250gr Solvent Tek Yön', 500, 550.00, 412.50, 0, 1),
(1, '1.000 Adet Standart Paket', '250gr Solvent Tek Yön (En Çok Tercih Edilen)', 1000, 1000.00, 750.00, 1, 2),
(1, '2.000 Adet Avantaj Paketi', '250gr Solvent Tek Yön', 2000, 1850.00, 1387.50, 0, 3),
(1, '5.000 Adet Toptan Paket', '250gr Solvent Tek Yön', 5000, 4200.00, 3150.00, 0, 4),

-- Kartvizit 350gr Laklı
(2, '1.000 Adet Prestij Paket', '350gr Çift Yön Mat Selefon + Kabartma Lak', 1000, 1450.00, 1087.50, 1, 1),
(2, '2.000 Adet Prestij Paket', '350gr Çift Yön Mat Selefon + Kabartma Lak', 2000, 2600.00, 1950.00, 0, 2),
(2, '5.000 Adet Prestij Paket', '350gr Çift Yön Mat Selefon + Kabartma Lak', 5000, 5800.00, 4350.00, 0, 3),

-- Broşür A5
(4, '1.000 Adet A5 Broşür', '135gr Parlak Kuşe Çift Yön Renkli', 1000, 850.00, 637.50, 0, 1),
(4, '2.000 Adet A5 Broşür', '135gr Parlak Kuşe Çift Yön Renkli', 2000, 1450.00, 1087.50, 1, 2),
(4, '5.000 Adet A5 Broşür', '135gr Parlak Kuşe Çift Yön Renkli', 5000, 3100.00, 2325.00, 0, 3),

-- Oto Kokusu
(13, '500 Adet Özel Kesim Oto Kokusu', '2mm Emici Karton + Özel Esans + Askı İpli', 500, 1250.00, 937.50, 0, 1),
(13, '1.000 Adet Özel Kesim Oto Kokusu', '2mm Emici Karton + Özel Esans + Askı İpli', 1000, 2100.00, 1575.00, 1, 2),
(13, '2.500 Adet Özel Kesim Oto Kokusu', '2mm Emici Karton + Özel Esans + Askı İpli', 2500, 4600.00, 3450.00, 0, 3),

-- Kupa Bardak
(12, '12 Adet Kupa Bardak', 'Porselen Süblimasyon Baskılı', 12, 580.00, 435.00, 0, 1),
(12, '24 Adet Kupa Bardak', 'Porselen Süblimasyon Baskılı', 24, 1050.00, 787.50, 0, 2),
(12, '50 Adet Kupa Bardak', 'Porselen Süblimasyon Baskılı', 50, 2050.00, 1537.50, 1, 3),
(12, '100 Adet Kupa Bardak', 'Porselen Süblimasyon Baskılı', 100, 3800.00, 2850.00, 0, 4),

-- Cepli Dosya
(14, '250 Adet Cepli Dosya', '350gr Amerikan Bristol + Mat Selefon + Cep', 250, 2200.00, 1650.00, 0, 1),
(14, '500 Adet Cepli Dosya', '350gr Amerikan Bristol + Mat Selefon + Cep', 500, 3600.00, 2700.00, 1, 2),
(14, '1.000 Adet Cepli Dosya', '350gr Amerikan Bristol + Mat Selefon + Cep', 1000, 6200.00, 4650.00, 0, 3);

-- Varsayılan Ayarlar
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_title', 'TamBaskı – Online Matbaa, Dijital Baskı & Pleksi Kesim Merkezi'),
('site_url', 'http://localhost/tambaski.com.tr'),
('support_phone', '0850 308 00 00'),
('support_whatsapp', '0544 000 00 00'),
('support_email', 'destek@tambaski.com.tr'),
('free_shipping_min', '750.00'),
('dealer_discount_percent', '25')
ON DUPLICATE KEY UPDATE `setting_value`=VALUES(`setting_value`);
