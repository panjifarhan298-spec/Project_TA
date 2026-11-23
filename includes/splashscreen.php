<?php
require_once 'config/koneksi.php';
require_once 'includes/functions.php';

// Data dasar + rekap status gizi tiap Posyandu (hanya data agregat/statistik,
// TIDAK menampilkan nama/NIK/foto balita di halaman publik ini)
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
    $posyanduList[] = $row;
}

// Rata-rata berat & tinggi (per tanggal pengukuran) untuk grafik tren tiap Posyandu
$grafikPosyandu = []; // [posyandu_id => ['label'=>[], 'berat'=>[], 'tinggi'=>[]]]
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
while ($row = mysqli_fetch_assoc($resG)) {
    $pid = $row['posyandu_id'];
    if (!isset($grafikPosyandu[$pid])) continue;
    $grafikPosyandu[$pid]['label'][] = date('d M Y', strtotime($row['tanggal']));
    $grafikPosyandu[$pid]['berat'][] = round($row['rerata_berat'], 1);
    $grafikPosyandu[$pid]['tinggi'][] = round($row['rerata_tinggi'], 1);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Posyandu Desa Kemuning Tropodo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="assets/css/style.css?v=20261117logo">

<style>
/* Splash screen hero mandiri untuk file index.php */
.hero-section { position: relative; min-height: 260px; overflow: hidden; }
body.splash-active { overflow: hidden; }
.hero-splash-screen { position: fixed; inset: 0; z-index: 9999; width: 100vw; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; overflow: hidden; background: linear-gradient(135deg,#fbd8ef 0%,#f4eafb 48%,#d9f0f7 100%); transition: opacity .65s ease, visibility .65s ease; animation: splashScene .8s ease-out both; }
.hero-splash-screen.is-hidden { opacity: 0; visibility: hidden; pointer-events: none; }
.hero-splash-screen:before { content: ''; position: absolute; top: -30%; left: -35%; width: 25%; height: 170%; transform: rotate(22deg); background: linear-gradient(90deg,transparent,rgba(255,255,255,.8),transparent); animation: splashShine 2.1s .3s ease-in-out both; }
.hero-splash-logo { position: relative; width: 112px; height: 112px; display: flex; align-items: center; justify-content: center; animation: splashLogo 1.5s ease-in-out infinite; }
.hero-splash-logo img { display: block; width: 100%; height: 100%; object-fit: contain; filter: drop-shadow(0 10px 14px rgba(18,59,112,.2)); }
.hero-splash-logo:after { content: ''; position: absolute; inset: -9px; border: 2px solid rgba(255,255,255,.7); border-radius: 50%; animation: splashRing 1.5s ease-out infinite; }
.hero-splash-brand { margin-top: 12px; color: #17151b; font: 700 1.5rem Poppins,Arial,sans-serif; animation: splashText .7s .22s both; }
.hero-splash-place { color: #bf5ea6; font: 600 .82rem Poppins,Arial,sans-serif; animation: splashText .7s .4s both; }
.hero-splash-loading { display: flex; gap: 5px; margin-top: 18px; }
.hero-splash-loading span { width: 7px; height: 7px; border-radius: 50%; background: #bf5ea6; animation: splashDot 1s ease-in-out infinite; }
.hero-splash-loading span:nth-child(2) { animation-delay: .15s; background: #a98ad6; }
.hero-splash-loading span:nth-child(3) { animation-delay: .3s; background: #3e8ea8; }
.hero-splash-progress { width: 140px; height: 3px; margin-top: 14px; overflow: hidden; border-radius: 99px; background: rgba(255,255,255,.75); animation: splashText .6s .55s both; }
.hero-splash-progress span { display: block; width: 0; height: 100%; background: linear-gradient(90deg,#bf5ea6,#3e8ea8); animation: splashProgress 1.8s .55s ease-in-out forwards; }
@keyframes splashScene { from { opacity:0; transform:scale(1.04); } to { opacity:1; transform:scale(1); } }
@keyframes splashLogo { 0%,100% { transform:translateY(0) scale(1); } 50% { transform:translateY(-8px) scale(1.08); } }
@keyframes splashRing { 0% { opacity:.8; transform:scale(.85); } 100% { opacity:0; transform:scale(1.35); } }
@keyframes splashText { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
@keyframes splashDot { 0%,80%,100% { transform:translateY(0); opacity:.55; } 40% { transform:translateY(-5px); opacity:1; } }
@keyframes splashProgress { from { width:0; } to { width:100%; } }
@keyframes splashShine { from { left:-35%; } to { left:125%; } }
@media (prefers-reduced-motion: reduce) { .hero-splash-screen,.hero-splash-screen:before,.hero-splash-logo,.hero-splash-logo:after,.hero-splash-brand,.hero-splash-place,.hero-splash-loading span,.hero-splash-progress,.hero-splash-progress span { animation:none; } }
</style>
</head>
<body class="landing-body">

<nav class="navbar navbar-expand-lg landing-navbar sticky-top">
  <div class="container">
    <a class="navbar-brand app-brand" href="index.php"><img class="app-brand-logo" src="assets/img/logo-sidoarjo.png" alt="Lambang Kabupaten Sidoarjo"> PenTing Balita</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navLanding">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navLanding">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item"><a class="nav-link" href="#posyandu">Posyandu</a></li>
        <li class="nav-item"><a class="nav-link" href="#tentang">Tentang</a></li>
      </ul>
    </div>
    <a href="login.php" class="btn btn-login-outline btn-sm landing-login-top ms-lg-3"><i class="fa-solid fa-right-to-bracket"></i> Masuk ke Sistem</a>
  </div>
</nav>

<header class="hero-section text-center">
  <div class="hero-splash-screen" id="heroSplashScreen" role="status" aria-label="Memuat Posyandu Desa Kemuning Tropodo">
    <div class="hero-splash-logo"><img src="assets/img/logo-sidoarjo.png" alt="Lambang Kabupaten Sidoarjo"></div>
    <div class="hero-splash-brand">Posyandu Desa Kemuning Tropodo</div>
    <div class="hero-splash-place">Sistem Informasi Posyandu Desa Kemuning Tropodo</div>
    <div class="hero-splash-loading"><span></span><span></span><span></span></div>
    <div class="hero-splash-progress" aria-hidden="true"><span></span></div>
  </div>
  <div class="container py-5">
    <div class="hero-splash-animation" aria-label="Posyandu Desa Kemuning Tropodo">
      <div class="hero-splash-orbit"></div>
      <div class="hero-icon mb-3"><i class="fa-solid fa-child-reaching"></i></div>
      <h1 class="fw-bold hero-title">Sistem Informasi Posyandu</h1>
      <h2 class="fw-semibold hero-subtitle mb-3">Desa Kemuning Tropodo</h2>
      <p class="hero-tagline mx-auto">Pantau tumbuh kembang balita, ibu hamil, dan lansia dengan mudah.</p>
      <div class="hero-splash-dots" aria-hidden="true"><span></span><span></span><span></span></div>
    </div>
  </div>
</header>

<section id="posyandu" class="py-5">
  <div class="container">
    <div class="text-center mb-4">
      <span class="section-badge">8 Posyandu Aktif</span>
      <h3 class="fw-bold mt-2">Posyandu di Desa Kemuning Tropodo</h3>
      <p class="text-muted">Setiap Posyandu memantau tumbuh kembang balita di wilayahnya masing-masing.</p>
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
              <h6 class="fw-bold mb-1"><?= bersih($p['nama']) ?></h6>
              <p class="small text-muted mb-2"><?= bersih($p['alamat']) ?></p>
              <span class="badge-status badge-normal"><?= (int)$p['total'] ?> Balita</span>
              <p class="small link-pink mt-2 mb-0"><i class="fa-solid fa-chart-simple"></i> Lihat Grafik</p>
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
        <h5 class="modal-title fw-bold">
          <i class="fa-solid fa-house-medical text-pink"></i> <?= bersih($p['nama']) ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="small text-muted mb-3"><?= bersih($p['alamat']) ?></p>

        <div class="row g-2 mb-4 text-center">
          <div class="col-3">
            <div class="mini-stat">
              <div class="fw-bold fs-5"><?= (int)$p['total'] ?></div>
              <div class="small text-muted">Total</div>
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
              <div class="fw-bold fs-5" style="color:#a5760b;"><?= (int)$p['berisiko'] ?></div>
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

        <div class="row g-3">
          <div class="col-md-5">
            <h6 class="small fw-bold text-muted mb-2">Distribusi Status Gizi</h6>
            <?php if ((int)$p['normal'] + (int)$p['berisiko'] + (int)$p['stunting'] > 0): ?>
              <canvas id="chartStatus<?= $p['id'] ?>" height="200"></canvas>
            <?php else: ?>
              <p class="text-muted small text-center py-4">Belum ada data status gizi balita di Posyandu ini.</p>
            <?php endif; ?>
          </div>
          <div class="col-md-7">
            <h6 class="small fw-bold text-muted mb-2">Tren Rata-rata Pertumbuhan</h6>
            <?php if (empty($grafikPosyandu[$p['id']]['label'])): ?>
              <p class="text-muted small text-center py-4">Belum ada data pengukuran untuk Posyandu ini.</p>
            <?php else: ?>
              <canvas id="chartTren<?= $p['id'] ?>" height="200"></canvas>
            <?php endif; ?>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
<?php endforeach; ?>

<section id="tentang" class="py-5 tentang-section">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-6">
        <span class="section-badge">Tentang Sistem</span>
        <h3 class="fw-bold mt-2">Mengelola Data Balita Jadi Lebih Mudah</h3>
        <p class="text-muted">PenTing Balita membantu Admin dan Bidan Desa Tropodo mencatat data balita,
          mengukur pertumbuhan berat &amp; tinggi badan, serta memantau status gizi balita secara rapi
          dan real-time untuk setiap Posyandu.</p>
        <ul class="list-unstyled tentang-list">
          <li><i class="fa-solid fa-circle-check"></i> Data balita per Posyandu tersimpan rapi</li>
          <li><i class="fa-solid fa-circle-check"></i> Grafik pertumbuhan otomatis dari riwayat pengukuran</li>
          <li><i class="fa-solid fa-circle-check"></i> Status gizi: Normal, Berisiko, Stunting</li>
        </ul>
      </div>
      <div class="col-lg-6 text-center">
        <div class="tentang-illustration">
          <i class="fa-solid fa-chart-line"></i>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
$pageTitle = "Landing";
?>
<footer class="app-footer text-center py-4">
  <p class="mb-0 small text-muted">&copy; <?= date('Y') ?> PenTing Balita &mdash; Desa Tropodo. Sistem Informasi Posyandu.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script src="assets/js/script.js?v=20261112c"></script>

<script>
// Data grafik tiap Posyandu (hanya statistik agregat, disiapkan dari server)
const dataGrafikPosyandu = <?= json_encode($grafikPosyandu, JSON_NUMERIC_CHECK) ?>;
const dataStatusPosyandu = {
  <?php foreach ($posyanduList as $p): ?>
  <?= $p['id'] ?>: { normal: <?= (int)$p['normal'] ?>, berisiko: <?= (int)$p['berisiko'] ?>, stunting: <?= (int)$p['stunting'] ?> },
  <?php endforeach; ?>
};

const chartInstances = {};

<?php foreach ($posyanduList as $p): $pid = $p['id']; ?>
document.getElementById('modalPosyandu<?= $pid ?>').addEventListener('shown.bs.modal', function () {
  const status = dataStatusPosyandu[<?= $pid ?>];

  // Hancurkan chart lama (jika ada) sebelum membuat ulang, agar tidak dobel saat modal dibuka lagi
  const canvasStatus = document.getElementById('chartStatus<?= $pid ?>');
  if (canvasStatus) {
    if (chartInstances['status<?= $pid ?>']) chartInstances['status<?= $pid ?>'].destroy();
    chartInstances['status<?= $pid ?>'] = new Chart(canvasStatus, {
      type: 'doughnut',
      data: {
        labels: ['Normal', 'Berisiko', 'Stunting'],
        datasets: [{
          data: [status.normal, status.berisiko, status.stunting],
          backgroundColor: ['#7bd389', '#ffc857', '#ff6b6b']
        }]
      },
      options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } } }
    });
  }

  const tren = dataGrafikPosyandu[<?= $pid ?>];
  const canvasTren = document.getElementById('chartTren<?= $pid ?>');
  if (canvasTren && tren && tren.label.length > 0) {
    if (chartInstances['tren<?= $pid ?>']) chartInstances['tren<?= $pid ?>'].destroy();
    chartInstances['tren<?= $pid ?>'] = new Chart(canvasTren, {
      type: 'line',
      data: {
        labels: tren.label,
        datasets: [
          { label: 'Berat (kg)', data: tren.berat, yAxisID: 'yBerat', borderColor: '#c9a7f2', tension: 0.35, fill: false },
          { label: 'Tinggi (cm)', data: tren.tinggi, yAxisID: 'yTinggi', borderColor: '#8ecae6', tension: 0.35, fill: false }
        ]
      },
      options: {
        responsive: true,
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } },
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
  }
});
<?php endforeach; ?>
</script>

<script>
// Splash screen tampil di area hero selama 2,2 detik lalu fade-out.
document.addEventListener('DOMContentLoaded', function () {
  document.body.classList.add('splash-active');
  const splash = document.getElementById('heroSplashScreen');
  if (!splash) return;
  window.setTimeout(function () {
    splash.classList.add('is-hidden');
    document.body.classList.remove('splash-active');
    window.setTimeout(function () { splash.remove(); }, 700);
  }, 2200);
});
</script>
</body>
</html>
