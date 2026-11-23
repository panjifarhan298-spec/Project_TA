<?php
require_once 'includes/auth.php';
require_once 'config/koneksi.php';
require_once 'includes/functions.php';
cekRoleAdminAtauBidan();

$kategori = $_GET['kategori'] ?? 'balita';
$labels = ['balita' => 'Balita', 'ibu_hamil' => 'Ibu Hamil', 'lansia' => 'Lansia'];
if (!isset($labels[$kategori])) $kategori = 'balita';

$cari = trim($_GET['cari'] ?? '');
$pid = (int)($_GET['posyandu_id'] ?? 0);

header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="rekap-' . $kategori . '-' . date('Ymd-His') . '.xls"');
echo "<meta charset='UTF-8'>";

if ($kategori === 'lansia') {
    $cek = mysqli_query($koneksi, "SHOW TABLES LIKE 'lansia'");
    if (mysqli_num_rows($cek) > 0) {
        $sql = "SELECT l.*, p.nama AS nama_posyandu FROM lansia l JOIN posyandu p ON p.id=l.posyandu_id WHERE 1=1";
        if ($pid > 0) $sql .= " AND l.posyandu_id=" . intval($pid);
        if ($cari !== '') $sql .= " AND (l.nama LIKE '%" . mysqli_real_escape_string($koneksi, $cari) . "%' OR l.nik LIKE '%" . mysqli_real_escape_string($koneksi, $cari) . "%')";
        $sql .= " ORDER BY l.id DESC";
        $res = mysqli_query($koneksi, $sql);
    } else {
        $sql = "SELECT b.*, p.nama AS nama_posyandu, b.jk AS jenis_kelamin, b.ttl AS tanggal_lahir, b.berat AS berat_badan, 'Tidak Ada' AS penyakit_kronis FROM balita b JOIN posyandu p ON p.id=b.posyandu_id WHERE b.kategori='lansia'";
        if ($pid > 0) $sql .= " AND b.posyandu_id=" . intval($pid);
        if ($cari !== '') $sql .= " AND (b.nama LIKE '%" . mysqli_real_escape_string($koneksi, $cari) . "%' OR b.nik LIKE '%" . mysqli_real_escape_string($koneksi, $cari) . "%')";
        $sql .= " ORDER BY b.id DESC";
        $res = mysqli_query($koneksi, $sql);
    }

    echo "<table border='1'>
    <tr><th colspan='13' style='background:#123B70; color:#FFFFFF; font-size:14pt;'>REKAP DATA LANSIA - POSYANDU DESA KEMUNING TROPODO</th></tr>
    <tr style='background:#E0EFFE; font-weight:bold;'>
      <th>No</th><th>ID Lansia</th><th>NIK</th><th>Nama Lansia</th><th>Jenis Kelamin</th><th>Tanggal Lahir</th><th>Umur (Thn)</th><th>Alamat</th><th>No. Telepon</th><th>Berat (Kg)</th><th>Penyakit Kronis</th><th>Tekanan Darah</th><th>Tgl Pemeriksaan</th><th>Posyandu</th>
    </tr>";
    $no = 1;
    while ($r = mysqli_fetch_assoc($res)) {
        $tglLahir = $r['tanggal_lahir'] ?? $r['ttl'] ?? '';
        $umur = !empty($r['umur']) ? $r['umur'] : ($tglLahir ? (int)date_diff(date_create($tglLahir), date_create())->y : 0);
        echo "<tr>
          <td>" . $no++ . "</td>
          <td>L-" . str_pad($r['id'], 3, '0', STR_PAD_LEFT) . "</td>
          <td>'" . htmlspecialchars($r['nik']) . "</td>
          <td>" . htmlspecialchars($r['nama']) . "</td>
          <td>" . (($r['jenis_kelamin'] ?? $r['jk'] ?? '') === 'L' ? 'Laki-laki' : 'Perempuan') . "</td>
          <td>" . ($tglLahir ? date('d-m-Y', strtotime($tglLahir)) : '-') . "</td>
          <td>" . $umur . "</td>
          <td>" . htmlspecialchars($r['alamat'] ?? '-') . "</td>
          <td>'" . htmlspecialchars($r['no_telepon'] ?? '-') . "</td>
          <td>" . ($r['berat_badan'] ?? $r['berat'] ?? '-') . "</td>
          <td>" . htmlspecialchars($r['penyakit_kronis'] ?? 'Tidak Ada') . "</td>
          <td>" . htmlspecialchars($r['tekanan_darah'] ?? '-') . "</td>
          <td>" . (!empty($r['tanggal_periksa']) ? date('d-m-Y', strtotime($r['tanggal_periksa'])) : '-') . "</td>
          <td>" . htmlspecialchars($r['nama_posyandu']) . "</td>
        </tr>";
    }
    echo "</table>";
    exit;

} elseif ($kategori === 'ibu_hamil') {
    $cek = mysqli_query($koneksi, "SHOW TABLES LIKE 'ibu_hamil'");
    if (mysqli_num_rows($cek) > 0) {
        $sql = "SELECT ih.*, p.nama AS nama_posyandu FROM ibu_hamil ih JOIN posyandu p ON p.id=ih.posyandu_id WHERE 1=1";
        if ($pid > 0) $sql .= " AND ih.posyandu_id=" . intval($pid);
        if ($cari !== '') $sql .= " AND (ih.nama LIKE '%" . mysqli_real_escape_string($koneksi, $cari) . "%' OR ih.nik LIKE '%" . mysqli_real_escape_string($koneksi, $cari) . "%')";
        $sql .= " ORDER BY ih.id DESC";
        $res = mysqli_query($koneksi, $sql);
    } else {
        $sql = "SELECT b.*, p.nama AS nama_posyandu, b.ttl AS tanggal_lahir, b.berat AS berat_badan, NULL AS lingkar_kepala, NULL AS lingkar_lengan FROM balita b JOIN posyandu p ON p.id=b.posyandu_id WHERE b.kategori='ibu_hamil'";
        if ($pid > 0) $sql .= " AND b.posyandu_id=" . intval($pid);
        if ($cari !== '') $sql .= " AND (b.nama LIKE '%" . mysqli_real_escape_string($koneksi, $cari) . "%' OR b.nik LIKE '%" . mysqli_real_escape_string($koneksi, $cari) . "%')";
        $sql .= " ORDER BY b.id DESC";
        $res = mysqli_query($koneksi, $sql);
    }

    echo "<table border='1'>
    <tr><th colspan='15' style='background:#123B70; color:#FFFFFF; font-size:14pt;'>REKAP DATA IBU HAMIL - POSYANDU DESA KEMUNING TROPODO</th></tr>
    <tr style='background:#E0EFFE; font-weight:bold;'>
      <th>No</th><th>ID Ibu Hamil</th><th>NIK</th><th>Nama Ibu Hamil</th><th>Tanggal Lahir</th><th>Umur (Thn)</th><th>Alamat</th><th>No. Telepon</th><th>Usia Kehamilan (Mgg)</th><th>HPL</th><th>Berat (Kg)</th><th>L. Kepala (Cm)</th><th>L. Lengan / LiLA (Cm)</th><th>Tekanan Darah</th><th>Tgl Pemeriksaan</th><th>Posyandu</th>
    </tr>";
    $no = 1;
    while ($r = mysqli_fetch_assoc($res)) {
        $tglLahir = $r['tanggal_lahir'] ?? $r['ttl'] ?? '';
        $umur = !empty($r['umur']) ? $r['umur'] : ($tglLahir ? (int)date_diff(date_create($tglLahir), date_create())->y : 0);
        echo "<tr>
          <td>" . $no++ . "</td>
          <td>IH-" . str_pad($r['id'], 3, '0', STR_PAD_LEFT) . "</td>
          <td>'" . htmlspecialchars($r['nik']) . "</td>
          <td>" . htmlspecialchars($r['nama']) . "</td>
          <td>" . ($tglLahir ? date('d-m-Y', strtotime($tglLahir)) : '-') . "</td>
          <td>" . $umur . "</td>
          <td>" . htmlspecialchars($r['alamat'] ?? '-') . "</td>
          <td>'" . htmlspecialchars($r['no_telepon'] ?? '-') . "</td>
          <td>" . (!empty($r['usia_kehamilan']) ? $r['usia_kehamilan'] . ' minggu' : '-') . "</td>
          <td>" . (!empty($r['hpl']) ? date('d-m-Y', strtotime($r['hpl'])) : '-') . "</td>
          <td>" . ($r['berat_badan'] ?? $r['berat'] ?? '-') . "</td>
          <td>" . (!empty($r['lingkar_kepala']) ? $r['lingkar_kepala'] : '-') . "</td>
          <td>" . (!empty($r['lingkar_lengan']) ? $r['lingkar_lengan'] : '-') . "</td>
          <td>" . htmlspecialchars($r['tekanan_darah'] ?? '-') . "</td>
          <td>" . (!empty($r['tanggal_periksa']) ? date('d-m-Y', strtotime($r['tanggal_periksa'])) : '-') . "</td>
          <td>" . htmlspecialchars($r['nama_posyandu']) . "</td>
        </tr>";
    }
    echo "</table>";
    exit;

} else {
    // Balita
    $sql = "SELECT b.*, p.nama AS nama_posyandu FROM balita b JOIN posyandu p ON p.id=b.posyandu_id WHERE b.kategori='balita'";
    if ($pid > 0) $sql .= " AND b.posyandu_id=" . intval($pid);
    if ($cari !== '') $sql .= " AND (b.nama LIKE '%" . mysqli_real_escape_string($koneksi, $cari) . "%' OR b.nik LIKE '%" . mysqli_real_escape_string($koneksi, $cari) . "%')";
    $sql .= " ORDER BY b.id DESC";
    $res = mysqli_query($koneksi, $sql);

    echo "<table border='1'>
    <tr><th colspan='13' style='background:#123B70; color:#FFFFFF; font-size:14pt;'>REKAP DATA BALITA - POSYANDU DESA KEMUNING TROPODO</th></tr>
    <tr style='background:#E0EFFE; font-weight:bold;'>
      <th>No</th><th>ID Balita</th><th>NIK</th><th>Nama Balita</th><th>Jenis Kelamin</th><th>Tanggal Lahir</th><th>Umur (Bln)</th><th>Nama Ayah</th><th>Nama Ibu</th><th>Alamat</th><th>Berat (Kg)</th><th>Tinggi (Cm)</th><th>Status Gizi</th><th>Posyandu</th>
    </tr>";
    $no = 1;
    while ($r = mysqli_fetch_assoc($res)) {
        $umurBulan = hitungUmurBulan($r['ttl']);
        echo "<tr>
          <td>" . $no++ . "</td>
          <td>B-" . str_pad($r['id'], 3, '0', STR_PAD_LEFT) . "</td>
          <td>'" . htmlspecialchars($r['nik']) . "</td>
          <td>" . htmlspecialchars($r['nama']) . "</td>
          <td>" . ($r['jk'] === 'L' ? 'Laki-laki' : 'Perempuan') . "</td>
          <td>" . date('d-m-Y', strtotime($r['ttl'])) . "</td>
          <td>" . $umurBulan . " (" . formatUmur($umurBulan) . ")</td>
          <td>" . htmlspecialchars($r['nama_ayah'] ?? '-') . "</td>
          <td>" . htmlspecialchars($r['nama_ibu'] ?? '-') . "</td>
          <td>" . htmlspecialchars($r['alamat'] ?? '-') . "</td>
          <td>" . $r['berat'] . "</td>
          <td>" . $r['tinggi'] . "</td>
          <td>" . htmlspecialchars($r['status']) . "</td>
          <td>" . htmlspecialchars($r['nama_posyandu']) . "</td>
        </tr>";
    }
    echo "</table>";
    exit;
}
