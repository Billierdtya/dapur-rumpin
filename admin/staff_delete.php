<?php
// ============================================================
//  admin/staff_delete.php — Handler hapus staff (POST only)
// ============================================================
require_once '../includes/auth_check.php';
authCheck(['owner', 'admin']);
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: staff_list.php'); exit;
}

if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    $_SESSION['flash'] = ['type'=>'error','msg'=>'Token tidak valid.'];
    header('Location: staff_list.php'); exit;
}

$id = (int)($_POST['id'] ?? 0);

// Jangan sampai admin menghapus akun dirinya sendiri
if ($id === (int)(currentUser()['id'])) {
    $_SESSION['flash'] = ['type'=>'error','msg'=>'Tidak bisa menghapus akun sendiri.'];
    header('Location: staff_list.php'); exit;
}

$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Akun berhasil dihapus.'];
} else {
    $_SESSION['flash'] = ['type'=>'error','msg'=>'Akun tidak ditemukan.'];
}

header('Location: staff_list.php');
exit;