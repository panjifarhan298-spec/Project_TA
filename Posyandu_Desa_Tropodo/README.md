# PenTing Balita Desa Tropodo — Website Posyandu Desa Tropodo

Website Posyandu Desa Tropodo digital bergaya seperti desain referensi yang diberikan, menggunakan:
- HTML
- CSS
- JavaScript
- PHP
- Penyimpanan data JSON (tidak wajib MySQL)

## Cara menjalankan di Windows

1. Install XAMPP.
2. Salin folder `Posyandu_PenTing_Balita` ke:
   `C:\xampp\htdocs\`
3. Jalankan Apache dari XAMPP.
4. Buka:
   `http://localhost/Posyandu_PenTing_Balita/`

## Login demo

Username: `admin`
Password: `admin123`

Atau gunakan Sign Up untuk membuat sesi pengguna sederhana.

## Struktur

- `index.php` — halaman dan routing utama
- `assets/style.css` — desain responsive
- `assets/app.js` — menu, grafik, interaksi
- `data/balita.json` — data balita yang disimpan oleh PHP

## Catatan

Versi ini sengaja dibuat tanpa framework agar mudah dipelajari dan dijalankan oleh siswa. Data tersimpan ke JSON. Untuk proyek produksi, bagian login dan database sebaiknya dipindahkan ke MySQL dengan password yang di-hash.
