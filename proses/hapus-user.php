<?php
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/functions.php';
cekRole('admin');

$id = (int)($_GET['id'] ?? 0);
$myId = (int)($_SESSION['user_id'] ?? 0);

if ($id > 0 && $id !== $myId) {
    // Hanya hapus jika role bukan admin (hanya boleh hapus bidan atau petugas)
    $stmt = mysqli_prepare($koneksi, "DELETE FROM users WHERE id = ? AND role != 'admin'");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: ../manajemen-user.php?sukses=hapus");
        exit;
    }
}

header("Location: ../manajemen-user.php");
exit;
