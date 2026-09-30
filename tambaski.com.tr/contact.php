<?php
require_once __DIR__ . '/config/config.php';

$pageTitle = 'İletişim & Ulaşım Bilgileri | TamBaskı';
$pageDesc = 'TamBaskı müşteri hizmetleri, açık fabrika adresi, telefon numarası ve WhatsApp destek hattı.';

$phone = Helper::getSetting('site_phone', '0850 308 00 00');
$whatsapp = Helper::getSetting('site_whatsapp', '905550000000');
$email = Helper::getSetting('site_email', 'info@tambaski.com.tr');
$address = Helper::getSetting('site_address', 'Maltepe Mah. Gümüşsuyu Cad. Topkapı Matbaacılar Sitesi No: 14 Zeytinburnu / İstanbul');
$companyName = Helper::getSetting('company_name', 'TamBaskı Matbaa ve Dijital Baskı Teknolojileri San. Tic. Ltd. Şti.');
$taxOffice = Helper::getSetting('tax_office', 'Zeytinburnu V.D. / 8150923412');

$sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = Helper::clean($_POST['name'] ?? '');
    $phoneInp = Helper::clean($_POST['phone'] ?? '');
    $emailInp = Helper::clean($_POST['email'] ?? '');
    $subject = Helper::clean($_POST['subject'] ?? '');
    $message = Helper::clean($_POST['message'] ?? '');

    if (!empty($name) && !empty($phoneInp) && !empty($message)) {
        // Talebi kaydet veya e-posta gönder
        $sent = true;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    
    <!-- Başlık & Breadcrumb -->
    <div class="text-center max-w-700 mx-auto mb-5">
        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1.5 fw-semibold mb-2">Bize Ulaşın</span>
        <h1 class="fw-bold tracking-tight mb-3">İletişim &amp; Fabrika Adresimiz</h1>
        <p class="text-muted">
            Tasarım, sipariş, kurumsal teklifler veya kargo süreçleriniz hakkında bilgi almak için bizimle dilediğiniz zaman iletişime geçebilirsiniz.
        </p>
    </div>

    <?php if ($sent): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-4 p-4 mb-4 shadow-sm" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill text-success fs-3 me-3"></i>
            <div>
                <h5 class="fw-bold mb-1">Mesajınız Başarıyla Alındı!</h5>
                <p class="mb-0 small text-muted">Müşteri temsilcilerimiz mesai saatleri içerisinde en kısa sürede sizinle iletişime geçecektir.</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
    </div>
    <?php endif; ?>

    <div class="row g-4">
        
        <!-- Sol Kolon: Kurumsal & İletişim Bilgileri -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 h-100 bg-white">
                <h5 class="fw-bold text-dark mb-4 border-bottom pb-3">
                    <i class="bi bi-building-check text-primary me-2"></i>Kurumsal Bilgiler
                </h5>

                <div class="d-flex align-items-start mb-4">
                    <div class="rounded-circle bg-primary-subtle text-primary p-3 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-geo-alt-fill fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark">Açık Adres</div>
                        <div class="text-muted small mt-1"><?= htmlspecialchars($address) ?></div>
                    </div>
                </div>

                <div class="d-flex align-items-start mb-4">
                    <div class="rounded-circle bg-success-subtle text-success p-3 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-telephone-fill fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark">Telefon Hattı</div>
                        <div class="text-muted small mt-1">
                            <a href="tel:<?= preg_replace('/[^0-9]/', '', $phone) ?>" class="text-decoration-none text-dark fw-bold"><?= htmlspecialchars($phone) ?></a>
                            <div class="text-muted" style="font-size: 11px;">Hafta içi: 09:00 - 18:30</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-start mb-4">
                    <div class="rounded-circle bg-success-subtle text-success p-3 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-whatsapp fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark">WhatsApp Canlı Destek</div>
                        <div class="text-muted small mt-1">
                            <a href="https://wa.me/<?= $whatsapp ?>?text=Merhaba,%20baskı%20siparişim%20hakkında%20bilgi%20almak%20istiyorum." target="_blank" class="text-decoration-none text-success fw-bold">+<?= htmlspecialchars($whatsapp) ?></a>
                            <div class="text-muted" style="font-size: 11px;">Grafiker ve sipariş takip hattı</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-start mb-4">
                    <div class="rounded-circle bg-info-subtle text-info p-3 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-envelope-fill fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark">Kurumsal E-Posta</div>
                        <div class="text-muted small mt-1">
                            <a href="mailto:<?= htmlspecialchars($email) ?>" class="text-decoration-none text-dark"><?= htmlspecialchars($email) ?></a>
                        </div>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-3 border small mt-auto">
                    <div class="fw-bold text-dark mb-1"><i class="bi bi-info-circle text-primary me-1"></i> Şirket Ünvanı &amp; Vergi Bilgisi:</div>
                    <div class="text-muted" style="font-size: 11.5px;"><?= htmlspecialchars($companyName) ?></div>
                    <div class="text-muted" style="font-size: 11px;"><?= htmlspecialchars($taxOffice) ?></div>
                </div>
            </div>
        </div>

        <!-- Sağ Kolon: Hızlı İletişim Formu -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white h-100">
                <h5 class="fw-bold text-dark mb-4 border-bottom pb-3">
                    <i class="bi bi-chat-dots-fill text-primary me-2"></i>Bize Mesaj Gönderin
                </h5>

                <form action="<?= SITE_URL ?>/contact.php" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Adınız Soyadınız *</label>
                            <input type="text" name="name" class="form-control" required placeholder="Örn: Ahmet Yılmaz">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Telefon Numaranız *</label>
                            <input type="tel" name="phone" class="form-control" required placeholder="05XX XXX XX XX">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">E-Posta Adresiniz</label>
                            <input type="email" name="email" class="form-control" placeholder="ornek@alanadi.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Konu</label>
                            <select name="subject" class="form-select">
                                <option value="siparis">Sipariş &amp; Kargo Durumu</option>
                                <option value="fiyat">Özel Ebat &amp; Toplu Fiyat Talebi</option>
                                <option value="tasarim">Grafik &amp; Tasarım Desteği</option>
                                <option value="bayilik">E-Bayilik Başvurusu</option>
                                <option value="diger">Diğer Konular</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark">Mesajınız *</label>
                            <textarea name="message" class="form-control" rows="5" required placeholder="Tasarım, sipariş veya ürünler hakkında sormak istedikleriniz..."></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-xs">
                                <i class="bi bi-send me-1"></i> Mesajı Gönder
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
