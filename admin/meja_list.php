<?php
// ============================================================
//  admin/meja_list.php — Daftar & Kelola Meja
//  Role: owner only
// ============================================================
require_once '../includes/auth_check.php';
authCheck('admin');
require_once '../config/db.php';

$user = currentUser();

// ── Flash message ─────────────────────────────────────────
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// ── Toggle status aktif via POST ──────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status'])) {
    if (!empty($_POST['csrf_token']) && $_POST['csrf_token'] === ($_SESSION['csrf_token'] ?? '')) {
        $mejaId    = (int)$_POST['meja_id'];
        $newStatus = (int)$_POST['new_status'];
        if ($mejaId > 0) {
            $stmt = $pdo->prepare("UPDATE meja SET is_active = ? WHERE id = ?");
            $stmt->execute([$newStatus, $mejaId]);
            $statusLabel = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
            $_SESSION['flash'] = ['type'=>'success', 'msg'=>"Meja berhasil $statusLabel."];
        }
    }
    header('Location: meja_list.php'); exit;
}

// ── Ambil semua meja ──────────────────────────────────────
$mejas = $pdo->query("SELECT * FROM meja ORDER BY CAST(nomor_meja AS UNSIGNED), nomor_meja ASC")->fetchAll();

// ── Hitung pesanan aktif per meja ─────────────────────────
$pesananPerMeja = [];
$stmtPesanan = $pdo->query("
    SELECT nomor_meja, COUNT(*) as total 
    FROM pesanan 
    WHERE status IN ('pending', 'diproses') 
    GROUP BY nomor_meja
");
foreach ($stmtPesanan->fetchAll() as $row) {
    $pesananPerMeja[$row['nomor_meja']] = $row['total'];
}

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Meja – DapurRumpin</title>
  <link rel="icon" type="image/jpg" href="../img/logo.jpg">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap">
  <link rel="stylesheet" href="../css/dashboard.css">
  <style>
    .btn-qr {
      padding: 5px 10px;
      border-radius: 6px;
      background: #eff6ff;
      color: #1d4ed8;
      border: 1px solid #bfdbfe;
      font-size: 0.78rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 3px;
    }
    .btn-qr:hover { background: #1d4ed8; color: #fff; }

    .btn-toggle-aktif {
      padding: 5px 10px;
      border-radius: 6px;
      font-size: 0.78rem;
      font-weight: 600;
      cursor: pointer;
      border: none;
      transition: all 0.3s ease;
    }
    .btn-toggle-aktif.aktifkan { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .btn-toggle-aktif.aktifkan:hover { background: #15803d; color: #fff; }
    .btn-toggle-aktif.nonaktifkan { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
    .btn-toggle-aktif.nonaktifkan:hover { background: #dc2626; color: #fff; }

    .meja-pesanan-badge {
      display: inline-flex;
      align-items: center;
      gap: 3px;
      padding: 2px 8px;
      border-radius: 50px;
      font-size: 0.72rem;
      font-weight: 700;
      background: #fef9c3;
      color: #a16207;
      border: 1px solid #fde68a;
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
        <h2 class="page-title">🪑 Kelola Meja</h2>
        <p class="page-sub">Total <?= count($mejas) ?> meja terdaftar</p>
      </div>
      <a href="meja_form.php" class="btn-primary-dash">+ Tambah Meja Baru</a>
    </div>

    <!-- Flash Message -->
    <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] ?> alert-dismissible">
        <?= htmlspecialchars($flash['msg']) ?>
        <button onclick="this.parentElement.remove()">✕</button>
      </div>
    <?php endif; ?>

    <!-- Stat Cards -->
    <div class="stat-grid">
      <?php
        $aktif    = count(array_filter($mejas, fn($m) => $m['is_active']));
        $nonaktif = count($mejas) - $aktif;
      ?>
      <div class="stat-card">
        <div class="stat-icon" style="background:#dcfce7;color:#15803d;">🪑</div>
        <div>
          <span class="stat-num"><?= $aktif ?></span>
          <span class="stat-lbl">Meja Aktif</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:#fee2e2;color:#dc2626;">🚫</div>
        <div>
          <span class="stat-num"><?= $nonaktif ?></span>
          <span class="stat-lbl">Meja Nonaktif</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:#fef9c3;color:#a16207;">📋</div>
        <div>
          <span class="stat-num"><?= array_sum($pesananPerMeja) ?></span>
          <span class="stat-lbl">Pesanan Aktif</span>
        </div>
      </div>
    </div>

    <!-- Tabel Meja -->
    <div class="card">
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th width="50">#</th>
              <th>Nomor Meja</th>
              <th>Nama Meja</th>
              <th>Status</th>
              <th>Pesanan Aktif</th>
              <th>Dibuat</th>
              <th width="280">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($mejas)): ?>
              <tr><td colspan="7" class="td-empty">Belum ada meja terdaftar. Klik "Tambah Meja Baru" untuk mulai.</td></tr>
            <?php else: ?>
              <?php foreach ($mejas as $i => $m): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td>
                  <strong style="font-size:1.1rem;">Meja <?= htmlspecialchars($m['nomor_meja']) ?></strong>
                </td>
                <td><?= htmlspecialchars($m['nama_meja'] ?? '-') ?></td>
                <td>
                  <span class="badge <?= $m['is_active'] ? 'badge-selesai' : 'badge-dibatalkan' ?>">
                    <?= $m['is_active'] ? '✅ Aktif' : '🚫 Nonaktif' ?>
                  </span>
                </td>
                <td>
                  <?php $pa = $pesananPerMeja[$m['nomor_meja']] ?? 0; ?>
                  <?php if ($pa > 0): ?>
                    <span class="meja-pesanan-badge">📋 <?= $pa ?> pesanan</span>
                  <?php else: ?>
                    <span style="color:var(--abu-muda);font-size:0.85rem;">—</span>
                  <?php endif; ?>
                </td>
                <td><?= date('d/m/Y', strtotime($m['created_at'])) ?></td>
                <td>
                  <div class="action-btns" style="gap:6px;flex-wrap:wrap;">
                    <!-- Lihat QR -->
                    <a href="meja_qr.php?id=<?= $m['id'] ?>" class="btn-qr" title="Lihat QR Code">
                      📱 QR Code
                    </a>

                    <!-- Edit -->
                    <a href="meja_form.php?id=<?= $m['id'] ?>" class="btn-action btn-edit" title="Edit Meja">✏️</a>

                    <!-- Toggle Aktif/Nonaktif -->
                    <form method="POST" style="display:inline;" onsubmit="return confirm('<?= $m['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?> meja ini?')">
                      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                      <input type="hidden" name="toggle_status" value="1">
                      <input type="hidden" name="meja_id" value="<?= $m['id'] ?>">
                      <input type="hidden" name="new_status" value="<?= $m['is_active'] ? 0 : 1 ?>">
                      <button type="submit" class="btn-toggle-aktif <?= $m['is_active'] ? 'nonaktifkan' : 'aktifkan' ?>">
                        <?= $m['is_active'] ? '🚫 Nonaktifkan' : '✅ Aktifkan' ?>
                      </button>
                    </form>
                  </div>
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

<script src="../js/dashboard.js"></script>
</body>
</html>
