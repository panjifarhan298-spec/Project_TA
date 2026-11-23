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
    if ($_SESSION['role'] === $role) {
        return;
    }
    // Jika mengecek bidan atau petugas, perbolehkan role staf layanan (bidan & petugas)
    if (in_array($role, ['bidan', 'petugas'], true) && in_array($_SESSION['role'], ['bidan', 'petugas'], true)) {
        return;
    }
    header("Location: /Posyandu_Desa_Tropodo/login.php?error=akses_ditolak");
    exit;
}

function cekRoleAdminAtauBidan() {
    cekLogin();
    if (!in_array($_SESSION['role'], ['admin', 'bidan', 'petugas'], true)) {
        header("Location: /Posyandu_Desa_Tropodo/login.php");
        exit;
    }
}
