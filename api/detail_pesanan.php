<?php
// ============================================================
//  api/detail_pesanan.php — Endpoint JSON detail pesanan
// ============================================================
require_once '../includes/auth_check.php';
authCheck(); // hanya admin & staff

require_once '../config/db.php';

header('Content-Type: application/json; charset=utf-8');

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID tidak valid']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM pesanan WHERE id = ?");
$stmt->execute([$id]);
$pesanan = $stmt->fetch();

if (!$pesanan) {
    http_response_code(404);
    echo json_encode(['error' => 'Pesanan tidak ditemukan']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM detail_pesanan WHERE pesanan_id = ?");
$stmt->execute([$id]);
$items = $stmt->fetchAll();

echo json_encode([
    'kode_pesanan' => $pesanan['kode_pesanan'],
    'nama_pemesan' => $pesanan['nama_pemesan'],
    'nomor_meja'   => $pesanan['nomor_meja'],
    'total_harga'  => $pesanan['total_harga'],
    'status'       => $pesanan['status'],
    'items'        => $items,
]);