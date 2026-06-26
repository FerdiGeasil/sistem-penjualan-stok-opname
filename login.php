<?php
require_once 'koneksi.php';

if (!empty($_SESSION['user_id'])) {
    header("Location: dashboard.php"); exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = escStr($conn, $_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $q    = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' LIMIT 1");
        $user = mysqli_fetch_assoc($q);

       if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nama']    = $user['nama'];
    $_SESSION['role']    = $user['role'];

    if($user['role'] == 'admin'){
        header("Location: dashboard.php");
    } else {
        header("Location: dashboard_kasir.php");
    }
    exit;
}else {
            $error = "Username atau password salah.";
        }
    } else {
        $error = "Isi semua field.";
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - PT Berkah Jaya Awing</title>

<meta property="og:title" content="PT Berkah Jaya Awing">
<meta property="og:description" content="Sistem Penjualan dan Inventori PT Berkah Jaya Awing">
<meta property="og:image" content="https://berkahjayaawing.store/img/logo.png">
<meta property="og:url" content="https://berkahjayaawing.store/">
<meta property="og:type" content="website">

<meta name="twitter:card" content="summary_large_image">
  <link rel="icon" type="image/png" sizes="32x32" href="img/logo.png?v=2">
  <link rel="icon" type="image/png" sizes="192x192" href="img/logo.png?v=2">
  <link rel="shortcut icon" href="img/logo.png?v=2">
  <link rel="stylesheet" href="css/style.css"/>
</head>
<body class="login-page">

<div class="login-card">

  <!-- Logo PT BJA -->
  <div class="login-logo-wrap">
    <img src="img/logo.png" alt="PT Berkah Jaya Awing" class="login-logo-img"/>
  </div>

  <h1 class="login-title">PT Berkah Jaya Awing</h1>
  <p class="login-sub">Sistem Informasi Penjualan &amp; Stok</p>

  <?php if ($error): ?>
  <div class="alert alert-danger" style="margin-bottom:16px">
    <svg width="15" height="15" fill="none" viewBox="0 0 24 24">
      <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8" fill="none"/>
      <line x1="12" y1="8" x2="12" y2="12" stroke="currentColor" stroke-width="1.8"/>
      <line x1="12" y1="16" x2="12.01" y2="16" stroke="currentColor" stroke-width="2"/>
    </svg>
    <span><?= htmlspecialchars($error) ?></span>
  </div>
  <?php endif; ?>

  <?php if(isset($_GET['success']) && $_GET['success'] === 'password'): ?>
  <div class="alert alert-success" style="margin-bottom:16px">
    <span>Password berhasil diganti. Silakan login kembali.</span>
  </div>
<?php endif; ?>

  <form method="POST" class="login-form">
    <div class="form-group">
      <label class="form-label">Username</label>
      <input class="form-input" type="text" name="username"
             placeholder="admin atau kasir"
             autocomplete="username" required
             value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
    </div>
    <div class="form-group">
  <label class="form-label">Password</label>

  <div class="password-wrap">
    <input class="form-input password-input"
           type="password"
           name="password"
           placeholder="••••••••"
           autocomplete="current-password"
           required>

    <span class="toggle-password">
      <svg width="18" height="18" fill="none" viewBox="0 0 24 24">
        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"
              stroke="currentColor"
              stroke-width="1.8"/>
        <circle cx="12" cy="12" r="3"
                stroke="currentColor"
                stroke-width="1.8"/>
      </svg>
    </span>
  </div>
</div>

<div style="text-align:right;margin-top:-6px;margin-bottom:15px;">
    <a href="ganti_password.php"
       style="
       color:#000;
       font-size:13px;
       text-decoration:none;
       font-weight:500;">
       Lupa / Ganti Password?
    </a>
</div>

    <button type="submit" class="btn primary btn-block" style="margin-top:10px">
      <svg width="14" height="14" fill="none" viewBox="0 0 24 24">
        <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"
              stroke="currentColor" stroke-width="1.8"/>
      </svg>
      Masuk
    </button>



  </form>

  <!-- <p class="login-hint">Demo: admin / admin#123 &nbsp;·&nbsp; kasir / kasir#123</p> -->
</div>

<script src="js/app.js?v=20"></script>
</body>
</html>