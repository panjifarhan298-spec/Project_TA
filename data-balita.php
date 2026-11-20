<?php
require_once 'includes/auth.php';
require_once 'config/koneksi.php';
require_once 'includes/functions.php';
cekRoleAdminAtauBidan();

$posyanduFilter = (int)($_GET['posyandu_id'] ?? 0);
$cari = trim($_GET['cari'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');
$daftarPosyandu = [];
$resP = mysqli_query($koneksi, "SELECT id, nama FROM posyandu ORDER BY id ASC");
while ($row = mysqli_fetch_assoc($resP)) $daftarPosyandu[] = $row;

$sql = "SELECT b.*, p.nama AS nama_posyandu FROM balita b JOIN posyandu p ON p.id=b.posyandu_id WHERE b.kategori='balita'";
$params = []; $types = '';

if ($posyanduFilter > 0) { 
    $sql .= " AND b.posyandu_id=?"; 
    $params[] = $posyanduFilter; 
    $types .= 'i'; 
}
if ($statusFilter !== '') {
    $sql .= " AND b.status=?";
    $params[] = $statusFilter;
    $types .= 's';
}
if ($cari !== '') { 
    $sql .= " AND (b.nama LIKE ? OR b.nik LIKE ? OR b.nama_ayah LIKE ? OR b.nama_ibu LIKE ?)"; 
    $like = "%$cari%"; 
    $params[] = $like; $params[] = $like; $params[] = $like; $params[] = $like; 
    $types .= 'ssss'; 
}
$sql .= " ORDER BY b.id DESC";

if ($types) {
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $daftarData = [];
    while ($row = mysqli_fetch_assoc($res)) $daftarData[] = $row;
} else {
    $res = mysqli_query($koneksi, $sql);
    $daftarData = [];
    while ($row = mysqli_fetch_assoc($res)) $daftarData[] = $row;
}

$sukses = $_GET['sukses'] ?? '';
$pageTitle = 'Data Balita';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>
<main class="container-fluid px-3 px-lg-4 py-4">
  <div class="page-heading mb-4 d-flex flex-wrap justify-content-between align-items-end gap-2">
    <div>
      <h4 class="fw-bold mb-0"><i class="fa-solid fa-child-reaching me-2" style="color: #60A5FA;"></i>Data Balita</h4>
      <p class="text-muted small mb-0">Pengelolaan data pertumbuhan dan gizi balita Posyandu Desa Tropodo</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      <a href="export-data.php?kategori=balita&cari=<?= urlencode($cari) ?>&posyandu_id=<?= $posyanduFilter ?>" class="btn btn-outline-success btn-sm"><i class="fa-solid fa-file-excel me-1"></i> Rekap Excel</a>
      <a href="tambah-balita.php" class="btn btn-pink-solid btn-3d"><i class="fa-solid fa-plus me-1"></i> Tambah Balita</a>
    </div>
  </div>

  <?php if ($sukses === 'tambah'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fa-solid fa-circle-check me-2"></i>Data balita berhasil ditambahkan!
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php elseif ($sukses === 'edit'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fa-solid fa-circle-check me-2"></i>Data balita berhasil diperbarui!
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php elseif ($sukses === 'hapus'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fa-solid fa-circle-check me-2"></i>Data balita berhasil dihapus!
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Filter & Pencarian -->
  <div class="chart-card mb-3">
    <form method="GET" class="row g-2 align-items-center">
      <div class="col-md-4">
        <div class="input-group">
          <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
          <input type="text" name="cari" value="<?= bersih($cari) ?>" class="form-control" placeholder="Cari nama, NIK, ortu...">
        </div>
      </div>
      <div class="col-md-3">
        <select name="posyandu_id" class="form-select">
          <option value="0">Semua Posyandu</option>
          <?php foreach ($daftarPosyandu as $p): ?>
            <option value="<?= $p['id'] ?>" <?= $posyanduFilter == $p['id'] ? 'selected' : '' ?>><?= bersih($p['nama']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <select name="status" class="form-select">
          <option value="">Semua Status</option>
          <option value="Normal" <?= $statusFilter === 'Normal' ? 'selected' : '' ?>>Normal</option>
          <option value="Berisiko" <?= $statusFilter === 'Berisiko' ? 'selected' : '' ?>>Berisiko</option>
          <option value="Stunting" <?= $statusFilter === 'Stunting' ? 'selected' : '' ?>>Stunting</option>
        </select>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-pink-outline w-100"><i class="fa-solid fa-filter me-1"></i> Filter</button>
      </div>
      <div class="col-md-1">
        <a href="data-balita.php" class="btn btn-light w-100" title="Reset filter"><i class="fa-solid fa-rotate-left"></i></a>
      </div>
    </form>
  </div>

  <!-- Tabel Data Balita Responsif -->
  <div class="chart-card">
    <div class="table-responsive">
      <table class="table table-modern align-middle mb-0">
        <thead>
          <tr>
            <th>No</th>
            <th>ID Balita</th>
            <th>NIK Anak</th>
            <th>Nama Balita</th>
            <th>JK</th>
            <th>Tgl Lahir</th>
            <th>Umur</th>
            <th>Nama Orang Tua</th>
            <th>Alamat</th>
            <th>Berat</th>
            <th>Tinggi</th>
            <th>Status Gizi</th>
            <th>Tgl Periksa</th>
            <th>Posyandu</th>
            <th class="text-center" style="min-width: 120px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($daftarData)): ?>
            <tr>
              <td colspan="15" class="text-center text-muted py-4">
                <i class="fa-solid fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                Belum ada data balita. <a href="tambah-balita.php" class="fw-semibold" style="color: #60A5FA;">Tambah data baru</a>
              </td>
            </tr>
          <?php endif; ?>
          <?php 
          $no = 1; 
          foreach ($daftarData as $b): 
            $umurBulan = hitungUmurBulan($b['ttl']);
            $ortu = [];
            if (!empty($b['nama_ayah'])) $ortu[] = 'A: ' . bersih($b['nama_ayah']);
            if (!empty($b['nama_ibu'])) $ortu[] = 'I: ' . bersih($b['nama_ibu']);
            $ortuStr = !empty($ortu) ? implode('<br>', $ortu) : '-';
          ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><span class="badge bg-light text-dark fw-bold border">B-<?= str_pad($b['id'], 3, '0', STR_PAD_LEFT) ?></span></td>
              <td class="fw-semibold text-nowrap"><?= bersih($b['nik']) ?></td>
              <td class="fw-bold text-nowrap" style="color: #123B70;"><?= bersih($b['nama']) ?></td>
              <td><?= $b['jk'] === 'L' ? '<span class="badge" style="background:#E0EFFE; color:#123B70;">L</span>' : '<span class="badge" style="background:#FCE7F3; color:#9D174D;">P</span>' ?></td>
              <td class="text-nowrap"><?= date('d-m-Y', strtotime($b['ttl'])) ?></td>
              <td class="text-nowrap"><?= formatUmur($umurBulan) ?></td>
              <td class="small text-nowrap"><?= $ortuStr ?></td>
              <td class="small" style="max-width: 140px;"><?= bersih($b['alamat'] ?? '-') ?></td>
              <td><?= $b['berat'] ?> kg</td>
              <td><?= $b['tinggi'] ?> cm</td>
              <td><span class="<?= badgeStatus($b['status']) ?>"><?= bersih($b['status']) ?></span></td>
              <td class="text-nowrap"><?= !empty($b['tanggal_periksa']) ? date('d-m-Y', strtotime($b['tanggal_periksa'])) : date('d-m-Y', strtotime($b['created_at'])) ?></td>
              <td class="text-nowrap"><span class="badge bg-secondary bg-opacity-10 text-dark"><?= bersih($b['nama_posyandu']) ?></span></td>
              <td class="text-center text-nowrap">
                <a href="detail-balita.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-pink" title="Lihat Detail & Grafik"><i class="fa-solid fa-eye"></i></a>
                <a href="edit-balita.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-blue" title="Edit Data"><i class="fa-solid fa-pen"></i></a>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                  <a href="proses/hapus-balita.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-danger btn-hapus" title="Hapus Data"><i class="fa-solid fa-trash"></i></a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
<?php require_once 'includes/footer.php'; ?>
