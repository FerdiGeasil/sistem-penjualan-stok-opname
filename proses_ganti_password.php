<?php
require_once 'koneksi.php';

$username=$_POST['username'];
$lama=$_POST['password_lama'];
$baru=$_POST['password_baru'];
$konfirmasi=$_POST['konfirmasi'];

if($baru!=$konfirmasi){

header("Location:ganti_password.php?error=Konfirmasi password tidak cocok");
exit;
}

$q=mysqli_query($conn,"
SELECT *
FROM users
WHERE username='$username'
LIMIT 1
");

$user=mysqli_fetch_assoc($q);

if(!$user){

header("Location:ganti_password.php?error=User tidak ditemukan");
exit;
}

if(!password_verify(
$lama,
$user['password']
)){

header("Location:ganti_password.php?error=Password lama salah");
exit;
}

$passwordBaru=password_hash(
$baru,
PASSWORD_DEFAULT
);

mysqli_query($conn,"
UPDATE users
SET password='$passwordBaru'
WHERE id=".$user['id']
);

header(
"Location:login.php?success=password"
);
exit;