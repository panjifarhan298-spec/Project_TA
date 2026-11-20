<?php
/**
 * Panggil catatAktivitasLogin($koneksi) segera setelah kredensial login berhasil.
 * Tabelnya dibuat oleh database/20261112_aktivitas_login.sql.
 */
function catatAktivitasLogin(mysqli $koneksi, ?int $userId = null, ?string $nama = null, ?string $username = null, ?string $role = null): bool
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);
    $stmt = mysqli_prepare($koneksi, 'INSERT INTO aktivitas_login (user_id, nama, username, role, ip_address, user_agent, login_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
    if (!$stmt) return false;
    mysqli_stmt_bind_param($stmt, 'isssss', $userId, $nama, $username, $role, $ip, $userAgent);
    return mysqli_stmt_execute($stmt);
}
