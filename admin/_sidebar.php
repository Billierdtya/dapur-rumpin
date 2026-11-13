<?php
// ============================================================
//  admin/_sidebar.php — Sidebar Dashboard Owner (Admin)
//  Dipakai oleh halaman-halaman di folder admin/
//  Updated untuk sistem 3 role baru (owner/kasir/dapur)
// ============================================================
$currentFile   = basename($_SERVER['PHP_SELF']);
$currentFolder = basename(dirname($_SERVER['PHP_SELF']));
$base          = '../';
$role          = $_SESSION['user_role'] ?? 'owner';

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
      <span class="sidebar-role-badge owner"><?= htmlspecialchars(getRoleLabel($role)) ?></span>
    </div>
  </div>

  <nav class="sidebar-nav">
    <span class="nav-section-label">Utama</span>

    <?php if (hasRole('owner')): ?>
    <a href="<?= $base ?>admin/index.php"
       class="nav-item <?= sidebarActive('index.php','admin') ?>">
      <span class="nav-icon">📊</span>
      <span class="nav-label">Dashboard</span>
    </a>
    <?php endif; ?>

    <?php if (hasRole('owner')): ?>
    <a href="<?= $base ?>kasir/pesanan.php"
       class="nav-item <?= sidebarActive('pesanan.php','kasir') ?>">
      <span class="nav-icon">📋</span>
      <span class="nav-label">Pesanan</span>
      <?php
        // Badge pesanan pending
        require_once dirname(__DIR__) . '/config/db.php';
        $pending = $pdo->query("SELECT COUNT(*) FROM pesanan WHERE status='pending'")->fetchColumn();
        if ($pending > 0): ?>
        <span class="nav-badge"><?= $pending ?></span>
      <?php endif; ?>
    </a>
    <?php endif; ?>

    <span class="nav-section-label" style="margin-top:16px;">Manajemen</span>

    <?php if (hasRole('admin')): ?>
    <a href="<?= $base ?>admin/menu_list.php"
       class="nav-item <?= sidebarActive('menu_list.php','admin') ?> <?= sidebarActive('menu_form.php','admin') ?>">
      <span class="nav-icon">🍽️</span>
      <span class="nav-label">Data Menu</span>
    </a>
    <?php endif; ?>

    <?php if (hasRole(['owner', 'admin'])): ?>
    <a href="<?= $base ?>admin/staff_list.php"
       class="nav-item <?= sidebarActive('staff_list.php','admin') ?> <?= sidebarActive('staff_form.php','admin') ?>">
      <span class="nav-icon">👥</span>
      <span class="nav-label">Data Staff</span>
    </a>
    <?php endif; ?>

    <?php if (hasRole('admin')): ?>
    <a href="<?= $base ?>admin/meja_list.php"
       class="nav-item <?= sidebarActive('meja_list.php','admin') ?> <?= sidebarActive('meja_form.php','admin') ?> <?= sidebarActive('meja_qr.php','admin') ?>">
      <span class="nav-icon">🪑</span>
      <span class="nav-label">Kelola Meja</span>
    </a>
    <?php endif; ?>

    <?php if (hasRole('owner')): ?>
    <a href="<?= $base ?>admin/laporan.php"
       class="nav-item <?= sidebarActive('laporan.php','admin') ?>">
      <span class="nav-icon">📈</span>
      <span class="nav-label">Laporan</span>
    </a>
    <?php endif; ?>

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