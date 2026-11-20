<?php
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/functions.php';
cekRoleAdminAtauBidan();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: ../riwayat-pengukuran.php"); exit; }

$idBalita   = (int)($_POST['id_balita'] ?? 0);
$tanggal    = trim($_POST['tanggal'] ?? '');
$berat      = (float)($_POST['berat'] ?? 0);
$tinggi     = (float)($_POST['tinggi'] ?? 0);
$keterangan = trim($_POST['keterangan'] ?? '');

if ($idBalita <= 0 || $tanggal === '' || $berat <= 0 || $tinggi <= 0) {
    header("Location: ../riwayat-pengukuran.php?error=validasi");
    exit;
}

// Simpan sebagai riwayat pengukuran baru (bukan hanya update tabel balita)
$stmt = mysqli_prepare($koneksi, "INSERT INTO pengukuran (id_balita, tanggal, berat, tinggi, keterangan, created_at)
                                   VALUES (?, ?, ?, ?, ?, NOW())");
mysqli_stmt_bind_param($stmt, "isdds", $idBalita, $tanggal, $berat, $tinggi, $keterangan);
mysqli_stmt_execute($stmt);

// Ambil data balita untuk hitung ulang status & umur terbaru
$stmtB = mysqli_prepare($koneksi, "SELECT ttl FROM balita WHERE id = ?");
mysqli_stmt_bind_param($stmtB, "i", $idBalita);
mysqli_stmt_execute($stmtB);
$balita = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtB));

if ($balita) {
    $umurBulan = hitungUmurBulan($balita['ttl']);
    $statusBaru = hitungStatusBalita($umurBulan, $tinggi, $berat);

    // Update berat/tinggi terakhir & status pada tabel balita agar selalu sinkron
    $stmtU = mysqli_prepare($koneksi, "UPDATE balita SET berat=?, tinggi=?, umur=?, status=?, updated_at=NOW() WHERE id=?");
    mysqli_stmt_bind_param($stmtU, "ddisi", $berat, $tinggi, $umurBulan, $statusBaru, $idBalita);
    mysqli_stmt_execute($stmtU);
}

header("Location: ../riwayat-pengukuran.php?sukses=pengukuran");
exit;
