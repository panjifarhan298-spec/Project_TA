<?php
/**
 * Script Otomatis Eksekusi Migrasi Database V2.1
 * Jalankan lewat browser: http://localhost/Posyandu_Desa_Tropodo/database/run_migration.php
 */
require_once __DIR__ . '/../config/koneksi.php';

$output = [];
$output[] = "=== Memulai Migrasi Database Posyandu Tropodo V2.1 ===";

// 1. Update Role Users (tambah 'petugas')
@mysqli_query($koneksi, "ALTER TABLE users MODIFY COLUMN role ENUM('admin','bidan','petugas') NOT NULL DEFAULT 'petugas'");
@mysqli_query($koneksi, "UPDATE users SET role = 'petugas' WHERE role = 'bidan'");
$output[] = "[OK] Role pengguna disesuaikan (mendukung 'admin' & 'petugas').";

// 2. Kolom tambahan di balita
@mysqli_query($koneksi, "ALTER TABLE balita ADD COLUMN IF NOT EXISTS alamat TEXT DEFAULT NULL AFTER nama_ibu");
@mysqli_query($koneksi, "ALTER TABLE balita ADD COLUMN IF NOT EXISTS tanggal_periksa DATE DEFAULT NULL AFTER status");
$output[] = "[OK] Kolom tabel balita siap.";

// 3. Tabel lansia (dengan penyakit_kronis)
$qLansia = "CREATE TABLE IF NOT EXISTS lansia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    posyandu_id INT NOT NULL,
    nik VARCHAR(20) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    jenis_kelamin ENUM('L','P') NOT NULL,
    tanggal_lahir DATE NOT NULL,
    umur INT NOT NULL DEFAULT 0 COMMENT 'Umur dalam tahun',
    alamat TEXT DEFAULT NULL,
    no_telepon VARCHAR(20) DEFAULT NULL,
    berat_badan DECIMAL(5,2) DEFAULT NULL,
    penyakit_kronis VARCHAR(150) DEFAULT 'Tidak Ada' COMMENT 'Riwayat penyakit kronis lansia',
    tekanan_darah VARCHAR(20) DEFAULT NULL COMMENT 'Format: 120/80',
    tanggal_periksa DATE DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_lansia_posyandu (posyandu_id),
    INDEX idx_lansia_nik (nik),
    CONSTRAINT fk_lansia_posyandu FOREIGN KEY (posyandu_id)
        REFERENCES posyandu(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
@mysqli_query($koneksi, $qLansia);
@mysqli_query($koneksi, "ALTER TABLE lansia ADD COLUMN IF NOT EXISTS penyakit_kronis VARCHAR(150) DEFAULT 'Tidak Ada' AFTER berat_badan");
$output[] = "[OK] Tabel lansia (dengan kolom penyakit kronis) siap.";

// 4. Tabel ibu_hamil (dengan lingkar_kepala & lingkar_lengan)
$qBumil = "CREATE TABLE IF NOT EXISTS ibu_hamil (
    id INT AUTO_INCREMENT PRIMARY KEY,
    posyandu_id INT NOT NULL,
    nik VARCHAR(20) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    tanggal_lahir DATE NOT NULL,
    umur INT NOT NULL DEFAULT 0 COMMENT 'Umur dalam tahun',
    alamat TEXT DEFAULT NULL,
    no_telepon VARCHAR(20) DEFAULT NULL,
    usia_kehamilan INT DEFAULT NULL COMMENT 'Usia kehamilan dalam minggu',
    hpl DATE DEFAULT NULL COMMENT 'Hari Perkiraan Lahir',
    berat_badan DECIMAL(5,2) DEFAULT NULL,
    lingkar_kepala DECIMAL(5,2) DEFAULT NULL COMMENT 'Lingkar kepala (cm)',
    lingkar_lengan DECIMAL(5,2) DEFAULT NULL COMMENT 'Lingkar lengan atas / LiLA (cm)',
    tekanan_darah VARCHAR(20) DEFAULT NULL COMMENT 'Format: 120/80',
    tanggal_periksa DATE DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_bumil_posyandu (posyandu_id),
    INDEX idx_bumil_nik (nik),
    CONSTRAINT fk_bumil_posyandu FOREIGN KEY (posyandu_id)
        REFERENCES posyandu(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
@mysqli_query($koneksi, $qBumil);
@mysqli_query($koneksi, "ALTER TABLE ibu_hamil ADD COLUMN IF NOT EXISTS lingkar_kepala DECIMAL(5,2) DEFAULT NULL AFTER berat_badan");
@mysqli_query($koneksi, "ALTER TABLE ibu_hamil ADD COLUMN IF NOT EXISTS lingkar_lengan DECIMAL(5,2) DEFAULT NULL AFTER lingkar_kepala");
$output[] = "[OK] Tabel ibu_hamil (dengan kolom lingkar kepala & lingkar lengan) siap.";

// 5. Tabel riwayat pemeriksaan Ibu Hamil dan Lansia
$qRiwayatIbuHamil = "CREATE TABLE IF NOT EXISTS pemeriksaan_ibu_hamil (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ibu_hamil_id INT NOT NULL,
    tanggal_periksa DATE NOT NULL,
    usia_kehamilan INT DEFAULT NULL,
    berat_badan DECIMAL(5,2) DEFAULT NULL,
    lingkar_kepala DECIMAL(5,2) DEFAULT NULL,
    lingkar_lengan DECIMAL(5,2) DEFAULT NULL,
    tekanan_darah VARCHAR(20) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_periksa_ibu_tanggal (ibu_hamil_id, tanggal_periksa),
    CONSTRAINT fk_periksa_ibu_data FOREIGN KEY (ibu_hamil_id)
        REFERENCES ibu_hamil(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
@mysqli_query($koneksi, $qRiwayatIbuHamil);

$qRiwayatLansia = "CREATE TABLE IF NOT EXISTS pemeriksaan_lansia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lansia_id INT NOT NULL,
    tanggal_periksa DATE NOT NULL,
    berat_badan DECIMAL(5,2) DEFAULT NULL,
    tekanan_darah VARCHAR(20) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_periksa_lansia_tanggal (lansia_id, tanggal_periksa),
    CONSTRAINT fk_periksa_lansia_data FOREIGN KEY (lansia_id)
        REFERENCES lansia(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
@mysqli_query($koneksi, $qRiwayatLansia);
$output[] = "[OK] Tabel riwayat pemeriksaan Ibu Hamil dan Lansia siap.";

// 6. Migrasi data lansia lama
$qMigrateLansia = "INSERT INTO lansia (posyandu_id, nik, nama, jenis_kelamin, tanggal_lahir, umur, berat_badan, penyakit_kronis, tanggal_periksa, created_at)
SELECT b.posyandu_id, b.nik, b.nama, b.jk, b.ttl, TIMESTAMPDIFF(YEAR, b.ttl, CURDATE()), b.berat, 'Tidak Ada', DATE(b.created_at), b.created_at
FROM balita b
WHERE b.kategori = 'lansia'
  AND NOT EXISTS (SELECT 1 FROM lansia l WHERE l.nik = b.nik)";
@mysqli_query($koneksi, $qMigrateLansia);
$output[] = "[OK] Sinkronisasi data Lansia berhasil.";

// 7. Migrasi data ibu hamil lama
$qMigrateBumil = "INSERT INTO ibu_hamil (posyandu_id, nik, nama, tanggal_lahir, umur, berat_badan, tanggal_periksa, created_at)
SELECT b.posyandu_id, b.nik, b.nama, b.ttl, TIMESTAMPDIFF(YEAR, b.ttl, CURDATE()), b.berat, DATE(b.created_at), b.created_at
FROM balita b
WHERE b.kategori = 'ibu_hamil'
  AND NOT EXISTS (SELECT 1 FROM ibu_hamil ih WHERE ih.nik = b.nik)";
@mysqli_query($koneksi, $qMigrateBumil);
$output[] = "[OK] Sinkronisasi data Ibu Hamil berhasil.";

// 8. Salin hasil pemeriksaan terbaru yang sudah ada ke tabel riwayat
@mysqli_query($koneksi, "INSERT INTO pemeriksaan_lansia (lansia_id, tanggal_periksa, berat_badan, tekanan_darah)
    SELECT l.id, l.tanggal_periksa, l.berat_badan, l.tekanan_darah
    FROM lansia l
    WHERE l.tanggal_periksa IS NOT NULL
      AND NOT EXISTS (SELECT 1 FROM pemeriksaan_lansia pl WHERE pl.lansia_id=l.id AND pl.tanggal_periksa=l.tanggal_periksa)");
@mysqli_query($koneksi, "INSERT INTO pemeriksaan_ibu_hamil (ibu_hamil_id, tanggal_periksa, usia_kehamilan, berat_badan, lingkar_kepala, lingkar_lengan, tekanan_darah)
    SELECT ih.id, ih.tanggal_periksa, ih.usia_kehamilan, ih.berat_badan, ih.lingkar_kepala, ih.lingkar_lengan, ih.tekanan_darah
    FROM ibu_hamil ih
    WHERE ih.tanggal_periksa IS NOT NULL
      AND NOT EXISTS (SELECT 1 FROM pemeriksaan_ibu_hamil ph WHERE ph.ibu_hamil_id=ih.id AND ph.tanggal_periksa=ih.tanggal_periksa)");
$output[] = "[OK] Riwayat pemeriksaan lama disalin.";

// 9. Ringkasan
$resCountBalita = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM balita WHERE kategori='balita'"))['total'] ?? 0;
$resCountLansia = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM lansia"))['total'] ?? 0;
$resCountBumil  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM ibu_hamil"))['total'] ?? 0;

$output[] = "--------------------------------------------------";
$output[] = "Status Data Terkini:";
$output[] = "- Total Data Balita: " . $resCountBalita;
$output[] = "- Total Data Ibu Hamil: " . $resCountBumil;
$output[] = "- Total Data Lansia: " . $resCountLansia;
$output[] = "=== Migrasi Selesai dengan Sukses! ===";
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Migrasi Database V2.1 - Posyandu Tropodo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
  body { font-family: 'Poppins', sans-serif; background: #F0F7FF; color: #123B70; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
  .card-migrasi { background: #FFFFFF; border-radius: 18px; box-shadow: 0 10px 30px rgba(18,59,112,0.12); border: 1px solid rgba(96,165,250,0.3); max-width: 650px; width: 100%; padding: 28px; }
  pre { background: #0F172A; color: #38BDF8; padding: 16px; border-radius: 12px; font-size: 0.82rem; }
  .btn-dashboard { background: linear-gradient(135deg, #60A5FA, #123B70); color: #FFF; font-weight: 600; border-radius: 10px; border: 0; padding: 10px 20px; text-decoration: none; display: inline-block; }
  .btn-dashboard:hover { color: #FFF; opacity: 0.95; }
</style>
</head>
<body>
<div class="card-migrasi">
  <h4 class="fw-bold mb-1" style="color: #123B70;">Migrasi Database Posyandu V2.1</h4>
  <p class="text-muted small mb-3">Pembaruan kolom <strong>lingkar kepala</strong>, <strong>lingkar lengan</strong>, <strong>penyakit kronis</strong>, dan role <strong>petugas</strong>.</p>
  <pre><?= htmlspecialchars(implode("\n", $output)) ?></pre>
  <div class="mt-3 text-end">
    <a href="../dashboard-admin.php" class="btn-dashboard">Buka Dashboard &rarr;</a>
  </div>
</div>
</body>
</html>
