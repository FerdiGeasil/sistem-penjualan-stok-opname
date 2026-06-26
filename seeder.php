<?php
/**
 * seeder.php
 * Jalankan SEKALI di browser setelah import SQL untuk set password yang benar.
 * Setelah berhasil, HAPUS file ini dari server.
 * Akses: http://localhost/sistem_penjualan/seeder.php
 */

$conn = mysqli_connect("localhost", "root", "", "sistem_penjualan");
if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());

$users = [
    ['admin', 'admin#123', 'admin'],
    ['kasir', 'kasir#123', 'kasir'],
];

$ok = 0;
foreach ($users as [$uname, $pass, $role]) {
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $uname = mysqli_real_escape_string($conn, $uname);
    $res = mysqli_query($conn, "UPDATE users SET password='$hash' WHERE username='$uname'");
    if ($res) $ok++;
}

echo "<h2> Seeder selesai — $ok user diperbarui.</h2>";
echo "<p>Akun yang tersedia:</p>";
echo "<ul>";
echo "<li><strong>admin</strong> / admin#123 — role: Admin</li>";
echo "<li><strong>kasir</strong> / kasir#123 — role: Kasir</li>";
echo "</ul>";
echo "<p style='color:red'><strong>⚠️ PENTING: Hapus file seeder.php ini sekarang!</strong></p>";
echo "<p><a href='login.php'>→ Pergi ke Login</a></p>";