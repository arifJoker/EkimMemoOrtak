<?php
/**
 * TAMBASKI.COM.TR - Footer Şablonu (Precision Studio Print)
 */
?>
<!-- ================= FOOTER (SHARED COMPONENT) ================= -->
<footer class="bg-surface-container-low border-t border-outline-variant mt-auto transition-colors duration-150">
    <div class="w-full px-6 py-12 max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
        <!-- Brand & Technical Specs -->
        <div class="flex flex-col md:flex-row items-center gap-4 text-center md:text-left">
            <a class="flex items-center gap-2 group" href="index.php" title="TamBaskı Online Matbaa">
                <img src="assets/img/logo.svg?v=2" alt="TamBaskı" class="h-7 w-auto">
            </a>
            <span class="hidden md:inline text-outline-variant">|</span>
            <p class="text-xs text-on-surface-variant">
                © <?= date('Y') ?> TamBaskı Commercial Printworks Inc. Calibrated CMYK &amp; Pantone certified.
            </p>
        </div>
        <!-- Footer Navigation Links -->
        <nav class="flex flex-wrap justify-center items-center gap-x-6 gap-y-2 text-xs font-medium text-on-surface-variant">
            <a class="hover:underline hover:text-secondary transition-colors" href="index.php#paper-stocks">Materyal Standartları</a>
            <a class="hover:underline hover:text-secondary transition-colors" href="category.php?slug=kartvizit">Kartvizitler</a>
            <a class="hover:underline hover:text-secondary transition-colors" href="category.php?slug=dekota-pleksi-kesim">Lazer &amp; Pleksi</a>
            <a class="hover:underline hover:text-secondary transition-colors" href="dealer_apply.php">E-Bayilik (%25)</a>
            <a class="hover:underline hover:text-secondary transition-colors" href="order_tracking.php">Kargo Takibi</a>
            <a class="hover:underline hover:text-secondary transition-colors" href="index.php#sample-kit">Numune Kiti</a>
        </nav>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Global Search shortcut ⌘K / Ctrl+K
document.addEventListener('keydown', (e) => {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        const searchInput = document.querySelector('input[name="q"]');
        if (searchInput) {
            searchInput.focus();
        }
    }
});
</script>
</body>
</html>
