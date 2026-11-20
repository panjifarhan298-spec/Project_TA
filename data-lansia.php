<?php
require_once 'includes/auth.php';
require_once 'config/koneksi.php';
require_once 'includes/functions.php';
cekRoleAdminAtauBidan();

$posyanduFilter = (int)($_GET['posyandu_id'] ?? 0);
$cari = trim($_GET['cari'] ?? '');
$daftarPosyandu = [];
$resP = mysqli_query($koneksi, "SELECT id, nama FROM posyandu ORDER BY id ASC");
while ($row = mysqli_fetch_assoc($resP)) $daftarPosyandu[] = $row;

// Cek apakah tabel lansia sudah ada
$cekTabel = mysqli_query($koneksi, "SHOW TABLES LIKE 'lansia'");
$tabelAda = mysqli_num_rows($cekTabel) > 0;

$daftarData = [];
if ($tabelAda) {
    $sql = "SELECT l.*, p.nama AS nama_posyandu FROM lansia l JOIN posyandu p ON p.id=l.posyandu_id WHERE 1=1";
    $params = []; $types = '';
    if ($posyanduFilter > 0) { $sql .= " AND l.posyandu_id=?"; $params[] = $posyanduFilter; $types .= 'i'; }
    if ($cari !== '') { $sql .= " AND (l.nama LIKE ? OR l.nik LIKE ? OR l.penyakit_kronis LIKE ?)"; $like = "%$cari%"; $params[] = $like; $params[] = $like; $params[] = $like; $types .= 'sss'; }
    $sql .= " ORDER BY l.id DESC";
    if ($types) {
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($res)) $daftarData[] = $row;
    } else {
        $res = mysqli_query($koneksi, $sql);
        while ($row = mysqli_fetch_assoc($res)) $daftarData[] = $row;
    }
} else {
    // Fallback: tabel balita lama dengan kategori lansia
    $sql = "SELECT b.*, p.nama AS nama_posyandu, b.jk AS jenis_kelamin, b.ttl AS tanggal_lahir, b.berat AS berat_badan, 'Tidak Ada' AS penyakit_kronis FROM balita b JOIN posyandu p ON p.id=b.posyandu_id WHERE b.kategori='lansia'";
    if ($posyanduFilter > 0) $sql .= " AND b.posyandu_id=" . intval($posyanduFilter);
    if ($cari !== '') $sql .= " AND (b.nama LIKE '%" . mysqli_real_escape_string($koneksi, $cari) . "%' OR b.nik LIKE '%" . mysqli_real_escape_string($koneksi, $cari) . "%')";
    $sql .= " ORDER BY b.id DESC";
    $res = mysqli_query($koneksi, $sql);
    while ($row = mysqli_fetch_assoc($res)) $daftarData[] = $row;
}

$sukses = $_GET['sukses'] ?? '';
$pageTitle = 'Data Lansia';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>
<main class="container-fluid px-3 px-lg-4 py-4">
  <div class="page-heading mb-4 d-flex flex-wrap justify-content-between align-items-end gap-2">
    <div>
      <h4 class="fw-bold mb-0" style="color: #123B70;"><i class="fa-solid fa-person-cane me-2" style="color: #60A5FA;"></i>Data Lansia</h4>
      <p class="text-muted small mb-0">Pengelolaan data kesehatan dan penyakit kronis lansia Posyandu Desa Tropodo</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      <a href="export-data.php?kategori=lansia&cari=<?= urlencode($cari) ?>&posyandu_id=<?= $posyanduFilter ?>" class="btn btn-outline-success btn-sm"><i class="fa-solid fa-file-excel me-1"></i> Rekap Excel</a>
      <a href="tambah-lansia.php" class="btn btn-primary-blue btn-3d"><i class="fa-solid fa-plus me-1"></i> Tambah Lansia</a>
    </div>
  </div>

  <?php if ($sukses === 'tambah'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fa-solid fa-circle-check me-2"></i>Data lansia berhasil ditambahkan!
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php elseif ($sukses === 'edit'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fa-solid fa-circle-check me-2"></i>Data lansia berhasil diperbarui!
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php elseif ($sukses === 'hapus'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fa-solid fa-circle-check me-2"></i>Data lansia berhasil dihapus!
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Filter & Pencarian -->
  <div class="chart-card mb-3">
    <form method="GET" class="row g-2 align-items-center">
      <div class="col-md-5">
        <div class="input-group">
          <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
          <input type="text" name="cari" value="<?= bersih($cari) ?>" class="form-control" placeholder="Cari nama, NIK, atau penyakit kronis...">
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
        <button type="submit" class="btn btn-outline-blue w-100"><i class="fa-solid fa-filter me-1"></i> Filter</button>
      </div>
      <div class="col-md-2">
        <a href="data-lansia.php" class="btn btn-light w-100">Reset</a>
      </div>
    </form>
  </div>

  <!-- Tabel Data Lansia Responsif -->
  <div class="chart-card">
    <div class="table-responsive">
      <table class="table table-modern align-middle mb-0">
        <thead>
          <tr>
            <th>No</th>
            <th>ID Lansia</th>
            <th>NIK</th>
            <th>Nama Lansia</th>
            <th>JK</th>
            <th>Tgl Lahir</th>
            <th>Umur</th>
            <th>Alamat</th>
            <th>No. Telepon</th>
            <th>Berat</th>
            <th>Penyakit Kronis</th>
            <th>Tekanan Darah</th>
            <th>Tgl Periksa</th>
            <th>Posyandu</th>
            <th class="text-center" style="min-width: 170px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($daftarData)): ?>
            <tr>
              <td colspan="15" class="text-center text-muted py-4">
                <i class="fa-solid fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                Belum ada data lansia. <a href="tambah-lansia.php" class="fw-semibold" style="color: #60A5FA;">Tambah data baru</a>
              </td>
            </tr>
          <?php endif; ?>
          <?php 
          $no = 1;
          foreach ($daftarData as $b): 
            $tglLahir = $b['tanggal_lahir'] ?? $b['ttl'] ?? null;
            $umurDisplay = !empty($b['umur']) ? $b['umur'] : ($tglLahir ? (int)date_diff(date_create($tglLahir), date_create())->y : 0);
            $penyakit = !empty($b['penyakit_kronis']) ? $b['penyakit_kronis'] : 'Tidak Ada';
          ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><span class="badge bg-light text-dark fw-bold border">L-<?= str_pad($b['id'], 3, '0', STR_PAD_LEFT) ?></span></td>
              <td class="fw-semibold text-nowrap"><?= bersih($b['nik']) ?></td>
              <td class="fw-bold text-nowrap" style="color: #123B70;"><?= bersih($b['nama']) ?></td>
              <td><?= ($b['jenis_kelamin'] ?? $b['jk'] ?? '') === 'L' ? '<span class="badge" style="background:#E0EFFE; color:#123B70;">Laki-laki</span>' : '<span class="badge" style="background:#FCE7F3; color:#9D174D;">Perempuan</span>' ?></td>
              <td class="text-nowrap"><?= $tglLahir ? date('d-m-Y', strtotime($tglLahir)) : '-' ?></td>
              <td><?= $umurDisplay ?> thn</td>
              <td class="small" style="max-width: 160px;"><?= bersih($b['alamat'] ?? '-') ?></td>
              <td class="text-nowrap"><?= bersih($b['no_telepon'] ?? '-') ?></td>
              <td><?= !empty($b['berat_badan'] ?? $b['berat']) ? ($b['berat_badan'] ?? $b['berat']) . ' kg' : '-' ?></td>
              <td>
                <?php if ($penyakit !== 'Tidak Ada' && $penyakit !== '-'): ?>
                  <span class="badge bg-warning bg-opacity-25 text-dark border border-warning"><i class="fa-solid fa-notes-medical me-1 text-danger"></i><?= bersih($penyakit) ?></span>
                <?php else: ?>
                  <span class="badge bg-light text-muted border">Tidak Ada</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if (!empty($b['tekanan_darah'])): ?>
                  <span class="badge bg-info bg-opacity-10 text-primary border border-info border-opacity-25"><?= bersih($b['tekanan_darah']) ?></span>
                <?php else: ?>
                  -
                <?php endif; ?>
              </td>
              <td class="text-nowrap"><?= !empty($b['tanggal_periksa']) ? date('d-m-Y', strtotime($b['tanggal_periksa'])) : '-' ?></td>
              <td class="text-nowrap"><span class="badge bg-secondary bg-opacity-10 text-dark"><?= bersih($b['nama_posyandu']) ?></span></td>
              <td class="text-center text-nowrap">
                <a href="detail-lansia.php?id=<?= (int)$b['id'] ?>" class="btn btn-sm btn-outline-blue" title="Lihat Detail"><i class="fa-solid fa-eye"></i></a>
                <a href="detail-lansia.php?id=<?= (int)$b['id'] ?>#form-pemeriksaan" class="btn btn-sm btn-outline-success" title="Catat Pemeriksaan"><i class="fa-solid fa-stethoscope"></i></a>
                <a href="edit-lansia.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-blue" title="Edit Data"><i class="fa-solid fa-pen"></i></a>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                  <a href="proses/hapus-lansia.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-danger btn-hapus" title="Hapus Data"><i class="fa-solid fa-trash"></i></a>
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
