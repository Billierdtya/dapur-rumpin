<?php
// kontak.php — rename dari kontak.html
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kontak – DapurRumpin</title>
  <meta name="description" content="Hubungi DapurRumpin – Alamat, telepon, email, dan media sosial." />
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
      <li><a href="tentang.php">Tentang</a></li>
      <li><a href="kontak.php" class="active">Kontak</a></li>
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
    <div class="breadcrumb"><a href="index.php">Beranda</a> › Kontak</div>
    <h1>📞 Hubungi Kami</h1>
    <p>Kami siap membantu Anda – reservasi meja, pertanyaan menu, atau catering</p>
  </div>

  <section class="kontak-section">
    <div class="text-center">
      <span class="section-tag">Kontak &amp; Lokasi</span>
      <h2 class="section-title">Temukan &amp; Hubungi Kami</h2>
      <p class="section-sub">Kunjungi kami langsung atau hubungi melalui berbagai saluran komunikasi yang tersedia.</p>
    </div>
    <div class="kontak-grid">
      <div>
        <div class="kontak-info-item">
          <div class="kontak-icon">📍</div>
          <div>
            <h4>Alamat</h4>
            <p>Jl. Raya Rumpin No. 12, Desa Rumpin<br/>Kec. Rumpin, Kab. Bogor 16350<br/>Jawa Barat, Indonesia</p>
          </div>
        </div>
        <div class="kontak-info-item">
          <div class="kontak-icon">📞</div>
          <div>
            <h4>Telepon &amp; WhatsApp</h4>
            <p>(0251) 123-4567<br/>+62 856-4311-7734 (WA)</p>
          </div>
        </div>
        <div class="kontak-info-item">
          <div class="kontak-icon">✉️</div>
          <div>
            <h4>Email</h4>
            <p>dapurrumpin3@gmail.com</p>
          </div>
        </div>
        <div class="kontak-info-item">
          <div class="kontak-icon">🕐</div>
          <div>
            <h4>Jam Operasional</h4>
            <p>Senin – Rabu &amp; Jumat – Minggu<br/>12.00 – 22.00 WIB<br/><strong>Kamis: Tutup</strong></p>
          </div>
        </div>
        <div class="kontak-info-item">
          <div class="kontak-icon">🚗</div>
          <div>
            <h4>Akses &amp; Parkir</h4>
            <p>Parkir luas untuk motor dan mobil<br/>Mudah diakses dari Jalan Raya Parungpanjang</p>
          </div>
        </div>
      </div>

      <div>
        <h3 style="color:var(--putih);font-family:'Playfair Display',serif;font-size:1.3rem;margin-bottom:8px;">Hubungi Langsung</h3>
        <p style="color:rgba(255,255,255,0.6);font-size:0.88rem;margin-bottom:28px;">Pilih cara yang paling mudah untuk menghubungi kami.</p>

        <div style="display:flex;flex-direction:column;gap:14px;">
          <a href="tel:02511234567"
             class="kontak-link-card">
            <span style="font-size:2rem;">📞</span>
            <div>
              <div style="font-weight:700;font-size:1rem;">Telepon Langsung</div>
              <div style="font-size:0.83rem;color:rgba(255,255,255,0.65);margin-top:2px;">(0251) 123-4567 · Senin–Minggu 12.00–22.00</div>
            </div>
            <span style="margin-left:auto;">→</span>
          </a>

          <a href="mailto:dapurrumpin3@gmail.com" class="kontak-link-card">
            <span style="font-size:2rem;">✉️</span>
            <div>
              <div style="font-weight:700;font-size:1rem;">Kirim Email</div>
              <div style="font-size:0.83rem;color:rgba(255,255,255,0.65);margin-top:2px;">dapurrumpin3@gmail.com</div>
            </div>
            <span style="margin-left:auto;">→</span>
          </a>

          <a href="https://www.instagram.com/dapurrumpin/" target="_blank" class="kontak-link-card">
            <span style="font-size:2rem;">📷</span>
            <div>
              <div style="font-weight:700;font-size:1rem;">Instagram</div>
              <div style="font-size:0.83rem;color:rgba(255,255,255,0.65);margin-top:2px;">@dapurrumpin · Follow untuk promo terbaru</div>
            </div>
            <span style="margin-left:auto;">→</span>
          </a>

          <a href="https://wa.me/6285643117734" target="_blank" class="kontak-link-card" style="background:rgba(37,211,102,0.15);border-color:rgba(37,211,102,0.35);">
            <span style="font-size:2rem;">💬</span>
            <div>
              <div style="font-weight:700;font-size:1rem;">WhatsApp</div>
              <div style="font-size:0.83rem;color:rgba(255,255,255,0.65);margin-top:2px;">+62 856-4311-7734 · Chat langsung ke admin</div>
            </div>
            <span style="margin-left:auto;">→</span>
          </a>
        </div>
      </div>
    </div>

    <!-- PETA -->
    <div class="map-wrap">
      <iframe
        src="https://maps.google.com/maps?q=Resto%20Dapur%20Rumpin,%20Bogor&t=&z=15&ie=UTF8&iwloc=&output=embed"
        allowfullscreen="" loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
        title="Lokasi DapurRumpin di Rumpin, Bogor">
      </iframe>
    </div>
  </section>

  <?php include 'includes/footer.php'; ?>

  <button class="scroll-top" id="scrollTop">↑</button>
  <script src="js/main.js"></script>

  <style>
    .kontak-link-card {
      display: flex;
      align-items: center;
      gap: 16px;
      background: rgba(116,198,157,0.12);
      border: 1px solid rgba(116,198,157,0.3);
      padding: 18px 22px;
      border-radius: var(--radius-md);
      color: var(--putih);
      text-decoration: none;
      transition: var(--transition);
    }
    .kontak-link-card:hover { background: rgba(116,198,157,0.22); }
  </style>
</body>
</html>