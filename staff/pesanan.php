<?php
// ============================================================
//  staff/pesanan.php — Daftar & Kelola Pesanan
//  Bisa diakses oleh role 'staff' MAUPUN 'admin'
// ============================================================
require_once '../includes/auth_check.php';
authCheck(); // tanpa parameter = admin + staff boleh
require_once '../config/db.php';

$user  = currentUser();
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// ── Update status via POST ────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    if (!empty($_POST['csrf_token']) && $_POST['csrf_token'] === ($_SESSION['csrf_token'] ?? '')) {
        $pid    = (int)$_POST['pesanan_id'];
        $status = $_POST['status'];
        $allowed = ['pending','diproses','selesai','dibatalkan'];
        if ($pid > 0 && in_array($status, $allowed)) {
            $stmt = $pdo->prepare("UPDATE pesanan SET status = ? WHERE id = ?");
            $stmt->execute([$status, $pid]);
            $_SESSION['flash'] = ['type'=>'success','msg'=>"Status pesanan berhasil diubah ke \"$status\"."];
        }
    }
    header('Location: pesanan.php'); exit;
}

// ── Filter ────────────────────────────────────────────────
$filterStatus = $_GET['status'] ?? 'semua';
$filterDate   = $_GET['tgl']    ?? '';
$search       = trim($_GET['q'] ?? '');

$sql    = "SELECT * FROM pesanan WHERE 1=1";
$params = [];

if ($filterStatus !== 'semua') { $sql .= " AND status = ?";      $params[] = $filterStatus; }
if ($filterDate   !== '')       { $sql .= " AND DATE(created_at) = ?"; $params[] = $filterDate; }
if ($search       !== '')       {
    $sql .= " AND (nama_pemesan LIKE ? OR kode_pesanan LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$sql .= " ORDER BY created_at DESC";

$stmt     = $pdo->prepare($sql);
$stmt->execute($params);
$pesanans = $stmt->fetchAll();

// ── Helper badge ─────────────────────────────────────────
function statusBadge(string $s): string {
    return match($s) {
        'pending'    => 'badge-pending',
        'diproses'   => 'badge-diproses',
        'selesai'    => 'badge-selesai',
        'dibatalkan' => 'badge-dibatalkan',
        default      => ''
    };
}

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pesanan – DapurRumpin</title>
  <link rel="icon" type="image/jpg" href="../img/logo.jpg">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap">
  <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>

<?php include '../admin/_sidebar.php'; ?>

<div class="main-content">
  <?php include '../admin/_topbar.php'; ?>

  <div class="page-body">
    <div class="page-title-wrap">
      <div>
        <h2 class="page-title">Daftar Pesanan</h2>
        <p class="page-sub"><?= count($pesanans) ?> pesanan ditemukan</p>
      </div>
    </div>

    <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] ?> alert-dismissible">
        <?= htmlspecialchars($flash['msg']) ?>
        <button onclick="this.parentElement.remove()">✕</button>
      </div>
    <?php endif; ?>

    <!-- Filter Bar -->
    <div class="card">
      <form method="GET" class="filter-bar filter-bar-col">
        <div class="filter-tabs">
          <?php foreach ([
            'semua'     => 'Semua',
            'pending'   => 'Pending',
            'diproses'  => 'Diproses',
            'selesai'   => 'Selesai',
            'dibatalkan'=> 'Dibatalkan',
          ] as $val => $lbl): ?>
            <button type="submit" name="status" value="<?= $val ?>"
              class="filter-tab <?= $filterStatus === $val ? 'active' : '' ?>">
              <?= $lbl ?>
            </button>
          <?php endforeach; ?>
          <input type="hidden" name="q"   value="<?= htmlspecialchars($search) ?>">
          <input type="hidden" name="tgl" value="<?= htmlspecialchars($filterDate) ?>">
        </div>
        <div class="filter-row-right">
          <input type="date" name="tgl" value="<?= htmlspecialchars($filterDate) ?>"
                 class="form-input form-input-sm" onchange="this.form.submit()">
          <input type="text"  name="q"   value="<?= htmlspecialchars($search) ?>"
                 class="search-input" placeholder="🔍 Cari nama / kode..."
                 onchange="this.form.submit()">
        </div>
      </form>
    </div>

    <!-- Tabel Pesanan -->
    <div class="card">
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Nama Pemesan</th>
              <th>Meja</th>
              <th>Total</th>
              <th>Status</th>
              <th>Waktu</th>
              <th width="200">Ubah Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($pesanans)): ?>
              <tr><td colspan="7" class="td-empty">Tidak ada pesanan ditemukan.</td></tr>
            <?php else: ?>
              <?php foreach ($pesanans as $p): ?>
              <tr id="row-<?= $p['id'] ?>">
                <td>
                  <code class="kode-pesanan"><?= htmlspecialchars($p['kode_pesanan']) ?></code>
                  <button class="btn-detail-pesanan"
                          onclick="lihatDetail(<?= $p['id'] ?>)"
                          title="Lihat detail item">🔍</button>
                </td>
                <td><?= htmlspecialchars($p['nama_pemesan']) ?></td>
                <td>Meja <?= htmlspecialchars($p['nomor_meja']) ?></td>
                <td><strong>Rp <?= number_format($p['total_harga'], 0, ',', '.') ?></strong></td>
                <td><span class="badge <?= statusBadge($p['status']) ?>"><?= ucfirst($p['status']) ?></span></td>
                <td><?= date('d/m/y H:i', strtotime($p['created_at'])) ?></td>
                <td>
                  <?php if ($p['status'] !== 'selesai' && $p['status'] !== 'dibatalkan'): ?>
                  <form method="POST" class="inline-form">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="update_status" value="1">
                    <input type="hidden" name="pesanan_id" value="<?= $p['id'] ?>">
                    <select name="status" class="select-status" onchange="this.form.submit()">
                      <option value="pending"  <?= $p['status']==='pending'  ?'selected':'' ?>>Pending</option>
                      <option value="diproses" <?= $p['status']==='diproses' ?'selected':'' ?>>Diproses</option>
                      <option value="selesai"  <?= $p['status']==='selesai'  ?'selected':'' ?>>Selesai</option>
                      <option value="dibatalkan" <?= $p['status']==='dibatalkan'?'selected':''?>>Batalkan</option>
                    </select>
                  </form>
                  <?php else: ?>
                    <span class="status-final">Status final</span>
                  <?php endif; ?>
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

<!-- Modal Detail Item Pesanan -->
<div class="modal-overlay-dash" id="modalDetailPesanan" onclick="tutupDetail(event)">
  <div class="modal-dash">
    <div class="modal-dash-header">
      <h3 id="modalDetailJudul">Detail Pesanan</h3>
      <button class="modal-close-dash" onclick="tutupDetail()">✕</button>
    </div>
    <div class="modal-dash-body" id="modalDetailBody">
      <p style="text-align:center;color:var(--abu);padding:20px;">Memuat...</p>
    </div>
  </div>
</div>

<script src="../js/dashboard.js"></script>
<script>
function lihatDetail(id) {
  var modal = document.getElementById('modalDetailPesanan');
  var body  = document.getElementById('modalDetailBody');
  var judul = document.getElementById('modalDetailJudul');

  modal.classList.add('open');
  document.body.style.overflow = 'hidden';
  body.innerHTML = '<p style="text-align:center;padding:30px;">⏳ Memuat detail...</p>';

  fetch('../api/detail_pesanan.php?id=' + id)
    .then(function(r) { return r.json(); })
    .then(function(data) {
      judul.textContent = 'Detail Pesanan – ' + data.kode_pesanan;
      var html = '<table class="data-table">';
      html += '<thead><tr><th>Menu</th><th>Harga</th><th>Qty</th><th>Subtotal</th></tr></thead><tbody>';
      data.items.forEach(function(item) {
        html += '<tr>';
        html += '<td>' + item.nama_menu + '</td>';
        html += '<td>Rp ' + Number(item.harga_saat_pesan).toLocaleString('id-ID') + '</td>';
        html += '<td>' + item.qty + 'x</td>';
        html += '<td>Rp ' + Number(item.subtotal).toLocaleString('id-ID') + '</td>';
        html += '</tr>';
      });
      html += '</tbody><tfoot><tr>';
      html += '<td colspan="3" style="text-align:right;font-weight:700;">Total</td>';
      html += '<td><strong>Rp ' + Number(data.total_harga).toLocaleString('id-ID') + '</strong></td>';
      html += '</tr></tfoot></table>';
      body.innerHTML = html;
    })
    .catch(function() {
      body.innerHTML = '<p style="text-align:center;color:#dc2626;padding:20px;">Gagal memuat detail.</p>';
    });
}

function tutupDetail(event) {
  if (event && event.target !== document.getElementById('modalDetailPesanan')
      && !event.target.classList.contains('modal-close-dash')) return;
  if (!event) {
    document.getElementById('modalDetailPesanan').classList.remove('open');
    document.body.style.overflow = '';
  } else {
    document.getElementById('modalDetailPesanan').classList.remove('open');
    document.body.style.overflow = '';
  }
}
</script>
</body>
</html>