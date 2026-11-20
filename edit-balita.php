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

$stmt = mysqli_prepare($koneksi, "SELECT * FROM balita WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$balita = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$balita) { 
    header("Location: data-balita.php"); 
    exit; 
}

$daftarPosyandu = [];
$resP = mysqli_query($koneksi, "SELECT id, nama FROM posyandu ORDER BY id ASC");
while ($row = mysqli_fetch_assoc($resP)) $daftarPosyandu[] = $row;

$pageTitle = 'Edit Data Balita';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<main class="container-fluid px-3 px-lg-4 py-4">
  <div class="page-heading mb-4 d-flex justify-content-between align-items-center">
    <div>
      <h4 class="fw-bold mb-0"><i class="fa-solid fa-pen-to-square me-2" style="color: #60A5FA;"></i>Edit Data Balita</h4>
      <p class="text-muted small mb-0">Perbarui informasi balita: <strong><?= bersih($balita['nama']) ?></strong></p>
    </div>
    <a href="data-balita.php" class="btn btn-light"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
  </div>

  <div class="chart-card">
    <form action="proses/edit-balita.php" method="POST" enctype="multipart/form-data" class="row g-3">
      <input type="hidden" name="id" value="<?= $balita['id'] ?>">

      <div class="col-md-4 text-center">
        <label class="form-label small fw-semibold d-block">Foto Balita</label>
        <div class="upload-preview mb-2">
          <?php if (!empty($balita['foto']) && file_exists('assets/upload/' . $balita['foto'])): ?>
            <img id="previewFoto" src="assets/upload/<?= bersih($balita['foto']) ?>" class="foto-preview">
          <?php else: ?>
            <img id="previewFoto" src="assets/img/default-baby.png" onerror="this.src='https://placehold.co/110x110?text=Balita'" class="foto-preview">
          <?php endif; ?>
        </div>
        <input type="file" name="foto" accept="image/*" class="form-control" onchange="previewGambar(event)">
        <div class="form-text">Kosongkan jika tidak ingin mengubah foto</div>
      </div>

      <div class="col-md-8">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Nama Lengkap Balita <span class="text-danger">*</span></label>
            <input type="text" name="nama" class="form-control" value="<?= bersih($balita['nama']) ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">NIK / Identitas Anak <span class="text-danger">*</span></label>
            <input type="text" name="nik" class="form-control" value="<?= bersih($balita['nik']) ?>" maxlength="20" required>
          </div>

          <div class="col-md-6">
            <label class="form-label small fw-semibold">Tanggal Lahir <span class="text-danger">*</span></label>
            <input type="date" name="ttl" class="form-control" value="<?= bersih($balita['ttl']) ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
            <select name="jk" class="form-select" required>
              <option value="L" <?= $balita['jk']=='L'?'selected':'' ?>>Laki-laki</option>
              <option value="P" <?= $balita['jk']=='P'?'selected':'' ?>>Perempuan</option>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label small fw-semibold">Nama Ayah</label>
            <input type="text" name="nama_ayah" class="form-control" value="<?= bersih($balita['nama_ayah'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Nama Ibu</label>
            <input type="text" name="nama_ibu" class="form-control" value="<?= bersih($balita['nama_ibu'] ?? '') ?>">
          </div>

          <div class="col-12">
            <label class="form-label small fw-semibold">Alamat Tempat Tinggal</label>
            <textarea name="alamat" class="form-control" rows="2"><?= bersih($balita['alamat'] ?? '') ?></textarea>
          </div>

          <div class="col-md-6">
            <label class="form-label small fw-semibold">Posyandu <span class="text-danger">*</span></label>
            <select name="posyandu_id" class="form-select" required>
              <?php foreach ($daftarPosyandu as $p): ?>
                <option value="<?= $p['id'] ?>" <?= $balita['posyandu_id']==$p['id']?'selected':'' ?>><?= bersih($p['nama']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Status Pertumbuhan</label>
            <select name="status" class="form-select" required>
              <option value="Normal" <?= $balita['status']=='Normal'?'selected':'' ?>>Normal</option>
              <option value="Berisiko" <?= $balita['status']=='Berisiko'?'selected':'' ?>>Berisiko</option>
              <option value="Stunting" <?= $balita['status']=='Stunting'?'selected':'' ?>>Stunting</option>
            </select>
            <div class="form-text">Status otomatis terhitung saat ada pengukuran baru</div>
          </div>

          <div class="col-md-6">
            <label class="form-label small fw-semibold">Berat Badan Terakhir (Kg) <span class="text-danger">*</span></label>
            <div class="input-group">
              <input type="number" step="0.1" min="0" name="berat" class="form-control" value="<?= $balita['berat'] ?>" required>
              <span class="input-group-text">kg</span>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Tinggi Badan Terakhir (Cm) <span class="text-danger">*</span></label>
            <div class="input-group">
              <input type="number" step="0.1" min="0" name="tinggi" class="form-control" value="<?= $balita['tinggi'] ?>" required>
              <span class="input-group-text">cm</span>
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label small fw-semibold">Tanggal Pemeriksaan Terakhir</label>
            <input type="date" name="tanggal_periksa" class="form-control" value="<?= $balita['tanggal_periksa'] ?? date('Y-m-d') ?>">
          </div>
        </div>
      </div>

      <div class="col-12 d-flex gap-2 justify-content-end mt-4">
        <a href="data-balita.php" class="btn btn-light px-4">Batal</a>
        <button type="submit" class="btn btn-pink-solid btn-3d px-4"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan</button>
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
