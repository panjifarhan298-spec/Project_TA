<?php
require_once 'includes/auth.php';
require_once 'config/koneksi.php';
require_once 'includes/functions.php';
cekRole('admin');

// Hitung jumlah masing-masing role non-admin (Bidan dan Petugas)
$countAdmin = (int)(mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM users WHERE role = 'admin'"))['total'] ?? 0);
$countBidan = (int)(mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM users WHERE role = 'bidan'"))['total'] ?? 0);
$countPetugas = (int)(mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM users WHERE role = 'petugas'"))['total'] ?? 0);

// Ambil daftar pengguna non-admin (mengecualikan admin)
$daftarUser = [];
$res = mysqli_query($koneksi, "SELECT id, username, nama, role, created_at FROM users WHERE role != 'admin' ORDER BY FIELD(role, 'bidan', 'petugas'), nama ASC");
while ($row = mysqli_fetch_assoc($res)) {
    $daftarUser[] = $row;
}

$sukses = $_GET['sukses'] ?? '';
$error = $_GET['error'] ?? '';

$pageTitle = "Manajemen Pengguna (Admin, Bidan & Petugas)";
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<main class="container-fluid px-3 px-lg-4 py-4">
  <div class="page-heading mb-4 d-flex flex-wrap justify-content-between align-items-end gap-2">
    <div>
      <h4 class="fw-bold mb-1" style="color: #123B70;"><i class="fa-solid fa-users-gear me-2" style="color: #60A5FA;"></i>Manajemen Pengguna</h4>
      <p class="text-muted small mb-0">Kelola akun Bidan dan Petugas Posyandu yang bertugas mencatat dan melayani data warga.</p>
    </div>
    <a href="tambah-user.php" class="btn btn-primary-blue btn-3d"><i class="fa-solid fa-user-plus me-1"></i> Tambah Pengguna</a>
  </div>

  <?php if ($sukses === 'password'): ?>
    <div class="alert alert-success py-2 small alert-dismissible fade show">
      <i class="fa-solid fa-circle-check me-1"></i>Password pengguna berhasil diperbarui.
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php elseif ($sukses === 'user'): ?>
    <div class="alert alert-success py-2 small alert-dismissible fade show">
      <i class="fa-solid fa-circle-check me-1"></i>Pengguna baru berhasil ditambahkan!
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php elseif ($sukses === 'hapus'): ?>
    <div class="alert alert-success py-2 small alert-dismissible fade show">
      <i class="fa-solid fa-circle-check me-1"></i>Akun pengguna berhasil dihapus.
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php elseif ($error === 'password'): ?>
    <div class="alert alert-danger py-2 small alert-dismissible fade show">
      Gagal memperbarui password. Pastikan password baru minimal 6 karakter dan konfirmasi sesuai.
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Ringkasan Statistik Akun Bidan & Petugas (Tanpa Card Admin) -->
  <div class="row g-3 mb-4">
    <!-- Role: Bidan -->
    <div class="col-12 col-md-6">
      <div class="stat-card h-100 p-3 text-center">
        <div class="stat-icon mx-auto mb-2" style="background: linear-gradient(135deg, #EC4899, #BE185D); color:#FFF;">
          <i class="fa-solid fa-user-doctor"></i>
        </div>
        <div class="stat-number fw-bold fs-4" style="color: #BE185D;"><?= $countBidan ?></div>
        <div class="stat-label small fw-semibold text-muted">Total Akun Bidan</div>
      </div>
    </div>

    <!-- Role: Petugas -->
    <div class="col-12 col-md-6">
      <div class="stat-card h-100 p-3 text-center">
        <div class="stat-icon mx-auto mb-2" style="background: linear-gradient(135deg, #10B981, #047857); color:#FFF;">
          <i class="fa-solid fa-user-check"></i>
        </div>
        <div class="stat-number fw-bold fs-4" style="color: #047857;"><?= $countPetugas ?></div>
        <div class="stat-label small fw-semibold text-muted">Total Akun Petugas</div>
      </div>
    </div>
  </div>

  <!-- Daftar Kartu Pengguna (Hanya Bidan & Petugas) -->
  <div class="row g-3">
    <?php if (empty($daftarUser)): ?>
      <div class="col-12">
        <div class="chart-card text-center py-5">
          <i class="fa-solid fa-user-xmark fa-3x text-muted mb-3 opacity-50"></i>
          <h6 class="fw-bold" style="color: #123B70;">Belum Ada Akun Bidan / Petugas</h6>
          <p class="text-muted small mb-3">Klik tombol di bawah untuk menambahkan akun pengguna baru.</p>
          <a href="tambah-user.php" class="btn btn-primary-blue btn-3d"><i class="fa-solid fa-user-plus me-1"></i> Tambah Pengguna</a>
        </div>
      </div>
    <?php endif; ?>

    <?php foreach ($daftarUser as $u): 
      $role = $u['role'];
      if ($role === 'bidan') {
          $avatarBg = '#FCE7F3';
          $avatarColor = '#9D174D';
          $badgeStyle = 'background:#FCE7F3; color:#9D174D; border: 1px solid rgba(157,23,77,0.2);';
          $roleIcon = 'fa-user-doctor';
          $roleLabel = 'Bidan';
      } else {
          $avatarBg = '#D1FAE5';
          $avatarColor = '#065F46';
          $badgeStyle = 'background:#D1FAE5; color:#065F46; border: 1px solid rgba(6,95,70,0.2);';
          $roleIcon = 'fa-user-check';
          $roleLabel = 'Petugas';
      }
    ?>
      <div class="col-md-6">
        <div class="chart-card h-100 d-flex flex-column justify-content-between p-3">
          <div>
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
              <div class="d-flex align-items-center gap-2">
                <div class="user-avatar" style="background: <?= $avatarBg ?>; color: <?= $avatarColor ?>; border-radius: 12px; width: 42px; height: 42px; display:flex; align-items:center; justify-content:center; font-size:1.15rem;">
                  <i class="fa-solid <?= $roleIcon ?>"></i>
                </div>
                <div>
                  <h6 class="fw-bold mb-0 text-truncate" style="color: #123B70; max-width: 190px;"><?= bersih($u['nama']) ?></h6>
                  <span class="badge" style="<?= $badgeStyle ?> font-size: 0.72rem;">
                    <i class="fa-solid <?= $roleIcon ?> me-1"></i><?= $roleLabel ?>
                  </span>
                </div>
              </div>
              <a href="proses/hapus-user.php?id=<?= $u['id'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus akun <?= htmlspecialchars(addslashes($u['nama'])) ?>?');" class="btn btn-sm btn-outline-danger btn-hapus" title="Hapus Pengguna"><i class="fa-solid fa-trash"></i></a>
            </div>

            <p class="small text-muted mb-3">Username: <strong><?= bersih($u['username']) ?></strong> &bull; Dibuat: <?= date('d M Y', strtotime($u['created_at'])) ?></p>
          </div>

          <form action="proses/ubah-password.php" method="POST" class="row g-2 mt-2 pt-2 border-top">
            <input type="hidden" name="id" value="<?= $u['id'] ?>">
            <div class="col-12">
              <label class="form-label small fw-semibold mb-1">Reset Password</label>
              <input type="password" name="password_baru" class="form-control form-control-sm" minlength="6" placeholder="Password baru (min. 6 karakter)" required>
            </div>
            <div class="col-12">
              <input type="password" name="konfirmasi_password" class="form-control form-control-sm" minlength="6" placeholder="Konfirmasi password baru" required>
            </div>
            <div class="col-12 text-end mt-2">
              <button type="submit" class="btn btn-outline-blue btn-sm"><i class="fa-solid fa-key me-1"></i> Simpan Password</button>
            </div>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<?php require_once 'includes/footer.php'; ?>
