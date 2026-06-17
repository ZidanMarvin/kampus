<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.html");
    exit;
}
$nama = htmlspecialchars($_SESSION['nama']);
$role = htmlspecialchars($_SESSION['role']);
$roleLabel = $role === 'teknisi' ? 'Teknisi' : 'Mahasiswa';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ITATAS FIX — Dashboard</title>
<link rel="stylesheet" href="_clean.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/dist/tabler-icons.min.css">
<style>
@keyframes slideUp {
  from { transform: translateY(16px); opacity: 0; }
  to   { transform: translateY(0);    opacity: 1; }
}
@keyframes pulse { 50% { opacity: 0.4; } }
.loading-cell { animation: pulse 1.4s infinite; }
.toast { animation: slideUp 0.25s ease; }

/* Foto thumbnail */
.foto-thumb {
  width: 52px;
  height: 52px;
  object-fit: cover;
  border-radius: 6px;
  border: 1px solid var(--border);
  cursor: pointer;
  transition: transform 0.15s, box-shadow 0.15s;
  display: block;
}
.foto-thumb:hover {
  transform: scale(1.08);
  box-shadow: var(--shadow);
}
.no-foto {
  width: 52px; height: 52px;
  border-radius: 6px;
  border: 1px dashed var(--border-2);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--text-light);
  font-size: 20px;
}

/* Lightbox */
.lightbox {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.75);
  z-index: 9990;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}
.lightbox.open { display: flex; }
.lightbox img {
  max-width: 90vw;
  max-height: 80vh;
  border-radius: 10px;
  box-shadow: 0 24px 64px rgba(0,0,0,0.4);
  object-fit: contain;
}
.lightbox-close {
  position: absolute;
  top: 1.2rem; right: 1.5rem;
  color: #fff;
  font-size: 32px;
  cursor: pointer;
  line-height: 1;
  opacity: 0.8;
}
.lightbox-close:hover { opacity: 1; }
.lightbox-caption {
  position: absolute;
  bottom: 1.5rem;
  color: rgba(255,255,255,0.7);
  font-size: 13px;
  text-align: center;
  left: 0; right: 0;
}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="cf-nav">
  <a href="dashboard.php" class="brand">
    <span class="dot"></span>
    ITATAS<span>Fix</span>
  </a>
  <div class="nav-right">
    <div class="user-info">
      <span class="user-name"><?= $nama ?></span>
      <span class="user-role"><?= $roleLabel ?></span>
    </div>
    <a href="logout.php" class="btn btn-outline" style="font-size:13px;padding:0.4rem 0.9rem;">
      <i class="ti ti-logout"></i> Keluar
    </a>
  </div>
</nav>

<div class="page-wrap">

  <!-- PAGE HEADER -->
  <div class="section-header">
    <div>
      <div class="section-eyebrow">Overview</div>
      <h2>Dashboard Laporan</h2>
    </div>
    <a href="laporan.html" class="btn btn-primary">
      <i class="ti ti-plus"></i> Buat Laporan
    </a>
  </div>

  <!-- STAT CARDS -->
  <div class="stat-grid">
    <div class="stat-card navy">
      <div class="stat-label">Total Aktif</div>
      <div class="stat-value" id="statTotal">—</div>
    </div>
    <div class="stat-card yellow">
      <div class="stat-label">Menunggu</div>
      <div class="stat-value" id="statMenunggu">—</div>
    </div>
    <div class="stat-card blue">
      <div class="stat-label">Diverifikasi</div>
      <div class="stat-value" id="statDiverifikasi">—</div>
    </div>
    <div class="stat-card purple">
      <div class="stat-label">Dalam Perbaikan</div>
      <div class="stat-value" id="statPerbaikan">—</div>
    </div>
    <div class="stat-card green">
      <div class="stat-label">Selesai</div>
      <div class="stat-value" id="statSelesai">—</div>
    </div>
  </div>

  <!-- TABLE -->
  <div class="table-section">
    <div class="table-header">
      <span class="table-title">
        <i class="ti ti-list-details"></i> Data Laporan
      </span>
    </div>
    <div style="overflow-x:auto;">
      <table class="cf-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Judul</th>
            <th>Kategori</th>
            <th>Lokasi</th>
            <th>Status</th>
            <?php if ($role === 'teknisi'): ?>
            <th>Foto</th>
            <th>Ubah Status</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody id="tabelBody">
          <tr>
            <td colspan="<?= $role === 'teknisi' ? 7 : 5 ?>" class="loading-cell">
              Memuat data...
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- LIGHTBOX -->
<div class="lightbox" id="lightbox" onclick="closeLightbox()">
  <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
  <img id="lightboxImg" src="" alt="Foto laporan">
  <div class="lightbox-caption" id="lightboxCaption"></div>
</div>

<!-- TOAST -->
<div class="toast" id="toast" style="display:none;">
  <i class="ti ti-check"></i>
  <span id="toastMsg"></span>
</div>

<script>
const isTeknisi = <?= json_encode($role === 'teknisi') ?>;
const colSpan   = isTeknisi ? 7 : 5;

function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toastMsg').textContent = msg;
  t.style.display = 'flex';
  setTimeout(() => { t.style.display = 'none'; }, 2800);
}

function badgeClass(s) {
  switch((s||'').trim()) {
    case 'Selesai':         return 'badge-selesai';
    case 'Dalam Perbaikan': return 'badge-perbaikan';
    case 'Diverifikasi':    return 'badge-diverifikasi';
    default:                return 'badge-menunggu';
  }
}

function loadData() {
  fetch('data_laporan.php')
    .then(r => r.json())
    .then(data => {
      const tbody = document.getElementById('tabelBody');

      const menunggu     = data.filter(r => r.status === 'Menunggu').length;
      const diverifikasi = data.filter(r => r.status === 'Diverifikasi').length;
      const perbaikan    = data.filter(r => r.status === 'Dalam Perbaikan').length;
      const selesai      = data.filter(r => r.status === 'Selesai').length;

      document.getElementById('statTotal').textContent        = menunggu + diverifikasi + perbaikan;
      document.getElementById('statMenunggu').textContent     = menunggu;
      document.getElementById('statDiverifikasi').textContent = diverifikasi;
      document.getElementById('statPerbaikan').textContent    = perbaikan;
      document.getElementById('statSelesai').textContent      = selesai;

      if (data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="${colSpan}" class="loading-cell" style="animation:none;padding:3rem;">Belum ada laporan masuk.</td></tr>`;
        return;
      }

      tbody.innerHTML = data.map((row, i) => {
        const s = row.status || 'Menunggu';

        // Foto thumbnail (teknisi only)
        let fotoCell = '';
        if (isTeknisi) {
          if (row.foto) {
            const src = 'uploads/' + row.foto;
            fotoCell = `<td>
              <img class="foto-thumb" src="${src}" alt="Foto"
                onclick="openLightbox('${src}', '${row.judul.replace(/'/g,"\\'")}')">
            </td>`;
          } else {
            fotoCell = `<td>
              <div class="no-foto" title="Tidak ada foto">
                <i class="ti ti-photo-off"></i>
              </div>
            </td>`;
          }
        }

        return `
          <tr>
            <td class="row-num">${String(i+1).padStart(2,'0')}</td>
            <td class="td-judul">${row.judul}</td>
            <td style="color:var(--text-muted)">${row.kategori}</td>
            <td style="color:var(--text-muted)">${row.lokasi}</td>
            <td><span class="badge ${badgeClass(s)}">${s}</span></td>
            ${fotoCell}
            ${isTeknisi ? `
            <td>
              <select class="status-select" onchange="ubahStatus(${row.id}, this.value, this)">
                <option value="Menunggu"        ${s==='Menunggu'?'selected':''}>Menunggu</option>
                <option value="Diverifikasi"    ${s==='Diverifikasi'?'selected':''}>Diverifikasi</option>
                <option value="Dalam Perbaikan" ${s==='Dalam Perbaikan'?'selected':''}>Dalam Perbaikan</option>
                <option value="Selesai"         ${s==='Selesai'?'selected':''}>Selesai</option>
              </select>
            </td>` : ''}
          </tr>`;
      }).join('');
    })
    .catch(() => {
      document.getElementById('tabelBody').innerHTML =
        `<tr><td colspan="${colSpan}" class="loading-cell" style="animation:none;color:var(--red);">Gagal memuat data. Coba refresh halaman.</td></tr>`;
    });
}

loadData();

function openLightbox(src, caption) {
  document.getElementById('lightboxImg').src = src;
  document.getElementById('lightboxCaption').textContent = caption;
  document.getElementById('lightbox').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeLightbox() {
  document.getElementById('lightbox').classList.remove('open');
  document.getElementById('lightboxImg').src = '';
  document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });

function ubahStatus(id, status, selectEl) {
  selectEl.disabled = true;
  fetch('update_status.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `id=${id}&status=${encodeURIComponent(status)}`
  })
  .then(r => r.text())
  .then(() => {
    showToast('Status diperbarui: ' + status);
    loadData();
  })
  .catch(() => {
    showToast('Gagal memperbarui status.');
    selectEl.disabled = false;
  });
}
</script>
</body>
</html>