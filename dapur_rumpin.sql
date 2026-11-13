-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2026 at 04:47 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dapur_rumpin`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `id` int(10) UNSIGNED NOT NULL,
  `pesanan_id` int(10) UNSIGNED NOT NULL,
  `menu_id` int(10) UNSIGNED DEFAULT NULL,
  `nama_menu` varchar(150) NOT NULL,
  `harga_saat_pesan` int(10) UNSIGNED NOT NULL,
  `qty` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `subtotal` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`id`, `pesanan_id`, `menu_id`, `nama_menu`, `harga_saat_pesan`, `qty`, `subtotal`) VALUES
(1, 1, 4, 'Gurame Asam Manis', 98000, 1, 98000),
(2, 2, 3, 'Nasi Pecel Jowo', 38000, 1, 38000),
(3, 3, 22, 'Chicken Blackpapper', 26000, 1, 26000),
(4, 4, 22, 'Chicken Blackpapper', 26000, 1, 26000),
(5, 4, 14, 'Ca Kangkung', 10000, 1, 10000),
(6, 5, 14, 'Ca Kangkung', 10000, 1, 10000),
(7, 6, 13, 'Ca Tauge', 10000, 1, 10000),
(8, 7, 14, 'Ca Kangkung', 10000, 1, 10000),
(9, 8, 13, 'Ca Tauge', 10000, 1, 10000),
(10, 9, 13, 'Ca Tauge', 10000, 1, 10000),
(11, 10, 13, 'Ca Tauge', 10000, 1, 10000),
(12, 11, 13, 'Ca Tauge', 10000, 1, 10000),
(13, 12, 13, 'Ca Tauge', 10000, 1, 10000),
(14, 13, 13, 'Ca Tauge', 10000, 1, 10000),
(15, 14, 22, 'Chicken Blackpapper', 26000, 1, 26000),
(16, 14, 24, 'Cumi Balado', 26000, 1, 26000),
(17, 14, 43, 'Ice Cream', 12000, 1, 12000),
(18, 14, 32, 'Es Kuwut Melon', 15000, 1, 15000),
(19, 15, 16, 'Gurame Bakar Madu', 98000, 1, 98000),
(20, 16, 22, 'Chicken Blackpapper', 26000, 1, 26000),
(21, 16, 21, 'Chicken Teriyaki', 26000, 1, 26000),
(22, 16, 7, 'Cumi Asam Manis', 26000, 1, 26000),
(23, 17, 14, 'Ca Kangkung', 10000, 1, 10000),
(24, 17, 13, 'Ca Tauge', 10000, 1, 10000),
(25, 18, 35, 'Strawberry Squash', 18000, 1, 18000),
(26, 19, 35, 'Strawberry Squash', 18000, 1, 18000),
(27, 20, 22, 'Chicken Blackpapper', 26000, 1, 26000),
(28, 20, 31, 'Iced Green Tea', 14000, 1, 14000),
(29, 21, 11, 'Pecel Sayur', 10000, 1, 10000);

-- --------------------------------------------------------

--
-- Table structure for table `meja`
--

CREATE TABLE `meja` (
  `id` int(10) UNSIGNED NOT NULL,
  `nomor_meja` varchar(10) NOT NULL,
  `nama_meja` varchar(100) DEFAULT NULL,
  `qr_token` varchar(64) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meja`
--

INSERT INTO `meja` (`id`, `nomor_meja`, `nama_meja`, `qr_token`, `is_active`, `created_at`) VALUES
(1, '1', 'Meja 1 - Teras Depan', '230b6149713f11f192bc0a0027000006', 1, '2026-06-26 09:12:19'),
(2, '2', 'Meja 2 - Teras Depan', '230b6e07713f11f192bc0a0027000006', 1, '2026-06-26 09:12:19'),
(3, '3', 'Meja 3 - Ruang Utama', '230b70e7713f11f192bc0a0027000006', 1, '2026-06-26 09:12:19'),
(4, '4', 'Meja 4 - Ruang Utama', '230b7221713f11f192bc0a0027000006', 1, '2026-06-26 09:12:19'),
(5, '5', 'Meja 5 - Ruang VIP', '230b7340713f11f192bc0a0027000006', 1, '2026-06-26 09:12:19'),
(6, '6', 'Meja 6 - Ruang VIP', '230b745b713f11f192bc0a0027000006', 1, '2026-06-26 09:12:19');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama` varchar(150) NOT NULL,
  `kategori` enum('makanan','cemilan','minuman') NOT NULL,
  `harga` int(10) UNSIGNED NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `badge` varchar(50) DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `nama`, `kategori`, `harga`, `deskripsi`, `badge`, `gambar`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Nasi Timbel Sunda', 'makanan', 42000, 'Paket nasi timbel dengan lauk ayam goreng, sambel lalapan dan tahu tempe.', 'Favorit', 'img/menu/menu_1781100689_14dae673.webp', 1, '2026-06-10 14:10:01', '2026-06-11 04:39:56'),
(2, 'Nasir Goreng Sereh', 'makanan', 24000, 'Nama lain Lemongrass Fried Rice, olahan nasi goreng spesial dengan serai yang harum dan menggugah selera.', 'Spesial', 'img/menu/menu_1781100800_d66e3b9d.webp', 1, '2026-06-10 14:13:20', '2026-06-11 04:40:01'),
(3, 'Nasi Pecel Jowo', 'makanan', 38000, 'Sajian Khas Jawa dengan paket nasi lauk ayam dan sayuran masak saus kacang yang gurih.', 'Khas Jawa', 'img/menu/menu_1781100862_fc5ac07c.webp', 1, '2026-06-10 14:14:22', '2026-06-11 04:39:50'),
(4, 'Gurame Asam Manis', 'makanan', 98000, 'Olahan ikan tawar asli Indonesia dengan sauce asam manis. Terdapat juga varian Bakar Madu dan Goreng.', 'Premium', 'img/menu/menu_1781100937_3ea66c93.webp', 1, '2026-06-10 14:15:37', '2026-06-11 04:39:42'),
(5, 'Udang Asam Manis', 'makanan', 30000, 'Olahan udang dengan sauce asam manis yang memanjakan lidah. Terdapat varian goreng tepung.', 'Seafood', 'img/menu/menu_1781152107_dba7f9ee.jpg', 1, '2026-06-11 04:27:57', '2026-06-11 04:28:27'),
(6, 'Mie Goreng GTR', 'makanan', 22000, 'Sajian mie goreng spesial dengan bumbu khas Dapur Rumpin, lezat dan mengenyangkan.', 'Favorit', 'img/menu/menu_1781152177_62f9b5a3.webp', 1, '2026-06-11 04:29:37', '2026-06-11 04:29:37'),
(7, 'Cumi Asam Manis', 'makanan', 26000, 'Olahan seafood cumi dengan sauce asam manis. Ada varian goreng tepung.', 'Seafood', 'img/menu/menu_1781152232_8fe64150.webp', 1, '2026-06-11 04:30:32', '2026-06-11 04:30:32'),
(8, 'Pecak Ikan Mas', 'makanan', 26000, 'Sajian khas olahan ikan Mas dengan bumbu pecak yang kaya rempah dan aromatik.', 'Tradisional', 'img/menu/menu_1781152273_3137dc34.webp', 1, '2026-06-11 04:31:13', '2026-06-11 04:31:13'),
(9, 'Nila Bakar + Sambal', 'makanan', 25000, 'Hidangan ikan bakar dengan nila pilihan dan sambal segar. Ada varian goreng juga.', 'Bakar', 'img/menu/menu_1781152398_4557f3d5.webp', 1, '2026-06-11 04:33:18', '2026-06-11 04:33:18'),
(10, 'Sayur Asam', 'makanan', 12000, 'Khas Indonesia banget. Dengan variant macam sayur seperti jagung manis, asam, melinjo dll.', 'Sehat', 'img/menu/menu_1781152573_97723734.webp', 1, '2026-06-11 04:34:02', '2026-06-11 04:36:13'),
(11, 'Pecel Sayur', 'makanan', 10000, 'Varian sayuran rebus khas Jawa dengan sambal bumbu kacang yang gurih dan lezat.', 'Vegetarian', 'img/menu/menu_1781152636_349e7c05.webp', 1, '2026-06-11 04:37:16', '2026-06-11 04:37:16'),
(12, 'Karedok', 'makanan', 10000, 'Salad sayuran mentah khas Sunda dengan bumbu kacang segar yang pedas dan segar.', 'Segar', 'img/menu/menu_1781152678_03390976.webp', 1, '2026-06-11 04:37:58', '2026-06-11 04:37:58'),
(13, 'Ca Tauge', 'makanan', 10000, 'Tauge segar ditumis dengan bumbu bawang dan cabai, sederhana namun lezat.', 'Sederhana', 'img/menu/menu_1781152715_b16fc8e1.webp', 1, '2026-06-11 04:38:35', '2026-06-11 04:38:35'),
(14, 'Ca Kangkung', 'makanan', 10000, 'Kangkung segar ditumis dengan bumbu terasi dan cabai, renyah dan menggugah selera.', 'Sederhana', 'img/menu/menu_1781152760_c226bf24.webp', 1, '2026-06-11 04:39:00', '2026-06-11 04:39:20'),
(15, 'Sambal', 'makanan', 5000, 'Pilihan sambal segar berbagai varian: tomat, dadak, terasi, dan kecap untuk pelengkap hidangan.', 'Pedas', 'img/menu/menu_1781162062_fdffbe13.jpg', 1, '2026-06-11 04:41:10', '2026-06-11 07:14:22'),
(16, 'Gurame Bakar Madu', 'makanan', 98000, 'Ikan gurame pilihan dibakar dengan olesan madu dan rempah spesial hingga harum kecoklatan.', 'Premium', 'img/menu/menu_1781161621_4ec04a0e.jpg', 1, '2026-06-11 04:42:24', '2026-06-11 07:07:01'),
(17, 'Gurame Goreng', 'makanan', 94000, 'Ikan gurame segar digoreng garing hingga renyah di luar namun tetap lembut di dalam.', 'Premium', 'img/menu/menu_1781161717_d8c89f4e.jpg', 1, '2026-06-11 04:42:59', '2026-06-11 07:08:37'),
(18, 'Nila Goreng', 'makanan', 25000, 'Ikan nila segar digoreng hingga garing dengan bumbu rempah pilihan yang gurih.', 'Gurih', 'img/menu/menu_1781161793_7768e4fd.jpg', 1, '2026-06-11 04:45:53', '2026-06-11 07:09:53'),
(19, 'Pecak Nila', 'makanan', 27000, 'Ikan nila pilihan dimasak dengan bumbu pecak khas yang kaya rempah dan pedas.', 'Pedas', 'img/menu/menu_1781162090_a379be46.jpg', 1, '2026-06-11 04:46:26', '2026-06-11 07:14:50'),
(20, 'Tahu & Tempe Goreng', 'makanan', 6000, 'Tahu dan tempe pilihan digoreng garing dengan bumbu sederhana, cocok sebagai lauk pelengkap.', 'Sederhana', 'img/menu/menu_1781162195_59cc747a.jpg', 1, '2026-06-11 04:47:22', '2026-06-11 07:16:35'),
(21, 'Chicken Teriyaki', 'makanan', 26000, 'Nasi dengan topping ayam fillet dengan sauce teriyaki manis dan gurih yang lezat.', 'Ricebowl', 'img/menu/menu_1781153332_fde37f1d.webp', 1, '2026-06-11 04:48:52', '2026-06-11 04:48:52'),
(22, 'Chicken Blackpapper', 'makanan', 26000, 'Nasi dengan topping ayam fillet sauce blackpepper spesial yang pedas dan gurih.', 'Ricebowl', 'img/menu/menu_1781153387_5c151763.webp', 1, '2026-06-11 04:49:47', '2026-06-11 04:49:47'),
(23, 'Korean Spicy Chicken', 'makanan', 26000, 'Nasi dengan topping olahan daging ayam slice sauce pedas ala Korean yang menggugah selera.', 'Ricebowl', 'img/menu/menu_1781153474_9465a648.webp', 1, '2026-06-11 04:51:14', '2026-06-11 04:51:14'),
(24, 'Cumi Balado', 'makanan', 26000, 'Nasi dengan topping baby cumi masak sauce balado pedas yang menggugah selera.', 'Ricebowl', 'img/menu/menu_1781153512_3e80dbbe.webp', 1, '2026-06-11 04:51:52', '2026-06-11 04:51:52'),
(25, 'Risol Mayo', 'cemilan', 14000, 'Risoles goreng dengan isian telur dengan mayonaise di dalamnya, crispy dan creamy.', 'Cemilan', 'img/menu/menu_1781153594_37890684.webp', 1, '2026-06-11 04:53:14', '2026-06-11 04:53:14'),
(26, 'French Fries', 'cemilan', 10000, 'Snack dengan bahan dasar kentang yang diiris tipis dan digoreng crunchy sempurna.', 'Cemilan', 'img/menu/menu_1781153632_5d6a4c08.webp', 1, '2026-06-11 04:53:52', '2026-06-11 04:53:52'),
(27, 'Potato Wedges', 'cemilan', 17000, 'Kentang potong baji digoreng atau dipanggang, pas disajikan dengan saus spesial.', 'Cemilan', 'img/menu/menu_1781153670_9614812a.webp', 1, '2026-06-11 04:54:30', '2026-06-11 04:54:30'),
(28, 'Rujak Cireng', 'cemilan', 10000, 'Jajanan aci digoreng kering dengan sauce rujak untuk cocolan yang pedas dan segar.', 'Cemilan', 'img/menu/menu_1781153703_21b0accf.webp', 1, '2026-06-11 04:55:03', '2026-06-11 04:55:03'),
(29, 'Tahu Baso', 'cemilan', 10000, 'Cemilan enak khas Semarang – tahu isi baso yang digoreng garing dan lezat.', 'Cemilan', 'img/menu/menu_1781153733_b926472a.webp', 1, '2026-06-11 04:55:33', '2026-06-11 05:12:13'),
(30, 'Iced Lhcyee Tea', 'minuman', 15000, 'Sajian varian minuman segar dari teh dan leci yang menyegarkan di siang hari.', 'Segar', 'img/menu/menu_1781153868_040acc44.webp', 1, '2026-06-11 04:57:48', '2026-06-11 04:57:48'),
(31, 'Iced Green Tea', 'minuman', 14000, 'Minuman dari teh hijau yang segar dengan tambahan creamer yang creamy.', 'Segar', 'img/menu/menu_1781153899_f1e1b7c1.webp', 1, '2026-06-11 04:58:19', '2026-06-11 04:58:19'),
(32, 'Es Kuwut Melon', 'minuman', 15000, 'Minuman segar dari varian buah melon dan timun yang diserut, sangat menyegarkan.', 'Segar', 'img/menu/menu_1781153937_dc671d0f.webp', 1, '2026-06-11 04:58:57', '2026-06-11 04:58:57'),
(33, 'Icced Lemon tea', 'minuman', 10000, 'Minuman teh dingin dengan tambahan lemon segar yang menyegarkan dan ringan.', 'Segar', 'img/menu/menu_1781153983_5e1975dc.webp', 1, '2026-06-11 04:59:43', '2026-06-11 04:59:43'),
(34, 'Lemongrass Squash', 'minuman', 17000, 'Minuman mix antara serai dan lemon ditambah kesegaran soda yang menyegarkan.', 'Soda', 'img/menu/menu_1781154159_1f307689.webp', 1, '2026-06-11 05:02:39', '2026-06-11 05:02:39'),
(35, 'Strawberry Squash', 'minuman', 18000, 'Minuman dominan strawberry ditambah dengan kesegaran lemon dan soda.', '', 'img/menu/menu_1781154190_56d1cd13.webp', 1, '2026-06-11 05:03:10', '2026-06-11 05:03:10'),
(36, 'Juice Melon', 'minuman', 12000, 'Kesegaran buah melon sangat terasa saat penghilang dahaga yang menyehatkan.', 'Jus', 'img/menu/menu_1781154464_57b9fffe.webp', 1, '2026-06-11 05:07:44', '2026-06-11 05:07:44'),
(37, 'Juice Tomat', 'minuman', 12000, 'Minuman segar dari tomat asli yang menyehatkan dan menyegarkan alami.', 'Jus', 'img/menu/menu_1781154525_e1407940.webp', 1, '2026-06-11 05:08:45', '2026-06-11 05:08:45'),
(38, 'Jus Alpukat', 'minuman', 16000, 'Buah favorit juices dengan lelehan cream coklat yang lezat dan kaya nutrisi.', 'Jus', 'img/menu/menu_1781154577_ca4b3763.webp', 1, '2026-06-11 05:09:37', '2026-06-11 05:09:37'),
(39, 'Milkshake Strawberry', 'minuman', 18000, 'Minuman shake susu dan ice cream dengan rasa strawberry, extra topping ice cream strawberry.', 'Milkshake', 'img/menu/menu_1781154611_d38b914e.webp', 1, '2026-06-11 05:10:11', '2026-06-11 05:10:11'),
(40, 'Milkshake Chocolate', 'minuman', 18000, 'Minuman shake susu dan ice cream dengan rasa coklat, extra topping ice cream coklat.', 'Milkshake', 'img/menu/menu_1781154646_96af0063.webp', 1, '2026-06-11 05:10:46', '2026-06-11 05:10:46'),
(41, 'Milkshake Vanilla', 'minuman', 18000, 'Minuman shake susu dan ice cream dengan rasa vanilla yang lembut dan creamy.', 'Milkshake', 'img/menu/menu_1781154676_bb4792e7.webp', 1, '2026-06-11 05:11:16', '2026-06-11 05:11:16'),
(42, 'Juice Orange', 'minuman', 12000, 'Dengan jeruk pilihan dan segar yang banyak mengandung vitamin C.', 'Jus', 'img/menu/menu_1781154817_ba9a856f.webp', 1, '2026-06-11 05:13:37', '2026-06-11 05:13:37'),
(43, 'Ice Cream', 'minuman', 12000, 'Ice cream berbagai macam rasa dengan topping buah segar yang menyegarkan dan lezat.', 'Dessert', 'img/menu/menu_1781155052_3613f6d8.webp', 1, '2026-06-11 05:17:32', '2026-06-11 05:17:32');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id` int(10) UNSIGNED NOT NULL,
  `kode_pesanan` varchar(30) NOT NULL,
  `nama_pemesan` varchar(100) NOT NULL,
  `nomor_meja` varchar(20) NOT NULL,
  `total_harga` int(10) UNSIGNED NOT NULL,
  `payment_method` enum('qris','tunai') DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','diproses','selesai','dibatalkan') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id`, `kode_pesanan`, `nama_pemesan`, `nomor_meja`, `total_harga`, `payment_method`, `paid_at`, `status`, `created_at`, `updated_at`) VALUES
(1, 'DR-20260611-0001', 'budi', '6', 98000, NULL, NULL, 'dibatalkan', '2026-06-11 03:24:18', '2026-06-11 03:36:49'),
(2, 'DR-20260611-0002', 'santo', '2', 38000, NULL, NULL, 'selesai', '2026-06-11 03:37:16', '2026-06-11 03:37:53'),
(3, 'DR-20260611-0003', 'tes', '1', 26000, NULL, NULL, 'selesai', '2026-06-11 08:30:38', '2026-06-11 12:01:28'),
(4, 'DR-20260611-0004', 'tes', '1', 36000, NULL, NULL, 'selesai', '2026-06-11 08:46:31', '2026-06-11 12:01:35'),
(5, 'DR-20260611-0005', 'tes', '1', 10000, NULL, NULL, 'selesai', '2026-06-11 08:47:19', '2026-06-11 12:01:31'),
(6, 'DR-20260611-0006', 'tes1', '1', 10000, NULL, NULL, 'dibatalkan', '2026-06-11 08:49:09', '2026-06-11 12:01:17'),
(7, 'DR-20260611-0007', 'budi', '1', 10000, NULL, NULL, 'dibatalkan', '2026-06-11 09:53:16', '2026-06-11 12:01:14'),
(8, 'DR-20260611-0008', 'cek pesanan', '5', 10000, NULL, NULL, 'dibatalkan', '2026-06-11 09:55:58', '2026-06-11 12:01:07'),
(9, 'DR-20260611-0009', 'cek pesanan', '1', 10000, NULL, NULL, 'dibatalkan', '2026-06-11 10:13:51', '2026-06-11 12:01:05'),
(10, 'DR-20260611-0010', 'indonesia', '1', 10000, NULL, NULL, 'selesai', '2026-06-11 10:15:20', '2026-06-11 10:18:22'),
(11, 'DR-20260611-0011', 'contoh', '5', 10000, NULL, NULL, 'selesai', '2026-06-11 10:16:37', '2026-06-11 10:18:29'),
(12, 'DR-20260611-0012', 'budi', '2', 10000, NULL, NULL, 'pending', '2026-06-11 12:29:17', '2026-06-11 12:29:17'),
(13, 'DR-20260611-0013', 'budi', '2', 10000, NULL, NULL, 'pending', '2026-06-11 12:30:37', '2026-06-11 12:30:37'),
(14, 'DR-20260614-0001', 'Akbar', '4', 79000, NULL, NULL, 'pending', '2026-06-14 06:09:02', '2026-06-14 06:09:02'),
(15, 'DR-20260626-0001', 'busi', '5', 98000, NULL, NULL, 'diproses', '2026-06-26 08:31:56', '2026-06-26 08:33:55'),
(16, 'DR-20260626-0002', 'tes', '1', 78000, NULL, NULL, 'pending', '2026-06-26 09:18:56', '2026-06-26 09:18:56'),
(17, 'DR-20260626-0003', 'andin', '3', 20000, NULL, NULL, 'pending', '2026-06-26 09:24:43', '2026-06-26 09:24:43'),
(18, 'DR-20260626-0004', 'andin', '3', 18000, NULL, NULL, 'pending', '2026-06-26 09:27:10', '2026-06-26 09:27:10'),
(19, 'DR-20260626-0005', 'andin', '3', 18000, 'qris', '2026-06-26 09:41:14', 'selesai', '2026-06-26 09:33:05', '2026-06-26 09:41:14'),
(20, 'DR-20260626-0006', 'anto', '6', 40000, NULL, NULL, 'diproses', '2026-06-26 10:21:21', '2026-06-26 10:21:51'),
(21, 'DR-20260626-0007', 'tes', '1', 10000, 'qris', '2026-06-26 10:45:34', 'selesai', '2026-06-26 10:27:44', '2026-06-26 10:45:34');

-- --------------------------------------------------------

--
-- Table structure for table `qr_logs`
--

CREATE TABLE `qr_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `nomor_meja` varchar(10) NOT NULL,
  `status` enum('valid','invalid') NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `scanned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `qr_logs`
--

INSERT INTO `qr_logs` (`id`, `nomor_meja`, `status`, `ip_address`, `user_agent`, `scanned_at`) VALUES
(1, '1', 'valid', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-26 09:14:39'),
(2, '1', 'valid', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-26 09:16:30'),
(3, '1', 'valid', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-26 09:18:44'),
(4, '1', 'valid', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-26 09:18:45'),
(5, '1', 'valid', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-26 09:18:45'),
(6, '1', 'valid', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-26 09:18:46'),
(7, '1', 'valid', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-26 09:18:46'),
(8, '3', 'valid', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-26 09:24:20'),
(9, '3', 'valid', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-26 09:26:58'),
(10, '3', 'valid', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-26 09:27:47'),
(11, '3', 'valid', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-26 09:32:52'),
(12, '6', 'valid', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-26 10:20:57'),
(13, '1', 'valid', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-26 10:27:30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(60) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','owner','kasir','dapur') NOT NULL DEFAULT 'kasir',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'Administrator', 'owner', '$2y$10$OXZhXUdxpw264wHhnXadI.P7PsUkq4zhYFanORC7megQz0flZ7E0m', 'owner', '2026-06-10 06:24:38'),
(2, 'admin', 'admin 2', 'admin', 'owner', '2026-06-10 06:59:57'),
(4, 'budi', 'kasir', '$2y$10$OXZhXUdxpw264wHhnXadI.P7PsUkq4zhYFanORC7megQz0flZ7E0m', 'kasir', '2026-06-10 13:51:43'),
(5, 'Staff Dapur', 'dapur', '$2y$10$OXZhXUdxpw264wHhnXadI.P7PsUkq4zhYFanORC7megQz0flZ7E0m', 'dapur', '2026-06-26 09:08:59'),
(6, 'Administrator', 'admin', '$2y$10$JY.xNJq0qv5vGkRdbwhVl.IZHYz6hpfAXuBgLpQ2/I7psFzC6Csfe', 'admin', '2026-06-26 10:01:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_detail_pesanan` (`pesanan_id`),
  ADD KEY `fk_detail_menu` (`menu_id`);

--
-- Indexes for table `meja`
--
ALTER TABLE `meja`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_nomor_meja` (`nomor_meja`),
  ADD UNIQUE KEY `uq_qr_token` (`qr_token`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_pesanan` (`kode_pesanan`);

--
-- Indexes for table `qr_logs`
--
ALTER TABLE `qr_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `meja`
--
ALTER TABLE `meja`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `qr_logs`
--
ALTER TABLE `qr_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `fk_detail_menu` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_detail_pesanan` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
