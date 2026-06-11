<?php
// ============================================================
//  includes/footer.php — Footer halaman publik
//  Deteksi root otomatis supaya link tetap benar
//  dari folder manapun file ini di-include
// ============================================================
$base = (basename(dirname($_SERVER['SCRIPT_FILENAME'])) !== 'dapur-rumpin')
      ? '../' : '';
?>
<footer class="footer">
  <div class="footer-grid">
    <div class="footer-brand">
      <div class="footer-logo-wrap">
        <img src="<?= $base ?>img/logo.jpg" alt="Logo DapurRumpin" class="footer-logo-img"
             onerror="this.style.display='none'" />
        <span class="brand-text" style="font-family:'Playfair Display',serif;color:#fff;">
          Dapur<span style="color:var(--hijau-cerah)">Rumpin</span>
        </span>
      </div>
      <p class="footer-desc">Menyajikan kelezatan masakan tradisional Sunda dan Nusantara dengan bahan segar pilihan di jantung alam Rumpin, Bogor.</p>
      <div class="footer-social">
        <a href="https://www.instagram.com/dapurrumpin/" target="_blank" class="social-btn" title="Instagram">📷</a>
        <a href="#" class="social-btn" title="Facebook">📘</a>
        <a href="https://wa.me/6285643117734" target="_blank" class="social-btn" title="WhatsApp">💬</a>
        <a href="#" class="social-btn" title="TikTok">🎵</a>
      </div>
    </div>

    <div class="footer-col">
      <h4>Navigasi</h4>
      <ul>
        <li><a href="<?= $base ?>index.php">Beranda</a></li>
        <li><a href="<?= $base ?>menu.php">Menu</a></li>
        <li><a href="<?= $base ?>tentang.php">Tentang Kami</a></li>
        <li><a href="<?= $base ?>kontak.php">Kontak</a></li>
      </ul>
    </div>

    <div class="footer-col">
      <h4>Jam Buka</h4>
      <ul>
        <li><a href="#">Senin – Rabu</a></li>
        <li><a href="#">Jumat – Minggu</a></li>
        <li><a href="#">12.00 – 22.00 WIB</a></li>
        <li><a href="#" style="color:#fca5a5;">Kamis: Tutup</a></li>
      </ul>
    </div>

    <div class="footer-col">
      <h4>Kontak</h4>
      <ul>
        <li><a href="#">📍 Jl. Raya Rumpin No. 12, Bogor</a></li>
        <li><a href="tel:02511234567">📞 (0251) 123-4567</a></li>
        <li><a href="mailto:dapurrumpin3@gmail.com">✉️ dapurrumpin3@gmail.com</a></li>
      </ul>
    </div>
  </div>

  <div class="footer-bottom">
    <span>© <?= date('Y') ?> DapurRumpin. Hak Cipta Dilindungi.</span>
    <span>Dibuat dengan ❤️ untuk <a href="#">kuliner Nusantara</a></span>
  </div>
</footer>