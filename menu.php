<?php
// ============================================================
//  menu.php — Halaman Menu Dinamis
// ============================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/db.php';

// ── QR Code Scan Logic ────────────────────────────────────
$qrMessage = null;
$qrType    = 'success';

if (isset($_GET['meja'], $_GET['token'])) {
    $mejaNum = trim($_GET['meja']);
    $token   = trim($_GET['token']);
    
    $stmt = $pdo->prepare("SELECT id, is_active FROM meja WHERE nomor_meja = ? AND qr_token = ?");
    $stmt->execute([$mejaNum, $token]);
    $mejaDb = $stmt->fetch();
    
    if ($mejaDb && $mejaDb['is_active']) {
        $_SESSION['qr_meja']  = $mejaNum;
        $_SESSION['qr_valid'] = true;
        $qrMessage = "✅ Berhasil terhubung ke Meja $mejaNum. Silakan pesan menu Anda.";
        
        // Log keberhasilan
        $stmtLog = $pdo->prepare("INSERT INTO qr_logs (nomor_meja, status, ip_address, user_agent) VALUES (?, 'valid', ?, ?)");
        $stmtLog->execute([$mejaNum, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']);
    } else {
        $qrMessage = "❌ QR Code tidak valid atau meja tidak aktif.";
        $qrType    = 'error';
        // Hapus session jika scan gagal
        unset($_SESSION['qr_meja'], $_SESSION['qr_valid']);

        // Log kegagalan
        $stmtLog = $pdo->prepare("INSERT INTO qr_logs (nomor_meja, status, ip_address, user_agent) VALUES (?, 'invalid', ?, ?)");
        $stmtLog->execute([$mejaNum, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']);
    }
}

// Ambil semua menu aktif, dikelompokkan per kategori
$stmt  = $pdo->query("SELECT * FROM menu WHERE is_active = 1 ORDER BY kategori, nama ASC");
$semua = $stmt->fetchAll();

$totalMenu = count($semua);

// Kelompokkan
$grouped = ['makanan' => [], 'cemilan' => [], 'minuman' => []];
foreach ($semua as $m) {
    if (isset($grouped[$m['kategori']])) {
        $grouped[$m['kategori']][] = $m;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="base-url" content="<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') ?>/">
  <title>Menu – DapurRumpin</title>
  <meta name="description" content="Daftar menu lengkap DapurRumpin – pilihan makanan dan minuman lezat." />
  <link rel="icon" type="image/jpg" href="img/logo.jpg">
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>

  <!-- NAVBAR -->
  <nav class="navbar">
    <div class="navbar-brand">
      <div class="brand-logo">
        <img src="img/logo.jpg" alt="Logo DapurRumpin" class="logo-img" />
      </div>
      <span class="brand-text">Dapur<span>Rumpin</span></span>
    </div>
    <ul class="nav-links">
      <li><a href="index.php">Beranda</a></li>
      <li><a href="menu.php" class="active">Menu</a></li>
      <li><a href="tentang.php">Tentang</a></li>
      <li><a href="kontak.php">Kontak</a></li>
      <li><a href="kontak.php" class="btn-pesan"> Pesan Sekarang</a></li>
    </ul>
    <div class="hamburger" id="hamburger" onclick="toggleMenu()">
      <span></span><span></span><span></span>
    </div>
  </nav>

  <div class="mobile-menu" id="mobileMenu">
    <a href="index.php"> Beranda</a>
    <a href="menu.php"> Menu</a>
    <a href="tentang.php">ℹ Tentang Kami</a>
    <a href="kontak.php"> Kontak</a>
  </div>

  <!-- PAGE HEADER -->
  <div class="page-header">
    <div class="breadcrumb">
      <a href="index.php">Beranda</a> › Menu
    </div>
    <h1> Menu Lengkap Kami</h1>
    <p>
      <?= $totalMenu ?> pilihan lezat –
      <?= count($grouped['makanan']) ?> makanan,
      <?= count($grouped['cemilan']) ?> cemilan &amp;
      <?= count($grouped['minuman']) ?> minuman,
      dimasak dengan cinta setiap hari
    </p>
  </div>

  <?php if ($qrMessage): ?>
    <div style="max-width: 800px; margin: 20px auto; padding: 12px 20px; border-radius: 8px; text-align: center; font-weight: 600; <?php echo $qrType === 'success' ? 'background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;' : 'background: #fee2e2; color: #dc2626; border: 1px solid #fecaca;'; ?>">
      <?= htmlspecialchars($qrMessage) ?>
    </div>
  <?php endif; ?>

  <!-- MENU SECTION -->
  <section class="menu-page" style="padding-top:50px;">

    <!-- TABS -->
    <div class="menu-tabs">
      <button class="tab-btn active" onclick="filterMenu('semua',this)"> Semua Menu</button>
      <button class="tab-btn" onclick="filterMenu('makanan',this)"> Makanan</button>
      <button class="tab-btn" onclick="filterMenu('cemilan',this)"> Cemilan</button>
      <button class="tab-btn" onclick="filterMenu('minuman',this)"> Minuman</button>
    </div>

    <!-- GRID MENU -->
    <div class="menu-grid" id="menuGrid">

      <?php if (empty($semua)): ?>
        <p style="grid-column:1/-1;text-align:center;color:var(--abu);padding:40px;">
          Menu sedang diperbarui. Silakan cek kembali nanti.
        </p>
      <?php endif; ?>

      <?php foreach ($semua as $m):
        $hargaFmt = 'Rp ' . number_format($m['harga'], 0, ',', '.');
        $gambar   = $m['gambar'] ?: '';
        $badge    = htmlspecialchars($m['badge'] ?? '');
        $nama     = htmlspecialchars($m['nama'], ENT_QUOTES);
        $desc     = htmlspecialchars($m['deskripsi'] ?? '', ENT_QUOTES);
        $imgJs    = htmlspecialchars($gambar, ENT_QUOTES);
        $kat      = htmlspecialchars($m['kategori']);
      ?>
      <div class="menu-card" data-kat="<?= $kat ?>"
           onclick="bukaModal('<?= $nama ?>','<?= $hargaFmt ?>','<?= $desc ?>','<?= $imgJs ?>','<?= $badge ?>')">
        <div class="menu-card-img">
          <?php if ($gambar): ?>
            <img src="<?= htmlspecialchars($gambar) ?>"
                 alt="<?= $nama ?>"
                 loading="lazy"
                 onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&q=75'" />
          <?php else: ?>
            <span style="font-size:3rem;"></span>
          <?php endif; ?>
          <?php if ($badge): ?>
            <span class="menu-badge"><?= $badge ?></span>
          <?php endif; ?>
        </div>
        <div class="menu-card-body">
          <h3><?= htmlspecialchars($m['nama']) ?></h3>
          <div class="harga"><?= $hargaFmt ?></div>
          <p class="desc-preview"><?= htmlspecialchars(substr($m['deskripsi'] ?? '', 0, 80)) ?>...</p>
          <button class="btn-detail">Lihat Detail →</button>
        </div>
      </div>
      <?php endforeach; ?>

    </div><!-- /menu-grid -->
  </section>

  <!-- FOOTER -->
  <?php include 'includes/footer.php'; ?>

  <!-- MODAL -->
  <?php include 'includes/modal_menu.php'; ?>

  <!-- CART & CHECKOUT -->
  <?php include 'includes/cart_panel.php'; ?>

  <button class="scroll-top" id="scrollTop">↑</button>
  <script src="js/main.js?v=<?= time() ?>"></script>
</body>
</html>