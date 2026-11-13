<?php
// ============================================================
//  dapur/antrian.php — Tampilan Antrian Dapur (Read-Only)
//  Role: dapur + owner boleh akses
//  Fitur: card besar, auto-refresh 30 detik, tanpa tombol aksi
// ============================================================
require_once '../includes/auth_check.php';
authCheck(['owner', 'dapur']); // hanya dapur dan owner
require_once '../config/db.php';

$user = currentUser();

// ── Ambil pesanan aktif (pending + diproses) ──────────────
$stmt = $pdo->query("
    SELECT p.*, 
           GROUP_CONCAT(CONCAT(dp.nama_menu, ' x', dp.qty) SEPARATOR '||') AS items_summary
    FROM pesanan p
    LEFT JOIN detail_pesanan dp ON dp.pesanan_id = p.id
    WHERE p.status IN ('pending', 'diproses')
    GROUP BY p.id
    ORDER BY p.created_at ASC
");
$pesanans = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Antrian Dapur – DapurRumpin</title>
  <link rel="icon" type="image/jpg" href="../img/logo.jpg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600;700&display=swap">
  <style>
    /* ── Variables ── */
    :root {
      --hijau-tua:    #1a3c2b;
      --hijau-utama:  #2d6a4f;
      --hijau-muda:   #52b788;
      --hijau-cerah:  #74c69d;
      --hijau-pucat:  #d8f3dc;
      --hijau-pastel: #f0faf2;
      --kuning-aksen: #f4a261;
      --abu:          #5a6472;
      --abu-muda:     #94a3b8;
      --gelap:        #1b1f23;
      --putih:        #ffffff;
      --bg-dapur:     #0f172a;
      --border:       #1e293b;
      --radius-lg:    16px;
      --radius-md:    10px;
      --transition:   all 0.3s cubic-bezier(.4,0,.2,1);
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { font-size: 16px; scroll-behavior: smooth; }

    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--bg-dapur);
      color: #e2e8f0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* ════════════════════════════════════════════════════════
       TOPBAR DAPUR
       ════════════════════════════════════════════════════════ */
    .dapur-topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 12px 24px;
      background: linear-gradient(135deg, #1e293b, #0f172a);
      border-bottom: 2px solid var(--hijau-utama);
      position: sticky;
      top: 0;
      z-index: 100;
      flex-wrap: wrap;
      gap: 12px;
    }

    .dapur-topbar-left {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .dapur-topbar-logo {
      width: 40px; height: 40px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid var(--hijau-muda);
    }

    .dapur-topbar-brand { display: flex; flex-direction: column; }

    .dapur-topbar-title {
      font-family: 'Playfair Display', serif;
      font-size: 1.2rem;
      font-weight: 800;
      color: var(--putih);
    }

    .dapur-topbar-subtitle {
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 2px;
      color: var(--hijau-muda);
    }

    .dapur-topbar-center {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .dapur-clock {
      font-family: 'DM Sans', sans-serif;
      font-size: 2rem;
      font-weight: 800;
      color: var(--hijau-cerah);
      letter-spacing: 3px;
      line-height: 1;
    }

    .dapur-date {
      font-size: 0.78rem;
      color: var(--abu-muda);
      margin-top: 2px;
    }

    .dapur-topbar-right {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .dapur-user-info {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.9rem;
      color: #cbd5e1;
    }

    .dapur-user-icon { font-size: 1.3rem; }

    .dapur-btn-logout {
      padding: 6px 16px;
      border-radius: 50px;
      background: rgba(220,38,38,0.15);
      color: #fca5a5;
      font-size: 0.82rem;
      font-weight: 600;
      border: 1px solid rgba(220,38,38,0.3);
      text-decoration: none;
      transition: var(--transition);
    }

    .dapur-btn-logout:hover {
      background: rgba(220,38,38,0.3);
      color: #fff;
    }

    /* ════════════════════════════════════════════════════════
       MAIN CONTENT
       ════════════════════════════════════════════════════════ */
    .dapur-main {
      flex: 1;
      padding: 24px;
    }

    /* ── Header bar ── */
    .dapur-header-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 24px;
      flex-wrap: wrap;
      gap: 12px;
    }

    .dapur-header-bar h1 {
      font-family: 'Playfair Display', serif;
      font-size: 1.6rem;
      font-weight: 800;
      color: var(--putih);
    }

    .dapur-header-bar h1 span { color: var(--hijau-muda); }

    .dapur-stats {
      display: flex;
      gap: 16px;
    }

    .dapur-stat-pill {
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 6px 14px;
      border-radius: 50px;
      font-size: 0.85rem;
      font-weight: 700;
    }

    .dapur-stat-pill.pending {
      background: rgba(234,179,8,0.15);
      color: #fbbf24;
      border: 1px solid rgba(234,179,8,0.3);
    }

    .dapur-stat-pill.diproses {
      background: rgba(82,183,136,0.15);
      color: var(--hijau-cerah);
      border: 1px solid rgba(82,183,136,0.3);
    }

    .refresh-indicator-dapur {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 0.78rem;
      color: var(--abu-muda);
      padding: 4px 12px;
      background: rgba(255,255,255,0.05);
      border-radius: 50px;
      border: 1px solid var(--border);
    }

    .refresh-dot-dapur {
      width: 8px; height: 8px;
      border-radius: 50%;
      background: var(--hijau-muda);
      animation: pulseDot 2s infinite;
    }

    @keyframes pulseDot {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.3; }
    }

    /* ════════════════════════════════════════════════════════
       ANTRIAN GRID — Card Besar
       ════════════════════════════════════════════════════════ */
    .antrian-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
      gap: 20px;
    }

    .antrian-card {
      background: #1e293b;
      border-radius: var(--radius-lg);
      overflow: hidden;
      transition: var(--transition);
      animation: cardFadeIn 0.4s ease both;
    }

    @keyframes cardFadeIn {
      from { opacity: 0; transform: translateY(12px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .antrian-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 32px rgba(0,0,0,0.3);
    }

    /* Warna strip atas berdasarkan status */
    .antrian-card.status-pending {
      border-top: 4px solid #eab308;
      box-shadow: 0 2px 16px rgba(234,179,8,0.1);
    }

    .antrian-card.status-diproses {
      border-top: 4px solid var(--hijau-muda);
      box-shadow: 0 2px 16px rgba(82,183,136,0.1);
    }

    /* ── Card Header ── */
    .antrian-card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px 20px 12px;
    }

    .antrian-meja {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .antrian-meja-icon {
      width: 48px; height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      flex-shrink: 0;
    }

    .status-pending .antrian-meja-icon {
      background: rgba(234,179,8,0.15);
    }

    .status-diproses .antrian-meja-icon {
      background: rgba(82,183,136,0.15);
    }

    .antrian-meja-num {
      font-family: 'Playfair Display', serif;
      font-size: 1.6rem;
      font-weight: 800;
      color: var(--putih);
      line-height: 1.1;
    }

    .antrian-meja-kode {
      font-size: 0.72rem;
      color: var(--abu-muda);
      margin-top: 2px;
    }

    .antrian-status-badge {
      padding: 4px 12px;
      border-radius: 50px;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .badge-status-pending {
      background: rgba(234,179,8,0.15);
      color: #fbbf24;
      border: 1px solid rgba(234,179,8,0.3);
    }

    .badge-status-diproses {
      background: rgba(82,183,136,0.15);
      color: var(--hijau-cerah);
      border: 1px solid rgba(82,183,136,0.3);
    }

    .dapur-select-status {
      padding: 6px 12px;
      border-radius: 8px;
      font-size: 0.8rem;
      font-weight: 700;
      color: var(--putih);
      background: var(--hijau-tua);
      border: 1px solid var(--hijau-muda);
      cursor: pointer;
      outline: none;
    }
    .dapur-select-status:focus {
      border-color: var(--hijau-cerah);
    }

    /* ── Card Body: daftar item ── */
    .antrian-card-body {
      padding: 0 20px 16px;
    }

    .antrian-items {
      list-style: none;
    }

    .antrian-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 8px 0;
      border-bottom: 1px solid rgba(255,255,255,0.06);
      font-size: 1.05rem;
    }

    .antrian-item:last-child { border-bottom: none; }

    .antrian-item-name {
      color: #e2e8f0;
      font-weight: 500;
    }

    .antrian-item-qty {
      font-weight: 700;
      color: var(--hijau-cerah);
      font-size: 1.1rem;
      min-width: 40px;
      text-align: right;
    }

    /* ── Card Footer: waktu ── */
    .antrian-card-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 12px 20px;
      background: rgba(0,0,0,0.2);
      font-size: 0.82rem;
      color: var(--abu-muda);
    }

    .antrian-waktu-icon { margin-right: 4px; }

    .antrian-elapsed {
      font-weight: 700;
      color: #fbbf24;
    }

    .antrian-nama-pemesan {
      font-size: 0.78rem;
      color: var(--abu-muda);
    }

    /* ════════════════════════════════════════════════════════
       EMPTY STATE — Dapur Bersih
       ════════════════════════════════════════════════════════ */
    .dapur-empty {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 50vh;
      text-align: center;
      animation: cardFadeIn 0.5s ease both;
    }

    .dapur-empty-icon {
      font-size: 5rem;
      margin-bottom: 20px;
      animation: floatChef 3s ease-in-out infinite;
    }

    @keyframes floatChef {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-12px); }
    }

    .dapur-empty h2 {
      font-family: 'Playfair Display', serif;
      font-size: 2rem;
      font-weight: 800;
      color: var(--putih);
      margin-bottom: 8px;
    }

    .dapur-empty p {
      font-size: 1.1rem;
      color: var(--abu-muda);
    }

    /* ════════════════════════════════════════════════════════
       RESPONSIVE
       ════════════════════════════════════════════════════════ */
    @media (max-width: 768px) {
      .dapur-topbar { padding: 10px 16px; }
      .dapur-clock { font-size: 1.4rem; }
      .dapur-main { padding: 16px; }
      .dapur-header-bar h1 { font-size: 1.3rem; }
      .antrian-grid { grid-template-columns: 1fr; }
      .antrian-meja-num { font-size: 1.3rem; }
      .antrian-item { font-size: 0.95rem; }
    }

    @media (min-width: 1400px) {
      .antrian-grid { grid-template-columns: repeat(3, 1fr); }
    }
  </style>
</head>
<body>

<?php include '_topbar.php'; ?>

<div class="dapur-main">
  <!-- Header Bar -->
  <div class="dapur-header-bar">
    <div>
      <h1>🔥 Antrian <span>Pesanan</span></h1>
    </div>
    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
      <?php
        $countPending  = 0;
        $countDiproses = 0;
        foreach ($pesanans as $pp) {
            if ($pp['status'] === 'pending') $countPending++;
            if ($pp['status'] === 'diproses') $countDiproses++;
        }
      ?>
      <span class="dapur-stat-pill pending" id="statPending">
        ⏳ Pending: <strong><?= $countPending ?></strong>
      </span>
      <span class="dapur-stat-pill diproses" id="statDiproses">
        🍳 Diproses: <strong><?= $countDiproses ?></strong>
      </span>
      <span class="refresh-indicator-dapur">
        <span class="refresh-dot-dapur"></span>
        Auto-refresh: <span id="countdown">30</span>s
      </span>
    </div>
  </div>

  <!-- Antrian Grid -->
  <div class="antrian-grid" id="antrianGrid">
    <?php if (empty($pesanans)): ?>
      <div class="dapur-empty" id="dapurEmpty" style="grid-column: 1 / -1;">
        <span class="dapur-empty-icon">👨‍🍳</span>
        <h2>Dapur Bersih!</h2>
        <p>Tidak ada pesanan masuk saat ini</p>
      </div>
    <?php else: ?>
      <?php foreach ($pesanans as $p): ?>
        <?php
          $items = $p['items_summary'] ? explode('||', $p['items_summary']) : [];
          $timeIn = strtotime($p['created_at']);
          $elapsed = time() - $timeIn;
          $elapsedMin = floor($elapsed / 60);
        ?>
        <div class="antrian-card status-<?= $p['status'] ?>">
          <div class="antrian-card-header">
            <div class="antrian-meja">
              <div class="antrian-meja-icon">🪑</div>
              <div>
                <div class="antrian-meja-num">Meja <?= htmlspecialchars($p['nomor_meja']) ?></div>
                <div class="antrian-meja-kode"><?= htmlspecialchars($p['kode_pesanan']) ?></div>
              </div>
            </div>
            <select class="dapur-select-status" onchange="updateStatus(<?= $p['id'] ?>, this)">
              <option value="pending" <?= $p['status'] === 'pending' ? 'selected' : '' ?>>⏳ Pending</option>
              <option value="diproses" <?= $p['status'] === 'diproses' ? 'selected' : '' ?>>🍳 Diproses</option>
              <option value="selesai" <?= $p['status'] === 'selesai' ? 'selected' : '' ?>>🍽️ Selesai</option>
              <option value="dibatalkan" <?= $p['status'] === 'dibatalkan' ? 'selected' : '' ?>>❌ Batal</option>
            </select>
          </div>

          <div class="antrian-card-body">
            <ul class="antrian-items">
              <?php foreach ($items as $item):
                $parts = explode(' x', $item);
                $namaMenu = trim($parts[0] ?? '');
                $qty = trim($parts[1] ?? '1');
              ?>
                <li class="antrian-item">
                  <span class="antrian-item-name"><?= htmlspecialchars($namaMenu) ?></span>
                  <span class="antrian-item-qty">x<?= htmlspecialchars($qty) ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>

          <div class="antrian-card-footer">
            <span>
              <span class="antrian-waktu-icon">🕐</span>
              <?= date('H:i', $timeIn) ?> —
              <span class="antrian-elapsed"><?= $elapsedMin ?> menit lalu</span>
            </span>
            <span class="antrian-nama-pemesan">👤 <?= htmlspecialchars($p['nama_pemesan']) ?></span>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<script>
// ════════════════════════════════════════════════════════════
//  AUTO-REFRESH ANTRIAN SETIAP 30 DETIK
// ════════════════════════════════════════════════════════════
var refreshInterval = 30;
var countdownTimer = refreshInterval;
var countdownEl = document.getElementById('countdown');

// Countdown
setInterval(function() {
  countdownTimer--;
  if (countdownTimer <= 0) countdownTimer = refreshInterval;
  if (countdownEl) countdownEl.textContent = countdownTimer;
}, 1000);

// Fetch data baru dan render ulang
setInterval(function() {
  countdownTimer = refreshInterval;

  fetch('../api/antrian_dapur.php')
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (!data.success) return;

      var grid = document.getElementById('antrianGrid');
      var pesanans = data.data;

      // Update stat pills
      var pending = 0, diproses = 0;
      pesanans.forEach(function(p) {
        if (p.status === 'pending') pending++;
        if (p.status === 'diproses') diproses++;
      });
      document.getElementById('statPending').innerHTML = '⏳ Pending: <strong>' + pending + '</strong>';
      document.getElementById('statDiproses').innerHTML = '🍳 Diproses: <strong>' + diproses + '</strong>';

      // Jika tidak ada pesanan
      if (pesanans.length === 0) {
        grid.innerHTML = '<div class="dapur-empty" style="grid-column: 1 / -1;">'
          + '<span class="dapur-empty-icon">👨‍🍳</span>'
          + '<h2>Dapur Bersih!</h2>'
          + '<p>Tidak ada pesanan masuk saat ini</p>'
          + '</div>';
        return;
      }

      // Render cards
      var html = '';
      pesanans.forEach(function(p, idx) {
        var statusClass = 'status-' + p.status;
        var statusLabel = p.status === 'pending' ? '⏳ Pending' : '🍳 Diproses';
        var badgeClass = 'badge-status-' + p.status;

        // Hitung elapsed time
        var timeIn = new Date(p.created_at);
        var now = new Date();
        var elapsedMs = now - timeIn;
        var elapsedMin = Math.floor(elapsedMs / 60000);
        var timeStr = String(timeIn.getHours()).padStart(2,'0') + ':' + String(timeIn.getMinutes()).padStart(2,'0');

        html += '<div class="antrian-card ' + statusClass + '" style="animation-delay:' + (idx * 0.05) + 's">';

        // Header
        html += '<div class="antrian-card-header">';
        html += '<div class="antrian-meja">';
        html += '<div class="antrian-meja-icon">🪑</div>';
        html += '<div>';
        html += '<div class="antrian-meja-num">Meja ' + escapeHtml(p.nomor_meja) + '</div>';
        html += '<div class="antrian-meja-kode">' + escapeHtml(p.kode_pesanan) + '</div>';
        html += '</div></div>';
        html += '<select class="dapur-select-status" onchange="updateStatus(' + p.id + ', this)">';
        html += '<option value="pending" ' + (p.status === "pending" ? "selected" : "") + '>⏳ Pending</option>';
        html += '<option value="diproses" ' + (p.status === "diproses" ? "selected" : "") + '>🍳 Diproses</option>';
        html += '<option value="selesai" ' + (p.status === "selesai" ? "selected" : "") + '>🍽️ Selesai</option>';
        html += '<option value="dibatalkan" ' + (p.status === "dibatalkan" ? "selected" : "") + '>❌ Batal</option>';
        html += '</select>';
        html += '</div>';

        // Body — items
        html += '<div class="antrian-card-body"><ul class="antrian-items">';
        if (p.items && p.items.length > 0) {
          p.items.forEach(function(item) {
            html += '<li class="antrian-item">';
            html += '<span class="antrian-item-name">' + escapeHtml(item.nama_menu) + '</span>';
            html += '<span class="antrian-item-qty">x' + item.qty + '</span>';
            html += '</li>';
          });
        }
        html += '</ul></div>';

        // Footer
        html += '<div class="antrian-card-footer">';
        html += '<span><span class="antrian-waktu-icon">🕐</span> ' + timeStr + ' — ';
        html += '<span class="antrian-elapsed">' + elapsedMin + ' menit lalu</span></span>';
        html += '<span class="antrian-nama-pemesan">👤 ' + escapeHtml(p.nama_pemesan) + '</span>';
        html += '</div>';

        html += '</div>';
      });

      grid.innerHTML = html;
    })
    .catch(function(err) {
      console.error('Auto-refresh antrian gagal:', err);
    });

}, refreshInterval * 1000);

// ── Helper ────────────────────────────────────────────────
function escapeHtml(text) {
  var div = document.createElement('div');
  div.appendChild(document.createTextNode(text || ''));
  return div.innerHTML;
}

function updateStatus(pesananId, selectEl) {
  var newStatus = selectEl.value;
  selectEl.disabled = true;

  fetch('../api/update_status_pesanan.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id: pesananId, status: newStatus })
  })
  .then(function(res) { return res.json(); })
  .then(function(data) {
    if (data.success) {
      // Force refresh data sekarang juga
      countdownTimer = 1;
    } else {
      alert(data.message);
      selectEl.disabled = false;
    }
  })
  .catch(function(err) {
    alert('Terjadi kesalahan jaringan.');
    selectEl.disabled = false;
  });
}
</script>

</body>
</html>
