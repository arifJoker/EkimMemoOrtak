<?php
/**
 * Ürün, Matbaa Varyantları, Fiyat Matrisi ve AI Entegrasyon Sınıfı
 */
class Product {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Tüm Aktif Ürünleri Getirir
     */
    public function getAll($limit = null, $categoryId = null, $onlyFeatured = false, $onlyUrgent = false) {
        $sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 1";
        $params = [];

        if ($categoryId) {
            $sql .= " AND (p.category_id = ? OR c.parent_id = ?)";
            $params[] = $categoryId;
            $params[] = $categoryId;
        }

        if ($onlyFeatured) {
            $sql .= " AND p.is_featured = 1";
        }

        if ($onlyUrgent) {
            $sql .= " AND p.is_urgent = 1";
        }

        $sql .= " ORDER BY p.sort_order ASC, p.id DESC";

        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll();

        foreach ($products as &$prod) {
            $prod['starting_price'] = $this->getStartingPrice($prod);
            $prod['base_price'] = $prod['starting_price'];
        }

        return $products;
    }

    /**
     * Ürünün Vitrin Başlangıç Fiyatını (1000 Adet veya Min Tiraj) Döndürür
     */
    public function getStartingPrice($product) {
        if (is_numeric($product)) {
            $product = $this->getById((int)$product);
        }
        if (!$product) return 0.00;

        // 1. Manuel özel vitrin başlangıç fiyatı verilmişse (custom_starting_price)
        if (!empty($product['custom_starting_price']) && (float)$product['custom_starting_price'] > 0) {
            return (float)$product['custom_starting_price'];
        }

        // 2. Minimum adet kademesini bul
        $tiers = $this->getQuantityTiers($product['id']);
        $minQty = 1000;
        if (!empty($tiers) && isset($tiers[0]['quantity'])) {
            $minQty = (int)$tiers[0]['quantity'];
        }

        $lowestPrice = null;

        // 3. Fiyat motoru ile ekonomik paket fiyatını hesapla (Kargo ve KDV Hariç En Uygun Fiyat)
        $calcEko = $this->calculatePrice($product['id'], $minQty, [], 0, 0, false, 'ekonomik');
        if (!empty($calcEko['success']) && isset($calcEko['subtotal']) && $calcEko['subtotal'] > 0) {
            $lowestPrice = (float)$calcEko['subtotal'];
        }

        // 4. Standart paket fiyatını hesapla
        $calcStd = $this->calculatePrice($product['id'], $minQty, [], 0, 0, false, 'standart');
        if (!empty($calcStd['success']) && isset($calcStd['subtotal']) && $calcStd['subtotal'] > 0) {
            if ($lowestPrice === null || (float)$calcStd['subtotal'] < $lowestPrice) {
                $lowestPrice = (float)$calcStd['subtotal'];
            }
        }

        if ($lowestPrice !== null && $lowestPrice > 0) {
            return $lowestPrice;
        }

        $basePrice = (float)($product['base_price'] ?? 0);
        return $basePrice > 0 ? $basePrice : 0.00;
    }

    /**
     * Slug ile Ürün ve Detaylarını Getirir
     */
    public function getBySlug($slug) {
        $stmt = $this->db->prepare("SELECT p.*, c.name AS category_name, c.slug AS category_slug 
                                    FROM products p 
                                    LEFT JOIN categories c ON p.category_id = c.id 
                                    WHERE p.slug = ? AND p.status = 1");
        $stmt->execute([$slug]);
        $product = $stmt->fetch();

        if ($product) {
            $product['attributes'] = $this->getAttributes($product['id']);
            $product['quantity_tiers'] = $this->getQuantityTiers($product['id']);
            $product['templates'] = $this->getTemplates($product['id'], $product);
            $product['variant_groups'] = $this->getVariantGroups($product['id'], $product);
            $product['allowed_papers_list'] = $this->getAllowedPapers($product['id'], $product);
            $product['gallery_array'] = !empty($product['gallery']) ? json_decode($product['gallery'], true) : [];
        }

        return $product;
    }

    /**
     * ID ile Ürün Getirir
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        if ($product) {
            $product['attributes'] = $this->getAttributes($product['id']);
            $product['quantity_tiers'] = $this->getQuantityTiers($product['id']);
            $product['templates'] = $this->getTemplates($product['id'], $product);
            $product['variant_groups'] = $this->getVariantGroups($product['id'], $product);
            $product['allowed_papers_list'] = $this->getAllowedPapers($product['id'], $product);
            $product['gallery_array'] = !empty($product['gallery']) ? json_decode($product['gallery'], true) : [];
        }

        return $product;
    }

    /**
     * Ürüne Ait İzinli Kağıt Türlerini Getirir
     */
    public function getAllowedPapers($productId, $productRow = null) {
        if ($productRow === null) {
            $stmt = $this->db->prepare("SELECT allowed_papers FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $productRow = $stmt->fetch();
        }
        $allowed = !empty($productRow['allowed_papers']) ? json_decode($productRow['allowed_papers'], true) : [];
        if (!empty($allowed) && is_array($allowed)) {
            $inClause = implode(',', array_map('intval', $allowed));
            if (!empty($inClause)) {
                $stmt = $this->db->query("SELECT * FROM paper_types WHERE id IN ($inClause) AND status = 1 ORDER BY gsm ASC");
                $papers = $stmt ? $stmt->fetchAll() : [];
                if (!empty($papers)) return $papers;
            }
        }
        return $this->db->query("SELECT * FROM paper_types WHERE status = 1 ORDER BY gsm ASC")->fetchAll();
    }

    /**
     * Ürüne Ait Özellikleri ve Değerlerini Getirir
     */
    public function getAttributes($productId) {
        $stmt = $this->db->prepare("SELECT * FROM product_attributes WHERE product_id = ? ORDER BY sort_order ASC, id ASC");
        $stmt->execute([$productId]);
        $attributes = $stmt->fetchAll();

        foreach ($attributes as &$attr) {
            $valStmt = $this->db->prepare("SELECT * FROM product_attribute_values WHERE attribute_id = ? ORDER BY sort_order ASC, id ASC");
            $valStmt->execute([$attr['id']]);
            $attr['values'] = $valStmt->fetchAll();
        }

        return $attributes;
    }

    /**
     * Ürüne Ait Adet Kademelerini Getirir
     */
    public function getQuantityTiers($productId) {
        $stmt = $this->db->prepare("SELECT * FROM product_quantity_tiers WHERE product_id = ? ORDER BY quantity ASC");
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    /**
     * Tüm Sektörleri Getirir
     */
    public function getIndustries() {
        return $this->db->query("SELECT * FROM industries WHERE status = 1 ORDER BY sort_order ASC, id ASC")->fetchAll();
    }

    /**
     * Ürüne Ait Hazır Vektörel Şablonları Getirir (Sektör Bilgileriyle)
     */
    public function getTemplates($productId, $productRow = null) {
        if ($productRow === null) {
            $stmt = $this->db->prepare("SELECT p.*, c.slug AS category_slug FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
            $stmt->execute([$productId]);
            $productRow = $stmt->fetch();
        }

        if (!$productRow || empty($productRow['allow_online_editor'])) {
            return [];
        }

        // Ürün Türünü Belirle (Kartvizit, El İlanı / Broşür, Cepli Dosya, Kaşe)
        $catSlug = $productRow['category_slug'] ?? '';
        $prodSlug = $productRow['slug'] ?? '';
        $prodName = mb_strtolower($productRow['name'] ?? '', 'UTF-8');

        $detectedType = 'kartvizit';
        if (str_contains($catSlug, 'brosur') || str_contains($catSlug, 'ilan') || str_contains($prodSlug, 'brosur') || str_contains($prodSlug, 'ilan') || str_contains($prodName, 'broşür') || str_contains($prodName, 'el ilanı')) {
            $detectedType = 'brosur';
        } elseif (str_contains($catSlug, 'dosya') || str_contains($prodSlug, 'dosya') || str_contains($prodName, 'dosya')) {
            $detectedType = 'cepli-dosya';
        } elseif (str_contains($catSlug, 'kase') || str_contains($prodSlug, 'kase') || str_contains($prodName, 'kaşe')) {
            $detectedType = 'kase';
        }

        // 1. Ürüne özel seçilmiş şablon ID listesi varsa
        $allowedTplIds = !empty($productRow['allowed_templates']) ? json_decode($productRow['allowed_templates'], true) : [];
        if (!empty($allowedTplIds) && is_array($allowedTplIds)) {
            $inClause = implode(',', array_map('intval', $allowedTplIds));
            if (!empty($inClause)) {
                $sql = "SELECT dt.*, i.name AS industry_name, i.icon AS industry_icon 
                        FROM design_templates dt 
                        LEFT JOIN industries i ON dt.industry_slug = i.slug 
                        WHERE dt.id IN ($inClause) AND dt.status = 1 
                        ORDER BY i.sort_order ASC, dt.id ASC";
                $stmt = $this->db->query($sql);
                $templates = $stmt ? $stmt->fetchAll() : [];
                if (!empty($templates)) {
                    foreach ($templates as &$tpl) {
                        $tpl['fields'] = !empty($tpl['template_data']) ? json_decode($tpl['template_data'], true) : null;
                    }
                    return $templates;
                }
            }
        }

        // 2. Ürüne veya Ürün Türüne (kartvizit, brosur, cepli-dosya vb.) göre uyumlu şablonları getir
        $sql = "SELECT dt.*, i.name AS industry_name, i.icon AS industry_icon 
                FROM design_templates dt 
                LEFT JOIN industries i ON dt.industry_slug = i.slug 
                WHERE dt.status = 1 
                  AND (dt.product_id = ? OR dt.product_type = ? OR (dt.product_type IS NULL AND ? = 'kartvizit'))
                ORDER BY i.sort_order ASC, dt.id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int)$productId, $detectedType, $detectedType]);
        $templates = $stmt ? $stmt->fetchAll() : [];

        // Eğer türe göre bulunamazsa fallback olarak genel aktif şablonları getir
        if (empty($templates)) {
            $sql = "SELECT dt.*, i.name AS industry_name, i.icon AS industry_icon 
                    FROM design_templates dt 
                    LEFT JOIN industries i ON dt.industry_slug = i.slug 
                    WHERE dt.status = 1 
                    ORDER BY i.sort_order ASC, dt.id ASC";
            $stmt = $this->db->query($sql);
            $templates = $stmt ? $stmt->fetchAll() : [];
        }

        foreach ($templates as &$tpl) {
            $tpl['fields'] = !empty($tpl['template_data']) ? json_decode($tpl['template_data'], true) : null;
        }
        return $templates;
    }

    /**
     * Sektöre Göre Önerilen Tamamlayıcı Ürünleri Getirir
     */
    public function getRecommendedProductsByIndustry($industrySlug, $excludeProductId = null, $limit = 4) {
        if (empty($industrySlug)) {
            $industrySlug = 'genel-kurumsal';
        }

        $sql = "SELECT p.*, c.name AS category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 1";
        $params = [];

        if ($excludeProductId) {
            $sql .= " AND p.id != ?";
            $params[] = (int)$excludeProductId;
        }

        $sql .= " AND (p.target_industries LIKE ? OR p.target_industries IS NULL OR p.target_industries = '' OR p.target_industries = '[]')";
        $params[] = '%' . $industrySlug . '%';

        $sql .= " ORDER BY p.is_featured DESC, p.sort_order ASC, p.id DESC LIMIT " . (int)$limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll();

        // Eğer sektör ürünü azsa diğer popüler ürünlerle tamamla
        if (count($products) < $limit) {
            $existingIds = array_column($products, 'id');
            if ($excludeProductId) $existingIds[] = (int)$excludeProductId;
            $notIn = !empty($existingIds) ? "AND p.id NOT IN (" . implode(',', $existingIds) . ")" : "";
            
            $remainLimit = $limit - count($products);
            $stmtExtra = $this->db->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.status = 1 $notIn ORDER BY p.is_featured DESC, p.id DESC LIMIT $remainLimit");
            if ($stmtExtra) {
                $extraProds = $stmtExtra->fetchAll();
                $products = array_merge($products, $extraProds);
            }
        }

        return $products;
    }

    /**
     * Ürüne Ait Aktif Varyant Gruplarını ve Seçeneklerini Getirir
     */
    public function getVariantGroups($productId = null, $productRow = null) {
        $allowedGroups = [];
        $allowedOptions = [];
        if ($productId) {
            if ($productRow === null) {
                $stmt = $this->db->prepare("SELECT allowed_variant_groups, allowed_variant_options FROM products WHERE id = ?");
                $stmt->execute([$productId]);
                $productRow = $stmt->fetch();
            }
            if (!empty($productRow['allowed_variant_groups'])) {
                $allowedGroups = json_decode($productRow['allowed_variant_groups'], true);
            }
            if (!empty($productRow['allowed_variant_options'])) {
                $allowedOptions = json_decode($productRow['allowed_variant_options'], true);
            }
        }

        $sql = "SELECT * FROM variant_groups WHERE status = 1";
        if (!empty($allowedGroups) && is_array($allowedGroups)) {
            $inClause = implode(',', array_map('intval', $allowedGroups));
            if (!empty($inClause)) {
                $sql .= " AND id IN ($inClause)";
            }
        }
        $sql .= " ORDER BY sort_order ASC, id ASC";

        $stmt = $this->db->query($sql);
        $groups = $stmt ? $stmt->fetchAll() : [];
        $finalGroups = [];

        foreach ($groups as $grp) {
            $optSql = "SELECT * FROM variant_options WHERE group_id = ? AND status = 1";
            if (!empty($allowedOptions) && is_array($allowedOptions)) {
                $inOptClause = implode(',', array_map('intval', $allowedOptions));
                if (!empty($inOptClause)) {
                    $optSql .= " AND id IN ($inOptClause)";
                }
            }
            $optSql .= " ORDER BY sort_order ASC, id ASC";

            $optStmt = $this->db->prepare($optSql);
            $optStmt->execute([$grp['id']]);
            $opts = $optStmt->fetchAll();

            if (!empty($opts)) {
                $grp['options'] = $opts;
                $finalGroups[] = $grp;
            }
        }

        return $finalGroups;
    }

    /**
     * Tiraj Çarpanını Hesaplar (Adet arttıkça birim maliyetin düşüş eğrisi)
    /**
     * Tiraj Çarpanını Hesaplar (Adet arttıkça birim maliyetin düşüş eğrisi - 1.000 Adet = 1.00 Baz)
     */
    public function getTierMultiplier($quantity, $productId = null) {
        if ($productId) {
            $tiers = $this->getQuantityTiers($productId);
            if (!empty($tiers)) {
                foreach ($tiers as $t) {
                    if ((int)$t['quantity'] === (int)$quantity && isset($t['multiplier'])) {
                        $m = (float)$t['multiplier'];
                        if ($m > 0) {
                            // Eğer tier çarpanı birim indirim olarak (< 1.0) girildiyse toplam çarpana çevir
                            if ($quantity > 1000 && $m < 1.0) {
                                return ($quantity / 1000) * $m;
                            }
                            return $m;
                        }
                    }
                }
            }
        }

        // Standart Matbaa Ofset Tiraj Eğrisi (1.000 Adet = 1.00 Baz)
        if ($quantity == 1000) return 1.00;
        if ($quantity == 2000) return 1.70;
        if ($quantity == 3000) return 2.40;
        if ($quantity == 5000) return 3.65;
        if ($quantity == 10000) return 6.80;
        if ($quantity == 500) return 0.75;
        if ($quantity == 250) return 0.60;
        if ($quantity == 100) return 0.50;

        if ($quantity < 1000) {
            return max(0.40, 0.50 + (($quantity - 100) / 900) * 0.50);
        } else {
            return pow($quantity / 1000, 0.82);
        }
    }

    /**
     * 70x100 Tabaka, Döviz ve Matbaa Fiyat Hesaplama Motoru
     */
    public function calculatePrice($productId, $quantity, $selectedOptions = [], $customWidth = 0, $customHeight = 0, $includeDesignService = false, $selectedPackage = 'standart', $customPaperId = 0) {
        $product = $this->getById($productId);
        if (!$product) {
            return ['success' => false, 'error' => 'Ürün bulunamadı.'];
        }

        $usdRate = Helper::getUsdRate();
        $laborPercent = (float)Helper::getSetting('cutting_labor_percent', 5);
        $giftThreshold = (int)Helper::getSetting('gift_waste_threshold', 2);

        // 1. Ölçü Tespiti (Standart veya Özel)
        $stdW = (float)($product['standard_width'] ?? 8.40);
        $stdH = (float)($product['standard_height'] ?? 5.20);
        $w = $stdW;
        $h = $stdH;
        $isCustom = false;

        if ($product['is_custom_size'] && (float)$customWidth > 0 && (float)$customHeight > 0) {
            $inputW = (float)$customWidth;
            $inputH = (float)$customHeight;
            if (abs($inputW - $stdW) > 0.05 || abs($inputH - $stdH) > 0.05) {
                $w = $inputW;
                $h = $inputH;
                $isCustom = true;
            }
        }

        // 2. 70x100 Tabaka Yerleşimi
        $placement = Helper::calculateSheetPlacement($w, $h, 70, 100, 0.4);
        $itemsPerSheet = max(1, (int)$placement['items_per_sheet']);
        $quantity = max(1, (int)$quantity);

        // 3. Kağıt ve Selefon / Varyant Fiyatlarının Tespiti
        $paperUsd = 7.00; // Varsayılan 350gr Kuşe
        $finishingSheetUsd = 0.00;
        $fixedFeeUsd = 0.00;
        $extraFixedTry = (float)($product['extra_fixed_fee'] ?? 0);
        $extraPercent = (float)($product['extra_percent_fee'] ?? 0);

        $packageSuppCost = 0.00;

        // Paket presetine göre kağıt ve yüzey işlemini çek
        $presets = !empty($product['package_presets']) ? json_decode($product['package_presets'], true) : [];
        if (!empty($presets[$selectedPackage])) {
            $preset = $presets[$selectedPackage];
            if (isset($preset['supplier_cost_1000']) && (float)$preset['supplier_cost_1000'] > 0) {
                $packageSuppCost = (float)$preset['supplier_cost_1000'];
            }

            $paperId = (int)($preset['paper_id'] ?? 0);
            $finId = (int)($preset['fin_id'] ?? 0);

            if ($paperId > 0) {
                $pStmt = $this->db->prepare("SELECT price_usd_70x100 FROM paper_types WHERE id = ?");
                $pStmt->execute([$paperId]);
                $pRow = $pStmt->fetch();
                if ($pRow) {
                    $paperUsd = (float)$pRow['price_usd_70x100'];
                }
            }
            if ($finId > 0) {
                $fStmt = $this->db->prepare("SELECT * FROM finishing_options WHERE id = ?");
                $fStmt->execute([$finId]);
                $fRow = $fStmt->fetch();
                if ($fRow) {
                    $cType = $fRow['calc_type'] ?? 'sheet_usd';
                    if ($cType === 'percent') {
                        $extraPercent += (float)$fRow['percent_fee'];
                    } elseif ($cType === 'per_unit_try') {
                        $extraFixedTry += ($quantity * (float)$fRow['per_unit_fee_try']);
                    } elseif ($cType === 'fixed_try') {
                        $extraFixedTry += (float)$fRow['fixed_fee_try'];
                    } elseif ($cType === 'fixed_usd') {
                        $fixedFeeUsd += (float)$fRow['fixed_fee_usd'];
                    } else {
                        $finishingSheetUsd += (float)$fRow['price_usd_70x100'];
                        $fixedFeeUsd += (float)($fRow['fixed_fee_usd'] ?? 0);
                    }
                }
            }
        }

        // Eğer müşterinin özel ince ayardan seçtiği kağıt varsa paket kağıdını ez
        if ($customPaperId > 0) {
            $cpStmt = $this->db->prepare("SELECT price_usd_70x100 FROM paper_types WHERE id = ?");
            $cpStmt->execute([$customPaperId]);
            $cpRow = $cpStmt->fetch();
            if ($cpRow) {
                $paperUsd = (float)$cpRow['price_usd_70x100'];
            }
        }

        $suppOptDelta1000 = 0.00;

        // Eğer müşterinin özel panelden seçtiği ek varyantlar varsa
        if (!empty($selectedOptions) && is_array($selectedOptions)) {
            foreach ($selectedOptions as $optKey => $optVal) {
                if (is_numeric($optVal) && (int)$optVal > 0) {
                    $optId = (int)$optVal;
                    // 1. variant_options kontrolü
                    $vStmt = $this->db->prepare("SELECT * FROM variant_options WHERE id = ?");
                    $vStmt->execute([$optId]);
                    $vRow = $vStmt->fetch();
                    if ($vRow) {
                        if (isset($vRow['supplier_cost_1000'])) {
                            $suppOptDelta1000 += (float)$vRow['supplier_cost_1000'];
                        }

                        $cType = $vRow['calc_type'] ?? 'per_unit_try';
                        if ($cType === 'percent') {
                            $extraPercent += (float)$vRow['percent_fee'];
                        } elseif ($cType === 'per_unit_try') {
                            $extraFixedTry += ($quantity * (float)$vRow['per_unit_fee_try']);
                        } elseif ($cType === 'fixed_try') {
                            $extraFixedTry += (float)$vRow['fixed_fee_try'];
                        } elseif ($cType === 'fixed_usd') {
                            $fixedFeeUsd += (float)$vRow['fixed_fee_usd'];
                        } else {
                            $finishingSheetUsd += (float)$vRow['price_usd_70x100'];
                            $fixedFeeUsd += (float)($vRow['fixed_fee_usd'] ?? 0);
                        }
                    } else {
                        // 2. finishing_options kontrolü
                        $fStmt = $this->db->prepare("SELECT * FROM finishing_options WHERE id = ?");
                        $fStmt->execute([$optId]);
                        $fRow = $fStmt->fetch();
                        if ($fRow) {
                            $cType = $fRow['calc_type'] ?? 'sheet_usd';
                            if ($cType === 'percent') {
                                $extraPercent += (float)$fRow['percent_fee'];
                            } elseif ($cType === 'per_unit_try') {
                                $extraFixedTry += ($quantity * (float)$fRow['per_unit_fee_try']);
                            } elseif ($cType === 'fixed_try') {
                                $extraFixedTry += (float)$fRow['fixed_fee_try'];
                            } elseif ($cType === 'fixed_usd') {
                                $fixedFeeUsd += (float)$fRow['fixed_fee_usd'];
                            } else {
                                $finishingSheetUsd += (float)$fRow['price_usd_70x100'];
                                $fixedFeeUsd += (float)($fRow['fixed_fee_usd'] ?? 0);
                            }
                        }
                    }
                }
            }
        }

        // 4. Fiyatlandırma Modu Tespiti (Otomatik m² / Tedarikçi vs Manuel Sabit Fiyat)
        $globalMode = Helper::getSetting('default_pricing_mode', 'auto_m2');
        $pricingMode = !empty($product['pricing_mode']) ? $product['pricing_mode'] : $globalMode;
        $profitMargin = (isset($product['profit_margin_percent']) && (float)$product['profit_margin_percent'] > 0) 
                        ? (float)$product['profit_margin_percent'] 
                        : (float)Helper::getSetting('global_profit_margin', 50.0);

        $baseRawCost = ($packageSuppCost > 0) ? $packageSuppCost : (float)($product['supplier_cost_1000'] ?? 220.00);
        $suppBaseCost1000 = $baseRawCost + $suppOptDelta1000;



        if ($pricingMode === 'manual') {
            // MANUEL SABİT FİYAT MODU:
            $manualBase = (float)(!empty($product['manual_base_price']) ? $product['manual_base_price'] : ($product['base_price'] ?? 0));
            $tierMultiplier = $this->getTierMultiplier($quantity, $productId);
            $manualSubtotal = $manualBase * $tierMultiplier;

            // Eğer müşteri özel ebat girdiyse alan oranında ölçekle
            if ($isCustom && $stdW > 0 && $stdH > 0) {
                $manualSubtotal *= (($w * $h) / ($stdW * $stdH));
            }

            $rawCostTry = $manualSubtotal;
            $calculatedSubtotal = $manualSubtotal + $extraFixedTry;
            if ($extraPercent > 0) {
                $calculatedSubtotal *= (1 + ($extraPercent / 100));
            }
            $sheetsNeeded = max(0.4, ($quantity / $itemsPerSheet));
            $totalProduced = $quantity;
        } else {
            // OTOMATİK M² & TEDARİKÇİ TABANLI FİYAT MODU:
            if ($suppBaseCost1000 > 0) {
                // 1. Doğrudan Tedarikçi 1000 Adet Alış Maliyeti Tanımlıysa (Örn: Türmatsan 450 TL):
                $stdAreaSingleM2 = ($stdW * $stdH) / 10000;
                $tierMultiplier = $this->getTierMultiplier($quantity, $productId);

                if ($isCustom && $stdAreaSingleM2 > 0) {
                    $customAreaSingleM2 = ($w * $h) / 10000;
                    $areaRatio = $customAreaSingleM2 / $stdAreaSingleM2;
                    $rawCostTry = ($suppBaseCost1000 * $tierMultiplier) * $areaRatio;
                    
                    $sheetsNeeded = (int)ceil($quantity / $itemsPerSheet);
                    $totalProduced = $sheetsNeeded * $itemsPerSheet;
                    $giftCount = max(0, $totalProduced - $quantity);
                } else {
                    $rawCostTry = $suppBaseCost1000 * $tierMultiplier;
                    $sheetsNeeded = max(0.4, ($quantity / $itemsPerSheet));
                    $totalProduced = $quantity;
                    $giftCount = 0;
                }
            } else {
                // 2. Tabaka Hammadde USD Parametrelerinden Hesaplama:
                if ($isCustom) {
                    $sheetsNeeded = (int)ceil($quantity / $itemsPerSheet);
                    $totalProduced = $sheetsNeeded * $itemsPerSheet;
                    $giftCount = max(0, $totalProduced - $quantity);
                    $fullSheetsCostUsd = ($sheetsNeeded * ($paperUsd + $finishingSheetUsd)) + $fixedFeeUsd;
                    if ($quantity < $totalProduced) {
                        $fraction = $quantity / $totalProduced;
                        $sheetTotalUsd = $fullSheetsCostUsd * (0.65 + (0.35 * $fraction));
                    } else {
                        $sheetTotalUsd = $fullSheetsCostUsd;
                    }
                    $totalWithLaborUsd = $sheetTotalUsd * (1 + ($laborPercent / 100));
                } else {
                    $tierMultiplier = $this->getTierMultiplier($quantity, $productId);
                    $baseSheetTotalUsd = (($paperUsd + $finishingSheetUsd) * (1 + ($laborPercent / 100))) + $fixedFeeUsd;
                    $totalWithLaborUsd = $baseSheetTotalUsd * $tierMultiplier;
                    $totalProduced = $quantity;
                    $giftCount = 0;
                    $sheetsNeeded = max(0.4, ($quantity / $itemsPerSheet));
                }
                $rawCostTry = ($totalWithLaborUsd * $usdRate);
            }

            // Dinamik Kâr Marjı Ekleme (% profit_margin_percent - Örn: +%100 veya +%50)
            $profitMultiplier = 1 + ($profitMargin / 100);
            $calculatedSubtotal = ($rawCostTry * $profitMultiplier) + $extraFixedTry;

            if ($extraPercent > 0) {
                $calculatedSubtotal *= (1 + ($extraPercent / 100));
            }
        }

        // 5. Tasarım Desteği Eklentisi
        $designServiceCost = 0.00;
        if ($includeDesignService && $product['allow_design_service']) {
            $designServiceCost = (float)$product['design_service_price'];
            $calculatedSubtotal += $designServiceCost;
        }

        // 6. B2B / Bayi İskontosu
        $dealerDiscountRate = Auth::getDiscountRate();
        $dealerDiscountAmount = 0.00;
        if ($dealerDiscountRate > 0) {
            $dealerDiscountAmount = $calculatedSubtotal * ($dealerDiscountRate / 100);
            $calculatedSubtotal -= $dealerDiscountAmount;
        }

        // 7. KDV Hesaplama
        $taxRate = (float)$product['tax_rate'];
        $taxAmount = $calculatedSubtotal * ($taxRate / 100);
        $totalWithTax = $calculatedSubtotal + $taxAmount;
        $unitPrice = $quantity > 0 ? ($calculatedSubtotal / $quantity) : 0;

        // 8. Avantajlı Üretim & Akıllı Adet Tavsiyesi (Yalnızca Özel Ölçüde veya 1.000 Altı Özel Adetlerde Çalışır)
        $upsell = null;
        $targetQty = 0;

        if ($isCustom && $quantity < $totalProduced) {
            // Özel ebatta tam tabaka adedine tamamlama önerisi
            $targetQty = $totalProduced;
        } elseif (!$isCustom && $quantity < 1000) {
            // Standart ebatta 1.000 altı özel adet girilmişse firesiz 1.000 tam pakete tamamlama önerisi
            $targetQty = 1000;
        }

        if ($targetQty > $quantity) {
            if ($isCustom) {
                $targetSheetsNeeded = (int)ceil($targetQty / $itemsPerSheet);
                $targetFullCostUsd = ($targetSheetsNeeded * ($paperUsd + $finishingSheetUsd)) + $fixedFeeUsd;
                $targetTotalWithLaborUsd = $targetFullCostUsd * (1 + ($laborPercent / 100));
            } else {
                $targetTierMultiplier = $this->getTierMultiplier($targetQty, $productId);
                $baseSheetTotalUsd = (($paperUsd + $finishingSheetUsd) * (1 + ($laborPercent / 100))) + $fixedFeeUsd;
                $targetTotalWithLaborUsd = $baseSheetTotalUsd * $targetTierMultiplier;
            }

            $targetSubtotal = ($targetTotalWithLaborUsd * $usdRate) + $extraFixedTry;
            if ($extraPercent > 0) {
                $targetSubtotal *= (1 + ($extraPercent / 100));
            }
            if ($includeDesignService && $product['allow_design_service']) {
                $targetSubtotal += (float)$product['design_service_price'];
            }
            if ($dealerDiscountRate > 0) {
                $targetSubtotal -= ($targetSubtotal * ($dealerDiscountRate / 100));
            }
            $targetTax = $targetSubtotal * ($taxRate / 100);
            $targetGrandTotal = $targetSubtotal + $targetTax;
            $targetUnitPrice = $targetQty > 0 ? ($targetGrandTotal / $targetQty) : 0;
            $currentUnitPriceWithTax = $quantity > 0 ? ($totalWithTax / $quantity) : 0;

            $diffAmount = max(0, $targetGrandTotal - $totalWithTax);
            $addedQty = $targetQty - $quantity;

            if ($addedQty > 0 && $diffAmount > 0 && $targetUnitPrice < ($currentUnitPriceWithTax * 0.98)) {
                $unitDiscountPercent = $currentUnitPriceWithTax > 0 ? (int)round((1 - ($targetUnitPrice / $currentUnitPriceWithTax)) * 100) : 0;
                $upsellMsg = "Bu ebatta en avantajlı üretim adedi <strong>{$targetQty} Adet</strong>tir. Sadece <strong>+" . Helper::formatPrice($diffAmount) . "</strong> farkla <strong>+{$addedQty} Adet daha</strong> (Toplam {$targetQty} Adet) alabilirsiniz! Birim fiyatınız " . Helper::formatPrice($currentUnitPriceWithTax, ' ₺') . " yerine <strong class='text-success'>" . Helper::formatPrice($targetUnitPrice, ' ₺') . "</strong>'ye (%{$unitDiscountPercent} daha indirimli) düşer.";

                $upsell = [
                    'active'                       => true,
                    'target_quantity'              => $targetQty,
                    'added_quantity'               => $addedQty,
                    'diff_amount'                  => round($diffAmount, 2),
                    'formatted_diff'               => '+' . Helper::formatPrice($diffAmount),
                    'target_total'                 => round($targetGrandTotal, 2),
                    'formatted_target_total'       => Helper::formatPrice($targetGrandTotal),
                    'current_unit_price'           => round($currentUnitPriceWithTax, 2),
                    'formatted_current_unit_price' => Helper::formatPrice($currentUnitPriceWithTax, ' ₺'),
                    'target_unit_price'            => round($targetUnitPrice, 2),
                    'formatted_target_unit_price'  => Helper::formatPrice($targetUnitPrice, ' ₺'),
                    'unit_discount_pct'            => $unitDiscountPercent,
                    'message'                      => $upsellMsg
                ];
            }
        }

        return [
            'success'               => true,
            'product_id'            => $productId,
            'quantity'              => $quantity,
            'usd_rate'              => $usdRate,
            'sheets_needed'         => $sheetsNeeded,
            'items_per_sheet'       => $itemsPerSheet,
            'total_produced'        => $totalProduced,
            'upsell'                => $upsell,
            'waste_percent'         => $placement['waste_percent'],
            'is_custom_size'        => $isCustom,
            'pricing_mode'          => $pricingMode,
            'profit_margin_percent' => $profitMargin,
            'raw_cost_try'          => round($rawCostTry ?? 0, 2),
            'subtotal'              => round($calculatedSubtotal, 2),
            'tax_rate'              => $taxRate,
            'tax_amount'            => round($taxAmount, 2),
            'grand_total'           => round($totalWithTax, 2),
            'unit_price'            => round($unitPrice, 4),
            'formatted_subtotal'    => Helper::formatPrice($calculatedSubtotal),
            'formatted_total'       => Helper::formatPrice($totalWithTax),
            'formatted_unit_price'  => Helper::formatPrice($unitPrice, ' ₺ / adet')
        ];
    }

    /**
     * AI Araçları & REST API için Komple Ürün, Varyant ve Şablon Oluşturucu
     */
    public function createFullProductFromAI($data) {
        try {
            $this->db->beginTransaction();

            // 1. Kategori Kontrolü veya Otomatik Oluşturma
            $categoryId = $data['category_id'] ?? null;
            if (!$categoryId && !empty($data['category_name'])) {
                $catSlug = Helper::slugify($data['category_name']);
                $catStmt = $this->db->prepare("SELECT id FROM categories WHERE slug = ?");
                $catStmt->execute([$catSlug]);
                $catRow = $catStmt->fetch();
                if ($catRow) {
                    $categoryId = $catRow['id'];
                } else {
                    $insCat = $this->db->prepare("INSERT INTO categories (name, slug, description, icon) VALUES (?, ?, ?, ?)");
                    $insCat->execute([$data['category_name'], $catSlug, $data['category_description'] ?? '', $data['category_icon'] ?? 'bi bi-box-seam']);
                    $categoryId = $this->db->lastInsertId();
                }
            }

            if (!$categoryId) {
                $categoryId = 1; // Varsayılan
            }

            // 2. Ürün Ana Kaydı
            $slug = !empty($data['slug']) ? Helper::slugify($data['slug']) : Helper::slugify($data['name']);
            // Benzersiz slug kontrolü
            $slugCheck = $this->db->prepare("SELECT id FROM products WHERE slug = ?");
            $slugCheck->execute([$slug]);
            if ($slugCheck->fetch()) {
                $slug .= '-' . bin2hex(random_bytes(2));
            }

            $prodStmt = $this->db->prepare("INSERT INTO products (
                category_id, name, slug, sku, short_description, full_description, 
                base_price, tax_rate, is_custom_size, min_width, max_width, min_height, max_height, price_per_sqm,
                allow_design_upload, allow_online_editor, allow_design_service, design_service_price,
                featured_image, gallery, is_featured, is_urgent, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $prodStmt->execute([
                $categoryId,
                $data['name'],
                $slug,
                $data['sku'] ?? 'AI-' . strtoupper(bin2hex(random_bytes(3))),
                $data['short_description'] ?? '',
                $data['full_description'] ?? '',
                $data['base_price'] ?? 100.00,
                $data['tax_rate'] ?? 20.00,
                !empty($data['is_custom_size']) ? 1 : 0,
                $data['min_width'] ?? 0,
                $data['max_width'] ?? 0,
                $data['min_height'] ?? 0,
                $data['max_height'] ?? 0,
                $data['price_per_sqm'] ?? 0,
                isset($data['allow_design_upload']) ? (int)$data['allow_design_upload'] : 1,
                isset($data['allow_online_editor']) ? (int)$data['allow_online_editor'] : 1,
                isset($data['allow_design_service']) ? (int)$data['allow_design_service'] : 1,
                $data['design_service_price'] ?? 150.00,
                $data['featured_image'] ?? null,
                !empty($data['gallery']) ? json_encode($data['gallery']) : null,
                !empty($data['is_featured']) ? 1 : 0,
                !empty($data['is_urgent']) ? 1 : 0,
                isset($data['status']) ? (int)$data['status'] : 1
            ]);

            $productId = $this->db->lastInsertId();

            // 3. Özellikler & Değerler (Kağıt, Selefon, Ebat vb.)
            if (!empty($data['attributes']) && is_array($data['attributes'])) {
                foreach ($data['attributes'] as $sort => $attr) {
                    $attrStmt = $this->db->prepare("INSERT INTO product_attributes (product_id, name, type, is_required, sort_order) VALUES (?, ?, ?, ?, ?)");
                    $attrStmt->execute([
                        $productId,
                        $attr['name'],
                        $attr['type'] ?? 'radio',
                        isset($attr['is_required']) ? (int)$attr['is_required'] : 1,
                        $sort + 1
                    ]);
                    $attrId = $this->db->lastInsertId();

                    if (!empty($attr['values']) && is_array($attr['values'])) {
                        foreach ($attr['values'] as $vSort => $val) {
                            $valStmt = $this->db->prepare("INSERT INTO product_attribute_values (attribute_id, title, price_extra, price_multiplier, is_default, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
                            $valStmt->execute([
                                $attrId,
                                $val['title'],
                                $val['price_extra'] ?? 0.00,
                                $val['price_multiplier'] ?? 1.00,
                                !empty($val['is_default']) ? 1 : 0,
                                $vSort + 1
                            ]);
                        }
                    }
                }
            }

            // 4. Adet Kademeleri
            if (!empty($data['quantity_tiers']) && is_array($data['quantity_tiers'])) {
                foreach ($data['quantity_tiers'] as $tSort => $tier) {
                    $tierStmt = $this->db->prepare("INSERT INTO product_quantity_tiers (product_id, quantity, multiplier, fixed_price, discount_percent, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
                    $tierStmt->execute([
                        $productId,
                        (int)$tier['quantity'],
                        $tier['multiplier'] ?? 1.0000,
                        $tier['fixed_price'] ?? null,
                        $tier['discount_percent'] ?? 0.00,
                        $tSort + 1
                    ]);
                }
            }

            // 5. Hazır Vektörel Şablonlar (SVG / Online Editor)
            if (!empty($data['templates']) && is_array($data['templates'])) {
                foreach ($data['templates'] as $tplSort => $tpl) {
                    $tplSlug = Helper::slugify($tpl['title']);
                    $tplStmt = $this->db->prepare("INSERT INTO design_templates (product_id, title, slug, thumbnail, category, canvas_width, canvas_height, default_svg, template_data, status, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $tplStmt->execute([
                        $productId,
                        $tpl['title'],
                        $tplSlug,
                        $tpl['thumbnail'] ?? null,
                        $tpl['category'] ?? 'Kurumsal',
                        $tpl['canvas_width'] ?? 850,
                        $tpl['canvas_height'] ?? 500,
                        $tpl['default_svg'] ?? '',
                        !empty($tpl['fields']) ? json_encode(['fields' => $tpl['fields']]) : null,
                        1,
                        $tplSort + 1
                    ]);
                }
            }

            $this->db->commit();
            return [
                'success'    => true,
                'product_id' => $productId,
                'slug'       => $slug,
                'url'        => SITE_URL . '/product.php?slug=' . $slug
            ];

        } catch (Exception $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'error'   => 'Ürün eklenirken hata: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Tüm Aktif Tedarikçileri Getirir
     */
    public function getSuppliers($onlyActive = true) {
        $sql = "SELECT * FROM suppliers";
        if ($onlyActive) {
            $sql .= " WHERE status = 1";
        }
        $sql .= " ORDER BY sort_order ASC, id ASC";
        try {
            $stmt = $this->db->query($sql);
            return $stmt ? $stmt->fetchAll() : [];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * ID ile Tedarikçi Getirir
     */
    public function getSupplierById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM suppliers WHERE id = ?");
            $stmt->execute([(int)$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Tedarikçi Fiyat Listesi Kalemlerini Getirir
     */
    public function getSupplierItems($supplierId = null, $category = null) {
        $sql = "SELECT i.*, s.name as supplier_name FROM supplier_price_items i LEFT JOIN suppliers s ON i.supplier_id = s.id WHERE i.status = 1";
        $params = [];
        if ($supplierId) {
            $sql .= " AND i.supplier_id = ?";
            $params[] = (int)$supplierId;
        }
        if ($category) {
            $sql .= " AND i.category = ?";
            $params[] = $category;
        }
        $sql .= " ORDER BY i.category ASC, i.id ASC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt ? $stmt->fetchAll() : [];
        } catch (Exception $e) {
            return [];
        }
    }
}

