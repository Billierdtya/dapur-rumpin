<?php
// ============================================================
//  admin/meja_delete.php — Soft delete meja (nonaktifkan)
//  Role: owner only
//  Method: POST dengan CSRF token
//  Aksi: UPDATE is_active = 0 (soft delete)
// ============================================================
require_once '../includes/auth_check.php';
authCheck('admin');
require_once '../config/db.php';

// Hanya terima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: meja_list.php'); exit;
}

// CSRF
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Token tidak valid.'];
    header('Location: meja_list.php'); exit;
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    $_SESSION['flash'] = ['type'=>'error', 'msg'=>'ID meja tidak valid.'];
    header('Location: meja_list.php'); exit;
}

// Cek meja ada
$stmt = $pdo->prepare("SELECT id, nomor_meja FROM meja WHERE id = ?");
$stmt->execute([$id]);
$meja = $stmt->fetch();

if (!$meja) {
    $_SESSION['flash'] = ['type'=>'error', 'msg'=>'Meja tidak ditemukan.'];
    header('Location: meja_list.php'); exit;
}

// Soft delete: nonaktifkan meja (bukan hapus permanen)
$stmt = $pdo->prepare("UPDATE meja SET is_active = 0 WHERE id = ?");
$stmt->execute([$id]);

$_SESSION['flash'] = [
    'type' => 'success',
    'msg'  => 'Meja ' . $meja['nomor_meja'] . ' berhasil dinonaktifkan.'
];
header('Location: meja_list.php');
exit;
