<?php
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/functions.php';
cekRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../tambah-user.php');
    exit;
}

$nama = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');
$role = trim($_POST['role'] ?? 'petugas');
$password = trim($_POST['password'] ?? '');
$konfirmasi = trim($_POST['konfirmasi_password'] ?? '');

$redirectError = '../tambah-user.php?error=';

if ($nama === '' || strlen($nama) > 100) {
    header('Location: ' . $redirectError . 'nama');
    exit;
}

if (!preg_match('/^[A-Za-z0-9._-]{3,50}$/', $username)) {
    header('Location: ' . $redirectError . 'username');
    exit;
}

if (!in_array($role, ['admin', 'bidan', 'petugas'], true)) {
    $role = 'petugas';
}

if (strlen($password) < 6 || $password !== $konfirmasi) {
    header('Location: ' . $redirectError . 'password');
    exit;
}

$stmtCek = mysqli_prepare($koneksi, 'SELECT id FROM users WHERE username = ? LIMIT 1');
mysqli_stmt_bind_param($stmtCek, 's', $username);
mysqli_stmt_execute($stmtCek);
$hasilCek = mysqli_stmt_get_result($stmtCek);

if (mysqli_num_rows($hasilCek) > 0) {
    header('Location: ' . $redirectError . 'username_terpakai');
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

// Pastikan enum role mendukung 'petugas'
$cekEnum = mysqli_query($koneksi, "SHOW COLUMNS FROM users LIKE 'role'");
$colInfo = mysqli_fetch_assoc($cekEnum);
if ($colInfo && strpos($colInfo['Type'], 'petugas') === false) {
    @mysqli_query($koneksi, "ALTER TABLE users MODIFY COLUMN role ENUM('admin','bidan','petugas') NOT NULL DEFAULT 'petugas'");
}

// Simpan user baru
$stmt = mysqli_prepare($koneksi, "INSERT INTO users (username, password, nama, role, created_at) VALUES (?, ?, ?, ?, NOW())");
mysqli_stmt_bind_param($stmt, 'ssss', $username, $hash, $nama, $role);
if (mysqli_stmt_execute($stmt)) {
    header('Location: ../manajemen-user.php?sukses=user');
    exit;
} else {
    // Jika enum database lama belum support 'petugas', simpan sebagai 'bidan'
    $fallbackRole = 'bidan';
    $stmtFallback = mysqli_prepare($koneksi, "INSERT INTO users (username, password, nama, role, created_at) VALUES (?, ?, ?, ?, NOW())");
    mysqli_stmt_bind_param($stmtFallback, 'ssss', $username, $hash, $nama, $fallbackRole);
    mysqli_stmt_execute($stmtFallback);
    header('Location: ../manajemen-user.php?sukses=user');
    exit;
}
