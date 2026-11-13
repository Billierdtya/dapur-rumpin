<?php
// ============================================================
//  api/update_status_pesanan.php — Endpoint ubah status pesanan
//  Role: dapur + owner
// ============================================================
require_once '../includes/auth_check.php';
authCheck(['owner', 'dapur']); 

require_once '../config/db.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

$id = (int)($data['id'] ?? 0);
$status = $data['status'] ?? '';
$allowed = ['pending','diproses','selesai','dibatalkan'];

if ($id <= 0 || !in_array($status, $allowed)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Data tidak valid.']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE pesanan SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Status pesanan berhasil diperbarui.'
    ]);
} catch (PDOException $e) {
    error_log("Update status error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Gagal mengubah status.'
    ]);
}
