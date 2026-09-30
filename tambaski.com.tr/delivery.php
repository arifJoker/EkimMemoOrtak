<?php
require_once __DIR__ . '/config/config.php';

$pageTitle = 'Teslimat ve Kargo Koşulları | TamBaskı';
$pageDesc = 'TamBaskı siparişlerinizin üretim, paketleme ve anlaşmalı kargo teslimat süreleri hakkında detaylı bilgiler.';

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    
    <div class="text-center max-w-700 mx-auto mb-5">
        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1.5 fw-semibold mb-2">Kargo &amp; Lojistik</span>
        <h1 class="fw-bold tracking-tight mb-3">Teslimat ve Kargo Koşulları</h1>
        <p class="text-muted">
            Siparişlerinizin üretimden kapınıza kadar olan sevkiyat ve teslimat süreçleri hakkında bilmeniz gereken tüm detaylar.
        </p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-4">
                
                <h5 class="fw-bold text-dark mb-3">
                    <i class="bi bi-clock-history text-primary me-2"></i>1. Üretim ve Baskı Süreleri
                </h5>
                <p class="text-muted small leading-relaxed mb-4">
                    TamBaskı üzerinden verilen siparişler, grafik onayının tamamlanması ve ödemenin onaylanmasının ardından otomatik olarak üretim bandına alınır. Ürün grubuna göre standart üretim süreleri şu şekildedir:
                </p>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered small align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Ürün Grubu</th>
                                <th>Standart Üretim Süresi</th>
                                <th>Acil (24 Saat) Seçeneği</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold">Standart &amp; Ekonomik Kartvizitler</td>
                                <td>2 - 3 İş Günü</td>
                                <td><span class="badge bg-success-subtle text-success">Mevcut (24 Saatte Kargo)</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Özel Laklı &amp; Varak Yaldızlı Kartvizitler</td>
                                <td>4 - 6 İş Günü</td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Dekota &amp; Forex İSG Uyarı Levhaları</td>
                                <td>1 - 2 İş Günü</td>
                                <td><span class="badge bg-success-subtle text-success">Mevcut</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Broşür, El İlanı &amp; Cepli Dosya</td>
                                <td>3 - 4 İş Günü</td>
                                <td><span class="badge bg-success-subtle text-success">Mevcut</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h5 class="fw-bold text-dark mb-3">
                    <i class="bi bi-truck text-primary me-2"></i>2. Anlaşmalı Kargo Firmaları ve Teslimat
                </h5>
                <p class="text-muted small leading-relaxed mb-3">
                    Siparişleriniz üretim ve kalite kontrol aşamalarını tamamladıktan sonra korunaklı özel kutularda paketlenir ve Türkiye genelinde yaygın dağıtım ağına sahip anlaşmalı kargo firmalarımıza (<strong>Yurtiçi Kargo, MNG Kargo, Aras Kargo</strong>) teslim edilir.
                </p>
                <ul class="text-muted small mb-4">
                    <li class="mb-2"><strong>İstanbul ve Çevre İller:</strong> Kargoya verildikten sonra 1 iş günü içinde teslimat sağlanır.</li>
                    <li class="mb-2"><strong>Diğer İller:</strong> Kargoya verildikten sonra mesafeye bağlı olarak 1 - 3 iş günü içinde adrese teslim edilir.</li>
                    <li class="mb-2"><strong>Mobil Alanlar:</strong> Kargo şubesi bulunmayan köy ve kasaba gibi mobil teslimat bölgelerine haftanın belirli günlerinde dağıtım yapılmaktadır.</li>
                </ul>

                <h5 class="fw-bold text-dark mb-3">
                    <i class="bi bi-gift text-primary me-2"></i>3. Kargo Ücreti ve Ücretsiz Kargo
                </h5>
                <p class="text-muted small leading-relaxed mb-4">
                    Sepet tutarınız <strong>750,00 ₺ ve üzerinde</strong> olduğunda Türkiye'nin tüm illerine kargo gönderimi <strong>TAMAMEN ÜCRETSİZDİR</strong>. 750,00 ₺ altındaki siparişlerde standart sabit kargo ücreti ödeme adımında sepete eklenir.
                </p>

                <h5 class="fw-bold text-dark mb-3">
                    <i class="bi bi-box-seam text-primary me-2"></i>4. Teslim Alırken Dikkat Edilmesi Gerekenler
                </h5>
                <p class="text-muted small leading-relaxed mb-0">
                    Kargonuzu teslim alırken pakette ezilme, yırtılma veya ıslanma gibi belirgin bir hasar olup olmadığını kontrol ediniz. Eğer koli hasar görmüşse, kargo görevlisine <strong>"Hasar Tespit Tutanağı"</strong> tutturarak paketi teslim almayınız ve durumu derhal müşteri hizmetlerimize bildiriniz.
                </p>

            </div>

        </div>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
