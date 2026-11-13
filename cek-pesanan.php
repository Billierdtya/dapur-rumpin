<?php
// ============================================================
//  cek-pesanan.php — Cek Status Pesanan (Customer)
//  Tidak butuh login. Customer input kode pesanan.
// ============================================================
require_once 'config/db.php';

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'];
$script   = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl  = $protocol . '://' . $host . rtrim($script, '/') . '/';

$pesanan = null;
$items   = [];
$error   = '';
$kodeInput = trim($_GET['kode'] ?? $_POST['kode'] ?? '');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Auto-fill dari session jika tidak ada input manual dari user
if ($kodeInput === '' && !empty($_SESSION['last_kode_pesanan'])) {
    $kodeInput = $_SESSION['last_kode_pesanan'];
}

if ($kodeInput !== '') {
    $kodeInput = strtoupper($kodeInput);

    $stmt = $pdo->prepare("SELECT * FROM pesanan WHERE kode_pesanan = ? LIMIT 1");
    $stmt->execute([$kodeInput]);
    $pesanan = $stmt->fetch();

    if (!$pesanan) {
        $error = 'Kode pesanan <strong>' . htmlspecialchars($kodeInput) . '</strong> tidak ditemukan. Pastikan kode yang kamu masukkan sudah benar.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM detail_pesanan WHERE pesanan_id = ?");
        $stmt->execute([$pesanan['id']]);
        $items = $stmt->fetchAll();
    }
}

// ── Data tampilan per status ─────────────────────────────
$statusData = [
    'pending'    => [
        'label'   => 'Menunggu Konfirmasi',
        'icon'    => '⏳',
        'color'   => '#a16207',
        'bg'      => '#fef9c3',
        'border'  => '#fde68a',
        'desc'    => 'Pesananmu sudah kami terima dan sedang menunggu konfirmasi dari tim dapur.',
        'step'    => 1,
    ],
    'diproses'   => [
        'label'   => 'Sedang Dimasak',
        'icon'    => '👨‍🍳',
        'color'   => '#1d4ed8',
        'bg'      => '#dbeafe',
        'border'  => '#bfdbfe',
        'desc'    => 'Pesananmu sedang dimasak oleh tim dapur kami dengan penuh cinta. Harap sabar ya!',
        'step'    => 2,
    ],
    'selesai'    => [
        'label'   => 'Siap Disajikan',
        'icon'    => '',
        'color'   => '#15803d',
        'bg'      => '#dcfce7',
        'border'  => '#bbf7d0',
        'desc'    => 'Pesananmu sudah siap! Silakan nikmati hidangan dari DapurRumpin.',
        'step'    => 3,
    ],
    'dibatalkan' => [
        'label'   => 'Dibatalkan',
        'icon'    => '❌',
        'color'   => '#dc2626',
        'bg'      => '#fee2e2',
        'border'  => '#fecaca',
        'desc'    => 'Maaf, pesananmu dibatalkan. Silakan hubungi staff kami untuk informasi lebih lanjut.',
        'step'    => 0,
    ],
];

$sd = $pesanan ? ($statusData[$pesanan['status']] ?? $statusData['pending']) : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cek Status Pesanan – DapurRumpin</title>
  <meta name="description" content="Cek status pesananmu di DapurRumpin secara real-time." />
  <link rel="icon" type="image/jpg" href="<?= $baseUrl ?>img/logo.jpg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= $baseUrl ?>css/style.css" />
  <style>
    /* ── Halaman khusus tracking ── */
    .tracking-wrap {
      min-height: 100vh;
      background: var(--hijau-pastel);
      padding: 100px 5% 60px;
    }

    .tracking-container {
      max-width: 640px;
      margin: 0 auto;
    }

    /* Search box */
    .search-card {
      background: var(--putih);
      border-radius: var(--radius-lg);
      padding: 36px 32px;
      box-shadow: var(--shadow-soft);
      margin-bottom: 28px;
      border: 1px solid rgba(82,183,136,0.15);
    }

    .search-card h2 {
      font-family: 'Playfair Display', serif;
      font-size: 1.5rem;
      font-weight: 800;
      color: var(--hijau-tua);
      margin-bottom: 6px;
    }

    .search-card p {
      font-size: 0.88rem;
      color: var(--abu);
      margin-bottom: 22px;
    }

    .search-form {
      display: flex;
      gap: 10px;
    }

    .search-form input {
      flex: 1;
      padding: 13px 16px;
      border: 2px solid rgba(82,183,136,0.35);
      border-radius: 50px;
      font-family: 'DM Sans', sans-serif;
      font-size: 0.95rem;
      color: var(--gelap);
      outline: none;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .search-form input:focus {
      border-color: var(--hijau-muda);
      box-shadow: 0 0 0 3px rgba(82,183,136,0.15);
    }

    .search-form input::placeholder {
      text-transform: none;
      letter-spacing: 0;
      color: #a0aec0;
    }

    .btn-cek {
      padding: 13px 24px;
      background: var(--hijau-utama);
      color: var(--putih);
      border: none;
      border-radius: 50px;
      font-family: 'DM Sans', sans-serif;
      font-size: 0.92rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      white-space: nowrap;
      box-shadow: 0 4px 16px rgba(45,106,79,0.3);
    }

    .btn-cek:hover {
      background: var(--hijau-tua);
      transform: translateY(-2px);
    }

    /* Error */
    .error-box {
      background: #fff1f2;
      border: 1px solid #fecdd3;
      color: #be123c;
      padding: 14px 18px;
      border-radius: var(--radius-md);
      font-size: 0.88rem;
      line-height: 1.6;
      margin-bottom: 20px;
    }

    /* Result card */
    .result-card {
      background: var(--putih);
      border-radius: var(--radius-lg);
      overflow: hidden;
      box-shadow: var(--shadow-soft);
      border: 1px solid rgba(82,183,136,0.15);
      animation: fadeUp 0.5s ease both;
    }

    @keyframes fadeUp {
      from { opacity:0; transform:translateY(20px); }
      to   { opacity:1; transform:translateY(0); }
    }

    /* Status banner */
    .status-banner {
      padding: 28px 28px 24px;
      display: flex;
      align-items: center;
      gap: 18px;
    }

    .status-icon-big {
      width: 64px;
      height: 64px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      flex-shrink: 0;
    }

    .status-info h3 {
      font-family: 'Playfair Display', serif;
      font-size: 1.3rem;
      font-weight: 800;
      margin-bottom: 4px;
    }

    .status-info p {
      font-size: 0.85rem;
      opacity: 0.75;
      line-height: 1.55;
    }

    /* Progress tracker */
    .progress-wrap {
      padding: 20px 28px 24px;
      border-top: 1px solid #f1f5f9;
    }

    .progress-steps {
      display: flex;
      align-items: center;
      gap: 0;
      position: relative;
    }

    .step-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
      flex: 1;
      position: relative;
      z-index: 1;
    }

    .step-circle {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.1rem;
      border: 3px solid #e2e8f0;
      background: var(--putih);
      transition: all 0.4s ease;
      position: relative;
      z-index: 2;
    }

    .step-circle.done {
      background: var(--hijau-muda);
      border-color: var(--hijau-muda);
    }

    .step-circle.active {
      background: var(--kuning-aksen);
      border-color: var(--kuning-aksen);
      box-shadow: 0 0 0 4px rgba(244,162,97,0.25);
      animation: pulse 1.8s ease-in-out infinite;
    }

    @keyframes pulse {
      0%, 100% { box-shadow: 0 0 0 4px rgba(244,162,97,0.25); }
      50%       { box-shadow: 0 0 0 8px rgba(244,162,97,0.1); }
    }

    .step-label {
      font-size: 0.72rem;
      font-weight: 600;
      color: var(--abu);
      text-align: center;
      line-height: 1.3;
    }

    .step-label.active { color: var(--hijau-tua); }
    .step-label.done   { color: var(--hijau-utama); }

    /* Garis penghubung antar step */
    .step-line {
      flex: 1;
      height: 3px;
      background: #e2e8f0;
      margin-bottom: 24px;
      transition: background 0.4s ease;
      position: relative;
      z-index: 0;
    }

    .step-line.done { background: var(--hijau-muda); }

    /* Dibatalkan */
    .cancelled-bar {
      padding: 20px 28px;
      border-top: 1px solid #f1f5f9;
      text-align: center;
      font-size: 0.88rem;
      color: #dc2626;
      background: #fff5f5;
    }

    /* Detail pesanan */
    .detail-wrap {
      padding: 20px 28px 28px;
      border-top: 1px solid #f1f5f9;
    }

    .detail-title {
      font-size: 0.78rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1.2px;
      color: var(--hijau-utama);
      margin-bottom: 14px;
    }

    .order-meta {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
      margin-bottom: 18px;
    }

    .meta-item {
      background: var(--hijau-pastel);
      border-radius: var(--radius-sm);
      padding: 10px 14px;
    }

    .meta-item .meta-lbl {
      font-size: 0.72rem;
      color: var(--abu);
      text-transform: uppercase;
      letter-spacing: 0.8px;
      display: block;
      margin-bottom: 3px;
    }

    .meta-item .meta-val {
      font-size: 0.9rem;
      font-weight: 700;
      color: var(--hijau-tua);
    }

    /* Tabel item */
    .items-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.88rem;
      margin-bottom: 16px;
    }

    .items-table th {
      padding: 8px 10px;
      text-align: left;
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--hijau-utama);
      background: var(--hijau-pastel);
      border-bottom: 2px solid var(--hijau-pucat);
    }

    .items-table td {
      padding: 10px 10px;
      border-bottom: 1px solid #f1f5f9;
      color: var(--gelap);
    }

    .items-table tr:last-child td { border-bottom: none; }

    .total-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 14px 16px;
      background: var(--hijau-pastel);
      border-radius: var(--radius-md);
    }

    .total-row span:first-child {
      font-weight: 700;
      color: var(--hijau-tua);
      font-size: 0.92rem;
    }

    .total-row span:last-child {
      font-family: 'Playfair Display', serif;
      font-size: 1.2rem;
      font-weight: 800;
      color: var(--hijau-utama);
    }

    /* Waktu & refresh */
    .card-footer-track {
      padding: 14px 28px;
      border-top: 1px solid #f1f5f9;
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: 0.8rem;
      color: var(--abu);
      background: #f8fafc;
    }

    .btn-refresh {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 14px;
      border-radius: 50px;
      border: 1.5px solid rgba(82,183,136,0.4);
      background: var(--putih);
      color: var(--hijau-utama);
      font-family: 'DM Sans', sans-serif;
      font-size: 0.8rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
    }

    .btn-refresh:hover {
      background: var(--hijau-utama);
      color: var(--putih);
      border-color: var(--hijau-utama);
    }

    /* WA button */
    .btn-wa-track {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      width: 100%;
      padding: 13px;
      background: #25d366;
      color: var(--putih);
      border: none;
      border-radius: 50px;
      font-family: 'DM Sans', sans-serif;
      font-size: 0.9rem;
      font-weight: 700;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.3s ease;
      box-shadow: 0 4px 16px rgba(37,211,102,0.3);
      margin-top: 16px;
    }

    .btn-wa-track:hover {
      background: #1da851;
      transform: translateY(-2px);
    }

    /* Tips box */
    .tips-box {
      background: var(--putih);
      border-radius: var(--radius-lg);
      padding: 22px 24px;
      margin-top: 20px;
      border: 1px solid rgba(82,183,136,0.15);
      box-shadow: var(--shadow-soft);
    }

    .tips-box h4 {
      font-size: 0.85rem;
      font-weight: 700;
      color: var(--hijau-tua);
      margin-bottom: 12px;
    }

    .tips-list {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .tips-item {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      font-size: 0.83rem;
      color: var(--abu);
      line-height: 1.5;
    }

    .tips-item span:first-child { flex-shrink: 0; font-size: 1rem; }

    /* Auto refresh indicator */
    .refresh-indicator {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 0.75rem;
      color: var(--hijau-utama);
    }

    .refresh-dot {
      width: 8px; height: 8px;
      background: var(--hijau-muda);
      border-radius: 50%;
      animation: blink 2s ease-in-out infinite;
    }

    @keyframes blink {
      0%, 100% { opacity:1; }
      50%       { opacity:0.3; }
    }

    @media (max-width: 640px) {
      .tracking-wrap  { padding: 88px 4% 50px; }
      .search-card    { padding: 24px 20px; }
      .search-form    { flex-direction: column; }
      .status-banner  { padding: 20px; gap: 14px; }
      .detail-wrap    { padding: 16px 20px 24px; }
      .order-meta     { grid-template-columns: 1fr; }
      .card-footer-track { flex-direction: column; gap: 10px; align-items: flex-start; }
      .status-icon-big{ width: 52px; height: 52px; font-size: 1.6rem; }
      .step-label     { font-size: 0.65rem; }
    }
  </style>
</head>
<body>

  <!-- NAVBAR -->
  <nav class="navbar">
    <div class="navbar-brand">
      <div class="brand-logo">
        <img src="<?= $baseUrl ?>img/logo.jpg" alt="Logo DapurRumpin" class="logo-img"
             onerror="this.style.display='none'" />
      </div>
      <span class="brand-text">Dapur<span>Rumpin</span></span>
    </div>
    <ul class="nav-links">
      <li><a href="<?= $baseUrl ?>index.php">Beranda</a></li>
      <li><a href="<?= $baseUrl ?>menu.php">Menu</a></li>
      <li><a href="<?= $baseUrl ?>tentang.php">Tentang</a></li>
      <li><a href="<?= $baseUrl ?>kontak.php">Kontak</a></li>
      <li><a href="<?= $baseUrl ?>kontak.php" class="btn-pesan">📞 Pesan Sekarang</a></li>
    </ul>
    <div class="hamburger" id="hamburger" onclick="toggleMenu()">
      <span></span><span></span><span></span>
    </div>
  </nav>

  <div class="mobile-menu" id="mobileMenu">
    <a href="<?= $baseUrl ?>index.php">Beranda</a>
    <a href="<?= $baseUrl ?>menu.php">Menu</a>
    <a href="<?= $baseUrl ?>tentang.php">Tentang Kami</a>
    <a href="<?= $baseUrl ?>kontak.php">Kontak</a>
  </div>

  <div class="tracking-wrap">
    <div class="tracking-container">

      <!-- Search Box -->
      <div class="search-card">
        <h2>🔍 Cek Status Pesanan</h2>
        <p>Masukkan kode pesanan yang kamu terima saat checkout untuk melihat status terkini.</p>

        <?php if ($error): ?>
          <div class="error-box"><?= $error ?></div>
        <?php endif; ?>

        <form method="GET" action="cek-pesanan.php" class="search-form">
          <input
            type="text"
            name="kode"
            value="<?= htmlspecialchars($kodeInput) ?>"
            placeholder="Contoh: DR-20260611-0001"
            maxlength="30"
            autocomplete="off"
            autofocus
          />
          <button type="submit" class="btn-cek">🔍 Cek Sekarang</button>
        </form>
      </div>

      <!-- Hasil Pencarian -->
      <?php if ($pesanan && $sd): ?>
      <div class="result-card" id="resultCard">

        <!-- Status Banner -->
        <div class="status-banner" style="background:<?= $sd['bg'] ?>;border-bottom:2px solid <?= $sd['border'] ?>;">
          <div class="status-icon-big" style="background:<?= $sd['border'] ?>;color:<?= $sd['color'] ?>;">
            <?= $sd['icon'] ?>
          </div>
          <div class="status-info" style="color:<?= $sd['color'] ?>;">
            <h3><?= $sd['label'] ?></h3>
            <p><?= $sd['desc'] ?></p>
          </div>
        </div>

        <!-- Progress Steps (sembunyikan kalau dibatalkan) -->
        <?php if ($pesanan['status'] !== 'dibatalkan'): ?>
        <div class="progress-wrap">
          <div class="progress-steps">

            <!-- Step 1: Pesanan Diterima -->
            <div class="step-item">
              <div class="step-circle <?= $sd['step'] >= 1 ? ($sd['step'] === 1 ? 'active' : 'done') : '' ?>">
                <?= $sd['step'] > 1 ? '✓' : '📥' ?>
              </div>
              <span class="step-label <?= $sd['step'] >= 1 ? ($sd['step'] === 1 ? 'active' : 'done') : '' ?>">
                Pesanan<br>Diterima
              </span>
            </div>

            <div class="step-line <?= $sd['step'] >= 2 ? 'done' : '' ?>"></div>

            <!-- Step 2: Sedang Dimasak -->
            <div class="step-item">
              <div class="step-circle <?= $sd['step'] >= 2 ? ($sd['step'] === 2 ? 'active' : 'done') : '' ?>">
                <?= $sd['step'] > 2 ? '✓' : '👨‍🍳' ?>
              </div>
              <span class="step-label <?= $sd['step'] >= 2 ? ($sd['step'] === 2 ? 'active' : 'done') : '' ?>">
                Sedang<br>Dimasak
              </span>
            </div>

            <div class="step-line <?= $sd['step'] >= 3 ? 'done' : '' ?>"></div>

            <!-- Step 3: Siap Disajikan -->
            <div class="step-item">
              <div class="step-circle <?= $sd['step'] === 3 ? 'done' : '' ?>">
                <?= $sd['step'] === 3 ? '✓' : '🍽️' ?>
              </div>
              <span class="step-label <?= $sd['step'] === 3 ? 'done' : '' ?>">
                Siap<br>Disajikan
              </span>
            </div>

          </div>
        </div>
        <?php else: ?>
        <div class="cancelled-bar">
          ❌ Pesanan ini telah dibatalkan. Hubungi staff kami untuk informasi lebih lanjut.
        </div>
        <?php endif; ?>

        <!-- Detail Pesanan -->
        <div class="detail-wrap">
          <p class="detail-title">Informasi Pesanan</p>

          <div class="order-meta">
            <div class="meta-item">
              <span class="meta-lbl">Kode Pesanan</span>
              <span class="meta-val" style="font-size:0.82rem;letter-spacing:0.5px;">
                <?= htmlspecialchars($pesanan['kode_pesanan']) ?>
              </span>
            </div>
            <div class="meta-item">
              <span class="meta-lbl">Nama Pemesan</span>
              <span class="meta-val"><?= htmlspecialchars($pesanan['nama_pemesan']) ?></span>
            </div>
            <div class="meta-item">
              <span class="meta-lbl">Nomor Meja</span>
              <span class="meta-val">Meja <?= htmlspecialchars($pesanan['nomor_meja']) ?></span>
            </div>
            <div class="meta-item">
              <span class="meta-lbl">Waktu Pesan</span>
              <span class="meta-val" style="font-size:0.82rem;">
                <?= date('d M Y, H:i', strtotime($pesanan['created_at'])) ?> WIB
              </span>
            </div>
          </div>

          <p class="detail-title" style="margin-top:18px;">Rincian Item</p>
          <table class="items-table">
            <thead>
              <tr>
                <th>Menu</th>
                <th style="text-align:center;">Qty</th>
                <th style="text-align:right;">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $item): ?>
              <tr>
                <td><?= htmlspecialchars($item['nama_menu']) ?></td>
                <td style="text-align:center;"><?= $item['qty'] ?>x</td>
                <td style="text-align:right;">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <div class="total-row">
            <span>Total Pembayaran</span>
            <span>Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></span>
          </div>

          <!-- Tombol WA & QRIS -->
          <?php if ($pesanan['status'] === 'pending'): ?>
            <!-- QRIS Section -->
            <div class="qris-wrap" style="margin-top:20px; padding:20px; background:#fff; border:1px solid #e2e8f0; border-radius:12px; text-align:center;">
              <h4 style="margin-bottom:12px; color:#1a3c2b; font-size:1.1rem;">Lakukan Pembayaran QRIS</h4>
              <p class="qris-amount" style="font-size:1.4rem; font-weight:800; color:#2d6a4f; margin-bottom:16px;">Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></p>
              <div class="qris-img-frame" style="width:200px; height:200px; margin:0 auto 16px; border:2px solid #52b788; border-radius:12px; overflow:hidden;">
                <img src="img/qris.jpeg" alt="QRIS Dapur Rumpin" style="width:100%; height:100%; object-fit:cover;"
                     onerror="this.parentElement.innerHTML='<span>📱 QRIS Dapur Rumpin<br><small>Letakkan file QRIS di img/qris.jpeg</small></span>'" />
              </div>
              <div class="qris-instructions" style="text-align:left; font-size:0.85rem; color:#5a6472; margin-bottom:20px; line-height:1.5;">
                <strong>Cara Bayar via QRIS:</strong>
                <ol style="margin-top:6px; padding-left:20px;">
                  <li>Buka aplikasi dompet digital (GoPay, OVO, Dana, dll)</li>
                  <li>Scan QR Code di atas dan bayar sesuai total tagihan</li>
                  <li>Klik tombol konfirmasi di bawah ini agar pesananmu segera diproses!</li>
                </ol>
              </div>
              <?php
                $waMsg  = "*KONFIRMASI PEMBAYARAN*\n\n";
                $waMsg .= "Nama  : " . $pesanan['nama_pemesan'] . "\n";
                $waMsg .= "Meja  : " . $pesanan['nomor_meja'] . "\n";
                $waMsg .= "Kode  : " . $pesanan['kode_pesanan'] . "\n\n";
                $waMsg .= "*Detail Pesanan:*\n";
                foreach ($items as $item) {
                    $waMsg .= "• " . $item['nama_menu'] . " x" . $item['qty'] . " = Rp " . number_format($item['subtotal'], 0, ',', '.') . "\n";
                }
                $waMsg .= "\n*Total: Rp " . number_format($pesanan['total_harga'], 0, ',', '.') . "*\n\n";
                $waMsg .= "Saya sudah melakukan pembayaran via QRIS. Mohon dikonfirmasi.";
                $waUrl  = "https://wa.me/627775551647?text=" . urlencode($waMsg);
              ?>
              <a href="<?= htmlspecialchars($waUrl) ?>" target="_blank" class="btn-wa-track" style="background:#25d366;">
                💬 Konfirmasi Pembayaran via WhatsApp
              </a>
            </div>
          <?php else: ?>
            <!-- Tanya Status Biasa -->
            <?php
              $waMsg  = "Halo DapurRumpin! Saya ingin menanyakan status pesanan saya.\n\n";
              $waMsg .= "🔖 Kode Pesanan : " . $pesanan['kode_pesanan'] . "\n";
              $waMsg .= "👤 Nama         : " . $pesanan['nama_pemesan'] . "\n";
              $waMsg .= "🪑 Meja         : " . $pesanan['nomor_meja'] . "\n";
              $waMsg .= "📊 Status saat ini: " . ucfirst($pesanan['status']) . "\n\n";
              $waMsg .= "Mohon konfirmasinya, terima kasih!";
              $waUrl  = "https://wa.me/627775551647?text=" . urlencode($waMsg);
            ?>
            <a href="<?= htmlspecialchars($waUrl) ?>" target="_blank" class="btn-wa-track" style="margin-top:20px;">
              💬 Tanya Status via WhatsApp
            </a>
            
            <?php if ($pesanan['status'] === 'selesai'): ?>
            <a href="cetak_struk.php?kode=<?= urlencode($pesanan['kode_pesanan']) ?>" target="_blank" class="btn-wa-track" style="background:#1e293b; margin-top:10px;">
              🖨️ Cetak Struk Pembelian
            </a>
            <?php endif; ?>
          <?php endif; ?>

        </div>

        <!-- Footer card: waktu update & refresh -->
        <div class="card-footer-track">
          <div class="refresh-indicator">
            <div class="refresh-dot"></div>
            <span>Terakhir diperbarui: <?= date('H:i:s') ?> WIB</span>
          </div>
          <a href="cek-pesanan.php?kode=<?= urlencode($pesanan['kode_pesanan']) ?>"
             class="btn-refresh">
            🔄 Refresh
          </a>
        </div>

      </div><!-- /result-card -->
      <?php endif; ?>

      <!-- Tips box (tampil kalau belum ada hasil) -->
      <?php if (!$pesanan): ?>
      <div class="tips-box">
        <h4>💡 Cara mendapatkan kode pesanan:</h4>
        <div class="tips-list">
          <div class="tips-item">
            <span>1️⃣</span>
            <span>Kode pesanan dikirim otomatis ke pesan WhatsApp konfirmasi kamu setelah checkout.</span>
          </div>
          <div class="tips-item">
            <span>2️⃣</span>
            <span>Format kode: <strong>DR-YYYYMMDD-XXXX</strong>, contoh: <strong>DR-20260611-0001</strong></span>
          </div>
          <div class="tips-item">
            <span>3️⃣</span>
            <span>Belum punya kode? Hubungi staff kami via WhatsApp untuk bantuan.</span>
          </div>
          <div class="tips-item">
            <span>4️⃣</span>
            <span>Halaman ini otomatis refresh setiap 30 detik saat kode pesanan sudah dimasukkan.</span>
          </div>
        </div>
        <a href="https://wa.me/6285643117734" target="_blank" class="btn-wa-track" style="margin-top:14px;">
          💬 Hubungi Staff via WhatsApp
        </a>
      </div>
      <?php endif; ?>

    </div>
  </div>

  <?php include 'includes/footer.php'; ?>

  <button class="scroll-top" id="scrollTop">↑</button>
  <script src="<?= $baseUrl ?>js/main.js"></script>
  <script>
    // ── Real-time Status Polling (Tanpa Refresh) ──
    <?php if ($pesanan && $pesanan['status'] !== 'selesai' && $pesanan['status'] !== 'dibatalkan'): ?>
    var currentKode = '<?= htmlspecialchars($pesanan['kode_pesanan']) ?>';
    
    setInterval(function() {
      fetch('cek-pesanan.php?kode=' + encodeURIComponent(currentKode))
        .then(response => response.text())
        .then(html => {
          var parser = new DOMParser();
          var doc = parser.parseFromString(html, 'text/html');
          var newCard = doc.getElementById('resultCard');
          var oldCard = document.getElementById('resultCard');
          
          if (newCard && oldCard) {
            oldCard.innerHTML = newCard.innerHTML;
            
            // Hentikan polling jika pesanan sudah selesai/batal dari konten yang baru diambil
            if (html.includes('Siap Disajikan') || html.includes('Dibatalkan')) {
                // Biarkan update terjadi sekali lalu setInterval akan terus jalan, tapi bisa dibiarkan
                // Atau lebih baik kita reload saja sekali jika statusnya final agar state JS berhenti
                if (!window.hasReloadedForFinalState) {
                    window.hasReloadedForFinalState = true;
                    setTimeout(() => window.location.reload(), 2000);
                }
            }
          }
        })
        .catch(err => console.error('Error updating status:', err));
    }, 5000); // Polling setiap 5 detik
    <?php endif; ?>

    // ── Auto uppercase input kode pesanan ──
    var inputKode = document.querySelector('.search-form input[name="kode"]');
    if (inputKode) {
      inputKode.addEventListener('input', function() {
        var pos = this.selectionStart;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(pos, pos);
      });
    }

    // ── Scroll ke hasil otomatis kalau ada result ──
    <?php if ($pesanan): ?>
    window.addEventListener('DOMContentLoaded', function() {
      var card = document.getElementById('resultCard');
      if (card) {
        setTimeout(function() {
          card.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 300);
      }
    });
    <?php endif; ?>
  </script>
</body>
</html>