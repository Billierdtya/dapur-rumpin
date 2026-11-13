<!-- ============================================================
     includes/cart_panel.php — Cart panel + Checkout modal
     Di-include oleh index.php dan menu.php
     ============================================================ -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$qrMejaValue = $_SESSION['qr_meja'] ?? '';
$isQrValid   = !empty($_SESSION['qr_valid']) && !empty($qrMejaValue);
?>

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
          <?php if ($isQrValid): ?>
            <input type="text" id="inputNomorMeja" value="<?= htmlspecialchars($qrMejaValue) ?>" readonly style="background-color: #f1f5f9; cursor: not-allowed; color: #64748b; border-color: #cbd5e1;" />
            <small style="color: #059669; font-size: 0.75rem; display: block; margin-top: 4px;">✅ Otomatis terisi (terhubung via QR Code)</small>
          <?php else: ?>
            <input type="text" id="inputNomorMeja" placeholder="Contoh: 5" />
          <?php endif; ?>
        </div>
        <button class="btn-bayar" onclick="prosesBayar()">💳 Lanjut ke Pembayaran QRIS</button>
      </div>



    </div>
  </div>
</div>