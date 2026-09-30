<?php
require_once __DIR__ . '/config/config.php';

$pageTitle = 'Gizlilik ve Güvenlik Politikası | TamBaskı';
$pageDesc = 'TamBaskı 256-Bit SSL güvenliği, PayTR 3D Secure ödeme altyapısı ve KVKK aydınlatma metni.';

$companyName = Helper::getSetting('company_name', 'TamBaskı Matbaa ve Dijital Baskı Teknolojileri San. Tic. Ltd. Şti.');
$email = Helper::getSetting('site_email', 'info@tambaski.com.tr');

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    
    <div class="text-center max-w-700 mx-auto mb-5">
        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1.5 fw-semibold mb-2">Güvenli Alışveriş</span>
        <h1 class="fw-bold tracking-tight mb-3">Gizlilik ve Güvenlik Politikası</h1>
        <p class="text-muted">
            Kişisel verilerinizin korunması, 256-Bit SSL şifreleme ve güvenli ödeme standartlarımız hakkında detaylı bilgi.
        </p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white small text-muted leading-relaxed">
                
                <h5 class="fw-bold text-dark mb-3">
                    <i class="bi bi-shield-lock-fill text-success me-2"></i>1. Kredi Kartı ve Ödeme Güvenliği (256-Bit SSL &amp; 3D Secure)
                </h5>
                <p>
                    Sitemizde kredi kartı ile yapılan tüm işlemler <strong>256-Bit SSL (Secure Socket Layer)</strong> sertifikası ile uluslararası şifreleme standartlarında korunmaktadır. Ödeme aşamasında kart bilgileriniz doğrudan <strong>PayTR</strong> ve BDDK lisanslı banka sistemlerine iletilir; sitemiz sunucularında veya veritabanında kesinlikle kredi kartı numarası, son kullanma tarihi veya CVV güvenlik kodu <strong>kaydedilmez ve saklanmaz</strong>.
                </p>
                <p>
                    Tüm ödemeler <strong>3D Secure (SMS Onay Kodu)</strong> katmanı ile gerçekleştirilerek kart sahibinin onayı olmadan hiçbir harcama yapılmasına izin verilmez.
                </p>

                <hr class="my-4">

                <h5 class="fw-bold text-dark mb-3">
                    <i class="bi bi-person-check-fill text-primary me-2"></i>2. 6698 Sayılı KVKK Kapsamında Kişisel Verilerin Korunması
                </h5>
                <p>
                    <?= htmlspecialchars($companyName) ?> ("TamBaskı") olarak, 6698 sayılı Kişisel Verilerin Korunması Kanunu ("KVKK") uyarınca veri sorumlusu sıfatıyla; ad, soyad, telefon, e-posta, teslimat ve fatura adresi gibi kişisel bilgilerinizi sadece siparişin hazırlanması, faturalandırılması ve kargo teslimatının sağlanması amacıyla işlemekteyiz.
                </p>
                <p>
                    Kişisel verileriniz, yasal zorunluluklar ve lojistik/kargo iş ortaklarımız haricinde hiçbir üçüncü taraf kişi veya kurumla ticari amaçla paylaşılmaz ve satılmaz.
                </p>

                <hr class="my-4">

                <h5 class="fw-bold text-dark mb-3">
                    <i class="bi bi-file-earmark-lock2-fill text-primary me-2"></i>3. Baskı Dosyalarının ve Kurumsal Tasarımların Gizliliği
                </h5>
                <p class="mb-0">
                    Sipariş verirken yüklediğiniz firma logoları, kurumsal kimlik dosyaları (PDF, AI, PSD vb.) ve baskı içerikleri yalnızca siparişinizin üretimi amacıyla kullanılır. Şirketinize ait ticari sırlar, kurumsal veriler veya tasarımlar kesinlikle gizli tutulur ve izniniz olmadan başka hiçbir baskı veya tanıtımda kullanılamaz.
                </p>

            </div>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
