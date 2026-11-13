<?php
// ============================================================
//  kasir/_sidebar.php — Sidebar khusus role Kasir
//  Menu: Pesanan Masuk, Logout
// ============================================================
$currentFile   = basename($_SERVER['PHP_SELF']);
$currentFolder = basename(dirname($_SERVER['PHP_SELF']));
$base          = '../';
$role          = $_SESSION['user_role'] ?? 'kasir';

// Helper: tambah class 'active' kalau cocok
if (!function_exists('sidebarActive')) {
    function sidebarActive(string $file, string $folder = ''): string {
        global $currentFile, $currentFolder;
        $fileMatch   = ($currentFile   === $file);
        $folderMatch = ($folder === '' || $currentFolder === $folder);
        return ($fileMatch && $folderMatch) ? 'active' : '';
    }
}
?>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <img src="<?= $base ?>img/logo.jpg" alt="Logo"
         onerror="this.style.display='none'"
         class="sidebar-logo">
    <div class="sidebar-brand">
      <span class="sidebar-brand-text">Dapur<span>Rumpin</span></span>
      <span class="sidebar-role-badge kasir"><?= htmlspecialchars(getRoleLabel($role)) ?></span>
    </div>
  </div>

  <nav class="sidebar-nav">
    <span class="nav-section-label">Utama</span>

    <a href="<?= $base ?>kasir/pesanan.php"
       class="nav-item <?= sidebarActive('pesanan.php','kasir') ?>">
      <span class="nav-icon">📋</span>
      <span class="nav-label">Pesanan Masuk</span>
      <?php
        // Badge pesanan pending
        require_once dirname(__DIR__) . '/config/db.php';
        $pendingCount = $pdo->query("SELECT COUNT(*) FROM pesanan WHERE status='pending'")->fetchColumn();
        if ($pendingCount > 0): ?>
        <span class="nav-badge" id="sidebarBadgePending"><?= $pendingCount ?></span>
      <?php endif; ?>
    </a>

    <span class="nav-section-label" style="margin-top:16px;">Lainnya</span>

    <a href="<?= $base ?>index.php" class="nav-item" target="_blank">
      <span class="nav-icon">🌐</span>
      <span class="nav-label">Lihat Website</span>
    </a>

    <a href="<?= $base ?>logout.php" class="nav-item nav-item-logout"
       onclick="return confirm('Yakin ingin logout?')">
      <span class="nav-icon">🚪</span>
      <span class="nav-label">Logout</span>
    </a>
  </nav>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
