<?php
require_once 'includes/auth.php';
require_once 'config/koneksi.php';
require_once 'includes/functions.php';
cekRoleAdminAtauBidan();

$daftarPosyandu = [];
$resP = mysqli_query($koneksi, "SELECT id, nama FROM posyandu ORDER BY id ASC");
while ($row = mysqli_fetch_assoc($resP)) $daftarPosyandu[] = $row;

$error = $_GET['error'] ?? '';
$pageTitle = 'Tambah Balita';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>
<main class="container-fluid px-3 px-lg-4 py-4">
  <div class="page-heading mb-4 d-flex justify-content-between align-items-center">
    <div>
      <h4 class="fw-bold mb-0"><i class="fa-solid fa-child-reaching me-2" style="color: #60A5FA;"></i>Tambah Data Balita</h4>
      <p class="text-muted small mb-0">Isi formulir pendaftaran dan pengukuran balita baru</p>
    </div>
    <a href="data-balita.php" class="btn btn-light"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
  </div>

  <?php if ($error === 'validasi'): ?>
    <div class="alert alert-danger alert-dismissible fade show">
      <i class="fa-solid fa-triangle-exclamation me-2"></i>Harap lengkapi semua kolom yang bertanda bintang (*)!
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php elseif ($error === 'nik_ada'): ?>
    <div class="alert alert-danger alert-dismissible fade show">
      <i class="fa-solid fa-triangle-exclamation me-2"></i>NIK sudah terdaftar dalam sistem. Gunakan NIK lain.
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="chart-card">
    <form action="proses/tambah-balita.php" method="POST" enctype="multipart/form-data" class="row g-3">
      <input type="hidden" name="kategori" value="balita">

      <!-- Foto Balita -->
      <div class="col-md-4 text-center">
        <label class="form-label small fw-semibold d-block">Foto Balita (Opsional)</label>
        <div class="upload-preview mb-2">
          <img id="previewFoto" src="assets/img/default-baby.png" onerror="this.src='https://placehold.co/110x110?text=Balita'" class="foto-preview">
        </div>
        <input type="file" name="foto" accept="image/*" class="form-control" onchange="previewGambar(event)">
        <div class="form-text">Maksimal 2MB (JPG, PNG, WebP)</div>
      </div>

      <!-- Form Isian -->
      <div class="col-md-8">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Nama Lengkap Balita <span class="text-danger">*</span></label>
            <input type="text" name="nama" class="form-control" placeholder="Contoh: Muhammad Rafa" required>
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">NIK / Identitas Anak <span class="text-danger">*</span></label>
            <input type="text" name="nik" class="form-control" maxlength="20" placeholder="16 digit NIK anak" required>
          </div>

          <div class="col-md-6">
            <label class="form-label small fw-semibold">Tanggal Lahir <span class="text-danger">*</span></label>
            <input type="date" name="ttl" id="ttl" class="form-control" required onchange="hitungUmurBalita(this.value)">
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
            <select name="jk" class="form-select" required>
              <option value="">-- Pilih Jenis Kelamin --</option>
              <option value="L">Laki-laki</option>
              <option value="P">Perempuan</option>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label small fw-semibold">Nama Ayah</label>
            <input type="text" name="nama_ayah" class="form-control" placeholder="Nama ayah kandung">
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Nama Ibu</label>
            <input type="text" name="nama_ibu" class="form-control" placeholder="Nama ibu kandung">
          </div>

          <div class="col-12">
            <label class="form-label small fw-semibold">Alamat Tempat Tinggal</label>
            <textarea name="alamat" class="form-control" rows="2" placeholder="RT/RW, Dusun, Desa Tropodo"></textarea>
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
            <label class="form-label small fw-semibold">Tanggal Pemeriksaan</label>
            <input type="date" name="tanggal_periksa" class="form-control" value="<?= date('Y-m-d') ?>">
          </div>

          <div class="col-12"><hr class="my-1 text-muted"></div>
          <div class="col-12"><h6 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-ruler-combined me-1"></i> Data Pengukuran Awal</h6></div>

          <div class="col-md-6">
            <label class="form-label small fw-semibold">Berat Badan (Kg) <span class="text-danger">*</span></label>
            <div class="input-group">
              <input type="number" step="0.1" min="0" name="berat" class="form-control" placeholder="Contoh: 8.5" required>
              <span class="input-group-text">kg</span>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Tinggi Badan (Cm) <span class="text-danger">*</span></label>
            <div class="input-group">
              <input type="number" step="0.1" min="0" name="tinggi" class="form-control" placeholder="Contoh: 75.0" required>
              <span class="input-group-text">cm</span>
            </div>
            <div class="form-text">Status gizi (Normal/Berisiko/Stunting) akan dihitung otomatis.</div>
          </div>
        </div>
      </div>

      <div class="col-12 d-flex gap-2 justify-content-end mt-4">
        <a href="data-balita.php" class="btn btn-light px-4">Batal</a>
        <button type="submit" class="btn btn-pink-solid btn-3d px-4"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan Data Balita</button>
      </div>
    </form>
  </div>
</main>

<script>
function previewGambar(e) {
  const p = document.getElementById('previewFoto');
  if (e.target.files && e.target.files[0]) {
    p.src = URL.createObjectURL(e.target.files[0]);
  }
}
</script>

<?php require_once 'includes/footer.php'; ?>
