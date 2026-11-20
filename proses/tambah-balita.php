<?php
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/functions.php';
cekRoleAdminAtauBidan();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../data-balita.php');
    exit;
}

$kategori    = trim($_POST['kategori'] ?? 'balita');
if (!in_array($kategori, ['balita', 'ibu_hamil', 'lansia'], true)) $kategori = 'balita';

$nama        = trim($_POST['nama'] ?? '');
$nik         = trim($_POST['nik'] ?? '');
$ttl         = trim($_POST['ttl'] ?? '');
$jk          = trim($_POST['jk'] ?? '');
$posyanduId  = (int)($_POST['posyandu_id'] ?? 0);
$berat       = (float)($_POST['berat'] ?? 0);
$tinggi      = (float)($_POST['tinggi'] ?? 0);
$ayah        = trim($_POST['nama_ayah'] ?? '');
$ibu         = trim($_POST['nama_ibu'] ?? '');
$alamat      = trim($_POST['alamat'] ?? '');
$tglPeriksa  = trim($_POST['tanggal_periksa'] ?? '') ?: date('Y-m-d');

if ($nama === '' || $nik === '' || $ttl === '' || !in_array($jk, ['L', 'P'], true) || $posyanduId <= 0) {
    header("Location: ../tambah-balita.php?error=validasi");
    exit;
}

// Cek apakah NIK sudah dipakai di balita
$cekNik = mysqli_prepare($koneksi, "SELECT id FROM balita WHERE nik = ? LIMIT 1");
mysqli_stmt_bind_param($cekNik, "s", $nik);
mysqli_stmt_execute($cekNik);
if (mysqli_num_rows(mysqli_stmt_get_result($cekNik)) > 0) {
    header("Location: ../tambah-balita.php?error=nik_ada");
    exit;
}

$umur = hitungUmurBulan($ttl);
$status = hitungStatusBalita($umur, $tinggi, $berat);
$foto = uploadFoto('foto', '../assets/upload');

// Cek apakah kolom alamat dan tanggal_periksa ada di tabel balita
$cekCol = mysqli_query($koneksi, "SHOW COLUMNS FROM balita LIKE 'alamat'");
$hasAlamat = mysqli_num_rows($cekCol) > 0;

if ($hasAlamat) {
    $stmt = mysqli_prepare($koneksi, "INSERT INTO balita (posyandu_id, kategori, nama, nama_ayah, nama_ibu, alamat, nik, ttl, umur, jk, foto, berat, tinggi, status, tanggal_periksa, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    mysqli_stmt_bind_param($stmt, 'isssssssissddss', $posyanduId, $kategori, $nama, $ayah, $ibu, $alamat, $nik, $ttl, $umur, $jk, $foto, $berat, $tinggi, $status, $tglPeriksa);
} else {
    $stmt = mysqli_prepare($koneksi, "INSERT INTO balita (posyandu_id, kategori, nama, nama_ayah, nama_ibu, nik, ttl, umur, jk, foto, berat, tinggi, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    mysqli_stmt_bind_param($stmt, 'issssssissdds', $posyanduId, $kategori, $nama, $ayah, $ibu, $nik, $ttl, $umur, $jk, $foto, $berat, $tinggi, $status);
}

mysqli_stmt_execute($stmt);
$id = mysqli_insert_id($koneksi);

// Simpan riwayat pengukuran awal
if ($id && $berat > 0 && $tinggi > 0) {
    $s = mysqli_prepare($koneksi, "INSERT INTO pengukuran (id_balita, tanggal, berat, tinggi, keterangan, created_at) VALUES (?, ?, ?, ?, 'Pengukuran awal saat pendaftaran', NOW())");
    mysqli_stmt_bind_param($s, 'isdd', $id, $tglPeriksa, $berat, $tinggi);
    mysqli_stmt_execute($s);
}

header("Location: ../data-balita.php?sukses=tambah");
exit;
