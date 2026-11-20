<?php
require_once 'includes/auth.php';
require_once 'config/koneksi.php';
require_once 'includes/functions.php';
cekRoleAdminAtauBidan();

$jadwal = [];
$tabelSiap = false;
$cekTabel = mysqli_query($koneksi, "SHOW TABLES LIKE 'jadwal_posyandu'");
$tabelSiap = $cekTabel && mysqli_num_rows($cekTabel) > 0;
if ($tabelSiap) {
    $sql = "SELECT j.*, p.nama AS nama_posyandu, p.alamat
            FROM jadwal_posyandu j
            JOIN posyandu p ON p.id = j.posyandu_id
            ORDER BY j.tanggal ASC, j.waktu_mulai ASC, p.nama ASC";
    $res = mysqli_query($koneksi, $sql);
    while ($row = mysqli_fetch_assoc($res)) $jadwal[] = $row;
}
$daftarPosyandu = [];
$resP = mysqli_query($koneksi, "SELECT id, nama FROM posyandu ORDER BY nama ASC");
while ($row = mysqli_fetch_assoc($resP)) $daftarPosyandu[] = $row;
$pageTitle = 'Jadwal Posyandu';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>
<main class="container-fluid px-3 px-lg-4 py-4">
  <div class="page-heading mb-4 d-flex flex-wrap justify-content-between align-items-end gap-2">
    <div>
      <h4 class="fw-bold mb-0">Jadwal Posyandu</h4>
      <p class="text-muted small mb-0">Atur tanggal, jam, lokasi, dan catatan kegiatan sesuai kebutuhan bidan.</p>
    </div>
    <?php if ($tabelSiap): ?><button class="btn btn-pink-solid" data-bs-toggle="modal" data-bs-target="#modalJadwal"><i class="fa-solid fa-plus"></i> Tambah Jadwal</button><?php endif; ?>
  </div>
  <?php if (!$tabelSiap): ?>
    <div class="alert alert-warning"><i class="fa-solid fa-triangle-exclamation me-2"></i>Tabel jadwal belum tersedia. Import file <strong>database/20261112_jadwal_posyandu.sql</strong> terlebih dahulu.</div>
  <?php elseif (isset($_GET['sukses'])): ?>
    <div class="alert alert-success"><i class="fa-solid fa-circle-check me-2"></i>Jadwal berhasil diperbarui.</div>
  <?php endif; ?>
  <div class="row g-3">
    <?php if (!$jadwal && $tabelSiap): ?><div class="col-12"><div class="chart-card text-center py-5"><i class="fa-regular fa-calendar-plus fs-1 text-pink mb-3"></i><p class="mb-0 text-muted">Belum ada jadwal. Tambahkan jadwal posyandu pertama.</p></div></div><?php endif; ?>
    <?php foreach ($jadwal as $j): ?>
      <div class="col-md-6 col-xl-4">
        <div class="chart-card schedule-card h-100">
          <div class="d-flex justify-content-between align-items-start gap-2">
            <div><span class="section-badge"><i class="fa-regular fa-calendar me-1"></i><?= date('d M Y', strtotime($j['tanggal'])) ?></span><h5 class="fw-bold mt-3 mb-1"><?= bersih($j['nama_posyandu']) ?></h5></div>
            <div class="d-flex gap-1"><button class="btn btn-sm btn-outline-pink" data-bs-toggle="modal" data-bs-target="#modalJadwal" data-jadwal='<?= htmlspecialchars(json_encode($j), ENT_QUOTES, 'UTF-8') ?>' title="Ubah"><i class="fa-solid fa-pen"></i></button><form method="POST" action="proses/simpan-jadwal-posyandu.php" onsubmit="return confirm('Hapus jadwal ini?');"><input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= (int)$j['id'] ?>"><button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fa-solid fa-trash"></i></button></form></div>
          </div>
          <div class="schedule-detail"><div><i class="fa-regular fa-clock"></i><?= date('H:i', strtotime($j['waktu_mulai'])) ?><?= $j['waktu_selesai'] ? ' - '.date('H:i', strtotime($j['waktu_selesai'])) : '' ?> WIB</div><div><i class="fa-solid fa-location-dot"></i><?= bersih($j['lokasi'] ?: $j['alamat']) ?></div><?php if ($j['keterangan']): ?><div><i class="fa-regular fa-note-sticky"></i><?= bersih($j['keterangan']) ?></div><?php endif; ?></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</main>
<?php if ($tabelSiap): ?>
<div class="modal fade" id="modalJadwal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content posyandu-modal-content"><form method="POST" action="proses/simpan-jadwal-posyandu.php"><div class="modal-header"><h5 class="modal-title fw-bold"><i class="fa-regular fa-calendar-plus text-pink me-2"></i><span id="judulModalJadwal">Tambah Jadwal</span></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="hidden" name="aksi" value="simpan"><input type="hidden" name="id" id="jadwalId"><div class="mb-3"><label class="form-label small fw-semibold">Posyandu</label><select name="posyandu_id" id="jadwalPosyandu" class="form-select" required><option value="">-- Pilih Posyandu --</option><?php foreach ($daftarPosyandu as $p): ?><option value="<?= (int)$p['id'] ?>"><?= bersih($p['nama']) ?></option><?php endforeach; ?></select></div><div class="row g-3"><div class="col-6"><label class="form-label small fw-semibold">Tanggal</label><input type="date" name="tanggal" id="jadwalTanggal" class="form-control" required></div><div class="col-6"><label class="form-label small fw-semibold">Jam mulai</label><input type="time" name="waktu_mulai" id="jadwalMulai" class="form-control" required></div><div class="col-6"><label class="form-label small fw-semibold">Jam selesai</label><input type="time" name="waktu_selesai" id="jadwalSelesai" class="form-control"></div><div class="col-6"><label class="form-label small fw-semibold">Lokasi</label><input type="text" name="lokasi" id="jadwalLokasi" class="form-control" placeholder="Balai desa / rumah kader"></div></div><div class="mt-3"><label class="form-label small fw-semibold">Catatan</label><textarea name="keterangan" id="jadwalKeterangan" class="form-control" rows="2" placeholder="Contoh: membawa KMS dan buku KIA"></textarea></div></div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-pink-solid"><i class="fa-solid fa-floppy-disk"></i> Simpan Jadwal</button></div></form></div></div></div>
<script>
document.getElementById('modalJadwal').addEventListener('show.bs.modal', function (event) { const trigger = event.relatedTarget; const data = trigger && trigger.dataset.jadwal ? JSON.parse(trigger.dataset.jadwal) : null; document.getElementById('judulModalJadwal').textContent = data ? 'Ubah Jadwal' : 'Tambah Jadwal'; document.getElementById('jadwalId').value = data ? data.id : ''; document.getElementById('jadwalPosyandu').value = data ? data.posyandu_id : ''; document.getElementById('jadwalTanggal').value = data ? data.tanggal : ''; document.getElementById('jadwalMulai').value = data ? data.waktu_mulai.substring(0,5) : ''; document.getElementById('jadwalSelesai').value = data && data.waktu_selesai ? data.waktu_selesai.substring(0,5) : ''; document.getElementById('jadwalLokasi').value = data ? (data.lokasi || '') : ''; document.getElementById('jadwalKeterangan').value = data ? (data.keterangan || '') : ''; });
</script>
<?php endif; ?>
<?php require_once 'includes/footer.php'; ?>
