/**
 * PenTing Balita Desa Tropodo - Script Umum
 */
document.addEventListener('DOMContentLoaded', function () {

  // Konfirmasi sebelum menghapus data balita
  document.querySelectorAll('.btn-hapus').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      const url = this.getAttribute('href');
      if (confirm('Yakin ingin menghapus data ini? Riwayat pengukuran juga akan terhapus.')) {
        window.location.href = url;
      }
    });
  });

  // Auto dismiss alert setelah beberapa detik
  document.querySelectorAll('.alert').forEach(function (alertEl) {
    setTimeout(function () {
      alertEl.style.transition = 'opacity 0.5s';
      alertEl.style.opacity = '0';
      setTimeout(() => alertEl.remove(), 500);
    }, 4000);
  });

});

  // Splash screen ditampilkan setiap kali halaman aplikasi dibuka.
  const splash = document.getElementById('appSplash');
  if (splash) {
    window.setTimeout(function () {
      splash.classList.add('is-hidden');
      window.setTimeout(function () { splash.remove(); }, 500);
    }, 2200);
  }
  const heroSplash = document.getElementById('heroSplashScreen');
  if (heroSplash) {
    window.setTimeout(function () {
      heroSplash.classList.add('is-hidden');
      window.setTimeout(function () { heroSplash.remove(); }, 700);
    }, 2200);
  }
