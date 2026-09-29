<?php
require_once __DIR__ . '/../config/config.php';
$phone = Helper::getSetting('site_phone', '0850 123 45 67');
$whatsapp = Helper::getSetting('site_whatsapp', '905550000000');
$email = Helper::getSetting('site_email', 'destek@baskimatbaa.com');
?>
<!-- WhatsApp Hızlı İletişim Butonu -->
<a href="https://wa.me/<?= $whatsapp ?>?text=Merhaba,%20baskı%20siparişi%20hakkında%20bilgi%20almak%20istiyorum." 
   target="_blank" 
   class="btn btn-success position-fixed rounded-circle shadow-lg d-flex align-items-center justify-content-center"
   style="bottom: 75px; right: 20px; width: 56px; height: 56px; z-index: 999; font-size: 28px;">
    <i class="bi bi-whatsapp"></i>
</a>

<!-- Mobil Alt Gezinme Barı -->
<nav class="mobile-bottom-nav">
    <a href="<?= SITE_URL ?>/" class="mobile-bottom-item <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
        <i class="bi bi-house-door"></i>
        <span>Ana Sayfa</span>
    </a>
    <a href="<?= SITE_URL ?>/category.php" class="mobile-bottom-item <?= basename($_SERVER['PHP_SELF']) == 'category.php' ? 'active' : '' ?>">
        <i class="bi bi-grid"></i>
        <span>Ürünler</span>
    </a>
    <a href="<?= SITE_URL ?>/cart.php" class="mobile-bottom-item <?= basename($_SERVER['PHP_SELF']) == 'cart.php' ? 'active' : '' ?>">
        <i class="bi bi-bag"></i>
        <span>Sepetim</span>
    </a>
    <a href="<?= SITE_URL ?>/account.php" class="mobile-bottom-item <?= basename($_SERVER['PHP_SELF']) == 'account.php' ? 'active' : '' ?>">
        <i class="bi bi-person"></i>
        <span>Hesabım</span>
    </a>
</nav>

<!-- Footer -->
<footer class="bg-white border-top mt-5 pt-5 pb-4">
    <div class="container">
        <div class="row g-4 mb-4">
            
            <div class="col-lg-4 col-md-6">
                <div class="mb-3">
                    <a href="<?= SITE_URL ?>/" class="text-decoration-none">
                        <img src="<?= SITE_URL ?>/assets/img/logo.svg" alt="TamBaskı" style="height: 42px; width: auto;">
                    </a>
                </div>
                <p class="text-muted small pe-lg-4">
                    Türkiye'nin en hızlı ve güvenilir online matbaası. Apple sadeliğinde sipariş deneyimi, canlı vektörel tasarım editörü, PayTR & iyzico güvenli ödeme ve anlaşmalı kargo ile kapınızda.
                </p>
                <div class="d-flex gap-3 text-muted">
                    <span class="small"><i class="bi bi-shield-check text-success me-1"></i>256-Bit SSL</span>
                    <span class="small"><i class="bi bi-credit-card text-primary me-1"></i>3D Secure</span>
                    <span class="small"><i class="bi bi-truck text-dark me-1"></i>Hızlı Kargo</span>
                </div>
            </div>

            <div class="col-lg-2 col-6">
                <h6 class="fw-bold mb-3">Popüler Baskılar</h6>
                <ul class="list-unstyled small text-muted">
                    <li class="mb-2"><a href="<?= SITE_URL ?>/product.php?slug=kurumsal-prestij-kartvizit" class="text-decoration-none text-muted">Kartvizit Çeşitleri</a></li>
                    <li class="mb-2"><a href="<?= SITE_URL ?>/product.php?slug=dekota-isg-guvenlik-uyari-levhasi" class="text-decoration-none text-muted">Dekota Uyarı Levhaları</a></li>
                    <li class="mb-2"><a href="<?= SITE_URL ?>/category.php" class="text-decoration-none text-muted">Tüm Kategoriler</a></li>
                    <li class="mb-2"><a href="<?= SITE_URL ?>/dealer_apply.php" class="text-decoration-none text-danger fw-bold">E-Bayi Başvurusu (%25)</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-6">
                <h6 class="fw-bold mb-3">Kurumsal &amp; Yasal</h6>
                <ul class="list-unstyled small text-muted">
                    <li class="mb-2"><a href="<?= SITE_URL ?>/contact.php" class="text-decoration-none text-muted">İletişim &amp; Ulaşım</a></li>
                    <li class="mb-2"><a href="<?= SITE_URL ?>/delivery.php" class="text-decoration-none text-muted">Teslimat &amp; Kargo</a></li>
                    <li class="mb-2"><a href="<?= SITE_URL ?>/terms.php" class="text-decoration-none text-muted">Mesafeli Satış Sözleşmesi</a></li>
                    <li class="mb-2"><a href="<?= SITE_URL ?>/returns.php" class="text-decoration-none text-muted">İptal &amp; İade Koşulları</a></li>
                    <li class="mb-2"><a href="<?= SITE_URL ?>/privacy.php" class="text-decoration-none text-muted">Gizlilik &amp; KVKK Politikası</a></li>
                    <li class="mb-2"><a href="<?= SITE_URL ?>/order_tracking.php" class="text-decoration-none text-muted">Kargo &amp; Sipariş Takibi</a></li>
                </ul>
            </div>

            <div class="col-lg-4 col-md-6">
                <h6 class="fw-bold mb-3">Müşteri Hizmetleri &amp; Destek</h6>
                <div class="mb-2 small"><i class="bi bi-geo-alt text-danger me-2"></i><strong>Fabrika Adresi:</strong> Topkapı Matbaacılar Sitesi No: 14 Zeytinburnu / İstanbul</div>
                <div class="mb-2 small"><i class="bi bi-telephone text-primary me-2"></i><strong>Telefon:</strong> <?= htmlspecialchars($phone) ?></div>
                <div class="mb-2 small"><i class="bi bi-whatsapp text-success me-2"></i><strong>WhatsApp:</strong> +<?= htmlspecialchars($whatsapp) ?></div>
                <div class="mb-3 small"><i class="bi bi-envelope text-primary me-2"></i><strong>E-Posta:</strong> <?= htmlspecialchars($email) ?></div>
                <div class="p-3 bg-light rounded-3 small">
                    <i class="bi bi-clock-history me-1 text-muted"></i> Hafta içi 09:00 - 18:30 saatleri arasında kesintisiz grafik ve sipariş desteği.
                </div>
            </div>

        </div>

        <hr class="my-4 text-muted">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center small text-muted">
            <div>
                © <?= date('Y') ?> TamBaskı A.Ş. Tüm hakları saklıdır.
                <a href="<?= SITE_URL ?>/admin/login.php" class="text-secondary small text-decoration-none ms-3 opacity-75" title="Yönetici Girişi">
                    <i class="bi bi-shield-lock me-1"></i>Yönetici Girişi
                </a>
            </div>
            <div class="d-flex gap-3 mt-2 mt-md-0 align-items-center">
                <span>Ödeme Altyapıları:</span>
                <span class="badge bg-dark">PayTR</span>
                <span class="badge bg-primary">iyzico</span>
                <span class="badge bg-secondary">Havale / EFT</span>
            </div>
        </div>
    </div>
</footer>

<!-- PWA Mobile Uygulama Yükleme Bildirimi -->
<div id="pwaInstallBanner" class="position-fixed bottom-0 start-0 end-0 p-3 bg-white border-top shadow-lg d-none" style="z-index: 1090; border-top-left-radius: 20px; border-top-right-radius: 20px;">
    <div class="container d-flex align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <img src="<?= SITE_URL ?>/assets/img/icon-192.png" width="48" height="48" class="rounded-3 shadow-sm" alt="TamBaskı App">
            <div>
                <div class="fw-bold text-dark" style="font-size: 14px;">TamBaskı Uygulamasını Yükleyin</div>
                <div class="text-muted small">Daha hızlı mobil sipariş ve anlık kargo bildirimleri</div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-light rounded-pill px-3" onclick="dismissPwaBanner()">Sonra</button>
            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" id="btnInstallPwa">Uygulamayı Ekle</button>
        </div>
    </div>
</div>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
let deferredPrompt;
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    if (!localStorage.getItem('pwa_banner_dismissed')) {
        const banner = document.getElementById('pwaInstallBanner');
        if (banner) banner.classList.remove('d-none');
    }
});

const installBtn = document.getElementById('btnInstallPwa');
if (installBtn) {
    installBtn.addEventListener('click', async () => {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            deferredPrompt = null;
            document.getElementById('pwaInstallBanner').classList.add('d-none');
        }
    });
}

function dismissPwaBanner() {
    document.getElementById('pwaInstallBanner').classList.add('d-none');
    localStorage.setItem('pwa_banner_dismissed', 'true');
}
</script>

</body>
</html>
