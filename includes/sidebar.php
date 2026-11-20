<?php
// Pastikan session sudah aktif sebelum membaca data pengguna.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Ambil role dari session. Mendukung dua pola session umum.
$sessionRole = $_SESSION['role'] ?? ($_SESSION['user']['role'] ?? '');

// Kompatibilitas dengan kode lama yang masih menggunakan $current['role'].
$current = is_array($current ?? null) ? $current : [];
$current['role'] = $current['role'] ?? $sessionRole;

$active_page = $active_page ?? '';
$items = [
    ['dashboard', 'Dashboard', $current['role'] === 'admin' ? 'dashboard-admin.php' : 'dashboard-bidan.php', 'fa-grid-2'],
    ['balita', 'Data Balita', 'data-balita.php', 'fa-child-reaching'],
    ['tambah', 'Tambah Balita', 'tambah-balita.php', 'fa-plus'],
    ['pengukuran', 'Pengukuran', 'riwayat-pengukuran.php', 'fa-ruler-combined'],
    ['grafik', 'Grafik Pertumbuhan', 'grafik.php', 'fa-chart-line'],
    ['posyandu', '8 Posyandu', 'data-posyandu.php', 'fa-location-dot'],
];

if ($current['role'] === 'admin') {
    $items[] = ['users', 'Manajemen User', 'manajemen-user.php', 'fa-users'];
    $items[] = ['aktivitas-login', 'Aktivitas Login', 'aktivitas-login.php', 'fa-clock-rotate-left'];
}

// Fallback escape function jika functions.php belum dimuat.
if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}
?>

<aside class="sidebar d-none d-lg-flex">
    <div class="sidebar-label">Ruang kerja</div>
    <nav class="sidebar-menu">
        <?php foreach ($items as $item): ?>
            <a class="sidebar-link <?= $active_page === $item[0] ? 'active' : '' ?>" href="<?= e($item[2]) ?>">
                <i class="fa-solid <?= e($item[3]) ?>"></i>
                <span><?= e($item[1]) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-note">
        <span class="eight-petal-mini"><i class="fa-solid fa-seedling"></i></span>
        <strong>Catatan hari ini</strong>
        <p>Data yang rapi membantu setiap balita tumbuh lebih terpantau.</p>
    </div>
    <a href="logout.php" class="sidebar-logout">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
    </a>
</aside>

<div class="offcanvas offcanvas-start mobile-sidebar" tabindex="-1" id="mobileSidebar">
    <div class="offcanvas-header">
        <span class="brand-lockup">
            <span class="brand-mark-small"><img src="assets/img/logo-sidoarjo.png" alt="Lambang Kabupaten Sidoarjo" style="width: 36px; height: 36px; object-fit: contain;"></span>
            <span><strong>PENTING BALITA</strong><small>Desa Tropodo</small></span>
        </span>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <?php foreach ($items as $item): ?>
            <a class="sidebar-link <?= $active_page === $item[0] ? 'active' : '' ?>" href="<?= e($item[2]) ?>">
                <i class="fa-solid <?= e($item[3]) ?>"></i>
                <span><?= e($item[1]) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</div>
