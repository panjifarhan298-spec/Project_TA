<?php
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/functions.php';
cekRole('admin'); // Hanya admin yang boleh menghapus data

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header("Location: ../data-balita.php"); exit; }

// Hapus riwayat pengukuran terkait dahulu (menjaga integrity, walau sudah ada FK cascade)
$stmt1 = mysqli_prepare($koneksi, "DELETE FROM pengukuran WHERE id_balita = ?");
mysqli_stmt_bind_param($stmt1, "i", $id);
mysqli_stmt_execute($stmt1);

// Hapus foto fisik jika ada
$stmtF = mysqli_prepare($koneksi, "SELECT foto FROM balita WHERE id = ?");
mysqli_stmt_bind_param($stmtF, "i", $id);
mysqli_stmt_execute($stmtF);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtF));
if ($data && !empty($data['foto']) && file_exists('../assets/upload/' . $data['foto'])) {
    @unlink('../assets/upload/' . $data['foto']);
}

$stmt2 = mysqli_prepare($koneksi, "DELETE FROM balita WHERE id = ?");
mysqli_stmt_bind_param($stmt2, "i", $id);
mysqli_stmt_execute($stmt2);

header("Location: ../data-balita.php?sukses=hapus");
exit;
