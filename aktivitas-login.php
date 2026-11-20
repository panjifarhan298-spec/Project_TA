<?php
require_once 'includes/auth.php';
require_once 'config/koneksi.php';
require_once 'includes/functions.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Akses ditolak. Halaman ini hanya dapat diakses oleh admin.');
}
$aktivitas = [];
$tabelSiap = false;
$cekTabel = mysqli_query($koneksi, "SHOW TABLES LIKE 'aktivitas_login'");
$tabelSiap = $cekTabel && mysqli_num_rows($cekTabel) > 0;
if ($tabelSiap) {
    $res = mysqli_query($koneksi, 'SELECT id, nama, username, role, ip_address, user_agent, login_at FROM aktivitas_login ORDER BY login_at DESC LIMIT 200');
    while ($row = mysqli_fetch_assoc($res)) $aktivitas[] = $row;
}
$pageTitle = 'Aktivitas Login';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>
<main class="container-fluid px-3 px-lg-4 py-4">
  <div class="page-heading mb-4">
    <h4 class="fw-bold mb-0">Aktivitas Login</h4>
    <p class="text-muted small mb-0">Riwayat pengguna yang berhasil masuk ke sistem. Halaman ini khusus admin.</p>
  </div>
  <?php if (!$tabelSiap): ?>
    <div class="alert alert-warning"><i class="fa-solid fa-triangle-exclamation me-2"></i>Tabel aktivitas login belum tersedia. Import file <strong>database/20261112_aktivitas_login.sql</strong> terlebih dahulu.</div>
  <?php else: ?>
    <div class="chart-card">
      <div class="d-flex justify-content-between align-items-center mb-3"><h6 class="fw-bold mb-0"><i class="fa-solid fa-shield-halved text-pink me-2"></i>Log Login Terbaru</h6><span class="section-badge">Maks. 200 aktivitas</span></div>
      <div class="table-responsive"><table class="table table-modern align-middle mb-0"><thead><tr><th>Waktu Login</th><th>Nama</th><th>Username</th><th>Role</th><th>IP Address</th><th>Perangkat/Browser</th></tr></thead><tbody>
      <?php if (!$aktivitas): ?><tr><td colspan="6" class="text-center text-muted py-4">Belum ada aktivitas login yang tercatat.</td></tr><?php endif; ?>
      <?php foreach ($aktivitas as $a): ?><tr><td><?= date('d-m-Y H:i:s', strtotime($a['login_at'])) ?></td><td class="fw-semibold"><?= bersih($a['nama'] ?: '-') ?></td><td><?= bersih($a['username'] ?: '-') ?></td><td><span class="badge-status <?= $a['role'] === 'admin' ? 'badge-stunting' : 'badge-normal' ?>"><?= bersih(ucfirst($a['role'] ?: '-')) ?></span></td><td><code><?= bersih($a['ip_address'] ?: '-') ?></code></td><td class="small text-muted" style="min-width:260px"><?= bersih($a['user_agent'] ?: '-') ?></td></tr><?php endforeach; ?>
      </tbody></table></div>
    </div>
  <?php endif; ?>
</main>
<?php require_once 'includes/footer.php'; ?>
