<?php
/**
 * AI & Entegrasyon REST API v1 - Şema ve Otomatik Keşif Dokümantasyonu
 * AI Ajanları bu endpoint'i okuyarak sistemin tüm yeteneklerini anlar.
 */
require_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json; charset=utf-8');

$apiSchema = [
    'api_name'        => 'Online Matbaa & E-Ticaret AI REST API',
    'version'         => '1.0.0',
    'description'     => 'AI araçları ve harici entegrasyonlar için tam özellikli ürün, varyant, adet kademesi, vektörel şablon ve sipariş yönetim API\'si.',
    'base_url'        => SITE_URL . '/api/v1',
    'authentication'  => [
        'type'        => 'API Key',
        'header_key'  => 'X-API-Key: YOUR_API_KEY',
        'bearer_auth' => 'Authorization: Bearer YOUR_API_KEY'
    ],
    'endpoints' => [
        [
            'endpoint'    => '/api/v1/products.php',
            'method'      => 'GET',
            'description' => 'Tüm ürünleri, kategorileri, varyantları ve şablonları listeler.',
            'query_params'=> ['category_id' => 'int (opsiyonel)', 'limit' => 'int (opsiyonel)']
        ],
        [
            'endpoint'    => '/api/v1/products.php',
            'method'      => 'POST',
            'description' => 'Tek bir JSON ile Ürün + Kategori + Nitelikler/Varyantlar + Adet Kademeleri + Vektörel SVG Şablonlarını otomatik oluşturur.',
            'sample_payload' => [
                'name'                => 'Altın Varak Yaldızlı Kartvizit',
                'category_name'       => 'Kartvizit',
                'sku'                 => 'YKV-901',
                'short_description'   => '350 gr. Mat Kuşe üzerine Altın Varak Yaldız baskılı prestij kartvizit.',
                'full_description'    => '<p>Lüks tasarım, altın varak detaylar ve mat selefon.</p>',
                'base_price'          => 350.00,
                'tax_rate'            => 20.00,
                'allow_design_upload' => 1,
                'allow_online_editor' => 1,
                'allow_design_service'=> 1,
                'design_service_price'=> 150.00,
                'is_featured'         => 1,
                'is_urgent'           => 1,
                'attributes' => [
                    [
                        'name' => 'Kağıt Türü',
                        'type' => 'radio',
                        'values' => [
                            ['title' => '350 gr. Mat Kuşe', 'price_extra' => 0, 'is_default' => 1],
                            ['title' => '300 gr. Tuale Dokulu', 'price_extra' => 120, 'is_default' => 0]
                        ]
                    ],
                    [
                        'name' => 'Yaldız Rengi',
                        'type' => 'radio',
                        'values' => [
                            ['title' => 'Altın Yaldız (Gold)', 'price_extra' => 0, 'is_default' => 1],
                            ['title' => 'Gümüş Yaldız (Silver)', 'price_extra' => 0, 'is_default' => 0],
                            ['title' => 'Rose Gold Yaldız', 'price_extra' => 50, 'is_default' => 0]
                        ]
                    ]
                ],
                'quantity_tiers' => [
                    ['quantity' => 100, 'multiplier' => 1.0, 'fixed_price' => 350.00, 'discount_percent' => 0],
                    ['quantity' => 250, 'multiplier' => 1.4, 'fixed_price' => 490.00, 'discount_percent' => 15],
                    ['quantity' => 500, 'multiplier' => 1.8, 'fixed_price' => 650.00, 'discount_percent' => 25],
                    ['quantity' => 1000, 'multiplier' => 2.5, 'fixed_price' => 950.00, 'discount_percent' => 40]
                ],
                'templates' => [
                    [
                        'title'         => 'Lüks Gold & Dark Tema',
                        'category'      => 'Lüks',
                        'canvas_width'  => 850,
                        'canvas_height' => 500,
                        'default_svg'   => '<svg viewBox="0 0 850 500"><rect width="850" height="500" fill="#111"/><text id="personName" x="50" y="200" fill="#d4af37">Ad Soyad</text></svg>',
                        'fields'        => [
                            ['id' => 'personName', 'label' => 'Ad Soyad', 'default' => 'Ad Soyad']
                        ]
                    ]
                ]
            ]
        ],
        [
            'endpoint'    => '/api/v1/categories.php',
            'method'      => 'GET / POST',
            'description' => 'Kategorileri listeler veya yeni kategori ekler.'
        ],
        [
            'endpoint'    => '/api/v1/templates.php',
            'method'      => 'GET / POST',
            'description' => 'Vektörel SVG şablonlarını yönetir.'
        ],
        [
            'endpoint'    => '/api/v1/orders.php',
            'method'      => 'GET',
            'description' => 'Siparişleri, müşterilerin yüklediği matbaa dosyalarını ve SVG tasarımlarını listeler.'
        ],
        [
            'endpoint'    => '/api/v1/upload.php',
            'method'      => 'POST',
            'description' => 'Mockup veya ürün görseli yükler (multipart/form-data veya Base64).'
        ]
    ]
];

Helper::jsonResponse($apiSchema, 200);
