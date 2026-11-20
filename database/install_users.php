<?php
require_once __DIR__ . '/../config/koneksi.php';

$accounts = [
    ['admin', 'admin123', 'Administrator Posyandu', 'admin'],
    ['bidan', 'bidan123', 'Bidan Desa Tropodo', 'bidan'],
];

$statement = $conn->prepare(
    'INSERT INTO users (username, password, nama, role) VALUES (?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE password = VALUES(password), nama = VALUES(nama), role = VALUES(role)'
);

foreach ($accounts as $account) {
    [$username, $password, $nama, $role] = $account;
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $statement->bind_param('ssss', $username, $hash, $nama, $role);
    $statement->execute();
}

$statement->close();
echo 'Akun default berhasil dibuat. Username: admin / admin123 dan bidan / bidan123';
