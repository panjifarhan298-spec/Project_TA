<?php
require_once 'includes/auth.php';
require_once 'config/koneksi.php';
require_once 'includes/functions.php';
cekRoleAdminAtauBidan();

$cari = trim($_GET['cari'] ?? '');

$sql = "SELECT pk.*, b.nama AS nama_balita, p.nama AS nama_posyandu
        FROM pengukuran pk
        JOIN balita b ON b.id = pk.id_balita
        JOIN posyandu p ON p.id = b.posyandu_id";
$params = []; $types = "";
if ($cari !== '') {
    $sql .= " WHERE b.nama LIKE ?";
    $params[] = "%$cari%";
    $types .= "s";
}
$sql .= " ORDER BY pk.tanggal DESC, pk.created_at DESC";

$stmt = mysqli_prepare($koneksi, $sql);
if (!empty($params)) mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$riwayat = [];
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) $riwayat[] = $row;

$pageTitle = "Riwayat Pengukuran";
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<main class="container-fluid px-3 px-lg-4 py-4 measurement-history-page">
  <div class="page-heading mb-4 d-flex flex-wrap justify-content-between align-items-end gap-2">
    <div>
      <h4 class="fw-bold mb-0">Riwayat Pengukuran</h4>
      <p class="text-muted small mb-0">Seluruh riwayat pengukuran balita</p>
    </div>
    <a href="tambah-pengukuran.php" class="btn btn-pink-solid">
      <i class="fa-solid fa-plus"></i> Tambah Pengukuran
    </a>
  </div>

  <div class="chart-card mb-3">
    <form method="GET" class="row g-2">
      <div class="col-md-4">
        <input type="text" name="cari" value="<?= bersih($cari) ?>" class="form-control" placeholder="Cari nama balita...">
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-pink-outline w-100"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
      </div>
    </form>
  </div>

  <div class="chart-card">
    <div class="table-responsive">
      <table class="table table-modern align-middle mb-0">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Nama Balita</th>
            <th>Posyandu</th>
            <th>Berat</th>
            <th>Tinggi</th>
            <th>Keterangan</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($riwayat)): ?>
            <tr><td colspan="7" class="text-center text-muted py-4">Belum ada riwayat pengukuran</td></tr>
          <?php endif; ?>
          <?php foreach ($riwayat as $r): ?>
            <tr>
              <td><?= date('d-m-Y', strtotime($r['tanggal'])) ?></td>
              <td class="fw-semibold"><?= bersih($r['nama_balita']) ?></td>
              <td><?= bersih($r['nama_posyandu']) ?></td>
              <td><?= $r['berat'] ?> Kg</td>
              <td><?= $r['tinggi'] ?> Cm</td>
              <td><?= bersih($r['keterangan']) ?></td>
              <td><a href="detail-balita.php?id=<?= $r['id_balita'] ?>" class="btn btn-sm btn-outline-pink"><i class="fa-solid fa-eye"></i></a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<?php require_once 'includes/footer.php'; ?>
