        </div><!-- /.p-4 -->

        <footer class="mt-auto py-3 px-4 bg-white border-top text-muted small d-flex justify-content-between align-items-center">
            <div>TamBaskı E-Ticaret Yönetim Altyapısı &copy; <?= date('Y') ?></div>
            <div>Sürüm <strong>v2.0.0 (Apple UI + PWA + Mockup Engine)</strong></div>
        </footer>
    </main>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('sidebarToggleBtn');
    const sidebar = document.getElementById('adminSidebar');
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
    }
});
</script>
</body>
</html>
