<?php
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/functions.php';
cekRoleAdminAtauBidan();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../data-lansia.php');
    exit;
}

$id              = (int)($_POST['id'] ?? 0);
$tabelAda        = ($_POST['tabel_ada'] ?? '0') === '1';
$nama            = trim($_POST['nama'] ?? '');
$nik             = trim($_POST['nik'] ?? '');
$jk              = trim($_POST['jenis_kelamin'] ?? '');
$tgl_lahir       = trim($_POST['tanggal_lahir'] ?? '');
$umurInput       = (int)($_POST['umur'] ?? 0);
$no_telp         = trim($_POST['no_telepon'] ?? '');
$alamat          = trim($_POST['alamat'] ?? '');
$berat           = (float)($_POST['berat_badan'] ?? 0);
$penyakitKronis  = trim($_POST['penyakit_kronis'] ?? '') ?: 'Tidak Ada';
$tekanan         = trim($_POST['tekanan_darah'] ?? '');
$tgl_periksa     = trim($_POST['tanggal_periksa'] ?? '') ?: date('Y-m-d');
$posyanduId      = (int)($_POST['posyandu_id'] ?? 0);

if ($id <= 0 || $nama === '' || $nik === '' || !in_array($jk, ['L', 'P']) || $tgl_lahir === '' || $posyanduId <= 0) {
    header('Location: ../edit-lansia.php?id=' . $id . '&error=validasi');
    exit;
}

$umur = $umurInput > 0 ? $umurInput : (int)date_diff(date_create($tgl_lahir), date_create())->y;

if ($tabelAda) {
    // Cek kolom penyakit_kronis
    $cekCol = mysqli_query($koneksi, "SHOW COLUMNS FROM lansia LIKE 'penyakit_kronis'");
    if (mysqli_num_rows($cekCol) > 0) {
        $stmt = mysqli_prepare($koneksi, "UPDATE lansia SET posyandu_id=?, nik=?, nama=?, jenis_kelamin=?, tanggal_lahir=?, umur=?, alamat=?, no_telepon=?, berat_badan=?, penyakit_kronis=?, tekanan_darah=?, tanggal_periksa=?, updated_at=NOW() WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'issssisssdssi', $posyanduId, $nik, $nama, $jk, $tgl_lahir, $umur, $alamat, $no_telp, $berat, $penyakitKronis, $tekanan, $tgl_periksa, $id);
    } else {
        $stmt = mysqli_prepare($koneksi, "UPDATE lansia SET posyandu_id=?, nik=?, nama=?, jenis_kelamin=?, tanggal_lahir=?, umur=?, alamat=?, no_telepon=?, berat_badan=?, tekanan_darah=?, tanggal_periksa=?, updated_at=NOW() WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'issssisdsdsi', $posyanduId, $nik, $nama, $jk, $tgl_lahir, $umur, $alamat, $no_telp, $berat, $tekanan, $tgl_periksa, $id);
    }
    mysqli_stmt_execute($stmt);
} else {
    $stmt = mysqli_prepare($koneksi, "UPDATE balita SET posyandu_id=?, nama=?, nik=?, ttl=?, umur=?, jk=?, berat=?, updated_at=NOW() WHERE id=? AND kategori='lansia'");
    mysqli_stmt_bind_param($stmt, 'isssisdi', $posyanduId, $nama, $nik, $tgl_lahir, $umur, $jk, $berat, $id);
    mysqli_stmt_execute($stmt);
}

header('Location: ../data-lansia.php?sukses=edit');
exit;
