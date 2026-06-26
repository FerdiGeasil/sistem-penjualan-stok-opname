<?php
require_once 'koneksi.php';
requireAdmin();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $nama        = escStr($conn, $_POST['nama'] ?? '');
    $barcode     = escStr($conn, $_POST['barcode'] ?? '');
    $id_kategori = intval($_POST['id_kategori'] ?? 0);
    $harga_beli  = intval($_POST['harga_beli'] ?? 0);
    $harga_jual  = intval($_POST['harga_jual'] ?? 0);
    $stok        = intval($_POST['stok'] ?? 0);
    $min_stok    = intval($_POST['min_stok'] ?? 0);
    
    $gambar = '';

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

    $gambar = 'produk_' . time() . '_' . rand(1000,9999) . '.' . $ext;

    move_uploaded_file(
        $_FILES['gambar']['tmp_name'],
        $folder . $gambar
    );
}
}


if (!$error) {
    $cek = mysqli_query($conn, "SELECT id FROM barang WHERE barcode='$barcode'");

    if (mysqli_num_rows($cek) > 0) {
        $error = "Barcode sudah digunakan.";
    } else {
        mysqli_query($conn, "
            INSERT INTO barang
            (nama_barang, barcode, id_kategori, harga_beli, harga_jual, stok, min_stok, gambar)
            VALUES
            ('$nama','$barcode','$id_kategori','$harga_beli','$harga_jual','$stok','$min_stok','$gambar')
        ");

        header("Location: barang.php?msg=tambah");
        exit;
    }
}
}



$search = escStr($conn, $_GET['search'] ?? '');
$kat    = escStr($conn, $_GET['kat'] ?? '');
$where  = "WHERE 1=1";
if ($search) $where .= " AND (nama_barang LIKE '%$search%' OR barcode LIKE '%$search%')";
if ($kat) $where .= " AND id_kategori='$kat'";

$data = mysqli_query($conn, "
    SELECT 
        barang.*,
        kategori.nama AS nama_kategori
    FROM barang
    LEFT JOIN kategori 
        ON barang.id_kategori = kategori.id
    $where
    ORDER BY barang.id DESC
");

$kategoriList = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama");
$kategoriModal = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama");

$total     = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM barang $where"))['c'];

$msg = $_GET['msg'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Data Barang PT Berkah Jaya Awing</title>
  <link rel="icon" type="image/png" sizes="32x32" href="img/logo.png?v=2">
  <link rel="icon" type="image/png" sizes="192x192" href="img/logo.png?v=2">
  <link rel="shortcut icon" href="img/logo.png?v=2">
  <link rel="stylesheet" href="css/style.css?v=20"/>
</head>
<body>
<div class="app">

<div class="sidebar-overlay" id="sidebarOverlay"></div>

  <?php include 'sidebar.php'; ?>

  <div class="main">
    <div class="topbar">
    <div class="topbar-left">

        <button class="menu-toggle" id="menuToggle" aria-label="Menu">
  <svg width="20" height="20" fill="none" viewBox="0 0 24 24">
    <line x1="3" y1="6" x2="21" y2="6"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"/>

    <line x1="3" y1="12" x2="21" y2="12"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"/>

    <line x1="3" y1="18" x2="21" y2="18"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"/>
  </svg>
</button>

        <h2>Data Barang</h2>
        <p>Master Data — CRUD Barang</p>
      </div>

      <div class="topbar-actions">
  <button type="button" class="tb-btn primary" onclick="modalOpen('modalBarang')">
    <svg width="15" height="15" fill="none" viewBox="0 0 24 24">
      <path d="M12 5V19M5 12H19"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"/>
    </svg>
    Tambah Barang
  </button>
</div>


</div>
<script>
function genBarcode() {
  document.getElementById('barcode').value =
    'BC' + Math.floor(Math.random()*9000000+1000000);
}
</script>

    <div class="content">
      <?php if ($msg === 'tambah'): ?>
      <div class="alert alert-success auto-close">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24">
          <path d="M22 11.08V12a10 10 0 11-5.93-9.14" stroke="currentColor" stroke-width="1.8" fill="none"/>
          <polyline points="22 4 12 14.01 9 11.01" stroke="currentColor" stroke-width="1.8" fill="none"/>
        </svg>
        <span>Barang berhasil ditambahkan!</span>
      </div>
      <?php elseif ($msg === 'edit'): ?>
      <div class="alert alert-success auto-close">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24">
          <path d="M22 11.08V12a10 10 0 11-5.93-9.14" stroke="currentColor" stroke-width="1.8" fill="none"/>
          <polyline points="22 4 12 14.01 9 11.01" stroke="currentColor" stroke-width="1.8" fill="none"/>
        </svg>
        <span>Barang berhasil diupdate!</span>
      </div>

        <?php elseif ($msg === 'hapus_error'): ?>
<div class="alert alert-warning auto-close">
  <span>Barang tidak dapat dihapus karena sudah memiliki riwayat transaksi atau stok. Silakan gunakan fitur Edit Barang untuk mengubah data.</span>
</div>

<?php elseif ($msg === 'hapus'): ?>
<div class="alert alert-success auto-close">
   <span>Barang berhasil dihapus.</span>
</div>
<?php endif; ?>

      <!-- Toolbar -->
      <form method="GET" class="toolbar barang-toolbar">
        <div class="search-box">
          <svg class="search-icon" width="14" height="14" fill="none" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="1.8"/>
            <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="1.8"/>
          </svg>
          <input type="text" name="search" placeholder="Cari nama / barcode…"
                 value="<?= htmlspecialchars($search) ?>">
        </div>
        <select name="kat" class="filter-select" onchange="this.form.submit()">
          <option value="">Semua Kategori</option>
          <?php while($k = mysqli_fetch_assoc($kategoriList)): ?>
          <option value="<?= $k['id'] ?>" <?= $kat==$k['id']?'selected':'' ?>>
         <?= htmlspecialchars($k['nama']) ?>
          </option>
          <?php endwhile; ?>
        </select>
        <button type="submit" class="tb-btn btn-cari-barang">
          Cari
        </button>
        <?php if ($search || $kat): ?>
        <a href="barang.php" class="tb-btn">Reset</a>
        <?php endif; ?>
      </form>

      <!-- Table -->
      <div class="card">
        <div class="card-header">
          <span class="card-title">Daftar Barang</span>
          <span class="card-link text-muted"><?= $total ?> barang</span>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>No</th>
                <th>Barcode</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Harga Beli</th>
                <th>Harga Jual</th>
                <th>Stok</th>
                <th>Min Stok</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $no = 1;
              while ($row = mysqli_fetch_assoc($data)):
                $stok    = (int)$row['stok'];
                $minStok = (int)$row['min_stok'];
                if ($stok === 0)         { $sCls='badge-danger';  $sLbl='Habis'; }
                elseif ($stok<=$minStok) { $sCls='badge-warning'; $sLbl='Menipis'; }
                else                     { $sCls='badge-success'; $sLbl='Normal'; }
              ?>
              <tr>
                <td class="text-muted"><?= $no++ ?></td>
                <td><span class="barcode-tag"><?= htmlspecialchars($row['barcode']) ?></span></td>
                <td class="fw-600"><?= htmlspecialchars($row['nama_barang']) ?></td>
                <td><span class="badge badge-info"><?= htmlspecialchars($row['nama_kategori']) ?></span></td>
                <td>Rp <?= number_format($row['harga_beli']) ?></td>
                <td class="fw-600">Rp <?= number_format($row['harga_jual']) ?></td>
                <td class="fw-600" style="color:<?= $stok===0?'#c62828':($stok<=$minStok?'#E65100':'#212121') ?>">
                  <?= $stok ?>
                </td>
                <td class="text-muted"><?= $minStok ?></td>
                <td><span class="badge <?= $sCls ?>"><?= $sLbl ?></span></td>
                <td>
                  <div class="action-group">
                    <a href="edit_barang.php?id=<?= $row['id'] ?>" class="act-btn" title="Edit">
                      <svg width="13" height="13" fill="none" viewBox="0 0 24 24">
                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="1.8"/>
                      </svg>
                    </a>
                  <button type="button"
                      class="act-btn act-del"
                      title="Hapus"
                      onclick="openDeleteBarangModal(
                        '<?= $row['id'] ?>',
                        '<?= htmlspecialchars($row['nama_barang'], ENT_QUOTES) ?>'
                      )">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24">
                  <polyline points="3 6 5 6 21 6" stroke="currentColor" stroke-width="1.8"/>
                  <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6M10 11v6M14 11v6"
                        stroke="currentColor" stroke-width="1.8"/>
                </svg>
              </button>
                  </div>

                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>

           <div class="modal-backdrop" id="modalBarang">
    <div class="modal modal-lg">

        <div class="modal-header">
            <h3 class="modal-title">Tambah Barang</h3>
            <button type="button"
                class="modal-close"
                onclick="closeModal('modalBarang')">×</button>
        </div>




<?php if ($error): ?>
<div class="alert alert-danger">
    <span><?= htmlspecialchars($error) ?></span>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

    <div class="modal-body">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Nama Barang <span class="required">*</span></label>
                <input class="form-input" type="text" name="nama"
                       placeholder="Cat Perfume Strawberry"
                       value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
              </div>
              <div class="form-group">
                <label class="form-label">Barcode <span class="required">*</span></label>
                <div style="display:flex;gap:8px">
                  <input class="form-input" type="text" name="barcode" id="barcode"
                         placeholder="CP001"
                         value="<?= htmlspecialchars($_POST['barcode'] ?? '') ?>" required>
                  <button type="button" class="tb-btn" onclick="genBarcode()">Generate</button>
                </div>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Kategori <span class="required">*</span></label>
                <select class="form-input select-placeholder" name="id_kategori" required>
                  <option value="" disabled selected hidden>Pilih Kategori</option>

    <?php while($k = mysqli_fetch_assoc($kategoriModal)): ?>

        <option value="<?= $k['id']; ?>"
            <?= (($_POST['id_kategori'] ?? '') == $k['id']) ? 'selected' : '' ?>>
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
    <span class="upload-title">Pilih Foto Produk</span>
    <span class="upload-info" id="fileNameProduk">JPG, PNG, WEBP • Maks. 3 MB</span>
  </label>
</div>
  </div>
  <div class="form-group"><!-- spacer --></div>
</div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Harga Beli (Rp)</label>
                <input class="form-input" type="number" name="harga_beli" min="0"
                       placeholder="15000"
                       value="<?= htmlspecialchars($_POST['harga_beli'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Harga Jual (Rp)</label>
                <input class="form-input" type="number" name="harga_jual" min="0"
                       placeholder="25000"
                       value="<?= htmlspecialchars($_POST['harga_jual'] ?? '') ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Stok Awal</label>
                <input class="form-input" type="number" name="stok" min="0"
                       placeholder="0"
                       value="<?= htmlspecialchars($_POST['stok'] ?? '0') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Minimum Stok (Reorder Point)</label>
                <input class="form-input" type="number" name="min_stok" min="0"
                       placeholder="5"
                       value="<?= htmlspecialchars($_POST['min_stok'] ?? '5') ?>">
              </div>
            </div>
            </div>
            <div class="modal-footer">
              <button type="button"
                  class="tb-btn"
                  onclick="closeModal('modalBarang')">
              Batal
          </button>
              <button type="submit" name="simpan" class="tb-btn primary">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24">
                  <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"
                        stroke="currentColor" stroke-width="1.8" fill="none"/>
                  <polyline points="17 21 17 13 7 13 7 21" stroke="currentColor" stroke-width="1.8" fill="none"/>
                  <polyline points="7 3 7 8 15 8" stroke="currentColor" stroke-width="1.8" fill="none"/>
                </svg>
                Simpan Barang
              </button>
            </div>
          </form>
    </div> <!-- modal -->
</div> <!-- modal-backdrop -->

<div class="modal-delete-backdrop" id="deleteBarangModal">
  <div class="modal-delete-box">
    <div class="modal-delete-icon">!</div>
    <h3>Hapus Barang?</h3>
    <p id="deleteBarangText">Barang akan dihapus dari data.</p>

    <form method="POST" action="hapus_barang.php" class="modal-delete-actions">
      <input type="hidden" name="id" id="deleteBarangId">

      <button type="button" class="tb-btn" onclick="closeDeleteBarangModal()">Batal</button>
      <button type="submit" class="tb-btn danger">Hapus</button>
    </form>
  </div>
</div>

</div> <!-- main -->
</div> <!-- app -->

<script>
document.querySelectorAll('.alert.auto-close').forEach(el => {
  setTimeout(() => { el.style.opacity='0'; setTimeout(()=>el.remove(),400); }, 3500);
});
</script>
<script>
document.querySelectorAll('.select-placeholder').forEach(select => {
    select.addEventListener('change', function(){
        this.classList.remove('placeholder');
    });

    if(!select.value){
        select.classList.add('placeholder');
    }
});
</script>

<script>
function openDeleteBarangModal(id, namaBarang){
  document.getElementById('deleteBarangId').value = id;
  document.getElementById('deleteBarangText').innerText =
    'Yakin ingin menghapus barang "' + namaBarang + '"?';

  document.getElementById('deleteBarangModal').classList.add('open');
}

function closeDeleteBarangModal(){
  document.getElementById('deleteBarangModal').classList.remove('open');
}
</script>

<script src="js/app.js?v=20"></script>
</body>
</html>