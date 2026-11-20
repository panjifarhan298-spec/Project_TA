<?php
require_once 'includes/auth.php';
require_once 'config/koneksi.php';
require_once 'includes/functions.php';
cekRoleAdminAtauBidan();

$totalBalita = (int)mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM balita"))['total'];
$totalNormal = (int)mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM balita WHERE status='Normal'"))['total'];
$totalBerisiko = (int)mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM balita WHERE status='Berisiko'"))['total'];
$totalStunting = (int)mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM balita WHERE status='Stunting'"))['total'];
$totalPengukuran = (int)mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pengukuran"))['total'];

$rekapPosyandu = [];
$resRekap = mysqli_query($koneksi, "SELECT p.nama, COUNT(b.id) AS total,
    SUM(CASE WHEN b.status='Normal' THEN 1 ELSE 0 END) AS normal,
    SUM(CASE WHEN b.status='Berisiko' THEN 1 ELSE 0 END) AS berisiko,
    SUM(CASE WHEN b.status='Stunting' THEN 1 ELSE 0 END) AS stunting
    FROM posyandu p LEFT JOIN balita b ON b.posyandu_id = p.id
    GROUP BY p.id ORDER BY p.id ASC");
while ($row = mysqli_fetch_assoc($resRekap)) $rekapPosyandu[] = $row;

$pengukuranTerbaru = [];
$resTerbaru = mysqli_query($koneksi, "SELECT pk.tanggal, COUNT(*) AS jumlah FROM pengukuran pk GROUP BY pk.tanggal ORDER BY pk.tanggal DESC LIMIT 6");
while ($row = mysqli_fetch_assoc($resTerbaru)) $pengukuranTerbaru[] = $row;

$pageTitle = "Laporan";
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<main class="container-fluid px-3 px-lg-4 py-4">
  <div class="page-heading mb-4 d-flex flex-wrap justify-content-between align-items-end gap-2">
    <div><h4 class="fw-bold mb-1">Laporan Posyandu</h4><p class="text-muted small mb-0">Ringkasan data balita, status gizi, dan kegiatan pengukuran.</p></div>
    <button type="button" class="btn btn-outline-pink btn-sm" onclick="window.print()"><i class="fa-solid fa-print me-1"></i> Cetak Laporan</button>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-6 col-lg-3"><div class="stat-card stat-total h-100"><div class="stat-icon"><i class="fa-solid fa-child-reaching"></i></div><div class="stat-number"><?= $totalBalita ?></div><div class="stat-label">Total Balita</div></div></div>
    <div class="col-6 col-lg-3"><div class="stat-card stat-normal h-100"><div class="stat-icon"><i class="fa-solid fa-face-smile"></i></div><div class="stat-number"><?= $totalNormal ?></div><div class="stat-label">Status Normal</div></div></div>
    <div class="col-6 col-lg-3"><div class="stat-card stat-berisiko h-100"><div class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></div><div class="stat-number"><?= $totalBerisiko ?></div><div class="stat-label">Berisiko</div></div></div>
    <div class="col-6 col-lg-3"><div class="stat-card stat-stunting h-100"><div class="stat-icon"><i class="fa-solid fa-circle-exclamation"></i></div><div class="stat-number"><?= $totalStunting ?></div><div class="stat-label">Stunting</div></div></div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-lg-8"><div class="chart-card h-100"><div class="d-flex justify-content-between align-items-center mb-3"><h6 class="fw-bold mb-0"><i class="fa-solid fa-table-list text-pink me-1"></i> Rekap per Posyandu</h6><span class="small text-muted">Total pengukuran: <?= $totalPengukuran ?></span></div><div class="table-responsive"><table class="table table-modern align-middle mb-0"><thead><tr><th>Posyandu</th><th>Total</th><th>Normal</th><th>Berisiko</th><th>Stunting</th></tr></thead><tbody><?php foreach ($rekapPosyandu as $row): ?><tr><td class="fw-semibold"><?= bersih($row['nama']) ?></td><td><?= (int)$row['total'] ?></td><td><span class="badge-status badge-normal"><?= (int)$row['normal'] ?></span></td><td><span class="badge-status badge-berisiko"><?= (int)$row['berisiko'] ?></span></td><td><span class="badge-status badge-stunting"><?= (int)$row['stunting'] ?></span></td></tr><?php endforeach; ?></tbody></table></div></div></div>
    <div class="col-lg-4"><div class="chart-card h-100"><h6 class="fw-bold mb-3"><i class="fa-solid fa-clock-rotate-left text-pink me-1"></i> Pengukuran Terbaru</h6><?php if (!$pengukuranTerbaru): ?><p class="small text-muted mb-0">Belum ada data pengukuran.</p><?php else: ?><div class="list-group list-group-flush"><?php foreach ($pengukuranTerbaru as $row): ?><div class="list-group-item px-0 d-flex justify-content-between"><span class="small"><?= date('d M Y', strtotime($row['tanggal'])) ?></span><span class="badge-status badge-normal"><?= (int)$row['jumlah'] ?> data</span></div><?php endforeach; ?></div><?php endif; ?></div></div>
  </div>

  <div class="alert alert-info small mb-0"><i class="fa-solid fa-shield-halved me-1"></i> Laporan ini menampilkan data agregat untuk kebutuhan pemantauan pelayanan. Simpan dan bagikan laporan sesuai kewenangan petugas.</div>
</main>

<?php require_once 'includes/footer.php'; ?>
