<?php
// ============================================================
//  kasir/pesanan.php — Daftar & Kelola Pesanan (Role: Kasir + Owner)
//  Fitur: filter, ubah status, pembayaran QRIS, auto-refresh 30 detik
// ============================================================
require_once '../includes/auth_check.php';
authCheck(['owner', 'kasir']); // owner dan kasir boleh akses
require_once '../config/db.php';

$user  = currentUser();
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);


// ── Filter ────────────────────────────────────────────────
$filterStatus = $_GET['status'] ?? 'semua';
$filterDate   = $_GET['tgl']    ?? '';
$search       = trim($_GET['q'] ?? '');

$sql    = "SELECT * FROM pesanan WHERE 1=1";
$params = [];

if ($filterStatus !== 'semua') { $sql .= " AND status = ?";      $params[] = $filterStatus; }
if ($filterDate   !== '')       { $sql .= " AND DATE(created_at) = ?"; $params[] = $filterDate; }
if ($search       !== '')       {
    $sql .= " AND (nama_pemesan LIKE ? OR kode_pesanan LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$sql .= " ORDER BY created_at DESC";

$stmt     = $pdo->prepare($sql);
$stmt->execute($params);
$pesanans = $stmt->fetchAll();

// ── Helper badge ─────────────────────────────────────────
function statusBadge(string $s): string {
    return match($s) {
        'pending'    => 'badge-pending',
        'diproses'   => 'badge-diproses',
        'selesai'    => 'badge-selesai',
        'dibatalkan' => 'badge-dibatalkan',
        default      => ''
    };
}

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pesanan – Kasir DapurRumpin</title>
  <link rel="icon" type="image/jpg" href="../img/logo.jpg">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap">
  <link rel="stylesheet" href="../css/dashboard.css">
  <style>
    /* ── Tombol Bayar QRIS ── */
    .btn-bayar-qris {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 5px 12px;
      background: linear-gradient(135deg, #6366f1, #8b5cf6);
      color: #fff;
      border: none;
      border-radius: 50px;
      font-family: 'DM Sans', sans-serif;
      font-size: 0.78rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(.4,0,.2,1);
      box-shadow: 0 2px 8px rgba(99,102,241,0.3);
      margin-top: 6px;
    }

    .btn-bayar-qris:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 16px rgba(99,102,241,0.4);
    }

    /* ── Tombol Cetak Struk ── */
    .btn-cetak-struk {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 5px 12px;
      background: #f1f5f9;
      color: #334155;
      border: 1px solid #cbd5e1;
      border-radius: 50px;
      font-family: 'DM Sans', sans-serif;
      font-size: 0.78rem;
      font-weight: 600;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(.4,0,.2,1);
      margin-top: 6px;
    }

    .btn-cetak-struk:hover {
      background: #e2e8f0;
      color: #0f172a;
      transform: translateY(-1px);
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    /* ── Modal Pembayaran QRIS ── */
    .qris-modal-body {
      text-align: center;
      padding: 24px;
    }

    .qris-modal-body .qris-image {
      max-width: 280px;
      border-radius: 12px;
      border: 2px solid #e2e8f0;
      margin: 0 auto 16px;
    }

    .qris-nominal {
      font-family: 'Playfair Display', serif;
      font-size: 1.8rem;
      font-weight: 800;
      color: var(--hijau-tua);
      margin-bottom: 4px;
    }

    .qris-info {
      font-size: 0.85rem;
      color: var(--abu);
      margin-bottom: 20px;
    }

    .btn-konfirmasi-lunas {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 12px 32px;
      background: linear-gradient(135deg, var(--hijau-utama), var(--hijau-muda));
      color: #fff;
      border: none;
      border-radius: 50px;
      font-family: 'DM Sans', sans-serif;
      font-size: 1rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(.4,0,.2,1);
      box-shadow: 0 4px 16px rgba(45,106,79,0.35);
    }

    .btn-konfirmasi-lunas:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(45,106,79,0.4);
    }

    .btn-konfirmasi-lunas:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none;
    }

    /* ── Auto-refresh indicator ── */
    .refresh-indicator {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 0.78rem;
      color: var(--abu-muda);
      padding: 4px 12px;
      background: #f8fafc;
      border-radius: 50px;
      border: 1px solid var(--border);
    }

    .refresh-dot {
      width: 8px; height: 8px;
      border-radius: 50%;
      background: var(--hijau-muda);
      animation: pulse-dot 2s infinite;
    }

    @keyframes pulse-dot {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.3; }
    }

    /* ── Status di kolom bayar ── */
    .payment-status {
      font-size: 0.75rem;
      color: var(--abu-muda);
      font-style: italic;
    }

    .payment-status.lunas {
      color: #15803d;
      font-weight: 600;
      font-style: normal;
    }
  </style>
</head>
<body>

<?php 
if (hasRole('owner')) {
    include '../admin/_sidebar.php';
} else {
    include '_sidebar.php';
}
?>

<div class="main-content">
  <?php 
  if (hasRole('owner')) {
      include '../admin/_topbar.php';
  } else {
      include '_topbar.php';
  }
  ?>

  <div class="page-body">
    <div class="page-title-wrap">
      <div>
        <h2 class="page-title">Daftar Pesanan</h2>
        <p class="page-sub"><span id="jumlahPesanan"><?= count($pesanans) ?></span> pesanan ditemukan</p>
      </div>
      <div class="refresh-indicator">
        <span class="refresh-dot"></span>
        <span>Auto-refresh: <span id="countdown">30</span>s</span>
      </div>
    </div>

    <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] ?> alert-dismissible">
        <?= htmlspecialchars($flash['msg']) ?>
        <button onclick="this.parentElement.remove()">✕</button>
      </div>
    <?php endif; ?>

    <!-- Filter Bar -->
    <div class="card">
      <form method="GET" class="filter-bar filter-bar-col">
        <div class="filter-tabs">
          <?php foreach ([
            'semua'     => 'Semua',
            'pending'   => 'Pending',
            'diproses'  => 'Diproses',
            'selesai'   => 'Selesai',
            'dibatalkan'=> 'Dibatalkan',
          ] as $val => $lbl): ?>
            <button type="submit" name="status" value="<?= $val ?>"
              class="filter-tab <?= $filterStatus === $val ? 'active' : '' ?>">
              <?= $lbl ?>
            </button>
          <?php endforeach; ?>
          <input type="hidden" name="q"   value="<?= htmlspecialchars($search) ?>">
          <input type="hidden" name="tgl" value="<?= htmlspecialchars($filterDate) ?>">
        </div>
        <div class="filter-row-right">
          <input type="date" name="tgl" value="<?= htmlspecialchars($filterDate) ?>"
                 class="form-input form-input-sm" onchange="this.form.submit()">
          <input type="text"  name="q"   value="<?= htmlspecialchars($search) ?>"
                 class="search-input" placeholder="🔍 Cari nama / kode..."
                 onchange="this.form.submit()">
        </div>
      </form>
    </div>

    <!-- Tabel Pesanan -->
    <div class="card">
      <div class="table-wrap">
        <table class="data-table" id="tabelPesanan">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Nama Pemesan</th>
              <th>Meja</th>
              <th>Total</th>
              <th>Status</th>
              <th>Waktu</th>
              <th width="140">Bayar</th>
            </tr>
          </thead>
          <tbody id="tbodyPesanan">
            <?php if (empty($pesanans)): ?>
              <tr><td colspan="8" class="td-empty">Tidak ada pesanan ditemukan.</td></tr>
            <?php else: ?>
              <?php foreach ($pesanans as $p): ?>
              <tr id="row-<?= $p['id'] ?>">
                <td>
                  <code class="kode-pesanan"><?= htmlspecialchars($p['kode_pesanan']) ?></code>
                  <button class="btn-detail-pesanan"
                          onclick="lihatDetail(<?= $p['id'] ?>)"
                          title="Lihat detail item">🔍</button>
                </td>
                <td><?= htmlspecialchars($p['nama_pemesan']) ?></td>
                <td>Meja <?= htmlspecialchars($p['nomor_meja']) ?></td>
                <td><strong>Rp <?= number_format($p['total_harga'], 0, ',', '.') ?></strong></td>
                <td><span class="badge <?= statusBadge($p['status']) ?>"><?= ucfirst($p['status']) ?></span></td>
                <td><?= date('d/m/y H:i', strtotime($p['created_at'])) ?></td>
                <td>
                  <?php if ($p['status'] === 'diproses'): ?>
                    <button class="btn-bayar-qris"
                            onclick="bukaBayarQRIS(<?= $p['id'] ?>, '<?= htmlspecialchars($p['kode_pesanan']) ?>', <?= $p['total_harga'] ?>)">
                      💳 Bayar QRIS
                    </button>
                  <?php elseif ($p['status'] === 'selesai' && !empty($p['paid_at'])): ?>
                    <span class="payment-status lunas">✅ Lunas</span><br>
                    <a href="../cetak_struk.php?kode=<?= urlencode($p['kode_pesanan']) ?>" target="_blank" class="btn-cetak-struk">🖨️ Struk</a>
                  <?php elseif ($p['status'] === 'selesai'): ?>
                    <span class="payment-status">Selesai</span><br>
                    <a href="../cetak_struk.php?kode=<?= urlencode($p['kode_pesanan']) ?>" target="_blank" class="btn-cetak-struk">🖨️ Struk</a>
                  <?php else: ?>
                    <span class="payment-status">—</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<!-- Modal Detail Item Pesanan -->
<div class="modal-overlay-dash" id="modalDetailPesanan" onclick="tutupDetail(event)">
  <div class="modal-dash">
    <div class="modal-dash-header">
      <h3 id="modalDetailJudul">Detail Pesanan</h3>
      <button class="modal-close-dash" onclick="tutupDetail()">✕</button>
    </div>
    <div class="modal-dash-body" id="modalDetailBody">
      <p style="text-align:center;color:var(--abu);padding:20px;">Memuat...</p>
    </div>
  </div>
</div>

<!-- Modal Pembayaran QRIS -->
<div class="modal-overlay-dash" id="modalBayarQRIS" onclick="tutupBayar(event)">
  <div class="modal-dash">
    <div class="modal-dash-header" style="background:linear-gradient(135deg, #6366f1, #8b5cf6);">
      <h3 id="modalBayarJudul">💳 Pembayaran QRIS</h3>
      <button class="modal-close-dash" onclick="tutupBayar()">✕</button>
    </div>
    <div class="modal-dash-body qris-modal-body">
      <img src="../img/qris.jpeg" alt="QR Code QRIS" class="qris-image"
           onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22280%22 height=%22280%22%3E%3Crect fill=%22%23f1f5f9%22 width=%22280%22 height=%22280%22/%3E%3Ctext x=%22140%22 y=%22140%22 text-anchor=%22middle%22 fill=%22%2394a3b8%22 font-size=%2214%22%3EQRIS tidak tersedia%3C/text%3E%3C/svg%3E'">
      <div class="qris-nominal" id="qrisNominal">Rp 0</div>
      <p class="qris-info" id="qrisInfo">Minta customer scan QRIS di atas untuk membayar</p>
      <input type="hidden" id="qrisPesananId" value="">
      <button class="btn-konfirmasi-lunas" id="btnKonfirmasiLunas" onclick="konfirmasiLunas()">
        ✅ Konfirmasi Lunas
      </button>
    </div>
  </div>
</div>

<script src="../js/dashboard.js"></script>
<script>
// ── CSRF token untuk request AJAX ─────────────────────────
var csrfToken = '<?= htmlspecialchars($_SESSION['csrf_token']) ?>';

// ════════════════════════════════════════════════════════════
//  MODAL DETAIL PESANAN
// ════════════════════════════════════════════════════════════
function lihatDetail(id) {
  var modal = document.getElementById('modalDetailPesanan');
  var body  = document.getElementById('modalDetailBody');
  var judul = document.getElementById('modalDetailJudul');

  modal.classList.add('open');
  document.body.style.overflow = 'hidden';
  body.innerHTML = '<p style="text-align:center;padding:30px;">⏳ Memuat detail...</p>';

  fetch('../api/detail_pesanan.php?id=' + id)
    .then(function(r) { return r.json(); })
    .then(function(data) {
      judul.textContent = 'Detail Pesanan – ' + data.kode_pesanan;
      var html = '<table class="data-table">';
      html += '<thead><tr><th>Menu</th><th>Harga</th><th>Qty</th><th>Subtotal</th></tr></thead><tbody>';
      data.items.forEach(function(item) {
        html += '<tr>';
        html += '<td>' + item.nama_menu + '</td>';
        html += '<td>Rp ' + Number(item.harga_saat_pesan).toLocaleString('id-ID') + '</td>';
        html += '<td>' + item.qty + 'x</td>';
        html += '<td>Rp ' + Number(item.subtotal).toLocaleString('id-ID') + '</td>';
        html += '</tr>';
      });
      html += '</tbody><tfoot><tr>';
      html += '<td colspan="3" style="text-align:right;font-weight:700;">Total</td>';
      html += '<td><strong>Rp ' + Number(data.total_harga).toLocaleString('id-ID') + '</strong></td>';
      html += '</tr></tfoot></table>';
      body.innerHTML = html;
    })
    .catch(function() {
      body.innerHTML = '<p style="text-align:center;color:#dc2626;padding:20px;">Gagal memuat detail.</p>';
    });
}

function tutupDetail(event) {
  var modal = document.getElementById('modalDetailPesanan');
  if (event && event.target !== modal && !event.target.classList.contains('modal-close-dash')) return;
  modal.classList.remove('open');
  document.body.style.overflow = '';
}

// ════════════════════════════════════════════════════════════
//  MODAL PEMBAYARAN QRIS
// ════════════════════════════════════════════════════════════
function bukaBayarQRIS(pesananId, kodePesanan, totalHarga) {
  var modal = document.getElementById('modalBayarQRIS');
  document.getElementById('modalBayarJudul').textContent = '💳 Pembayaran – ' + kodePesanan;
  document.getElementById('qrisNominal').textContent = 'Rp ' + Number(totalHarga).toLocaleString('id-ID');
  document.getElementById('qrisInfo').textContent = 'Minta customer scan QRIS di atas untuk membayar';
  document.getElementById('qrisPesananId').value = pesananId;
  document.getElementById('btnKonfirmasiLunas').disabled = false;
  document.getElementById('btnKonfirmasiLunas').textContent = '✅ Konfirmasi Lunas';

  modal.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function tutupBayar(event) {
  var modal = document.getElementById('modalBayarQRIS');
  if (event && event.target !== modal && !event.target.classList.contains('modal-close-dash')) return;
  modal.classList.remove('open');
  document.body.style.overflow = '';
}

function konfirmasiLunas() {
  var pesananId = document.getElementById('qrisPesananId').value;
  var btn = document.getElementById('btnKonfirmasiLunas');

  if (!pesananId) return;
  if (!confirm('Konfirmasi bahwa pembayaran sudah diterima?')) return;

  // Disable tombol untuk mencegah double-click
  btn.disabled = true;
  btn.textContent = '⏳ Memproses...';

  fetch('../api/bayar_pesanan.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'pesanan_id=' + encodeURIComponent(pesananId) + '&csrf_token=' + encodeURIComponent(csrfToken)
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (data.success) {
      btn.textContent = '✅ Pembayaran Berhasil!';
      document.getElementById('qrisInfo').textContent = 'Pesanan telah ditandai lunas.';

      // Tutup modal setelah 1.5 detik dan reload halaman
      setTimeout(function() {
        tutupBayar();
        window.location.reload();
      }, 1500);
    } else {
      alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
      btn.disabled = false;
      btn.textContent = '✅ Konfirmasi Lunas';
    }
  })
  .catch(function() {
    alert('Terjadi kesalahan jaringan. Coba lagi.');
    btn.disabled = false;
    btn.textContent = '✅ Konfirmasi Lunas';
  });
}

// ════════════════════════════════════════════════════════════
//  AUTO-REFRESH PESANAN SETIAP 30 DETIK
// ════════════════════════════════════════════════════════════
var refreshInterval = 30; // detik
var countdownEl = document.getElementById('countdown');
var countdownTimer = refreshInterval;

// Countdown timer
setInterval(function() {
  countdownTimer--;
  if (countdownTimer <= 0) countdownTimer = refreshInterval;
  if (countdownEl) countdownEl.textContent = countdownTimer;
}, 1000);

// Auto-refresh data pesanan
setInterval(function() {
  countdownTimer = refreshInterval;

  // Bangun URL API berdasarkan filter yang aktif saat ini
  var params = new URLSearchParams();
  var currentStatus = '<?= htmlspecialchars($filterStatus) ?>';
  var currentDate   = '<?= htmlspecialchars($filterDate) ?>';
  var currentSearch = '<?= htmlspecialchars($search) ?>';

  if (currentStatus !== 'semua') params.set('status', currentStatus);
  if (currentDate !== '') params.set('tgl', currentDate);
  if (currentSearch !== '') params.set('q', currentSearch);

  fetch('../api/pesanan_list.php?' + params.toString())
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (!data.success) return;

      var tbody = document.getElementById('tbodyPesanan');
      var jumlahEl = document.getElementById('jumlahPesanan');

      if (data.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="td-empty">Tidak ada pesanan ditemukan.</td></tr>';
        if (jumlahEl) jumlahEl.textContent = '0';
        return;
      }

      if (jumlahEl) jumlahEl.textContent = data.data.length;

      var html = '';
      data.data.forEach(function(p) {
        html += '<tr id="row-' + p.id + '">';

        // Kode
        html += '<td>';
        html += '<code class="kode-pesanan">' + escapeHtml(p.kode_pesanan) + '</code>';
        html += ' <button class="btn-detail-pesanan" onclick="lihatDetail(' + p.id + ')" title="Lihat detail item">🔍</button>';
        html += '</td>';

        // Nama
        html += '<td>' + escapeHtml(p.nama_pemesan) + '</td>';

        // Meja
        html += '<td>Meja ' + escapeHtml(p.nomor_meja) + '</td>';

        // Total
        html += '<td><strong>Rp ' + Number(p.total_harga).toLocaleString('id-ID') + '</strong></td>';

        // Status badge
        var badgeClass = 'badge-' + p.status;
        html += '<td><span class="badge ' + badgeClass + '">' + capitalFirst(p.status) + '</span></td>';

        // Waktu
        html += '<td>' + formatTanggal(p.created_at) + '</td>';

        // Ubah Status
        html += '<td>';
        if (p.status !== 'selesai' && p.status !== 'dibatalkan') {
          html += '<form method="POST" class="inline-form">';
          html += '<input type="hidden" name="csrf_token" value="' + csrfToken + '">';
          html += '<input type="hidden" name="update_status" value="1">';
          html += '<input type="hidden" name="pesanan_id" value="' + p.id + '">';
          html += '<select name="status" class="select-status" onchange="this.form.submit()">';
          html += '<option value="pending"'    + (p.status==='pending'   ?' selected':'') + '>Pending</option>';
          html += '<option value="diproses"'   + (p.status==='diproses'  ?' selected':'') + '>Diproses</option>';
          html += '<option value="selesai"'    + (p.status==='selesai'   ?' selected':'') + '>Selesai</option>';
          html += '<option value="dibatalkan"' + (p.status==='dibatalkan'?' selected':'') + '>Batalkan</option>';
          html += '</select></form>';
        } else {
          html += '<span class="status-final">Status final</span>';
        }
        html += '</td>';

        // Bayar
        html += '<td>';
        if (p.status === 'diproses') {
          html += '<button class="btn-bayar-qris" onclick="bukaBayarQRIS(' + p.id + ',\'' + escapeHtml(p.kode_pesanan) + '\',' + p.total_harga + ')">💳 Bayar QRIS</button>';
        } else if (p.status === 'selesai' && p.paid_at) {
          html += '<span class="payment-status lunas">✅ Lunas</span><br>';
          html += '<a href="../cetak_struk.php?kode=' + encodeURIComponent(p.kode_pesanan) + '" target="_blank" class="btn-cetak-struk">🖨️ Struk</a>';
        } else if (p.status === 'selesai') {
          html += '<span class="payment-status">Selesai</span><br>';
          html += '<a href="../cetak_struk.php?kode=' + encodeURIComponent(p.kode_pesanan) + '" target="_blank" class="btn-cetak-struk">🖨️ Struk</a>';
        } else {
          html += '<span class="payment-status">—</span>';
        }
        html += '</td>';

        html += '</tr>';
      });

      tbody.innerHTML = html;
    })
    .catch(function(err) {
      console.error('Auto-refresh gagal:', err);
    });

}, refreshInterval * 1000);

// ════════════════════════════════════════════════════════════
//  NOTIFIKASI BADGE (update tiap 30 detik)
// ════════════════════════════════════════════════════════════
function updateNotifBadge() {
  fetch('../api/notif_pesanan.php')
    .then(function(r) { return r.json(); })
    .then(function(data) {
      var badge = document.getElementById('notifBadge');
      var sidebarBadge = document.getElementById('sidebarBadgePending');
      if (data.count > 0) {
        if (badge) { badge.style.display = 'block'; badge.textContent = data.count; }
      } else {
        if (badge) badge.style.display = 'none';
      }
      // Update sidebar badge juga
      if (sidebarBadge && data.total_pending !== undefined) {
        sidebarBadge.textContent = data.total_pending;
        sidebarBadge.style.display = data.total_pending > 0 ? 'inline' : 'none';
      }
    })
    .catch(function() {});
}

// Jalankan sekali di awal, lalu tiap 30 detik
updateNotifBadge();
setInterval(updateNotifBadge, 30000);

// ════════════════════════════════════════════════════════════
//  HELPER FUNCTIONS
// ════════════════════════════════════════════════════════════
function escapeHtml(text) {
  var div = document.createElement('div');
  div.appendChild(document.createTextNode(text));
  return div.innerHTML;
}

function capitalFirst(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}

function formatTanggal(dateStr) {
  var d = new Date(dateStr);
  var day   = String(d.getDate()).padStart(2, '0');
  var month = String(d.getMonth() + 1).padStart(2, '0');
  var year  = String(d.getFullYear()).slice(-2);
  var hour  = String(d.getHours()).padStart(2, '0');
  var min   = String(d.getMinutes()).padStart(2, '0');
  return day + '/' + month + '/' + year + ' ' + hour + ':' + min;
}
</script>
</body>
</html>
