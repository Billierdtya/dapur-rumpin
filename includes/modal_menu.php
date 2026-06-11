<!-- ============================================================
     includes/modal_menu.php — Modal detail menu
     Di-include oleh index.php dan menu.php
     ============================================================ -->
<div class="modal-overlay" id="modalOverlay" onclick="tutupModal(event)">
  <div class="modal-box">
    <button class="modal-close" onclick="tutupModal()">✕</button>
    <div class="modal-img" id="modalImg">
      <img id="modalImgEl" src="" alt="" />
    </div>
    <div class="modal-content">
      <h2 id="modalNama">–</h2>
      <div class="modal-harga" id="modalHarga">–</div>
      <p class="modal-desc" id="modalDesc">–</p>
      <div class="modal-tags">
        <span class="modal-tag">🌿 Bahan Segar</span>
        <span class="modal-tag">🍽️ Porsi Lengkap</span>
        <span class="modal-tag" id="modalBadgeTag">–</span>
      </div>
      <button id="btnTambahKeranjang"
              class="btn-tambah-cart"
              onclick="tambahKeKeranjang()">
        🛒 Tambah ke Keranjang
      </button>
    </div>
  </div>
</div>