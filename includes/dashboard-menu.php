<?php
$isAdminUser = ($_SESSION['role'] ?? '') === 'admin';
$dashboardShortcuts = [
    ['label' => 'Imunisasi', 'description' => 'Jadwal dan informasi imunisasi anak.', 'href' => 'imunisasi.php', 'icon' => 'fa-syringe', 'class' => 'shortcut-blue'],
    ['label' => 'Jadwal Posyandu', 'description' => 'Lihat jadwal kegiatan tiap wilayah.', 'href' => 'jadwal-posyandu.php', 'icon' => 'fa-calendar-days', 'class' => 'shortcut-green'],
    ['label' => 'Laporan', 'description' => 'Ringkasan data dan cetak laporan.', 'href' => 'laporan.php', 'icon' => 'fa-file-lines', 'class' => 'shortcut-orange'],
];
if ($isAdminUser) {
    $dashboardShortcuts[] = ['label' => 'Manajemen User', 'description' => 'Kelola akun Admin dan Bidan.', 'href' => 'manajemen-user.php', 'icon' => 'fa-users-gear', 'class' => 'shortcut-purple'];
}
?>

<section class="dashboard-shortcuts mb-4" aria-labelledby="shortcut-title">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h5 id="shortcut-title" class="fw-bold mb-1" style="color: #123B70;">Menu Layanan & Akses Cepat</h5>
      <p class="text-muted small mb-0">Navigasi langsung ke fitur utama sistem informasi Posyandu.</p>
    </div>
    <i class="fa-solid fa-grip" style="color: #60A5FA; font-size: 1.25rem;"></i>
  </div>
  <div class="row g-3">
    <?php foreach ($dashboardShortcuts as $shortcut): ?>
      <div class="col-6 <?= count($dashboardShortcuts) === 3 ? 'col-lg-4' : 'col-lg-3' ?>">
        <a class="dashboard-shortcut-card <?= $shortcut['class'] ?>" href="<?= $shortcut['href'] ?>">
          <span class="dashboard-shortcut-icon"><i class="fa-solid <?= $shortcut['icon'] ?>"></i></span>
          <span class="dashboard-shortcut-content"><strong><?= $shortcut['label'] ?></strong><small><?= $shortcut['description'] ?></small></span>
          <i class="fa-solid fa-chevron-right dashboard-shortcut-arrow"></i>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
</section>
