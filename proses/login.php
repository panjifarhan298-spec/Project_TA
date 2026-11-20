<?php
session_start();
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php");
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    header("Location: ../login.php?error=gagal");
    exit;
}

$stmt = mysqli_prepare($koneksi, "SELECT id, username, password, nama, role FROM users WHERE username = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id']  = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['nama']     = $user['nama'];
    $_SESSION['role']     = $user['role'];

    if ($user['role'] === 'admin') {
        header("Location: ../dashboard-admin.php");
    } else {
        header("Location: ../dashboard-bidan.php");
    }
    exit;
} else {
    header("Location: ../login.php?error=gagal");
    exit;
}
