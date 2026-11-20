<?php
require_once 'includes/auth.php';
require_once 'config/koneksi.php';
require_once 'includes/functions.php';
cekRoleAdminAtauBidan();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: data-ibu-hamil.php');
    exit;
}

$cekTabel = mysqli_query($koneksi, "SHOW TABLES LIKE 'ibu_hamil'");
$tabelAda = $cekTabel && mysqli_num_rows($cekTabel) > 0;
$data = null;
if ($tabelAda) {
    $stmt = mysqli_prepare($koneksi, "SELECT ih.*, p.nama AS nama_posyandu FROM ibu_hamil ih JOIN posyandu p ON p.id=ih.posyandu_id WHERE ih.id=?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
} else {
    $stmt = mysqli_prepare($koneksi, "SELECT b.*, p.nama AS nama_posyandu, b.ttl AS tanggal_lahir, b.berat AS berat_badan, NULL AS lingkar_kepala, NULL AS lingkar_lengan FROM balita b JOIN posyandu p ON p.id=b.posyandu_id WHERE b.id=? AND b.kategori='ibu_hamil'");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

if (!$data) {
    header('Location: data-ibu-hamil.php');
    exit;
}

$riwayatTabelAda = false;
$riwayat = [];
if ($tabelAda) {
    $cekRiwayat = mysqli_query($koneksi, "SHOW TABLES LIKE 'pemeriksaan_ibu_hamil'");
    $riwayatTabelAda = $cekRiwayat && mysqli_num_rows($cekRiwayat) > 0;
    if ($riwayatTabelAda) {
        $stmtRiwayat = mysqli_prepare($koneksi, "SELECT * FROM pemeriksaan_ibu_hamil WHERE ibu_hamil_id=? ORDER BY tanggal_periksa DESC, id DESC");
        mysqli_stmt_bind_param($stmtRiwayat, 'i', $id);
        mysqli_stmt_execute($stmtRiwayat);
        $hasilRiwayat = mysqli_stmt_get_result($stmtRiwayat);
        while ($row = mysqli_fetch_assoc($hasilRiwayat)) $riwayat[] = $row;
    }
}

$tglLahir = $data['tanggal_lahir'] ?? $data['ttl'] ?? null;
$umur = (int)($data['umur'] ?? 0);
if ($umur <= 0 && $tglLahir) $umur = (int)date_diff(date_create($tglLahir), date_create())->y;
$beratTerakhir = $data['berat_badan'] ?? $data['berat'] ?? null;
$pageTitle = 'Detail Ibu Hamil';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>
<main class="container-fluid px-3 px-lg-4 py-4">
  <div class="page-heading mb-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
      <h4 class="fw-bold mb-0" style="color:#123B70;"><i class="fa-solid fa-person-pregnant me-2" style="color:#60A5FA;"></i>Detail Ibu Hamil</h4>
      <p class="text-muted small mb-0">Informasi lengkap dan riwayat pemeriksaan</p>
    </div>
    <a href="data-ibu-hamil.php" class="btn btn-light"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
  </div>

  <?php if (($_GET['sukses'] ?? '') === 'pemeriksaan'): ?>
    <div class="alert alert-success">Pemeriksaan Ibu Hamil berhasil disimpan.</div>
  <?php elseif (($_GET['error'] ?? '') === 'validasi'): ?>
    <div class="alert alert-warning">Periksa kembali tanggal dan nilai pengukuran yang diisi.</div>
  <?php elseif (($_GET['error'] ?? '') === 'simpan'): ?>
    <div class="alert alert-danger">Pemeriksaan gagal disimpan. Silakan coba kembali.</div>
  <?php endif; ?>

  <?php if (!$riwayatTabelAda): ?>
    <div class="alert alert-warning">Fitur riwayat pemeriksaan belum tersedia. Jalankan migrasi database V2.1 terlebih dahulu.</div>
  <?php endif; ?>

  <div class="row g-3 mb-4">
    <div class="col-lg-4">
      <div class="chart-card h-100">
        <h5 class="fw-bold mb-1"><?= bersih($data['nama']) ?></h5>
        <p class="text-muted small mb-3">ID Bumil: IH-<?= str_pad((string)$data['id'], 3, '0', STR_PAD_LEFT) ?></p>
        <div class="d-flex flex-wrap gap-2">
          <a href="edit-ibu-hamil.php?id=<?= (int)$data['id'] ?>" class="btn btn-outline-blue btn-sm"><i class="fa-solid fa-pen me-1"></i>Edit Data</a>
          <?php if ($riwayatTabelAda): ?>
            <a href="#form-pemeriksaan" class="btn btn-primary-blue btn-sm"><i class="fa-solid fa-stethoscope me-1"></i>Catat Pemeriksaan</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div class="col-lg-8">
      <div class="chart-card h-100">
        <h6 class="fw-bold mb-3">Informasi Ibu Hamil</h6>
        <div class="row g-3 small">
          <div class="col-sm-6"><span class="text-muted">NIK</span><div class="fw-semibold"><?= bersih($data['nik']) ?></div></div>
          <div class="col-sm-6"><span class="text-muted">Tanggal Lahir / Umur</span><div class="fw-semibold"><?= $tglLahir ? date('d-m-Y', strtotime($tglLahir)) : '-' ?> / <?= $umur ?> tahun</div></div>
          <div class="col-sm-6"><span class="text-muted">Alamat</span><div class="fw-semibold"><?= bersih($data['alamat'] ?? '-') ?></div></div>
          <div class="col-sm-6"><span class="text-muted">No. Telepon</span><div class="fw-semibold"><?= bersih($data['no_telepon'] ?? '-') ?></div></div>
          <div class="col-sm-6"><span class="text-muted">Posyandu</span><div class="fw-semibold"><?= bersih($data['nama_posyandu']) ?></div></div>
          <div class="col-sm-6"><span class="text-muted">HPL</span><div class="fw-semibold"><?= !empty($data['hpl']) ? date('d-m-Y', strtotime($data['hpl'])) : '-' ?></div></div>
          <div class="col-sm-6"><span class="text-muted">Usia Kehamilan Terakhir</span><div class="fw-semibold"><?= !empty($data['usia_kehamilan']) ? (int)$data['usia_kehamilan'] . ' minggu' : '-' ?></div></div>
          <div class="col-sm-6"><span class="text-muted">Berat Badan Terakhir</span><div class="fw-semibold"><?= $beratTerakhir !== null && $beratTerakhir !== '' ? bersih($beratTerakhir) . ' kg' : '-' ?></div></div>
          <div class="col-sm-6"><span class="text-muted">Lingkar Kepala Terakhir</span><div class="fw-semibold"><?= !empty($data['lingkar_kepala']) ? bersih($data['lingkar_kepala']) . ' cm' : '-' ?></div></div>
          <div class="col-sm-6"><span class="text-muted">LiLA Terakhir</span><div class="fw-semibold"><?= !empty($data['lingkar_lengan']) ? bersih($data['lingkar_lengan']) . ' cm' : '-' ?></div></div>
          <div class="col-sm-6"><span class="text-muted">Tekanan Darah Terakhir</span><div class="fw-semibold"><?= bersih($data['tekanan_darah'] ?? '-') ?></div></div>
          <div class="col-sm-6"><span class="text-muted">Tanggal Periksa Terakhir</span><div class="fw-semibold"><?= !empty($data['tanggal_periksa']) ? date('d-m-Y', strtotime($data['tanggal_periksa'])) : '-' ?></div></div>
        </div>
      </div>
    </div>
  </div>

  <?php if ($riwayatTabelAda): ?>
    <div class="chart-card mb-4" id="form-pemeriksaan">
      <h6 class="fw-bold mb-3"><i class="fa-solid fa-stethoscope me-2 text-primary"></i>Catat Pemeriksaan Baru</h6>
      <form action="proses/tambah-pemeriksaan-ibu-hamil.php" method="POST" class="row g-3">
        <input type="hidden" name="id" value="<?= (int)$data['id'] ?>">
        <div class="col-md-4">
          <label class="form-label small fw-semibold">Tanggal Pemeriksaan</label>
          <input type="date" name="tanggal_periksa" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-semibold">Usia Kehamilan (minggu)</label>
          <input type="number" name="usia_kehamilan" class="form-control" min="1" max="45">
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-semibold">Berat Badan (kg)</label>
          <input type="number" name="berat_badan" class="form-control" min="0.1" step="0.1">
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-semibold">Lingkar Kepala (cm)</label>
          <input type="number" name="lingkar_kepala" class="form-control" min="0.1" step="0.1">
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-semibold">Lingkar Lengan / LiLA (cm)</label>
          <input type="number" name="lingkar_lengan" class="form-control" min="0.1" step="0.1">
        </div>
        <div class="col-md-4">
          <label class="form-label small fw-semibold">Tekanan Darah</label>
          <input type="text" name="tekanan_darah" class="form-control" maxlength="20" placeholder="110/80">
        </div>
        <div class="col-12 text-end">
          <button type="submit" class="btn btn-primary-blue"><i class="fa-solid fa-floppy-disk me-1"></i>Simpan Pemeriksaan</button>
        </div>
      </form>
    </div>

    <div class="chart-card">
      <h6 class="fw-bold mb-3"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Riwayat Pemeriksaan</h6>
      <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
          <thead><tr><th>Tanggal</th><th>Usia Kehamilan</th><th>Berat</th><th>Lingkar Kepala</th><th>LiLA</th><th>Tekanan Darah</th></tr></thead>
          <tbody>
            <?php if (empty($riwayat)): ?>
              <tr><td colspan="6" class="text-center text-muted py-3">Belum ada riwayat pemeriksaan.</td></tr>
            <?php endif; ?>
            <?php foreach ($riwayat as $r): ?>
              <tr>
                <td><?= date('d-m-Y', strtotime($r['tanggal_periksa'])) ?></td>
                <td><?= $r['usia_kehamilan'] !== null ? (int)$r['usia_kehamilan'] . ' minggu' : '-' ?></td>
                <td><?= $r['berat_badan'] !== null ? bersih($r['berat_badan']) . ' kg' : '-' ?></td>
                <td><?= $r['lingkar_kepala'] !== null ? bersih($r['lingkar_kepala']) . ' cm' : '-' ?></td>
                <td><?= $r['lingkar_lengan'] !== null ? bersih($r['lingkar_lengan']) . ' cm' : '-' ?></td>
                <td><?= bersih($r['tekanan_darah'] ?? '-') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>
</main>
<?php require_once 'includes/footer.php'; ?>
