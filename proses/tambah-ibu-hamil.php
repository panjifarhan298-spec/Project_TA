<?php
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/functions.php';
cekRoleAdminAtauBidan();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../data-ibu-hamil.php');
    exit;
}

$nama           = trim($_POST['nama'] ?? '');
$nik            = trim($_POST['nik'] ?? '');
$tgl_lahir      = trim($_POST['tanggal_lahir'] ?? '');
$umurInput      = (int)($_POST['umur'] ?? 0);
$no_telp        = trim($_POST['no_telepon'] ?? '');
$alamat         = trim($_POST['alamat'] ?? '');
$usia_hamil     = !empty($_POST['usia_kehamilan']) ? (int)$_POST['usia_kehamilan'] : null;
$hpl            = !empty($_POST['hpl']) ? trim($_POST['hpl']) : null;
$berat          = (float)($_POST['berat_badan'] ?? 0);
$lingkarKepala  = !empty($_POST['lingkar_kepala']) ? (float)$_POST['lingkar_kepala'] : null;
$lingkarLengan  = !empty($_POST['lingkar_lengan']) ? (float)$_POST['lingkar_lengan'] : null;
$tekanan        = trim($_POST['tekanan_darah'] ?? '');
$tgl_periksa    = trim($_POST['tanggal_periksa'] ?? '') ?: date('Y-m-d');
$posyanduId     = (int)($_POST['posyandu_id'] ?? 0);

if ($nama === '' || $nik === '' || $tgl_lahir === '' || $posyanduId <= 0) {
    header('Location: ../tambah-ibu-hamil.php?error=validasi');
    exit;
}

$umur = $umurInput > 0 ? $umurInput : (int)date_diff(date_create($tgl_lahir), date_create())->y;

$cekTabel = mysqli_query($koneksi, "SHOW TABLES LIKE 'ibu_hamil'");
if (mysqli_num_rows($cekTabel) > 0) {
    // Cek kolom lingkar_kepala & lingkar_lengan
    $cekCol = mysqli_query($koneksi, "SHOW COLUMNS FROM ibu_hamil LIKE 'lingkar_kepala'");
    if (mysqli_num_rows($cekCol) > 0) {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO ibu_hamil (posyandu_id, nik, nama, tanggal_lahir, umur, alamat, no_telepon, usia_kehamilan, hpl, berat_badan, lingkar_kepala, lingkar_lengan, tekanan_darah, tanggal_periksa, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        mysqli_stmt_bind_param($stmt, 'isssissisdddss', $posyanduId, $nik, $nama, $tgl_lahir, $umur, $alamat, $no_telp, $usia_hamil, $hpl, $berat, $lingkarKepala, $lingkarLengan, $tekanan, $tgl_periksa);
    } else {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO ibu_hamil (posyandu_id, nik, nama, tanggal_lahir, umur, alamat, no_telepon, usia_kehamilan, hpl, berat_badan, tekanan_darah, tanggal_periksa, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        mysqli_stmt_bind_param($stmt, 'isssissisdds', $posyanduId, $nik, $nama, $tgl_lahir, $umur, $alamat, $no_telp, $usia_hamil, $hpl, $berat, $tekanan, $tgl_periksa);
    }
    mysqli_stmt_execute($stmt);
} else {
    // Fallback simpan ke balita dengan kategori ibu_hamil
    $stmt = mysqli_prepare($koneksi, "INSERT INTO balita (posyandu_id, kategori, nama, nik, ttl, umur, jk, berat, tinggi, status, created_at, updated_at) VALUES (?, 'ibu_hamil', ?, ?, ?, ?, 'P', ?, 0, 'Normal', NOW(), NOW())");
    mysqli_stmt_bind_param($stmt, 'isssiid', $posyanduId, $nama, $nik, $tgl_lahir, $umur, $berat);
    mysqli_stmt_execute($stmt);
}

header('Location: ../data-ibu-hamil.php?sukses=tambah');
exit;
