<?php
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/functions.php';
cekRole('admin'); // Hanya Admin yang boleh mengubah password akun (termasuk akun sendiri)

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: ../manajemen-user.php"); exit; }

$id = (int)($_POST['id'] ?? 0);
$passwordBaru = trim($_POST['password_baru'] ?? '');
$konfirmasi = trim($_POST['konfirmasi_password'] ?? '');

if ($id <= 0 || $passwordBaru === '' || strlen($passwordBaru) < 6 || $passwordBaru !== $konfirmasi) {
    header("Location: ../manajemen-user.php?error=password");
    exit;
}

// Pastikan hanya boleh update akun admin atau bidan yang memang ada (total 2 akun)
$stmtCek = mysqli_prepare($koneksi, "SELECT id, role FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmtCek, "i", $id);
mysqli_stmt_execute($stmtCek);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtCek));

if (!$user) {
    header("Location: ../manajemen-user.php?error=akses");
    exit;
}

$hash = password_hash($passwordBaru, PASSWORD_DEFAULT);
$stmt = mysqli_prepare($koneksi, "UPDATE users SET password = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, "si", $hash, $id);
mysqli_stmt_execute($stmt);

header("Location: ../manajemen-user.php?sukses=password");
exit;
