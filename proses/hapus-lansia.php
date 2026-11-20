<?php
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
cekRole('admin');

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $cekTabel = mysqli_query($koneksi, "SHOW TABLES LIKE 'lansia'");
    if (mysqli_num_rows($cekTabel) > 0) {
        $stmt = mysqli_prepare($koneksi, "DELETE FROM lansia WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
    } else {
        $stmt = mysqli_prepare($koneksi, "DELETE FROM balita WHERE id = ? AND kategori = 'lansia'");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
    }
}

header('Location: ../data-lansia.php?sukses=hapus');
exit;
