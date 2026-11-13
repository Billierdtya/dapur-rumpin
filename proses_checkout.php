<?php
// ============================================================
//  proses_checkout.php — AJAX Endpoint Simpan Pesanan ke DB
//  Dipanggil oleh main.js via fetch() saat customer checkout
//  Tidak butuh login (guest checkout)
// ============================================================

// Hanya terima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');
require_once 'config/db.php';

// ── Baca body JSON dari fetch() ───────────────────────────
$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Data tidak valid.']);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Validasi field wajib ──────────────────────────────────
$namaPemesan = trim($data['nama_pemesan'] ?? '');
$nomorMeja   = trim($data['nomor_meja']   ?? '');

// Jika scan QR valid, paksa nomor_meja dari session (anti-tamper)
if (!empty($_SESSION['qr_valid']) && !empty($_SESSION['qr_meja'])) {
    $nomorMeja = $_SESSION['qr_meja'];
}
$items       = $data['items']             ?? [];

if ($namaPemesan === '') {
    echo json_encode(['success' => false, 'message' => 'Nama pemesan wajib diisi.']);
    exit;
}
if ($nomorMeja === '') {
    echo json_encode(['success' => false, 'message' => 'Nomor meja wajib diisi.']);
    exit;
}
if (empty($items) || !is_array($items)) {
    echo json_encode(['success' => false, 'message' => 'Keranjang tidak boleh kosong.']);
    exit;
}

// ── Hitung total & validasi setiap item ───────────────────
$grandTotal  = 0;
$itemsBersih = [];

foreach ($items as $item) {
    $nama  = trim($item['nama']  ?? '');
    $harga = (int)($item['harga'] ?? 0);
    $qty   = (int)($item['qty']   ?? 0);

    if ($nama === '' || $harga <= 0 || $qty <= 0) continue; // skip item tidak valid

    $subtotal      = $harga * $qty;
    $grandTotal   += $subtotal;
    $itemsBersih[] = [
        'nama'     => $nama,
        'harga'    => $harga,
        'qty'      => $qty,
        'subtotal' => $subtotal,
    ];
}

if (empty($itemsBersih)) {
    echo json_encode(['success' => false, 'message' => 'Tidak ada item valid dalam pesanan.']);
    exit;
}

// ── Generate kode pesanan unik: DR-YYYYMMDD-XXXX ─────────
function generateKodePesanan(PDO $pdo): string {
    $tgl    = date('Ymd');
    $prefix = "DR-{$tgl}-";
    // Ambil nomor urut hari ini
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM pesanan WHERE kode_pesanan LIKE ? AND DATE(created_at) = CURDATE()"
    );
    $stmt->execute([$prefix . '%']);
    $urut = (int)$stmt->fetchColumn() + 1;
    return $prefix . str_pad($urut, 4, '0', STR_PAD_LEFT);
}

// ── Simpan ke DB dalam satu transaksi ─────────────────────
try {
    $pdo->beginTransaction();

    // 1. Insert header pesanan
    $kodePesanan = generateKodePesanan($pdo);

    $stmt = $pdo->prepare("
        INSERT INTO pesanan (kode_pesanan, nama_pemesan, nomor_meja, total_harga, status)
        VALUES (?, ?, ?, ?, 'pending')
    ");
    $stmt->execute([$kodePesanan, $namaPemesan, $nomorMeja, $grandTotal]);
    $pesananId = (int)$pdo->lastInsertId();

    // 2. Insert setiap detail item
    $stmtDetail = $pdo->prepare("
        INSERT INTO detail_pesanan (pesanan_id, menu_id, nama_menu, harga_saat_pesan, qty, subtotal)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    foreach ($itemsBersih as $item) {
        // Cari menu_id berdasarkan nama (snapshot, boleh NULL kalau tidak ketemu)
        $stmtMenu = $pdo->prepare("SELECT id FROM menu WHERE nama = ? LIMIT 1");
        $stmtMenu->execute([$item['nama']]);
        $menuId = $stmtMenu->fetchColumn() ?: null;

        $stmtDetail->execute([
            $pesananId,
            $menuId,
            $item['nama'],
            $item['harga'],
            $item['qty'],
            $item['subtotal'],
        ]);
    }

    $pdo->commit();

    // ── Response sukses ───────────────────────────────────
    $_SESSION['last_kode_pesanan'] = $kodePesanan;
    
    echo json_encode([
        'success'      => true,
        'message'      => 'Pesanan berhasil disimpan.',
        'kode_pesanan' => $kodePesanan,
        'total_harga'  => $grandTotal,
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Checkout Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan server. Silakan coba lagi.',
    ]);
}