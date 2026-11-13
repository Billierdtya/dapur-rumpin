/* ============================================
   WarungRumpin – main.js  (UPDATED: simpan ke DB)
   ============================================ */

document.body.style.overflow = '';

/* ----- MOBILE MENU TOGGLE ----- */
function toggleMenu() {
  var menu = document.getElementById('mobileMenu');
  if (menu) menu.classList.toggle('open');
}

document.addEventListener('DOMContentLoaded', function () {
  var mobileLinks = document.querySelectorAll('.mobile-menu a');
  mobileLinks.forEach(function (link) {
    link.addEventListener('click', function () {
      var menu = document.getElementById('mobileMenu');
      if (menu) menu.classList.remove('open');
    });
  });
});

/* ----- MODAL MENU ----- */
function bukaModal(nama, harga, deskripsi, imgUrl, badge) {
  var overlay = document.getElementById('modalOverlay');
  if (!overlay) return;

  var elNama  = document.getElementById('modalNama');
  var elHarga = document.getElementById('modalHarga');
  var elDesc  = document.getElementById('modalDesc');
  var elBadge = document.getElementById('modalBadgeTag');
  var elImg   = document.getElementById('modalImgEl');

  if (elNama)  elNama.textContent  = nama;
  if (elHarga) elHarga.textContent = harga;
  if (elDesc)  elDesc.textContent  = deskripsi;
  if (elBadge) elBadge.textContent = badge;
  if (elImg && imgUrl) { elImg.src = imgUrl; elImg.alt = nama; }

  overlay.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function tutupModal(event) {
  var overlay = document.getElementById('modalOverlay');
  if (!overlay) return;
  if (event) {
    var clickedOverlay = (event.target === overlay);
    var clickedClose   = event.target.classList.contains('modal-close');
    if (!clickedOverlay && !clickedClose) return;
  }
  overlay.classList.remove('open');
  document.body.style.overflow = '';
}

document.addEventListener('keydown', function (e) {
  if (e.key !== 'Escape') return;
  var overlay = document.getElementById('modalOverlay');
  if (overlay && overlay.classList.contains('open')) {
    overlay.classList.remove('open');
    document.body.style.overflow = '';
  }
});

/* ----- FILTER MENU ----- */
function filterMenu(kategori, btn) {
  var tabs = document.querySelectorAll('.tab-btn');
  tabs.forEach(function (t) { t.classList.remove('active'); });
  if (btn) btn.classList.add('active');

  var cards = document.querySelectorAll('.menu-card');
  cards.forEach(function (card) {
    var kat = card.getAttribute('data-kat');
    if (kategori === 'semua' || kat === kategori) {
      card.style.display   = '';
      card.style.animation = 'fadeUp 0.4s ease both';
    } else {
      card.style.display = 'none';
    }
  });
}

/* ----- SCROLL TO TOP ----- */
document.addEventListener('DOMContentLoaded', function () {
  var scrollBtn = document.getElementById('scrollTop');
  if (!scrollBtn) return;
  window.addEventListener('scroll', function () {
    scrollBtn.classList.toggle('visible', window.scrollY > 400);
  });
  scrollBtn.addEventListener('click', function () {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
});

/* ----- NAVBAR SHADOW ----- */
window.addEventListener('scroll', function () {
  var navbar = document.querySelector('.navbar');
  if (!navbar) return;
  navbar.style.boxShadow = window.scrollY > 50
    ? '0 4px 30px rgba(0,0,0,0.28)'
    : '0 2px 20px rgba(0,0,0,0.15)';
});

/* ----- FADE IN CARDS ----- */
document.addEventListener('DOMContentLoaded', function () {
  if (!('IntersectionObserver' in window)) return;
  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.style.opacity   = '1';
        entry.target.style.transform = 'translateY(0)';
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.08 });

  document.querySelectorAll('.menu-card, .feature-card, .testi-card, .nilai-card')
    .forEach(function (el) {
      el.style.opacity    = '0';
      el.style.transform  = 'translateY(24px)';
      el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
      observer.observe(el);
    });
});

/* ============================================================
   SHOPPING CART
   ============================================================ */

function parseHarga(str) {
  if (typeof str === 'number') return str;
  return parseInt(str.replace(/[^\d]/g, ''), 10) || 0;
}

function formatHarga(num) {
  return 'Rp ' + num.toLocaleString('id-ID');
}

function getCart() {
  try { return JSON.parse(localStorage.getItem('drCart')) || []; }
  catch(e) { return []; }
}

function saveCart(cart) {
  localStorage.setItem('drCart', JSON.stringify(cart));
}

function updateCartBadge() {
  var cart  = getCart();
  var total = cart.reduce(function(s, i) { return s + i.qty; }, 0);
  document.querySelectorAll('.cart-badge').forEach(function(b) {
    b.textContent   = total;
    b.style.display = total > 0 ? 'flex' : 'none';
  });
}

function tambahKeKeranjang() {
  var nama  = document.getElementById('modalNama')  ? document.getElementById('modalNama').textContent  : '';
  var harga = document.getElementById('modalHarga') ? document.getElementById('modalHarga').textContent : '';
  if (!nama || !harga) return;

  var hargaNum = parseHarga(harga);
  var cart = getCart();
  var idx  = cart.findIndex(function(i) { return i.nama === nama; });

  if (idx > -1) { cart[idx].qty += 1; }
  else { cart.push({ nama: nama, harga: hargaNum, qty: 1 }); }

  saveCart(cart);
  updateCartBadge();

  var btn = document.getElementById('btnTambahKeranjang');
  if (btn) {
    btn.textContent = '✅ Ditambahkan!';
    btn.disabled = true;
    setTimeout(function() {
      btn.textContent = '🛒 Tambah ke Keranjang';
      btn.disabled = false;
    }, 1200);
  }
}

function renderKeranjang() {
  var cart    = getCart();
  var list    = document.getElementById('cartItemList');
  var totalEl = document.getElementById('cartTotal');
  if (!list) return;

  if (cart.length === 0) {
    list.innerHTML = '<p style="text-align:center;color:var(--abu);padding:30px 0;font-size:0.9rem;">Keranjang masih kosong 🛒</p>';
    if (totalEl) totalEl.textContent = 'Rp 0';
    return;
  }

  list.innerHTML = '';
  var grandTotal = 0;

  cart.forEach(function(item, idx) {
    grandTotal += item.harga * item.qty;
    var row = document.createElement('div');
    row.className = 'cart-item-row';
    row.innerHTML =
      '<div class="cart-item-info">' +
        '<span class="cart-item-nama">' + item.nama + '</span>' +
        '<span class="cart-item-harga">' + formatHarga(item.harga) + ' x ' + item.qty + '</span>' +
      '</div>' +
      '<div class="cart-item-ctrl">' +
        '<button onclick="ubahQty(' + idx + ',-1)" class="qty-btn">−</button>' +
        '<span class="qty-num">' + item.qty + '</span>' +
        '<button onclick="ubahQty(' + idx + ',1)"  class="qty-btn">+</button>' +
        '<button onclick="hapusItem(' + idx + ')"  class="qty-btn del-btn">🗑</button>' +
      '</div>';
    list.appendChild(row);
  });

  if (totalEl) totalEl.textContent = formatHarga(grandTotal);
}

function ubahQty(idx, delta) {
  var cart = getCart();
  cart[idx].qty += delta;
  if (cart[idx].qty <= 0) cart.splice(idx, 1);
  saveCart(cart);
  updateCartBadge();
  renderKeranjang();
}

function hapusItem(idx) {
  var cart = getCart();
  cart.splice(idx, 1);
  saveCart(cart);
  updateCartBadge();
  renderKeranjang();
}

function bukaKeranjang() {
  var panel = document.getElementById('cartPanel');
  if (!panel) return;
  renderKeranjang();
  panel.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function tutupKeranjang() {
  var panel = document.getElementById('cartPanel');
  if (!panel) return;
  panel.classList.remove('open');
  document.body.style.overflow = '';
}

function bukaCheckout() {
  var cart = getCart();
  if (cart.length === 0) {
    alert('Keranjang masih kosong! Tambahkan menu terlebih dahulu.');
    return;
  }
  tutupKeranjang();

  var sum     = document.getElementById('checkoutSummary');
  var totalEl = document.getElementById('checkoutTotal');
  if (!sum) return;

  var grandTotal = 0;
  sum.innerHTML  = '';
  cart.forEach(function(item) {
    grandTotal += item.harga * item.qty;
    var row = document.createElement('div');
    row.className = 'checkout-row';
    row.innerHTML =
      '<span>' + item.nama + ' x' + item.qty + '</span>' +
      '<span>' + formatHarga(item.harga * item.qty) + '</span>';
    sum.appendChild(row);
  });

  if (totalEl) totalEl.textContent = formatHarga(grandTotal);

  var modal = document.getElementById('checkoutModal');
  if (modal) {
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
    document.getElementById('checkoutStep1').style.display = '';
    document.getElementById('checkoutStep2').style.display = 'none';
    var fn = document.getElementById('inputNamaPemesan');
    var fm = document.getElementById('inputNomorMeja');
    if (fn) fn.value = '';
    if (fm && !fm.hasAttribute('readonly')) {
      fm.value = '';
    }
  }
}

function tutupCheckout() {
  var modal = document.getElementById('checkoutModal');
  if (modal) modal.classList.remove('open');
  document.body.style.overflow = '';
}

function prosesBayar() {
  var nama = document.getElementById('inputNamaPemesan')
    ? document.getElementById('inputNamaPemesan').value.trim() : '';
  var meja = document.getElementById('inputNomorMeja')
    ? document.getElementById('inputNomorMeja').value.trim()   : '';

  if (!nama) { alert('Mohon isi nama pemesan terlebih dahulu.'); return; }
  if (!meja) { alert('Mohon isi nomor meja terlebih dahulu.');  return; }

  var btnBayar = event.currentTarget || document.querySelector('.btn-bayar');
  if (btnBayar) {
    btnBayar.disabled = true;
    btnBayar.innerHTML = '⏳ Memproses...';
  }

  var cart       = getCart();
  var grandTotal = cart.reduce(function(s, i) { return s + i.harga * i.qty; }, 0);

  // ── Simpan pesanan ke DB via AJAX ──────────────────────
  var payload = {
    nama_pemesan: nama,
    nomor_meja:   meja,
    items: cart.map(function(i) {
      return { nama: i.nama, harga: i.harga, qty: i.qty };
    })
  };

  var base = document.querySelector('meta[name="base-url"]');
  var baseHref = base ? base.getAttribute('content') : '';
  var url  = baseHref + 'proses_checkout.php';

  fetch(url, {
    method:  'POST',
    headers: { 'Content-Type': 'application/json' },
    body:    JSON.stringify(payload)
  })
  .then(function(res) { return res.json(); })
  .then(function(data) {
    if (data.success) {
      window._kodePesanan = data.kode_pesanan;

      // Reset keranjang
      saveCart([]);
      updateCartBadge();
      
      // Langsung redirect ke cek-pesanan
      window.location.href = baseHref + 'cek-pesanan.php';
    } else {
      alert(data.message);
      if (btnBayar) {
        btnBayar.disabled = false;
        btnBayar.innerHTML = '💳 Lanjut ke Pembayaran QRIS';
      }
    }
  })
  .catch(function(err) {
    console.warn('Network error saat simpan pesanan:', err);
  });
}

/* ---- Konfirmasi via WhatsApp (dipanggil setelah QRIS) ---- */
function konfirmasiWA() {
  var nama  = document.getElementById('inputNamaPemesan')
    ? document.getElementById('inputNamaPemesan').value.trim() : '-';
  var meja  = document.getElementById('inputNomorMeja')
    ? document.getElementById('inputNomorMeja').value.trim()   : '-';
  var cart  = getCart();
  var grandTotal = cart.reduce(function(s, i) { return s + i.harga * i.qty; }, 0);

  var pesan  = '*PESANAN DAPUR RUMPIN*\n\n';
  pesan += 'Nama  : ' + nama + '\n';
  pesan += 'Meja  : ' + meja + '\n';
  if (window._kodePesanan) {
    pesan += 'Kode  : ' + window._kodePesanan + '\n';
  }
  pesan += '\n*Detail Pesanan:*\n';
  cart.forEach(function(item) {
    pesan += '• ' + item.nama + ' x' + item.qty + ' = ' + formatHarga(item.harga * item.qty) + '\n';
  });
  pesan += '\*Total: ' + formatHarga(grandTotal) + '*\n\n';
  pesan += 'Saya sudah melakukan pembayaran via QRIS. Mohon dikonfirmasi. Terima kasih!';

  var noWA = '627775551647';
  var url  = 'https://wa.me/' + noWA + '?text=' + encodeURIComponent(pesan);
  window.open(url, '_blank');

  // Reset keranjang
  saveCart([]);
  updateCartBadge();
  window._kodePesanan = null;
  tutupCheckout();
}

/* ---- Init ---- */
document.addEventListener('DOMContentLoaded', function() {
  updateCartBadge();
});