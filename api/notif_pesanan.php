<?php
// ============================================================
//  api/notif_pesanan.php — Endpoint GET: Notifikasi pesanan pending
//  Return JSON: jumlah pesanan pending baru + total pending
//  Dipakai oleh badge notifikasi di topbar kasir dan dapur
// ============================================================
require_once '../includes/auth_check.php';
authCheck(); // semua role yang login boleh akses

require_once '../config/db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // Jumlah pesanan pending yang masuk dalam 30 detik terakhir (notifikasi baru)
    $stmt = $pdo->query("
        SELECT COUNT(*) FROM pesanan 
        WHERE status = 'pending' 
        AND created_at >= NOW() - INTERVAL 30 SECOND
    ");
    $recentCount = (int)$stmt->fetchColumn();

    // Total semua pesanan pending (untuk badge sidebar)
    $stmt2 = $pdo->query("SELECT COUNT(*) FROM pesanan WHERE status = 'pending'");
    $totalPending = (int)$stmt2->fetchColumn();

    echo json_encode([
        'success'       => true,
        'count'         => $recentCount,
        'total_pending' => $totalPending,
    ]);
} catch (PDOException $e) {
    error_log("Error notif_pesanan API: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success'       => false,
        'count'         => 0,
        'total_pending' => 0,
    ]);
}
