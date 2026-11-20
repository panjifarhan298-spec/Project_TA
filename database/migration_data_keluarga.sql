USE posyandu_tropodo;

ALTER TABLE balita
  ADD COLUMN kategori ENUM('balita','ibu_hamil','lansia') NOT NULL DEFAULT 'balita' AFTER posyandu_id,
  ADD COLUMN nama_ayah VARCHAR(100) DEFAULT NULL AFTER nama,
  ADD COLUMN nama_ibu VARCHAR(100) DEFAULT NULL AFTER nama_ayah;

CREATE INDEX idx_balita_kategori ON balita(kategori);
