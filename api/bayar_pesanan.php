<?php
// ============================================================
//  api/bayar_pesanan.php — Endpoint POST: Konfirmasi pembayaran QRIS
//  Method: POST
//  Parameter: pesanan_id, csrf_token
//  Response: JSON {success: true/false, message: "..."}
// ============================================================
require_once '../includes/auth_check.php';
authCheck(['owner', 'kasir']); // hanya owner dan kasir yang boleh proses bayar

require_once '../config/db.php';

header('Content-Type: application/json; charset=utf-8');

// Hanya terima method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan. Gunakan POST.']);
    exit;
}

// Validasi CSRF token
$csrfToken = $_POST['csrf_token'] ?? '';
if (empty($csrfToken) || $csrfToken !== ($_SESSION['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Token keamanan tidak valid. Silakan refresh halaman.']);
    exit;
}

// Validasi pesanan_id
$pesananId = (int)($_POST['pesanan_id'] ?? 0);
if ($pesananId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID pesanan tidak valid.']);
    exit;
}

// Cari pesanan di database
$stmt = $pdo->prepare("SELECT id, kode_pesanan, status, total_harga FROM pesanan WHERE id = ?");
$stmt->execute([$pesananId]);
$pesanan = $stmt->fetch();

if (!$pesanan) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan.']);
    exit;
}

// Pesanan harus berstatus 'diproses' untuk bisa dibayar
if ($pesanan['status'] !== 'diproses') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Pesanan ini tidak bisa dibayar. Status saat ini: "' . $pesanan['status'] . '". Hanya pesanan berstatus "diproses" yang bisa dibayar.'
    ]);
    exit;
}

// Update status pesanan: diproses → selesai, catat pembayaran
try {
    $stmt = $pdo->prepare("
        UPDATE pesanan 
        SET status = 'selesai', 
            payment_method = 'qris', 
            paid_at = NOW(),
            updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$pesananId]);

    echo json_encode([
        'success' => true,
        'message' => 'Pembayaran berhasil dikonfirmasi. Pesanan ' . $pesanan['kode_pesanan'] . ' telah lunas.'
    ]);
} catch (PDOException $e) {
    error_log("Error bayar pesanan: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan server. Silakan coba lagi.']);
}
