<?php
// ============================================================
//  admin/staff_list.php
// ============================================================
require_once '../includes/auth_check.php';
authCheck('admin');
require_once '../config/db.php';

$user  = currentUser();
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$staffs = $pdo->query(
    "SELECT * FROM users WHERE role = 'staff' ORDER BY created_at DESC"
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Staff – DapurRumpin</title>
  <link rel="icon" type="image/jpg" href="../img/logo.jpg">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap">
  <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>

<?php include '_sidebar.php'; ?>

<div class="main-content">
  <?php include '_topbar.php'; ?>

  <div class="page-body">
    <div class="page-title-wrap">
      <div>
        <h2 class="page-title">Data Staff</h2>
        <p class="page-sub"><?= count($staffs) ?> akun staff terdaftar</p>
      </div>
      <a href="staff_form.php" class="btn-primary-dash">+ Tambah Staff</a>
    </div>

    <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] ?> alert-dismissible">
        <?= htmlspecialchars($flash['msg']) ?>
        <button onclick="this.parentElement.remove()">✕</button>
      </div>
    <?php endif; ?>

    <div class="card">
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Nama</th>
              <th>Username</th>
              <th>Role</th>
              <th>Dibuat</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($staffs)): ?>
              <tr><td colspan="6" class="td-empty">Belum ada akun staff.</td></tr>
            <?php else: ?>
              <?php foreach ($staffs as $i => $s): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td>
                  <div class="user-cell">
                    <div class="user-avatar-sm"><?= strtoupper(substr($s['nama'], 0, 1)) ?></div>
                    <strong><?= htmlspecialchars($s['nama']) ?></strong>
                  </div>
                </td>
                <td><code><?= htmlspecialchars($s['username']) ?></code></td>
                <td><span class="badge badge-diproses">Staff</span></td>
                <td><?= date('d M Y', strtotime($s['created_at'])) ?></td>
                <td>
                  <div class="action-btns">
                    <a href="staff_form.php?id=<?= $s['id'] ?>" class="btn-action btn-edit" title="Edit">✏️</a>
                    <button onclick="hapusStaff(<?= $s['id'] ?>, '<?= htmlspecialchars(addslashes($s['nama'])) ?>')"
                            class="btn-action btn-delete" title="Hapus">🗑️</button>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<form id="formHapusStaff" method="POST" action="staff_delete.php" style="display:none;">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
  <input type="hidden" name="id" id="hapusStaffId">
</form>

<script src="../js/dashboard.js"></script>
<script>
function hapusStaff(id, nama) {
  if (confirm('Hapus akun staff "' + nama + '"?\n\nAksi ini tidak bisa dibatalkan.')) {
    document.getElementById('hapusStaffId').value = id;
    document.getElementById('formHapusStaff').submit();
  }
}
</script>
</body>
</html>