<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../manajemen-user.php');
}

$id = (int) ($_POST['id'] ?? 0);
$nama = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($id < 1 || $nama === '' || !preg_match('/^[a-zA-Z0-9._-]{4,50}$/', $username)) {
    flash('danger', 'Nama dan username belum memenuhi ketentuan.');
    redirect('../edit-user.php?id=' . $id);
}

if ($password !== '') {
    if (strlen($password) < 6) {
        flash('danger', 'Password baru minimal 6 karakter.');
        redirect('../edit-user.php?id=' . $id);
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $statement = $conn->prepare('UPDATE users SET nama = ?, username = ?, password = ? WHERE id = ? AND role = "bidan"');
    $statement->bind_param('sssi', $nama, $username, $hash, $id);
} else {
    $statement = $conn->prepare('UPDATE users SET nama = ?, username = ? WHERE id = ? AND role = "bidan"');
    $statement->bind_param('ssi', $nama, $username, $id);
}

if ($statement->execute()) {
    flash('success', 'Data Bidan berhasil diperbarui.');
} else {
    flash('danger', 'Data tidak dapat diperbarui. Pastikan username belum digunakan.');
}
$statement->close();
redirect('../manajemen-user.php');
