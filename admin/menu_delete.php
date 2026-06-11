<?php
// ============================================================
//  admin/menu_delete.php — Handler hapus menu (POST only)
// ============================================================
require_once '../includes/auth_check.php';
authCheck('admin');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: menu_list.php'); exit;
}

// CSRF
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    $_SESSION['flash'] = ['type'=>'error','msg'=>'Token tidak valid.'];
    header('Location: menu_list.php'); exit;
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    $_SESSION['flash'] = ['type'=>'error','msg'=>'ID tidak valid.'];
    header('Location: menu_list.php'); exit;
}

// Ambil data gambar sebelum dihapus
$stmt = $pdo->prepare("SELECT gambar FROM menu WHERE id = ?");
$stmt->execute([$id]);
$menu = $stmt->fetch();

if (!$menu) {
    $_SESSION['flash'] = ['type'=>'error','msg'=>'Menu tidak ditemukan.'];
    header('Location: menu_list.php'); exit;
}

// Hapus dari DB
$stmt = $pdo->prepare("DELETE FROM menu WHERE id = ?");
$stmt->execute([$id]);

// Hapus file gambar kalau ada di folder menu
if ($menu['gambar'] && str_starts_with($menu['gambar'], 'img/menu/')) {
    @unlink('../' . $menu['gambar']);
}

$_SESSION['flash'] = ['type'=>'success','msg'=>'Menu berhasil dihapus.'];
header('Location: menu_list.php');
exit;