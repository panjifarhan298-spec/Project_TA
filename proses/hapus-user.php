<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$id = (int) ($_GET['id'] ?? 0);
if ($id > 0 && $id !== (int) $_SESSION['user_id']) {
    $statement = $conn->prepare('DELETE FROM users WHERE id = ? AND role = "bidan"');
    $statement->bind_param('i', $id);
    $statement->execute();
    $statement->close();
    flash('success', 'Akun Bidan berhasil dihapus.');
}

redirect('../manajemen-user.php');
