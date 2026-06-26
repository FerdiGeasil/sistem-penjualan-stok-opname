# 🛒 Sistem Penjualan dan Stok Opname

![PHP](https://img.shields.io/badge/PHP-Native-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?logo=mysql&logoColor=white)
![Status](https://img.shields.io/badge/Status-Completed-success)
![License](https://img.shields.io/badge/License-Educational-blue)

Sistem Penjualan dan Stok Opname berbasis web yang dikembangkan sebagai proyek **Kerja Praktek (KP)**. Sistem ini membantu proses pengelolaan penjualan, manajemen stok, restock barang, serta stok opname secara real-time.

## 📑 Daftar Isi

- Status Project
- Fitur
- Informasi Proyek
- Cara Menjalankan
- Teknologi
- Preview Sistem
- Pengembang
- Lisensi

## 🚀 Status Project

✅ Completed

Project ini dikembangkan sebagai proyek Kerja Praktek (KP) dan seluruh fitur utama telah selesai diimplementasikan.

## 🎯 Fitur

- Multi User Login
- Dashboard Admin & Kasir
- CRUD Master Data
- Point of Sale (POS)
- Restock Barang
- Stock Opname
- Laporan Penjualan
- Export Data

---

## 📌 Informasi Proyek

- **Nama Proyek:** Sistem Penjualan dan Stok Opname
- **Jenis:** Web Application
- **Bahasa Pemrograman:** PHP Native
- **Database:** MySQL
- **Frontend:** HTML, CSS, JavaScript
- **Web Server:** Apache (XAMPP/Laragon)

---

## ✨ Fitur Utama

### 🔐 Autentikasi
- Login Multi User
- Logout
- Ganti Password

### 📦 Master Data
- CRUD Barang
- CRUD Kategori
- CRUD Supplier
- CRUD Pelanggan

### 💳 Point of Sale (POS)
- Transaksi Penjualan
- Perhitungan Total Otomatis
- Perhitungan Kembalian
- Pengurangan Stok Otomatis
- Export Data Penjualan

### 📊 Manajemen Stok
- Restock Barang
- Riwayat Restock
- Stock Opname
- Riwayat Stock Opname
- Notifikasi Stok Minimum

### 📈 Laporan
- Laporan Penjualan
- Laporan Stok Barang
- Export Laporan Excel

---

## 🗂 Struktur Folder

```
SistemPenjualan
│
├── css/
├── img/
│   └── produk/
├── js/
├── backup/
│
├── barang.php
├── dashboard.php
├── dashboard_kasir.php
├── kategori.php
├── supplier.php
├── pelanggan.php
├── pembelian.php
├── transaksi.php
├── laporan.php
├── stock_opname.php
├── riwayat_opname.php
├── restock.php
├── restock_log.php
├── login.php
├── logout.php
├── koneksi.php
└── sistem_penjualan.sql
```

---

## ⚙️ Cara Menjalankan Project

### 1. Clone Repository

```bash
git clone https://github.com/USERNAME/sistem-penjualan-stok-opname.git
```

atau download ZIP dari GitHub.

---

### 2. Pindahkan Project

Salin folder ke:

```
xampp/htdocs/
```

Contoh:

```
xampp/htdocs/SistemPenjualan
```

---

### 3. Import Database

Buka phpMyAdmin

```
New Database

↓

Buat database

↓

Import

↓

sistem_penjualan.sql
```

---

### 4. Ubah Koneksi Database

Edit file

```
koneksi.php
```

Contoh:

```php
$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "sistem_penjualan"
);
```

---

### 5. Jalankan XAMPP

Aktifkan

- Apache
- MySQL

---

### 6. Buka Browser

```
http://localhost/SistemPenjualan/
```

---

## 🛠 Teknologi

- PHP Native
- MySQL
- HTML5
- CSS3
- JavaScript
- Apache
- XAMPP

---

## 👨‍💻 Pengembang

Proyek Kerja Praktek

Universitas Pamulang

Program Studi Teknik Informatika

---

## 📄 Lisensi

Project ini dibuat untuk keperluan pembelajaran dan Kerja Praktek (KP).

## 📸 Preview Sistem

### Login

![Login](screenshots/login.png)

### Dashboard

![Dashboard](screenshots/dashboard.png)

### Barang

![Barang](screenshots/barang.png)

### Transaksi POS

![POS](screenshots/transaksi.png)

### Restock Barang

![Restock](screenshots/restock.png)

### Stock Opname

![Stock Opname](screenshots/stock-opname.png)

### Laporan

![Laporan](screenshots/laporan.png)

---

## 📌 Catatan

Default konfigurasi database menggunakan localhost dengan XAMPP.

Silakan sesuaikan konfigurasi database pada file `koneksi.php` sebelum menjalankan aplikasi.