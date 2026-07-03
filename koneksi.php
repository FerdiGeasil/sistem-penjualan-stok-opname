<?php
/**
 * koneksi.php — Koneksi DB + Session + Auth helpers
 * Gunakan: require_once 'koneksi.php'; di semua file
 */

date_default_timezone_set('Asia/Jakarta');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Konfigurasi koneksi database Docker
$conn = mysqli_connect(
    "db",
    "root",
    "rahasia_portofolio",
    "db_native_project"
);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");

/* ── Auth guards ── */
function requireLogin() {
    if (empty($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: dashboard.php?err=akses");
        exit;
    }
}

function requireKasir() {
    requireLogin();
    if (empty($_SESSION['role']) || $_SESSION['role'] !== 'kasir') {
        header("Location: dashboard.php");
        exit;
    }
}

/* ── Sanitasi input ── */
if (!function_exists('intVal')) {
    function intVal($v) {
        return (int) $v;
    }
}

if (!function_exists('escStr')) {
    // Dioptimasi: Menggunakan global $conn agar pemanggilan di file lain lebih ringkas
    function escStr($v) {
        global $conn;
        return mysqli_real_escape_string($conn, trim((string)$v));
    }
}