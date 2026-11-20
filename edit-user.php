<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();
$id = (int) ($_GET['id'] ?? 0);
$statement = $conn->prepare('SELECT * FROM users WHERE id = ? AND role = "bidan"');
$statement->bind_param('i', $id);
$statement->execute();
$user = $statement->get_result()->fetch_assoc();
$statement->close();
if (!$user) {
    flash('danger', 'Akun Bidan tidak ditemukan.');
    redirect('manajemen-user.php');
}
$page_title = 'Edit Bidan';
$active_page = 'users';
$current = current_user();
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/includes/sidebar.php';
?>
<main class="app-content">
    <div class="content-wrap">
        <section class="page-head">
            <div>
                <div class="eyebrow">Perbarui akun</div>
                <h1 class="page-title">Edit Bidan</h1>
                <p class="page-subtitle">Perbarui identitas atau buat password baru. Kosongkan password jika tidak ingin mengubahnya.</p>
            </div><a class="btn-soft" href="manajemen-user.php"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
        </section>
        <form action="proses/edit-user.php" method="post"><input type="hidden" name="id" value="<?= e($id) ?>">
            <div class="card-soft section-card" style="max-width:720px">
                <div class="row g-3">
                    <div class="col-12"><label class="field-label" for="nama">Nama lengkap</label><input class="form-control" id="nama" name="nama" value="<?= e($user['nama']) ?>" required></div>
                    <div class="col-md-6"><label class="field-label" for="username">Username</label><input class="form-control" id="username" name="username" value="<?= e($user['username']) ?>" required></div>
                    <div class="col-md-6"><label class="field-label" for="password">Password baru (opsional)</label><input class="form-control" id="password" name="password" type="password" minlength="6"></div>
                    <div class="col-12"><button class="btn-bloom" type="submit">Simpan perubahan <i class="fa-solid fa-check ms-1"></i></button></div>
                </div>
            </div>
        </form>
    </div>
</main><?php include __DIR__ . '/includes/footer.php'; ?>