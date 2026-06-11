<?php
// ============================================================
//  admin/staff_form.php — Tambah & Edit Staff
// ============================================================
require_once '../includes/auth_check.php';
authCheck('admin');
require_once '../config/db.php';

$user   = currentUser();
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = $id > 0;
$errors = [];
$staff  = ['nama' => '', 'username' => ''];

if ($isEdit) {
    $stmt = $pdo->prepare("SELECT id, nama, username FROM users WHERE id = ? AND role = 'staff'");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
        $_SESSION['flash'] = ['type'=>'error','msg'=>'Staff tidak ditemukan.'];
        header('Location: staff_list.php'); exit;
    }
    $staff = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $errors[] = 'Token tidak valid.';
    } else {
        $staff['nama']     = trim($_POST['nama'] ?? '');
        $staff['username'] = trim($_POST['username'] ?? '');
        $password          = $_POST['password'] ?? '';
        $passwordKonfirm   = $_POST['password_konfirm'] ?? '';

        if ($staff['nama'] === '')     $errors[] = 'Nama wajib diisi.';
        if ($staff['username'] === '') $errors[] = 'Username wajib diisi.';

        // Cek username duplikat (kecuali milik sendiri saat edit)
        $stmtCek = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmtCek->execute([$staff['username'], $id]);
        if ($stmtCek->fetch()) $errors[] = 'Username sudah digunakan oleh akun lain.';

        // Password: wajib saat tambah, opsional saat edit
        if (!$isEdit && $password === '') $errors[] = 'Password wajib diisi untuk akun baru.';
        if ($password !== '' && strlen($password) < 6) $errors[] = 'Password minimal 6 karakter.';
        if ($password !== '' && $password !== $passwordKonfirm) $errors[] = 'Konfirmasi password tidak cocok.';

        if (empty($errors)) {
            if ($isEdit) {
                if ($password !== '') {
                    $stmt = $pdo->prepare("UPDATE users SET nama=?, username=?, password=? WHERE id=?");
                    $stmt->execute([$staff['nama'], $staff['username'], password_hash($password, PASSWORD_BCRYPT), $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET nama=?, username=? WHERE id=?");
                    $stmt->execute([$staff['nama'], $staff['username'], $id]);
                }
                $_SESSION['flash'] = ['type'=>'success','msg'=>'Akun staff berhasil diperbarui.'];
            } else {
                $stmt = $pdo->prepare("INSERT INTO users (nama, username, password, role) VALUES (?, ?, ?, 'staff')");
                $stmt->execute([$staff['nama'], $staff['username'], password_hash($password, PASSWORD_BCRYPT)]);
                $_SESSION['flash'] = ['type'=>'success','msg'=>'Akun staff baru berhasil dibuat.'];
            }
            header('Location: staff_list.php'); exit;
        }
    }
}

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $isEdit ? 'Edit' : 'Tambah' ?> Staff – DapurRumpin</title>
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
        <h2 class="page-title"><?= $isEdit ? '✏️ Edit Staff' : '➕ Tambah Staff Baru' ?></h2>
        <p class="page-sub"><a href="staff_list.php" class="breadcrumb-link">Data Staff</a> › <?= $isEdit ? 'Edit' : 'Tambah' ?></p>
      </div>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error">
        <strong>⚠️ Terdapat kesalahan:</strong>
        <ul style="margin:8px 0 0 18px;">
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <div class="form-card" style="max-width:520px;">
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <div class="form-group-dash">
          <label class="form-label">Nama Lengkap <span class="req">*</span></label>
          <input type="text" name="nama" class="form-input"
                 value="<?= htmlspecialchars($staff['nama']) ?>"
                 placeholder="Contoh: Siti Aminah" required>
        </div>

        <div class="form-group-dash">
          <label class="form-label">Username <span class="req">*</span></label>
          <input type="text" name="username" class="form-input"
                 value="<?= htmlspecialchars($staff['username']) ?>"
                 placeholder="Contoh: staff01" required>
          <small class="form-hint">Huruf kecil, angka, dan underscore. Tidak boleh ada spasi.</small>
        </div>

        <div class="form-group-dash">
          <label class="form-label">
            Password <?= $isEdit ? '<span class="form-hint-inline">(kosongkan jika tidak ingin mengubah)</span>' : '<span class="req">*</span>' ?>
          </label>
          <input type="password" name="password" class="form-input"
                 placeholder="<?= $isEdit ? 'Isi hanya jika ingin mengubah password' : 'Minimal 6 karakter' ?>"
                 <?= !$isEdit ? 'required' : '' ?>>
        </div>

        <div class="form-group-dash">
          <label class="form-label">Konfirmasi Password</label>
          <input type="password" name="password_konfirm" class="form-input"
                 placeholder="Ulangi password">
        </div>

        <div class="form-actions">
          <a href="staff_list.php" class="btn-secondary-dash">✕ Batal</a>
          <button type="submit" class="btn-primary-dash">
            <?= $isEdit ? '💾 Simpan Perubahan' : '➕ Buat Akun' ?>
          </button>
        </div>
      </form>
    </div>

  </div>
</div>

<script src="../js/dashboard.js"></script>
</body>
</html>