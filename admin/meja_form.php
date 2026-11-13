<?php
// ============================================================
//  admin/meja_form.php — Tambah & Edit Meja
//  Role: owner only
//  Mode tambah: GET tanpa ?id=
//  Mode edit:   GET dengan ?id=X
// ============================================================
require_once '../includes/auth_check.php';
authCheck('admin');
require_once '../config/db.php';

$user    = currentUser();
$id      = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit  = $id > 0;
$errors  = [];
$meja    = [
    'nomor_meja' => '',
    'nama_meja'  => '',
    'qr_token'   => '',
    'is_active'  => 1,
];

// ── Kalau edit, ambil data lama ──────────────────────────
if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM meja WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
        $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Meja tidak ditemukan.'];
        header('Location: meja_list.php'); exit;
    }
    $meja = $row;
}

// ── Regenerate token via POST ─────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['regenerate_token'])) {
    if (!empty($_POST['csrf_token']) && $_POST['csrf_token'] === ($_SESSION['csrf_token'] ?? '')) {
        if ($isEdit) {
            $newToken = bin2hex(random_bytes(32));
            $stmt = $pdo->prepare("UPDATE meja SET qr_token = ? WHERE id = ?");
            $stmt->execute([$newToken, $id]);
            $_SESSION['flash'] = ['type'=>'success', 'msg'=>'QR Token berhasil di-regenerate. QR Code lama tidak akan valid lagi.'];
            header('Location: meja_form.php?id=' . $id); exit;
        }
    }
}

// ── Proses simpan form ────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_meja'])) {

    // CSRF
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $errors[] = 'Token tidak valid. Silakan ulangi.';
    } else {

        // Sanitize input
        $meja['nomor_meja'] = trim($_POST['nomor_meja'] ?? '');
        $meja['nama_meja']  = trim($_POST['nama_meja'] ?? '');
        $meja['is_active']  = isset($_POST['is_active']) ? 1 : 0;

        // Validasi wajib
        if ($meja['nomor_meja'] === '') {
            $errors[] = 'Nomor meja wajib diisi.';
        }

        // Cek duplikat nomor meja
        if ($meja['nomor_meja'] !== '') {
            $sqlCheck = "SELECT id FROM meja WHERE nomor_meja = ?";
            $paramsCheck = [$meja['nomor_meja']];
            if ($isEdit) {
                $sqlCheck .= " AND id != ?";
                $paramsCheck[] = $id;
            }
            $stmtCheck = $pdo->prepare($sqlCheck);
            $stmtCheck->execute($paramsCheck);
            if ($stmtCheck->fetch()) {
                $errors[] = 'Nomor meja "' . $meja['nomor_meja'] . '" sudah digunakan.';
            }
        }

        // Simpan ke DB kalau tidak ada error
        if (empty($errors)) {
            if ($isEdit) {
                // Edit: qr_token TIDAK berubah
                $stmt = $pdo->prepare("
                    UPDATE meja SET nomor_meja = ?, nama_meja = ?, is_active = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $meja['nomor_meja'], $meja['nama_meja'],
                    $meja['is_active'], $id
                ]);
                $_SESSION['flash'] = ['type'=>'success', 'msg'=>'Data meja berhasil diperbarui.'];
            } else {
                // Tambah baru: auto-generate qr_token
                $qrToken = bin2hex(random_bytes(32));
                $stmt = $pdo->prepare("
                    INSERT INTO meja (nomor_meja, nama_meja, qr_token, is_active)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([
                    $meja['nomor_meja'], $meja['nama_meja'],
                    $qrToken, $meja['is_active']
                ]);
                $_SESSION['flash'] = ['type'=>'success', 'msg'=>'Meja baru berhasil ditambahkan.'];
            }
            header('Location: meja_list.php'); exit;
        }
    }
}

// Refresh CSRF
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $isEdit ? 'Edit' : 'Tambah' ?> Meja – DapurRumpin</title>
  <link rel="icon" type="image/jpg" href="../img/logo.jpg">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap">
  <link rel="stylesheet" href="../css/dashboard.css">
  <style>
    .token-display {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-top: 8px;
    }
    .token-code {
      font-family: 'Courier New', monospace;
      font-size: 0.78rem;
      background: #f1f5f9;
      padding: 8px 12px;
      border-radius: 6px;
      border: 1px solid #e2e8f0;
      word-break: break-all;
      flex: 1;
      color: var(--abu);
    }
    .btn-regenerate {
      padding: 6px 14px;
      border-radius: 50px;
      background: #fff7ed;
      color: #c2410c;
      border: 1px solid #fed7aa;
      font-size: 0.78rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      white-space: nowrap;
    }
    .btn-regenerate:hover { background: #c2410c; color: #fff; }
  </style>
</head>
<body>

<?php include '_sidebar.php'; ?>

<div class="main-content">
  <?php include '_topbar.php'; ?>

  <div class="page-body">
    <div class="page-title-wrap">
      <div>
        <h2 class="page-title"><?= $isEdit ? '✏️ Edit Meja' : '➕ Tambah Meja Baru' ?></h2>
        <p class="page-sub"><a href="meja_list.php" class="breadcrumb-link">Kelola Meja</a> › <?= $isEdit ? 'Edit' : 'Tambah' ?></p>
      </div>
    </div>

    <!-- Errors -->
    <?php if (!empty($errors)): ?>
      <div class="alert alert-error">
        <strong>⚠️ Terdapat kesalahan:</strong>
        <ul style="margin:8px 0 0 18px;">
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <div class="form-card">
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <input type="hidden" name="simpan_meja" value="1">

        <div class="form-grid-2">

          <!-- Nomor Meja -->
          <div class="form-group-dash">
            <label class="form-label">Nomor Meja <span class="req">*</span></label>
            <input type="text" name="nomor_meja" class="form-input"
                   value="<?= htmlspecialchars($meja['nomor_meja']) ?>"
                   placeholder="Contoh: 1, 2, 3, VIP1" required>
            <small class="form-hint">Harus unik. Bisa berupa angka atau kode (misal: VIP1).</small>
          </div>

          <!-- Nama Meja -->
          <div class="form-group-dash">
            <label class="form-label">Nama/Keterangan Meja</label>
            <input type="text" name="nama_meja" class="form-input"
                   value="<?= htmlspecialchars($meja['nama_meja'] ?? '') ?>"
                   placeholder="Contoh: Meja Teras Depan (opsional)">
            <small class="form-hint">Deskripsi lokasi meja (opsional).</small>
          </div>

          <!-- Status Aktif -->
          <div class="form-group-dash form-col-2">
            <label class="toggle-label">
              <input type="checkbox" name="is_active" value="1"
                     <?= $meja['is_active'] ? 'checked' : '' ?>>
              <span class="toggle-switch"></span>
              <span class="toggle-text">Meja Aktif (QR Code dapat digunakan customer)</span>
            </label>
          </div>

          <!-- QR Token (hanya tampil saat edit) -->
          <?php if ($isEdit && $meja['qr_token']): ?>
          <div class="form-group-dash form-col-2">
            <label class="form-label">QR Token</label>
            <div class="token-display">
              <code class="token-code"><?= htmlspecialchars($meja['qr_token']) ?></code>
            </div>
            <small class="form-hint" style="margin-top:6px;">
              Token ini digunakan di URL QR Code. Jangan diubah manual kecuali ingin invalidasi QR yang sudah dicetak.
            </small>
          </div>

          <!-- Tombol Regenerate Token (terpisah dari form simpan) -->
          <div class="form-group-dash form-col-2" style="margin-top:-10px;">
            <form method="POST" onsubmit="return confirm('⚠️ Regenerate token akan membuat QR Code lama tidak valid!\n\nLanjutkan?')" style="display:inline;">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
              <input type="hidden" name="regenerate_token" value="1">
              <button type="submit" class="btn-regenerate">🔄 Regenerate Token</button>
            </form>
          </div>
          <?php endif; ?>

        </div><!-- /form-grid-2 -->

        <div class="form-actions">
          <a href="meja_list.php" class="btn-secondary-dash">✕ Batal</a>
          <button type="submit" class="btn-primary-dash">
            <?= $isEdit ? '💾 Simpan Perubahan' : '+ Tambah Meja' ?>
          </button>
        </div>

      </form>
    </div>

    <!-- Info QR Code (hanya saat edit) -->
    <?php if ($isEdit): ?>
    <div class="card" style="margin-top:20px;">
      <div class="card-header">
        <span class="card-title">📱 QR Code Meja Ini</span>
        <a href="meja_qr.php?id=<?= $id ?>" class="btn-sm btn-outline-hijau">Lihat & Cetak QR</a>
      </div>
    </div>
    <?php endif; ?>

  </div>
</div>

<script src="../js/dashboard.js"></script>
</body>
</html>
