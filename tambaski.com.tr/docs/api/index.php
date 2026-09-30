<?php
/**
 * TAMBASKI.COM.TR - API Dokümantasyon Portalı & Geliştirici Merkezi
 */
require_once __DIR__ . '/../../config/config.php';

$sampleApiKey = 'tb_live_memo_7f9b2c4e1a8d5063';
$specUrl = SITE_URL . '/docs/api/openapi.php';
$rulesMdUrl = SITE_URL . '/docs/api/rules.php?format=markdown';
$rulesJsonUrl = SITE_URL . '/docs/api/rules.php?format=json';
$downloadUrl = SITE_URL . '/docs/api/rules.php?format=download';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TamBaskı Developer Portal &amp; REST API Dokümantasyonu</title>
    <link rel="icon" type="image/svg+xml" href="<?= SITE_URL ?>/assets/img/favicon.svg">
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --tb-orange: #f15a24;
            --tb-dark: #0f172a;
            --tb-card-bg: #1e293b;
            --tb-border: #334155;
            --tb-text: #f8fafc;
            --tb-muted: #94a3b8;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--tb-dark);
            color: var(--tb-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Hero Header */
        .portal-header {
            background: linear-gradient(135deg, #090d16 0%, #162032 100%);
            border-bottom: 1px solid var(--tb-border);
            padding: 24px 32px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        }

        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        .brand-area {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .brand-logo {
            height: 38px;
            width: auto;
        }

        .badge-live {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
            font-size: 12px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-live::before {
            content: '';
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 8px #10b981;
        }

        .actions-area {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
        }

        .btn-primary-tb {
            background: var(--tb-orange);
            color: #ffffff;
            box-shadow: 0 2px 10px rgba(241, 90, 36, 0.3);
        }
        .btn-primary-tb:hover {
            background: #d94b1a;
            transform: translateY(-1px);
        }

        .btn-secondary-tb {
            background: var(--tb-card-bg);
            color: var(--tb-text);
            border: 1px solid var(--tb-border);
        }
        .btn-secondary-tb:hover {
            background: #334155;
            border-color: #475569;
        }

        /* Notice Banner */
        .sync-notice {
            background: rgba(241, 90, 36, 0.08);
            border-left: 4px solid var(--tb-orange);
            padding: 14px 24px;
            max-width: 1400px;
            margin: 16px auto 0 auto;
            width: 100%;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 13px;
        }

        .sync-notice strong {
            color: var(--tb-orange);
        }

        .key-badge {
            background: #090d16;
            padding: 4px 10px;
            border-radius: 6px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: #38bdf8;
            border: 1px solid #1e293b;
            user-select: all;
        }

        /* Scalar Container */
        #api-reference-container {
            flex: 1;
            width: 100%;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="portal-header">
        <div class="header-container">
            <div class="brand-area">
                <a href="<?= SITE_URL ?>" style="display: flex; align-items: center; text-decoration: none;">
                    <img src="<?= SITE_URL ?>/assets/img/logo.svg" alt="TamBaskı" class="brand-logo" onerror="this.src='https://dummyimage.com/140x38/f15a24/fff&text=TAMBASKI'">
                </a>
                <span class="badge-live">REST API v1 CANLI</span>
            </div>

            <div class="actions-area">
                <!-- Tek Tıkla MEMO_RULES.md İndir -->
                <a href="<?= $downloadUrl ?>" class="btn-action btn-primary-tb" title="Antigravity için kural dosyasını indir">
                    <i class="bi bi-cloud-arrow-down-fill"></i>
                    <span>Kural Dosyasını İndir (MEMO_RULES.md)</span>
                </a>

                <!-- Canlı Kural Görüntüle -->
                <a href="<?= $rulesMdUrl ?>" target="_blank" class="btn-action btn-secondary-tb" title="Canlı Markdown formatı">
                    <i class="bi bi-markdown-fill"></i>
                    <span>Canlı Markdown</span>
                </a>

                <!-- Canlı JSON Görüntüle -->
                <a href="<?= $rulesJsonUrl ?>" target="_blank" class="btn-action btn-secondary-tb" title="Canlı JSON formatı">
                    <i class="bi bi-filetype-json"></i>
                    <span>rules.json</span>
                </a>

                <!-- API Anahtarı Kopyala -->
                <button type="button" class="btn-action btn-secondary-tb" onclick="copyApiKey()" id="copyKeyBtn">
                    <i class="bi bi-key-fill text-warning"></i>
                    <span>API Key Kopyala</span>
                </button>
            </div>
        </div>

        <div class="sync-notice">
            <div>
                <i class="bi bi-info-circle-fill text-warning me-1"></i>
                <strong>Memo ve Antigravity Entegrasyonu:</strong> Bu doküman canlı veritabanı ile tam senkronizedir. Arif yeni kategori eklediğinde liste otomatik yenilenir.
            </div>
            <div>
                Varsayılan API Key: <span class="key-badge" id="apiKeyText"><?= $sampleApiKey ?></span>
            </div>
        </div>
    </header>

    <!-- Scalar Interactive API Reference -->
    <div id="api-reference-container">
        <script
            id="api-reference"
            type="application/json"
            data-url="<?= $specUrl ?>"
            data-configuration='{
                "theme": "purple",
                "layout": "modern",
                "defaultHttpClient": {
                    "targetKey": "shell",
                    "clientKey": "curl"
                },
                "darkMode": true,
                "hideDownloadButton": false
            }'>
        </script>
        <script src="https://cdn.jsdelivr.net/npm/@scalar/api-reference"></script>
    </div>

    <script>
        function copyApiKey() {
            const key = document.getElementById('apiKeyText').innerText.trim();
            navigator.clipboard.writeText(key).then(() => {
                const btn = document.getElementById('copyKeyBtn');
                const orig = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i> <span>Kopyalandı!</span>';
                setTimeout(() => { btn.innerHTML = orig; }, 2000);
            });
        }
    </script>
</body>
</html>
