<?php
require_once __DIR__ . '/config/config.php';

$pageTitle = 'İptal ve İade Koşulları | TamBaskı';
$pageDesc = 'TamBaskı siparişlerinizde geçerli iptal, iade ve hatalı baskı yeniden üretim prosedürleri.';

$phone = Helper::getSetting('site_phone', '0850 308 00 00');
$email = Helper::getSetting('site_email', 'info@tambaski.com.tr');

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    
    <div class="text-center max-w-700 mx-auto mb-5">
        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1.5 fw-semibold mb-2">Müşteri Memnuniyeti</span>
        <h1 class="fw-bold tracking-tight mb-3">İptal, İade ve Değişim Koşulları</h1>
        <p class="text-muted">
            TamBaskı %100 baskı kalitesi garantisi ve yasal tüketici hakları çerçevesinde iade süreçleri.
        </p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white small text-muted leading-relaxed">
                
                <h5 class="fw-bold text-dark mb-3">
                    <i class="bi bi-shield-check text-success me-2"></i>1. %100 Baskı Kalitesi &amp; Hatalı Ürün Güvencesi
                </h5>
                <p>
                    TamBaskı olarak müşteri memnuniyetini en üst seviyede tutmayı hedefliyoruz. Tarafımızdan kaynaklanan her türlü teknik ve üretimsel kusurda (hatalı kağıt türü, sipariş edilenden farklı kalınlık/ebat, baskıda kayma veya hatalı kesim) siparişiniz <strong>hiçbir ek ücret talep edilmeksizin derhal yeniden basılır</strong> ve en hızlı şekilde kargoya verilir.
                </p>

                <hr class="my-4">

                <h5 class="fw-bold text-dark mb-3">
                    <i class="bi bi-x-octagon text-danger me-2"></i>2. Sipariş İptali Süreci
                </h5>
                <p>
                    Verdiğiniz siparişi, grafik onayı verilmeden ve <strong>üretim bandına (baskı kalıbına) alınmadan önce</strong> müşteri hizmetlerimizi arayarak veya WhatsApp destek hattımızdan bize ulaşarak iptal edebilirsiniz. Üretime alınmamış siparişlerin ödemesi, kullandığınız ödeme kanalına (Kredi Kartı / Havale) 1-3 iş günü içinde kesintisiz olarak iade edilir.
                </p>

                <hr class="my-4">

                <h5 class="fw-bold text-dark mb-3">
                    <i class="bi bi-info-circle text-primary me-2"></i>3. Kişiye / Firmaya Özel Üretimlerde Cayma Hakkı İstisnası
                </h5>
                <p>
                    6502 sayılı Tüketicinin Korunması Hakkında Kanun ve Mesafeli Sözleşmeler Yönetmeliği Madde 15/b uyarınca; <em>"Tüketicinin istekleri veya kişisel ihtiyaçları doğrultusunda hazırlanan mallara ilişkin sözleşmelerde cayma hakkı kullanılamaz."</em>
                </p>
                <p>
                    Bu yasal düzenleme gereğince, üzerine firma logosu, isim, unvan, özel iletişim bilgileri basılan veya müşterinin belirlediği özel ebatlarda CNC kesimi yapılan ürünlerde (kartvizit, İSG levhası, broşür, antetli kağıt vb.) baskı onayından sonra <strong>keyfi iade ve iptal kabul edilmemektedir</strong>.
                </p>

                <hr class="my-4">

                <h5 class="fw-bold text-dark mb-3">
                    <i class="bi bi-arrow-repeat text-primary me-2"></i>4. Hatalı Ürün Bildirimi ve Yeniden Basım Adımları
                </h5>
                <ol class="ps-3 mb-0">
                    <li class="mb-2">Kargonuzu teslim aldıktan sonraki <strong>7 iş günü içinde</strong> hatalı olduğunu düşündüğünüz ürünün net fotoğraflarını çekiniz.</li>
                    <li class="mb-2">Sipariş numaranız ile birlikte fotoğrafları <a href="mailto:<?= htmlspecialchars($email) ?>" class="text-dark fw-bold"><?= htmlspecialchars($email) ?></a> adresimize veya WhatsApp hattımıza iletiniz.</li>
                    <li class="mb-2">Kalite kontrol ekibimiz bildirimi 24 saat içinde inceleyerek haklı şikayetlerde ücretsiz yeniden üretim sürecini derhal başlatacaktır.</li>
                </ol>

            </div>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
