# Sistem Inventaris ATK – Hwaseung Indonesia

Aplikasi web internal untuk mengelola **inventaris alat tulis kantor (ATK)** di lingkungan perusahaan (contoh: Hwaseung Indonesia).

Dibangun dengan:

-   Laravel 11
-   Laravel Breeze (Blade + Tailwind CSS)
-   MySQL / MariaDB

---

## Tentang Aplikasi

Sistem ini dibuat untuk membantu:

-   Mengontrol stok ATK (masuk & keluar).
-   Mencatat peminjaman barang yang akan dikembalikan.
-   Menyediakan riwayat pergerakan stok yang jelas.
-   Mengelola user dan hak akses berdasarkan role.

---

## Fitur Utama

### 1. Autentikasi & Role User

-   Login / logout menggunakan Laravel Breeze.
-   Role user:
    -   `admin`
    -   `staff_pengelola`
-   Akses berdasarkan role:
    -   `admin`:
        -   Mengelola user.
        -   Mengelola master barang.
        -   Mengelola transaksi stok dan peminjaman.
    -   `staff_pengelola`:
        -   Mengelola master barang.
        -   Mengelola transaksi stok dan peminjaman.
        -   Tidak dapat mengelola user.

---

### 2. Master Data Barang ATK

Menu: **Master Barang**

Mencatat data master untuk setiap ATK:

-   Kode barang
-   Nama barang
-   Kategori (pulpen, kertas, map, dll.)
-   Satuan (pcs, box, rim, pak, dll.)
-   Stok awal
-   Stok terkini
-   Catatan

Catatan penting:

-   Di halaman **edit barang**, stok hanya ditampilkan (read-only).
-   Perubahan stok dilakukan melalui transaksi:
    -   Barang Masuk
    -   Barang Keluar
    -   Peminjaman / Pengembalian

---

### 3. Barang Masuk

Menu: **Barang Masuk**

Digunakan untuk mencatat **penambahan stok**.

Input:

-   Barang (dipilih dari master barang)
-   Jumlah
-   Tanggal
-   Keterangan (opsional)

Proses:

-   Stok terkini barang bertambah.
-   Transaksi dicatat di tabel riwayat stok dengan jenis **masuk**.

---

### 4. Barang Keluar

Menu: **Barang Keluar**

Digunakan untuk mencatat ATK yang **keluar dan habis pakai**.

Input:

-   Barang
-   Jumlah
-   Tanggal
-   Keterangan (misalnya untuk divisi tertentu atau keperluan tertentu)

Proses:

-   Stok terkini barang berkurang.
-   Ada validasi untuk mencegah stok menjadi negatif.
-   Transaksi dicatat di tabel riwayat stok dengan jenis **keluar**.

---

### 5. Riwayat Stok

Menu: **Riwayat Stok**

Menampilkan log pergerakan stok dari seluruh transaksi:

-   Barang masuk
-   Barang keluar
-   Peminjaman
-   Pengembalian

Informasi yang terlihat:

-   Tanggal transaksi
-   Jenis transaksi (masuk / keluar)
-   Barang (kode, nama, satuan)
-   Jumlah
-   User internal yang mencatat (jika ada)
-   Keterangan

Riwayat stok menggunakan pagination untuk memudahkan navigasi data.

---

### 6. Peminjaman ATK

Sistem mendukung peminjaman ATK yang nantinya akan dikembalikan, misalnya:

-   Gunting
-   Stapler
-   Kalkulator
-   Barang non-habis pakai lainnya

Peminjaman terbagi dua sisi:

#### 6.1. Form Peminjaman (Tanpa Login)

-   Dapat diakses tanpa login (misalnya oleh karyawan melalui jaringan internal).
-   Karyawan mengisi:
    -   Nama peminjam
    -   Departemen / divisi
    -   Barang yang ingin dipinjam
        -   Stok terkini ditampilkan.
        -   Barang dengan stok 0 ditandai habis dan tidak dapat dipilih.
    -   Jumlah yang dipinjam
    -   Tanggal pinjam
    -   Tanggal rencana kembali (opsional)
    -   Keterangan (opsional)
-   Proses sistem:
    -   Mengurangi stok terkini barang.
    -   Menyimpan data peminjaman di tabel khusus peminjaman.
    -   Mencatat transaksi di riwayat stok dengan jenis **keluar** dan keterangan peminjaman.

Form peminjaman publik dilindungi dengan pembatasan jumlah permintaan (rate limiting) untuk mengurangi risiko spam.

#### 6.2. Manajemen Peminjaman (Internal)

Diakses oleh user yang sudah login sebagai `admin` atau `staff_pengelola`.

Fitur:

-   Melihat daftar peminjaman:
    -   Kode peminjaman
    -   Nama peminjam dan departemen
    -   Barang dan jumlah
    -   Tanggal pinjam
    -   Tanggal rencana kembali
    -   Tanggal kembali (jika sudah dikembalikan)
    -   Status peminjaman (masih dipinjam / sudah dikembalikan)
-   Filter berdasarkan status (semua, masih dipinjam, sudah dikembalikan).
-   Menandai peminjaman sebagai **sudah dikembalikan**:
    -   Menambahkan stok terkini barang sesuai jumlah pinjaman.
    -   Mengubah status dan tanggal kembali di data peminjaman.
    -   Mencatat transaksi di riwayat stok dengan jenis **masuk** dan keterangan pengembalian.

---

### 7. Manajemen User (Admin Only)

Menu: **Manajemen User**

Hanya untuk user dengan role `admin`.

Fitur:

-   Melihat daftar user:
    -   Nama
    -   Email
    -   Role
-   Menambah user baru:
    -   Nama
    -   Email
    -   Role (`admin` atau `staff_pengelola`)
    -   Password + konfirmasi password
-   Mengubah user:
    -   Nama
    -   Email
    -   Role
    -   Password baru (opsional; jika tidak diisi, password lama tetap dipakai)
-   Menghapus user:
    -   Admin tidak dapat menghapus akun dirinya sendiri (untuk mencegah kehilangan akses).

---

## Teknologi yang Digunakan

-   Laravel 11
-   Laravel Breeze (Blade + Tailwind CSS)
-   MySQL / MariaDB
-   Eloquent ORM
-   Middleware autentikasi dan role-based access

---

## Persyaratan Sistem

-   PHP 8.2 atau lebih baru
-   Composer
-   MySQL / MariaDB
-   Node.js dan NPM
-   Git (opsional, untuk clone repository)

---
