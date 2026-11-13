<?php
// ============================================================
//  dapur/_topbar.php — Topbar khusus mode Dapur
//  Tampilkan: nama restoran, jam digital real-time, nama staff, logout
// ============================================================
$user = currentUser();
?>
<header class="dapur-topbar">
  <div class="dapur-topbar-left">
    <img src="../img/logo.jpg" alt="Logo" class="dapur-topbar-logo" onerror="this.style.display='none'">
    <div class="dapur-topbar-brand">
      <span class="dapur-topbar-title">DapurRumpin</span>
      <span class="dapur-topbar-subtitle">Mode Dapur</span>
    </div>
  </div>

  <div class="dapur-topbar-center">
    <span class="dapur-clock" id="dapurClock">00:00:00</span>
    <span class="dapur-date" id="dapurDate"></span>
  </div>

  <div class="dapur-topbar-right">
    <div class="dapur-user-info">
      <span class="dapur-user-icon">👨‍🍳</span>
      <span class="dapur-user-name"><?= htmlspecialchars($user['nama']) ?></span>
    </div>
    <a href="../logout.php" class="dapur-btn-logout"
       onclick="return confirm('Yakin ingin logout?')">
      🚪 Logout
    </a>
  </div>
</header>

<script>
// ── Jam digital real-time (update tiap detik) ─────────────
function updateDapurClock() {
  var now = new Date();
  var h = String(now.getHours()).padStart(2, '0');
  var m = String(now.getMinutes()).padStart(2, '0');
  var s = String(now.getSeconds()).padStart(2, '0');
  document.getElementById('dapurClock').textContent = h + ':' + m + ':' + s;

  var days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
  var months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  var dateStr = days[now.getDay()] + ', ' + now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();
  document.getElementById('dapurDate').textContent = dateStr;
}
updateDapurClock();
setInterval(updateDapurClock, 1000);
</script>
