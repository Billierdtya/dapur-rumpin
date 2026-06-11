<?php
// ============================================================
//  includes/auth_check.php
//  Cara pakai:
//    require_once '../includes/auth_check.php';
//    authCheck('admin');   ← hanya admin yang boleh masuk
//    authCheck('staff');   ← hanya staff yang boleh masuk
//    authCheck();          ← admin DAN staff boleh masuk
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Cek apakah user sudah login dan punya role yang sesuai.
 * Kalau tidak, redirect ke login.php dengan pesan error.
 *
 * @param string|null $requiredRole  'admin' | 'staff' | null (semua role)
 */
function authCheck(?string $requiredRole = null): void
{
    // Belum login sama sekali
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . getLoginUrl() . '?err=login_required');
        exit;
    }

    // Sudah login tapi role tidak cocok
    if ($requiredRole !== null && $_SESSION['user_role'] !== $requiredRole) {
        // Admin yang nyasar ke halaman staff → arahkan ke dashboard admin
        if ($_SESSION['user_role'] === 'admin') {
            header('Location: ' . getBaseUrl() . 'admin/index.php?err=forbidden');
            exit;
        }
        // Staff yang nyasar ke halaman admin → arahkan ke halaman staff
        if ($_SESSION['user_role'] === 'staff') {
            header('Location: ' . getBaseUrl() . 'staff/pesanan.php?err=forbidden');
            exit;
        }
    }
}

/**
 * Hitung path ke login.php dari file yang memanggil auth_check.
 * Bekerja untuk kedalaman folder admin/ maupun staff/
 */
function getLoginUrl(): string
{
    // Cari root project berdasarkan letak auth_check.php itu sendiri
    $includesDir = __DIR__;             // .../dapur-rumpin/includes
    $rootDir     = dirname($includesDir); // .../dapur-rumpin

    // Hitung relatif dari script yang sedang dieksekusi
    $callerDir = dirname($_SERVER['SCRIPT_FILENAME']);
    $rel       = '';

    // Seberapa dalam caller berada dari root?
    $depth = substr_count(
        str_replace($rootDir, '', $callerDir),
        DIRECTORY_SEPARATOR
    );

    for ($i = 0; $i < $depth; $i++) {
        $rel .= '../';
    }

    return $rel . 'login.php';
}

function getBaseUrl(): string
{
    $includesDir = __DIR__;
    $rootDir     = dirname($includesDir);
    $callerDir   = dirname($_SERVER['SCRIPT_FILENAME']);
    $rel         = '';

    $depth = substr_count(
        str_replace($rootDir, '', $callerDir),
        DIRECTORY_SEPARATOR
    );

    for ($i = 0; $i < $depth; $i++) {
        $rel .= '../';
    }

    return $rel;
}

/**
 * Shortcut: ambil data user yang sedang login dari session.
 */
function currentUser(): array
{
    return [
        'id'       => $_SESSION['user_id']   ?? null,
        'nama'     => $_SESSION['user_nama']  ?? '',
        'username' => $_SESSION['user_username'] ?? '',
        'role'     => $_SESSION['user_role']  ?? '',
    ];
}