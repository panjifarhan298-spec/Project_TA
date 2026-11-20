# PENTING BALITA — Desa Tropodo

Website ini adalah Sistem Informasi Posyandu berbasis **PHP Native, MySQL/MariaDB, Bootstrap 5, Tailwind CSS, Chart.js, Font Awesome, dan JavaScript**. Source code disusun agar dapat langsung diletakkan di `C:\xampp\htdocs\Posyandu_Desa_Tropodo` dan dibuka melalui `http://localhost/Posyandu_Desa_Tropodo/`.

## Fitur utama

Sistem menyediakan tepat **8 Posyandu Desa Tropodo**, data Balita, Ibu Hamil, dan Lansia terpisah berdasarkan Posyandu, dashboard Admin dan Bidan, pencarian serta filter data, detail dan edit data, upload foto, serta riwayat pengukuran Balita dan riwayat pemeriksaan Ibu Hamil/Lansia yang tersimpan per kunjungan.

Hanya terdapat dua role: **Admin** dan **Bidan**. Admin dapat mengelola seluruh data serta akun Bidan. Bidan dapat mengelola data operasional balita dan pengukuran, tetapi tidak dapat mengakses manajemen user, menghapus balita, membuat Admin, atau mengubah struktur Posyandu.

## Instalasi melalui XAMPP

Pertama, aktifkan modul **Apache** dan **MySQL** pada XAMPP. Salin folder `Posyandu_Desa_Tropodo` ke `C:\xampp\htdocs\`. Pastikan folder `assets/upload/` memiliki izin tulis untuk menyimpan foto balita.

Selanjutnya, buka `http://localhost/phpmyadmin`, buat atau pilih database `posyandu_tropodo`, kemudian pilih menu **Import** dan unggah file `database/posyandu_tropodo.sql`. File SQL tersebut membuat empat tabel (`users`, `posyandu`, `balita`, dan `pengukuran`) serta mengisi tepat delapan Posyandu default. Setelah itu, jalankan sekali `http://localhost/Posyandu_Desa_Tropodo/database/run_migration.php` untuk menyiapkan tabel kategori warga dan riwayat pemeriksaan.

Setelah import selesai, buka sekali `http://localhost/Posyandu_Desa_Tropodo/database/install_users.php`. Script tersebut membuat akun default menggunakan `password_hash()`. Demi keamanan, hapus atau pindahkan file `database/install_users.php` setelah akun berhasil dibuat.

## Publikasi halaman informasi di GitHub Pages

Workflow `.github/workflows/pages.yml` memublikasikan halaman informasi statis yang sama dengan tampilan utama saat ada push ke branch `main` atau `master`. File `index.html` di root juga mengarahkan pengunjung ke halaman tersebut jika Pages masih memakai sumber branch. Di repositori GitHub, pilih **Settings → Pages → GitHub Actions** agar hanya workflow tersebut yang menerbitkan situs. Workflow juga dapat dijalankan manual dari tab **Actions**.

GitHub Pages hanya melayani file statis, jadi login, database, dan pengelolaan data PHP tidak akan berfungsi di sana. Untuk memakai fitur tersebut, jalankan aplikasi dengan PHP/MySQL seperti langkah XAMPP di atas.

## Akun default

| Role | Username | Password | Akses utama |
|---|---|---|---|
| Admin | `admin` | `admin123` | Akses penuh dashboard, balita, pengukuran, grafik, Posyandu, dan user Bidan |
| Bidan | `bidan` | `bidan123` | Dashboard operasional, balita, pengukuran, grafik, dan delapan Posyandu |

Segera ganti password setelah login pertama. Password tidak disimpan dalam plaintext; proses login menggunakan `password_verify()` dan pembuatan akun menggunakan `password_hash()`.

## Struktur folder

```text
Posyandu_Desa_Tropodo/
├── index.php
├── login.php
├── logout.php
├── dashboard-admin.php
├── dashboard-bidan.php
├── data-balita.php
├── data-ibu-hamil.php
├── data-lansia.php
├── tambah-balita.php
├── edit-balita.php
├── detail-balita.php
├── detail-ibu-hamil.php
├── detail-lansia.php
├── tambah-pengukuran.php
├── riwayat-pengukuran.php
├── grafik.php
├── data-posyandu.php
├── manajemen-user.php
├── tambah-user.php
├── edit-user.php
├── config/koneksi.php
├── includes/{auth,functions,header,footer,navbar,sidebar,dashboard-data}.php
├── proses/*.php
├── assets/css/style.css
├── assets/js/script.js
├── assets/img/
├── assets/upload/
└── database/posyandu_tropodo.sql
```

## Keamanan dan validasi

Query dinamis menggunakan prepared statement `mysqli`, session dilindungi dengan `session_regenerate_id(true)` saat login, output halaman disaring dengan `htmlspecialchars()`, file foto divalidasi berdasarkan MIME type dan ukuran maksimum 2 MB, dan halaman manajemen user dilindungi dengan pengecekan role Admin. Penghapusan balita hanya tersedia untuk Admin, sementara penghapusan akun dibatasi pada role Bidan sehingga Admin tidak dapat menghapus dirinya sendiri.

## Catatan desain

Tema yang dipakai adalah **Pita & Catatan Kesehatan**. Palet utama menggunakan Tropodo Bloom `#D879C5`, biru aqua `#69B6CC`, putih hangat `#FFF7FB`, dan plum `#3E3040`, dengan status Normal `#55C96F`, Berisiko `#F2A63B`, serta Stunting `#EA6372`. Font menggunakan Fraunces untuk heading dan angka, serta DM Sans untuk body, tabel, dan kontrol.
# Project_TA
