<?php
// ============================================================
//  admin/_sidebar.php  (include dari admin/ MAUPUN staff/)
//  Deteksi folder otomatis supaya link aktif & path benar
// ============================================================
$currentFile   = basename($_SERVER['PHP_SELF']);
$currentFolder = basename(dirname($_SERVER['PHP_SELF']));
$base          = ($currentFolder === 'admin') ? '../' : '../';
$role          = $_SESSION['user_role'] ?? 'staff';

// Helper: tambah class 'active' kalau cocok
function sidebarActive(string $file, string $folder = ''): string {
    global $currentFile, $currentFolder;
    $fileMatch   = ($currentFile   === $file);
    $folderMatch = ($folder === '' || $currentFolder === $folder);
    return ($fileMatch && $folderMatch) ? 'active' : '';
}
?>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <img src="<?= $base ?>img/logo.jpg" alt="Logo"
         onerror="this.style.display='none'"
         class="sidebar-logo">
    <div class="sidebar-brand">
      <span class="sidebar-brand-text">Dapur<span>Rumpin</span></span>
      <span class="sidebar-role-badge <?= $role ?>"><?= ucfirst($role) ?></span>
    </div>
  </div>

  <nav class="sidebar-nav">
    <span class="nav-section-label">Utama</span>

    <?php if ($role === 'admin'): ?>
    <a href="<?= $base ?>admin/index.php"
       class="nav-item <?= sidebarActive('index.php','admin') ?>">
      <span class="nav-icon"></span>
      <span class="nav-label">Dashboard</span>
    </a>
    <?php endif; ?>

    <a href="<?= $base ?>staff/pesanan.php"
       class="nav-item <?= sidebarActive('pesanan.php','staff') ?>">
      <span class="nav-icon"></span>
      <span class="nav-label">Pesanan</span>
      <?php
        // Badge pesanan pending
        require_once dirname(__DIR__) . '/config/db.php';
        $pending = $pdo->query("SELECT COUNT(*) FROM pesanan WHERE status='pending'")->fetchColumn();
        if ($pending > 0): ?>
        <span class="nav-badge"><?= $pending ?></span>
      <?php endif; ?>
    </a>

    <?php if ($role === 'admin'): ?>
    <span class="nav-section-label" style="margin-top:16px;">Manajemen</span>

    <a href="<?= $base ?>admin/menu_list.php"
       class="nav-item <?= sidebarActive('menu_list.php','admin') ?> <?= sidebarActive('menu_form.php','admin') ?>">
      <span class="nav-icon"></span>
      <span class="nav-label">Data Menu</span>
    </a>

    <a href="<?= $base ?>admin/staff_list.php"
       class="nav-item <?= sidebarActive('staff_list.php','admin') ?> <?= sidebarActive('staff_form.php','admin') ?>">
      <span class="nav-icon"></span>
      <span class="nav-label">Data Staff</span>
    </a>
    <?php endif; ?>

    <span class="nav-section-label" style="margin-top:16px;">Lainnya</span>

    <a href="<?= $base ?>index.php" class="nav-item" target="_blank">
      <span class="nav-icon"></span>
      <span class="nav-label">Lihat Website</span>
    </a>

    <a href="<?= $base ?>logout.php" class="nav-item nav-item-logout"
       onclick="return confirm('Yakin ingin logout?')">
      <span class="nav-icon"></span>
      <span class="nav-label">Logout</span>
    </a>
  </nav>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>