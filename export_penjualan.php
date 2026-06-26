<?php
require_once 'koneksi.php';
requireAdmin();

$dari   = $_GET['dari']   ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');
$dari   = escStr($conn, $dari);
$sampai = escStr($conn, $sampai);


$namaFile = "Laporan_Penjualan_" . date('d-m-Y_H-i-s') . ".xls";

header("Content-Type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=\"$namaFile\"");

$q = mysqli_query($conn,"
SELECT 
    p.tanggal,
    b.barcode,
    b.nama_barang,
    dp.jumlah,
    dp.harga,
    (dp.jumlah * dp.harga) AS subtotal
FROM penjualan p
JOIN detail_penjualan dp ON dp.id_penjualan = p.id
JOIN barang b ON b.id = dp.id_barang
WHERE DATE(p.tanggal) BETWEEN '$dari' AND '$sampai'
ORDER BY p.id DESC
");

$totalPendapatan = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COALESCE(SUM(total),0) t FROM penjualan
    WHERE DATE(tanggal) BETWEEN '$dari' AND '$sampai'
"))['t'];
?>

<table border="1">
    
<tr>
    <th colspan="6">
        LAPORAN PENJUALAN PT BERKAH JAYA AWING
    </th>
</tr>

<tr>
   <td colspan="6" align="center">
      Periode: <?= $dari ?> s/d <?= $sampai ?>
   </td>
</tr>

<tr></tr>

<tr>
    <th>Tanggal</th>
    <th>Barcode</th>
    <th>Produk</th>
    <th>Jumlah</th>
    <th>Harga</th>
    <th>Total</th>
</tr>

<?php while($r = mysqli_fetch_assoc($q)): ?>
<tr>
    <td><?= $r['tanggal'] ?></td>
    <td><?= $r['barcode'] ?></td>
    <td><?= $r['nama_barang'] ?></td>
    <td><?= $r['jumlah'] ?></td>
    <td><?= $r['harga'] ?></td>
    <td><?= $r['subtotal'] ?></td>
</tr>
<?php endwhile; ?>

<tr>
    <th colspan="5" align="right">TOTAL KESELURUHAN</th>
    <th><?= $totalPendapatan ?></th>
</tr>

</table>