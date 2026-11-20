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

$cekTabel = mysqli_query($koneksi, "SHOW TABLES LIKE 'ibu_hamil'");
$tabelAda = mysqli_num_rows($cekTabel) > 0;

$daftarData = [];
if ($tabelAda) {
    $sql = "SELECT ih.*, p.nama AS nama_posyandu FROM ibu_hamil ih JOIN posyandu p ON p.id=ih.posyandu_id WHERE 1=1";
    $params = []; $types = '';
    if ($posyanduFilter > 0) { $sql .= " AND ih.posyandu_id=?"; $params[] = $posyanduFilter; $types .= 'i'; }
    if ($cari !== '') { $sql .= " AND (ih.nama LIKE ? OR ih.nik LIKE ?)"; $like = "%$cari%"; $params[] = $like; $params[] = $like; $types .= 'ss'; }
    $sql .= " ORDER BY ih.id DESC";
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
    // Fallback: tabel balita lama dengan kategori ibu_hamil
    $sql = "SELECT b.*, p.nama AS nama_posyandu, b.ttl AS tanggal_lahir, b.berat AS berat_badan, NULL AS lingkar_kepala, NULL AS lingkar_lengan FROM balita b JOIN posyandu p ON p.id=b.posyandu_id WHERE b.kategori='ibu_hamil'";
    if ($posyanduFilter > 0) $sql .= " AND b.posyandu_id=" . intval($posyanduFilter);
    if ($cari !== '') $sql .= " AND (b.nama LIKE '%" . mysqli_real_escape_string($koneksi, $cari) . "%' OR b.nik LIKE '%" . mysqli_real_escape_string($koneksi, $cari) . "%')";
    $sql .= " ORDER BY b.id DESC";
    $res = mysqli_query($koneksi, $sql);
    while ($row = mysqli_fetch_assoc($res)) $daftarData[] = $row;
}

$sukses = $_GET['sukses'] ?? '';
$pageTitle = 'Data Ibu Hamil';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>
<main class="container-fluid px-3 px-lg-4 py-4">
  <div class="page-heading mb-4 d-flex flex-wrap justify-content-between align-items-end gap-2">
    <div>
      <h4 class="fw-bold mb-0" style="color: #123B70;"><i class="fa-solid fa-person-pregnant me-2" style="color: #60A5FA;"></i>Data Ibu Hamil</h4>
      <p class="text-muted small mb-0">Pengelolaan data kehamilan, lingkar lengan (LiLA), lingkar kepala, dan pemeriksaan Posyandu</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      <a href="export-data.php?kategori=ibu_hamil&cari=<?= urlencode($cari) ?>&posyandu_id=<?= $posyanduFilter ?>" class="btn btn-outline-success btn-sm"><i class="fa-solid fa-file-excel me-1"></i> Rekap Excel</a>
      <a href="tambah-ibu-hamil.php" class="btn btn-primary-blue btn-3d"><i class="fa-solid fa-plus me-1"></i> Tambah Ibu Hamil</a>
    </div>
  </div>

  <?php if ($sukses === 'tambah'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fa-solid fa-circle-check me-2"></i>Data ibu hamil berhasil ditambahkan!
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php elseif ($sukses === 'edit'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fa-solid fa-circle-check me-2"></i>Data ibu hamil berhasil diperbarui!
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php elseif ($sukses === 'hapus'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fa-solid fa-circle-check me-2"></i>Data ibu hamil berhasil dihapus!
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Filter & Pencarian -->
  <div class="chart-card mb-3">
    <form method="GET" class="row g-2 align-items-center">
      <div class="col-md-5">
        <div class="input-group">
          <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
          <input type="text" name="cari" value="<?= bersih($cari) ?>" class="form-control" placeholder="Cari nama atau NIK ibu hamil...">
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
        <a href="data-ibu-hamil.php" class="btn btn-light w-100">Reset</a>
      </div>
    </form>
  </div>

  <!-- Tabel Data Ibu Hamil Responsif -->
  <div class="chart-card">
    <div class="table-responsive">
      <table class="table table-modern align-middle mb-0">
        <thead>
          <tr>
            <th>No</th>
            <th>ID Bumil</th>
            <th>NIK</th>
            <th>Nama Ibu Hamil</th>
            <th>Tgl Lahir</th>
            <th>Umur</th>
            <th>Alamat</th>
            <th>No. Telepon</th>
            <th>Usia Hamil</th>
            <th>HPL</th>
            <th>Berat</th>
            <th>L. Kepala</th>
            <th>L. Lengan (LiLA)</th>
            <th>Tekanan Darah</th>
            <th>Tgl Periksa</th>
            <th>Posyandu</th>
            <th class="text-center" style="min-width: 170px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($daftarData)): ?>
            <tr>
              <td colspan="17" class="text-center text-muted py-4">
                <i class="fa-solid fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                Belum ada data ibu hamil. <a href="tambah-ibu-hamil.php" class="fw-semibold" style="color: #60A5FA;">Tambah data baru</a>
              </td>
            </tr>
          <?php endif; ?>
          <?php 
          $no = 1;
          foreach ($daftarData as $b): 
            $tglLahir = $b['tanggal_lahir'] ?? $b['ttl'] ?? null;
            $umurDisplay = !empty($b['umur']) ? $b['umur'] : ($tglLahir ? (int)date_diff(date_create($tglLahir), date_create())->y : 0);
          ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><span class="badge bg-light text-dark fw-bold border">IH-<?= str_pad($b['id'], 3, '0', STR_PAD_LEFT) ?></span></td>
              <td class="fw-semibold text-nowrap"><?= bersih($b['nik']) ?></td>
              <td class="fw-bold text-nowrap" style="color: #123B70;"><?= bersih($b['nama']) ?></td>
              <td class="text-nowrap"><?= $tglLahir ? date('d-m-Y', strtotime($tglLahir)) : '-' ?></td>
              <td><?= $umurDisplay ?> thn</td>
              <td class="small" style="max-width: 150px;"><?= bersih($b['alamat'] ?? '-') ?></td>
              <td class="text-nowrap"><?= bersih($b['no_telepon'] ?? '-') ?></td>
              <td>
                <?php if (!empty($b['usia_kehamilan'])): ?>
                  <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25"><?= $b['usia_kehamilan'] ?> mgg</span>
                <?php else: ?>
                  -
                <?php endif; ?>
              </td>
              <td class="text-nowrap fw-semibold" style="color: #059669;"><?= !empty($b['hpl']) ? date('d-m-Y', strtotime($b['hpl'])) : '-' ?></td>
              <td><?= !empty($b['berat_badan'] ?? $b['berat']) ? ($b['berat_badan'] ?? $b['berat']) . ' kg' : '-' ?></td>
              <td><?= !empty($b['lingkar_kepala']) ? $b['lingkar_kepala'] . ' cm' : '-' ?></td>
              <td><?= !empty($b['lingkar_lengan']) ? $b['lingkar_lengan'] . ' cm' : '-' ?></td>
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
                <a href="detail-ibu-hamil.php?id=<?= (int)$b['id'] ?>" class="btn btn-sm btn-outline-blue" title="Lihat Detail"><i class="fa-solid fa-eye"></i></a>
                <a href="detail-ibu-hamil.php?id=<?= (int)$b['id'] ?>#form-pemeriksaan" class="btn btn-sm btn-outline-success" title="Catat Pemeriksaan"><i class="fa-solid fa-stethoscope"></i></a>
                <a href="edit-ibu-hamil.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-blue" title="Edit Data"><i class="fa-solid fa-pen"></i></a>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                  <a href="proses/hapus-ibu-hamil.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-danger btn-hapus" title="Hapus Data"><i class="fa-solid fa-trash"></i></a>
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
