<?php
// ============================================================
//  login.php  (taruh di root project)
// ============================================================

session_start();

// Kalau sudah login, langsung redirect sesuai role
if (!empty($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header('Location: admin/index.php');
    } else {
        header('Location: staff/pesanan.php');
    }
    exit;
}

require_once 'config/db.php';

$error   = '';
$success = '';

// ── Pesan dari redirect lain ──────────────────────────────
$errCode = $_GET['err'] ?? '';
if ($errCode === 'login_required') $error   = 'Silakan login terlebih dahulu untuk mengakses halaman tersebut.';
if ($errCode === 'forbidden')      $error   = 'Akses ditolak. Anda tidak memiliki izin untuk halaman itu.';
if ($errCode === 'logged_out')     $success = 'Anda berhasil logout. Sampai jumpa!';

// ── Proses Form POST ──────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF token sederhana berbasis session
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $error = 'Request tidak valid. Silakan coba lagi.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            $error = 'Username dan password wajib diisi.';
        } else {
            // Cari user di DB — prepared statement, aman dari SQL injection
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Regenerate session ID untuk cegah session fixation
                session_regenerate_id(true);

                $_SESSION['user_id']       = $user['id'];
                $_SESSION['user_nama']     = $user['nama'];
                $_SESSION['user_username'] = $user['username'];
                $_SESSION['user_role']     = $user['role'];

                // Redirect sesuai role
                if ($user['role'] === 'admin') {
                    header('Location: admin/index.php');
                } else {
                    header('Location: staff/pesanan.php');
                }
                exit;
            } else {
                // Pesan error yang sama untuk username/password salah
                // (tidak kasih tahu mana yang salah — security best practice)
                $error = 'Username atau password salah. Silakan coba lagi.';

                // Opsional: tambahkan delay kecil untuk melambat brute-force
                sleep(1);
            }
        }
    }
}

// Generate CSRF token baru setiap kali halaman dimuat
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Dashboard – DapurRumpin</title>
  <link rel="icon" type="image/jpg" href="img/logo.jpg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap">
  <style>
    /* ── Variables (sama dengan style.css utama) ── */
    :root {
      --hijau-tua:    #1a3c2b;
      --hijau-utama:  #2d6a4f;
      --hijau-muda:   #52b788;
      --hijau-cerah:  #74c69d;
      --hijau-pucat:  #d8f3dc;
      --hijau-pastel: #f0faf2;
      --kuning-aksen: #f4a261;
      --abu:          #5a6472;
      --gelap:        #1b1f23;
      --putih:        #ffffff;
      --radius-lg:    16px;
      --radius-md:    10px;
      --transition:   all 0.3s cubic-bezier(.4,0,.2,1);
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'DM Sans', sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background:
        linear-gradient(135deg, rgba(26,60,43,0.95) 0%, rgba(45,106,79,0.9) 100%),
        url('https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=1200&q=70') center/cover no-repeat;
      padding: 20px;
    }

    /* ── Card ── */
    .login-card {
      background: var(--putih);
      border-radius: var(--radius-lg);
      padding: 44px 40px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 24px 64px rgba(0,0,0,0.25);
      animation: fadeUp .5s ease both;
    }

    @keyframes fadeUp {
      from { opacity:0; transform:translateY(20px); }
      to   { opacity:1; transform:translateY(0); }
    }

    /* ── Header Card ── */
    .login-header {
      text-align: center;
      margin-bottom: 32px;
    }

    .login-logo {
      width: 68px; height: 68px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid var(--hijau-muda);
      margin: 0 auto 14px;
      display: block;
    }

    .login-header h1 {
      font-family: 'Playfair Display', serif;
      font-size: 1.6rem;
      font-weight: 800;
      color: var(--hijau-tua);
      margin-bottom: 4px;
    }

    .login-header h1 span { color: var(--hijau-muda); }

    .login-header p {
      font-size: 0.85rem;
      color: var(--abu);
    }

    /* ── Alert ── */
    .alert {
      padding: 12px 16px;
      border-radius: var(--radius-md);
      font-size: 0.875rem;
      margin-bottom: 20px;
      line-height: 1.5;
    }

    .alert-error {
      background: #fff1f2;
      border: 1px solid #fecdd3;
      color: #be123c;
    }

    .alert-success {
      background: var(--hijau-pastel);
      border: 1px solid var(--hijau-pucat);
      color: var(--hijau-utama);
    }

    /* ── Form ── */
    .form-group {
      display: flex;
      flex-direction: column;
      gap: 6px;
      margin-bottom: 18px;
    }

    .form-group label {
      font-size: 0.83rem;
      font-weight: 600;
      color: var(--gelap);
    }

    .input-wrap {
      position: relative;
    }

    .input-wrap .input-icon {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 1rem;
      pointer-events: none;
    }

    .form-group input {
      width: 100%;
      padding: 12px 14px 12px 42px;
      border: 1.5px solid #e2e8f0;
      border-radius: var(--radius-md);
      font-family: 'DM Sans', sans-serif;
      font-size: 0.92rem;
      color: var(--gelap);
      outline: none;
      transition: var(--transition);
      background: #f8fafc;
    }

    .form-group input:focus {
      border-color: var(--hijau-muda);
      background: var(--putih);
      box-shadow: 0 0 0 3px rgba(82,183,136,0.15);
    }

    /* Toggle show/hide password */
    .toggle-pw {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      font-size: 1rem;
      color: var(--abu);
      padding: 4px;
      line-height: 1;
    }

    /* ── Submit Button ── */
    .btn-login {
      width: 100%;
      padding: 13px;
      background: var(--hijau-utama);
      color: var(--putih);
      border: none;
      border-radius: 50px;
      font-family: 'DM Sans', sans-serif;
      font-size: 1rem;
      font-weight: 700;
      cursor: pointer;
      transition: var(--transition);
      box-shadow: 0 4px 16px rgba(45,106,79,0.35);
      margin-top: 4px;
    }

    .btn-login:hover {
      background: var(--hijau-tua);
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(45,106,79,0.4);
    }

    .btn-login:active { transform: translateY(0); }

    /* ── Footer Card ── */
    .login-footer {
      text-align: center;
      margin-top: 24px;
      font-size: 0.82rem;
      color: var(--abu);
    }

    .login-footer a {
      color: var(--hijau-utama);
      font-weight: 600;
      text-decoration: none;
    }

    .login-footer a:hover { text-decoration: underline; }

    /* ── Role Badges (info visual) ── */
    .role-info {
      display: flex;
      gap: 10px;
      margin-top: 20px;
      padding-top: 20px;
      border-top: 1px solid #f1f5f9;
    }

    .role-badge {
      flex: 1;
      text-align: center;
      padding: 10px 8px;
      border-radius: var(--radius-md);
      font-size: 0.78rem;
    }

    .role-badge.admin  { background: #fff7ed; border: 1px solid #fed7aa; color: #c2410c; }
    .role-badge.staff  { background: var(--hijau-pastel); border: 1px solid var(--hijau-pucat); color: var(--hijau-utama); }

    .role-badge .role-icon { font-size: 1.2rem; display: block; margin-bottom: 4px; }
    .role-badge .role-name { font-weight: 700; display: block; margin-bottom: 2px; }

    @media (max-width: 480px) {
      .login-card { padding: 32px 24px; }
    }
  </style>
</head>
<body>

  <div class="login-card">

    <div class="login-header">
      <img src="img/logo.jpg" alt="Logo DapurRumpin" class="login-logo"
           onerror="this.style.display='none'">
      <h1>Dapur<span>Rumpin</span></h1>
      <p>Dashboard Admin &amp; Staff</p>
    </div>

    <!-- Alert error / success -->
    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert alert-success"> <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Form Login -->
    <form method="POST" action="login.php" autocomplete="on" novalidate>
      <!-- CSRF token tersembunyi -->
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

      <div class="form-group">
        <label for="username">Username</label>
        <div class="input-wrap">
          <input
            type="text"
            id="username"
            name="username"
            placeholder="Masukkan username"
            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
            autocomplete="username"
            required
          >
        </div>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <div class="input-wrap">
          <input
            type="password"
            id="password"
            name="password"
            placeholder="Masukkan password"
            autocomplete="current-password"
            required
          >
          <button type="button" class="toggle-pw" onclick="togglePassword()" title="Tampilkan password">
            👁️
          </button>
        </div>
      </div>

      <button type="submit" class="btn-login">Masuk ke Dashboard</button>
    </form>

    <div class="login-footer">
      <a href="index.php">← Kembali ke Website</a>

      <!-- Info role (hanya visual, bukan fitur register) -->
      <div class="role-info">
        <div class="role-badge admin">
          <span class="role-name">Admin</span>
          <span>Kelola Menu &amp; Staff</span>
        </div>
        <div class="role-badge staff">
          <span class="role-name">Staff</span>
          <span>Kelola Pesanan</span>
        </div>
      </div>
    </div>

  </div>

  <script>
    // Toggle show/hide password
    function togglePassword() {
      var input = document.getElementById('password');
      var btn   = document.querySelector('.toggle-pw');
      if (input.type === 'password') {
        input.type = 'text';
        btn.textContent = '🙈';
        btn.title = 'Sembunyikan password';
      } else {
        input.type = 'password';
        btn.textContent = '👁️';
        btn.title = 'Tampilkan password';
      }
    }

    // Auto-focus ke username kalau kosong, ke password kalau username sudah ada
    window.addEventListener('DOMContentLoaded', function() {
      var un = document.getElementById('username');
      var pw = document.getElementById('password');
      if (un.value.trim() !== '') {
        pw.focus();
      } else {
        un.focus();
      }
    });
  </script>

</body>
</html>