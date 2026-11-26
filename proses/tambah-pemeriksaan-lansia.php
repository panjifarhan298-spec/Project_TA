<?php
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
cekRoleAdminAtauBidan();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../data-lansia.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: ../data-lansia.php');
    exit;
}
$detailUrl = '../detail-lansia.php?id=' . $id;

$cekTabel = mysqli_query($koneksi, "SHOW TABLES LIKE 'pemeriksaan_lansia'");
if (!$cekTabel || mysqli_num_rows($cekTabel) === 0) {
    header('Location: ' . $detailUrl . '&error=migrasi');
    exit;
}

$tanggal = trim($_POST['tanggal_periksa'] ?? '');
$tanggalObj = DateTime::createFromFormat('!Y-m-d', $tanggal);
$tanggalValid = $tanggalObj && $tanggalObj->format('Y-m-d') === $tanggal;
$beratInput = trim($_POST['berat_badan'] ?? '');
$tekananDarahInput = trim($_POST['tekanan_darah'] ?? '');
$tekananDarah = $tekananDarahInput === '' ? null : $tekananDarahInput;
$berat = null;
if ($beratInput !== '') {
    if (!is_numeric($beratInput) || (float)$beratInput <= 0) {
        $berat = false;
    } else {
        $berat = (float)$beratInput;
    }
}

if (!$tanggalValid || $berat === false || ($berat === null && $tekananDarah === null) || ($tekananDarah !== null && strlen($tekananDarah) > 20)) {
    header('Location: ' . $detailUrl . '&error=validasi#form-pemeriksaan');
    exit;
}

$cekLansia = mysqli_prepare($koneksi, "SELECT id FROM lansia WHERE id=?");
if (!$cekLansia) {
    error_log('Gagal menyiapkan pemeriksaan Lansia: ' . mysqli_error($koneksi));
    header('Location: ' . $detailUrl . '&error=simpan');
    exit;
}
mysqli_stmt_bind_param($cekLansia, 'i', $id);
if (!mysqli_stmt_execute($cekLansia)) {
    error_log('Gagal memeriksa data Lansia: ' . mysqli_stmt_error($cekLansia));
    header('Location: ' . $detailUrl . '&error=simpan');
    exit;
}
if (!mysqli_fetch_assoc(mysqli_stmt_get_result($cekLansia))) {
    header('Location: ../data-lansia.php');
    exit;
}

if (!mysqli_begin_transaction($koneksi)) {
    error_log('Gagal memulai transaksi pemeriksaan Lansia: ' . mysqli_error($koneksi));
    header('Location: ' . $detailUrl . '&error=simpan');
    exit;
}

$stmt = mysqli_prepare($koneksi, "INSERT INTO pemeriksaan_lansia (lansia_id, tanggal_periksa, berat_badan, tekanan_darah) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    mysqli_rollback($koneksi);
    error_log('Gagal menyiapkan penyimpanan pemeriksaan Lansia: ' . mysqli_error($koneksi));
    header('Location: ' . $detailUrl . '&error=simpan');
    exit;
}
mysqli_stmt_bind_param($stmt, 'isds', $id, $tanggal, $berat, $tekananDarah);
if (!mysqli_stmt_execute($stmt)) {
    mysqli_rollback($koneksi);
    error_log('Gagal menyimpan pemeriksaan Lansia: ' . mysqli_stmt_error($stmt));
    header('Location: ' . $detailUrl . '&error=simpan');
    exit;
}

$stmtTerbaru = mysqli_prepare($koneksi, "SELECT berat_badan, tekanan_darah, tanggal_periksa FROM pemeriksaan_lansia WHERE lansia_id=? ORDER BY tanggal_periksa DESC, id DESC LIMIT 1");
if (!$stmtTerbaru) {
    mysqli_rollback($koneksi);
    error_log('Gagal membaca pemeriksaan terakhir Lansia: ' . mysqli_error($koneksi));
    header('Location: ' . $detailUrl . '&error=simpan');
    exit;
}
mysqli_stmt_bind_param($stmtTerbaru, 'i', $id);
if (!mysqli_stmt_execute($stmtTerbaru)) {
    mysqli_rollback($koneksi);
    error_log('Gagal membaca pemeriksaan terakhir Lansia: ' . mysqli_stmt_error($stmtTerbaru));
    header('Location: ' . $detailUrl . '&error=simpan');
    exit;
}
$terbaru = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtTerbaru));
if (!$terbaru) {
    mysqli_rollback($koneksi);
    error_log('Riwayat pemeriksaan Lansia tidak ditemukan setelah disimpan.');
    header('Location: ' . $detailUrl . '&error=simpan');
    exit;
}
$beratTerbaru = $terbaru['berat_badan'] === null ? null : (float)$terbaru['berat_badan'];
$tekananTerbaru = $terbaru['tekanan_darah'];
$tanggalTerbaru = $terbaru['tanggal_periksa'];

$stmtUpdate = mysqli_prepare($koneksi, "UPDATE lansia SET berat_badan=COALESCE(?, berat_badan), tekanan_darah=COALESCE(?, tekanan_darah), tanggal_periksa=?, updated_at=NOW() WHERE id=?");
if (!$stmtUpdate) {
    mysqli_rollback($koneksi);
    error_log('Gagal menyiapkan pembaruan data terakhir Lansia: ' . mysqli_error($koneksi));
    header('Location: ' . $detailUrl . '&error=simpan');
    exit;
}
mysqli_stmt_bind_param($stmtUpdate, 'dssi', $beratTerbaru, $tekananTerbaru, $tanggalTerbaru, $id);
if (!mysqli_stmt_execute($stmtUpdate)) {
    mysqli_rollback($koneksi);
    error_log('Gagal memperbarui data terakhir Lansia: ' . mysqli_stmt_error($stmtUpdate));
    header('Location: ' . $detailUrl . '&error=simpan');
    exit;
}
if (!mysqli_commit($koneksi)) {
    mysqli_rollback($koneksi);
    error_log('Gagal menyelesaikan transaksi pemeriksaan Lansia: ' . mysqli_error($koneksi));
    header('Location: ' . $detailUrl . '&error=simpan');
    exit;
}

header('Location: ' . $detailUrl . '&sukses=pemeriksaan');
exit;
