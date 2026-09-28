<?php
/**
 * TAMBASKI.COM.TR - Çekirdek Fonksiyonlar & Fiyat Hesaplama Motoru
 */

require_once __DIR__ . '/../config/app.php';

// Veritabanı bağlı değilken veya offline modda sorunsuz çalışacak Mock/Fallback Verileri
function get_static_categories() {
    return [
        ['id' => 1, 'name' => 'Kartvizit', 'slug' => 'kartvizit', 'icon' => 'bi-person-badge', 'description' => 'Standart, Sıvamalı, Laklı, Varaklı ve Özel Kesim Kartvizitler'],
        ['id' => 2, 'name' => 'El İlanı & Broşür', 'slug' => 'el-ilani-brosur', 'icon' => 'bi-file-earmark-richtext', 'description' => 'A4, A5, A6 ve Kırımlı Tanıtım Broşürleri'],
        ['id' => 3, 'name' => 'Kurumsal Ürünler', 'slug' => 'kurumsal-urunler', 'icon' => 'bi-briefcase', 'description' => 'Cepli Dosya, Antetli Kağıt, Diplomat Zarf, Bloknot'],
        ['id' => 4, 'name' => 'Dekota & Pleksi Kesim', 'slug' => 'dekota-pleksi-kesim', 'icon' => 'bi-layers', 'description' => '3mm / 5mm Dekota Foreks Baskı ve Pleksi Lazer Kesim'],
        ['id' => 5, 'name' => 'Folyo & Branda Reklam', 'slug' => 'folyo-branda-reklam', 'icon' => 'bi-badge-ad', 'description' => 'Cast Folyo, Kuşe Folyo, Dökme Branda, Roll-up Banner, One Way Vision'],
        ['id' => 6, 'name' => 'Etiket & Sticker', 'slug' => 'etiket-sticker', 'icon' => 'bi-tags', 'description' => 'Rulo Etiket, Kuşe Sticker, Şeffaf & Kraft Özel Kesim Etiketler'],
        ['id' => 7, 'name' => 'Promosyon & Hediyelik', 'slug' => 'promosyon-hediyelik', 'icon' => 'bi-gift', 'description' => 'Baskılı Kupa, Oto Kokusu, Kalem, Ajanda, Çakmak'],
        ['id' => 8, 'name' => 'Tekstil & Çanta', 'slug' => 'tekstil-canta', 'icon' => 'bi-bag', 'description' => 'DTF Baskılı Tişört, Bez Çanta, Tela Çanta, Şapka'],
        ['id' => 9, 'name' => 'Ambalaj & Kutu', 'slug' => 'ambalaj-kutu', 'icon' => 'bi-box-seam', 'description' => 'Karton Çanta, Kargo Kutusu, Kese Kağıdı, Pizza Kutusu'],
        ['id' => 10, 'name' => 'Kaşe Çeşitleri', 'slug' => 'kase-cesitleri', 'icon' => 'bi-stamp', 'description' => 'Otomatik Kaşe, Tarih Kaşesi, Cep Kaşesi, Mühür'],
        ['id' => 11, 'name' => 'Acil Baskı (24 Saat)', 'slug' => 'acil-baski', 'icon' => 'bi-lightning-charge-fill', 'description' => 'Aynı Gün Üretim ve 24 Saatte Hızlı Teslimat']
    ];
}

function get_static_products() {
    return [
        1 => [
            'id' => 1, 'category_id' => 1, 'category_slug' => 'kartvizit', 'name' => 'Ekonomik Kartvizit (250gr Solvent)', 'slug' => 'ekonomik-kartvizit-250gr',
            'short_desc' => '250gr Amerikan Bristol / Kuşe, Tek Yön Renkli Solvent Baskı',
            'description' => 'Uygun fiyatlı ve yüksek tirajlı tanıtımlarınız için ideal ekonomik kartvizit seçeneği. 1.000 adetten başlayan hazır paketler ve özel adet seçeneği.',
            'pricing_type' => 'package_and_custom', 'min_quantity' => 25, 'base_setup_fee' => 45.00, 'custom_unit_multiplier' => 0.75, 'base_sqm_price' => 0,
            'image' => 'assets/img/products/kartvizit_eko.webp', 'is_featured' => 1, 'is_urgent_available' => 1, 'has_template' => 1,
            'packages' => [
                ['id' => 101, 'title' => '500 Adet Paket', 'specs' => '250gr Solvent Tek Yön', 'quantity' => 500, 'price' => 550.00, 'dealer_price' => 412.50, 'is_popular' => 0],
                ['id' => 102, 'title' => '1.000 Adet Standart Paket', 'specs' => '250gr Solvent Tek Yön (En Çok Satan)', 'quantity' => 1000, 'price' => 1000.00, 'dealer_price' => 750.00, 'is_popular' => 1],
                ['id' => 103, 'title' => '2.000 Adet Avantaj Paketi', 'specs' => '250gr Solvent Tek Yön', 'quantity' => 2000, 'price' => 1850.00, 'dealer_price' => 1387.50, 'is_popular' => 0],
                ['id' => 104, 'title' => '5.000 Adet Toptan Paket', 'specs' => '250gr Solvent Tek Yön', 'quantity' => 5000, 'price' => 4200.00, 'dealer_price' => 3150.00, 'is_popular' => 0]
            ]
        ],
        2 => [
            'id' => 2, 'category_id' => 1, 'category_slug' => 'kartvizit', 'name' => 'Kurumsal Prestij Kartvizit (350gr Mat Kuşe + Lak)', 'slug' => 'kurumsal-prestij-kartvizit-350gr',
            'short_desc' => '350gr Mat Kuşe, Çift Yön Renkli, Mat Selefon + Bölgesel Kabartma Lak',
            'description' => 'Markanızı en üst seviyede temsil edecek şık, tok ve kabartma lak dokulu lüks kartvizit.',
            'pricing_type' => 'package_and_custom', 'min_quantity' => 50, 'base_setup_fee' => 75.00, 'custom_unit_multiplier' => 1.20, 'base_sqm_price' => 0,
            'image' => 'assets/img/products/kartvizit_prestij.webp', 'is_featured' => 1, 'is_urgent_available' => 1, 'has_template' => 1,
            'packages' => [
                ['id' => 201, 'title' => '1.000 Adet Prestij Paket', 'specs' => '350gr Çift Yön Mat Selefon + Kabartma Lak', 'quantity' => 1000, 'price' => 1450.00, 'dealer_price' => 1087.50, 'is_popular' => 1],
                ['id' => 202, 'title' => '2.000 Adet Prestij Paket', 'specs' => '350gr Çift Yön Mat Selefon + Kabartma Lak', 'quantity' => 2000, 'price' => 2600.00, 'dealer_price' => 1950.00, 'is_popular' => 0],
                ['id' => 203, 'title' => '5.000 Adet Prestij Paket', 'specs' => '350gr Çift Yön Mat Selefon + Kabartma Lak', 'quantity' => 5000, 'price' => 5800.00, 'dealer_price' => 4350.00, 'is_popular' => 0]
            ]
        ],
        4 => [
            'id' => 4, 'category_id' => 2, 'category_slug' => 'el-ilani-brosur', 'name' => 'A5 Tanıtım Broşürü (135gr Parlak Kuşe)', 'slug' => 'a5-tanitim-brosuru-135gr',
            'short_desc' => 'A5 Ebat (14.8x21cm), 135gr Parlak Kuşe, Çift Yön Ofset Baskı',
            'description' => 'Restoran, emlak, klinik ve mağaza kampanyaları için canlı renkli ve tiraj indirimli broşür.',
            'pricing_type' => 'package_and_custom', 'min_quantity' => 50, 'base_setup_fee' => 80.00, 'custom_unit_multiplier' => 0.85, 'base_sqm_price' => 0,
            'image' => 'assets/img/products/brosur_a5.webp', 'is_featured' => 1, 'is_urgent_available' => 1, 'has_template' => 1,
            'packages' => [
                ['id' => 401, 'title' => '1.000 Adet A5 Broşür', 'specs' => '135gr Parlak Kuşe Çift Yön Renkli', 'quantity' => 1000, 'price' => 850.00, 'dealer_price' => 637.50, 'is_popular' => 0],
                ['id' => 402, 'title' => '2.000 Adet A5 Broşür', 'specs' => '135gr Parlak Kuşe Çift Yön Renkli', 'quantity' => 2000, 'price' => 1450.00, 'dealer_price' => 1087.50, 'is_popular' => 1],
                ['id' => 403, 'title' => '5.000 Adet A5 Broşür', 'specs' => '135gr Parlak Kuşe Çift Yön Renkli', 'quantity' => 5000, 'price' => 3100.00, 'dealer_price' => 2325.00, 'is_popular' => 0]
            ]
        ],
        6 => [
            'id' => 6, 'category_id' => 4, 'category_slug' => 'dekota-pleksi-kesim', 'name' => 'Dekota (Foreks) Baskı & Özel Lazer Kesim', 'slug' => 'dekota-foreks-baski-kesim',
            'short_desc' => '3mm ve 5mm Sert Dekota Üzerine UV Baskı ve Özel CNC/Lazer Şekilli Kesim',
            'description' => 'Mağaza içi görseller, menü panoları, yönlendirmeler ve fuar stantları için hafif ve dayanıklı.',
            'pricing_type' => 'sqm_calculator', 'min_quantity' => 1, 'base_setup_fee' => 60.00, 'custom_unit_multiplier' => 0, 'base_sqm_price' => 320.00,
            'image' => 'assets/img/products/dekota_baski.webp', 'is_featured' => 1, 'is_urgent_available' => 1, 'has_template' => 0,
            'options' => [
                'thickness' => [
                    ['title' => '3mm Dekota (Standart)', 'multiplier' => 1.0],
                    ['title' => '5mm Dekota (Ekstra Dayanıklı)', 'multiplier' => 1.45]
                ],
                'cutting' => [
                    ['title' => 'Düz Giyotin Kesim', 'price_extra' => 0],
                    ['title' => 'Özel Şekilli CNC Lazer Kesim', 'price_extra' => 50.00]
                ]
            ]
        ],
        7 => [
            'id' => 7, 'category_id' => 4, 'category_slug' => 'dekota-pleksi-kesim', 'name' => 'Pleksi Lazer Kesim & UV Logo Baskı', 'slug' => 'pleksi-lazer-kesim-baski',
            'short_desc' => '2.8mm - 5mm Şeffaf / Siyah / Beyaz Pleksi Üzerine Özel Kesim ve Logo Baskısı',
            'description' => 'Işıklı/ışıksız tabelalar, masa üstü standlar, kapı isimlikleri ve mimari dekorasyonlar için kusursuz lazer kesim.',
            'pricing_type' => 'sqm_calculator', 'min_quantity' => 1, 'base_setup_fee' => 90.00, 'custom_unit_multiplier' => 0, 'base_sqm_price' => 580.00,
            'image' => 'assets/img/products/pleksi_kesim.webp', 'is_featured' => 1, 'is_urgent_available' => 1, 'has_template' => 0,
            'options' => [
                'thickness' => [
                    ['title' => '2.8mm Şeffaf Pleksi', 'multiplier' => 1.0],
                    ['title' => '4.0mm Şeffaf Pleksi', 'multiplier' => 1.35],
                    ['title' => '5.0mm Şeffaf / Renkli Pleksi', 'multiplier' => 1.70]
                ]
            ]
        ],
        8 => [
            'id' => 8, 'category_id' => 5, 'category_slug' => 'folyo-branda-reklam', 'name' => 'Dökme Branda / Vinil Afiş', 'slug' => 'dokme-branda-vinil-afis',
            'short_desc' => '440gr Avrupa Dökme Branda, Dört Kenar Dikiş & Kuşgözü Kapsül',
            'description' => 'Bina cepheleri, inşaat brandaları, açılış ve seçim afişleri için yüksek mukavemetli dış mekan baskı.',
            'pricing_type' => 'sqm_calculator', 'min_quantity' => 1, 'base_setup_fee' => 50.00, 'custom_unit_multiplier' => 0, 'base_sqm_price' => 180.00,
            'image' => 'assets/img/products/branda_afis.webp', 'is_featured' => 1, 'is_urgent_available' => 1, 'has_template' => 0
        ],
        9 => [
            'id' => 9, 'category_id' => 5, 'category_slug' => 'folyo-branda-reklam', 'name' => 'Folyo Baskı & Laminasyon (Bıçak Kesimli)', 'slug' => 'folyo-baski-laminasyon',
            'short_desc' => '100 Mikron Parlak/Mat Yapışkanlı Folyo, Mat Laminasyon Kaplama',
            'description' => 'Vitrin, araç kaplama, duvar giydirme ve yönlendirme etiketleri için suya ve güneşe dayanıklı.',
            'pricing_type' => 'sqm_calculator', 'min_quantity' => 1, 'base_setup_fee' => 40.00, 'custom_unit_multiplier' => 0, 'base_sqm_price' => 210.00,
            'image' => 'assets/img/products/folyo_baski.webp', 'is_featured' => 1, 'is_urgent_available' => 1, 'has_template' => 0
        ],
        13 => [
            'id' => 13, 'category_id' => 7, 'category_slug' => 'promosyon-hediyelik', 'name' => 'Özel Kesimli Baskılı Oto Kokusu', 'slug' => 'baskili-oto-kokusu',
            'short_desc' => '2mm Özel Emici Karton, Çift Yön Renkli Baskı, İstenen Özel Bıçak Şeklinde Kesim',
            'description' => 'Esanslı özel koku seçenekleri ile firmanızın reklamını araçlarda aylarca yaşatın.',
            'pricing_type' => 'package_and_custom', 'min_quantity' => 100, 'base_setup_fee' => 150.00, 'custom_unit_multiplier' => 2.20, 'base_sqm_price' => 0,
            'image' => 'assets/img/products/oto_kokusu.webp', 'is_featured' => 1, 'is_urgent_available' => 1, 'has_template' => 1,
            'packages' => [
                ['id' => 1301, 'title' => '500 Adet Özel Kesim Oto Kokusu', 'specs' => '2mm Emici Karton + Özel Esans + İpli', 'quantity' => 500, 'price' => 1250.00, 'dealer_price' => 937.50, 'is_popular' => 0],
                ['id' => 1302, 'title' => '1.000 Adet Özel Kesim Oto Kokusu', 'specs' => '2mm Emici Karton + Özel Esans + İpli', 'quantity' => 1000, 'price' => 2100.00, 'dealer_price' => 1575.00, 'is_popular' => 1],
                ['id' => 1303, 'title' => '2.500 Adet Özel Kesim Oto Kokusu', 'specs' => '2mm Emici Karton + Özel Esans + İpli', 'quantity' => 2500, 'price' => 4600.00, 'dealer_price' => 3450.00, 'is_popular' => 0]
            ]
        ]
    ];
}

// Kategorileri Getirme
function get_all_categories() {
    $db = getDB();
    if ($db) {
        try {
            $stmt = $db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order ASC");
            return $stmt->fetchAll();
        } catch (Exception $e) {}
    }
    return get_static_categories();
}

// Kategori Getirme (Slug ile)
function get_category_by_slug($slug) {
    $categories = get_all_categories();
    foreach ($categories as $cat) {
        if ($cat['slug'] === $slug) return $cat;
    }
    return null;
}

// Ürünleri Getirme
function get_all_products($cat_slug = null, $featured_only = false, $urgent_only = false) {
    $products = get_static_products();
    $result = [];
    foreach ($products as $p) {
        if ($cat_slug && $p['category_slug'] !== $cat_slug) continue;
        if ($featured_only && empty($p['is_featured'])) continue;
        if ($urgent_only && empty($p['is_urgent_available'])) continue;
        $result[] = $p;
    }
    return $result;
}

// Ürün Detayı Getirme (Slug ile)
function get_product_by_slug($slug) {
    $products = get_static_products();
    foreach ($products as $p) {
        if ($p['slug'] === $slug) return $p;
    }
    return null;
}

/**
 * 🧮 HİBRİT FİYAT HESAPLAMA MOTORU (Pricing Engine)
 * - Hazır Paket: Net sabit fiyat
 * - Özel Adet: Taban kurulum + Adet * Birim çarpanı
 * - m² Ürünleri: (Genişlik x Yükseklik / 10000) * m² Fiyatı * Adet + Kalınlık/İşçilik çarpanları
 */
function calculate_item_price($product, $params = []) {
    $is_dealer = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'dealer';
    $dealer_rate = $_SESSION['dealer_discount_rate'] ?? DEFAULT_DEALER_DISCOUNT;

    // 1. Hazır Paket Seçilmişse
    if (!empty($params['package_id']) && !empty($product['packages'])) {
        foreach ($product['packages'] as $pkg) {
            if ($pkg['id'] == $params['package_id']) {
                $base_price = (float)$pkg['price'];
                $final_price = $is_dealer ? ($pkg['dealer_price'] ?? ($base_price * (1 - $dealer_rate / 100))) : $base_price;
                return [
                    'type' => 'package',
                    'title' => $pkg['title'],
                    'specs' => $pkg['specs'],
                    'quantity' => (int)$pkg['quantity'],
                    'unit_price' => round($final_price / $pkg['quantity'], 4),
                    'total_price' => round($final_price, 2),
                    'is_dealer' => $is_dealer,
                    'original_price' => $base_price
                ];
            }
        }
    }

    // 2. m² Tabanlı Ürün (Dekota, Pleksi Kesim, Folyo, Branda vb.)
    if ($product['pricing_type'] === 'sqm_calculator') {
        $width_cm = max(1, (float)($params['width_cm'] ?? 100));
        $height_cm = max(1, (float)($params['height_cm'] ?? 100));
        $quantity = max(1, (int)($params['quantity'] ?? 1));
        
        $sqm = ($width_cm * $height_cm) / 10000; // cm² to m²
        // Minimum m² eşiği (0.2 m²)
        $billable_sqm = max(0.20, $sqm);

        $base_sqm_price = (float)$product['base_sqm_price'];
        $thickness_mult = (float)($params['thickness_multiplier'] ?? 1.0);
        $extra_cutting = (float)($params['extra_cutting_price'] ?? 0.0);
        $setup_fee = (float)$product['base_setup_fee'];

        $unit_price = ($billable_sqm * $base_sqm_price * $thickness_mult) + $extra_cutting;
        $subtotal = ($unit_price * $quantity) + $setup_fee;

        $final_price = $is_dealer ? ($subtotal * (1 - $dealer_rate / 100)) : $subtotal;

        return [
            'type' => 'sqm',
            'title' => $product['name'] . " ({$width_cm}x{$height_cm} cm)",
            'specs' => "Ebat: {$width_cm}x{$height_cm} cm (" . round($sqm, 2) . " m²)",
            'dimensions' => ['width_cm' => $width_cm, 'height_cm' => $height_cm, 'sqm' => round($sqm, 2)],
            'quantity' => $quantity,
            'unit_price' => round($final_price / $quantity, 2),
            'total_price' => round($final_price, 2),
            'is_dealer' => $is_dealer,
            'original_price' => round($subtotal, 2)
        ];
    }

    // 3. Özel Adet Girilmişse (Örn. 53 Adet, 120 Adet vb.)
    $custom_qty = max((int)($product['min_quantity'] ?? 1), (int)($params['custom_quantity'] ?? 100));
    $setup_fee = (float)($product['base_setup_fee'] ?? 40.00);
    $unit_rate = (float)($product['custom_unit_multiplier'] ?? 1.0);

    // Tiraj İndirimi Katsayısı (Adet arttıkça birim maliyet düşer)
    $discount_factor = 1.0;
    if ($custom_qty >= 5000) $discount_factor = 0.60;
    elseif ($custom_qty >= 2000) $discount_factor = 0.70;
    elseif ($custom_qty >= 1000) $discount_factor = 0.80;
    elseif ($custom_qty >= 500) $discount_factor = 0.90;

    $subtotal = $setup_fee + ($custom_qty * $unit_rate * $discount_factor);
    $final_price = $is_dealer ? ($subtotal * (1 - $dealer_rate / 100)) : $subtotal;

    return [
        'type' => 'custom_quantity',
        'title' => $product['name'] . " ({$custom_qty} Özel Adet)",
        'specs' => "Özel Adet Girişi: {$custom_qty} Adet",
        'quantity' => $custom_qty,
        'unit_price' => round($final_price / $custom_qty, 2),
        'total_price' => round($final_price, 2),
        'is_dealer' => $is_dealer,
        'original_price' => round($subtotal, 2)
    ];
}

// Sepet Yardımcıları
function get_cart() {
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    return $_SESSION['cart'];
}

function get_cart_count() {
    return count(get_cart());
}

function get_cart_totals() {
    $cart = get_cart();
    $subtotal = 0;
    foreach ($cart as $item) {
        $subtotal += (float)$item['total_price'];
    }
    $shipping = ($subtotal >= FREE_SHIPPING_LIMIT || $subtotal == 0) ? 0.00 : 75.00;
    $grand_total = $subtotal + $shipping;

    return [
        'subtotal' => $subtotal,
        'shipping' => $shipping,
        'grand_total' => $grand_total,
        'free_shipping_eligible' => ($subtotal >= FREE_SHIPPING_LIMIT)
    ];
}
