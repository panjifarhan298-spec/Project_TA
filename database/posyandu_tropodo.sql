-- =========================================================
-- Database: posyandu_tropodo
-- Sistem Informasi PenTing Balita Desa Tropodo
-- =========================================================

CREATE DATABASE IF NOT EXISTS posyandu_tropodo
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE posyandu_tropodo;

-- ---------------------------------------------------------
-- Tabel: users (Admin & Bidan)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    role ENUM('admin','bidan') NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Catatan: Akun default (admin/admin123 dan bidan/bidan123) DIBUAT
-- lewat file buat-akun-default.php agar password di-hash langsung
-- oleh PHP di server Anda (bcrypt via password_hash()), bukan lewat SQL.

-- ---------------------------------------------------------
-- Tabel: posyandu
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS posyandu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    alamat VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO posyandu (nama, alamat) VALUES
('Posyandu Tropodo 1', 'Dusun Tropodo 1, Desa Tropodo'),
('Posyandu Tropodo 2', 'Dusun Tropodo 2, Desa Tropodo'),
('Posyandu Tropodo 3', 'Dusun Tropodo 3, Desa Tropodo'),
('Posyandu Tropodo 4', 'Dusun Tropodo 4, Desa Tropodo'),
('Posyandu Tropodo 5', 'Dusun Tropodo 5, Desa Tropodo'),
('Posyandu Tropodo 6', 'Dusun Tropodo 6, Desa Tropodo'),
('Posyandu Tropodo 7', 'Dusun Tropodo 7, Desa Tropodo'),
('Posyandu Tropodo 8', 'Dusun Tropodo 8, Desa Tropodo');

-- ---------------------------------------------------------
-- Tabel: balita
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS balita (
    id INT AUTO_INCREMENT PRIMARY KEY,
    posyandu_id INT NOT NULL,
    kategori ENUM('balita','ibu_hamil','lansia') NOT NULL DEFAULT 'balita',
    nama VARCHAR(100) NOT NULL,
    nama_ayah VARCHAR(100) DEFAULT NULL,
    nama_ibu VARCHAR(100) DEFAULT NULL,
    nik VARCHAR(20) NOT NULL UNIQUE,
    ttl DATE NOT NULL,
    umur INT NOT NULL DEFAULT 0 COMMENT 'Umur dalam bulan, dihitung otomatis',
    jk ENUM('L','P') NOT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    berat DECIMAL(5,2) NOT NULL,
    tinggi DECIMAL(5,2) NOT NULL,
    status ENUM('Normal','Berisiko','Stunting') NOT NULL DEFAULT 'Normal',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_balita_posyandu FOREIGN KEY (posyandu_id)
        REFERENCES posyandu(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE INDEX idx_balita_posyandu ON balita(posyandu_id);
CREATE INDEX idx_balita_kategori ON balita(kategori);

-- ---------------------------------------------------------
-- Tabel: pengukuran (riwayat pengukuran balita)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS pengukuran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_balita INT NOT NULL,
    tanggal DATE NOT NULL,
    berat DECIMAL(5,2) NOT NULL,
    tinggi DECIMAL(5,2) NOT NULL,
    keterangan VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pengukuran_balita FOREIGN KEY (id_balita)
        REFERENCES balita(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE INDEX idx_pengukuran_balita ON pengukuran(id_balita);
CREATE INDEX idx_pengukuran_tanggal ON pengukuran(tanggal);
