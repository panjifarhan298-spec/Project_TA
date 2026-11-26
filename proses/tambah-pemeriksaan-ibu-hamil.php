<?php
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
cekRoleAdminAtauBidan();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../data-ibu-hamil.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: ../data-ibu-hamil.php');
    exit;
}
$detailUrl = '../detail-ibu-hamil.php?id=' . $id;

$cekTabel = mysqli_query($koneksi, "SHOW TABLES LIKE 'pemeriksaan_ibu_hamil'");
if (!$cekTabel || mysqli_num_rows($cekTabel) === 0) {
    header('Location: ' . $detailUrl . '&error=migrasi');
    exit;
}

$tanggal = trim($_POST['tanggal_periksa'] ?? '');
$tanggalObj = DateTime::createFromFormat('!Y-m-d', $tanggal);
$tanggalValid = $tanggalObj && $tanggalObj->format('Y-m-d') === $tanggal;
$usiaInput = trim($_POST['usia_kehamilan'] ?? '');
$usiaKehamilan = $usiaInput === '' ? null : (int)$usiaInput;
$beratInput = trim($_POST['berat_badan'] ?? '');
$kepalaInput = trim($_POST['lingkar_kepala'] ?? '');
$lenganInput = trim($_POST['lingkar_lengan'] ?? '');
$tekananDarahInput = trim($_POST['tekanan_darah'] ?? '');
$tekananDarah = $tekananDarahInput === '' ? null : $tekananDarahInput;

$parseUkuran = static function ($value) {
    if ($value === '') return null;
    if (!is_numeric($value) || (float)$value <= 0) return false;
    return (float)$value;
};
$berat = $parseUkuran($beratInput);
$lingkarKepala = $parseUkuran($kepalaInput);
$lingkarLengan = $parseUkuran($lenganInput);
$adaPengukuran = $usiaKehamilan !== null || $berat !== null || $lingkarKepala !== null || $lingkarLengan !== null || $tekananDarah !== null;
$ukuranValid = $berat !== false && $lingkarKepala !== false && $lingkarLengan !== false;
$usiaValid = $usiaInput === '' || (ctype_digit($usiaInput) && $usiaKehamilan >= 1 && $usiaKehamilan <= 45);

if (!$tanggalValid || !$ukuranValid || !$usiaValid || !$adaPengukuran || ($tekananDarah !== null && strlen($tekananDarah) > 20)) {
    header('Location: ' . $detailUrl . '&error=validasi#form-pemeriksaan');
    exit;
}

$cekIbu = mysqli_prepare($koneksi, "SELECT id FROM ibu_hamil WHERE id=?");
if (!$cekIbu) {
    error_log('Gagal menyiapkan pemeriksaan Ibu Hamil: ' . mysqli_error($koneksi));
    header('Location: ' . $detailUrl . '&error=simpan');
    exit;
}
mysqli_stmt_bind_param($cekIbu, 'i', $id);
if (!mysqli_stmt_execute($cekIbu)) {
    error_log('Gagal memeriksa data Ibu Hamil: ' . mysqli_stmt_error($cekIbu));
    header('Location: ' . $detailUrl . '&error=simpan');
    exit;
}
if (!mysqli_fetch_assoc(mysqli_stmt_get_result($cekIbu))) {
    header('Location: ../data-ibu-hamil.php');
    exit;
}

if (!mysqli_begin_transaction($koneksi)) {
    error_log('Gagal memulai transaksi pemeriksaan Ibu Hamil: ' . mysqli_error($koneksi));
    header('Location: ' . $detailUrl . '&error=simpan');
    exit;
}

$stmt = mysqli_prepare($koneksi, "INSERT INTO pemeriksaan_ibu_hamil (ibu_hamil_id, tanggal_periksa, usia_kehamilan, berat_badan, lingkar_kepala, lingkar_lengan, tekanan_darah) VALUES (?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    mysqli_rollback($koneksi);
    error_log('Gagal menyiapkan penyimpanan pemeriksaan Ibu Hamil: ' . mysqli_error($koneksi));
    header('Location: ' . $detailUrl . '&error=simpan');
    exit;
}
mysqli_stmt_bind_param($stmt, 'isiddds', $id, $tanggal, $usiaKehamilan, $berat, $lingkarKepala, $lingkarLengan, $tekananDarah);
if (!mysqli_stmt_execute($stmt)) {
    mysqli_rollback($koneksi);
    error_log('Gagal menyimpan pemeriksaan Ibu Hamil: ' . mysqli_stmt_error($stmt));
    header('Location: ' . $detailUrl . '&error=simpan');
    exit;
}

$stmtTerbaru = mysqli_prepare($koneksi, "SELECT usia_kehamilan, berat_badan, lingkar_kepala, lingkar_lengan, tekanan_darah, tanggal_periksa FROM pemeriksaan_ibu_hamil WHERE ibu_hamil_id=? ORDER BY tanggal_periksa DESC, id DESC LIMIT 1");
if (!$stmtTerbaru) {
    mysqli_rollback($koneksi);
    error_log('Gagal membaca pemeriksaan terakhir Ibu Hamil: ' . mysqli_error($koneksi));
    header('Location: ' . $detailUrl . '&error=simpan');
    exit;
}
mysqli_stmt_bind_param($stmtTerbaru, 'i', $id);
if (!mysqli_stmt_execute($stmtTerbaru)) {
    mysqli_rollback($koneksi);
    error_log('Gagal membaca pemeriksaan terakhir Ibu Hamil: ' . mysqli_stmt_error($stmtTerbaru));
    header('Location: ' . $detailUrl . '&error=simpan');
    exit;
}
$terbaru = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtTerbaru));
if (!$terbaru) {
    mysqli_rollback($koneksi);
    error_log('Riwayat pemeriksaan Ibu Hamil tidak ditemukan setelah disimpan.');
    header('Location: ' . $detailUrl . '&error=simpan');
    exit;
}
$usiaTerbaru = $terbaru['usia_kehamilan'] === null ? null : (int)$terbaru['usia_kehamilan'];
$beratTerbaru = $terbaru['berat_badan'] === null ? null : (float)$terbaru['berat_badan'];
$kepalaTerbaru = $terbaru['lingkar_kepala'] === null ? null : (float)$terbaru['lingkar_kepala'];
$lenganTerbaru = $terbaru['lingkar_lengan'] === null ? null : (float)$terbaru['lingkar_lengan'];
$tekananTerbaru = $terbaru['tekanan_darah'];
$tanggalTerbaru = $terbaru['tanggal_periksa'];

$stmtUpdate = mysqli_prepare($koneksi, "UPDATE ibu_hamil SET usia_kehamilan=COALESCE(?, usia_kehamilan), berat_badan=COALESCE(?, berat_badan), lingkar_kepala=COALESCE(?, lingkar_kepala), lingkar_lengan=COALESCE(?, lingkar_lengan), tekanan_darah=COALESCE(?, tekanan_darah), tanggal_periksa=?, updated_at=NOW() WHERE id=?");
if (!$stmtUpdate) {
    mysqli_rollback($koneksi);
    error_log('Gagal menyiapkan pembaruan data terakhir Ibu Hamil: ' . mysqli_error($koneksi));
    header('Location: ' . $detailUrl . '&error=simpan');
    exit;
}
mysqli_stmt_bind_param($stmtUpdate, 'idddssi', $usiaTerbaru, $beratTerbaru, $kepalaTerbaru, $lenganTerbaru, $tekananTerbaru, $tanggalTerbaru, $id);
if (!mysqli_stmt_execute($stmtUpdate)) {
    mysqli_rollback($koneksi);
    error_log('Gagal memperbarui data terakhir Ibu Hamil: ' . mysqli_stmt_error($stmtUpdate));
    header('Location: ' . $detailUrl . '&error=simpan');
    exit;
}
if (!mysqli_commit($koneksi)) {
    mysqli_rollback($koneksi);
    error_log('Gagal menyelesaikan transaksi pemeriksaan Ibu Hamil: ' . mysqli_error($koneksi));
    header('Location: ' . $detailUrl . '&error=simpan');
    exit;
}

header('Location: ' . $detailUrl . '&sukses=pemeriksaan');
exit;
