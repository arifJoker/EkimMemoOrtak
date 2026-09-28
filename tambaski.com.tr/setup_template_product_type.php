<?php
require_once __DIR__ . '/config/config.php';
header('Content-Type: application/json; charset=utf-8');

try {
    // 1. Add product_type column if not exists
    $cols = $db->query("SHOW COLUMNS FROM design_templates LIKE 'product_type'")->fetchAll();
    if (empty($cols)) {
        $db->exec("ALTER TABLE `design_templates` ADD COLUMN `product_type` VARCHAR(50) DEFAULT 'kartvizit' AFTER `product_id`");
    }

    // 2. Add orientation column if not exists
    $orientCols = $db->query("SHOW COLUMNS FROM design_templates LIKE 'orientation'")->fetchAll();
    if (empty($orientCols)) {
        $db->exec("ALTER TABLE `design_templates` ADD COLUMN `orientation` VARCHAR(20) DEFAULT 'horizontal' AFTER `product_type`");
    }

    // 3. Update existing 75 business card templates
    $db->exec("UPDATE `design_templates` SET `product_type` = 'kartvizit', `orientation` = 'horizontal' WHERE `product_type` IS NULL OR `product_type` = '' OR `product_type` = 'kartvizit'");

    // 4. Also generate 15 specialized Vertical Brochure (El İlanı / Broşür - 500x700 Dikey A5) templates (1 for each of the 15 industries)!
    $industries = $db->query("SELECT * FROM industries WHERE status = 1 ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);

    $brochureTemplatesCount = 0;
    foreach ($industries as $ind) {
        $check = $db->prepare("SELECT COUNT(*) FROM design_templates WHERE industry_slug = ? AND product_type = 'brosur'");
        $check->execute([$ind['slug']]);
        if ($check->fetchColumn() == 0) {
            $title = $ind['name'] . ' – A5 Dikey Tanıtım & Fırsat Broşürü';
            $slug = Helper::slugify($title);
            $cat = 'Dikey Broşür';
            $svg = '<svg viewBox="0 0 500 700" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="br_grad_' . $ind['slug'] . '" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#0f172a" />
      <stop offset="100%" stop-color="#1e293b" />
    </linearGradient>
    <linearGradient id="br_accent_' . $ind['slug'] . '" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="#0071e3" />
      <stop offset="100%" stop-color="#00c6ff" />
    </linearGradient>
  </defs>
  <rect width="500" height="700" fill="url(#br_grad_' . $ind['slug'] . ')" rx="8" />
  <path d="M 0 0 L 500 0 L 500 160 L 0 220 Z" fill="url(#br_accent_' . $ind['slug'] . ')" opacity="0.9" />
  <circle cx="430" cy="80" r="90" fill="#ffffff" opacity="0.08" />
  <text id="companyName" x="40" y="80" fill="#ffffff" font-size="28" font-family="system-ui, -apple-system, sans-serif" font-weight="bold" letter-spacing="1">ŞİRKETİNİZ &amp; MARKA</text>
  <text id="tagline" x="40" y="115" fill="#ffffff" font-size="14" font-family="system-ui, -apple-system, sans-serif" opacity="0.95">' . htmlspecialchars($ind['name']) . ' Hizmetleri</text>
  
  <!-- Ana Kampanya / Başlık Alanı -->
  <rect x="35" y="250" width="430" height="180" rx="12" fill="#ffffff" opacity="0.06" stroke="#ffffff" stroke-width="1" stroke-opacity="0.15" />
  <text id="campaignTitle" x="250" y="300" fill="#00c6ff" font-size="22" font-family="system-ui, -apple-system, sans-serif" font-weight="bold" text-anchor="middle">ÖZEL SEZON KAMPANYASI</text>
  <text id="campaignDesc1" x="250" y="340" fill="#e2e8f0" font-size="14" font-family="system-ui, -apple-system, sans-serif" text-anchor="middle">• Profesyonel ve Hızlı Çözümler</text>
  <text id="campaignDesc2" x="250" y="370" fill="#e2e8f0" font-size="14" font-family="system-ui, -apple-system, sans-serif" text-anchor="middle">• %100 Müşteri Memnuniyeti Garantisi</text>
  <text id="campaignDesc3" x="250" y="400" fill="#e2e8f0" font-size="14" font-family="system-ui, -apple-system, sans-serif" text-anchor="middle">• Ücretsiz Keşif &amp; Danışmanlık</text>

  <!-- İletişim / Alt Bilgi Alanı -->
  <rect x="35" y="460" width="430" height="190" rx="12" fill="#000000" opacity="0.3" />
  <text id="callToAction" x="250" y="500" fill="#f59e0b" font-size="16" font-family="system-ui, -apple-system, sans-serif" font-weight="bold" text-anchor="middle">HEMEN İLETİŞİME GEÇİN</text>
  <text id="phone" x="250" y="540" fill="#ffffff" font-size="20" font-family="system-ui, -apple-system, sans-serif" font-weight="bold" text-anchor="middle">0555 123 45 67</text>
  <text id="address" x="250" y="580" fill="#94a3b8" font-size="12" font-family="system-ui, -apple-system, sans-serif" text-anchor="middle">Merkez Mah. Atatürk Cad. No:123 / İSTANBUL</text>
  <text id="website" x="250" y="615" fill="#38bdf8" font-size="13" font-family="system-ui, -apple-system, sans-serif" text-anchor="middle">www.sirketiniz.com</text>
</svg>';
            $fields = json_encode([
                'fields' => [
                    ['id' => 'companyName', 'label' => 'Firma / Şirket Adı', 'default' => 'ŞİRKETİNİZ & MARKA'],
                    ['id' => 'tagline', 'label' => 'Slogan / Alt Başlık', 'default' => $ind['name'] . ' Hizmetleri'],
                    ['id' => 'campaignTitle', 'label' => 'Kampanya Başlığı', 'default' => 'ÖZEL SEZON KAMPANYASI'],
                    ['id' => 'campaignDesc1', 'label' => 'Hizmet Maddesi 1', 'default' => '• Profesyonel ve Hızlı Çözümler'],
                    ['id' => 'campaignDesc2', 'label' => 'Hizmet Maddesi 2', 'default' => '• %100 Müşteri Memnuniyeti Garantisi'],
                    ['id' => 'campaignDesc3', 'label' => 'Hizmet Maddesi 3', 'default' => '• Ücretsiz Keşif & Danışmanlık'],
                    ['id' => 'callToAction', 'label' => 'Aksiyon Çağrısı', 'default' => 'HEMEN İLETİŞİME GEÇİN'],
                    ['id' => 'phone', 'label' => 'Telefon Numarası', 'default' => '0555 123 45 67'],
                    ['id' => 'address', 'label' => 'Adres Bilgisi', 'default' => 'Merkez Mah. Atatürk Cad. No:123 / İSTANBUL'],
                    ['id' => 'website', 'label' => 'Web Sitesi', 'default' => 'www.sirketiniz.com']
                ]
            ], JSON_UNESCAPED_UNICODE);

            $stmt = $db->prepare("INSERT INTO design_templates (product_id, product_type, orientation, title, slug, category, industry_slug, industry_id, canvas_width, canvas_height, default_svg, template_data, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
            $stmt->execute([
                3, 'brosur', 'vertical', $title, $slug, $cat, $ind['slug'], $ind['id'], 500, 700, $svg, $fields
            ]);
            $brochureTemplatesCount++;
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Template system successfully upgraded with product types and vertical brochure templates.',
        'brochure_templates_added' => $brochureTemplatesCount,
        'total_templates' => $db->query("SELECT COUNT(*) FROM design_templates")->fetchColumn()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
