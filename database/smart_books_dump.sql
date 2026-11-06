-- SMART BOOKS MySQL Database Dump
-- Compatible with MySQL 5.7+ / 8.0+ / MariaDB 10.3+

CREATE DATABASE IF NOT EXISTS `smart_books` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `smart_books`;

-- Tabel Users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','kasir','pelanggan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pelanggan',
  `avatar_url` text COLLATE utf8mb4_unicode_ci,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Distributors
CREATE TABLE IF NOT EXISTS `distributors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `telepon` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Bukus
CREATE TABLE IF NOT EXISTS `bukus` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
  `isbn` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `penulis` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `penerbit` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun_terbit` year NOT NULL DEFAULT '2024',
  `harga_pokok` bigint NOT NULL,
  `harga_jual` bigint NOT NULL,
  `diskon` int NOT NULL DEFAULT '0',
  `stok` int NOT NULL DEFAULT '0',
  `lokasi_rak` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_url` text COLLATE utf8mb4_unicode_ci,
  `status` enum('publikasi','draf') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'publikasi',
  `sinopsis` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bukus_kategori_index` (`kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Pasoks
CREATE TABLE IF NOT EXISTS `pasoks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `distributor_id` bigint unsigned NOT NULL,
  `buku_id` bigint unsigned NOT NULL,
  `jumlah` int NOT NULL,
  `tanggal` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_pasok_distributor` FOREIGN KEY (`distributor_id`) REFERENCES `distributors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pasok_buku` FOREIGN KEY (`buku_id`) REFERENCES `bukus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Penjualans
CREATE TABLE IF NOT EXISTS `penjualans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_transaksi` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
  `user_id` bigint unsigned NOT NULL,
  `subtotal` bigint NOT NULL,
  `ppn` bigint NOT NULL DEFAULT '0',
  `diskon` bigint NOT NULL DEFAULT '0',
  `total_bayar` bigint NOT NULL,
  `metode_pembayaran` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Tunai',
  `bayar` bigint NOT NULL DEFAULT '0',
  `kembali` bigint NOT NULL DEFAULT '0',
  `status` enum('Completed','Pending','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Completed',
  `tipe` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pos',
  `alamat_pengiriman` text COLLATE utf8mb4_unicode_ci,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `tanggal` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_penjualan_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Detail Penjualans
CREATE TABLE IF NOT EXISTS `detail_penjualans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `penjualan_id` bigint unsigned NOT NULL,
  `buku_id` bigint unsigned NOT NULL,
  `jumlah` int NOT NULL,
  `harga_satuan` bigint NOT NULL,
  `subtotal` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_detail_penjualan` FOREIGN KEY (`penjualan_id`) REFERENCES `penjualans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_detail_buku` FOREIGN KEY (`buku_id`) REFERENCES `bukus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data Awal User (password: admin123 / kasir123 / pelanggan123)
INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Admin Utama', 'admin_user', 'admin@smartbooks.com', '$2y$12$g7T.qm90K1mOHSWPz6xLZetI9eTCGP6GrAc9CRqSwfBWWa0O.nCmm', 'admin', NOW(), NOW()),
(2, 'Siti Aminah', 'kasir_user', 'kasir@smartbooks.com', '$2y$12$qHz4xItM1YU4RARnXP8vpOmLfxBerlf9dGluIywzT/ZWl2R0yKXeu', 'kasir', NOW(), NOW()),
(3, 'Dewi Lestari', 'pelanggan_user', 'dewi@example.com', '$2y$12$nKNC6U16QT1K516N4ByOAedC8KOswvBUnLRtSobaR.drd1kb9ZKX.', 'pelanggan', NOW(), NOW());
