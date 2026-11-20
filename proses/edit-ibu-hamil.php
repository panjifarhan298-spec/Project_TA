<?php
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/functions.php';
cekRoleAdminAtauBidan();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../data-ibu-hamil.php');
    exit;
}

$id             = (int)($_POST['id'] ?? 0);
$tabelAda       = ($_POST['tabel_ada'] ?? '0') === '1';
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

if ($id <= 0 || $nama === '' || $nik === '' || $tgl_lahir === '' || $posyanduId <= 0) {
    header('Location: ../edit-ibu-hamil.php?id=' . $id . '&error=validasi');
    exit;
}

$umur = $umurInput > 0 ? $umurInput : (int)date_diff(date_create($tgl_lahir), date_create())->y;

if ($tabelAda) {
    $cekCol = mysqli_query($koneksi, "SHOW COLUMNS FROM ibu_hamil LIKE 'lingkar_kepala'");
    if (mysqli_num_rows($cekCol) > 0) {
        $stmt = mysqli_prepare($koneksi, "UPDATE ibu_hamil SET posyandu_id=?, nik=?, nama=?, tanggal_lahir=?, umur=?, alamat=?, no_telepon=?, usia_kehamilan=?, hpl=?, berat_badan=?, lingkar_kepala=?, lingkar_lengan=?, tekanan_darah=?, tanggal_periksa=?, updated_at=NOW() WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'isssissisdddssi', $posyanduId, $nik, $nama, $tgl_lahir, $umur, $alamat, $no_telp, $usia_hamil, $hpl, $berat, $lingkarKepala, $lingkarLengan, $tekanan, $tgl_periksa, $id);
    } else {
        $stmt = mysqli_prepare($koneksi, "UPDATE ibu_hamil SET posyandu_id=?, nik=?, nama=?, tanggal_lahir=?, umur=?, alamat=?, no_telepon=?, usia_kehamilan=?, hpl=?, berat_badan=?, tekanan_darah=?, tanggal_periksa=?, updated_at=NOW() WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'isssissisddsi', $posyanduId, $nik, $nama, $tgl_lahir, $umur, $alamat, $no_telp, $usia_hamil, $hpl, $berat, $tekanan, $tgl_periksa, $id);
    }
    mysqli_stmt_execute($stmt);
} else {
    $stmt = mysqli_prepare($koneksi, "UPDATE balita SET posyandu_id=?, nama=?, nik=?, ttl=?, umur=?, berat=?, updated_at=NOW() WHERE id=? AND kategori='ibu_hamil'");
    mysqli_stmt_bind_param($stmt, 'isssidi', $posyanduId, $nama, $nik, $tgl_lahir, $umur, $berat, $id);
    mysqli_stmt_execute($stmt);
}

header('Location: ../data-ibu-hamil.php?sukses=edit');
exit;
