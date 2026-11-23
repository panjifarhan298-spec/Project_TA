<?php
require_once 'includes/auth.php';
require_once 'config/koneksi.php';
require_once 'includes/functions.php';
cekRoleAdminAtauBidan();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: data-balita.php");
    exit;
}

$stmt = mysqli_prepare($koneksi, "SELECT b.*, p.nama AS nama_posyandu FROM balita b JOIN posyandu p ON p.id = b.posyandu_id WHERE b.id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$balita = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$balita) {
    header("Location: data-balita.php");
    exit;
}

$stmtR = mysqli_prepare($koneksi, "SELECT * FROM pengukuran WHERE id_balita = ? ORDER BY tanggal ASC");
mysqli_stmt_bind_param($stmtR, "i", $id);
mysqli_stmt_execute($stmtR);
$riwayat = [];
$res = mysqli_stmt_get_result($stmtR);
while ($row = mysqli_fetch_assoc($res)) $riwayat[] = $row;

$labelGrafik = array_map(fn($r) => date('d M Y', strtotime($r['tanggal'])), $riwayat);
$dataBerat = array_map(fn($r) => (float)$r['berat'], $riwayat);
$dataTinggi = array_map(fn($r) => (float)$r['tinggi'], $riwayat);

$umurBulan = hitungUmurBulan($balita['ttl']);

$pageTitle = "Detail Balita";
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<main class="container-fluid px-3 px-lg-4 py-4">

  <div class="page-heading mb-4 d-flex justify-content-between align-items-end">
    <div>
      <h4 class="fw-bold mb-0">Detail Balita</h4>
      <p class="text-muted small mb-0">Informasi lengkap dan riwayat pengukuran</p>
    </div>
    <a href="data-balita.php" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-lg-4">
      <div class="chart-card text-center h-100">
        <?php if (!empty($balita['foto']) && file_exists('assets/upload/' . $balita['foto'])): ?>
          <img src="assets/upload/<?= bersih($balita['foto']) ?>" class="foto-detail d-block mx-auto mb-3" alt="foto">
        <?php else: ?>
          <div class="foto-detail d-flex align-items-center justify-content-center bg-light mx-auto mb-3">
            <i class="fa-solid fa-baby fa-3x text-muted"></i>
          </div>
        <?php endif; ?>
        <h5 class="fw-bold mb-1"><?= bersih($balita['nama']) ?></h5>
        <span class="<?= badgeStatus($balita['status']) ?>"><?= bersih($balita['status']) ?></span>
        <div class="mt-3">
          <a href="edit-balita.php?id=<?= $balita['id'] ?>" class="btn btn-outline-blue btn-sm">
            <i class="fa-solid fa-pen"></i> Edit Data
          </a>
          <a href="tambah-pengukuran.php?id_balita=<?= $balita['id'] ?>" class="btn btn-pink-solid btn-sm">
            <i class="fa-solid fa-plus"></i> Tambah Pengukuran
          </a>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="chart-card h-100">
        <h6 class="fw-bold mb-3">Informasi Balita</h6>
        <div class="row g-3 small">
          <div class="col-6"><span class="text-muted">Kategori</span><div class="fw-semibold"><?= bersih(ucwords(str_replace("_", " ", $balita["kategori"] ?? "balita"))) ?></div></div>
          <div class="col-6"><span class="text-muted">NIK</span><div class="fw-semibold"><?= bersih($balita['nik']) ?></div></div>
          <div class="col-6"><span class="text-muted">Umur</span><div class="fw-semibold"><?= formatUmur($umurBulan) ?></div></div>
          <div class="col-6"><span class="text-muted">Jenis Kelamin</span><div class="fw-semibold"><?= $balita['jk'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></div></div>
          <div class="col-6"><span class="text-muted">Tanggal Lahir</span><div class="fw-semibold"><?= date('d-m-Y', strtotime($balita['ttl'])) ?></div></div>
          <div class="col-6"><span class="text-muted">Nama Ayah</span><div class="fw-semibold"><?= bersih($balita["nama_ayah"] ?? "-") ?></div></div>
          <div class="col-6"><span class="text-muted">Nama Ibu</span><div class="fw-semibold"><?= bersih($balita["nama_ibu"] ?? "-") ?></div></div>
          <div class="col-6"><span class="text-muted">Posyandu</span><div class="fw-semibold"><?= bersih($balita['nama_posyandu']) ?></div></div>
          <div class="col-6"><span class="text-muted">Status</span><div class="fw-semibold"><?= bersih($balita['status']) ?></div></div>
          <div class="col-6"><span class="text-muted">Berat Terakhir</span><div class="fw-semibold"><?= $balita['berat'] ?> Kg</div></div>
          <div class="col-6"><span class="text-muted">Tinggi Terakhir</span><div class="fw-semibold"><?= $balita['tinggi'] ?> Cm</div></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-lg-6">
      <div class="chart-card h-100">
        <h6 class="fw-bold mb-3"><i class="fa-solid fa-weight-scale text-pink"></i> Grafik Berat Badan</h6>
        <canvas id="chartBerat" height="220"></canvas>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="chart-card h-100">
        <h6 class="fw-bold mb-3"><i class="fa-solid fa-ruler-vertical text-pink"></i> Grafik Tinggi Badan</h6>
        <canvas id="chartTinggi" height="220"></canvas>
      </div>
    </div>
  </div>

  <div class="chart-card">
    <h6 class="fw-bold mb-3"><i class="fa-solid fa-clock-rotate-left text-pink"></i> Riwayat Pengukuran</h6>
    <div class="table-responsive">
      <table class="table table-modern align-middle mb-0">
        <thead><tr><th>Tanggal</th><th>Berat</th><th>Tinggi</th><th>Keterangan</th></tr></thead>
        <tbody>
          <?php if (empty($riwayat)): ?>
            <tr><td colspan="4" class="text-center text-muted py-3">Belum ada riwayat pengukuran</td></tr>
          <?php endif; ?>
          <?php foreach (array_reverse($riwayat) as $r): ?>
            <tr>
              <td><?= date('d-m-Y', strtotime($r['tanggal'])) ?></td>
              <td><?= $r['berat'] ?> Kg</td>
              <td><?= $r['tinggi'] ?> Cm</td>
              <td><?= bersih($r['keterangan']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</main>

<?php require_once 'includes/footer.php'; ?>

<script>
const labelGrafik = <?= json_encode($labelGrafik) ?>;
const dataBerat = <?= json_encode($dataBerat) ?>;
const dataTinggi = <?= json_encode($dataTinggi) ?>;

new Chart(document.getElementById('chartBerat'), {
  type: 'line',
  data: { labels: labelGrafik, datasets: [{ label: 'Berat (Kg)', data: dataBerat, borderColor: '#c9a7f2', backgroundColor: 'rgba(201,167,242,0.2)', tension: 0.35, fill: true }] },
  options: { responsive: true }
});
new Chart(document.getElementById('chartTinggi'), {
  type: 'line',
  data: { labels: labelGrafik, datasets: [{ label: 'Tinggi (Cm)', data: dataTinggi, borderColor: '#8ecae6', backgroundColor: 'rgba(142,202,230,0.2)', tension: 0.35, fill: true }] },
  options: { responsive: true }
});
</script>
