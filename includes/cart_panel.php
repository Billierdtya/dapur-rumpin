<!-- ============================================================
     includes/cart_panel.php — Cart panel + Checkout modal
     Di-include oleh index.php dan menu.php
     ============================================================ -->

<!-- Floating Cart Button -->
<button class="cart-float" onclick="bukaKeranjang()" title="Lihat Keranjang">
  🛒
  <span class="cart-badge" style="display:none;">0</span>
</button>

<!-- Cart Overlay + Side Panel -->
<div class="cart-overlay" id="cartOverlay" onclick="tutupKeranjang()"></div>
<div class="cart-panel" id="cartPanel">
  <div class="cart-panel-header">
    <h3>🛒 Keranjang Pesanan</h3>
    <button class="cart-close-btn" onclick="tutupKeranjang()">✕</button>
  </div>
  <div class="cart-item-list" id="cartItemList"></div>
  <div class="cart-panel-footer">
    <div class="cart-total-row">
      <span class="cart-total-label">Total Pembayaran</span>
      <span class="cart-total-harga" id="cartTotal">Rp 0</span>
    </div>
    <div class="cart-actions">
      <button class="btn-cart-clear"
              onclick="if(confirm('Kosongkan keranjang?')){saveCart([]);updateCartBadge();renderKeranjang();}">
        🗑 Kosongkan
      </button>
      <button class="btn-checkout" onclick="bukaCheckout()">Checkout →</button>
    </div>
  </div>
</div>

<!-- Checkout Modal -->
<div class="checkout-overlay" id="checkoutModal">
  <div class="checkout-box">
    <div class="checkout-header">
      <h3>📋 Checkout Pesanan</h3>
      <button class="cart-close-btn" onclick="tutupCheckout()">✕</button>
    </div>
    <div class="checkout-body">

      <!-- STEP 1: Ringkasan + Form -->
      <div id="checkoutStep1">
        <p class="checkout-section-title">Ringkasan Pesanan</p>
        <div id="checkoutSummary"></div>
        <div class="checkout-divider"></div>
        <div class="checkout-total-row">
          <span>Total</span>
          <span id="checkoutTotal">Rp 0</span>
        </div>
        <p class="checkout-section-title">Data Pemesan</p>
        <div class="checkout-form-group">
          <label for="inputNamaPemesan">Nama Pemesan *</label>
          <input type="text" id="inputNamaPemesan" placeholder="Contoh: Budi Santoso" />
        </div>
        <div class="checkout-form-group">
          <label for="inputNomorMeja">Nomor Meja *</label>
          <input type="text" id="inputNomorMeja" placeholder="Contoh: 5" />
        </div>
        <button class="btn-bayar" onclick="prosesBayar()">💳 Lanjut ke Pembayaran QRIS</button>
      </div>

      <!-- STEP 2: QRIS -->
      <div id="checkoutStep2" style="display:none;">
        <div class="qris-wrap">
          <div class="qris-info-box">
            <strong id="qrisNamaPemesan">-</strong> &nbsp;|&nbsp;
            <strong id="qrisNomorMeja">-</strong>
          </div>
          <p class="qris-amount" id="qrisTotal">Rp 0</p>
          <div class="qris-img-frame">
            <img src="img/qris.jpeg" alt="QRIS Dapur Rumpin"
                 onerror="this.parentElement.innerHTML='<span>📱 QRIS Dapur Rumpin<br><small>Letakkan file QRIS di img/qris.jpeg</small></span>'" />
          </div>
          <div class="qris-instructions">
            <strong>Cara Bayar via QRIS:</strong>
            <ol>
              <li>Buka aplikasi dompet digital (GoPay, OVO, Dana, dll)</li>
              <li>Pilih menu <strong>Scan QR / Bayar</strong></li>
              <li>Arahkan kamera ke QR Code di atas</li>
              <li>Masukkan nominal sesuai total tagihan</li>
              <li>Konfirmasi pembayaran</li>
              <li>Klik tombol WhatsApp di bawah untuk konfirmasi ke admin</li>
            </ol>
          </div>
          <button class="btn-wa-konfirmasi" onclick="konfirmasiWA()">
            💬 Konfirmasi Pembayaran via WhatsApp
          </button>
        </div>
      </div>

    </div>
  </div>
</div>