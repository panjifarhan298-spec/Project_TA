<?php
require_once 'includes/auth.php';
require_once 'config/koneksi.php';
require_once 'includes/functions.php';
cekRoleAdminAtauBidan();

$posyanduList = [];
$sql = "SELECT p.id, p.nama, p.alamat,
               SUM(CASE WHEN b.kategori='balita' THEN 1 ELSE 0 END) AS total_balita,
               SUM(CASE WHEN b.kategori='balita' AND b.status='Normal' THEN 1 ELSE 0 END) AS normal,
               SUM(CASE WHEN b.kategori='balita' AND b.status='Berisiko' THEN 1 ELSE 0 END) AS berisiko,
               SUM(CASE WHEN b.kategori='balita' AND b.status='Stunting' THEN 1 ELSE 0 END) AS stunting,
               SUM(CASE WHEN b.kategori='ibu_hamil' THEN 1 ELSE 0 END) AS ibu_hamil,
               SUM(CASE WHEN b.kategori='lansia' THEN 1 ELSE 0 END) AS lansia
        FROM posyandu p
        LEFT JOIN balita b ON b.posyandu_id = p.id
        GROUP BY p.id, p.nama, p.alamat ORDER BY p.id ASC";
$res = mysqli_query($koneksi, $sql);
while ($row = mysqli_fetch_assoc($res)) $posyanduList[] = $row;
$pageTitle = 'Data Posyandu';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>
<main class="container-fluid px-3 px-lg-4 py-4 data-posyandu-page">
  <div class="page-heading mb-4">
    <div>
      <h4 class="fw-bold mb-1">Data Posyandu</h4>
      <p class="text-muted small mb-0">Ringkasan status gizi balita dan jumlah Ibu Hamil serta Lansia per Posyandu.</p>
    </div>
  </div>

  <?php if (!$posyanduList): ?>
    <div class="chart-card text-center text-muted py-5">Data Posyandu belum tersedia.</div>
  <?php else: ?>
    <div class="posyandu-data-grid">
      <?php foreach ($posyanduList as $i => $p): ?>
        <article class="posyandu-dashboard-card">
          <div class="posyandu-dashboard-head">
            <div class="posyandu-icon"><i class="fa-solid <?= ['fa-house-chimney-medical','fa-hand-holding-medical','fa-baby','fa-heart-pulse','fa-syringe','fa-weight-scale','fa-ruler-vertical','fa-child'][$i % 8] ?>"></i></div>
            <h6><?= bersih($p['nama']) ?></h6>
            <p><?= bersih($p['alamat']) ?></p>
          </div>
          <div class="posyandu-total-number"><?= (int)$p['total_balita'] ?><small>Total Balita</small></div>
          <div class="posyandu-status-badges">
            <span class="badge-status badge-normal">N:<?= (int)$p['normal'] ?></span>
            <span class="badge-status badge-risk">B:<?= (int)$p['berisiko'] ?></span>
            <span class="badge-status badge-stunting">S:<?= (int)$p['stunting'] ?></span>
          </div>
          <div class="posyandu-chart-wrap"><canvas id="chartPosyandu<?= (int)$p['id'] ?>" aria-label="Grafik status <?= bersih($p['nama']) ?>"></canvas></div>
          <div class="posyandu-detail-picker">
            <span class="small text-muted"><i class="fa-solid fa-list-check me-1"></i>Lihat detail:</span>
            <div class="posyandu-detail-buttons">
              <a href="data-balita.php?posyandu_id=<?= (int)$p['id'] ?>" class="btn btn-detail-balita"><i class="fa-solid fa-child-reaching"></i> Balita</a>
              <a href="data-ibu-hamil.php?posyandu_id=<?= (int)$p['id'] ?>" class="btn btn-detail-ibu"><i class="fa-solid fa-person-pregnant"></i> Ibu Hamil</a>
              <a href="data-lansia.php?posyandu_id=<?= (int)$p['id'] ?>" class="btn btn-detail-lansia"><i class="fa-solid fa-person-cane"></i> Lansia</a>
            </div>
          </div>
          <div class="posyandu-extra-counts"><span><i class="fa-solid fa-person-pregnant"></i> <?= (int)$p['ibu_hamil'] ?> Ibu Hamil</span><span><i class="fa-solid fa-person-cane"></i> <?= (int)$p['lansia'] ?> Lansia</span></div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const cards = <?= json_encode($posyanduList, JSON_NUMERIC_CHECK) ?>;
  cards.forEach(function (p) {
    const canvas = document.getElementById('chartPosyandu' + p.id);
    if (!canvas || typeof Chart === 'undefined') return;
    new Chart(canvas, {
      type: 'doughnut',
      data: { labels: ['Normal', 'Berisiko', 'Stunting'], datasets: [{ data: [p.normal, p.berisiko, p.stunting], backgroundColor: ['#73d18a', '#f4cf63', '#ff686d'], borderColor: '#fff', borderWidth: 2 }] },
      options: { responsive: true, maintainAspectRatio: false, cutout: '57%', plugins: { legend: { display: false }, tooltip: { callbacks: { label: function (ctx) { return ' ' + ctx.label + ': ' + ctx.raw; } } } } }
    });
  });
});
</script>
<?php require_once 'includes/footer.php'; ?>
