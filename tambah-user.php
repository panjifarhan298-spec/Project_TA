<?php
require_once 'includes/auth.php';
require_once 'config/koneksi.php';
require_once 'includes/functions.php';
cekRole('admin');
$error = $_GET['error'] ?? '';

$pageTitle = "Tambah Pengguna Baru";
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<main class="container-fluid px-3 px-lg-4 py-4">
  <div class="page-heading mb-4 d-flex flex-wrap justify-content-between align-items-end gap-2">
    <div>
      <div class="d-flex align-items-center gap-2 mb-1">
        <a href="manajemen-user.php" class="btn btn-sm btn-light" aria-label="Kembali ke manajemen user"><i class="fa-solid fa-arrow-left"></i></a>
        <h4 class="fw-bold mb-0" style="color: #123B70;">Tambah Pengguna Baru</h4>
      </div>
      <p class="text-muted small mb-0">Buat akun pengguna baru dengan pilihan hak akses Admin, Bidan, atau Petugas.</p>
    </div>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-danger py-2 small alert-dismissible fade show">
      <i class="fa-solid fa-triangle-exclamation me-1"></i>
      <?php if ($error === 'username_terpakai'): ?>Username sudah digunakan. Silakan pilih username lain.
      <?php elseif ($error === 'username'): ?>Username harus berisi 3–50 karakter berupa huruf, angka, titik, garis bawah, atau tanda hubung.
      <?php elseif ($error === 'password'): ?>Password minimal 6 karakter dan konfirmasi password harus sama.
      <?php elseif ($error === 'role'): ?>Silakan pilih role user yang valid.
      <?php else: ?>Data user tidak dapat disimpan. Periksa kembali isian form.
      <?php endif; ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="row g-3">
    <div class="col-lg-7">
      <div class="chart-card">
        <div class="d-flex align-items-center gap-3 mb-4">
          <div class="user-avatar" style="background: #E0EFFE; color: #123B70;"><i class="fa-solid fa-user-plus"></i></div>
          <div>
            <h6 class="fw-bold mb-1" style="color: #123B70;">Data Akun Baru</h6>
            <p class="small text-muted mb-0">Pastikan username unik dan belum pernah didaftarkan.</p>
          </div>
        </div>

        <form action="proses/tambah-user.php" method="POST" autocomplete="off">
          <div class="mb-3">
            <label for="nama" class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" id="nama" name="nama" class="form-control" maxlength="100" placeholder="Contoh: Siti Aminah, A.Md.Keb" required>
          </div>
          <div class="mb-3">
            <label for="username" class="form-label small fw-semibold">Username <span class="text-danger">*</span></label>
            <input type="text" id="username" name="username" class="form-control" maxlength="50" pattern="[A-Za-z0-9._-]{3,50}" placeholder="Contoh: petugas.siti" required>
            <div class="form-text">Gunakan 3–50 karakter: huruf kecil, angka, titik, atau garis bawah.</div>
          </div>
          <div class="mb-3">
            <label for="role" class="form-label small fw-semibold">Role / Hak Akses <span class="text-danger">*</span></label>
            <select id="role" name="role" class="form-select" required>
              <option value="petugas" selected>Petugas (Kader / Petugas Lapangan)</option>
              <option value="bidan">Bidan (Tenaga Kesehatan / Bidan Desa)</option>
              <option value="admin">Admin (Administrator Utama)</option>
            </select>
            <div class="form-text">Pilih salah satu dari 3 role: Admin, Bidan, atau Petugas.</div>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label for="password" class="form-label small fw-semibold">Password <span class="text-danger">*</span></label>
              <input type="password" id="password" name="password" class="form-control" minlength="6" placeholder="Minimal 6 karakter" required>
            </div>
            <div class="col-md-6">
              <label for="konfirmasi_password" class="form-label small fw-semibold">Konfirmasi Password <span class="text-danger">*</span></label>
              <input type="password" id="konfirmasi_password" name="konfirmasi_password" class="form-control" minlength="6" placeholder="Ulangi password" required>
            </div>
          </div>
          <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="manajemen-user.php" class="btn btn-light px-4">Batal</a>
            <button type="submit" class="btn btn-primary-blue btn-3d px-4"><i class="fa-solid fa-user-plus me-1"></i> Simpan Pengguna</button>
          </div>
        </form>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="chart-card h-100">
        <h6 class="fw-bold mb-3" style="color: #123B70;"><i class="fa-solid fa-shield-halved me-1" style="color: #60A5FA;"></i> Pembagian 3 Role Pengguna</h6>
        <div class="small text-muted d-flex flex-column gap-3">
          <div class="p-2 rounded bg-light border-start border-3 border-primary">
            <strong class="text-primary d-block mb-1"><i class="fa-solid fa-user-gear me-1"></i> 1. Admin</strong>
            Memiliki hak akses penuh untuk mengelola pengguna, menghapus data, melihat laporan, dan log aktivitas login sistem.
          </div>
          <div class="p-2 rounded bg-light border-start border-3 border-danger">
            <strong class="text-danger d-block mb-1"><i class="fa-solid fa-user-doctor me-1"></i> 2. Bidan</strong>
            Tenaga medis posyandu untuk mencatat pelayanan klinis, imunisasi balita, dan pemeriksaan kesehatan ibu hamil.
          </div>
          <div class="p-2 rounded bg-light border-start border-3 border-success">
            <strong class="text-success d-block mb-1"><i class="fa-solid fa-user-check me-1"></i> 3. Petugas</strong>
            Kader posyandu untuk pendataan dan monitoring balita, ibu hamil, lansia, dan jadwal posyandu di wilayah desa.
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<?php require_once 'includes/footer.php'; ?>
