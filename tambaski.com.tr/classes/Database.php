<?php
/**
 * Veritabanı Bağlantı Sınıfı (PDO Singleton Mimarisi)
 */
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Veritabanı henüz oluşturulmamış veya bağlanılamıyorsa kullanıcıya bilgilendirme
            if (php_sapi_name() === 'cli' || strpos($_SERVER['REQUEST_URI'] ?? '', 'install') !== false) {
                $this->pdo = null;
            } else {
                die('<div style="font-family:system-ui;max-width:600px;margin:80px auto;padding:30px;border-radius:12px;background:#fef2f2;border:1px solid #f87171;color:#991b1b;box-shadow:0 10px 25px rgba(0,0,0,0.05);">'
                    . '<h2 style="margin-top:0;">Veritabanı Bağlantı Hatası</h2>'
                    . '<p>Veritabanı bağlantısı kurulamadı. Lütfen <code>config/config.php</code> dosyasındaki veritabanı ayarlarını ve <code>database.sql</code> dosyasının MySQL içine aktarıldığını kontrol edin.</p>'
                    . '<p><small>Hata Detayı: ' . htmlspecialchars($e->getMessage()) . '</small></p>'
                    . '</div>');
            }
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    // Clone & Wakeup koruması
    private function __clone() {}
    public function __wakeup() {}
}
