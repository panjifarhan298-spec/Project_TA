<?php
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/functions.php';
cekRoleAdminAtauBidan();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../jadwal-posyandu.php'); exit; }
$aksi = $_POST['aksi'] ?? '';
$id = (int)($_POST['id'] ?? 0);
if ($aksi === 'hapus' && $id > 0) {
    $stmt = mysqli_prepare($koneksi, 'DELETE FROM jadwal_posyandu WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id); mysqli_stmt_execute($stmt);
    header('Location: ../jadwal-posyandu.php?sukses=hapus'); exit;
}
$posyanduId = (int)($_POST['posyandu_id'] ?? 0);
$tanggal = trim($_POST['tanggal'] ?? '');
$mulai = trim($_POST['waktu_mulai'] ?? '');
$selesai = trim($_POST['waktu_selesai'] ?? '');
$lokasi = trim($_POST['lokasi'] ?? '');
$keterangan = trim($_POST['keterangan'] ?? '');
if ($posyanduId <= 0 || $tanggal === '' || $mulai === '') { header('Location: ../jadwal-posyandu.php?error=validasi'); exit; }
$selesaiDb = $selesai === '' ? null : $selesai;
if ($id > 0) {
    $stmt = mysqli_prepare($koneksi, 'UPDATE jadwal_posyandu SET posyandu_id=?, tanggal=?, waktu_mulai=?, waktu_selesai=?, lokasi=?, keterangan=?, updated_by=?, updated_at=NOW() WHERE id=?');
    $userId = (int)($_SESSION['id'] ?? 0);
    mysqli_stmt_bind_param($stmt, 'isssssii', $posyanduId, $tanggal, $mulai, $selesaiDb, $lokasi, $keterangan, $userId, $id);
} else {
    $stmt = mysqli_prepare($koneksi, 'INSERT INTO jadwal_posyandu (posyandu_id, tanggal, waktu_mulai, waktu_selesai, lokasi, keterangan, updated_by, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
    $userId = (int)($_SESSION['id'] ?? 0);
    mysqli_stmt_bind_param($stmt, 'isssssi', $posyanduId, $tanggal, $mulai, $selesaiDb, $lokasi, $keterangan, $userId);
}
mysqli_stmt_execute($stmt);
header('Location: ../jadwal-posyandu.php?sukses=simpan'); exit;
