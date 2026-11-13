<?php
// ============================================================
//  api/antrian_dapur.php — Endpoint GET: Antrian pesanan untuk dapur
//  Return JSON: array pesanan (pending + diproses) beserta detail item
//  Dipakai oleh auto-refresh di dapur/antrian.php
// ============================================================
require_once '../includes/auth_check.php';
authCheck(['owner', 'dapur']); // hanya owner dan dapur

require_once '../config/db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // Ambil pesanan aktif: pending dan diproses, urutkan dari paling lama
    $stmt = $pdo->query("
        SELECT id, kode_pesanan, nama_pemesan, nomor_meja, total_harga, status, created_at
        FROM pesanan 
        WHERE status IN ('pending', 'diproses')
        ORDER BY created_at ASC
    ");
    $pesanans = $stmt->fetchAll();

    // Ambil detail item untuk setiap pesanan
    $result = [];
    foreach ($pesanans as $p) {
        $stmtDetail = $pdo->prepare("
            SELECT nama_menu, harga_saat_pesan, qty, subtotal 
            FROM detail_pesanan 
            WHERE pesanan_id = ?
        ");
        $stmtDetail->execute([$p['id']]);
        $items = $stmtDetail->fetchAll();

        $p['items'] = $items;
        $result[] = $p;
    }

    echo json_encode([
        'success' => true,
        'data'    => $result,
        'count'   => count($result),
    ]);
} catch (PDOException $e) {
    error_log("Error antrian_dapur API: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'data'    => [],
        'message' => 'Gagal mengambil data antrian.',
    ]);
}
