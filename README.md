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

```text
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
