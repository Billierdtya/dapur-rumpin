<?php
// ============================================================
//  kasir/_topbar.php — Topbar khusus role Kasir
//  Tampilkan nama user, badge role, dan tombol logout
// ============================================================
$user = currentUser();
?>
<header class="topbar">
  <button class="hamburger-dash" onclick="toggleSidebar()" title="Toggle Sidebar">
    ☰
  </button>
  <div class="topbar-right">
    <!-- Badge notifikasi pesanan pending baru -->
    <div class="topbar-notif" id="topbarNotif" style="position:relative;cursor:pointer;" title="Pesanan pending">
      <span style="font-size:1.3rem;">🔔</span>
      <span class="notif-badge" id="notifBadge" style="display:none;position:absolute;top:-4px;right:-6px;background:#e63946;color:#fff;font-size:0.65rem;font-weight:700;padding:1px 5px;border-radius:50px;min-width:16px;text-align:center;"></span>
    </div>

    <div class="topbar-user">
      <div class="topbar-avatar">
        <?= strtoupper(substr($user['nama'], 0, 1)) ?>
      </div>
      <div class="topbar-user-info">
        <span class="topbar-name"><?= htmlspecialchars($user['nama']) ?></span>
        <span class="topbar-role"><?= htmlspecialchars(getRoleLabel($user['role'])) ?></span>
      </div>
    </div>
    <a href="../logout.php" class="btn-sm btn-logout-top"
       onclick="return confirm('Yakin ingin logout?')">
      Logout
    </a>
  </div>
</header>
