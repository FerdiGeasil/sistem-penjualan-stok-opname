---

### Kode Markdown Baru untuk `README.md` Anda:

```markdown
# 🛒 Sistem Penjualan dan Stok Opname

![PHP](https://img.shields.io/badge/PHP-Native-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?logo=docker&logoColor=white)
![Status](https://img.shields.io/badge/Status-Completed-success)

Sistem Penjualan dan Stok Opname berbasis web yang dikembangkan sebagai proyek **Kerja Praktek (KP)**. Sistem ini membantu proses pengelolaan penjualan, manajemen stok, restock barang, serta stok opname secara real-time.

## 📑 Daftar Isi
- [Status Project](#-status-project)
- [Fitur Utama](#-fitur-utama)
- [Informasi Proyek](#-informasi-proyek)
- [Struktur Folder](#-struktur-folder)
- [⚙️ Cara Menjalankan Project (Pilihan)](#️-cara-menjalankan-project-pilihan)
  - [Opsi A: Menggunakan Docker (Sangat Direkomendasikan)](#opsi-a-menggunakan-docker-sangat-direkomendasikan)
  - [Opsi B: Menggunakan XAMPP (Manual)](#opsi-b-menggunakan-xampp-manual)
- [🛠 Teknologi](#-teknologi)
- [👨‍💻 Pengembang](#-pengembang)
- [📸 Preview Sistem](#-preview-sistem)

---

## 🚀 Status Project
✅ **Completed**  
Project ini dikembangkan sebagai proyek Kerja Praktek (KP) dan seluruh fitur utama telah selesai diimplementasikan.

---

## ✨ Fitur Utama

### 🔐 Autentikasi & Security
- Login Multi User (Admin & Kasir) dengan Session Guards.
- Sanitasi input data (*Anti-SQL Injection*) terpusat pada sistem helper.
- Ganti Password.

### 📦 Master Data
- CRUD Barang, Kategori, Supplier, dan Pelanggan.

### 💳 Point of Sale (POS)
- Transaksi Penjualan dengan perhitungan total & kembalian otomatis.
- Pengurangan stok otomatis (*real-time*) saat transaksi berhasil.
- Ekspor riwayat transaksi.

### 📊 Manajemen Stok & Opname
- Fitur Restock Barang & Pencatatan Log Riwayat Restock.
- Fitur **Stock Opname** berkala untuk sinkronisasi stok fisik dan sistem.
- Notifikasi otomatis jika persediaan barang mencapai batas minimum.

### 📈 Laporan
- Laporan Penjualan & Stok Barang dengan fitur **Export Excel**.

---

## 📌 Informasi Proyek
- **Nama Proyek:** Sistem Penjualan dan Stok Opname
- **Jenis:** Web Application
- **Bahasa Pemrograman:** PHP Native (Docker-Ready)
- **Database:** MySQL 8.0
- **Frontend:** HTML5, CSS3, JavaScript
- **Containerization:** Docker & Docker Compose

---

## 🗂 Struktur Folder

```

SistemPenjualan
│
├── css/
├── img/
│   └── produk/
├── js/
├── screenshots/
│
├── Dockerfile              # Konfigurasi Environment PHP & Apache Container
├── docker-compose.yml      # Orkestrasi Container (Web, MySQL, phpMyAdmin)
├── index.php               # Entry point (Auto-Redirect ke Login)
├── koneksi.php            # Manajemen Koneksi Database
├── sistem_penjualan.sql    # Database Dump File
└── [file_fitur_aplikasi].php

```

---

## ⚙️ Cara Menjalankan Project (Pilihan)

Pertama, lakukan clone repository ini ke komputer lokal Anda:
```bash
git clone [https://github.com/USERNAME/sistem-penjualan-stok-opname.git](https://github.com/USERNAME/sistem-penjualan-stok-opname.git)
cd sistem-penjualan-stok-opname

```

### Opsi A: Menggunakan Docker (Sangat Direkomendasikan)

Metode ini memastikan aplikasi berjalan secara instan tanpa perlu mengonfigurasi PHP, Apache, atau MySQL secara manual di komputer Anda.

1. **Jalankan Docker Compose:**
Pastikan Docker Desktop sudah menyala, buka terminal di folder proyek ini, lalu jalankan:
```bash
docker compose up -d

```


2. **Setup Database di phpMyAdmin:**
* Akses panel database melalui browser: `http://localhost:8081`
* Login menggunakan Username: `root` dan Password: `rahasia_portofolio`
* Buat database baru bernama `db_native_project`, lalu **Import** file `sistem_penjualan.sql`.


3. **Akses Aplikasi:**
* Buka browser Anda dan akses: **`http://localhost:8080`**



*Catatan untuk Opsi Docker: File `koneksi.php` secara default dikonfigurasi menggunakan host `"db"` agar terhubung ke jaringan kontainer.*

---

### Opsi B: Menggunakan XAMPP (Manual)

Jika Anda lebih memilih menggunakan lingkungan server lokal tradisional:

1. **Pindahkan Folder Proyek:**
Salin folder proyek ini ke direktori server lokal Anda (misal: `C:/xampp/htdocs/SistemPenjualan`).
2. **Sesuaikan File Koneksi Database:**
Buka file `koneksi.php`, sesuaikan konfigurasi parameter database ke server lokal Anda:
```php
$conn = mysqli_connect("localhost", "root", "", "sistem_penjualan");

```


3. **Import Database:**
* Buka phpMyAdmin lokal (`http://localhost/phpmyadmin`).
* Buat database baru bernama `sistem_penjualan`, lalu **Import** file `sistem_penjualan.sql`.


4. **Jalankan XAMPP Control Panel:**
* Aktifkan modul **Apache** dan **MySQL**.


5. **Akses Aplikasi:**
* Buka browser Anda dan akses: `http://localhost/SistemPenjualan/`



---

## 🛠 Teknologi

* **PHP Native**
* **MySQL 8.0**
* **HTML5 & CSS3**
* **JavaScript**
* **Docker & Docker Compose**

---

## 👨‍💻 Pengembang

**Proyek Kerja Praktek (KP)**

Program Studi Teknik Informatika

Fakultas Ilmu Komputer

**Universitas Pamulang**

---

## 📄 Lisensi

Project ini dibuat untuk keperluan pembelajaran, dokumentasi Kerja Praktek (KP), dan pengembangan portofolio profesional.

---

## 📸 Preview Sistem

*(Silakan lampirkan screenshot sistem Anda di bawah ini)*

### Login

### Dashboard

### Manajemen Barang

### Transaksi POS

### Restock Barang

### Stock Opname

### Laporan Penjualan

```

---

### Langkah untuk memperbarui di GitHub:
1. Buka file `README.md` Anda di VS Code.
2. Blok seluruh isinya, lalu *paste* kode Markdown baru di atas.
3. Simpan (`Ctrl + S`).
4. Lakukan *commit* dan *push* bersama dengan file `Dockerfile`, `docker-compose.yml`, dan `index.php` yang baru saja kita buat ke repositori GitHub Anda menggunakan Git Bash atau Terminal VS Code:
   ```bash
   git add .
   git commit -m "Feat: Add Docker configurations and update README documentation"
   git push origin main

```