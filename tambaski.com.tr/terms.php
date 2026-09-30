<?php
require_once __DIR__ . '/config/config.php';

$pageTitle = 'Mesafeli Satış Sözleşmesi | TamBaskı';
$pageDesc = 'TamBaskı online matbaa siparişlerinde geçerli mesafeli satış sözleşmesi ve yasal tüketici hakları.';

$phone = Helper::getSetting('site_phone', '0850 308 00 00');
$email = Helper::getSetting('site_email', 'info@tambaski.com.tr');
$address = Helper::getSetting('site_address', 'Maltepe Mah. Gümüşsuyu Cad. Topkapı Matbaacılar Sitesi No: 14 Zeytinburnu / İstanbul');
$companyName = Helper::getSetting('company_name', 'TamBaskı Matbaa ve Dijital Baskı Teknolojileri San. Tic. Ltd. Şti.');

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    
    <div class="text-center max-w-700 mx-auto mb-5">
        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1.5 fw-semibold mb-2">Yasal Bilgilendirme</span>
        <h1 class="fw-bold tracking-tight mb-3">Mesafeli Satış Sözleşmesi</h1>
        <p class="text-muted">
            6502 sayılı Tüketicinin Korunması Hakkında Kanun ve Mesafeli Sözleşmeler Yönetmeliği uyarınca tarafların hak ve yükümlülükleri.
        </p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white small text-muted leading-relaxed">
                
                <h5 class="fw-bold text-dark mb-3">MADDE 1 - TARAFLAR</h5>
                
                <p><strong>1.1. SATICI:</strong><br>
                <strong>Ünvan:</strong> <?= htmlspecialchars($companyName) ?><br>
                <strong>Adres:</strong> <?= htmlspecialchars($address) ?><br>
                <strong>Telefon:</strong> <?= htmlspecialchars($phone) ?><br>
                <strong>E-Posta:</strong> <?= htmlspecialchars($email) ?><br>
                <strong>Web:</strong> www.tambaski.com.tr</p>

                <p><strong>1.2. ALICI:</strong><br>
                TamBaskı web sitesinden sipariş veren, fatura ve teslimat adresi sipariş formunda belirtilen gerçek veya tüzel kişidir.</p>

                <hr class="my-4">

                <h5 class="fw-bold text-dark mb-3">MADDE 2 - SÖZLEŞMENİN KONUSU</h5>
                <p>
                    İşbu sözleşmenin konusu, ALICI'nın SATICI'ya ait www.tambaski.com.tr internet sitesinden elektronik ortamda siparişini yaptığı, sözleşmede bahsi geçen nitelikleri haiz ve satış fiyatı belirtilen ürün/hizmetin satışı ve teslimi ile ilgili olarak 6502 sayılı Tüketicinin Korunması Hakkında Kanun ve Mesafeli Sözleşmelere Dair Yönetmelik hükümleri gereğince tarafların hak ve yükümlülüklerinin saptanmasıdır.
                </p>

                <hr class="my-4">

                <h5 class="fw-bold text-dark mb-3">MADDE 3 - SÖZLEŞME KONUSU ÜRÜN VE ÖDEME</h5>
                <p>
                    Ürünlerin cinsi ve türü, miktarı, marka/modeli, rengi, adedi, satış bedeli ve teslimat bilgileri siparişin sonlandığı andaki sipariş özetinde ve ALICI'ya gönderilen onay e-postasında belirtildiği gibidir. Ödemeler kredi kartı (PayTR 256-Bit SSL korumalı 3D Secure altyapısı ile), banka kartı veya SATICI'nın banka hesaplarına Havale/EFT yoluyla yapılır.
                </p>

                <hr class="my-4">

                <h5 class="fw-bold text-dark mb-3">MADDE 4 - GRAFİK ONAYI VE BASKI KURALLARI</h5>
                <p>
                    4.1. ALICI tarafından yüklenen tasarım dosyaları veya online editör üzerinden hazırlanan baskı şablonları, ALICI'nın siparişi onaylaması ile nihai baskı onayını almış sayılır.<br>
                    4.2. İsim, unvan, telefon, adres gibi metin hataları ve yazım yanlışlıklarından ALICI sorumludur. SATICI teknik çözünürlük ve kesim payı kontrollerini yapar ancak içerik metnindeki yazım hatalarından sorumlu tutulamaz.
                </p>

                <hr class="my-4">

                <h5 class="fw-bold text-dark mb-3">MADDE 5 - CAYMA HAKKI VE İSTİSNALARI</h5>
                <p>
                    5.1. 6502 sayılı Tüketicinin Korunması Hakkında Kanun ve Mesafeli Sözleşmeler Yönetmeliği'nin 15. maddesinin (b) bendi uyarınca; <strong>"Tüketicinin istekleri veya kişisel ihtiyaçları doğrultusunda hazırlanan mallara ilişkin sözleşmelerde cayma hakkı kullanılamaz."</strong><br>
                    5.2. Bu kapsamda, ALICI'nın özel talebi doğrultusunda üzerine özel isim, logo, unvan veya özel ölçü basılan kartvizit, levha, broşür, antetli kağıt gibi matbaa ürünlerinde baskı işlemine başlandıktan sonra cayma ve keyfi iade hakkı bulunmamaktadır.<br>
                    5.3. Ancak SATICI kaynaklı üretim hataları (yanlış kağıt, yanlış ebat, hatalı kesim vb.) durumunda ürünler SATICI garantisi altında ücretsiz olarak yeniden basılır.
                </p>

                <hr class="my-4">

                <h5 class="fw-bold text-dark mb-3">MADDE 6 - YETKİLİ MAHKEME</h5>
                <p class="mb-0">
                    İşbu sözleşmenin uygulanmasında, Ticaret Bakanlığınca ilan edilen değere kadar Tüketici Hakem Heyetleri ile ALICI'nın veya SATICI'nın yerleşim yerindeki Tüketici Mahkemeleri yetkilidir.
                </p>

            </div>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
