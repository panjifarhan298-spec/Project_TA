-- =========================================================
-- Migration V2.1: Pemisahan Data Lansia, Ibu Hamil, dan Balita
-- Sesuai Desain Posyandu System Sidoarjo
-- =========================================================

USE posyandu_tropodo;

-- 1. Penyesuaian Role pada Tabel users (tiga role: admin, bidan, petugas)
ALTER TABLE users 
  MODIFY COLUMN role ENUM('admin','bidan','petugas') NOT NULL DEFAULT 'petugas';

-- 2. Penyesuaian Tabel balita
ALTER TABLE balita
  ADD COLUMN IF NOT EXISTS alamat TEXT DEFAULT NULL AFTER nama_ibu,
  ADD COLUMN IF NOT EXISTS tanggal_periksa DATE DEFAULT NULL AFTER status;

-- 3. Tabel: lansia (Mandiri)
-- Catatan: Tinggi badan diganti menjadi penyakit_kronis
CREATE TABLE IF NOT EXISTS lansia (
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
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Jika tabel lansia sudah ada sebelumnya, pastikan kolom penyakit_kronis ada
ALTER TABLE lansia 
  ADD COLUMN IF NOT EXISTS penyakit_kronis VARCHAR(150) DEFAULT 'Tidak Ada' AFTER berat_badan;

-- 4. Tabel: ibu_hamil (Mandiri)
-- Catatan: Ditambahkan lingkar_kepala dan lingkar_lengan (LiLA)
CREATE TABLE IF NOT EXISTS ibu_hamil (
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
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Jika tabel ibu_hamil sudah ada sebelumnya, pastikan kolom lingkar_kepala dan lingkar_lengan ada
ALTER TABLE ibu_hamil
  ADD COLUMN IF NOT EXISTS lingkar_kepala DECIMAL(5,2) DEFAULT NULL AFTER berat_badan,
  ADD COLUMN IF NOT EXISTS lingkar_lengan DECIMAL(5,2) DEFAULT NULL AFTER lingkar_kepala;

-- 5. Migrasi Data Lama Lansia dari tabel balita -> tabel lansia
INSERT INTO lansia (posyandu_id, nik, nama, jenis_kelamin, tanggal_lahir, umur, berat_badan, penyakit_kronis, tanggal_periksa, created_at)
SELECT b.posyandu_id, b.nik, b.nama, b.jk, b.ttl, TIMESTAMPDIFF(YEAR, b.ttl, CURDATE()), b.berat, 'Tidak Ada', DATE(b.created_at), b.created_at
FROM balita b
WHERE b.kategori = 'lansia'
  AND NOT EXISTS (SELECT 1 FROM lansia l WHERE l.nik = b.nik);

-- 6. Migrasi Data Lama Ibu Hamil dari tabel balita -> tabel ibu_hamil
INSERT INTO ibu_hamil (posyandu_id, nik, nama, tanggal_lahir, umur, berat_badan, tanggal_periksa, created_at)
SELECT b.posyandu_id, b.nik, b.nama, b.ttl, TIMESTAMPDIFF(YEAR, b.ttl, CURDATE()), b.berat, DATE(b.created_at), b.created_at
FROM balita b
WHERE b.kategori = 'ibu_hamil'
  AND NOT EXISTS (SELECT 1 FROM ibu_hamil ih WHERE ih.nik = b.nik);

-- 7. Riwayat pemeriksaan Ibu Hamil
CREATE TABLE IF NOT EXISTS pemeriksaan_ibu_hamil (
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
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT INTO pemeriksaan_ibu_hamil
    (ibu_hamil_id, tanggal_periksa, usia_kehamilan, berat_badan, lingkar_kepala, lingkar_lengan, tekanan_darah)
SELECT ih.id, ih.tanggal_periksa, ih.usia_kehamilan, ih.berat_badan, ih.lingkar_kepala, ih.lingkar_lengan, ih.tekanan_darah
FROM ibu_hamil ih
WHERE ih.tanggal_periksa IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM pemeriksaan_ibu_hamil ph
      WHERE ph.ibu_hamil_id = ih.id AND ph.tanggal_periksa = ih.tanggal_periksa
  );

-- 8. Riwayat pemeriksaan Lansia
CREATE TABLE IF NOT EXISTS pemeriksaan_lansia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lansia_id INT NOT NULL,
    tanggal_periksa DATE NOT NULL,
    berat_badan DECIMAL(5,2) DEFAULT NULL,
    tekanan_darah VARCHAR(20) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_periksa_lansia_tanggal (lansia_id, tanggal_periksa),
    CONSTRAINT fk_periksa_lansia_data FOREIGN KEY (lansia_id)
        REFERENCES lansia(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT INTO pemeriksaan_lansia
    (lansia_id, tanggal_periksa, berat_badan, tekanan_darah)
SELECT l.id, l.tanggal_periksa, l.berat_badan, l.tekanan_darah
FROM lansia l
WHERE l.tanggal_periksa IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM pemeriksaan_lansia pl
      WHERE pl.lansia_id = l.id AND pl.tanggal_periksa = l.tanggal_periksa
  );
