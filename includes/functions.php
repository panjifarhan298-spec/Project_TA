<?php
/**
 * Kumpulan fungsi bantu (helper functions)
 * PenTing Balita Desa Tropodo
 */

/**
 * Hitung umur (dalam bulan) dari tanggal lahir sampai hari ini
 */
function hitungUmurBulan($ttl) {
    $lahir = new DateTime($ttl);
    $sekarang = new DateTime();
    $selisih = $lahir->diff($sekarang);
    return ($selisih->y * 12) + $selisih->m;
}

function formatUmur($umurBulan) {
    $tahun = intdiv($umurBulan, 12);
    $bulan = $umurBulan % 12;
    $teks = "";
    if ($tahun > 0) $teks .= $tahun . " thn ";
    $teks .= $bulan . " bln";
    return trim($teks);
}

/**
 * Tabel referensi median tinggi (cm) & berat (kg) berdasarkan umur (bulan).
 * CATATAN PENTING: Tabel ini adalah pendekatan sederhana untuk kebutuhan
 * demo/latihan sistem informasi, BUKAN standar klinis resmi.
 * Untuk penggunaan nyata di lapangan, gunakan tabel Z-score WHO/Kemenkes RI
 * yang resmi dan sudah dipisah berdasarkan jenis kelamin.
 */
function tabelReferensiPertumbuhan() {
    return [
        0  => [49.9, 3.3],
        3  => [61.4, 6.4],
        6  => [67.6, 7.9],
        9  => [72.0, 8.9],
        12 => [75.7, 9.6],
        18 => [82.3, 10.9],
        24 => [87.1, 12.2],
        30 => [91.9, 13.3],
        36 => [96.1, 14.3],
        42 => [99.9, 15.3],
        48 => [103.3, 16.3],
        54 => [106.7, 17.3],
        60 => [110.0, 18.3],
    ];
}

/**
 * Ambil median tinggi & berat untuk umur tertentu (interpolasi linear sederhana)
 */
function getMedianPertumbuhan($umurBulan) {
    $tabel = tabelReferensiPertumbuhan();
    $keys = array_keys($tabel);

    if ($umurBulan <= $keys[0]) return $tabel[$keys[0]];
    if ($umurBulan >= end($keys)) return $tabel[end($keys)];

    for ($i = 0; $i < count($keys) - 1; $i++) {
        $bawah = $keys[$i];
        $atas  = $keys[$i + 1];
        if ($umurBulan >= $bawah && $umurBulan <= $atas) {
            $rasio = ($atas == $bawah) ? 0 : ($umurBulan - $bawah) / ($atas - $bawah);
            $tinggiMedian = $tabel[$bawah][0] + $rasio * ($tabel[$atas][0] - $tabel[$bawah][0]);
            $beratMedian  = $tabel[$bawah][1] + $rasio * ($tabel[$atas][1] - $tabel[$bawah][1]);
            return [$tinggiMedian, $beratMedian];
        }
    }
    return $tabel[end($keys)];
}

/**
 * Hitung status gizi/pertumbuhan balita secara sederhana berdasarkan
 * rasio tinggi & berat aktual terhadap median referensi umur.
 * Mengembalikan salah satu: 'Normal', 'Berisiko', 'Stunting'
 */
function hitungStatusBalita($umurBulan, $tinggi, $berat) {
    [$tinggiMedian, $beratMedian] = getMedianPertumbuhan($umurBulan);

    $rasioTinggi = $tinggiMedian > 0 ? ($tinggi / $tinggiMedian) : 1;
    $rasioBerat  = $beratMedian > 0 ? ($berat / $beratMedian) : 1;

    if ($rasioTinggi < 0.90) {
        return "Stunting";
    } elseif ($rasioTinggi < 0.95 || $rasioBerat < 0.80) {
        return "Berisiko";
    } else {
        return "Normal";
    }
}

/**
 * Kelas badge Bootstrap sesuai status
 */
function badgeStatus($status) {
    switch ($status) {
        case "Normal":
            return "badge-status badge-normal";
        case "Berisiko":
            return "badge-status badge-berisiko";
        case "Stunting":
            return "badge-status badge-stunting";
        default:
            return "badge-status";
    }
}

/**
 * Upload foto balita, kembalikan nama file baru atau null jika tidak ada upload
 */
function uploadFoto($fileInputName, $folderTujuan) {
    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $file = $_FILES[$fileInputName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $ekstensiValid = ['jpg', 'jpeg', 'png', 'webp'];
    $ekstensi = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ekstensi, $ekstensiValid)) {
        return null;
    }

    // Batas ukuran file 2MB
    if ($file['size'] > 2 * 1024 * 1024) {
        return null;
    }

    $namaBaru = "balita_" . uniqid() . "." . $ekstensi;
    $tujuan = rtrim($folderTujuan, '/') . '/' . $namaBaru;

    if (move_uploaded_file($file['tmp_name'], $tujuan)) {
        return $namaBaru;
    }

    return null;
}

/**
 * Bersihkan input string untuk mencegah XSS saat ditampilkan
 */
function bersih($string) {
    return htmlspecialchars(trim($string ?? ''), ENT_QUOTES, 'UTF-8');
}
