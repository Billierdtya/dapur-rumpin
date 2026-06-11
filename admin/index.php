<?php
// ============================================================
//  admin/index.php — Dashboard Admin
// ============================================================
require_once '../includes/auth_check.php';
authCheck('admin');
require_once '../config/db.php';

$user = currentUser();

// ── Statistik ringkas ────────────────────────────────────
$totalMenu     = $pdo->query("SELECT COUNT(*) FROM menu WHERE is_active = 1")->fetchColumn();
$totalPesanan  = $pdo->query("SELECT COUNT(*) FROM pesanan")->fetchColumn();
$pesananPending= $pdo->query("SELECT COUNT(*) FROM pesanan WHERE status = 'pending'")->fetchColumn();
$totalStaff    = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'staff'")->fetchColumn();
$pendapatanHari= $pdo->query(
    "SELECT COALESCE(SUM(total_harga),0) FROM pesanan
     WHERE DATE(created_at) = CURDATE() AND status != 'dibatalkan'"
)->fetchColumn();

// ── 5 pesanan terbaru ────────────────────────────────────
$pesananTerbaru = $pdo->query(
    "SELECT * FROM pesanan ORDER BY created_at DESC LIMIT 5"
)->fetchAll();

// ── Warna badge status ───────────────────────────────────
function statusBadge(string $status): string {
    return match($status) {
        'pending'     => 'badge-pending',
        'diproses'    => 'badge-diproses',
        'selesai'     => 'badge-selesai',
        'dibatalkan'  => 'badge-dibatalkan',
        default       => ''
    };
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin – DapurRumpin</title>
  <link rel="icon" type="image/jpg" href="../img/logo.jpg">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap">
  <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>

<?php include '_sidebar.php'; ?>

<div class="main-content">
  <?php include '_topbar.php'; ?>

  <div class="page-body">
    <div class="page-title-wrap">
      <h2 class="page-title">Dashboard</h2>
      <p class="page-sub">Selamat datang kembali, <strong><?= htmlspecialchars($user['nama']) ?></strong> 👋</p>
    </div>

    <!-- STAT CARDS -->
    <div class="stat-grid">
      <div class="stat-card">
        <div class="stat-icon" style="background:#d1fae5;color:#065f46;"></div>
        <div class="stat-info">
          <span class="stat-num"><?= $totalMenu ?></span>
          <span class="stat-lbl">Menu Aktif</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:#fff7ed;color:#c2410c;"></div>
        <div class="stat-info">
          <span class="stat-num"><?= $totalPesanan ?></span>
          <span class="stat-lbl">Total Pesanan</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:#fef9c3;color:#a16207;"></div>
        <div class="stat-info">
          <span class="stat-num"><?= $pesananPending ?></span>
          <span class="stat-lbl">Pesanan Pending</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:#ede9fe;color:#6d28d9;"></div>
        <div class="stat-info">
          <span class="stat-num"><?= $totalStaff ?></span>
          <span class="stat-lbl">Jumlah Staff</span>
        </div>
      </div>
      <div class="stat-card stat-card-wide">
        <div class="stat-icon" style="background:#dcfce7;color:#15803d;"></div>
        <div class="stat-info">
          <span class="stat-num">Rp <?= number_format($pendapatanHari, 0, ',', '.') ?></span>
          <span class="stat-lbl">Pendapatan Hari Ini</span>
        </div>
      </div>
    </div>

    <!-- PESANAN TERBARU -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"> Pesanan Terbaru</h3>
        <a href="../staff/pesanan.php" class="btn-sm btn-outline-hijau">Lihat Semua →</a>
      </div>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Nama Pemesan</th>
              <th>Meja</th>
              <th>Total</th>
              <th>Status</th>
              <th>Waktu</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($pesananTerbaru)): ?>
              <tr><td colspan="6" class="td-empty">Belum ada pesanan masuk.</td></tr>
            <?php else: ?>
              <?php foreach ($pesananTerbaru as $p): ?>
              <tr>
                <td><code><?= htmlspecialchars($p['kode_pesanan']) ?></code></td>
                <td><?= htmlspecialchars($p['nama_pemesan']) ?></td>
                <td>Meja <?= htmlspecialchars($p['nomor_meja']) ?></td>
                <td>Rp <?= number_format($p['total_harga'], 0, ',', '.') ?></td>
                <td><span class="badge <?= statusBadge($p['status']) ?>"><?= ucfirst($p['status']) ?></span></td>
                <td><?= date('d/m H:i', strtotime($p['created_at'])) ?></td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div><!-- /page-body -->
</div><!-- /main-content -->

<script src="../js/dashboard.js"></script>
</body>
</html>