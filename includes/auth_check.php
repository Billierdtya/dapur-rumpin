<?php
// ============================================================
//  includes/auth_check.php
//  Sistem Autentikasi DapurRumpin v2 — 3 Role (owner, kasir, dapur)
//
//  Cara pakai:
//    require_once '../includes/auth_check.php';
//    authCheck('owner');            ← hanya owner yang boleh masuk
//    authCheck('kasir');            ← hanya kasir yang boleh masuk
//    authCheck('dapur');            ← hanya dapur yang boleh masuk
//    authCheck(['owner','kasir']);   ← owner DAN kasir boleh masuk
//    authCheck();                   ← semua role yang sudah login
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Daftar role yang valid di sistem.
 * Dipakai untuk validasi internal.
 */
define('VALID_ROLES', ['admin', 'owner', 'kasir', 'dapur']);

/**
 * Mapping role → URL dashboard masing-masing.
 * Dipakai saat redirect user yang nyasar ke halaman yang bukan haknya.
 */
define('ROLE_DASHBOARD', [
    'owner' => 'admin/index.php',
    'admin' => 'admin/menu_list.php',
    'kasir' => 'kasir/pesanan.php',
    'dapur' => 'dapur/antrian.php',
]);

/**
 * Cek apakah user sudah login dan punya role yang sesuai.
 * Kalau belum login → redirect ke login.php
 * Kalau role tidak cocok → redirect ke dashboard role-nya sendiri
 *
 * @param string|array|null $requiredRole  Role atau array role yang diizinkan.
 *   - string: 'owner' | 'kasir' | 'dapur'
 *   - array:  ['owner', 'kasir'] → multi-role
 *   - null:   semua role yang sudah login boleh masuk
 */
function authCheck(string|array|null $requiredRole = null): void
{
    // ── Belum login sama sekali ──────────────────────────────
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . getLoginUrl() . '?err=login_required');
        exit;
    }

    // Kalau null (semua role boleh), langsung return
    if ($requiredRole === null) {
        return;
    }

    // Normalisasi jadi array supaya pengecekan seragam
    $allowedRoles = is_array($requiredRole) ? $requiredRole : [$requiredRole];
    $currentRole  = $_SESSION['user_role'] ?? '';

    // ── Role cocok → lanjut ─────────────────────────────────
    if (in_array($currentRole, $allowedRoles, true)) {
        return;
    }

    // ── Role tidak cocok → redirect ke 403 ───
    header('Location: ' . getBaseUrl() . '403.php');
    exit;
}

/**
 * Ambil URL dashboard berdasarkan role.
 *
 * @param string $role  Role user (owner/kasir/dapur)
 * @return string  Path relatif ke dashboard
 */
function getDashboardUrl(string $role): string
{
    return ROLE_DASHBOARD[$role] ?? 'login.php';
}

/**
 * Hitung path ke login.php dari file yang memanggil auth_check.
 * Bekerja untuk kedalaman folder admin/, kasir/, dapur/ dll.
 *
 * @return string  Path relatif ke login.php
 */
function getLoginUrl(): string
{
    // Cari root project berdasarkan letak auth_check.php itu sendiri
    $includesDir = __DIR__;               // .../dapur-rumpin/includes
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

/**
 * Hitung base URL (path ke root project) dari file yang memanggil.
 * Berguna untuk membuat link ke file lain di root.
 *
 * @return string  Path relatif ke root project (selalu diakhiri '/' jika ada depth)
 */
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
 *
 * @return array  Data user (id, nama, username, role)
 */
function currentUser(): array
{
    return [
        'id'       => $_SESSION['user_id']       ?? null,
        'nama'     => $_SESSION['user_nama']      ?? '',
        'username' => $_SESSION['user_username']   ?? '',
        'role'     => $_SESSION['user_role']       ?? '',
    ];
}

/**
 * Cek apakah user yang sedang login punya role tertentu.
 * Berguna untuk conditional rendering di template.
 *
 * @param string|array $role  Role atau array role yang dicek
 * @return bool
 */
function hasRole(string|array $role): bool
{
    $currentRole = $_SESSION['user_role'] ?? '';
    $roles = is_array($role) ? $role : [$role];
    return in_array($currentRole, $roles, true);
}

/**
 * Ambil label tampilan yang lebih ramah untuk role.
 * 'owner' → 'Owner', 'kasir' → 'Kasir', 'dapur' → 'Staff Dapur'
 *
 * @param string $role  Role user
 * @return string  Label tampilan
 */
function getRoleLabel(string $role): string
{
    return match ($role) {
        'admin' => 'Administrator',
        'owner' => 'Owner',
        'kasir' => 'Kasir',
        'dapur' => 'Staff Dapur',
        default => ucfirst($role),
    };
}