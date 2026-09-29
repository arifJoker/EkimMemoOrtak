<?php
/**
 * Kullanıcı Girişi, Bayilik ve API Kimlik Doğrulama Sınıfı
 */
class Auth {
    private static $currentUser = null;

    public static function check() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public static function id() {
        return $_SESSION['user_id'] ?? null;
    }

    public static function user() {
        if (!self::check()) return null;
        if (self::$currentUser !== null) return self::$currentUser;

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        self::$currentUser = $stmt->fetch();
        return self::$currentUser;
    }

    public static function isAdmin() {
        $user = self::user();
        return $user && $user['role'] === 'admin';
    }

    public static function isEditor() {
        $user = self::user();
        return $user && in_array($user['role'], ['editor', 'product_manager']);
    }

    public static function hasAdminAccess() {
        $user = self::user();
        return $user && in_array($user['role'], ['admin', 'editor', 'product_manager']);
    }

    public static function canManageProducts() {
        $user = self::user();
        return $user && in_array($user['role'], ['admin', 'editor', 'product_manager']);
    }

    public static function canManageDesign() {
        $user = self::user();
        return $user && $user['role'] === 'admin';
    }

    public static function canManageSettings() {
        $user = self::user();
        return $user && $user['role'] === 'admin';
    }

    public static function isDealer() {
        $user = self::user();
        return $user && $user['role'] === 'dealer' && $user['dealer_status'] === 'approved';
    }

    public static function getDiscountRate() {
        if (self::isDealer()) {
            $user = self::user();
            return (float)($user['discount_rate'] ?? 0.00);
        }
        return 0.00;
    }

    public static function login($email, $password) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];
            self::$currentUser = $user;
            return ['success' => true, 'user' => $user];
        }

        return ['success' => false, 'error' => 'Geçersiz e-posta veya şifre.'];
    }

    public static function register($data) {
        $db = Database::getInstance()->getConnection();
        
        // E-posta mükerrer kontrolü
        $checkStmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$data['email']]);
        if ($checkStmt->fetch()) {
            return ['success' => false, 'error' => 'Bu e-posta adresi ile kayıtlı bir hesap zaten var.'];
        }

        $hashPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $role = $data['role'] ?? 'customer';
        $dealerStatus = ($role === 'dealer') ? 'pending' : 'approved';

        $stmt = $db->prepare("INSERT INTO users (full_name, email, password, phone, role, dealer_company, tax_number, tax_office, dealer_status, address, city, district) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $success = $stmt->execute([
            $data['full_name'],
            $data['email'],
            $hashPassword,
            $data['phone'] ?? '',
            $role,
            $data['dealer_company'] ?? null,
            $data['tax_number'] ?? null,
            $data['tax_office'] ?? null,
            $dealerStatus,
            $data['address'] ?? null,
            $data['city'] ?? null,
            $data['district'] ?? null
        ]);

        if ($success) {
            $newId = $db->lastInsertId();
            $_SESSION['user_id'] = $newId;
            $_SESSION['user_name'] = $data['full_name'];
            $_SESSION['user_role'] = $role;
            return ['success' => true, 'user_id' => $newId];
        }

        return ['success' => false, 'error' => 'Kayıt sırasında bir hata oluştu.'];
    }

    public static function logout() {
        unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_role']);
        session_regenerate_id(true);
        self::$currentUser = null;
    }

    public static function requireLogin($redirect = 'login.php') {
        if (!self::check()) {
            Helper::setFlash('warning', 'Bu işlemi yapabilmek için lütfen giriş yapınız.');
            header("Location: " . SITE_URL . "/login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }
    }

    public static function requireAdmin() {
        if (!self::hasAdminAccess()) {
            Helper::setFlash('danger', 'Bu sayfaya erişim yetkiniz bulunmuyor.');
            header("Location: " . SITE_URL . "/admin/login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }
    }

    public static function requireDesignPermission() {
        self::requireAdmin();
        if (!self::canManageDesign()) {
            Helper::setFlash('danger', 'Tasarım ve sistem ayarlarını değiştirme yetkiniz bulunmamaktadır. Yetkiniz yalnızca ürün yönetimi ve ürün yükleme ile sınırlandırılmıştır.');
            header("Location: " . SITE_URL . "/admin/products.php");
            exit;
        }
    }

    /**
     * AI Araçları & REST API için API Anahtarı Doğrulama
     */
    public static function verifyApiKey($apiKey, $apiSecret = null) {
        $db = Database::getInstance()->getConnection();
        if ($apiSecret) {
            $stmt = $db->prepare("SELECT * FROM api_keys WHERE api_key = ? AND api_secret = ? AND status = 'active'");
            $stmt->execute([$apiKey, $apiSecret]);
        } else {
            $stmt = $db->prepare("SELECT * FROM api_keys WHERE api_key = ? AND status = 'active'");
            $stmt->execute([$apiKey]);
        }
        $keyData = $stmt->fetch();
        if ($keyData) {
            // Son kullanım tarihini güncelle
            $update = $db->prepare("UPDATE api_keys SET last_used_at = NOW() WHERE id = ?");
            $update->execute([$keyData['id']]);
            return ['valid' => true, 'data' => $keyData];
        }
        return ['valid' => false, 'error' => 'Geçersiz veya pasif API Anahtarı.'];
    }
}
