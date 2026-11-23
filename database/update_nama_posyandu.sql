-- =========================================================
-- Update Nama Posyandu: Tropodo -> Kemuning Tropodo
-- Jalankan file ini sekali di phpMyAdmin atau MySQL CLI
-- =========================================================

USE posyandu_tropodo;

UPDATE posyandu SET nama = 'Posyandu Kemuning Tropodo 1', alamat = 'Dusun Tropodo 1, Desa Tropodo' WHERE nama = 'Posyandu Tropodo 1';
UPDATE posyandu SET nama = 'Posyandu Kemuning Tropodo 2', alamat = 'Dusun Tropodo 2, Desa Tropodo' WHERE nama = 'Posyandu Tropodo 2';
UPDATE posyandu SET nama = 'Posyandu Kemuning Tropodo 3', alamat = 'Dusun Tropodo 3, Desa Tropodo' WHERE nama = 'Posyandu Tropodo 3';
UPDATE posyandu SET nama = 'Posyandu Kemuning Tropodo 4', alamat = 'Dusun Tropodo 4, Desa Tropodo' WHERE nama = 'Posyandu Tropodo 4';
UPDATE posyandu SET nama = 'Posyandu Kemuning Tropodo 5', alamat = 'Dusun Tropodo 5, Desa Tropodo' WHERE nama = 'Posyandu Tropodo 5';
UPDATE posyandu SET nama = 'Posyandu Kemuning Tropodo 6', alamat = 'Dusun Tropodo 6, Desa Tropodo' WHERE nama = 'Posyandu Tropodo 6';
UPDATE posyandu SET nama = 'Posyandu Kemuning Tropodo 7', alamat = 'Dusun Tropodo 7, Desa Tropodo' WHERE nama = 'Posyandu Tropodo 7';
UPDATE posyandu SET nama = 'Posyandu Kemuning Tropodo 8', alamat = 'Dusun Tropodo 8, Desa Tropodo' WHERE nama = 'Posyandu Tropodo 8';

-- Verifikasi hasil
SELECT id, nama, alamat FROM posyandu ORDER BY id;
