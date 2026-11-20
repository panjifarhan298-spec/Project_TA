<?php
/**
 * Proteksi Halaman Berdasarkan Role
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function cekLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /Posyandu_Desa_Tropodo/login.php");
        exit;
    }
}

function cekRole($role) {
    cekLogin();
    // Jika mengecek bidan, perbolehkan juga role 'petugas'
    if ($role === 'bidan' && in_array($_SESSION['role'], ['bidan', 'petugas'], true)) {
        return;
    }
    if ($_SESSION['role'] !== $role) {
        header("Location: /Posyandu_Desa_Tropodo/login.php?error=akses_ditolak");
        exit;
    }
}

function cekRoleAdminAtauBidan() {
    cekLogin();
    if (!in_array($_SESSION['role'], ['admin', 'bidan', 'petugas'], true)) {
        header("Location: /Posyandu_Desa_Tropodo/login.php");
        exit;
    }
}
