<?php
// tentang.php — rename dari tentang.html
// Tidak butuh DB, konten statis
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tentang Kami – DapurRumpin</title>
  <meta name="description" content="DapurRumpin – Sejarah, visi, dan tim kami di balik hidangan autentik Nusantara." />
  <link rel="icon" type="image/jpg" href="img/logo.jpg">
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>

  <nav class="navbar">
    <div class="navbar-brand">
      <div class="brand-logo">
        <img src="img/logo.jpg" alt="Logo DapurRumpin" class="logo-img" />
      </div>
      <span class="brand-text">Dapur<span>Rumpin</span></span>
    </div>
    <ul class="nav-links">
      <li><a href="index.php">Beranda</a></li>
      <li><a href="menu.php">Menu</a></li>
      <li><a href="tentang.php" class="active">Tentang</a></li>
      <li><a href="kontak.php">Kontak</a></li>
      <li><a href="kontak.php" class="btn-pesan">📞 Pesan Sekarang</a></li>
    </ul>
    <div class="hamburger" id="hamburger" onclick="toggleMenu()">
      <span></span><span></span><span></span>
    </div>
  </nav>

  <div class="mobile-menu" id="mobileMenu">
    <a href="index.php">🏠 Beranda</a>
    <a href="menu.php">🍽️ Menu</a>
    <a href="tentang.php">ℹ️ Tentang Kami</a>
    <a href="kontak.php">📞 Kontak</a>
  </div>

  <div class="page-header">
    <div class="breadcrumb"><a href="index.php">Beranda</a> › Tentang Kami</div>
    <h1>🌿 Tentang DapurRumpin</h1>
    <p>Mengenal lebih dekat kisah di balik cita rasa autentik kami</p>
  </div>

  <!-- SEJARAH -->
  <section class="tentang-section tentang-full">
    <div class="tentang-grid">
      <div class="tentang-img-wrap">
        <div class="tentang-img-main">
          <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=700&q=80"
               alt="Interior DapurRumpin" loading="lazy" />
        </div>
        <div class="tentang-img-badge">
          <span class="num">2022</span>
          <span class="lbl">Berdiri Sejak</span>
        </div>
      </div>
      <div class="tentang-content">
        <span class="section-tag">Sejarah Kami</span>
        <h2 class="section-title">Lahir dari Cinta akan Masakan Nusantara</h2>
        <p class="section-sub">DapurRumpin bermula dari keinginan sederhana menghadirkan masakan rumahan yang autentik dan terjangkau di Rumpin, Kabupaten Bogor. Didirikan pada Agustus 2022, restoran ini berkembang pesat berkat kepercayaan pelanggan lokal.</p>
        <ul class="tentang-list">
          <li>Didirikan Agustus 2022 dari keinginan menghadirkan masakan rumahan autentik</li>
          <li>Berkembang berkat kepercayaan pelanggan lokal dan rekomendasi dari mulut ke mulut</li>
          <li>Bahan baku segar dari pemasok lokal Rumpin dan Parung, Kabupaten Bogor</li>
          <li>Terdaftar resmi dengan legalitas usaha (NIB) dan melayani 50+ pelanggan harian</li>
        </ul>
        <a href="menu.php" class="btn btn-hijau">Lihat Menu Kami →</a>
      </div>
    </div>
  </section>

  <!-- NILAI -->
  <section style="background:var(--putih);padding:90px 5%;">
    <div class="text-center">
      <span class="section-tag">Nilai Kami</span>
      <h2 class="section-title">Visi, Misi &amp; Nilai Kami</h2>
      <p class="section-sub"><strong>Visi:</strong> Menjadi restoran rumahan pilihan utama di Rumpin yang berkualitas dan terjangkau.</p>
    </div>
    <div class="nilai-grid">
      <div class="nilai-card"><div class="nilai-icon">🌱</div><h3>Bahan Lokal Segar</h3><p>Menggunakan bahan lokal segar dari pemasok Rumpin dan Parung setiap harinya.</p></div>
      <div class="nilai-card"><div class="nilai-icon">⭐</div><h3>Pelayanan Prima</h3><p>Memberikan pelayanan terbaik agar setiap pelanggan merasa dihargai dan puas.</p></div>
      <div class="nilai-card"><div class="nilai-icon">💡</div><h3>Inovasi Menu</h3><p>Terus berinovasi menghadirkan menu baru dan promo berkala untuk pelanggan setia.</p></div>
      <div class="nilai-card"><div class="nilai-icon">🤝</div><h3>Dukung Lokal</h3><p>Mendukung ekonomi lokal melalui kerjasama dengan pemasok dan komunitas Rumpin.</p></div>
      <div class="nilai-card"><div class="nilai-icon">✨</div><h3>Kualitas Terjaga</h3><p>Standar kebersihan dan kualitas masakan selalu dijaga konsisten setiap hari.</p></div>
      <div class="nilai-card"><div class="nilai-icon">💰</div><h3>Harga Terjangkau</h3><p>Menyajikan makanan berkualitas dengan harga terjangkau untuk semua kalangan.</p></div>
    </div>
  </section>

  <!-- TIM -->
  <section style="background:var(--hijau-pastel);padding:90px 5%;">
    <div class="text-center">
      <span class="section-tag">Tim Kami</span>
      <h2 class="section-title">Orang-orang di Balik Kelezatan</h2>
      <p class="section-sub">Tim DapurRumpin terdiri dari orang-orang berdedikasi yang mencintai kuliner Nusantara.</p>
    </div>
    <div class="features-grid" style="margin-top:50px;">
      <div class="feature-card">
        <div class="feature-icon" style="font-size:2rem;">👩‍🍳</div>
        <h3>Meisna Fitrianingrum</h3>
        <p><strong style="color:var(--hijau-utama)">Pengelola &amp; Kepala Dapur</strong><br/>Merangkap pengelola harian yang memastikan kualitas dan pelayanan selalu terjaga.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon" style="font-size:2rem;">👨‍🍳</div>
        <h3>Staff Dapur</h3>
        <p><strong style="color:var(--hijau-utama)">Karyawan Bagian Dapur</strong><br/>Bertanggung jawab atas pengolahan dan penyajian seluruh menu dengan standar kualitas tinggi.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon" style="font-size:2rem;">🧑‍💼</div>
        <h3>Staff Pelayanan</h3>
        <p><strong style="color:var(--hijau-utama)">Karyawan Bagian Pelayanan</strong><br/>Melayani pelanggan dengan ramah untuk pengalaman bersantap yang menyenangkan.</p>
      </div>
    </div>
  </section>

  <?php include 'includes/footer.php'; ?>

  <button class="scroll-top" id="scrollTop">↑</button>
  <script src="js/main.js"></script>
</body>
</html>