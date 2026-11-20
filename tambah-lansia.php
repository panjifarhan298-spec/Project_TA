<?php
require_once 'includes/auth.php';
require_once 'config/koneksi.php';
require_once 'includes/functions.php';
cekRoleAdminAtauBidan();

$daftarPosyandu = [];
$resP = mysqli_query($koneksi, "SELECT id, nama FROM posyandu ORDER BY id ASC");
while ($row = mysqli_fetch_assoc($resP)) $daftarPosyandu[] = $row;

$error = $_GET['error'] ?? '';
$pageTitle = 'Tambah Lansia';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>
<main class="container-fluid px-3 px-lg-4 py-4">
  <div class="page-heading mb-4 d-flex justify-content-between align-items-center">
    <div>
      <h4 class="fw-bold mb-0" style="color: #123B70;"><i class="fa-solid fa-person-cane me-2" style="color: #60A5FA;"></i>Tambah Data Lansia</h4>
      <p class="text-muted small mb-0">Isi formulir data pemeriksaan lansia dan riwayat penyakit kronis</p>
    </div>
    <a href="data-lansia.php" class="btn btn-light"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
  </div>

  <?php if ($error === 'validasi'): ?>
    <div class="alert alert-danger alert-dismissible fade show">
      <i class="fa-solid fa-triangle-exclamation me-2"></i>Harap lengkapi semua kolom yang bertanda bintang (*)!
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="chart-card">
    <form action="proses/tambah-lansia.php" method="POST" class="row g-3">
      <div class="col-md-6">
        <label class="form-label small fw-semibold">Nama Lengkap Lansia <span class="text-danger">*</span></label>
        <input type="text" name="nama" class="form-control" placeholder="Contoh: Mbah Supardi" required>
      </div>
      <div class="col-md-6">
        <label class="form-label small fw-semibold">NIK (Nomor Induk Kependudukan) <span class="text-danger">*</span></label>
        <input type="text" name="nik" class="form-control" maxlength="20" placeholder="16 digit NIK" required>
      </div>

      <div class="col-md-4">
        <label class="form-label small fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
        <select name="jenis_kelamin" class="form-select" required>
          <option value="">-- Pilih Jenis Kelamin --</option>
          <option value="L">Laki-laki</option>
          <option value="P">Perempuan</option>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label small fw-semibold">Tanggal Lahir <span class="text-danger">*</span></label>
        <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" required onchange="hitungUmurOtomatis(this.value)">
      </div>
      <div class="col-md-4">
        <label class="form-label small fw-semibold">Umur (Tahun)</label>
        <input type="number" name="umur" id="umur" class="form-control" placeholder="Terhitung otomatis" min="0" max="150">
      </div>

      <div class="col-md-6">
        <label class="form-label small fw-semibold">Nomor Telepon / HP</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
          <input type="text" name="no_telepon" class="form-control" placeholder="08xxxxxxxxxx">
        </div>
      </div>
      <div class="col-md-6">
        <label class="form-label small fw-semibold">Posyandu <span class="text-danger">*</span></label>
        <select name="posyandu_id" class="form-select" required>
          <option value="">-- Pilih Posyandu --</option>
          <?php foreach ($daftarPosyandu as $p): ?>
            <option value="<?= $p['id'] ?>"><?= bersih($p['nama']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-12">
        <label class="form-label small fw-semibold">Alamat Lengkap</label>
        <textarea name="alamat" class="form-control" rows="2" placeholder="RT/RW, Dusun, Desa Tropodo"></textarea>
      </div>

      <div class="col-12"><hr class="my-1 text-muted"></div>
      <div class="col-12"><h6 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-stethoscope me-1"></i> Data Pemeriksaan Fisik & Penyakit Kronis</h6></div>

      <div class="col-md-4">
        <label class="form-label small fw-semibold">Berat Badan (Kg)</label>
        <div class="input-group">
          <input type="number" step="0.1" min="0" name="berat_badan" class="form-control" placeholder="0.0">
          <span class="input-group-text">kg</span>
        </div>
      </div>

      <!-- Ganti Tinggi Badan menjadi Penyakit Kronis -->
      <div class="col-md-4">
        <label class="form-label small fw-semibold">Penyakit Kronis</label>
        <input type="text" name="penyakit_kronis" class="form-control" placeholder="Contoh: Hipertensi, Diabetes, Asam Urat, atau Tidak Ada" value="Tidak Ada">
        <div class="form-text">Isi riwayat penyakit menahun atau kronis (jika ada)</div>
      </div>

      <div class="col-md-4">
        <label class="form-label small fw-semibold">Tekanan Darah</label>
        <input type="text" name="tekanan_darah" class="form-control" placeholder="Contoh: 120/80">
      </div>

      <div class="col-md-6">
        <label class="form-label small fw-semibold">Tanggal Pemeriksaan</label>
        <input type="date" name="tanggal_periksa" class="form-control" value="<?= date('Y-m-d') ?>">
      </div>

      <div class="col-12 d-flex gap-2 justify-content-end mt-4">
        <a href="data-lansia.php" class="btn btn-light px-4">Batal</a>
        <button type="submit" class="btn btn-primary-blue btn-3d px-4"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan Data Lansia</button>
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
