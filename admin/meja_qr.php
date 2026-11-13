<?php
// ============================================================
//  admin/meja_qr.php — Tampilkan QR Code Meja
//  Role: owner only
//  Parameter: ?id=X
//  Fitur: gambar QR via Google Charts API, cetak print-friendly
// ============================================================
require_once '../includes/auth_check.php';
authCheck('admin');
require_once '../config/db.php';

$user = currentUser();
$id   = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['flash'] = ['type'=>'error', 'msg'=>'ID meja tidak valid.'];
    header('Location: meja_list.php'); exit;
}

$stmt = $pdo->prepare("SELECT * FROM meja WHERE id = ?");
$stmt->execute([$id]);
$meja = $stmt->fetch();

if (!$meja) {
    $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Meja tidak ditemukan.'];
    header('Location: meja_list.php'); exit;
}

// ── Bangun URL menu untuk QR Code ─────────────────────────
// Deteksi base URL dari server
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'];
$basePath = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\');
$menuUrl  = $protocol . '://' . $host . $basePath . '/menu.php?meja=' . urlencode($meja['nomor_meja']) . '&token=' . urlencode($meja['qr_token']);

// QR Server API (Pengganti Google Charts yang sudah ditutup)
$qrSize    = '350x350';
$qrApiUrl  = 'https://api.qrserver.com/v1/create-qr-code/?size=' . $qrSize . '&data=' . urlencode($menuUrl);

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>QR Code Meja <?= htmlspecialchars($meja['nomor_meja']) ?> – DapurRumpin</title>
  <link rel="icon" type="image/jpg" href="../img/logo.jpg">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap">
  <link rel="stylesheet" href="../css/dashboard.css">
  <style>
    /* ── QR Display Card ── */
    .qr-display-card {
      background: var(--putih);
      border-radius: var(--radius-lg);
      border: 1px solid var(--border);
      box-shadow: var(--shadow);
      text-align: center;
      padding: 40px 32px;
      max-width: 500px;
      margin: 0 auto;
    }

    .qr-meja-label {
      font-family: 'Playfair Display', serif;
      font-size: 1.8rem;
      font-weight: 800;
      color: var(--hijau-tua);
      margin-bottom: 4px;
    }

    .qr-meja-nama {
      font-size: 0.9rem;
      color: var(--abu);
      margin-bottom: 24px;
    }

    .qr-image-wrap {
      display: block;
      margin: 0 auto 20px auto;
      padding: 16px;
      background: #fff;
      border-radius: 16px;
      border: 3px solid var(--hijau-pucat);
      box-shadow: 0 4px 24px rgba(0,0,0,0.08);
      width: max-content;
    }

    .qr-image {
      width: 280px;
      height: 280px;
      border-radius: 8px;
    }

    .qr-url-display {
      font-family: 'Courier New', monospace;
      font-size: 0.72rem;
      background: #f1f5f9;
      padding: 10px 14px;
      border-radius: 8px;
      border: 1px solid #e2e8f0;
      word-break: break-all;
      color: var(--abu);
      margin-bottom: 20px;
      text-align: left;
    }

    .qr-status {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 16px;
      border-radius: 50px;
      font-size: 0.82rem;
      font-weight: 700;
      margin-bottom: 24px;
    }

    .qr-status.aktif { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .qr-status.nonaktif { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }

    .qr-actions {
      display: flex;
      gap: 10px;
      justify-content: center;
      flex-wrap: wrap;
    }

    .btn-print {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 10px 24px;
      background: var(--hijau-utama);
      color: var(--putih);
      border: none;
      border-radius: 50px;
      font-family: 'DM Sans', sans-serif;
      font-size: 0.9rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 16px rgba(45,106,79,0.3);
    }

    .btn-print:hover { background: var(--hijau-tua); transform: translateY(-2px); }

    .btn-copy-url {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 10px 24px;
      background: var(--putih);
      color: var(--abu);
      border: 1.5px solid var(--border);
      border-radius: 50px;
      font-family: 'DM Sans', sans-serif;
      font-size: 0.9rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-copy-url:hover { border-color: var(--abu); color: var(--gelap); }

    /* Sembunyikan area cetak di layar biasa */
    .print-area {
      display: none;
    }

    /* ════════════════════════════════════════════════════════
       PRINT STYLES — Hanya tampilkan QR saat cetak
       ════════════════════════════════════════════════════════ */
    @media print {
      /* Sembunyikan semua elemen non-print */
      body * { visibility: hidden; }

      .print-area, .print-area * {
        visibility: visible !important;
      }

      .print-area {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: #fff;
        padding: 40px;
      }

      .print-logo {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        margin-bottom: 12px;
      }

      .print-brand {
        font-family: 'Playfair Display', serif;
        font-size: 1.6rem;
        font-weight: 800;
        margin-bottom: 4px;
      }

      .print-meja-label {
        font-family: 'Playfair Display', serif;
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 4px;
      }

      .print-meja-nama {
        font-size: 1rem;
        color: #666;
        margin-bottom: 20px;
      }

      .print-qr {
        width: 300px;
        height: 300px;
        border: 3px solid #ddd;
        border-radius: 12px;
        margin-bottom: 16px;
      }

      .print-instruction {
        font-size: 1.1rem;
        color: #333;
        margin-bottom: 8px;
        font-weight: 600;
      }

      .print-sub-instruction {
        font-size: 0.85rem;
        color: #888;
      }

      /* Sembunyikan elemen yang tidak perlu di print */
      .sidebar, .topbar, .page-title-wrap, .no-print,
      .qr-url-display, .qr-actions, .qr-status,
      .breadcrumb-link { display: none !important; }
    }
  </style>
</head>
<body>

<?php include '_sidebar.php'; ?>

<div class="main-content">
  <?php include '_topbar.php'; ?>

  <div class="page-body">
    <div class="page-title-wrap no-print">
      <div>
        <h2 class="page-title">📱 QR Code Meja</h2>
        <p class="page-sub">
          <a href="meja_list.php" class="breadcrumb-link">Kelola Meja</a> ›
          QR Code Meja <?= htmlspecialchars($meja['nomor_meja']) ?>
        </p>
      </div>
      <a href="meja_list.php" class="btn-secondary-dash">← Kembali</a>
    </div>

    <!-- QR Display Card -->
    <div class="qr-display-card">
      <div class="qr-meja-label">🪑 Meja <?= htmlspecialchars($meja['nomor_meja']) ?></div>
      <?php if ($meja['nama_meja']): ?>
        <div class="qr-meja-nama"><?= htmlspecialchars($meja['nama_meja']) ?></div>
      <?php endif; ?>

      <div class="qr-status <?= $meja['is_active'] ? 'aktif' : 'nonaktif' ?>">
        <?= $meja['is_active'] ? '✅ Meja Aktif' : '🚫 Meja Nonaktif' ?>
      </div>

      <div class="qr-image-wrap">
        <img src="<?= htmlspecialchars($qrApiUrl) ?>" alt="QR Code Meja <?= htmlspecialchars($meja['nomor_meja']) ?>"
             class="qr-image" id="qrImage"
             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22280%22 height=%22280%22%3E%3Crect fill=%22%23f1f5f9%22 width=%22280%22 height=%22280%22/%3E%3Ctext x=%22140%22 y=%22130%22 text-anchor=%22middle%22 fill=%22%2394a3b8%22 font-size=%2214%22%3EGagal memuat QR%3C/text%3E%3Ctext x=%22140%22 y=%22155%22 text-anchor=%22middle%22 fill=%22%2394a3b8%22 font-size=%2212%22%3ECoba refresh halaman%3C/text%3E%3C/svg%3E'">
      </div>

      <div class="qr-url-display" id="qrUrl"><?= htmlspecialchars($menuUrl) ?></div>

      <div class="qr-actions">
        <button class="btn-print" onclick="window.print()">
          🖨️ Cetak QR Code
        </button>
        <button class="btn-copy-url" onclick="copyUrl()" id="btnCopyUrl">
          📋 Salin URL
        </button>
      </div>
    </div>

  </div>
</div>

<!-- Area Print (hanya terlihat saat cetak) -->
<div class="print-area">
  <img src="../img/logo.jpg" alt="Logo" class="print-logo" onerror="this.style.display='none'">
  <div class="print-brand">DapurRumpin</div>
  <div class="print-meja-label">Meja <?= htmlspecialchars($meja['nomor_meja']) ?></div>
  <?php if ($meja['nama_meja']): ?>
    <div class="print-meja-nama"><?= htmlspecialchars($meja['nama_meja']) ?></div>
  <?php endif; ?>
  <img src="<?= htmlspecialchars($qrApiUrl) ?>" alt="QR Code" class="print-qr">
  <div class="print-instruction">Scan QR Code untuk Pesan</div>
  <div class="print-sub-instruction">Arahkan kamera HP Anda ke QR Code di atas</div>
</div>

<script src="../js/dashboard.js"></script>
<script>
// Salin URL ke clipboard
function copyUrl() {
  var url = document.getElementById('qrUrl').textContent;
  var btn = document.getElementById('btnCopyUrl');

  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(url).then(function() {
      btn.innerHTML = '✅ URL Tersalin!';
      setTimeout(function() { btn.innerHTML = '📋 Salin URL'; }, 2000);
    }).catch(function() {
      fallbackCopy(url, btn);
    });
  } else {
    fallbackCopy(url, btn);
  }
}

function fallbackCopy(text, btn) {
  var textarea = document.createElement('textarea');
  textarea.value = text;
  textarea.style.position = 'fixed';
  textarea.style.opacity = '0';
  document.body.appendChild(textarea);
  textarea.select();
  try {
    document.execCommand('copy');
    btn.innerHTML = '✅ URL Tersalin!';
    setTimeout(function() { btn.innerHTML = '📋 Salin URL'; }, 2000);
  } catch(e) {
    alert('Gagal menyalin. Salin manual: ' + text);
  }
  document.body.removeChild(textarea);
}
</script>
</body>
</html>
