-- Jalankan sekali pada database PenTing Balita Desa Tropodo.
CREATE TABLE IF NOT EXISTS aktivitas_login (
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NULL,
  nama VARCHAR(120) NULL,
  username VARCHAR(120) NULL,
  role VARCHAR(30) NULL,
  ip_address VARCHAR(45) NULL,
  user_agent VARCHAR(500) NULL,
  login_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_aktivitas_login_waktu (login_at),
  KEY idx_aktivitas_login_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
