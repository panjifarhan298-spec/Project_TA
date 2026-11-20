    <footer class="app-footer text-center py-4 mt-4">
      <p class="mb-0 small text-muted">
        &copy; <?= date('Y') ?> Posyandu Desa Tropodo &mdash; Sistem Informasi Posyandu.
      </p>
    </footer>

  </div><!-- /.app-content -->
</div><!-- /.app-shell -->

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <!-- Custom Script -->
    <script src="/Posyandu_Desa_Tropodo/assets/js/script.js?v=20261117blue"></script>
    <script>
      (function () {
        var sidebar = document.getElementById('appSidebar');
        var overlay = document.getElementById('sidebarOverlay');
        var openBtn = document.getElementById('sidebarToggle');
        var closeBtn = document.getElementById('sidebarClose');
        if (!sidebar) return;

        function openSidebar() {
          sidebar.classList.add('show');
          overlay.classList.add('show');
          if (openBtn) openBtn.setAttribute('aria-expanded', 'true');
          document.body.classList.add('sidebar-locked');
        }
        function closeSidebar() {
          sidebar.classList.remove('show');
          overlay.classList.remove('show');
          if (openBtn) openBtn.setAttribute('aria-expanded', 'false');
          document.body.classList.remove('sidebar-locked');
        }
        if (openBtn) openBtn.addEventListener('click', openSidebar);
        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if (overlay) overlay.addEventListener('click', closeSidebar);
        document.addEventListener('keydown', function (e) {
          if (e.key === 'Escape') closeSidebar();
        });
      })();
    </script>
  </body>
</html>
