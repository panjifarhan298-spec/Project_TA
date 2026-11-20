# Catatan Redesign PenTing Balita

Perubahan utama pada redesign ini mengikuti referensi UI aplikasi mobile PenTing Balita/Posyandu Tropodo.

## Yang diubah

- Sistem warna pastel pink, aqua blue, lavender, hijau, kuning, dan merah untuk status gizi.
- Header internal diringkas dengan branding PenTing Balita, menu responsif, serta bottom navigation pada layar mobile.
- Kartu dashboard, statistik, status, tombol, form, tabel, dan modal dibuat lebih rounded dan ringan.
- Halaman login disusun ulang menjadi layar `Sign In` dengan kartu pastel, input rounded, dan CTA `Log In`.
- Daftar balita otomatis berubah menjadi kartu ringkas pada layar mobile; desktop tetap menggunakan tabel lengkap.
- Seluruh perubahan visual dipusatkan di `assets/css/style.css` agar konsisten di halaman admin dan bidan.

## Verifikasi

- Seluruh file PHP lulus pemeriksaan sintaks menggunakan PHP CLI.
- Halaman `login.php` berhasil dirender melalui server PHP lokal dengan respons HTTP 200.

## Menjalankan

Ekstrak arsip proyek, lalu sesuaikan konfigurasi database di `config/koneksi.php`. Jalankan melalui Apache/XAMPP atau server PHP pada document root proyek.

## Fitur Pengaturan Admin

Halaman `manajemen-user.php` sekarang tampil sebagai Pengaturan Admin dan menyediakan tombol `Tambah User`. Form tersebut berada di `tambah-user.php`, sedangkan proses server-side tersedia di `proses/tambah-user.php`. Hanya role Admin yang dapat mengakses dan membuat akun baru; username dicek agar unik, role dibatasi ke Admin atau Bidan, dan password di-hash dengan `password_hash()`.
