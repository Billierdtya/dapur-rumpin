<?php
// ============================================================
//  api/pesanan_list.php — Endpoint GET: Daftar pesanan (JSON)
//  Dipakai oleh auto-refresh di kasir/pesanan.php
//  Parameter opsional: status, tgl, q (search)
//  Response: JSON {success: true, data: [...]}
// ============================================================
require_once '../includes/auth_check.php';
authCheck(['owner', 'kasir']); // hanya owner dan kasir

require_once '../config/db.php';

header('Content-Type: application/json; charset=utf-8');

// ── Filter dari query string ──────────────────────────────
$filterStatus = $_GET['status'] ?? '';
$filterDate   = $_GET['tgl']    ?? '';
$search       = trim($_GET['q'] ?? '');

$sql    = "SELECT * FROM pesanan WHERE 1=1";
$params = [];

// Filter by status (hanya jika bukan kosong)
if ($filterStatus !== '' && $filterStatus !== 'semua') {
    $allowed = ['pending', 'diproses', 'selesai', 'dibatalkan'];
    if (in_array($filterStatus, $allowed, true)) {
        $sql .= " AND status = ?";
        $params[] = $filterStatus;
    }
}

// Filter by tanggal
if ($filterDate !== '') {
    $sql .= " AND DATE(created_at) = ?";
    $params[] = $filterDate;
}

// Filter by search (nama pemesan atau kode pesanan)
if ($search !== '') {
    $sql .= " AND (nama_pemesan LIKE ? OR kode_pesanan LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY created_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $pesanans = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'data'    => $pesanans,
        'count'   => count($pesanans),
    ]);
} catch (PDOException $e) {
    error_log("Error pesanan_list API: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Gagal mengambil data pesanan.',
        'data'    => [],
    ]);
}
