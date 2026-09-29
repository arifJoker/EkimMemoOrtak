<?php
/**
 * Yardımcı Araçlar ve Güvenlik Sınıfı
 */
class Helper {
    private static $settingsCache = null;

    /**
     * Para Birimi Formatlayıcı (Örn: 1.250,50 ₺)
     */
    public static function formatPrice($amount, $currency = ' ₺') {
        $formatted = number_format((float)$amount, 2, ',', '.');
        return $formatted . $currency;
    }

    public static function formatCurrency($amount, $currency = ' ₺') {
        return self::formatPrice($amount, $currency);
    }

    /**
     * Türkçe Karakter Uyumlu SEO URL (Slug) Üretici
     */
    public static function slugify($text) {
        $find = ['Ç', 'ç', 'Ğ', 'ğ', 'ı', 'İ', 'Ö', 'ö', 'Ş', 'ş', 'Ü', 'ü'];
        $replace = ['c', 'c', 'g', 'g', 'i', 'i', 'o', 'o', 's', 's', 'u', 'u'];
        $text = str_replace($find, $replace, $text);
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        return empty($text) ? 'n-a' : $text;
    }

    /**
     * Güvenli Giriş Filtresi (XSS ve HTML temizleyici)
     */
    public static function clean($data) {
        if (is_array($data)) {
            return array_map([self::class, 'clean'], $data);
        }
        return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
    }

    /**
     * JSON Yanıt Döndürücü (API ve Ajax için)
     */
    public static function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Tüm Site Ayarlarını Getirir (Önbellekli)
     */
    public static function getSettings() {
        if (self::$settingsCache !== null) {
            return self::$settingsCache;
        }

        try {
            $db = Database::getInstance()->getConnection();
            if (!$db) return [];
            $stmt = $db->query("SELECT setting_key, setting_value FROM settings");
            $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            self::$settingsCache = $rows ?: [];
            return self::$settingsCache;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Tekil Ayar Değeri
     */
    public static function getSetting($key, $default = '') {
        $settings = self::getSettings();
        return $settings[$key] ?? $default;
    }

    /**
     * Ayar Güncelle / Ekle
     */
    public static function saveSetting($key, $value) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        $result = $stmt->execute([$key, $value]);
        self::$settingsCache = null; // Cache sıfırla
        return $result;
    }

    /**
     * Dosya Yükleme Yöneticisi (Büyük Matbaa Dosyaları, Resimler, ZIP)
     */
    public static function uploadFile($file, $targetSubDir = 'designs', $allowedExtensions = ['pdf', 'ai', 'psd', 'cdr', 'eps', 'tiff', 'zip', 'rar', 'jpg', 'jpeg', 'png', 'svg'], $maxSizeMb = 150) {
        if (!isset($file['error']) || is_array($file['error'])) {
            return ['success' => false, 'error' => 'Geçersiz dosya parametresi.'];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors = [
                UPLOAD_ERR_INI_SIZE   => 'Dosya boyutu sunucu limitini aşıyor.',
                UPLOAD_ERR_FORM_SIZE  => 'Dosya form limitini aşıyor.',
                UPLOAD_ERR_PARTIAL    => 'Dosya sadece kısmen yüklendi.',
                UPLOAD_ERR_NO_FILE    => 'Hiçbir dosya seçilmedi.',
                UPLOAD_ERR_NO_TMP_DIR => 'Geçici klasör bulunamadı.',
                UPLOAD_ERR_CANT_WRITE => 'Dosya diske yazılamadı.',
            ];
            return ['success' => false, 'error' => $errors[$file['error']] ?? 'Bilinmeyen yükleme hatası.'];
        }

        if ($file['size'] > $maxSizeMb * 1024 * 1024) {
            return ['success' => false, 'error' => "Dosya boyutu en fazla {$maxSizeMb} MB olabilir."];
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions)) {
            return ['success' => false, 'error' => 'İzin verilmeyen dosya formatı. Desteklenenler: ' . implode(', ', $allowedExtensions)];
        }

        $uploadDir = UPLOAD_PATH . '/' . trim($targetSubDir, '/');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
        $destination = $uploadDir . '/' . $fileName;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return [
                'success'       => true,
                'file_name'     => $fileName,
                'file_path'     => 'uploads/' . trim($targetSubDir, '/') . '/' . $fileName,
                'full_url'      => UPLOAD_URL . '/' . trim($targetSubDir, '/') . '/' . $fileName,
                'original_name' => $file['name'],
                'size'          => $file['size'],
                'extension'     => $extension
            ];
        }

        return ['success' => false, 'error' => 'Dosya yükleme hedefine taşınamadı. Klasör yazma izinlerini kontrol edin.'];
    }

    /**
     * Resim Yükleme ve Otomatik WebP Dönüştürme Yöneticisi
     */
    public static function uploadImageAsWebp($file, $targetSubDir = 'products', $quality = 85, $maxWidth = 1920) {
        if (!isset($file['error']) || is_array($file['error'])) {
            return ['success' => false, 'error' => 'Geçersiz dosya parametresi.'];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'Yükleme hatası kodu: ' . $file['error']];
        }

        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExts)) {
            return ['success' => false, 'error' => 'Yalnızca resim dosyaları (JPG, PNG, WEBP, GIF) yüklenebilir.'];
        }

        if ($file['size'] > 25 * 1024 * 1024) {
            return ['success' => false, 'error' => 'Görsel boyutu en fazla 25 MB olabilir.'];
        }

        $uploadDir = UPLOAD_PATH . '/' . trim($targetSubDir, '/');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $baseName = date('Ymd_His') . '_' . bin2hex(random_bytes(6));
        $tmpPath = $file['tmp_name'];

        // GD WebP Desteği Kontrolü
        if (function_exists('imagewebp')) {
            $srcImg = null;
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $srcImg = @imagecreatefromjpeg($tmpPath);
                    if ($srcImg && function_exists('exif_read_data')) {
                        $exif = @exif_read_data($tmpPath);
                        if (!empty($exif['Orientation'])) {
                            switch ($exif['Orientation']) {
                                case 3: $srcImg = imagerotate($srcImg, 180, 0); break;
                                case 6: $srcImg = imagerotate($srcImg, -90, 0); break;
                                case 8: $srcImg = imagerotate($srcImg, 90, 0); break;
                            }
                        }
                    }
                    break;
                case 'png':
                    $srcImg = @imagecreatefrompng($tmpPath);
                    break;
                case 'webp':
                    $srcImg = @imagecreatefromwebp($tmpPath);
                    break;
                case 'gif':
                    $srcImg = @imagecreatefromgif($tmpPath);
                    break;
                case 'bmp':
                    $srcImg = @imagecreatefrombmp($tmpPath);
                    break;
            }

            if ($srcImg) {
                $origWidth = imagesx($srcImg);
                $origHeight = imagesy($srcImg);

                // Maksimum genişlikten büyükse orantılı küçült
                if ($origWidth > $maxWidth) {
                    $newWidth = $maxWidth;
                    $newHeight = (int)(($origHeight / $origWidth) * $maxWidth);
                    $resized = imagecreatetruecolor($newWidth, $newHeight);
                    
                    // Şeffaflık koruması
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                    $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
                    imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);

                    imagecopyresampled($resized, $srcImg, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
                    imagedestroy($srcImg);
                    $srcImg = $resized;
                } else {
                    imagealphablending($srcImg, false);
                    imagesavealpha($srcImg, true);
                }

                $webpFileName = $baseName . '.webp';
                $destPath = $uploadDir . '/' . $webpFileName;

                $saved = imagewebp($srcImg, $destPath, $quality);
                imagedestroy($srcImg);

                if ($saved && file_exists($destPath)) {
                    return [
                        'success'       => true,
                        'file_name'     => $webpFileName,
                        'file_path'     => 'uploads/' . trim($targetSubDir, '/') . '/' . $webpFileName,
                        'full_url'      => UPLOAD_URL . '/' . trim($targetSubDir, '/') . '/' . $webpFileName,
                        'original_name' => $file['name'],
                        'size'          => filesize($destPath),
                        'extension'     => 'webp',
                        'is_webp'       => true
                    ];
                }
            }
        }

        // WebP GD başarısız olursa normal upload'a düş
        return self::uploadFile($file, $targetSubDir, $allowedExts, 25);
    }

    /**
     * Video Yükleme Yöneticisi (MP4, WEBM, MOV)
     */
    public static function uploadVideo($file, $targetSubDir = 'products/videos', $maxSizeMb = 100) {
        $allowedVideoExts = ['mp4', 'webm', 'mov', 'm4v', 'ogg'];
        return self::uploadFile($file, $targetSubDir, $allowedVideoExts, $maxSizeMb);
    }

    /**
     * Benzersiz Sipariş Numarası Üretici (Örn: BM-2609-8472)
     */
    public static function generateOrderNumber() {
        return 'BM-' . date('ym') . '-' . strtoupper(bin2hex(random_bytes(2)));
    }

    /**
     * CSRF Güvenlik Kontrolü
     */
    public static function checkCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function getCsrfInput() {
        $token = $_SESSION['csrf_token'] ?? '';
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    /**
     * Toast / Bildirim Mesajı Yönetimi
     */
    public static function setFlash($type, $message) {
        $_SESSION['flash_message'] = [
            'type'    => $type, // 'success', 'danger', 'warning', 'info'
            'message' => $message
        ];
    }

    public static function getFlash() {
        if (isset($_SESSION['flash_message'])) {
            $flash = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            return $flash;
        }
        return null;
    }

    /**
     * Sipariş Durumu Rozeti (HTML Badge)
     */
    public static function getOrderStatusBadge($status) {
        $map = [
            'pending_payment'   => ['label' => 'Ödeme Bekleniyor', 'class' => 'bg-warning text-dark'],
            'payment_received'  => ['label' => 'Ödeme Alındı', 'class' => 'bg-info text-white'],
            'design_approval'   => ['label' => 'Tasarım Onayında', 'class' => 'bg-primary text-white'],
            'in_production'     => ['label' => 'Baskıda / Üretimde', 'class' => 'bg-purple text-white', 'style' => 'background-color:#8b5cf6;color:#fff'],
            'packaged'          => ['label' => 'Paketlendi', 'class' => 'bg-secondary text-white'],
            'shipped'           => ['label' => 'Kargoya Verildi', 'class' => 'bg-indigo text-white', 'style' => 'background-color:#4f46e5;color:#fff'],
            'delivered'         => ['label' => 'Teslim Edildi', 'class' => 'bg-success text-white'],
            'cancelled'         => ['label' => 'İptal Edildi', 'class' => 'bg-danger text-white']
        ];

        $badge = $map[$status] ?? ['label' => $status, 'class' => 'bg-secondary text-white'];
        $styleAttr = isset($badge['style']) ? ' style="' . $badge['style'] . '"' : '';
        return '<span class="badge ' . $badge['class'] . '"' . $styleAttr . '>' . $badge['label'] . '</span>';
    }

    /**
     * Ödeme Durumu Rozeti
     */
    public static function getPaymentStatusBadge($status) {
        $map = [
            'pending'   => ['label' => 'Bekliyor', 'class' => 'bg-warning text-dark'],
            'paid'      => ['label' => 'Ödendi', 'class' => 'bg-success text-white'],
            'failed'    => ['label' => 'Başarısız', 'class' => 'bg-danger text-white'],
            'refunded'  => ['label' => 'İade Edildi', 'class' => 'bg-dark text-white']
        ];

        $badge = $map[$status] ?? ['label' => $status, 'class' => 'bg-secondary text-white'];
        return '<span class="badge ' . $badge['class'] . '">' . $badge['label'] . '</span>';
    }

    /**
     * TCMB Canlı Dolar ($ USD) Kuru Getirir
     */
    public static function getUsdRate($forceRefresh = false) {
        $currentRate = (float)self::getSetting('usd_try_rate', 38.50);
        $lastUpdate = self::getSetting('usd_rate_updated_at', '');

        // Günde bir kez veya zorunluysa TCMB XML'den çek
        if ($forceRefresh || empty($lastUpdate) || date('Y-m-d', strtotime($lastUpdate)) !== date('Y-m-d')) {
            try {
                $ctx = stream_context_create(['http' => ['timeout' => 3]]);
                $xmlContent = @file_get_contents('https://www.tcmb.gov.tr/kurlar/today.xml', false, $ctx);
                if ($xmlContent) {
                    $xml = @simplexml_load_string($xmlContent);
                    if ($xml) {
                        foreach ($xml->Currency as $c) {
                            if ((string)$c['CurrencyCode'] === 'USD') {
                                $rate = (float)str_replace(',', '.', (string)$c->BanknoteSelling ?: (string)$c->ForexSelling);
                                if ($rate > 10) {
                                    $currentRate = $rate;
                                    self::saveSetting('usd_try_rate', number_format($rate, 4, '.', ''));
                                    self::saveSetting('usd_rate_updated_at', date('Y-m-d H:i:s'));
                                    break;
                                }
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                // Fallback to saved rate
            }
        }

        return $currentRate ?: 38.50;
    }

    /**
     * 70x100 Tabaka Yerleşimi & Fire Hesaplama Motoru
     * $itemW, $itemH (cm cinsinden ürün ebadı)
     * $sheetW, $sheetH (cm cinsinden tabaka ebadı, varsayılan 70x100)
     */
    public static function calculateSheetPlacement($itemW, $itemH, $sheetW = 70, $sheetH = 100, $bleed = 0.4) {
        $itemW = max(1.0, (float)$itemW) + $bleed; // Kesim payı
        $itemH = max(1.0, (float)$itemH) + $bleed;

        // Düz Yerleşim
        $fitNormal = floor($sheetW / $itemW) * floor($sheetH / $itemH);
        
        // Döndürülmüş (90 Derece) Yerleşim
        $fitRotated = floor($sheetW / $itemH) * floor($sheetH / $itemW);

        $itemsPerSheet = (int)max(1, max($fitNormal, $fitRotated));

        // Standart kartvizit (8.4 x 5.2 cm) için 96 - 100 adet çıkar
        $netAreaPerItem = ($itemW - $bleed) * ($itemH - $bleed); // cm2
        $totalNetArea = $itemsPerSheet * $netAreaPerItem;
        $sheetArea = $sheetW * $sheetH; // 7000 cm2

        $wastePercent = max(0, round((($sheetArea - $totalNetArea) / $sheetArea) * 100, 1));

        return [
            'items_per_sheet' => $itemsPerSheet,
            'waste_percent'   => $wastePercent,
            'is_rotated'      => $fitRotated > $fitNormal,
            'cols'            => $fitRotated > $fitNormal ? floor($sheetW / $itemH) : floor($sheetW / $itemW),
            'rows'            => $fitRotated > $fitNormal ? floor($sheetH / $itemW) : floor($sheetH / $itemH)
        ];
    }
}

