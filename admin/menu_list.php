<?php
// ============================================================
//  admin/menu_list.php
// ============================================================
require_once '../includes/auth_check.php';
authCheck('admin');
require_once '../config/db.php';

$user = currentUser();

// ── Flash message dari redirect ──────────────────────────
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// ── Filter & Search ──────────────────────────────────────
$kategori = $_GET['kat'] ?? 'semua';
$search   = trim($_GET['q'] ?? '');

$sql    = "SELECT * FROM menu WHERE 1=1";
$params = [];

if ($kategori !== 'semua') {
    $sql .= " AND kategori = ?";
    $params[] = $kategori;
}
if ($search !== '') {
    $sql .= " AND nama LIKE ?";
    $params[] = "%$search%";
}
$sql .= " ORDER BY kategori, nama ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$menus = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Menu – DapurRumpin</title>
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
        <h2 class="page-title">Data Menu</h2>
        <p class="page-sub">Total <?= count($menus) ?> item ditemukan</p>
      </div>
      <a href="menu_form.php" class="btn-primary-dash">+ Tambah Menu</a>
    </div>

    <!-- Flash Message -->
    <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] ?> alert-dismissible">
        <?= htmlspecialchars($flash['msg']) ?>
        <button onclick="this.parentElement.remove()">✕</button>
      </div>
    <?php endif; ?>

    <!-- Filter & Search -->
    <div class="card">
      <form method="GET" class="filter-bar">
        <div class="filter-tabs">
          <?php foreach (['semua'=>'Semua','makanan'=>'Makanan','cemilan'=>'Cemilan','minuman'=>'Minuman'] as $val => $lbl): ?>
            <button type="submit" name="kat" value="<?= $val ?>"
              class="filter-tab <?= $kategori === $val ? 'active' : '' ?>">
              <?= $lbl ?>
            </button>
          <?php endforeach; ?>
          <input type="hidden" name="q" value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="search-wrap">
          <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
                 placeholder="🔍 Cari nama menu..."
                 class="search-input"
                 onchange="this.form.submit()">
        </div>
      </form>
    </div>

    <!-- Tabel Menu -->
    <div class="card">
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th width="50">#</th>
              <th width="70">Gambar</th>
              <th>Nama Menu</th>
              <th>Kategori</th>
              <th>Harga</th>
              <th>Badge</th>
              <th>Status</th>
              <th width="150">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($menus)): ?>
              <tr><td colspan="8" class="td-empty">Tidak ada menu ditemukan.</td></tr>
            <?php else: ?>
              <?php foreach ($menus as $i => $m): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td>
                  <?php if ($m['gambar']): ?>
                    <img src="../<?= htmlspecialchars($m['gambar']) ?>"
                         alt="<?= htmlspecialchars($m['nama']) ?>"
                         class="table-thumb"
                         onerror="this.src='../img/placeholder.png'">
                  <?php else: ?>
                    <div class="table-thumb-placeholder">🍽️</div>
                  <?php endif; ?>
                </td>
                <td>
                  <strong><?= htmlspecialchars($m['nama']) ?></strong>
                  <?php if ($m['deskripsi']): ?>
                    <p class="td-sub"><?= htmlspecialchars(substr($m['deskripsi'], 0, 60)) ?>...</p>
                  <?php endif; ?>
                </td>
                <td><span class="badge badge-kat-<?= $m['kategori'] ?>"><?= ucfirst($m['kategori']) ?></span></td>
                <td>Rp <?= number_format($m['harga'], 0, ',', '.') ?></td>
                <td><?= htmlspecialchars($m['badge'] ?? '-') ?></td>
                <td>
                  <span class="badge <?= $m['is_active'] ? 'badge-selesai' : 'badge-dibatalkan' ?>">
                    <?= $m['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                  </span>
                </td>
                <td>
                  <div class="action-btns">
                    <a href="menu_form.php?id=<?= $m['id'] ?>" class="btn-action btn-edit" title="Edit">✏️</a>
                    <button onclick="hapusMenu(<?= $m['id'] ?>, '<?= htmlspecialchars(addslashes($m['nama'])) ?>')"
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

<!-- Form hapus tersembunyi -->
<form id="formHapus" method="POST" action="menu_delete.php" style="display:none;">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
  <input type="hidden" name="id" id="hapusId">
</form>

<script src="../js/dashboard.js"></script>
<script>
function hapusMenu(id, nama) {
  if (confirm('Hapus menu "' + nama + '"?\n\nData tidak bisa dikembalikan.')) {
    document.getElementById('hapusId').value = id;
    document.getElementById('formHapus').submit();
  }
}
</script>
</body>
</html>