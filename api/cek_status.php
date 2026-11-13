<?php
// ============================================================
//  api/cek_status.php — Cek Status Pesanan via AJAX
// ============================================================
require_once '../config/db.php';

header('Content-Type: application/json');

$kode = trim($_GET['kode'] ?? '');

if ($kode === '') {
    echo json_encode(['success' => false, 'message' => 'Kode pesanan kosong']);
    exit;
}

$stmt = $pdo->prepare("SELECT status FROM pesanan WHERE kode_pesanan = ? LIMIT 1");
$stmt->execute([$kode]);
$pesanan = $stmt->fetch();

if (!$pesanan) {
    echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan']);
    exit;
}

// Data status
$statusData = [
    'pending'    => [
        'label'   => 'Menunggu Konfirmasi',
        'icon'    => '⏳',
        'color'   => '#a16207',
        'bg'      => '#fef9c3',
        'border'  => '#fde68a',
        'desc'    => 'Pesananmu sudah kami terima dan sedang menunggu konfirmasi dari tim dapur.',
        'step'    => 1,
    ],
    'diproses'   => [
        'label'   => 'Sedang Dimasak',
        'icon'    => '👨‍🍳',
        'color'   => '#1d4ed8',
        'bg'      => '#dbeafe',
        'border'  => '#bfdbfe',
        'desc'    => 'Pesananmu sedang dimasak oleh tim dapur kami dengan penuh cinta. Harap sabar ya!',
        'step'    => 2,
    ],
    'selesai'    => [
        'label'   => 'Siap Disajikan',
        'icon'    => '🍽️',
        'color'   => '#15803d',
        'bg'      => '#dcfce7',
        'border'  => '#bbf7d0',
        'desc'    => 'Pesananmu sudah siap! Silakan nikmati hidangan dari DapurRumpin.',
        'step'    => 3,
    ],
    'dibatalkan' => [
        'label'   => 'Dibatalkan',
        'icon'    => '❌',
        'color'   => '#dc2626',
        'bg'      => '#fee2e2',
        'border'  => '#fecaca',
        'desc'    => 'Maaf, pesananmu dibatalkan. Silakan hubungi staff kami untuk informasi lebih lanjut.',
        'step'    => 0,
    ],
];

$status = $pesanan['status'];
$data = $statusData[$status] ?? $statusData['pending'];
$data['status_key'] = $status;

echo json_encode(['success' => true, 'data' => $data]);
