-- Jalankan sekali pada database PenTing Balita Desa Tropodo.
-- Tabel ini sengaja tidak menambahkan foreign key agar kompatibel dengan
-- instalasi lama yang mungkin memakai tipe kolom ID berbeda.
CREATE TABLE IF NOT EXISTS jadwal_posyandu (
  id INT NOT NULL AUTO_INCREMENT,
  posyandu_id INT NOT NULL,
  tanggal DATE NOT NULL,
  waktu_mulai TIME NOT NULL,
  waktu_selesai TIME NULL,
  lokasi VARCHAR(180) NULL,
  keterangan VARCHAR(500) NULL,
  updated_by INT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_jadwal_tanggal (tanggal),
  KEY idx_jadwal_posyandu (posyandu_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
