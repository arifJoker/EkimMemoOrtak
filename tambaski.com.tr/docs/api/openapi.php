<?php
/**
 * TAMBASKI.COM.TR - Dinamik OpenAPI 3.0 Şeması
 */
require_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json; charset=utf-8');

$db = Database::getInstance()->getConnection();
$stmt = $db->query("SELECT id, name, slug, pricing_model FROM categories WHERE status = 1 ORDER BY sort_order ASC, id ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$catListDesc = "";
foreach ($categories as $c) {
    $catListDesc .= "• ID {$c['id']}: {$c['name']} (Slug: `{$c['slug']}`, Model: `{$c['pricing_model']}`)\n";
}

$spec = [
    "openapi" => "3.0.3",
    "info" => [
        "title" => "TamBaskı E-Ticaret REST API",
        "description" => "TamBaskı matbaa, dekota ve reklam ürünleri için yüksek hızlı REST API.\n\n### Aktif Kategoriler:\n" . $catListDesc,
        "version" => "1.0.0",
        "contact" => [
            "name" => "TamBaskı Sistem Mimarisi",
            "url" => SITE_URL
        ]
    ],
    "servers" => [
        [
            "url" => SITE_URL . "/api/v1",
            "description" => "Canlı REST API Sunucusu"
        ]
    ],
    "components" => [
        "securitySchemes" => [
            "ApiKeyAuth" => [
                "type" => "apiKey",
                "in" => "header",
                "name" => "X-API-KEY",
                "description" => "Memo veya Bayi API Anahtarı (Örn: tb_live_memo_7f9b2c4e1a8d5063)"
            ],
            "BearerAuth" => [
                "type" => "http",
                "scheme" => "bearer",
                "bearerFormat" => "API Key"
            ]
        ]
    ],
    "security" => [
        ["ApiKeyAuth" => []],
        ["BearerAuth" => []]
    ],
    "paths" => [
        "/upload.php" => [
            "post" => [
                "tags" => ["Medya & Görseller"],
                "summary" => "Ürün Görseli veya Mockup Yükle",
                "description" => "Görsel dosyasını (JPG, PNG) veya harici görsel linkini alır; optimize WebP formatına dönüştürüp kalıcı yükleme yolu döner.",
                "requestBody" => [
                    "required" => true,
                    "content" => [
                        "multipart/form-data" => [
                            "schema" => [
                                "type" => "object",
                                "properties" => [
                                    "file" => [
                                        "type" => "string",
                                        "format" => "binary",
                                        "description" => "Yüklenecek görsel dosyası (JPG, PNG, WebP)"
                                    ]
                                ]
                            ]
                        ],
                        "application/json" => [
                            "schema" => [
                                "type" => "object",
                                "properties" => [
                                    "image_url" => [
                                        "type" => "string",
                                        "example" => "https://example.com/gorseller/dekota_baret.jpg"
                                    ],
                                    "base64_image" => [
                                        "type" => "string",
                                        "description" => "data:image/jpeg;base64,..."
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                "responses" => [
                    "200" => [
                        "description" => "Görsel başarıyla yüklendi",
                        "content" => [
                            "application/json" => [
                                "example" => [
                                    "success" => true,
                                    "file_name" => "20260930_isg_levha.webp",
                                    "file_path" => "uploads/products/20260930_isg_levha.webp",
                                    "full_url" => SITE_URL . "/uploads/products/20260930_isg_levha.webp"
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        "/categories.php" => [
            "get" => [
                "tags" => ["Kategoriler"],
                "summary" => "Aktif Kategorileri Listele",
                "description" => "Sistemdeki tüm aktif kategorileri, ID'lerini ve fiyat modellerini (package_tier, rigid_board, m2_calculator) döner.",
                "responses" => [
                    "200" => [
                        "description" => "Kategori listesi",
                        "content" => [
                            "application/json" => [
                                "example" => [
                                    "success" => true,
                                    "count" => count($categories),
                                    "categories" => $categories
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        "/products.php" => [
            "get" => [
                "tags" => ["Ürünler"],
                "summary" => "Ürünleri Listele veya Detay Getir",
                "parameters" => [
                    [
                        "name" => "category_id",
                        "in" => "query",
                        "schema" => ["type" => "integer"],
                        "description" => "Kategoriye göre filtrele"
                    ],
                    [
                        "name" => "id",
                        "in" => "query",
                        "schema" => ["type" => "integer"],
                        "description" => "Tek bir ürünün tam detayını getir"
                    ],
                    [
                        "name" => "limit",
                        "in" => "query",
                        "schema" => ["type" => "integer", "default" => 50],
                        "description" => "Maksimum kayıt sayısı"
                    ]
                ],
                "responses" => [
                    "200" => ["description" => "Başarılı liste"]
                ]
            ],
            "post" => [
                "tags" => ["Ürünler"],
                "summary" => "Yeni Ürün Ekle (Kartvizit veya Dekota)",
                "description" => "Seçilen kategorinin modeline uygun parametrelerle ürünü, paket fiyatlarını veya USD m² değerlerini kaydeder.",
                "requestBody" => [
                    "required" => true,
                    "content" => [
                        "application/json" => [
                            "schema" => [
                                "type" => "object",
                                "required" => ["category_id", "name", "base_price"],
                                "properties" => [
                                    "category_id" => ["type" => "integer", "example" => 1],
                                    "name" => ["type" => "string", "example" => "Baret Tak İSG Levhası"],
                                    "slug" => ["type" => "string", "example" => "baret-tak-isg-levhasi"],
                                    "short_description" => ["type" => "string"],
                                    "full_description" => ["type" => "string"],
                                    "featured_image" => ["type" => "string", "example" => "uploads/products/levha.webp"],
                                    "mockup_image" => ["type" => "string", "example" => "uploads/mockups/tambaski_dekota_mockup.jpg"],
                                    "base_price" => ["type" => "number", "example" => 95.00],
                                    "tax_rate" => ["type" => "number", "example" => 20.00],
                                    "m2_usd_price_3mm" => ["type" => "number", "example" => 14.50],
                                    "m2_usd_price_5mm" => ["type" => "number", "example" => 18.50],
                                    "m2_usd_price_9mm" => ["type" => "number", "example" => 26.00],
                                    "packages" => [
                                        "type" => "object",
                                        "description" => "Kartvizit/Broşür için 4 hazır paket (ekonomik, standart, premium, vip)"
                                    ],
                                    "tiers" => [
                                        "type" => "object",
                                        "description" => "Tiraj iskonto tablosu { 1000: 0, 2000: 15, ... }"
                                    ],
                                    "allow_online_editor" => ["type" => "integer", "example" => 1],
                                    "is_featured" => ["type" => "integer", "example" => 1],
                                    "status" => ["type" => "integer", "example" => 1]
                                ]
                            ]
                        ]
                    ]
                ],
                "responses" => [
                    "201" => [
                        "description" => "Ürün başarıyla oluşturuldu",
                        "content" => [
                            "application/json" => [
                                "example" => [
                                    "success" => true,
                                    "product_id" => 42,
                                    "slug" => "baret-tak-isg-levhasi",
                                    "url" => SITE_URL . "/product.php?slug=baret-tak-isg-levhasi"
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            "put" => [
                "tags" => ["Ürünler"],
                "summary" => "Var Olan Ürünü Güncelle",
                "parameters" => [
                    [
                        "name" => "id",
                        "in" => "query",
                        "required" => true,
                        "schema" => ["type" => "integer"]
                    ]
                ],
                "responses" => [
                    "200" => ["description" => "Ürün güncellendi"]
                ]
            ],
            "delete" => [
                "tags" => ["Ürünler"],
                "summary" => "Ürünü Sil veya Durumunu Değiştir",
                "parameters" => [
                    [
                        "name" => "id",
                        "in" => "query",
                        "required" => true,
                        "schema" => ["type" => "integer"]
                    ]
                ],
                "responses" => [
                    "200" => ["description" => "Ürün silindi"]
                ]
            ]
        ]
    ]
];

echo json_encode($spec, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
