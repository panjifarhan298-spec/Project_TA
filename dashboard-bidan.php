<?php
require_once 'includes/auth.php';
require_once 'config/koneksi.php';
require_once 'includes/functions.php';
cekRoleAdminAtauBidan();

$posyanduTerpilih = isset($_GET['posyandu_id']) ? (int)$_GET['posyandu_id'] : 0;

$whereFilter = "WHERE b.kategori='balita'";
if ($posyanduTerpilih > 0) {
    $whereFilter .= " AND b.posyandu_id = " . intval($posyanduTerpilih);
}

// 1. Total Balita
$totalBalita = (int)mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM balita b $whereFilter"))['total'];

// 2. Total Lansia
$cekLansia = mysqli_query($koneksi, "SHOW TABLES LIKE 'lansia'");
$totalLansia = 0;
$totalPemeriksaanLansia = 0;
if (mysqli_num_rows($cekLansia) > 0) {
    $wL = $posyanduTerpilih > 0 ? "WHERE posyandu_id=" . intval($posyanduTerpilih) : "";
    $rowL = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM lansia $wL"));
    $totalLansia = (int)($rowL['total'] ?? 0);
    $cekRiwayatLansia = mysqli_query($koneksi, "SHOW TABLES LIKE 'pemeriksaan_lansia'");
    if ($cekRiwayatLansia && mysqli_num_rows($cekRiwayatLansia) > 0) {
        $filterPemeriksaanLansia = $posyanduTerpilih > 0 ? "WHERE l.posyandu_id=" . intval($posyanduTerpilih) : "";
        $sqlPemeriksaanLansia = "SELECT COUNT(*) AS total FROM pemeriksaan_lansia pl JOIN lansia l ON l.id=pl.lansia_id $filterPemeriksaanLansia";
    } else {
        $wPemeriksaanLansia = "WHERE tanggal_periksa IS NOT NULL" . ($posyanduTerpilih > 0 ? " AND posyandu_id=" . intval($posyanduTerpilih) : "");
        $sqlPemeriksaanLansia = "SELECT COUNT(*) AS total FROM lansia $wPemeriksaanLansia";
    }
    $rowPemeriksaanL = mysqli_fetch_assoc(mysqli_query($koneksi, $sqlPemeriksaanLansia));
    $totalPemeriksaanLansia = (int)($rowPemeriksaanL['total'] ?? 0);
} else {
    $wL = "WHERE kategori='lansia'" . ($posyanduTerpilih > 0 ? " AND posyandu_id=" . intval($posyanduTerpilih) : "");
    $rowL = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM balita $wL"));
    $totalLansia = (int)($rowL['total'] ?? 0);
    $wPemeriksaanLansia = "WHERE kategori='lansia' AND tanggal_periksa IS NOT NULL" . ($posyanduTerpilih > 0 ? " AND posyandu_id=" . intval($posyanduTerpilih) : "");
    $rowPemeriksaanL = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM balita $wPemeriksaanLansia"));
    $totalPemeriksaanLansia = (int)($rowPemeriksaanL['total'] ?? 0);
}

// 3. Total Ibu Hamil
$cekBumil = mysqli_query($koneksi, "SHOW TABLES LIKE 'ibu_hamil'");
$totalBumil = 0;
$totalPemeriksaanBumil = 0;
if (mysqli_num_rows($cekBumil) > 0) {
    $wB = $posyanduTerpilih > 0 ? "WHERE posyandu_id=" . intval($posyanduTerpilih) : "";
    $rowB = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM ibu_hamil $wB"));
    $totalBumil = (int)($rowB['total'] ?? 0);
    $cekRiwayatBumil = mysqli_query($koneksi, "SHOW TABLES LIKE 'pemeriksaan_ibu_hamil'");
    if ($cekRiwayatBumil && mysqli_num_rows($cekRiwayatBumil) > 0) {
        $filterPemeriksaanBumil = $posyanduTerpilih > 0 ? "WHERE ih.posyandu_id=" . intval($posyanduTerpilih) : "";
        $sqlPemeriksaanBumil = "SELECT COUNT(*) AS total FROM pemeriksaan_ibu_hamil ph JOIN ibu_hamil ih ON ih.id=ph.ibu_hamil_id $filterPemeriksaanBumil";
    } else {
        $wPemeriksaanBumil = "WHERE tanggal_periksa IS NOT NULL" . ($posyanduTerpilih > 0 ? " AND posyandu_id=" . intval($posyanduTerpilih) : "");
        $sqlPemeriksaanBumil = "SELECT COUNT(*) AS total FROM ibu_hamil $wPemeriksaanBumil";
    }
    $rowPemeriksaanB = mysqli_fetch_assoc(mysqli_query($koneksi, $sqlPemeriksaanBumil));
    $totalPemeriksaanBumil = (int)($rowPemeriksaanB['total'] ?? 0);
} else {
    $wB = "WHERE kategori='ibu_hamil'" . ($posyanduTerpilih > 0 ? " AND posyandu_id=" . intval($posyanduTerpilih) : "");
    $rowB = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM balita $wB"));
    $totalBumil = (int)($rowB['total'] ?? 0);
    $wPemeriksaanBumil = "WHERE kategori='ibu_hamil' AND tanggal_periksa IS NOT NULL" . ($posyanduTerpilih > 0 ? " AND posyandu_id=" . intval($posyanduTerpilih) : "");
    $rowPemeriksaanB = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM balita $wPemeriksaanBumil"));
    $totalPemeriksaanBumil = (int)($rowPemeriksaanB['total'] ?? 0);
}

// 4. Pemeriksaan Balita
$totalPemeriksaanBalita = 0;
$cekPengukuran = mysqli_query($koneksi, "SHOW TABLES LIKE 'pengukuran'");
if (mysqli_num_rows($cekPengukuran) > 0) {
    $filterPemeriksaan = "WHERE b.kategori='balita'" . ($posyanduTerpilih > 0 ? " AND b.posyandu_id=" . intval($posyanduTerpilih) : "");
    $rowP = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pengukuran pk JOIN balita b ON b.id=pk.id_balita $filterPemeriksaan"));
    $totalPemeriksaanBalita = (int)($rowP['total'] ?? 0);
}
$daftarPosyandu = [];
$resP = mysqli_query($koneksi, "SELECT id, nama FROM posyandu ORDER BY id ASC");
while ($row = mysqli_fetch_assoc($resP)) $daftarPosyandu[] = $row;

$pageTitle = (($_SESSION['role'] ?? '') === 'bidan' ? "Dashboard Bidan" : "Dashboard Petugas") . " - Posyandu System";
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
        <p class="text-muted small mb-4">Jumlah warga dan pemeriksaan Balita, Ibu Hamil, serta Lansia di Posyandu Desa Kemuning Tropodo.</p>
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
            <i class="fa-solid fa-house-medical me-2"></i> Data 8 Posyandu Desa Kemuning Tropodo
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
