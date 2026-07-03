# 🛒 Sistem Penjualan dan Stok Opname

![PHP](https://img.shields.io/badge/PHP-Native-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?logo=docker&logoColor=white)
![Status](https://img.shields.io/badge/Status-Completed-success)
![License](https://img.shields.io/badge/License-Educational-blue)

Sistem Penjualan dan Stok Opname berbasis web yang dikembangkan sebagai proyek **Kerja Praktek (KP)**. Sistem ini membantu proses pengelolaan penjualan, manajemen stok, restock barang, serta stok opname secara real-time.

## 📑 Daftar Isi

- Status Project
- Fitur
- Informasi Proyek
- Struktur Folder
- Cara Menjalankan
- Teknologi
- Pengembang
- Lisensi
- Preview Sistem
- Catatan

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
- **Bahasa Pemrograman:** PHP Native (Docker-Ready)
- **Database:** MySQL
- **Frontend:** HTML, CSS, JavaScript
- **Web Server:** Apache (XAMPP/Laragon/Docker)

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
├── screenshots/
│
├── Dockerfile              # Konfigurasi Environment PHP & Apache Container
├── docker-compose.yml      # Orkestrasi Container (Web, MySQL, phpMyAdmin)
├── index.php               # Entry point (Auto-Redirect ke Login)
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

## ⚙️ Cara Menjalankan Project (Pilihan)

### Opsi A: Menggunakan Docker (Sangat Direkomendasikan)

Metode ini memastikan aplikasi berjalan secara instan tanpa perlu mengonfigurasi PHP, Apache, atau MySQL secara manual di komputer Anda.

#### 1. Clone Repository
```bash
git clone [https://github.com/FerdiGeasil/sistem-penjualan-stok-opname.git](https://github.com/FerdiGeasil/sistem-penjualan-stok-opname.git)
cd sistem-penjualan-stok-opname

```

#### 2. Jalankan Docker Compose

Pastikan Docker Desktop sudah menyala, buka terminal di folder proyek ini, lalu jalankan:

```bash
docker compose up -d

```

#### 3. Import Database

* Buka browser dan akses phpMyAdmin Docker: `http://localhost:8081`
* Login menggunakan Username: `root` dan Password: `rahasia_portofolio`
* Buat database baru bernama `db_native_project`, lalu **Import** file `sistem_penjualan.sql`.

#### 4. Akses Aplikasi

Buka browser Anda dan akses: **`http://localhost:8080`** (Akan otomatis dialihkan ke halaman login).

*Catatan untuk Opsi Docker: File `koneksi.php` secara default dikonfigurasi menggunakan host `"db"` agar terhubung ke jaringan kontainer.*

---

### Opsi B: Menggunakan XAMPP (Manual)

#### 1. Clone Repository

```bash
git clone [https://github.com/FerdiGeasil/sistem-penjualan-stok-opname.git](https://github.com/FerdiGeasil/sistem-penjualan-stok-opname.git)

```

atau download ZIP dari GitHub.

#### 2. Pindahkan Project

Salin folder ke:

```
xampp/htdocs/

```

Contoh:

```
xampp/htdocs/SistemPenjualan

```

#### 3. Import Database

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

#### 4. Ubah Koneksi Database

Edit file `koneksi.php`
Contoh:

```php
$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "sistem_penjualan"
);

```

#### 5. Jalankan XAMPP

Aktifkan:

* Apache
* MySQL

#### 6. Buka Browser

```
http://localhost/SistemPenjualan/

```

---

## 🛠 Teknologi

* PHP Native
* MySQL
* HTML5
* CSS3
* JavaScript
* Apache
* XAMPP
* Docker & Docker Compose

---

## 👨‍💻 Pengembang

Proyek Kerja Praktek

Universitas Pamulang

Program Studi Teknik Informatika

---

## 📄 Lisensi

Project ini dibuat untuk keperluan pembelajaran dan Kerja Praktek (KP).

---

## 📸 Preview Sistem

### Login

### Dashboard

### Barang

### Transaksi POS

### Restock Barang

### Stock Opname

### Laporan

---

## 📌 Catatan

Default konfigurasi database menggunakan localhost dengan XAMPP.

Silakan sesuaikan konfigurasi database pada file `koneksi.php` sebelum menjalankan aplikasi.
