<?php
require_once __DIR__ . '/../config/config.php';
Auth::requireDesignPermission();

$db = Database::getInstance()->getConnection();
$action = $_GET['action'] ?? 'list';

// Yeni API Anahtarı Üretici
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_api_key'])) {
    $name = trim($_POST['key_name'] ?? 'AI Entegrasyon Anahtarı');
    $apiKey = 'baski_key_' . bin2hex(random_bytes(16));
    $apiSecret = 'baski_sec_' . bin2hex(random_bytes(24));
    $perms = json_encode(["products:all", "categories:all", "templates:all", "orders:read"]);

    $stmt = $db->prepare("INSERT INTO api_keys (user_id, name, api_key, api_secret, permissions) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([Auth::id(), $name, $apiKey, $apiSecret, $perms]);

    Helper::setFlash('success', 'Yeni AI API Anahtarı başarıyla üretildi.');
    header("Location: " . SITE_URL . "/admin/ai_api.php");
    exit;
}

if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $db->prepare("DELETE FROM api_keys WHERE id = ?");
    $stmt->execute([$id]);
    Helper::setFlash('success', 'API Anahtarı silindi.');
    header("Location: " . SITE_URL . "/admin/ai_api.php");
    exit;
}

$apiKeys = $db->query("SELECT * FROM api_keys ORDER BY id DESC")->fetchAll();
$primaryKey = $apiKeys[0]['api_key'] ?? 'API_KEY_OLUSTURUNUZ';

$pageTitle = 'AI & REST API Entegrasyon Merkezi';
require_once __DIR__ . '/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-robot text-warning me-2"></i>AI & REST API Yönetim Merkezi</h4>
        <p class="text-muted small mb-0">Yapay zeka araçlarının (ChatGPT, Claude, Gemini, Cursor) sitenize otomatik ürün, varyant ve şablon eklemesini sağlayın.</p>
    </div>
    <form action="<?= SITE_URL ?>/admin/ai_api.php" method="POST" class="d-inline">
        <input type="hidden" name="generate_api_key" value="1">
        <button type="submit" class="btn btn-sm btn-primary">
            <i class="bi bi-key-fill me-1"></i> Yeni API Anahtarı Üret
        </button>
    </form>
</div>

<!-- AI Ajanı İçin Hazır Prompt Kopyalama Kartı -->
<div class="apple-card p-4 mb-4 border-primary border-opacity-50" style="background: #fdfdfd;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <span class="badge bg-primary text-white px-3 py-1 rounded-pill mb-1">🤖 AI Ajanına Verilecek Doğrudan Talimat</span>
            <h5 class="fw-bold mb-0">ChatGPT / Claude / Gemini İçin Hazır Sistem Promptu</h5>
        </div>
        <button class="btn btn-sm btn-apple-pink" onclick="copyPrompt()">
            <i class="bi bi-clipboard-check me-1"></i> Promptu Kopyala
        </button>
    </div>
    
    <div class="position-relative">
        <textarea id="aiPromptBox" class="form-control font-monospace small bg-light p-3" rows="11" readonly>
Sen bir E-Ticaret ve Matbaa Uzmanısın. Benim online matbaa siteme ürün, varyant, adet kademeleri ve hazır vektörel SVG şablonları ekleme yetkisine sahipsin.

İşte sitenin REST API bağlantı bilgileri:
- API Base URL: <?= SITE_URL ?>/api/v1
- Keşif & Şema Endpoint'i (GET): <?= SITE_URL ?>/api/v1/index.php
- Ürün Ekleme Endpoint'i (POST): <?= SITE_URL ?>/api/v1/products.php
- HTTP Headers:
  "Content-Type: application/json"
  "X-API-Key: <?= $primaryKey ?>"

Görevin:
Sana söylediğim matbaa ürününü (örn: Kartvizit, Broşür, Kaşe, Bayrak, Cepli Dosya, Etiket) tüm matbaa varyantlarıyla (Kağıt türleri, selefon/laminasyon, köşe kesimleri, baskı yönü vb.), adet kademeleri (100, 250, 500, 1000...) ve müşterinin canlı düzenleyebileceği hazır SVG kartvizit şablonlarıyla birlikte POST isteği olarak <?= SITE_URL ?>/api/v1/products.php adresine tek seferde göndermektir.

Şimdi hazırsan senden eklemeni istediğim ilk ürünü söyleyeceğim!
        </textarea>
    </div>
    <small class="text-muted mt-2 d-block">
        <i class="bi bi-info-circle me-1"></i> Bu metni kopyalayıp herhangi bir AI modeline verdiğinizde, AI sitenizin şemasını anlar ve doğrudan API üzerinden ürünlerinizi hazır şablonlarıyla beraber oluşturur.
    </small>
</div>

<!-- Kayıtlı API Anahtarları -->
<div class="apple-card p-4 mb-4">
    <h6 class="fw-bold mb-3 border-bottom pb-2">Aktif API Anahtarları</h6>
    <div class="table-responsive">
        <table class="table table-hover align-middle small">
            <thead class="table-light">
                <tr>
                    <th>Anahtar Adı</th>
                    <th>API Key (Header: X-API-Key)</th>
                    <th>İzinler</th>
                    <th>Son Kullanım</th>
                    <th>Durum</th>
                    <th class="text-end">İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($apiKeys as $k): ?>
                    <tr>
                        <td class="fw-bold"><?= htmlspecialchars($k['name']) ?></td>
                        <td><code><?= htmlspecialchars($k['api_key']) ?></code></td>
                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($k['permissions']) ?></span></td>
                        <td><?= $k['last_used_at'] ? date('d.m.Y H:i', strtotime($k['last_used_at'])) : '<span class="text-muted">Henüz kullanılmadı</span>' ?></td>
                        <td><?= $k['status'] === 'active' ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Pasif</span>' ?></td>
                        <td class="text-end">
                            <a href="<?= SITE_URL ?>/admin/ai_api.php?action=delete&id=<?= $k['id'] ?>" class="btn btn-sm btn-outline-danger py-0" onclick="return confirm('Bu API anahtarını silmek istediğinize emin misiniz?');">
                                Sil
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- API Endpoint Dokümantasyon Tablosu -->
<div class="apple-card p-4">
    <h6 class="fw-bold mb-3 border-bottom pb-2">REST API v1 Endpoint Referansı</h6>
    <div class="table-responsive small">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 100px;">Metod</th>
                    <th>Endpoint</th>
                    <th>Açıklama</th>
                    <th>Örnek cURL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="badge bg-success">GET</span></td>
                    <td><code>/api/v1/index.php</code></td>
                    <td>Tüm API yeteneklerini ve JSON şemasını otomatik keşif olarak döndürür.</td>
                    <td><code>curl -H "X-API-Key: KEY" <?= SITE_URL ?>/api/v1/index.php</code></td>
                </tr>
                <tr>
                    <td><span class="badge bg-primary">POST</span></td>
                    <td><code>/api/v1/products.php</code></td>
                    <td>Ürün + Varyantlar + Fiyat Matrisi + Vektörel SVG Şablonlarını tek istekte oluşturur.</td>
                    <td><code>curl -X POST -H "X-API-Key: KEY" -d '{...}' <?= SITE_URL ?>/api/v1/products.php</code></td>
                </tr>
                <tr>
                    <td><span class="badge bg-success">GET</span></td>
                    <td><code>/api/v1/orders.php</code></td>
                    <td>Siparişleri, müşterilerin yüklediği matbaa dosyalarını ve SVG tasarımlarını çeker.</td>
                    <td><code>curl -H "X-API-Key: KEY" <?= SITE_URL ?>/api/v1/orders.php</code></td>
                </tr>
                <tr>
                    <td><span class="badge bg-primary">POST</span></td>
                    <td><code>/api/v1/upload.php</code></td>
                    <td>Mockup, ürün görseli veya dosya yükler (Base64 veya Multipart).</td>
                    <td><code>curl -X POST -H "X-API-Key: KEY" -F "file=@img.jpg" <?= SITE_URL ?>/api/v1/upload.php</code></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
function copyPrompt() {
    const text = document.getElementById('aiPromptBox').value;
    navigator.clipboard.writeText(text).then(() => {
        alert('AI Promptu panoya kopyalandı! Şimdi ChatGPT, Claude veya Gemini\'ye yapıştırabilirsiniz.');
    });
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
