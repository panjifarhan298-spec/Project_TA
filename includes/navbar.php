<?php
/**
 * Navigasi Sidebar & Header - Posyandu System
 * Disesuaikan persis dengan layout Image 1 dengan palet warna Biru Muda (#60A5FA), Biru Tua (#123B70), dan Putih (#FFFFFF).
 */
$aktif = basename($_SERVER['PHP_SELF']);
$userRole = $_SESSION['role'] ?? 'petugas';
$dashboardUrl = $userRole === 'admin' ? 'dashboard-admin.php' : 'dashboard-bidan.php';
$isDashboard = $aktif === 'dashboard-admin.php' || $aktif === 'dashboard-bidan.php';
if ($userRole === 'admin') {
    $roleTitle = 'ADMIN UTAMA';
    $panelTitle = 'Panel Admin Utama';
} elseif ($userRole === 'bidan') {
    $roleTitle = 'BIDAN DESA';
    $panelTitle = 'Panel Bidan Desa';
} else {
    $roleTitle = 'PETUGAS POSYANDU';
    $panelTitle = 'Panel Petugas Posyandu';
}
?>
<div class="app-shell">

  <!-- Topbar (mobile only) -->
  <header class="app-topbar d-lg-none">
    <button class="sidebar-toggle" type="button" id="sidebarToggle" aria-label="Buka menu" aria-expanded="false" aria-controls="appSidebar">
      <i class="fa-solid fa-bars"></i>
    </button>
    <a class="app-brand" href="<?= $dashboardUrl ?>">
      <img class="app-brand-logo" src="assets/img/logo-sidoarjo.png" alt="Lambang Kabupaten Sidoarjo">
      <span>POSYANDU DESA KEMUNING TROPODO</span>
    </a>
    <a class="btn-logout top-logout" href="logout.php" title="Logout" aria-label="Logout"><i class="fa-solid fa-right-from-bracket"></i></a>
  </header>

  <!-- Overlay (mobile only) -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- Sidebar Kiri Sesuai Gambar 1 -->
  <aside class="app-sidebar" id="appSidebar">
    <div class="sidebar-head">
      <a class="sidebar-brand" href="<?= $dashboardUrl ?>">
        <span class="sidebar-brand-icon"><img src="assets/img/logo-sidoarjo.png" alt="Lambang Kabupaten Sidoarjo"></span>
        <span class="sidebar-brand-text" style="font-size: 0.8rem; line-height: 1.25;">POSYANDU KEMUNING TROPODO<small style="color: #60A5FA; font-weight: 700; letter-spacing: 0.5px;">Role: <?= $roleTitle ?></small></span>
      </a>
      <button class="sidebar-close d-lg-none" type="button" id="sidebarClose" aria-label="Tutup menu">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <nav class="sidebar-nav" aria-label="Navigasi utama">
      <!-- Menu Dashboard -->
      <a class="sidebar-link <?= $isDashboard ? 'active' : '' ?>" href="<?= $dashboardUrl ?>">
        <span class="sidebar-link-icon"><i class="fa-solid fa-chart-pie"></i></span>
        <span class="sidebar-link-label">Dashboard</span>
      </a>

      <!-- SECTION: DATA MASTER -->
      <div class="sidebar-section-title">DATA MASTER</div>
      <a class="sidebar-link <?= $aktif === 'data-balita.php' ? 'active' : '' ?>" href="data-balita.php">
        <span class="sidebar-link-icon"><i class="fa-solid fa-child-reaching"></i></span>
        <span class="sidebar-link-label">Data Balita</span>
      </a>
      <a class="sidebar-link <?= $aktif === 'data-ibu-hamil.php' ? 'active' : '' ?>" href="data-ibu-hamil.php">
        <span class="sidebar-link-icon"><i class="fa-solid fa-person-pregnant"></i></span>
        <span class="sidebar-link-label">Data Ibu &amp; Hamil</span>
      </a>
      <a class="sidebar-link <?= $aktif === 'data-lansia.php' ? 'active' : '' ?>" href="data-lansia.php">
        <span class="sidebar-link-icon"><i class="fa-solid fa-person-cane"></i></span>
        <span class="sidebar-link-label">Data Lansia</span>
      </a>

      <!-- SECTION: PELAYANAN -->
      <div class="sidebar-section-title">PELAYANAN</div>
      <a class="sidebar-link <?= $aktif === 'riwayat-pengukuran.php' ? 'active' : '' ?>" href="riwayat-pengukuran.php">
        <span class="sidebar-link-icon"><i class="fa-solid fa-stethoscope"></i></span>
        <span class="sidebar-link-label">Pemeriksaan</span>
      </a>
      <a class="sidebar-link <?= $aktif === 'imunisasi.php' ? 'active' : '' ?>" href="imunisasi.php">
        <span class="sidebar-link-icon"><i class="fa-solid fa-syringe"></i></span>
        <span class="sidebar-link-label">Imunisasi</span>
      </a>
      <a class="sidebar-link <?= $aktif === 'jadwal-posyandu.php' ? 'active' : '' ?>" href="jadwal-posyandu.php">
        <span class="sidebar-link-icon"><i class="fa-solid fa-calendar-days"></i></span>
        <span class="sidebar-link-label">Jadwal Kegiatan</span>
      </a>

      <!-- SECTION: INFORMASI & PUBLIKASI -->
      <div class="sidebar-section-title">INFORMASI &amp; PUBLIKASI</div>
      <a class="sidebar-link <?= $aktif === 'data-posyandu.php' ? 'active' : '' ?>" href="data-posyandu.php">
        <span class="sidebar-link-icon"><i class="fa-solid fa-house-medical"></i></span>
        <span class="sidebar-link-label">Data 8 Posyandu</span>
      </a>
      <a class="sidebar-link <?= $aktif === 'laporan.php' ? 'active' : '' ?>" href="laporan.php">
        <span class="sidebar-link-icon"><i class="fa-solid fa-file-lines"></i></span>
        <span class="sidebar-link-label">Laporan</span>
      </a>

      <?php if ($userRole === 'admin'): ?>
        <a class="sidebar-link <?= in_array($aktif, ['manajemen-user.php', 'tambah-user.php'], true) ? 'active' : '' ?>" href="manajemen-user.php">
          <span class="sidebar-link-icon"><i class="fa-solid fa-users-gear"></i></span>
          <span class="sidebar-link-label">Manajemen User</span>
        </a>
        <a class="sidebar-link <?= $aktif === 'aktivitas-login.php' ? 'active' : '' ?>" href="aktivitas-login.php">
          <span class="sidebar-link-icon"><i class="fa-solid fa-clock-rotate-left"></i></span>
          <span class="sidebar-link-label">Aktivitas Login</span>
        </a>
      <?php endif; ?>
    </nav>

    <div class="sidebar-foot">
      <a class="sidebar-logout" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i><span>Keluar / Logout</span></a>
    </div>
  </aside>

  <!-- Konten Aplikasi -->
  <div class="app-content">
    <!-- Topbar Panel Sesuai Gambar 1 -->
    <div class="top-panel-card mx-3 mx-lg-4 mt-3 mt-lg-4 mb-3 d-flex align-items-center justify-content-between p-3 bg-white rounded-3 shadow-sm border">
      <div class="d-flex align-items-center gap-2">
        <div class="panel-icon text-primary"><i class="fa-solid fa-user-shield fa-lg" style="color: #60A5FA;"></i></div>
        <h5 class="fw-bold mb-0" style="color: #123B70; font-size: 1.05rem;"><?= $panelTitle ?></h5>
      </div>
      <div class="d-flex align-items-center gap-2">
        <div class="user-pill-avatar" style="width: 32px; height: 32px; border-radius: 50%; background: #123B70; color: #FFF; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem;">
          <?= strtolower(substr($_SESSION['username'] ?? 'a', 0, 1)) ?>
        </div>
        <span class="fw-semibold small" style="color: #123B70;"><?= bersih($_SESSION['username'] ?? 'admin') ?></span>
        <a href="logout.php" class="text-muted ms-2" title="Logout"><i class="fa-solid fa-chevron-down small"></i></a>
      </div>
    </div>
