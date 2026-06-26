<?php
require_once 'koneksi.php';
requireKasir();

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$cart  = $data['cart'] ?? [];
$tunai = (int)($data['tunai'] ?? 0);

if (empty($cart)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Keranjang masih kosong.'
    ]);
    exit;
}

mysqli_begin_transaction($conn);

try {
    $total = 0;

    foreach ($cart as $item) {
        $id  = (int)$item['id'];
        $qty = (int)$item['qty'];

        $q = mysqli_query($conn, "SELECT * FROM barang WHERE id=$id FOR UPDATE");
        $barang = mysqli_fetch_assoc($q);

        if (!$barang) {
            throw new Exception("Barang tidak ditemukan.");
        }

        if ($qty <= 0) {
            throw new Exception("Jumlah barang tidak valid.");
        }

        if ($qty > (int)$barang['stok']) {
            throw new Exception("Stok {$barang['nama_barang']} tidak cukup.");
        }

        $total += ((int)$barang['harga_jual'] * $qty);
    }

    if ($tunai < $total) {
        throw new Exception("Uang tunai kurang.");
    }

    $id_kasir = (int) $_SESSION['user_id'];

    if (!mysqli_query($conn, "
        INSERT INTO penjualan (tanggal, total, id_kasir, status)
        VALUES (NOW(), $total, $id_kasir, 'selesai')
    ")) {
        throw new Exception(mysqli_error($conn));
    }

    $id_penjualan = mysqli_insert_id($conn);

    foreach ($cart as $item) {
        $id  = (int)$item['id'];
        $qty = (int)$item['qty'];

        $q = mysqli_query($conn, "SELECT * FROM barang WHERE id=$id FOR UPDATE");
        $barang = mysqli_fetch_assoc($q);

        $harga = (int)$barang['harga_jual'];

        if (!mysqli_query($conn, "
    INSERT INTO detail_penjualan (id_penjualan, id_barang, jumlah, harga)
    VALUES ($id_penjualan, $id, $qty, $harga)
")) {
    throw new Exception(mysqli_error($conn));
}

if (!mysqli_query($conn, "
    UPDATE barang 
    SET stok = stok - $qty 
    WHERE id = $id
")) {
    throw new Exception(mysqli_error($conn));
}
    }

    mysqli_commit($conn);

    echo json_encode([
        'status' => 'success',
        'message' => 'Transaksi berhasil disimpan.',
        'id_penjualan' => $id_penjualan,
        'total' => $total,
        'tunai' => $tunai,
        'kembalian' => $tunai - $total
    ]);

} catch (Exception $e) {
    mysqli_rollback($conn);

    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}