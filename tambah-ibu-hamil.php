<?php
require_once 'includes/auth.php';
require_once 'config/koneksi.php';
require_once 'includes/functions.php';
cekRoleAdminAtauBidan();

$daftarPosyandu = [];
$resP = mysqli_query($koneksi, "SELECT id, nama FROM posyandu ORDER BY id ASC");
while ($row = mysqli_fetch_assoc($resP)) $daftarPosyandu[] = $row;

$error = $_GET['error'] ?? '';
$pageTitle = 'Tambah Ibu Hamil';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>
<main class="container-fluid px-3 px-lg-4 py-4">
  <div class="page-heading mb-4 d-flex justify-content-between align-items-center">
    <div>
      <h4 class="fw-bold mb-0" style="color: #123B70;"><i class="fa-solid fa-person-pregnant me-2" style="color: #60A5FA;"></i>Tambah Data Ibu Hamil</h4>
      <p class="text-muted small mb-0">Isi formulir data kehamilan, lingkar kepala, dan lingkar lengan (LiLA) Posyandu</p>
    </div>
    <a href="data-ibu-hamil.php" class="btn btn-light"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
  </div>

  <?php if ($error === 'validasi'): ?>
    <div class="alert alert-danger alert-dismissible fade show">
      <i class="fa-solid fa-triangle-exclamation me-2"></i>Harap lengkapi semua kolom yang bertanda bintang (*)!
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="chart-card">
    <form action="proses/tambah-ibu-hamil.php" method="POST" class="row g-3">
      <div class="col-md-6">
        <label class="form-label small fw-semibold">Nama Lengkap Ibu Hamil <span class="text-danger">*</span></label>
        <input type="text" name="nama" class="form-control" placeholder="Contoh: Siti Aisyah" required>
      </div>
      <div class="col-md-6">
        <label class="form-label small fw-semibold">NIK (Nomor Induk Kependudukan) <span class="text-danger">*</span></label>
        <input type="text" name="nik" class="form-control" maxlength="20" placeholder="16 digit NIK" required>
      </div>

      <div class="col-md-4">
        <label class="form-label small fw-semibold">Tanggal Lahir <span class="text-danger">*</span></label>
        <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" required onchange="hitungUmurOtomatis(this.value)">
      </div>
      <div class="col-md-4">
        <label class="form-label small fw-semibold">Umur (Tahun)</label>
        <input type="number" name="umur" id="umur" class="form-control" placeholder="Terhitung otomatis" min="10" max="80">
      </div>
      <div class="col-md-4">
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
      <div class="col-md-6">
        <label class="form-label small fw-semibold">Alamat Lengkap</label>
        <input type="text" name="alamat" class="form-control" placeholder="RT/RW, Dusun, Desa Tropodo">
      </div>

      <div class="col-12"><hr class="my-1 text-muted"></div>
      <div class="col-12"><h6 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-person-breastfeeding me-1"></i> Data Kehamilan & Pengukuran Fisik</h6></div>

      <div class="col-md-3">
        <label class="form-label small fw-semibold">Usia Kehamilan (Minggu)</label>
        <div class="input-group">
          <input type="number" min="1" max="45" name="usia_kehamilan" class="form-control" placeholder="Contoh: 16">
          <span class="input-group-text">minggu</span>
        </div>
      </div>
      <div class="col-md-3">
        <label class="form-label small fw-semibold">HPL (Hari Perkiraan Lahir)</label>
        <input type="date" name="hpl" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label small fw-semibold">Berat Badan (Kg)</label>
        <div class="input-group">
          <input type="number" step="0.1" min="0" name="berat_badan" class="form-control" placeholder="0.0">
          <span class="input-group-text">kg</span>
        </div>
      </div>
      <div class="col-md-3">
        <label class="form-label small fw-semibold">Tekanan Darah</label>
        <input type="text" name="tekanan_darah" class="form-control" placeholder="Contoh: 110/80">
      </div>

      <!-- Tambahan Lingkar Kepala dan Lingkar Lengan -->
      <div class="col-md-4">
        <label class="form-label small fw-semibold">Lingkar Kepala (Cm)</label>
        <div class="input-group">
          <input type="number" step="0.1" min="0" name="lingkar_kepala" class="form-control" placeholder="Contoh: 54.0">
          <span class="input-group-text">cm</span>
        </div>
      </div>
      <div class="col-md-4">
        <label class="form-label small fw-semibold">Lingkar Lengan / LiLA (Cm)</label>
        <div class="input-group">
          <input type="number" step="0.1" min="0" name="lingkar_lengan" class="form-control" placeholder="Contoh: 24.5">
          <span class="input-group-text">cm</span>
        </div>
        <div class="form-text">Batas normal LiLA bumil &ge; 23.5 cm</div>
      </div>
      <div class="col-md-4">
        <label class="form-label small fw-semibold">Tanggal Pemeriksaan</label>
        <input type="date" name="tanggal_periksa" class="form-control" value="<?= date('Y-m-d') ?>">
      </div>

      <div class="col-12 d-flex gap-2 justify-content-end mt-4">
        <a href="data-ibu-hamil.php" class="btn btn-light px-4">Batal</a>
        <button type="submit" class="btn btn-primary-blue btn-3d px-4"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan Data Ibu Hamil</button>
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
