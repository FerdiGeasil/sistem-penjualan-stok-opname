<?php
require_once 'koneksi.php';
requireAdmin();

header("Content-Type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=laporan_stok.xls");

$q = mysqli_query($conn,"
SELECT 
    barang.*,
    kategori.nama AS kategori
FROM barang
LEFT JOIN kategori
ON barang.id_kategori = kategori.id
ORDER BY barang.stok ASC
");
?>

<table border="1">
<tr>
    <th colspan="6">
        LAPORAN STOK PT BERKAH JAYA AWING
    </th>
</tr>

<tr>
    <th>Barcode</th>
    <th>Nama Barang</th>
    <th>Kategori</th>
    <th>Stok</th>
    <th>Min Stok</th>
    <th>Status</th>
</tr>

<?php while($r = mysqli_fetch_assoc($q)): 

$stok = (int)$r['stok'];
$min  = (int)$r['min_stok'];

if($stok == 0){
    $status = 'Habis';
}elseif($stok <= $min){
    $status = 'Menipis';
}else{
    $status = 'Normal';
}
?>

<tr>
    <td><?= $r['barcode'] ?></td>
    <td><?= $r['nama_barang'] ?></td>
    <td><?= $r['kategori'] ?></td>
    <td><?= $stok ?></td>
    <td><?= $min ?></td>
    <td><?= $status ?></td>
</tr>

<?php endwhile; ?>

</table>