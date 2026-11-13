<?php
// ============================================================
//  cetak_struk.php — Halaman Cetak Struk (Thermal Printer)
//  Bisa diakses oleh Customer dan Kasir
// ============================================================
require_once 'config/db.php';

$kode = trim($_GET['kode'] ?? '');

if ($kode === '') {
    die("Kode pesanan tidak valid.");
}

$stmt = $pdo->prepare("SELECT * FROM pesanan WHERE kode_pesanan = ? LIMIT 1");
$stmt->execute([$kode]);
$pesanan = $stmt->fetch();

if (!$pesanan) {
    die("Pesanan tidak ditemukan.");
}

$stmt = $pdo->prepare("SELECT * FROM detail_pesanan WHERE pesanan_id = ?");
$stmt->execute([$pesanan['id']]);
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Struk Pesanan - <?= htmlspecialchars($pesanan['kode_pesanan']) ?></title>
  <style>
    /* Styling khusus untuk mensimulasikan kertas Thermal Printer (58mm - 80mm) */
    @import url('https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&display=swap');

    body {
      font-family: 'Courier Prime', monospace; /* Font monospace klasik struk */
      background: #f1f5f9;
      color: #000;
      margin: 0;
      padding: 20px;
      font-size: 13px;
    }
    .receipt-container {
      background: #fff;
      width: 300px; /* Cocok untuk 58mm */
      max-width: 100%;
      margin: 0 auto;
      padding: 20px 15px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .font-bold { font-weight: 700; }
    
    .brand {
      font-size: 18px;
      font-weight: 700;
      margin-bottom: 5px;
    }
    .address {
      font-size: 11px;
      margin-bottom: 15px;
      line-height: 1.4;
    }
    .divider {
      border-bottom: 1px dashed #000;
      margin: 10px 0;
    }
    .info-table, .item-table {
      width: 100%;
      border-collapse: collapse;
    }
    .info-table td {
      padding: 2px 0;
      vertical-align: top;
    }
    .item-table td {
      padding: 4px 0;
      vertical-align: top;
    }
    .item-name {
      display: block;
      margin-bottom: 2px;
    }
    .footer-msg {
      margin-top: 15px;
      font-size: 11px;
      line-height: 1.4;
    }

    /* Print Styles */
    @media print {
      body {
        background: transparent;
        padding: 0;
        margin: 0;
      }
      .receipt-container {
        width: 100%;
        max-width: 100%;
        margin: 0;
        padding: 0;
        box-shadow: none;
      }
      @page {
        margin: 0;
        size: auto; /* Membiarkan printer menentukan panjang */
      }
    }
  </style>
</head>
<body onload="window.print()">

<div class="receipt-container">
  <div class="text-center">
    <div class="brand">Dapur Rumpin</div>
    <div class="address">
      Jl. Teras Depan No. 123<br>
      Telp: 0812-3456-7890
    </div>
  </div>

  <div class="divider"></div>

  <table class="info-table">
    <tr>
      <td>Kode</td>
      <td>: <?= htmlspecialchars($pesanan['kode_pesanan']) ?></td>
    </tr>
    <tr>
      <td>Waktu</td>
      <td>: <?= date('d/m/Y H:i', strtotime($pesanan['created_at'])) ?></td>
    </tr>
    <tr>
      <td>Pemesan</td>
      <td>: <?= htmlspecialchars($pesanan['nama_pemesan']) ?></td>
    </tr>
    <tr>
      <td>Meja</td>
      <td>: <?= htmlspecialchars($pesanan['nomor_meja']) ?></td>
    </tr>
  </table>

  <div class="divider"></div>

  <table class="item-table">
    <?php foreach ($items as $item): ?>
    <tr>
      <td colspan="2">
        <span class="item-name font-bold"><?= htmlspecialchars($item['nama_menu']) ?></span>
      </td>
    </tr>
    <tr>
      <td><?= $item['qty'] ?> x Rp <?= number_format($item['harga_saat_pesan'], 0, ',', '.') ?></td>
      <td class="text-right">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
    </tr>
    <?php endforeach; ?>
  </table>

  <div class="divider"></div>

  <table class="item-table">
    <tr>
      <td class="font-bold">TOTAL BAYAR</td>
      <td class="text-right font-bold" style="font-size: 15px;">
        Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?>
      </td>
    </tr>
    <tr>
      <td>Status Pembayaran</td>
      <td class="text-right">
        <?= $pesanan['status'] === 'selesai' ? 'LUNAS' : strtoupper($pesanan['status']) ?>
      </td>
    </tr>
  </table>

  <div class="divider"></div>

  <div class="text-center footer-msg">
    Terima kasih atas kunjungan Anda!<br>
    Silakan datang kembali.<br><br>
    <small>Powered by DapurRumpin</small>
  </div>
</div>

</body>
</html>
