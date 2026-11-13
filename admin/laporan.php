<?php
// ============================================================
//  admin/laporan.php — Laporan Penjualan (Owner)
// ============================================================
require_once '../includes/auth_check.php';
authCheck('owner');
require_once '../config/db.php';

$user = currentUser();

// ── Filter Tanggal ────────────────────────────────────────
$tglMulai   = $_GET['start'] ?? date('Y-m-01'); // Default awal bulan
$tglSelesai = $_GET['end']   ?? date('Y-m-t');  // Default akhir bulan ini

// Validasi format tanggal Y-m-d
function isValidDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

if (!isValidDate($tglMulai))   $tglMulai   = date('Y-m-01');
if (!isValidDate($tglSelesai)) $tglSelesai = date('Y-m-d');

// ── Query Laporan (Hanya pesanan 'selesai') ──────────────
$stmt = $pdo->prepare("
    SELECT * FROM pesanan 
    WHERE status = 'selesai' 
      AND DATE(created_at) >= ? 
      AND DATE(created_at) <= ?
    ORDER BY created_at DESC
");
$stmt->execute([$tglMulai, $tglSelesai]);
$pesanan = $stmt->fetchAll();

// ── Kalkulasi ─────────────────────────────────────────────
$totalPendapatan = 0;
$totalTransaksi  = count($pesanan);

foreach ($pesanan as $p) {
    $totalPendapatan += $p['total_harga'];
}

// ── Item Terlaris (dalam rentang tanggal) ────────────────
$stmtPopuler = $pdo->prepare("
    SELECT nama_menu, SUM(qty) as total_terjual 
    FROM detail_pesanan dp
    JOIN pesanan p ON dp.pesanan_id = p.id
    WHERE p.status = 'selesai'
      AND DATE(p.created_at) >= ? 
      AND DATE(p.created_at) <= ?
    GROUP BY nama_menu
    ORDER BY total_terjual DESC
    LIMIT 5
");
$stmtPopuler->execute([$tglMulai, $tglSelesai]);
$menuPopuler = $stmtPopuler->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laporan Penjualan – DapurRumpin</title>
  <link rel="icon" type="image/jpg" href="../img/logo.jpg">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap">
  <link rel="stylesheet" href="../css/dashboard.css">
  <style>
    .report-summary {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 24px;
    }
    .summary-box {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.03);
    }
    .summary-box h4 {
      margin: 0 0 8px 0;
      color: var(--abu);
      font-size: 0.9rem;
      font-weight: 500;
    }
    .summary-box .val {
      font-size: 1.8rem;
      font-weight: 800;
      color: var(--gelap);
      font-family: 'Playfair Display', serif;
    }
    .filter-bar {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      align-items: center;
      background: #fff;
      padding: 16px;
      border-radius: 12px;
      border: 1px solid var(--border);
      margin-bottom: 24px;
    }
    .filter-bar label {
      font-weight: 600;
      font-size: 0.9rem;
      color: var(--abu);
    }
    .filter-bar input[type="date"] {
      padding: 8px 12px;
      border: 1px solid var(--border);
      border-radius: 6px;
      font-family: inherit;
    }
    .popular-list {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    .popular-list li {
      display: flex;
      justify-content: space-between;
      padding: 12px 0;
      border-bottom: 1px dashed var(--border);
    }
    .popular-list li:last-child {
      border-bottom: none;
    }
  </style>
</head>
<body>

<?php include '_sidebar.php'; ?>

<div class="main-content">
  <?php include '_topbar.php'; ?>

  <div class="page-body">
    <div class="page-title-wrap">
      <div>
        <h2 class="page-title">📈 Laporan Penjualan</h2>
        <p class="page-sub">Ringkasan pendapatan dari pesanan yang sudah selesai</p>
      </div>
      <button onclick="window.print()" class="btn-primary-dash">🖨️ Cetak</button>
    </div>

    <!-- Filter Form -->
    <form method="GET" class="filter-bar no-print">
      <label>Periode:</label>
      <input type="date" name="start" value="<?= htmlspecialchars($tglMulai) ?>" required>
      <span>s/d</span>
      <input type="date" name="end" value="<?= htmlspecialchars($tglSelesai) ?>" required>
      <button type="submit" class="btn-sm btn-hijau">Filter</button>
      
      <div style="margin-left: auto; display: flex; gap: 8px;">
        <a href="laporan.php?start=<?= date('Y-m-d') ?>&end=<?= date('Y-m-d') ?>" class="btn-sm btn-secondary-dash">Hari Ini</a>
        <a href="laporan.php?start=<?= date('Y-m-01') ?>&end=<?= date('Y-m-t') ?>" class="btn-sm btn-secondary-dash">Bulan Ini</a>
      </div>
    </form>

    <div class="report-summary">
      <div class="summary-box" style="border-left: 4px solid var(--hijau-utama);">
        <h4>Total Pendapatan</h4>
        <div class="val">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></div>
      </div>
      <div class="summary-box" style="border-left: 4px solid #3b82f6;">
        <h4>Pesanan Selesai</h4>
        <div class="val"><?= $totalTransaksi ?></div>
      </div>
    </div>

    <div class="stat-grid" style="grid-template-columns: 2fr 1fr; align-items: start;">
      
      <!-- Tabel Pesanan -->
      <div class="card" style="margin-bottom:0;">
        <div class="card-header">
          <h3 class="card-title">Rincian Transaksi</h3>
        </div>
        <div class="table-wrap">
          <table class="data-table">
            <thead>
              <tr>
                <th>Waktu</th>
                <th>Kode</th>
                <th>Pelanggan</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($pesanan)): ?>
                <tr><td colspan="4" class="td-empty">Tidak ada transaksi di periode ini.</td></tr>
              <?php else: ?>
                <?php foreach ($pesanan as $p): ?>
                <tr>
                  <td><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></td>
                  <td><code><?= htmlspecialchars($p['kode_pesanan']) ?></code></td>
                  <td><?= htmlspecialchars($p['nama_pemesan']) ?></td>
                  <td style="font-weight:600;">Rp <?= number_format($p['total_harga'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Menu Populer -->
      <div class="card" style="margin-bottom:0;">
        <div class="card-header">
          <h3 class="card-title">🏆 Top 5 Menu</h3>
        </div>
        <div class="card-body">
          <?php if (empty($menuPopuler)): ?>
            <p style="color:var(--abu);text-align:center;font-size:0.9rem;">Belum ada data.</p>
          <?php else: ?>
            <ul class="popular-list">
              <?php foreach ($menuPopuler as $mp): ?>
                <li>
                  <span style="font-weight:600;"><?= htmlspecialchars($mp['nama_menu']) ?></span>
                  <span style="color:var(--hijau-utama);font-weight:700;"><?= $mp['total_terjual'] ?> porsi</span>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>

    </div>

  </div>
</div>

<script src="../js/dashboard.js"></script>
</body>
</html>
