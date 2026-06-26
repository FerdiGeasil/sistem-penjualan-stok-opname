<?php
require_once 'koneksi.php';
requireAdmin();

$id  = intVal($_GET['id'] ?? 0);
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM barang WHERE id=$id"));
if (!$row) { header("Location: barang.php"); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $nama        = escStr($conn, $_POST['nama'] ?? '');
    $barcode     = escStr($conn, $_POST['barcode'] ?? '');
    $id_kategori = intVal($_POST['id_kategori'] ?? 0);
    $harga_beli  = intVal($_POST['harga_beli'] ?? 0);
    $harga_jual  = intVal($_POST['harga_jual'] ?? 0);
    $stok        = intVal($_POST['stok'] ?? 0);
    $min_stok    = intVal($_POST['min_stok'] ?? 0);
    
    $gambar = $row['gambar'] ?? '';

if (!empty($_FILES['gambar']['name'])) {
    $folder = 'img/produk/';

    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if ($_FILES['gambar']['size'] > 3 * 1024 * 1024) {

    $error = "Ukuran gambar maksimal 3 MB";

} elseif (!in_array($ext, $allowed)) {

    $error = "Format gambar harus JPG, JPEG, PNG, atau WEBP";

} else {

    $gambarBaru = 'produk_' . time() . '_' . rand(1000,9999) . '.' . $ext;

    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $folder . $gambarBaru)) {
        $gambar = $gambarBaru;
    }
}
}

if (!$error) {
    if (!$nama || !$barcode || !$id_kategori) {
        $error = "Nama, barcode, dan kategori wajib diisi.";
    } else {

        // cek barcode duplicate (kecuali barang ini sendiri)
        $cek = mysqli_query($conn, "
            SELECT id FROM barang
            WHERE barcode='$barcode'
            AND id != '$id'
        ");

        if (mysqli_num_rows($cek) > 0) {
            $error = "Barcode sudah digunakan barang lain.";
        } else {
            mysqli_query($conn, "UPDATE barang SET
                nama_barang='$nama',
                barcode='$barcode',
                id_kategori='$id_kategori',
                harga_beli=$harga_beli,
                harga_jual=$harga_jual,
                stok=$stok,
                min_stok=$min_stok,
                gambar='$gambar'
                WHERE id=$id");

            header("Location: barang.php?msg=edit");
            exit;
        }
    }
}

    // refresh form kalau error
    $row = array_merge($row, [
        'nama_barang' => $nama,
        'barcode' => $barcode,
        'id_kategori' => $id_kategori,
        'harga_beli' => $harga_beli,
        'harga_jual' => $harga_jual,
        'stok' => $stok,
        'min_stok' => $min_stok,
        'gambar' => $gambar
    ]);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Barang PT Berkah Jaya Awing</title>
  <link rel="icon" type="image/png" sizes="32x32" href="img/logo.png?v=2">
  <link rel="icon" type="image/png" sizes="192x192" href="img/logo.png?v=2">
  <link rel="shortcut icon" href="img/logo.png?v=2">
  <link rel="stylesheet" href="css/style.css?v=20"/>
</head>
<body>
<div class="app">
  <?php include 'sidebar.php'; ?>

  <div class="main">
    <div class="topbar">
      <div class="topbar-left">
        <h2>Edit Barang</h2>
        <p><a href="barang.php" class="breadcrumb-link">Data Barang</a> / Edit</p>
      </div>
      <div class="topbar-actions">
        <a href="barang.php" class="back-page-link">
  <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
    <path d="M15 18L9 12L15 6"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"/>
  </svg>
  <span>Kembali</span>
</a>
      </div>
    </div>

    <div class="content">
      <div class="form-page-wrap">
        <div class="form-card">

          <?php if ($error): ?>
          <div class="alert alert-danger" style="margin-bottom:16px">
            <span><?= htmlspecialchars($error) ?></span>
          </div>
          <?php endif; ?>

          <form method="POST" enctype="multipart/form-data">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Nama Barang <span class="required">*</span></label>
                <input class="form-input" type="text" name="nama"
                       value="<?= htmlspecialchars($row['nama_barang']) ?>" required>
              </div>
              <div class="form-group">
                <label class="form-label">Barcode <span class="required">*</span></label>
                <input class="form-input" type="text" name="barcode"
                       value="<?= htmlspecialchars($row['barcode']) ?>" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Kategori <span class="required">*</span></label>
                <select class="form-input" name="id_kategori" required>
    <option value="">Pilih Kategori</option>
    <?php
    $kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama");
    while($k = mysqli_fetch_assoc($kategori)):
    ?>
        <option value="<?= $k['id']; ?>"
            <?= $row['id_kategori'] == $k['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($k['nama']); ?>
        </option>
    <?php endwhile; ?>
</select>
              </div>
              
              <div class="form-group"><!-- spacer --></div>
            </div>
            
                          <div class="form-row">
  <div class="form-group">
    <label class="form-label">Foto Produk</label>

    <div class="upload-produk">
      <input
        type="file"
        id="gambarProduk"
        name="gambar"
        accept=".jpg,.jpeg,.png,.webp"
        class="upload-input">

      <label for="gambarProduk" class="upload-box">
        <span class="upload-title">Pilih Foto Produk Baru</span>
        <span class="upload-info" id="fileNameProduk">
          JPG, JPEG, PNG, WEBP • Maks. 3 MB
        </span>
      </label>
    </div>
  </div>

  <div class="form-group">
    <?php if (!empty($row['gambar'])): ?>
      <label class="form-label">Foto Saat Ini</label>
      <img
        src="img/produk/<?= htmlspecialchars($row['gambar']) ?>"
        style="width:100%;max-height:120px;object-fit:cover;border-radius:12px;border:1px solid #eee;">
    <?php endif; ?>
  </div>
</div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Harga Beli (Rp)</label>
                <input class="form-input" type="number" name="harga_beli" min="0"
                       value="<?= $row['harga_beli'] ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Harga Jual (Rp)</label>
                <input class="form-input" type="number" name="harga_jual" min="0"
                       value="<?= $row['harga_jual'] ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Stok Saat Ini</label>
                <input class="form-input" type="number" name="stok" min="0"
                       value="<?= $row['stok'] ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Minimum Stok</label>
                <input class="form-input" type="number" name="min_stok" min="0"
                       value="<?= $row['min_stok'] ?>">
              </div>
            </div>

            <div class="form-actions">
              <a href="barang.php" class="tb-btn">Batal</a>
              <button type="submit" name="update" class="tb-btn primary">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24">
                  <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"
                        stroke="currentColor" stroke-width="1.8" fill="none"/>
                </svg>
                Update Barang
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="js/app.js"></script>
</body>
</html>