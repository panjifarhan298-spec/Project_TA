<?php
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/functions.php';
cekRoleAdminAtauBidan();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../data-balita.php');
    exit;
}

$id         = (int)($_POST['id'] ?? 0);
$kategori   = 'balita';
$nama       = trim($_POST['nama'] ?? '');
$nik        = trim($_POST['nik'] ?? '');
$ttl        = trim($_POST['ttl'] ?? '');
$jk         = trim($_POST['jk'] ?? '');
$pid        = (int)($_POST['posyandu_id'] ?? 0);
$berat      = (float)($_POST['berat'] ?? 0);
$tinggi     = (float)($_POST['tinggi'] ?? 0);
$ayah       = trim($_POST['nama_ayah'] ?? '');
$ibu        = trim($_POST['nama_ibu'] ?? '');
$alamat     = trim($_POST['alamat'] ?? '');
$statusUser = trim($_POST['status'] ?? '');
$tglPeriksa = trim($_POST['tanggal_periksa'] ?? '') ?: date('Y-m-d');

if ($id <= 0 || $nama === '' || $nik === '' || $ttl === '' || !in_array($jk, ['L', 'P'], true) || $pid <= 0) {
    header("Location: ../edit-balita.php?id=$id&error=validasi");
    exit;
}

$s = mysqli_prepare($koneksi, 'SELECT foto FROM balita WHERE id=?');
mysqli_stmt_bind_param($s, 'i', $id);
mysqli_stmt_execute($s);
$old = mysqli_fetch_assoc(mysqli_stmt_get_result($s));
$foto = uploadFoto('foto', '../assets/upload') ?: ($old['foto'] ?? null);

$umur = hitungUmurBulan($ttl);
$status = in_array($statusUser, ['Normal', 'Berisiko', 'Stunting']) ? $statusUser : hitungStatusBalita($umur, $tinggi, $berat);

$cekCol = mysqli_query($koneksi, "SHOW COLUMNS FROM balita LIKE 'alamat'");
$hasAlamat = mysqli_num_rows($cekCol) > 0;

if ($hasAlamat) {
    $s = mysqli_prepare($koneksi, 'UPDATE balita SET posyandu_id=?, kategori=?, nama=?, nama_ayah=?, nama_ibu=?, alamat=?, nik=?, ttl=?, umur=?, jk=?, foto=?, berat=?, tinggi=?, status=?, tanggal_periksa=?, updated_at=NOW() WHERE id=?');
    mysqli_stmt_bind_param($s, 'isssssssissddssi', $pid, $kategori, $nama, $ayah, $ibu, $alamat, $nik, $ttl, $umur, $jk, $foto, $berat, $tinggi, $status, $tglPeriksa, $id);
} else {
    $s = mysqli_prepare($koneksi, 'UPDATE balita SET posyandu_id=?, kategori=?, nama=?, nama_ayah=?, nama_ibu=?, nik=?, ttl=?, umur=?, jk=?, foto=?, berat=?, tinggi=?, status=?, updated_at=NOW() WHERE id=?');
    mysqli_stmt_bind_param($s, 'issssssissddsi', $pid, $kategori, $nama, $ayah, $ibu, $nik, $ttl, $umur, $jk, $foto, $berat, $tinggi, $status, $id);
}

mysqli_stmt_execute($s);
header("Location: ../data-balita.php?sukses=edit");
exit;
