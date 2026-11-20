<?php
/**
 * Koneksi Database - PenTing Balita Desa Tropodo
 * Menggunakan MySQLi dengan Prepared Statement
 */

$host   = "localhost";
$user   = "root";
$pass   = "";
$dbname = "posyandu_tropodo";

$koneksi = mysqli_connect($host, $user, $pass, $dbname);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($koneksi, "utf8mb4");
