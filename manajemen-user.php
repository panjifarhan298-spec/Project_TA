<?php
require_once 'includes/auth.php';
require_once 'config/koneksi.php';
require_once 'includes/functions.php';
cekRole('admin');

// Ambil daftar petugas (role 'petugas' atau 'bidan'), exclude administrator sesuai permintaan
$daftarUser = [];
$res = mysqli_query($koneksi, "SELECT id, username, nama, role, created_at FROM users WHERE role != 'admin' ORDER BY nama ASC");
while ($row = mysqli_fetch_assoc($res)) {
    $daftarUser[] = $row;
}

$sukses = $_GET['sukses'] ?? '';
$error = $_GET['error'] ?? '';

$pageTitle = "Manajemen Petugas";
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<main class="container-fluid px-3 px-lg-4 py-4">
  <div class="page-heading mb-4 d-flex flex-wrap justify-content-between align-items-end gap-2">
    <div>
      <h4 class="fw-bold mb-1" style="color: #123B70;"><i class="fa-solid fa-users-gear me-2" style="color: #60A5FA;"></i>Manajemen Petugas</h4>
      <p class="text-muted small mb-0">Kelola akun Petugas Posyandu yang memiliki hak akses pencatatan data</p>
    </div>
    <a href="tambah-user.php" class="btn btn-primary-blue btn-3d"><i class="fa-solid fa-user-plus me-1"></i> Tambah Petugas</a>
  </div>

  <?php if ($sukses === 'password'): ?>
    <div class="alert alert-success py-2 small alert-dismissible fade show">
      <i class="fa-solid fa-circle-check me-1"></i>Password petugas berhasil diperbarui.
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php elseif ($sukses === 'user'): ?>
    <div class="alert alert-success py-2 small alert-dismissible fade show">
      <i class="fa-solid fa-circle-check me-1"></i>Petugas baru berhasil ditambahkan!
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php elseif ($sukses === 'hapus'): ?>
    <div class="alert alert-success py-2 small alert-dismissible fade show">
      <i class="fa-solid fa-circle-check me-1"></i>Akun petugas berhasil dihapus.
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php elseif ($error === 'password'): ?>
    <div class="alert alert-danger py-2 small alert-dismissible fade show">
      Gagal memperbarui password. Pastikan password baru minimal 6 karakter dan konfirmasi sesuai.
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Ringkasan Petugas -->
  <div class="row g-3 mb-4">
    <div class="col-12 col-md-6 col-lg-4">
      <div class="stat-card h-100">
        <div class="stat-icon" style="background: linear-gradient(135deg, #60A5FA, #123B70);"><i class="fa-solid fa-users"></i></div>
        <div class="stat-number"><?= count($daftarUser) ?></div>
        <div class="stat-label">Total Akun Petugas</div>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-8">
      <div class="chart-card h-100 d-flex align-items-center justify-content-between">
        <div>
          <h6 class="fw-bold mb-1" style="color: #123B70;">Hak Akses Petugas Posyandu</h6>
          <p class="text-muted small mb-0">Petugas dapat menginput dan memperbarui data balita, ibu hamil, lansia, dan jadwal pemeriksaan.</p>
        </div>
        <a href="tambah-user.php" class="btn btn-outline-blue btn-sm px-3 rounded-pill">Tambah Akun &rarr;</a>
      </div>
    </div>
  </div>

  <!-- Daftar Kartu Petugas (Tanpa Card Administrator di atas) -->
  <div class="row g-3">
    <?php if (empty($daftarUser)): ?>
      <div class="col-12">
        <div class="chart-card text-center py-5">
          <i class="fa-solid fa-user-xmark fa-3x text-muted mb-3 opacity-50"></i>
          <h6 class="fw-bold" style="color: #123B70;">Belum Ada Akun Petugas</h6>
          <p class="text-muted small mb-3">Klik tombol di bawah untuk menambahkan akun petugas pertama.</p>
          <a href="tambah-user.php" class="btn btn-primary-blue btn-3d"><i class="fa-solid fa-user-plus me-1"></i> Tambah Petugas</a>
        </div>
      </div>
    <?php endif; ?>

    <?php foreach ($daftarUser as $u): ?>
      <div class="col-md-6">
        <div class="chart-card h-100">
          <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
            <div class="d-flex align-items-center gap-3">
              <div class="user-avatar" style="background: #E0EFFE; color: #123B70; border-radius: 12px; width: 44px; height: 44px; display:flex; align-items:center; justify-content:center; font-size:1.2rem;">
                <i class="fa-solid fa-user-check"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-0" style="color: #123B70;"><?= bersih($u['nama']) ?></h6>
                <span class="badge" style="background: #E0EFFE; color: #123B70; font-size: 0.72rem;">
                  Petugas
                </span>
              </div>
            </div>
            <a href="proses/hapus-user.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-danger btn-hapus" title="Hapus Petugas"><i class="fa-solid fa-trash"></i></a>
          </div>

          <p class="small text-muted mb-3">Username: <strong><?= bersih($u['username']) ?></strong> &bull; Dibuat: <?= date('d M Y', strtotime($u['created_at'])) ?></p>

          <form action="proses/ubah-password.php" method="POST" class="row g-2">
            <input type="hidden" name="id" value="<?= $u['id'] ?>">
            <div class="col-12">
              <label class="form-label small fw-semibold">Reset Password Petugas</label>
              <input type="password" name="password_baru" class="form-control" minlength="6" placeholder="Password baru (minimal 6 karakter)" required>
            </div>
            <div class="col-12">
              <input type="password" name="konfirmasi_password" class="form-control" minlength="6" placeholder="Konfirmasi password baru" required>
            </div>
            <div class="col-12 text-end mt-2">
              <button type="submit" class="btn btn-outline-blue btn-sm"><i class="fa-solid fa-key me-1"></i> Simpan Password Baru</button>
            </div>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<?php require_once 'includes/footer.php'; ?>
