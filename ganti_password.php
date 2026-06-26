<?php require_once 'koneksi.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ganti Password</title>

<link rel="icon" type="image/png" sizes="32x32" href="img/logo.png?v=2">
<link rel="icon" type="image/png" sizes="192x192" href="img/logo.png?v=2">
<link rel="shortcut icon" href="img/logo.png?v=2">
<link rel="stylesheet" href="css/style.css?v=20">
</head>

<body class="login-page">

<div class="login-card">

<div class="login-logo-wrap">
<img src="img/logo.png"
class="login-logo-img">
</div>

<h2 class="login-title">
Ganti Password
</h2>
<?php if(isset($_GET['error'])): ?>
  <div class="alert alert-danger">
    <span><?= htmlspecialchars($_GET['error']) ?></span>
  </div>
<?php endif; ?>

<form method="POST"
action="proses_ganti_password.php">

<div class="form-group">
<label>Username</label>

<input type="text"
class="form-input"
name="username"
required>
</div>

<div class="form-group">
<label class="form-label">Password Lama</label>

<div class="password-wrap">

<input type="password"
class="form-input password-input"
name="password_lama"
required>

<span class="toggle-password">

<svg class="eye-open"
width="18"
height="18"
fill="none"
viewBox="0 0 24 24">

<path d="M1 12s4-7 11-7
11 7
11 7-4 7-11 7S1 12 1 12z"
stroke="currentColor"
stroke-width="1.8"/>

<circle cx="12"
cy="12"
r="3"
stroke="currentColor"
stroke-width="1.8"/>

</svg>

</span>

</div>
</div>

<div class="form-group">
<label class="form-label">Password Baru</label>

<div class="password-wrap">

<input type="password"
class="form-input password-input"
name="password_baru"
required>

<span class="toggle-password">

<svg class="eye-open"
width="18"
height="18"
fill="none"
viewBox="0 0 24 24">

<path d="M1 12s4-7 11-7
11 7
11 7-4 7-11 7S1 12 1 12z"
stroke="currentColor"
stroke-width="1.8"/>

<circle cx="12"
cy="12"
r="3"
stroke="currentColor"
stroke-width="1.8"/>

</svg>

</span>

</div>
</div>

<div class="form-group">
<label class="form-label">Konfirmasi Password</label>

<div class="password-wrap">

<input type="password"
class="form-input password-input"
name="konfirmasi"
required>

<span class="toggle-password">

<svg class="eye-open"
width="18"
height="18"
fill="none"
viewBox="0 0 24 24">

<path d="M1 12s4-7 11-7
11 7
11 7-4 7-11 7S1 12 1 12z"
stroke="currentColor"
stroke-width="1.8"/>

<circle cx="12"
cy="12"
r="3"
stroke="currentColor"
stroke-width="1.8"/>

</svg>

</span>
</div>
</div>

<button class="btn primary btn-block">
Ubah Password
</button>

<div style="text-align:center;margin-top:18px;">

<a href="login.php" class="back-login-link">

<svg width="16" height="16"
fill="none"
viewBox="0 0 24 24">


<path d="M15 18L9 12L15 6"
stroke="currentColor"
stroke-width="2"
stroke-linecap="round"
stroke-linejoin="round"/>

</svg>

<span>Kembali ke Login</span>

</a>

</div>

</form>

</div>


<script src="js/app.js?v=10"></script>
</body>
</html>