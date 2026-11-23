<?php
/**
 * buat-akun-default.php
 * -----------------------------------------------------------
 * SCRIPT SEKALI JALAN untuk membuat 2 akun default sistem:
 *   - admin / admin123
 *   - bidan / bidan123
 *
 * Password di-hash menggunakan password_hash() bawaan PHP di server
 * Anda sendiri (bcrypt), sehingga hash yang tersimpan di database
 * SELALU valid untuk versi PHP yang Anda jalankan di XAMPP.
 *
 * CARA PAKAI:
 * 1. Import database/posyandu_tropodo.sql lebih dulu lewat phpMyAdmin.
 * 2. Buka di browser: http://localhost/Posyandu_Desa_Tropodo/buat-akun-default.php
 * 3. Setelah muncul pesan sukses, SEGERA HAPUS file ini dari folder htdocs
 *    (agar tidak bisa diakses/disalahgunakan orang lain).
 */
require_once 'config/koneksi.php';

$cek = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM users");
$jumlah = mysqli_fetch_assoc($cek)['total'];

if ($jumlah > 0) {
    echo "<h3>Akun sudah pernah dibuat sebelumnya.</h3>";
    echo "<p>Jumlah akun di database saat ini: $jumlah. Silakan hapus file ini demi keamanan.</p>";
    exit;
}

$hashAdmin = password_hash('admin123', PASSWORD_DEFAULT);
$hashBidan = password_hash('bidan123', PASSWORD_DEFAULT);
$hashPetugas = password_hash('petugas123', PASSWORD_DEFAULT);

$stmt = mysqli_prepare($koneksi, "INSERT INTO users (username, password, nama, role, created_at) VALUES (?, ?, ?, ?, NOW())");

$username = 'admin'; $nama = 'Administrator'; $role = 'admin';
mysqli_stmt_bind_param($stmt, "ssss", $username, $hashAdmin, $nama, $role);
mysqli_stmt_execute($stmt);

$username = 'bidan'; $nama = 'Bidan Desa Kemuning Tropodo'; $role = 'bidan';
mysqli_stmt_bind_param($stmt, "ssss", $username, $hashBidan, $nama, $role);
mysqli_stmt_execute($stmt);

$username = 'petugas'; $nama = 'Petugas Posyandu Desa Kemuning Tropodo'; $role = 'petugas';
mysqli_stmt_bind_param($stmt, "ssss", $username, $hashPetugas, $nama, $role);
mysqli_stmt_execute($stmt);

echo "<div style='font-family:sans-serif;padding:24px;max-width:520px;margin:40px auto;border:1px solid #60A5FA;border-radius:16px;background:#F0F7FF;'>";
echo "<h3 style='color:#123B70;'>3 Akun Default Berhasil Dibuat &#127881;</h3>";
echo "<p><strong>1. Admin</strong> &rarr; username: <code>admin</code> / password: <code>admin123</code></p>";
echo "<p><strong>2. Bidan</strong> &rarr; username: <code>bidan</code> / password: <code>bidan123</code></p>";
echo "<p><strong>3. Petugas</strong> &rarr; username: <code>petugas</code> / password: <code>petugas123</code></p>";
echo "<p style='color:#c53c3c;'><strong>PENTING:</strong> Hapus file <code>buat-akun-default.php</code> ini sekarang dari folder htdocs Anda demi keamanan.</p>";
echo "<a href='login.php' style='display:inline-block;margin-top:8px;background:#123B70;color:#fff;padding:8px 20px;border-radius:50px;text-decoration:none;'>Ke Halaman Login</a>";
echo "</div>";
