<?php
require_once 'includes/auth.php';
require_once 'config/koneksi.php';
require_once 'includes/functions.php';
cekRoleAdminAtauBidan();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: data-lansia.php');
    exit;
}

$cekTabel = mysqli_query($koneksi, "SHOW TABLES LIKE 'lansia'");
$tabelAda = mysqli_num_rows($cekTabel) > 0;

$data = null;
if ($tabelAda) {
    $stmt = mysqli_prepare($koneksi, "SELECT l.*, p.nama AS nama_posyandu FROM lansia l JOIN posyandu p ON p.id=l.posyandu_id WHERE l.id=?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
} else {
    $stmt = mysqli_prepare($koneksi, "SELECT b.*, p.nama AS nama_posyandu, b.jk AS jenis_kelamin, b.ttl AS tanggal_lahir, b.berat AS berat_badan, 'Tidak Ada' AS penyakit_kronis FROM balita b JOIN posyandu p ON p.id=b.posyandu_id WHERE b.id=? AND b.kategori='lansia'");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

if (!$data) {
    header('Location: data-lansia.php');
    exit;
}

$daftarPosyandu = [];
$resP = mysqli_query($koneksi, "SELECT id, nama FROM posyandu ORDER BY id ASC");
while ($row = mysqli_fetch_assoc($resP)) $daftarPosyandu[] = $row;

$pageTitle = 'Edit Lansia';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>
<main class="container-fluid px-3 px-lg-4 py-4">
  <div class="page-heading mb-4 d-flex justify-content-between align-items-center">
    <div>
      <h4 class="fw-bold mb-0" style="color: #123B70;"><i class="fa-solid fa-pen-to-square me-2" style="color: #60A5FA;"></i>Edit Data Lansia</h4>
      <p class="text-muted small mb-0">Perbarui data lansia: <strong><?= bersih($data['nama']) ?></strong></p>
    </div>
    <a href="data-lansia.php" class="btn btn-light"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
  </div>

  <div class="chart-card">
    <form action="proses/edit-lansia.php" method="POST" class="row g-3">
      <input type="hidden" name="id" value="<?= $data['id'] ?>">
      <input type="hidden" name="tabel_ada" value="<?= $tabelAda ? '1' : '0' ?>">

      <div class="col-md-6">
        <label class="form-label small fw-semibold">Nama Lengkap Lansia <span class="text-danger">*</span></label>
        <input type="text" name="nama" class="form-control" value="<?= bersih($data['nama']) ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label small fw-semibold">NIK (Nomor Induk Kependudukan) <span class="text-danger">*</span></label>
        <input type="text" name="nik" class="form-control" maxlength="20" value="<?= bersih($data['nik']) ?>" required>
      </div>

      <div class="col-md-4">
        <label class="form-label small fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
        <select name="jenis_kelamin" class="form-select" required>
          <option value="L" <?= ($data['jenis_kelamin'] ?? $data['jk'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
          <option value="P" <?= ($data['jenis_kelamin'] ?? $data['jk'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label small fw-semibold">Tanggal Lahir <span class="text-danger">*</span></label>
        <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" value="<?= $data['tanggal_lahir'] ?? $data['ttl'] ?? '' ?>" required onchange="hitungUmurOtomatis(this.value)">
      </div>
      <div class="col-md-4">
        <label class="form-label small fw-semibold">Umur (Tahun)</label>
        <input type="number" name="umur" id="umur" class="form-control" value="<?= $data['umur'] ?? 0 ?>" min="0" max="150">
      </div>

      <div class="col-md-6">
        <label class="form-label small fw-semibold">Nomor Telepon / HP</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
          <input type="text" name="no_telepon" class="form-control" value="<?= bersih($data['no_telepon'] ?? '') ?>">
        </div>
      </div>
      <div class="col-md-6">
        <label class="form-label small fw-semibold">Posyandu <span class="text-danger">*</span></label>
        <select name="posyandu_id" class="form-select" required>
          <?php foreach ($daftarPosyandu as $p): ?>
            <option value="<?= $p['id'] ?>" <?= $data['posyandu_id'] == $p['id'] ? 'selected' : '' ?>><?= bersih($p['nama']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-12">
        <label class="form-label small fw-semibold">Alamat Lengkap</label>
        <textarea name="alamat" class="form-control" rows="2"><?= bersih($data['alamat'] ?? '') ?></textarea>
      </div>

      <div class="col-12"><hr class="my-1 text-muted"></div>
      <div class="col-12"><h6 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-stethoscope me-1"></i> Data Pemeriksaan Fisik & Penyakit Kronis</h6></div>

      <div class="col-md-4">
        <label class="form-label small fw-semibold">Berat Badan (Kg)</label>
        <div class="input-group">
          <input type="number" step="0.1" min="0" name="berat_badan" class="form-control" value="<?= $data['berat_badan'] ?? $data['berat'] ?? 0 ?>">
          <span class="input-group-text">kg</span>
        </div>
      </div>

      <!-- Ganti Tinggi Badan menjadi Penyakit Kronis -->
      <div class="col-md-4">
        <label class="form-label small fw-semibold">Penyakit Kronis</label>
        <input type="text" name="penyakit_kronis" class="form-control" value="<?= bersih($data['penyakit_kronis'] ?? 'Tidak Ada') ?>" placeholder="Contoh: Hipertensi, Diabetes">
        <div class="form-text">Riwayat penyakit menahun atau kronis</div>
      </div>

      <div class="col-md-4">
        <label class="form-label small fw-semibold">Tekanan Darah</label>
        <input type="text" name="tekanan_darah" class="form-control" value="<?= bersih($data['tekanan_darah'] ?? '') ?>" placeholder="120/80">
      </div>

      <div class="col-md-6">
        <label class="form-label small fw-semibold">Tanggal Pemeriksaan</label>
        <input type="date" name="tanggal_periksa" class="form-control" value="<?= $data['tanggal_periksa'] ?? date('Y-m-d') ?>">
      </div>

      <div class="col-12 d-flex gap-2 justify-content-end mt-4">
        <a href="data-lansia.php" class="btn btn-light px-4">Batal</a>
        <button type="submit" class="btn btn-primary-blue btn-3d px-4"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan</button>
      </div>
    </form>
  </div>
</main>

<script>
function hitungUmurOtomatis(tgl) {
  if (!tgl) return;
  const lahir = new Date(tgl);
  const now = new Date();
  let age = now.getFullYear() - lahir.getFullYear();
  const m = now.getMonth() - lahir.getMonth();
  if (m < 0 || (m === 0 && now.getDate() < lahir.getDate())) {
    age--;
  }
  document.getElementById('umur').value = Math.max(0, age);
}
</script>

<?php require_once 'includes/footer.php'; ?>
