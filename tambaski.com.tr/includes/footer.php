<?php
/**
 * TAMBASKI.COM.TR - Footer Şablonu
 */
?>
<!-- Apple Tarzı Footer -->
<footer class="apple-footer">
    <div class="container">
        <div class="row g-4">
            <!-- Marka & Açıklama -->
            <div class="col-lg-4 col-md-6">
                <div class="mb-3">
                    <img src="assets/img/logo.svg" alt="TamBaskı" style="height: 38px; filter: brightness(0) invert(1);">
                </div>
                <p class="text-white-50 small pe-lg-4">
                    TamBaskı, modern baskı teknolojileri, lazer/CNC kesim parkuru ve geniş ürün yelpazesiyle kurumsal ve bireysel tüm baskı ihtiyaçlarınızı tek çatı altında çözüme kavuşturur.
                </p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" class="text-white-50 fs-5"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-white-50 fs-5"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-white-50 fs-5"><i class="bi bi-linkedin"></i></a>
                    <a href="https://wa.me/905440000000" class="text-success fs-5"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>

            <!-- Hızlı Bağlantılar -->
            <div class="col-lg-2 col-md-6 col-6">
                <h6>Baskı Ürünleri</h6>
                <ul>
                    <li><a href="category.php?slug=kartvizit">Kartvizitler</a></li>
                    <li><a href="category.php?slug=el-ilani-brosur">Broşür & El İlanı</a></li>
                    <li><a href="category.php?slug=kurumsal-urunler">Cepli Dosya & Zarf</a></li>
                    <li><a href="category.php?slug=etiket-sticker">Etiket & Sticker</a></li>
                    <li><a href="category.php?slug=promosyon-hediyelik">Promosyon & Kupa</a></li>
                </ul>
            </div>

            <!-- Kesim & Reklam -->
            <div class="col-lg-3 col-md-6 col-6">
                <h6>Kesim & Dış Mekan</h6>
                <ul>
                    <li><a href="category.php?slug=dekota-pleksi-kesim">Dekota (Foreks) Baskı</a></li>
                    <li><a href="category.php?slug=dekota-pleksi-kesim">Pleksi Lazer Kesim</a></li>
                    <li><a href="category.php?slug=folyo-branda-reklam">Folyo Baskı & Kaplama</a></li>
                    <li><a href="category.php?slug=folyo-branda-reklam">Dökme Branda & Afiş</a></li>
                    <li><a href="category.php?slug=folyo-branda-reklam">Roll-up Banner Stand</a></li>
                </ul>
            </div>

            <!-- Kurumsal & İletişim -->
            <div class="col-lg-3 col-md-6">
                <h6>Müşteri Hizmetleri</h6>
                <ul>
                    <li><a href="order_tracking.php"><i class="bi bi-truck me-1"></i>Kargo & Sipariş Takibi</a></li>
                    <li><a href="dealer_apply.php"><i class="bi bi-briefcase me-1"></i>E-Bayi Başvurusu (%25)</a></li>
                    <li><a href="tel:08503080000"><i class="bi bi-headset me-1"></i>0850 308 00 00</a></li>
                    <li><a href="mailto:destek@tambaski.com.tr"><i class="bi bi-envelope me-1"></i>destek@tambaski.com.tr</a></li>
                </ul>
                <div class="mt-3 p-3 rounded-3" style="background: rgba(255, 255, 255, 0.05);">
                    <small class="text-white-50 d-block mb-1">Güvenli Ödeme</small>
                    <div class="d-flex gap-2 text-white-50 fs-4">
                        <i class="bi bi-credit-card-2-front"></i>
                        <i class="bi bi-shield-lock-fill text-success"></i>
                        <i class="bi bi-bank"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="text-white-50 small">
                © <?= date('Y') ?> <strong>TamBaskı</strong>. Tüm hakları saklıdır.
            </div>
            <div class="d-flex gap-3 text-white-50 small">
                <a href="#">Gizlilik Politikası</a>
                <a href="#">Mesafeli Satış Sözleşmesi</a>
                <a href="#">Baskı Şablonları & Teknik Kılavuz</a>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- TamBaskı JS -->
<script src="assets/js/main.js?v=<?= time() ?>"></script>
</body>
</html>
