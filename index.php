<?php
require_once 'config/koneksi.php';
require_once 'includes/functions.php';

// Cek apakah tabel lansia dan ibu_hamil sudah ada
$cekL = mysqli_query($koneksi, "SHOW TABLES LIKE 'lansia'");
$hasLansiaTbl = mysqli_num_rows($cekL) > 0;
$cekB = mysqli_query($koneksi, "SHOW TABLES LIKE 'ibu_hamil'");
$hasBumilTbl = mysqli_num_rows($cekB) > 0;

$posyanduList = [];
$sqlP = "SELECT p.id, p.nama, p.alamat,
                SUM(CASE WHEN b.kategori='balita' THEN 1 ELSE 0 END) AS total,
                SUM(CASE WHEN b.kategori='balita' AND b.status='Normal' THEN 1 ELSE 0 END) AS normal,
                SUM(CASE WHEN b.kategori='balita' AND b.status='Berisiko' THEN 1 ELSE 0 END) AS berisiko,
                SUM(CASE WHEN b.kategori='balita' AND b.status='Stunting' THEN 1 ELSE 0 END) AS stunting
         FROM posyandu p
         LEFT JOIN balita b ON b.posyandu_id = p.id
         GROUP BY p.id ORDER BY p.id ASC";
$result = mysqli_query($koneksi, $sqlP);
while ($row = mysqli_fetch_assoc($result)) {
    $pid = $row['id'];
    
    // Hitung bumil per posyandu
    if ($hasBumilTbl) {
        $c = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM ibu_hamil WHERE posyandu_id = $pid"));
        $row['ibu_hamil'] = (int)($c['total'] ?? 0);
    } else {
        $c = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM balita WHERE kategori='ibu_hamil' AND posyandu_id = $pid"));
        $row['ibu_hamil'] = (int)($c['total'] ?? 0);
    }

    // Hitung lansia per posyandu
    if ($hasLansiaTbl) {
        $c = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM lansia WHERE posyandu_id = $pid"));
        $row['lansia'] = (int)($c['total'] ?? 0);
    } else {
        $c = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM balita WHERE kategori='lansia' AND posyandu_id = $pid"));
        $row['lansia'] = (int)($c['total'] ?? 0);
    }

    $posyanduList[] = $row;
}

// Rata-rata berat & tinggi untuk grafik per posyandu
$grafikPosyandu = [];
foreach ($posyanduList as $p) {
    $grafikPosyandu[$p['id']] = ['label' => [], 'berat' => [], 'tinggi' => []];
}

$sqlG = "SELECT b.posyandu_id, pk.tanggal,
                AVG(pk.berat) AS rerata_berat, AVG(pk.tinggi) AS rerata_tinggi
         FROM pengukuran pk
         JOIN balita b ON b.id = pk.id_balita
         WHERE b.kategori = 'balita'
         GROUP BY b.posyandu_id, pk.tanggal
         ORDER BY pk.tanggal ASC";
$resG = mysqli_query($koneksi, $sqlG);
if ($resG) {
    while ($row = mysqli_fetch_assoc($resG)) {
        $pid = $row['posyandu_id'];
        if (!isset($grafikPosyandu[$pid])) continue;
        $grafikPosyandu[$pid]['label'][] = date('d M Y', strtotime($row['tanggal']));
        $grafikPosyandu[$pid]['berat'][] = round($row['rerata_berat'], 1);
        $grafikPosyandu[$pid]['tinggi'][] = round($row['rerata_tinggi'], 1);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Posyandu Desa Tropodo - Sistem Informasi Kesehatan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="assets/css/style.css?v=20261117logo">
</head>
<body class="landing-body">

<div class="hero-splash-screen" id="heroSplashScreen" role="status" aria-label="Memuat Posyandu Tropodo">
  <div class="hero-splash-logo">
    <img src="assets/img/logo-sidoarjo.png" alt="Lambang Kabupaten Sidoarjo">
  </div>
  <div class="hero-splash-brand">Posyandu Tropodo</div>
  <div class="hero-splash-place">Sistem Informasi Posyandu Desa Tropodo</div>
  <div class="hero-splash-loading"><span></span><span></span><span></span></div>
  <div class="hero-splash-progress" aria-hidden="true"><span></span></div>
</div>

<nav class="navbar navbar-expand-lg landing-navbar sticky-top">
  <div class="container">
    <a class="navbar-brand app-brand" href="index.php" style="color: #123B70 !important;">
      <img class="app-brand-logo" src="assets/img/logo-sidoarjo.png" alt="Lambang Kabupaten Sidoarjo">
      <span>Posyandu Tropodo</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navLanding">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navLanding">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item"><a class="nav-link" href="#kategori">Layanan Data</a></li>
        <li class="nav-item"><a class="nav-link" href="#posyandu">8 Posyandu</a></li>
        <li class="nav-item"><a class="nav-link" href="#tentang">Tentang</a></li>
      </ul>
    </div>
    <a href="login.php" class="btn btn-login-outline btn-sm landing-login-top ms-lg-3">
      <i class="fa-solid fa-right-to-bracket me-1"></i> Masuk ke Sistem
    </a>
  </div>
</nav>

<header class="hero-section text-center">
  <div class="container py-5">
    <div class="hero-splash-animation" aria-label="Posyandu Desa Tropodo">
      <div class="hero-splash-orbit"></div>
      <div class="hero-icon mb-3"><i class="fa-solid fa-hospital-user"></i></div>
      <h1 class="fw-bold hero-title">Sistem Informasi Posyandu</h1>
      <h2 class="fw-semibold hero-subtitle mb-3">Desa Tropodo</h2>
      <p class="hero-tagline mx-auto">Pengelolaan dan pemantauan terintegrasi untuk Balita, Ibu Hamil, dan Lansia di seluruh wilayah Desa Tropodo.</p>
      <div class="hero-splash-dots" aria-hidden="true"><span></span><span></span><span></span></div>
    </div>
  </div>
</header>

<!-- ================= TIGA KATEGORI LAYANAN ================= -->
<section id="kategori" class="py-5 bg-white">
  <div class="container">
    <div class="text-center mb-4">
      <span class="section-badge">Tiga Kategori Utama</span>
      <h3 class="fw-bold mt-2" style="color: #123B70;">Layanan Terpisah & Terfokus</h3>
      <p class="text-muted">Pemisahan data mandiri untuk mendukung pencatatan kesehatan yang akurat dan terorganisir.</p>
    </div>
    <div class="row g-4">
      <!-- Card Balita -->
      <div class="col-md-4">
        <div class="stat-card stat-card-balita h-100 text-center p-4">
          <div class="stat-icon mb-3 mx-auto" style="width: 60px; height: 60px; font-size: 1.5rem; background: linear-gradient(135deg, #60A5FA, #123B70);">
            <i class="fa-solid fa-child-reaching"></i>
          </div>
          <h5 class="fw-bold mb-2" style="color: #123B70;">Data Balita</h5>
          <p class="text-muted small mb-3">Pemantauan berat badan, tinggi badan, status gizi (Normal, Berisiko, Stunting), serta grafik pertumbuhan balita.</p>
          <a href="data-balita.php" class="btn btn-outline-blue btn-sm px-3 rounded-pill">Buka Data Balita <i class="fa-solid fa-arrow-right ms-1"></i></a>
        </div>
      </div>
      <!-- Card Ibu Hamil -->
      <div class="col-md-4">
        <div class="stat-card stat-card-bumil h-100 text-center p-4">
          <div class="stat-icon mb-3 mx-auto" style="width: 60px; height: 60px; font-size: 1.5rem; background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
            <i class="fa-solid fa-person-pregnant"></i>
          </div>
          <h5 class="fw-bold mb-2" style="color: #123B70;">Data Ibu Hamil</h5>
          <p class="text-muted small mb-3">Pencatatan usia kehamilan, perkiraan lahir (HPL), tekanan darah, dan pemeriksaan rutin ibu hamil.</p>
          <a href="data-ibu-hamil.php" class="btn btn-outline-blue btn-sm px-3 rounded-pill">Buka Data Ibu Hamil <i class="fa-solid fa-arrow-right ms-1"></i></a>
        </div>
      </div>
      <!-- Card Lansia -->
      <div class="col-md-4">
        <div class="stat-card stat-card-lansia h-100 text-center p-4">
          <div class="stat-icon mb-3 mx-auto" style="width: 60px; height: 60px; font-size: 1.5rem; background: linear-gradient(135deg, #93C5FD, #1E40AF);">
            <i class="fa-solid fa-person-cane"></i>
          </div>
          <h5 class="fw-bold mb-2" style="color: #123B70;">Data Lansia</h5>
          <p class="text-muted small mb-3">Monitoring tekanan darah, berat & tinggi badan, serta riwayat pemeriksaan kesehatan lansia secara berkala.</p>
          <a href="data-lansia.php" class="btn btn-outline-blue btn-sm px-3 rounded-pill">Buka Data Lansia <i class="fa-solid fa-arrow-right ms-1"></i></a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ================= 8 POSYANDU ================= -->
<section id="posyandu" class="py-5" style="background: #F0F7FF;">
  <div class="container">
    <div class="text-center mb-4">
      <span class="section-badge">Wilayah Pelayanan</span>
      <h3 class="fw-bold mt-2" style="color: #123B70;">8 Posyandu Aktif di Desa Tropodo</h3>
      <p class="text-muted">Klik pada kartu Posyandu untuk melihat ringkasan statistik dan grafik pertumbuhan wilayah.</p>
    </div>
    <div class="row g-3 g-lg-4">
      <?php
      $ikonList = ['fa-house-chimney-medical','fa-hand-holding-medical','fa-baby','fa-heart-pulse','fa-syringe','fa-weight-scale','fa-ruler-vertical','fa-child'];
      foreach ($posyanduList as $i => $p): ?>
        <div class="col-6 col-md-4 col-lg-3">
          <div class="card posyandu-landing-card h-100 text-center posyandu-clickable"
               role="button" tabindex="0"
               data-bs-toggle="modal" data-bs-target="#modalPosyandu<?= $p['id'] ?>">
            <div class="card-body">
              <div class="posyandu-icon mb-2">
                <i class="fa-solid <?= $ikonList[$i % count($ikonList)] ?>"></i>
              </div>
              <h6 class="fw-bold mb-1" style="color: #123B70;"><?= bersih($p['nama']) ?></h6>
              <p class="small text-muted mb-2"><?= bersih($p['alamat']) ?></p>
              <div class="posyandu-category-counts" aria-label="Ringkasan data">
                <span class="badge-status badge-normal"><i class="fa-solid fa-child-reaching me-1"></i><?= (int)$p['total'] ?> Balita</span>
                <span class="badge-status badge-ibu-hamil"><i class="fa-solid fa-person-pregnant me-1"></i><?= (int)$p['ibu_hamil'] ?> Bumil</span>
                <span class="badge-status badge-lansia"><i class="fa-solid fa-person-cane me-1"></i><?= (int)$p['lansia'] ?> Lansia</span>
              </div>
              <div class="posyandu-data-links mt-2">
                <a href="data-balita.php?posyandu_id=<?= (int)$p['id'] ?>" class="btn btn-data-link btn-data-balita" onclick="event.stopPropagation()">Balita</a>
                <a href="data-ibu-hamil.php?posyandu_id=<?= (int)$p['id'] ?>" class="btn btn-data-link btn-data-ibu" onclick="event.stopPropagation()">Bumil</a>
                <a href="data-lansia.php?posyandu_id=<?= (int)$p['id'] ?>" class="btn btn-data-link btn-data-lansia" onclick="event.stopPropagation()">Lansia</a>
              </div>
              <p class="small mt-2 mb-0 fw-semibold" style="color: #60A5FA;"><i class="fa-solid fa-chart-simple me-1"></i>Lihat Detail</p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>

      <?php if (empty($posyanduList)): ?>
        <div class="col-12 text-center text-muted">
          Data Posyandu belum tersedia. Pastikan database sudah di-import.
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- ================= MODAL GRAFIK & DATA PER POSYANDU ================= -->
<?php foreach ($posyanduList as $p): ?>
<div class="modal fade" id="modalPosyandu<?= $p['id'] ?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content posyandu-modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" style="color: #123B70;">
          <i class="fa-solid fa-house-medical me-2" style="color: #60A5FA;"></i> <?= bersih($p['nama']) ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="small text-muted mb-3"><?= bersih($p['alamat']) ?></p>

        <div class="row g-2 mb-3 text-center">
          <div class="col-3">
            <div class="mini-stat">
              <div class="fw-bold fs-5" style="color: #123B70;"><?= (int)$p['total'] ?></div>
              <div class="small text-muted">Total Balita</div>
            </div>
          </div>
          <div class="col-3">
            <div class="mini-stat">
              <div class="fw-bold fs-5 text-success"><?= (int)$p['normal'] ?></div>
              <div class="small text-muted">Normal</div>
            </div>
          </div>
          <div class="col-3">
            <div class="mini-stat">
              <div class="fw-bold fs-5" style="color:#d97706;"><?= (int)$p['berisiko'] ?></div>
              <div class="small text-muted">Berisiko</div>
            </div>
          </div>
          <div class="col-3">
            <div class="mini-stat">
              <div class="fw-bold fs-5 text-danger"><?= (int)$p['stunting'] ?></div>
              <div class="small text-muted">Stunting</div>
            </div>
          </div>
        </div>

        <div class="row g-2 mb-4 text-center posyandu-extra-stats">
          <div class="col-6">
            <div class="mini-stat mini-stat-pregnant">
              <div class="fw-bold fs-5" style="color: #2563EB;"><?= (int)$p['ibu_hamil'] ?></div>
              <div class="small text-muted">Total Ibu Hamil</div>
            </div>
          </div>
          <div class="col-6">
            <div class="mini-stat mini-stat-elderly">
              <div class="fw-bold fs-5" style="color: #1E40AF;"><?= (int)$p['lansia'] ?></div>
              <div class="small text-muted">Total Lansia</div>
            </div>
          </div>
        </div>

        <?php if (!empty($grafikPosyandu[$p['id']]['label'])): ?>
          <h6 class="fw-bold mb-2" style="color: #123B70;">Grafik Pertumbuhan Balita (Rata-rata)</h6>
          <canvas id="modalChart<?= $p['id'] ?>" height="160"></canvas>
        <?php else: ?>
          <div class="text-center text-muted py-3 small">
            Belum ada data riwayat pengukuran balita untuk Posyandu ini.
          </div>
        <?php endif; ?>
      </div>
      <div class="modal-footer d-flex justify-content-between">
        <div class="d-flex gap-2">
          <a href="data-balita.php?posyandu_id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-outline-primary">Lihat Balita</a>
          <a href="data-ibu-hamil.php?posyandu_id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-outline-primary">Lihat Bumil</a>
          <a href="data-lansia.php?posyandu_id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-outline-primary">Lihat Lansia</a>
        </div>
        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
<?php endforeach; ?>

<!-- ================= TENTANG ================= -->
<section id="tentang" class="py-5 tentang-section">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-md-6">
        <span class="section-badge">Tentang Posyandu</span>
        <h3 class="fw-bold mt-2" style="color: #123B70;">Sistem Informasi Posyandu Desa Tropodo</h3>
        <p class="text-muted">Aplikasi ini hadir untuk membantu para kader dan bidan desa dalam memantau kesehatan seluruh warga dari berbagai generasi secara terintegrasi:</p>
        <ul class="list-unstyled tentang-list">
          <li><i class="fa-solid fa-circle-check text-primary"></i> <strong>Data Balita:</strong> Pemantauan status gizi dan deteksi dini risiko stunting.</li>
          <li><i class="fa-solid fa-circle-check text-primary"></i> <strong>Data Ibu Hamil:</strong> Pengawalan usia kehamilan dan hari perkiraan lahir (HPL).</li>
          <li><i class="fa-solid fa-circle-check text-primary"></i> <strong>Data Lansia:</strong> Pemeriksaan berkala tekanan darah dan kondisi fisik lansia.</li>
        </ul>
      </div>
      <div class="col-md-6 text-center">
        <div class="tentang-illustration mx-auto shadow-sm" style="background: linear-gradient(135deg, #E0EFFE, #BFDBFE); color: #123B70;">
          <i class="fa-solid fa-hospital-user"></i>
        </div>
      </div>
    </div>
  </div>
</section>

<footer class="app-footer text-center py-4 bg-white border-top">
  <div class="container">
    <p class="mb-0 small text-muted">
      &copy; <?= date('Y') ?> Posyandu Desa Tropodo &mdash; Sistem Informasi Posyandu Terpadu.
    </p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script src="assets/js/script.js?v=20261117blue"></script>
<script>
const grafikData = <?= json_encode($grafikPosyandu) ?>;

// Inisialisasi chart modal saat dibuka
document.querySelectorAll('[id^="modalPosyandu"]').forEach(function(modalEl) {
  modalEl.addEventListener('shown.bs.modal', function () {
    const pid = this.id.replace('modalPosyandu', '');
    const canvasId = 'modalChart' + pid;
    const canvas = document.getElementById(canvasId);
    if (!canvas || canvas.dataset.rendered) return;

    const data = grafikData[pid];
    if (!data || !data.label.length) return;

    new Chart(canvas, {
      type: 'line',
      data: {
        labels: data.label,
        datasets: [
          { label: 'Berat (kg)', data: data.berat, yAxisID: 'yBerat', borderColor: '#60A5FA', backgroundColor: 'rgba(96,165,250,0.12)', tension: 0.35, fill: false },
          { label: 'Tinggi (cm)', data: data.tinggi, yAxisID: 'yTinggi', borderColor: '#123B70', backgroundColor: 'rgba(18,59,112,0.12)', tension: 0.35, fill: false }
        ]
      },
      options: {
        responsive: true,
        scales: {
          x: { title: { display: true, text: 'Tanggal pengukuran' } },
          yBerat: {
            type: 'linear',
            position: 'left',
            title: { display: true, text: 'Berat (kg)' }
          },
          yTinggi: {
            type: 'linear',
            position: 'right',
            grid: { drawOnChartArea: false },
            title: { display: true, text: 'Tinggi (cm)' }
          }
        }
      }
    });
    canvas.dataset.rendered = "true";
  });
});
</script>
</body>
</html>
