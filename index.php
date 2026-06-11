<?php
// ============================================================
//  index.php — Landing Page Dinamis
// ============================================================
require_once 'config/db.php';

// Ambil 4 menu unggulan (is_active=1, semua kategori, limit 4)
$stmt = $pdo->query("
    SELECT * FROM menu
    WHERE is_active = 1
    ORDER BY FIELD(badge,'Favorit #1','Favorit','Premium') DESC, id ASC
    LIMIT 4
");
$menuUnggulan = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="base-url" content="<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') ?>/">
  <title>DapurRumpin – Sajian Alam, Rasa Istimewa</title>
  <meta name="description" content="DapurRumpin – Restoran masakan nusantara dengan konsep alam segar di Rumpin, Bogor." />
  <link rel="icon" type="image/jpg" href="img/logo.jpg">
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>

  <!-- ===== NAVBAR ===== -->
  <nav class="navbar">
    <div class="navbar-brand">
      <div class="brand-logo">
        <img src="img/logo.jpg" alt="Logo DapurRumpin" class="logo-img" />
      </div>
      <span class="brand-text">Dapur<span>Rumpin</span></span>
    </div>
    <ul class="nav-links">
      <li><a href="index.php" class="active">Beranda</a></li>
      <li><a href="menu.php">Menu</a></li>
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

  <!-- ===== HERO ===== -->
  <section class="hero">
    <div class="hero-content">
      <div class="hero-badge">🌿 Masakan Nusantara Autentik</div>
      <h1>Nikmati <em>Cita Rasa Alam</em> yang Tak Terlupakan</h1>
      <p>DapurRumpin menyajikan masakan Nusantara dengan bahan-bahan segar pilihan, dimasak dengan penuh cinta di tengah suasana alam Rumpin yang asri.</p>
      <div class="hero-buttons">
        <a href="menu.php" class="btn btn-primary"> Lihat Menu Lengkap</a>
        <a href="tentang.php" class="btn btn-outline">Tentang Kami →</a>
      </div>
    </div>
  </section>

  <!-- ===== STATS ===== -->
  <div class="stats-bar">
    <div class="stats-grid">
      <?php
        $totalMenu = $pdo->query("SELECT COUNT(*) FROM menu WHERE is_active=1")->fetchColumn();
      ?>
      <div class="stat-item">
        <span class="stat-number"><?= $totalMenu ?>+</span>
        <span class="stat-label">Pilihan Menu</span>
      </div>
      <div class="stat-item">
        <span class="stat-number">2022</span>
        <span class="stat-label">Berdiri Sejak</span>
      </div>
      <div class="stat-item">
        <span class="stat-number">50+</span>
        <span class="stat-label">Pelanggan Harian</span>
      </div>
      <div class="stat-item">
        <span class="stat-number">4.0⭐</span>
        <span class="stat-label">Rating Google</span>
      </div>
    </div>
  </div>

  <!-- ===== KEUNGGULAN ===== -->
  <section class="features-section">
    <div class="text-center">
      <span class="section-tag">Mengapa Kami?</span>
      <h2 class="section-title">Keunggulan DapurRumpin</h2>
      <p class="section-sub">Kami berkomitmen memberikan pengalaman makan yang tidak hanya memuaskan selera, tapi juga menyehatkan jiwa.</p>
    </div>
    <div class="features-grid">
      <div class="feature-card fade-up delay-1">
        <div class="feature-icon">🌱</div>
        <h3>Bahan Segar Lokal</h3>
        <p>Semua bahan kami ambil langsung dari petani lokal Rumpin dan sekitarnya setiap pagi.</p>
      </div>
      <div class="feature-card fade-up delay-2">
        <div class="feature-icon">👨‍🍳</div>
        <h3>Chef Berpengalaman</h3>
        <p>Dimasak oleh juru masak berpengalaman yang menjaga keaslian resep turun-temurun.</p>
      </div>
      <div class="feature-card fade-up delay-3">
        <div class="feature-icon">🏡</div>
        <h3>Suasana Alam</h3>
        <p>Nikmati makan di tengah suasana alam terbuka yang menyegarkan dan nyaman.</p>
      </div>
      <div class="feature-card fade-up">
        <div class="feature-icon">💰</div>
        <h3>Harga Terjangkau</h3>
        <p>Kualitas restoran premium dengan harga yang ramah di kantong untuk semua kalangan.</p>
      </div>
    </div>
  </section>

  <!-- ===== MENU PREVIEW (DINAMIS) ===== -->
  <section class="menu-preview-section">
    <div class="text-center">
      <span class="section-tag">Pilihan Menu</span>
      <h2 class="section-title">Menu Unggulan Kami</h2>
      <p class="section-sub">Cicipi kelezatan terbaik dari dapur DapurRumpin. Semua dibuat segar setiap harinya.</p>
    </div>
    <div class="menu-preview-grid">
      <?php foreach ($menuUnggulan as $m):
        $hargaFmt = 'Rp ' . number_format($m['harga'], 0, ',', '.');
        $gambar   = $m['gambar'] ?: '';
        $badge    = htmlspecialchars($m['badge'] ?? '');
        $nama     = htmlspecialchars($m['nama'], ENT_QUOTES);
        $desc     = htmlspecialchars($m['deskripsi'] ?? '', ENT_QUOTES);
        $imgJs    = htmlspecialchars($gambar, ENT_QUOTES);
      ?>
      <div class="menu-card"
           onclick="bukaModal('<?= $nama ?>','<?= $hargaFmt ?>','<?= $desc ?>','<?= $imgJs ?>','<?= $badge ?>')">
        <div class="menu-card-img">
          <?php if ($gambar): ?>
            <img src="<?= htmlspecialchars($gambar) ?>" alt="<?= $nama ?>" loading="lazy"
                 onerror="this.src='https://images.unsplash.com/photo-1512058564366-18510be2db19?w=400&q=75'" />
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

      <?php if (empty($menuUnggulan)): ?>
        <p style="text-align:center;color:var(--abu);grid-column:1/-1;padding:30px;">
          Menu sedang diperbarui. Silakan cek kembali nanti.
        </p>
      <?php endif; ?>
    </div>
    <div class="text-center">
      <a href="menu.php" class="btn btn-hijau">🍽️ Lihat Semua <?= $totalMenu ?> Menu</a>
    </div>
  </section>

  <!-- ===== TENTANG PREVIEW ===== -->
  <section class="tentang-section">
    <div class="tentang-grid">
      <div class="tentang-img-wrap">
        <div class="tentang-img-main">
          <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=700&q=80"
               alt="Suasana DapurRumpin" loading="lazy" />
        </div>
        <div class="tentang-img-badge">
          <span class="num">2022</span>
          <span class="lbl">Berdiri Sejak</span>
        </div>
      </div>
      <div class="tentang-content">
        <span class="section-tag">Tentang Kami</span>
        <h2 class="section-title">Dapur Penuh Cinta dari Rumpin</h2>
        <p class="section-sub">DapurRumpin lahir dari keinginan menghadirkan masakan rumahan autentik di Rumpin, Bogor. Berbekal kepercayaan pelanggan lokal dan bahan-bahan segar dari pemasok sekitar Rumpin dan Parung, kami terus berkembang hingga menjadi restoran kebanggaan masyarakat setempat.</p>
        <ul class="tentang-list">
          <li>Didirikan Agustus 2022 dengan semangat memasak masakan rumahan autentik</li>
          <li>Bahan baku segar dari pemasok lokal wilayah Rumpin dan Parung, Bogor</li>
          <li>Tersedia layanan dine-in, take away, dan reservasi tempat</li>
          <li>43+ menu pilihan dengan harga terjangkau Rp5.000 – Rp98.000</li>
        </ul>
        <a href="tentang.php" class="btn btn-hijau">Kenali Kami Lebih Dekat →</a>
      </div>
    </div>
  </section>

  <!-- ===== TESTIMONIAL ===== -->
  <section class="testimonial-section">
    <div class="text-center">
      <span class="section-tag">Ulasan Pelanggan</span>
      <h2 class="section-title">Apa Kata Mereka?</h2>
      <p class="section-sub">Kepuasan pelanggan adalah motivasi terbesar kami untuk terus berinovasi.</p>
    </div>
    <div class="testimonial-grid">
      <div class="testi-card">
        <div class="testi-stars">★★★★★</div>
        <p class="testi-text">"Pertama kali coba makan disini tau tempat ini rekomendasi sodara saya, makananya enak banget! Porsinya pas, sambalnya pedasnya juga pas di lidah. Tempatnya bersih dan nyaman buat makan bareng keluarga. Harganya juga masih terjangkau untuk kualitas makanan seperti ini. Pasti bakal balik lagi!"</p>
        <div class="testi-author">
          <div class="testi-avatar">👩</div>
          <div><div class="testi-name">Rika Shintia</div><div class="testi-loc">Bogor, Jawa Barat</div></div>
        </div>
      </div>
      <div class="testi-card">
        <div class="testi-stars">★★★★★</div>
        <p class="testi-text">"Baru pertama kali coba makan di dapur rumpin dan langsung jatuh cinta! Makanannya benar-benar luar biasa, terutama sop iga nya yang empuk dan bumbu meresap sempurna. Suasananya elegan namun tetap nyaman untuk makan bareng keluarga. Sangat direkomendasikan!"</p>
        <div class="testi-author">
          <div class="testi-avatar">👨</div>
          <div><div class="testi-name">Arya Haidar</div><div class="testi-loc">Tangerang, Banten</div></div>
        </div>
      </div>
      <div class="testi-card">
        <div class="testi-stars">★★★★★</div>
        <p class="testi-text">"Tempat terbaik untuk family dinner, ada mini zoo juga. Makanannya juara, staff nya ramah dan edukatif menjelaskan menu special hari ini. Makan datang panas dan rasanya lezat. Worth it the price dan pasti akan kembali lagi!"</p>
        <div class="testi-author">
          <div class="testi-avatar">👨</div>
          <div><div class="testi-name">Fahdilah Putra</div><div class="testi-loc">Jakarta Selatan</div></div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== FOOTER ===== -->
  <?php include 'includes/footer.php'; ?>

  <!-- ===== MODAL ===== -->
  <?php include 'includes/modal_menu.php'; ?>

  <!-- ===== CART & CHECKOUT ===== -->
  <?php include 'includes/cart_panel.php'; ?>

  <button class="scroll-top" id="scrollTop">↑</button>
  <script src="js/main.js"></script>
</body>
</html>