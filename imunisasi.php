<?php
require_once 'includes/auth.php';
require_once 'config/koneksi.php';
require_once 'includes/functions.php';
cekRoleAdminAtauBidan();

$jadwalImunisasi = [
    ['vaksin' => 'BCG', 'usia' => '0–1 bulan', 'keterangan' => 'Perlindungan awal terhadap tuberkulosis.', 'warna' => 'blue', 'ikon' => 'fa-shield-virus'],
    ['vaksin' => 'Polio', 'usia' => '0–4 bulan', 'keterangan' => 'Diberikan sesuai rangkaian jadwal imunisasi dasar.', 'warna' => 'green', 'ikon' => 'fa-syringe'],
    ['vaksin' => 'DPT-HB-Hib', 'usia' => '2–4 bulan', 'keterangan' => 'Perlindungan terhadap difteri, pertusis, tetanus, hepatitis B, dan Hib.', 'warna' => 'orange', 'ikon' => 'fa-heart-pulse'],
    ['vaksin' => 'Campak / MR', 'usia' => '9–18 bulan', 'keterangan' => 'Mencegah campak dan rubella pada anak.', 'warna' => 'purple', 'ikon' => 'fa-child-reaching'],
    ['vaksin' => 'Imunisasi Lanjutan', 'usia' => '18 bulan–kelas 6', 'keterangan' => 'Diberikan sesuai program dan jadwal pelayanan setempat.', 'warna' => 'teal', 'ikon' => 'fa-calendar-check'],
];

$pageTitle = "Imunisasi";
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<main class="container-fluid px-3 px-lg-4 py-4">
  <div class="page-heading mb-4 d-flex flex-wrap justify-content-between align-items-end gap-2">
    <div>
      <h4 class="fw-bold mb-1">Layanan Imunisasi</h4>
      <p class="text-muted small mb-0">Informasi imunisasi dasar dan lanjutan untuk balita Posyandu Desa Kemuning Tropodo.</p>
    </div>
    <a href="jadwal-posyandu.php" class="btn btn-outline-blue btn-sm"><i class="fa-solid fa-calendar-days me-1"></i> Lihat Jadwal Posyandu</a>
  </div>

  <div class="alert alert-info small mb-4"><i class="fa-solid fa-circle-info me-1"></i> Pastikan membawa buku KIA atau kartu imunisasi saat datang ke Posyandu. Konfirmasi jadwal kepada kader setempat sebelum pelayanan.</div>

  <div class="row g-3 mb-4">
    <?php foreach ($jadwalImunisasi as $item): ?>
      <div class="col-md-6 col-xl-4">
        <div class="chart-card h-100 immunization-card">
          <div class="d-flex align-items-start gap-3">
            <div class="immunization-icon immunization-<?= bersih($item['warna']) ?>"><i class="fa-solid <?= bersih($item['ikon']) ?>"></i></div>
            <div>
              <h6 class="fw-bold mb-1"><?= bersih($item['vaksin']) ?></h6>
              <span class="badge-status badge-normal"><i class="fa-solid fa-clock me-1"></i><?= bersih($item['usia']) ?></span>
            </div>
          </div>
          <p class="small text-muted mt-3 mb-0"><?= bersih($item['keterangan']) ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="row g-3">
    <div class="col-lg-7">
      <div class="chart-card h-100">
        <h6 class="fw-bold mb-3"><i class="fa-solid fa-list-check text-pink me-1"></i> Alur Pelayanan Imunisasi</h6>
        <div class="d-flex gap-3 mb-3"><div class="user-avatar">1</div><div><strong class="small">Daftar di meja pelayanan</strong><p class="small text-muted mb-0">Sampaikan identitas anak dan tunjukkan buku KIA.</p></div></div>
        <div class="d-flex gap-3 mb-3"><div class="user-avatar">2</div><div><strong class="small">Pemeriksaan kondisi anak</strong><p class="small text-muted mb-0">Petugas memeriksa kondisi umum dan riwayat imunisasi.</p></div></div>
        <div class="d-flex gap-3"><div class="user-avatar">3</div><div><strong class="small">Pencatatan pelayanan</strong><p class="small text-muted mb-0">Imunisasi yang diberikan dicatat pada buku KIA dan sistem.</p></div></div>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="chart-card h-100">
        <h6 class="fw-bold mb-3"><i class="fa-solid fa-triangle-exclamation text-pink me-1"></i> Perhatian</h6>
        <p class="small text-muted">Jadwal imunisasi dapat berbeda berdasarkan kondisi anak dan kebijakan pelayanan. Konsultasikan dengan bidan atau tenaga kesehatan apabila anak sedang sakit atau memiliki riwayat alergi.</p>
        <a href="jadwal-posyandu.php" class="btn btn-pink-solid btn-sm">Cek Jadwal Kegiatan</a>
      </div>
    </div>
  </div>
</main>

<?php require_once 'includes/footer.php'; ?>
