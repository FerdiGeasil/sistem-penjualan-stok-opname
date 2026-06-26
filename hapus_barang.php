<?php
require_once 'koneksi.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: barang");
    exit;
}

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    header("Location: barang?msg=hapus_error");
    exit;
}

$tables = [
    'detail_penjualan' => 'id_barang',
    'pembelian'        => 'id_barang',
    'restock_log'      => 'id_barang',
    'stok_opname_log'  => 'id_barang'
];

foreach ($tables as $table => $column) {
    $q = mysqli_query($conn, "SELECT COUNT(*) AS total FROM $table WHERE $column = $id");

    if (!$q) {
        header("Location: barang?msg=hapus_error");
        exit;
    }

    $r = mysqli_fetch_assoc($q);

    if ((int)$r['total'] > 0) {
        header("Location: barang?msg=hapus_error");
        exit;
    }
}

$hapus = mysqli_query($conn, "DELETE FROM barang WHERE id = $id");

if ($hapus && mysqli_affected_rows($conn) > 0) {
    header("Location: barang?msg=hapus");
    exit;
}

header("Location: barang?msg=hapus_error");
exit;