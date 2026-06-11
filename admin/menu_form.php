<?php
// ============================================================
//  admin/menu_form.php — Tambah & Edit Menu
// ============================================================
require_once '../includes/auth_check.php';
authCheck('admin');
require_once '../config/db.php';

$user    = currentUser();
$id      = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit  = $id > 0;
$errors  = [];
$menu    = [
    'nama'      => '', 'kategori' => 'makanan',
    'harga'     => '', 'deskripsi' => '',
    'badge'     => '', 'gambar'   => '',
    'is_active' => 1,
];

// ── Kalau edit, ambil data lama ──────────────────────────
if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM menu WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
        $_SESSION['flash'] = ['type'=>'error','msg'=>'Menu tidak ditemukan.'];
        header('Location: menu_list.php'); exit;
    }
    $menu = $row;
}

// ── Proses POST ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $errors[] = 'Token tidak valid. Silakan ulangi.';
    } else {

        // Sanitize input
        $menu['nama']      = trim($_POST['nama'] ?? '');
        $menu['kategori']  = $_POST['kategori'] ?? 'makanan';
        $menu['harga']     = (int)str_replace(['.', ','], '', $_POST['harga'] ?? '0');
        $menu['deskripsi'] = trim($_POST['deskripsi'] ?? '');
        $menu['badge']     = trim($_POST['badge'] ?? '');
        $menu['is_active'] = isset($_POST['is_active']) ? 1 : 0;

        // Validasi wajib
        if ($menu['nama'] === '')  $errors[] = 'Nama menu wajib diisi.';
        if ($menu['harga'] <= 0)  $errors[] = 'Harga harus lebih dari 0.';
        if (!in_array($menu['kategori'], ['makanan','cemilan','minuman']))
            $errors[] = 'Kategori tidak valid.';

        // Upload gambar (opsional)
        $gambarBaru = $menu['gambar']; // default: tetap pakai gambar lama
        if (!empty($_FILES['gambar']['name'])) {
            $file    = $_FILES['gambar'];
            $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','webp','gif'];

            if (!in_array($ext, $allowed)) {
                $errors[] = 'Format gambar tidak didukung. Gunakan JPG, PNG, atau WEBP.';
            } elseif ($file['size'] > 2 * 1024 * 1024) {
                $errors[] = 'Ukuran gambar maksimal 2MB.';
            } else {
                $namaFile  = 'menu_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $uploadDir = '../img/menu/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $uploadPath = $uploadDir . $namaFile;

                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    // Hapus gambar lama kalau ada & bukan gambar dari folder img/ lama
                    if ($isEdit && $menu['gambar'] && str_starts_with($menu['gambar'], 'img/menu/')) {
                        @unlink('../' . $menu['gambar']);
                    }
                    $gambarBaru = 'img/menu/' . $namaFile;
                } else {
                    $errors[] = 'Gagal mengupload gambar. Cek permission folder img/menu/';
                }
            }
        }

        // Simpan ke DB kalau tidak ada error
        if (empty($errors)) {
            if ($isEdit) {
                $stmt = $pdo->prepare("
                    UPDATE menu SET
                        nama=?, kategori=?, harga=?, deskripsi=?,
                        badge=?, gambar=?, is_active=?
                    WHERE id=?
                ");
                $stmt->execute([
                    $menu['nama'], $menu['kategori'], $menu['harga'],
                    $menu['deskripsi'], $menu['badge'], $gambarBaru,
                    $menu['is_active'], $id
                ]);
                $_SESSION['flash'] = ['type'=>'success','msg'=>'Menu berhasil diperbarui.'];
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO menu (nama, kategori, harga, deskripsi, badge, gambar, is_active)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $menu['nama'], $menu['kategori'], $menu['harga'],
                    $menu['deskripsi'], $menu['badge'], $gambarBaru,
                    $menu['is_active']
                ]);
                $_SESSION['flash'] = ['type'=>'success','msg'=>'Menu baru berhasil ditambahkan.'];
            }
            header('Location: menu_list.php'); exit;
        }
    }
}

// Refresh CSRF setiap render form
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $isEdit ? 'Edit' : 'Tambah' ?> Menu – DapurRumpin</title>
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
        <h2 class="page-title"><?= $isEdit ? '✏️ Edit Menu' : '➕ Tambah Menu Baru' ?></h2>
        <p class="page-sub"><a href="menu_list.php" class="breadcrumb-link">Data Menu</a> › <?= $isEdit ? 'Edit' : 'Tambah' ?></p>
      </div>
    </div>

    <!-- Errors -->
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

    <div class="form-card">
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <div class="form-grid-2">

          <!-- Nama -->
          <div class="form-group-dash form-col-2">
            <label class="form-label">Nama Menu <span class="req">*</span></label>
            <input type="text" name="nama" class="form-input"
                   value="<?= htmlspecialchars($menu['nama']) ?>"
                   placeholder="Contoh: Nasi Goreng Sereh" required>
          </div>

          <!-- Kategori -->
          <div class="form-group-dash">
            <label class="form-label">Kategori <span class="req">*</span></label>
            <select name="kategori" class="form-input">
              <?php foreach (['makanan'=>'🥘 Makanan','cemilan'=>'🍟 Cemilan','minuman'=>'🥤 Minuman'] as $v => $l): ?>
                <option value="<?= $v ?>" <?= $menu['kategori'] === $v ? 'selected' : '' ?>><?= $l ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Harga -->
          <div class="form-group-dash">
            <label class="form-label">Harga (Rp) <span class="req">*</span></label>
            <input type="text" name="harga" id="inputHarga" class="form-input"
                   value="<?= $menu['harga'] ? number_format($menu['harga'], 0, ',', '.') : '' ?>"
                   placeholder="Contoh: 24.000" required>
            <small class="form-hint">Masukkan angka saja, titik pemisah ribuan otomatis.</small>
          </div>

          <!-- Badge -->
          <div class="form-group-dash">
            <label class="form-label">Badge Label</label>
            <input type="text" name="badge" class="form-input"
                   value="<?= htmlspecialchars($menu['badge'] ?? '') ?>"
                   placeholder="Contoh: Favorit, Premium, Spesial">
            <small class="form-hint">Label kecil di pojok gambar menu (opsional).</small>
          </div>

          <!-- Deskripsi -->
          <div class="form-group-dash form-col-2">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-input form-textarea"
                      placeholder="Tulis deskripsi singkat menu ini..."><?= htmlspecialchars($menu['deskripsi'] ?? '') ?></textarea>
          </div>

          <!-- Upload Gambar -->
          <div class="form-group-dash form-col-2">
            <label class="form-label">Gambar Menu</label>
            <div class="upload-area" id="uploadArea">
              <input type="file" name="gambar" id="inputGambar" accept="image/*"
                     onchange="previewImage(this)">
              <div class="upload-placeholder" id="uploadPlaceholder">
                <?php if ($menu['gambar']): ?>
                  <img src="../<?= htmlspecialchars($menu['gambar']) ?>" id="previewImg"
                       class="upload-preview" alt="Preview">
                  <p style="margin-top:8px;font-size:0.8rem;color:var(--abu)">Klik untuk ganti gambar</p>
                <?php else: ?>
                  <div id="uploadDefault">
                    <span style="font-size:2.5rem;">📷</span>
                    <p>Klik atau drag untuk upload gambar</p>
                    <small>JPG, PNG, WEBP – Maks 2MB</small>
                  </div>
                  <img src="" id="previewImg" class="upload-preview" style="display:none;" alt="Preview">
                <?php endif; ?>
              </div>
            </div>
          </div>

          <!-- Status Aktif -->
          <div class="form-group-dash form-col-2">
            <label class="toggle-label">
              <input type="checkbox" name="is_active" value="1"
                     <?= $menu['is_active'] ? 'checked' : '' ?>>
              <span class="toggle-switch"></span>
              <span class="toggle-text">Menu Aktif (ditampilkan di website publik)</span>
            </label>
          </div>

        </div><!-- /form-grid-2 -->

        <div class="form-actions">
          <a href="menu_list.php" class="btn-secondary-dash">✕ Batal</a>
          <button type="submit" class="btn-primary-dash">
            <?= $isEdit ? '💾 Simpan Perubahan' : '+ Tambah Menu' ?>
          </button>
        </div>

      </form>
    </div>

  </div>
</div>

<script src="../js/dashboard.js"></script>
<script>
// Preview gambar sebelum upload
function previewImage(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      var img = document.getElementById('previewImg');
      var def = document.getElementById('uploadDefault');
      img.src = e.target.result;
      img.style.display = 'block';
      if (def) def.style.display = 'none';
    };
    reader.readAsDataURL(input.files[0]);
  }
}

// Format harga dengan titik ribuan saat mengetik
document.getElementById('inputHarga').addEventListener('input', function() {
  var raw = this.value.replace(/\D/g, '');
  this.value = raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
});
</script>
</body>
</html>