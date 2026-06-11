<?php
// ============================================================
//  admin/_topbar.php
// ============================================================
$user = currentUser();
?>
<header class="topbar">
  <button class="hamburger-dash" onclick="toggleSidebar()" title="Toggle Sidebar">
    ☰
  </button>
  <div class="topbar-right">
    <div class="topbar-user">
      <div class="topbar-avatar">
        <?= strtoupper(substr($user['nama'], 0, 1)) ?>
      </div>
      <div class="topbar-user-info">
        <span class="topbar-name"><?= htmlspecialchars($user['nama']) ?></span>
        <span class="topbar-role"><?= ucfirst($user['role']) ?></span>
      </div>
    </div>
    <a href="../logout.php" class="btn-sm btn-logout-top"
       onclick="return confirm('Yakin ingin logout?')">
      Logout
    </a>
  </div>
</header>