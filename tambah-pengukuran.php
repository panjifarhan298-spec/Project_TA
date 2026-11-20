<?php
require_once 'includes/auth.php';
require_once 'config/koneksi.php';
require_once 'includes/functions.php';
cekRoleAdminAtauBidan();

$idBalitaTerpilih = (int)($_GET['id_balita'] ?? 0);

$daftarBalita = [];
$resB = mysqli_query($koneksi, "SELECT b.id, b.nama, p.nama AS nama_posyandu FROM balita b JOIN posyandu p ON p.id = b.posyandu_id ORDER BY b.nama ASC");
while ($row = mysqli_fetch_assoc($resB)) $daftarBalita[] = $row;

$pageTitle = "Tambah Pengukuran";
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<main class="container-fluid px-3 px-lg-4 py-4">
  <div class="page-heading mb-4">
    <h4 class="fw-bold mb-0">Tambah Pengukuran</h4>
    <p class="text-muted small mb-0">Catat hasil pengukuran berat &amp; tinggi balita</p>
  </div>

  <div class="chart-card">
    <form action="proses/tambah-pengukuran.php" method="POST" class="row g-3">
      <div class="col-md-6">
        <label class="form-label small fw-semibold">Pilih Balita</label>
        <select name="id_balita" class="form-select" required>
          <option value="">-- Pilih Balita --</option>
          <?php foreach ($daftarBalita as $b): ?>
            <option value="<?= $b['id'] ?>" <?= $idBalitaTerpilih == $b['id'] ? 'selected' : '' ?>>
              <?= bersih($b['nama']) ?> (<?= bersih($b['nama_posyandu']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label small fw-semibold">Tanggal Pengukuran</label>
        <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label small fw-semibold">Berat Badan (Kg)</label>
        <input type="number" step="0.1" min="0" name="berat" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label small fw-semibold">Tinggi Badan (Cm)</label>
        <input type="number" step="0.1" min="0" name="tinggi" class="form-control" required>
      </div>
      <div class="col-12">
        <label class="form-label small fw-semibold">Keterangan</label>
        <textarea name="keterangan" class="form-control" rows="2" placeholder="Contoh: kondisi sehat, nafsu makan baik, dsb"></textarea>
      </div>
      <div class="col-12 d-flex gap-2 justify-content-end mt-2">
        <a href="riwayat-pengukuran.php" class="btn btn-light">Batal</a>
        <button type="submit" class="btn btn-pink-solid"><i class="fa-solid fa-floppy-disk"></i> Simpan Pengukuran</button>
      </div>
    </form>
  </div>
</main>

<?php require_once 'includes/footer.php'; ?>
