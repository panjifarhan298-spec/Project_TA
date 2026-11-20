<?php
require_once 'includes/auth.php';
require_once 'config/koneksi.php';
require_once 'includes/functions.php';
cekRole('admin');

// 1. Hitung total data Balita
$totalBalita = (int)mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM balita WHERE kategori='balita'"))['total'];

// 2. Hitung total Lansia
$cekLansia = mysqli_query($koneksi, "SHOW TABLES LIKE 'lansia'");
$totalLansia = 0;
$totalPemeriksaanLansia = 0;
if (mysqli_num_rows($cekLansia) > 0) {
    $rowL = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM lansia"));
    $totalLansia = (int)($rowL['total'] ?? 0);
    $cekRiwayatLansia = mysqli_query($koneksi, "SHOW TABLES LIKE 'pemeriksaan_lansia'");
    $sqlPemeriksaanLansia = $cekRiwayatLansia && mysqli_num_rows($cekRiwayatLansia) > 0
        ? "SELECT COUNT(*) AS total FROM pemeriksaan_lansia"
        : "SELECT COUNT(*) AS total FROM lansia WHERE tanggal_periksa IS NOT NULL";
    $rowPemeriksaanL = mysqli_fetch_assoc(mysqli_query($koneksi, $sqlPemeriksaanLansia));
    $totalPemeriksaanLansia = (int)($rowPemeriksaanL['total'] ?? 0);
} else {
    $rowL = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM balita WHERE kategori='lansia'"));
    $totalLansia = (int)($rowL['total'] ?? 0);
    $rowPemeriksaanL = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM balita WHERE kategori='lansia' AND tanggal_periksa IS NOT NULL"));
    $totalPemeriksaanLansia = (int)($rowPemeriksaanL['total'] ?? 0);
}

// 3. Hitung total Ibu Hamil
$cekBumil = mysqli_query($koneksi, "SHOW TABLES LIKE 'ibu_hamil'");
$totalBumil = 0;
$totalPemeriksaanBumil = 0;
if (mysqli_num_rows($cekBumil) > 0) {
    $rowB = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM ibu_hamil"));
    $totalBumil = (int)($rowB['total'] ?? 0);
    $cekRiwayatBumil = mysqli_query($koneksi, "SHOW TABLES LIKE 'pemeriksaan_ibu_hamil'");
    $sqlPemeriksaanBumil = $cekRiwayatBumil && mysqli_num_rows($cekRiwayatBumil) > 0
        ? "SELECT COUNT(*) AS total FROM pemeriksaan_ibu_hamil"
        : "SELECT COUNT(*) AS total FROM ibu_hamil WHERE tanggal_periksa IS NOT NULL";
    $rowPemeriksaanB = mysqli_fetch_assoc(mysqli_query($koneksi, $sqlPemeriksaanBumil));
    $totalPemeriksaanBumil = (int)($rowPemeriksaanB['total'] ?? 0);
} else {
    $rowB = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM balita WHERE kategori='ibu_hamil'"));
    $totalBumil = (int)($rowB['total'] ?? 0);
    $rowPemeriksaanB = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM balita WHERE kategori='ibu_hamil' AND tanggal_periksa IS NOT NULL"));
    $totalPemeriksaanBumil = (int)($rowPemeriksaanB['total'] ?? 0);
}

// 4. Hitung pemeriksaan Balita dari riwayat pengukuran
$totalPemeriksaanBalita = 0;
$cekPengukuran = mysqli_query($koneksi, "SHOW TABLES LIKE 'pengukuran'");
if (mysqli_num_rows($cekPengukuran) > 0) {
    $rowP = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pengukuran pk JOIN balita b ON b.id=pk.id_balita WHERE b.kategori='balita'"));
    $totalPemeriksaanBalita = (int)($rowP['total'] ?? 0);
}
// 5. Data untuk grafik perbandingan statistik warga per Posyandu
$labelPosyandu = [];
$dataBalitaP = [];
$resP = mysqli_query($koneksi, "SELECT p.nama, COUNT(b.id) AS jumlah FROM posyandu p LEFT JOIN balita b ON b.posyandu_id = p.id AND b.kategori='balita' GROUP BY p.id ORDER BY p.id ASC");
while ($row = mysqli_fetch_assoc($resP)) { 
    $labelPosyandu[] = $row['nama']; 
    $dataBalitaP[] = (int)$row['jumlah']; 
}

$pageTitle = "Dashboard Admin - Posyandu System";
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<main class="container-fluid px-3 px-lg-4 pb-4">

  <!-- Statistik warga dan pemeriksaan per kategori -->
  <div class="row g-3 mb-4">
    <!-- Card 1: Total Balita -->
    <div class="col-12 col-sm-6 col-xl-4">
      <div class="stat-card-custom stat-card-blue h-100 p-3 bg-white rounded-3 shadow-sm border">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">Total Balita</span>
          <div class="stat-icon-circle icon-circle-blue">
            <i class="fa-solid fa-baby"></i>
          </div>
        </div>
        <div class="stat-number-large mb-1" style="color: #123B70; font-size: 2.1rem; font-weight: 700;"><?= $totalBalita ?></div>
        <div class="text-success small fw-semibold mt-2 d-flex align-items-center gap-1">
          <i class="fa-solid fa-circle-check"></i> <span>Data terpantau aktif</span>
        </div>
      </div>
    </div>

    <!-- Card 2: Total Ibu / Hamil -->
    <div class="col-12 col-sm-6 col-xl-4">
      <div class="stat-card-custom stat-card-green h-100 p-3 bg-white rounded-3 shadow-sm border">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">Total Ibu Hamil</span>
          <div class="stat-icon-circle icon-circle-green">
            <i class="fa-solid fa-person-pregnant"></i>
          </div>
        </div>
        <div class="stat-number-large mb-1" style="color: #123B70; font-size: 2.1rem; font-weight: 700;"><?= $totalBumil ?></div>
        <div class="text-success small fw-semibold mt-2 d-flex align-items-center gap-1">
          <i class="fa-solid fa-circle-check"></i> <span>Data terpantau aktif</span>
        </div>
      </div>
    </div>

    <!-- Card 3: Total Lansia -->
    <div class="col-12 col-sm-6 col-xl-4">
      <div class="stat-card-custom stat-card-amber h-100 p-3 bg-white rounded-3 shadow-sm border">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">Total Lansia</span>
          <div class="stat-icon-circle icon-circle-amber">
            <i class="fa-solid fa-person-cane"></i>
          </div>
        </div>
        <div class="stat-number-large mb-1" style="color: #123B70; font-size: 2.1rem; font-weight: 700;"><?= $totalLansia ?></div>
        <div class="text-success small fw-semibold mt-2 d-flex align-items-center gap-1">
          <i class="fa-solid fa-circle-check"></i> <span>Data terpantau aktif</span>
        </div>
      </div>
    </div>

    <!-- Pemeriksaan Balita -->
    <div class="col-12 col-sm-6 col-xl-4">
      <div class="stat-card-custom stat-card-cyan h-100 p-3 bg-white rounded-3 shadow-sm border">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">Pemeriksaan Balita</span>
          <div class="stat-icon-circle icon-circle-cyan">
            <i class="fa-solid fa-child-reaching"></i>
          </div>
        </div>
        <div class="stat-number-large mb-1" style="color: #123B70; font-size: 2.1rem; font-weight: 700;"><?= $totalPemeriksaanBalita ?></div>
        <div class="text-success small fw-semibold mt-2 d-flex align-items-center gap-1">
          <i class="fa-solid fa-circle-check"></i> <span>Riwayat pengukuran balita</span>
        </div>
      </div>
    </div>

    <!-- Pemeriksaan Ibu Hamil -->
    <div class="col-12 col-sm-6 col-xl-4">
      <div class="stat-card-custom stat-card-green h-100 p-3 bg-white rounded-3 shadow-sm border">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">Pemeriksaan Ibu Hamil</span>
          <div class="stat-icon-circle icon-circle-green">
            <i class="fa-solid fa-person-pregnant"></i>
          </div>
        </div>
        <div class="stat-number-large mb-1" style="color: #123B70; font-size: 2.1rem; font-weight: 700;"><?= $totalPemeriksaanBumil ?></div>
        <div class="text-success small fw-semibold mt-2 d-flex align-items-center gap-1">
          <i class="fa-solid fa-circle-check"></i> <span>Data dengan tanggal pemeriksaan</span>
        </div>
      </div>
    </div>

    <!-- Pemeriksaan Lansia -->
    <div class="col-12 col-sm-6 col-xl-4">
      <div class="stat-card-custom stat-card-amber h-100 p-3 bg-white rounded-3 shadow-sm border">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">Pemeriksaan Lansia</span>
          <div class="stat-icon-circle icon-circle-amber">
            <i class="fa-solid fa-person-cane"></i>
          </div>
        </div>
        <div class="stat-number-large mb-1" style="color: #123B70; font-size: 2.1rem; font-weight: 700;"><?= $totalPemeriksaanLansia ?></div>
        <div class="text-success small fw-semibold mt-2 d-flex align-items-center gap-1">
          <i class="fa-solid fa-circle-check"></i> <span>Data dengan tanggal pemeriksaan</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Konten Utama Dashboard Persis Seperti Gambar 1 (Kiri: Grafik, Kanan: Akses Cepat) -->
  <div class="row g-3">
    <!-- Kolom Kiri: Statistik Data Warga -->
    <div class="col-12 col-lg-8">
      <div class="chart-card bg-white p-4 rounded-3 shadow-sm border h-100">
        <div class="d-flex align-items-center gap-2 mb-1">
          <i class="fa-solid fa-chart-pie" style="color: #60A5FA; font-size: 1.15rem;"></i>
          <h5 class="fw-bold mb-0" style="color: #123B70;">Statistik Data Warga</h5>
        </div>
        <p class="text-muted small mb-4">Jumlah warga dan pemeriksaan Balita, Ibu Hamil, serta Lansia di Posyandu Desa Tropodo.</p>
        <canvas id="chartStatistikWarga" height="230"></canvas>
      </div>
    </div>

    <!-- Kolom Kanan: Akses Cepat -->
    <div class="col-12 col-lg-4">
      <div class="chart-card bg-white p-4 rounded-3 shadow-sm border h-100">
        <div class="d-flex align-items-center gap-2 mb-3">
          <span class="fs-5" style="color: #F59E0B;">⚡</span>
          <h5 class="fw-bold mb-0" style="color: #123B70;">Akses Cepat</h5>
        </div>

        <div class="d-flex flex-column gap-3 mt-4">
          <a href="imunisasi.php" class="btn btn-quick-access btn-quick-blue">
            <i class="fa-solid fa-syringe me-2"></i> Kelola Imunisasi
          </a>

          <a href="jadwal-posyandu.php" class="btn btn-quick-access btn-quick-green">
            <i class="fa-solid fa-calendar-days me-2"></i> Jadwal Kegiatan Posyandu
          </a>

          <a href="laporan.php" class="btn btn-quick-access btn-quick-cyan">
            <i class="fa-solid fa-file-lines me-2"></i> Lihat Laporan Pelayanan
          </a>

          <a href="data-posyandu.php" class="btn btn-quick-access btn-quick-navy">
            <i class="fa-solid fa-house-medical me-2"></i> Data 8 Posyandu Tropodo
          </a>
        </div>
      </div>
    </div>
  </div>

</main>

<?php require_once 'includes/footer.php'; ?>

<script>
const ctx = document.getElementById('chartStatistikWarga');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ['Balita', 'Ibu Hamil', 'Lansia', 'Periksa Balita', 'Periksa Bumil', 'Periksa Lansia'],
    datasets: [{
      label: 'Jumlah Warga / Pemeriksaan',
      data: [<?= $totalBalita ?>, <?= $totalBumil ?>, <?= $totalLansia ?>, <?= $totalPemeriksaanBalita ?>, <?= $totalPemeriksaanBumil ?>, <?= $totalPemeriksaanLansia ?>],
      backgroundColor: [
        '#60A5FA', // Biru muda
        '#10B981', // Hijau
        '#F59E0B', // Kuning/Amber
        '#06B6D4', // Cyan
        '#059669', // Hijau tua
        '#D97706'  // Oranye tua
      ],
      borderRadius: 8,
      maxBarThickness: 45
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false }
    },
    scales: {
      y: {
        beginAtZero: true,
        suggestedMax: 10,
        ticks: { precision: 0 }
      }
    }
  }
});
</script>
