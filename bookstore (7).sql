-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2025-12-20 09:47:29
-- 服务器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `bookstore`
--

-- --------------------------------------------------------

--
-- 表的结构 `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `photo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `email`, `password`, `photo`) VALUES
(1, 'admin1', 'leemh-wm24@student.tarc.edu.my', '6c7ca345f63f835cb353ff15bd6c5e052ec08e7a', '6945685b1ddd1.jpg');

-- --------------------------------------------------------

--
-- 表的结构 `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `cart`
--

INSERT INTO `cart` (`cart_id`, `customer_id`, `created_at`, `updated_at`) VALUES
(1, 37, '2025-12-10 21:37:46', '2025-12-20 16:35:45');

-- --------------------------------------------------------

--
-- 表的结构 `cart_item`
--

CREATE TABLE `cart_item` (
  `cart_item_id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `category_code` char(10) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `category`
--

INSERT INTO `category` (`category_id`, `category_code`, `category_name`, `sort_order`) VALUES
(1, 'NOV', 'Novel', 1),
(2, 'COM', 'Comics & Manga', 2),
(3, 'ART', 'Art Book', 3),
(4, 'FIG', 'Figure & Model', 4),
(6, 'GME', 'Game & Strategy Guide', 6),
(7, 'MAG', 'Magazine', 7),
(8, 'ACD', 'Academic & Textbook', 8),
(9, 'CHL', 'Children Book', 9),
(10, 'OTH', 'Otherss2', 10);

-- --------------------------------------------------------

--
-- 表的结构 `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `password` varchar(255) NOT NULL,
  `failed_attempt` int(11) NOT NULL DEFAULT 0,
  `last_failed_at` datetime DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `photo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `customer`
--

INSERT INTO `customer` (`customer_id`, `username`, `email`, `is_verified`, `password`, `failed_attempt`, `last_failed_at`, `phone`, `created_at`, `photo`) VALUES
(1, 'asdf', '123@gmail.com', 1, '$2y$10$DiD49ihsk.toJYHVV/mxNOrsFwC9/mdrIPgQKkewOdFvpm2w5PgwG', 0, NULL, '0123456789', '2025-11-26 09:30:34', 'default_pic.jpg'),
(2, 'afsdgasf', 'lclyjiushi@gmail.com', 1, '$2y$10$9PNrWlSvlozguI8e13UtROnUkxveuxL3/e3rkRNc5jd7AhYpxeUoi', 0, NULL, '0123456789', '2025-11-26 18:25:37', 'default_pic.jpg'),
(3, '2413492', '123456@gmail.com', 1, '$2y$10$w8nCPhbATzf9NXrjsE0gP.4rJ0ahXcZqDewnY3J90zc82Q.TRgr4y', 0, NULL, '01298765543', '2025-11-26 21:21:36', 'default_pic.jpg'),
(4, 'Bing_123', 'mokchun549@gmail.com', 1, '$2y$10$NPwrelq4Uc986r684jGNJeEBg9pM1g5sNuyZP8CYI9kBcNMwmZlOO', 0, NULL, '0164564996', '2025-11-27 14:59:36', 'default_pic.jpg'),
(5, 'mok', 'mokbingchun@gmail.com', 1, '01bc634ce656696cff9dd426464c0d21e32d5433', 0, NULL, '', '2025-12-02 12:59:51', 'default_pic.jpg'),
(6, 'mo12k', 'mokcb-wm24@student.tarc.edu.my', 1, '$2y$10$kzupMP5VEcrTWhygHfeezOUAqBiVQtXStALR9r1DSp9LiRbQV7zSq', 0, NULL, '', '2025-12-02 13:27:27', 'default_pic.jpg'),
(7, 'user01', 'user01@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 1, '2025-12-15 10:24:12', '0123456701', '2025-12-09 19:54:55', 'default_pic.jpg'),
(8, 'user02', 'user02@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456702', '2025-12-09 19:54:55', 'default_pic.jpg'),
(9, 'user03', 'user03@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456703', '2025-12-09 19:54:55', 'default_pic.jpg'),
(10, 'user04', 'user04@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456704', '2025-12-09 19:54:55', 'default_pic.jpg'),
(11, 'user05', 'user05@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456705', '2025-12-09 19:54:55', 'default_pic.jpg'),
(12, 'user06', 'user06@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456706', '2025-12-09 19:54:55', 'default_pic.jpg'),
(13, 'user07', 'user07@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456707', '2025-12-09 19:54:55', 'default_pic.jpg'),
(14, 'user08', 'user08@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456708', '2025-12-09 19:54:55', 'default_pic.jpg'),
(15, 'user09', 'user09@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456709', '2025-12-09 19:54:55', 'default_pic.jpg'),
(16, 'user10', 'user10@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456710', '2025-12-09 19:54:55', 'default_pic.jpg'),
(17, 'user11', 'user11@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456711', '2025-12-09 19:54:55', 'default_pic.jpg'),
(18, 'user12', 'user12@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456712', '2025-12-09 19:54:55', 'default_pic.jpg'),
(19, 'user13', 'user13@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456713', '2025-12-09 19:54:55', 'default_pic.jpg'),
(20, 'user14', 'user14@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456714', '2025-12-09 19:54:55', 'default_pic.jpg'),
(21, 'user15', 'user15@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456715', '2025-12-09 19:54:55', 'default_pic.jpg'),
(22, 'user16', 'user16@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456716', '2025-12-09 19:54:55', 'default_pic.jpg'),
(23, 'user17', 'user17@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456717', '2025-12-09 19:54:55', 'default_pic.jpg'),
(24, 'user18', 'user18@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456718', '2025-12-09 19:54:55', 'default_pic.jpg'),
(25, 'user19', 'user19@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456719', '2025-12-09 19:54:55', 'default_pic.jpg'),
(26, 'user20', 'user20@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456720', '2025-12-09 19:54:55', 'default_pic.jpg'),
(27, 'user21', 'user21@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456721', '2025-12-09 19:54:55', 'default_pic.jpg'),
(28, 'user22', 'user22@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456722', '2025-12-09 19:54:55', 'default_pic.jpg'),
(29, 'user23', 'user23@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456723', '2025-12-09 19:54:55', 'default_pic.jpg'),
(30, 'user24', 'user24@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456724', '2025-12-09 19:54:55', 'default_pic.jpg'),
(31, 'user25', 'user25@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456725', '2025-12-09 19:54:55', 'default_pic.jpg'),
(32, 'user26', 'user26@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456726', '2025-12-09 19:54:55', 'default_pic.jpg'),
(33, 'user27', 'user27@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456727', '2025-12-09 19:54:55', 'default_pic.jpg'),
(34, 'user28', 'user28@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456728', '2025-12-09 19:54:55', 'default_pic.jpg'),
(35, 'user29', 'user29@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456729', '2025-12-09 19:54:55', 'default_pic.jpg'),
(36, 'user30', 'user30@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456730', '2025-12-09 19:54:55', 'default_pic.jpg'),
(37, 'leemh', 'leemunhinn0409@e.newera.edu.my', 1, 'f0809fa0b952395f8d1bcc39f49dbb749e4d59fd', 0, NULL, '', '2025-12-15 10:33:05', '69465f2b2516f.jpg');

-- --------------------------------------------------------

--
-- 表的结构 `customer_address`
--

CREATE TABLE `customer_address` (
  `address_id` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `postcode` varchar(10) NOT NULL,
  `customer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `customer_address`
--

INSERT INTO `customer_address` (`address_id`, `address`, `city`, `state`, `postcode`, `customer_id`) VALUES
(1, '1, Light Street', 'George Town', 'Terengganu', '24678', 1),
(2, 'dasda', 'dsad', 'dsa', '321', 37);

-- --------------------------------------------------------

--
-- 表的结构 `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','shipping') NOT NULL DEFAULT 'pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `shipping_address` text NOT NULL,
  `shipping_city` varchar(100) NOT NULL,
  `shipping_state` varchar(100) NOT NULL,
  `shipping_postcode` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `total_amount`, `status`, `order_date`, `shipping_address`, `shipping_city`, `shipping_state`, `shipping_postcode`) VALUES
(27, 7, 71.96, 'shipping', '2025-12-12 13:48:52', '', '', '', ''),
(35, 7, 19.98, 'shipping', '2025-12-12 14:03:37', '', '', '', ''),
(36, 7, 9.99, 'completed', '2025-12-12 14:05:53', '', '', '', ''),
(38, 7, 29.00, 'pending', '2025-12-13 03:53:24', '', '', '', ''),
(39, 7, 14.50, 'completed', '2025-12-13 03:53:51', '', '', '', ''),
(40, 7, 14.50, 'shipping', '2025-12-13 03:54:33', '', '', '', ''),
(41, 7, 19.98, 'completed', '2025-12-13 03:58:56', '', '', '', ''),
(42, 7, 9.99, 'pending', '2025-12-13 04:39:18', '', '', '', ''),
(43, 7, 14.50, 'completed', '2025-12-13 05:59:24', '', '', '', ''),
(44, 7, 203.00, 'completed', '2025-12-13 07:54:06', '', '', '', ''),
(45, 7, 14.50, 'completed', '2025-12-13 07:54:56', '', '', '', ''),
(46, 7, 14.50, 'completed', '2025-12-13 07:58:27', '', '', '', ''),
(47, 7, 9.99, 'completed', '2025-12-13 07:59:23', '', '', '', ''),
(48, 7, 9.99, 'completed', '2025-12-13 08:00:49', '', '', '', ''),
(49, 7, 14.50, 'completed', '2025-12-13 08:05:07', '', '', '', ''),
(50, 7, 14.50, 'completed', '2025-12-13 08:07:03', '', '', '', ''),
(51, 7, 9.99, 'completed', '2025-12-13 08:16:47', '', '', '', ''),
(52, 7, 14.50, 'completed', '2025-12-13 08:18:41', '', '', '', ''),
(54, 7, 14.50, 'completed', '2025-12-13 08:20:56', '', '', '', ''),
(56, 7, 14.50, 'completed', '2025-12-13 08:21:13', '', '', '', ''),
(57, 7, 14.50, 'completed', '2025-12-13 08:22:15', '', '', '', ''),
(58, 7, 43.50, 'completed', '2025-12-13 08:22:29', '', '', '', ''),
(59, 7, 9.99, 'pending', '2025-12-13 08:23:50', '', '', '', ''),
(60, 7, 9.99, 'shipping', '2025-12-13 08:28:41', '', '', '', ''),
(61, 7, 45.00, 'pending', '2025-12-13 08:47:20', '', '', '', ''),
(62, 7, 14.50, 'pending', '2025-12-13 08:50:05', '', '', '', ''),
(63, 7, 9.99, 'completed', '2025-12-13 08:56:17', '', '', '', ''),
(64, 7, 14.50, 'pending', '2025-12-13 09:01:30', '', '', '', ''),
(65, 7, 189.99, 'completed', '2025-12-13 09:03:22', '', '', '', ''),
(66, 7, 9.99, 'pending', '2025-12-13 12:01:50', '', '', '', ''),
(67, 7, 9.99, 'shipping', '2025-12-13 12:03:38', '', '', '', ''),
(2001, 9, 149.80, 'completed', '2025-11-02 02:15:00', '', '', '', ''),
(2002, 37, 49.95, 'pending', '2025-12-15 02:38:19', '', '', '', ''),
(2003, 37, 9.99, 'pending', '2025-12-15 02:40:45', '', '', '', ''),
(2004, 37, 42.00, 'pending', '2025-12-15 02:42:39', '', '', '', ''),
(2005, 37, 71.92, 'pending', '2025-12-15 02:44:27', '', '', '', ''),
(2011, 37, 11.99, 'pending', '2025-12-15 03:50:32', '', '', '', ''),
(2012, 37, 45.00, 'pending', '2025-12-15 03:51:45', '', '', '', ''),
(2013, 37, 189.99, 'pending', '2025-12-15 03:52:32', '', '', '', ''),
(2014, 37, 691.50, 'pending', '2025-12-15 03:53:45', '', '', '', ''),
(2015, 37, 89.50, 'pending', '2025-12-15 03:54:41', '', '', '', ''),
(2016, 37, 148.00, 'pending', '2025-12-15 03:55:48', '', '', '', ''),
(2017, 37, 189.99, 'pending', '2025-12-15 03:57:30', '', '', '', ''),
(2019, 37, 14.50, 'completed', '2025-12-16 11:01:35', '', '', '', ''),
(2020, 37, 9.99, 'pending', '2025-12-17 11:17:23', '', '', '', ''),
(2021, 37, 45.00, 'pending', '2025-12-20 07:55:21', 'dasda', 'dsad', 'dsa', '321'),
(2022, 37, 45.00, 'pending', '2025-12-20 07:56:41', 'dasda', 'dsad', 'dsa', '321'),
(2023, 37, 1120.00, 'pending', '2025-12-20 07:57:45', 'dasda', 'dsad', 'dsa', '321'),
(2024, 37, 55.50, 'pending', '2025-12-20 08:26:29', 'dasda', 'dsad', 'dsa', '321'),
(2025, 37, 16.99, 'pending', '2025-12-20 08:33:44', 'dasda', 'dsad', 'dsa', '321'),
(2026, 37, 16.99, 'pending', '2025-12-20 08:35:50', 'dasda', 'dsad', 'dsa', '321');

-- --------------------------------------------------------

--
-- 表的结构 `order_item`
--

CREATE TABLE `order_item` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_each` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `order_item`
--

INSERT INTO `order_item` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price_each`, `subtotal`) VALUES
(2, 27, 2, 2, 14.50, 0.00),
(3, 27, 3, 3, 9.99, 0.00),
(10, 35, 3, 2, 9.99, 19.98),
(11, 36, 3, 1, 9.99, 9.99),
(13, 38, 2, 2, 14.50, 29.00),
(14, 39, 2, 1, 14.50, 14.50),
(15, 40, 2, 1, 14.50, 14.50),
(16, 41, 3, 2, 9.99, 19.98),
(17, 42, 3, 1, 9.99, 9.99),
(18, 43, 2, 1, 14.50, 14.50),
(19, 44, 2, 14, 14.50, 203.00),
(20, 45, 2, 1, 14.50, 14.50),
(21, 46, 2, 1, 14.50, 14.50),
(22, 47, 3, 1, 9.99, 9.99),
(23, 48, 3, 1, 9.99, 9.99),
(24, 49, 2, 1, 14.50, 14.50),
(25, 50, 2, 1, 14.50, 14.50),
(26, 51, 3, 1, 9.99, 9.99),
(27, 52, 2, 1, 14.50, 14.50),
(28, 54, 2, 1, 14.50, 14.50),
(29, 56, 2, 1, 14.50, 14.50),
(30, 57, 2, 1, 14.50, 14.50),
(31, 58, 2, 3, 14.50, 43.50),
(32, 59, 3, 1, 9.99, 9.99),
(33, 60, 3, 1, 9.99, 9.99),
(34, 61, 4, 1, 45.00, 45.00),
(35, 62, 2, 1, 14.50, 14.50),
(36, 63, 3, 1, 9.99, 9.99),
(37, 64, 2, 1, 14.50, 14.50),
(38, 65, 5, 1, 189.99, 189.99),
(39, 66, 3, 1, 9.99, 9.99),
(40, 67, 3, 1, 9.99, 9.99),
(41, 2002, 12, 5, 9.99, 49.95),
(42, 2003, 12, 1, 9.99, 9.99),
(43, 2004, 7, 4, 10.50, 42.00),
(44, 2005, 25, 8, 8.99, 71.92),
(45, 2011, 10, 1, 11.99, 11.99),
(46, 2012, 4, 1, 45.00, 45.00),
(47, 2013, 5, 1, 189.99, 189.99),
(48, 2014, 6, 3, 220.00, 660.00),
(49, 2014, 7, 3, 10.50, 31.50),
(50, 2015, 8, 1, 58.00, 58.00),
(51, 2015, 7, 3, 10.50, 31.50),
(52, 2016, 8, 1, 58.00, 58.00),
(53, 2016, 4, 2, 45.00, 90.00),
(54, 2017, 5, 1, 189.99, 189.99),
(56, 2019, 2, 1, 14.50, 14.50),
(57, 2020, 3, 1, 9.99, 9.99),
(58, 2021, 4, 1, 45.00, 45.00),
(59, 2022, 4, 1, 45.00, 45.00),
(60, 2023, 9, 4, 280.00, 1120.00),
(61, 2024, 4, 1, 45.00, 45.00),
(62, 2024, 7, 1, 10.50, 10.50),
(63, 2025, 18, 1, 16.99, 16.99),
(64, 2026, 18, 1, 16.99, 16.99);

-- --------------------------------------------------------

--
-- 表的结构 `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `method` enum('card','fpx','ewallet') NOT NULL,
  `status` enum('pending','success','failed') DEFAULT 'pending',
  `amount` decimal(10,2) NOT NULL,
  `paid_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `payment`
--

INSERT INTO `payment` (`payment_id`, `order_id`, `method`, `status`, `amount`, `paid_at`) VALUES
(26, 63, 'card', 'success', 9.99, '2025-12-13 08:56:17'),
(27, 64, 'ewallet', 'success', 14.50, '2025-12-13 09:01:30'),
(28, 65, 'card', 'success', 189.99, '2025-12-13 09:03:22'),
(29, 66, 'card', 'success', 9.99, '2025-12-13 12:01:50'),
(30, 67, 'card', 'success', 9.99, '2025-12-13 12:03:38'),
(31, 2002, 'ewallet', 'success', 49.95, '2025-12-15 02:38:19'),
(32, 2003, 'ewallet', 'success', 9.99, '2025-12-15 02:40:45'),
(33, 2004, 'ewallet', 'success', 42.00, '2025-12-15 02:42:39'),
(34, 2005, 'ewallet', 'success', 71.92, '2025-12-15 02:44:27'),
(35, 2011, 'ewallet', 'success', 11.99, '2025-12-15 03:50:32'),
(36, 2012, 'ewallet', 'success', 45.00, '2025-12-15 03:51:45'),
(37, 2013, 'ewallet', 'success', 189.99, '2025-12-15 03:52:32'),
(38, 2014, 'card', 'success', 691.50, '2025-12-15 03:53:45'),
(39, 2015, 'card', 'success', 89.50, '2025-12-15 03:54:41'),
(40, 2016, 'ewallet', 'success', 148.00, '2025-12-15 03:55:48'),
(41, 2017, 'ewallet', 'success', 189.99, '2025-12-15 03:57:30'),
(43, 2019, 'ewallet', 'success', 14.50, '2025-12-16 11:01:35'),
(44, 2020, 'ewallet', 'success', 9.99, '2025-12-17 11:17:23'),
(45, 2021, '', 'success', 45.00, '2025-12-20 07:55:21'),
(46, 2022, '', 'success', 45.00, '2025-12-20 07:56:41'),
(47, 2023, 'fpx', 'success', 1120.00, '2025-12-20 07:57:45'),
(48, 2024, '', 'success', 55.50, '2025-12-20 08:26:29'),
(49, 2025, '', 'success', 16.99, '2025-12-20 08:33:44'),
(50, 2026, 'ewallet', 'success', 16.99, '2025-12-20 08:35:50');

-- --------------------------------------------------------

--
-- 表的结构 `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `photo_name` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(100) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `updated_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `admin_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `product`
--

INSERT INTO `product` (`id`, `photo_name`, `title`, `author`, `category_id`, `price`, `stock`, `status`, `description`, `created_date`, `updated_date`, `admin_id`) VALUES
(2, 'product2.jpg', 'To Kill a Mockingbird', 'Harper Lee', 1, 14.50, 31, 1, 'Pulitzer Prize-winning story of racial injustice', '2025-11-30 17:14:10', '2025-12-18 13:27:36', 1),
(3, 'product3.jpg', 'One Piece Vol. 101', 'Eiichiro Oda', 2, 9.99, 87, 0, 'Latest volume of the legendary pirate adventure', '2025-11-30 17:14:10', '2025-12-19 21:10:32', 1),
(4, 'product4.jpg', 'Attack on Titan Final Season Art Book', 'Hajime Isayama', 3, 45.00, 9, 1, 'Official art collection with character designs', '2025-11-30 17:14:10', '2025-12-20 16:26:29', 1),
(5, 'product5.jpg', 'Genshin Impact Raiden Shogun Figure', 'miHoYo Official', 4, 189.99, 6, 0, '1/7 Scale PVC Figure - Limited Edition', '2025-11-30 17:14:10', '2025-12-19 18:03:45', 1),
(6, 'product6.jpg', 'Fate/Stay Night Saber Lily Figure', 'Good Smile Company', 4, 220.00, 0, 0, 'Premium 1/7 scale figure with Excalibur', '2025-11-30 17:14:10', '2025-12-19 18:04:00', 1),
(7, 'product7.jpg', 'Jujutsu Kaisen Vol. 20', 'Gege Akutami', 2, 10.50, 1, 1, 'Latest manga volume with Shibuya arc', '2025-11-30 17:14:10', '2025-12-20 16:26:29', 1),
(8, 'product8.jpg', 'Neon Genesis Evangelion Art Book', 'Yoshiyuki Sadamoto', 3, 58.00, 10, 0, 'Complete character and mecha design collection', '2025-11-30 17:14:10', '2025-12-19 18:21:54', 1),
(9, 'product9.jpg', 'Hatsune Miku 15th Anniversary Figure', 'Good Smile Company', 4, 280.00, 1, 1, 'Celebrating 15 years of virtual idol', '2025-11-30 17:14:10', '2025-12-20 15:57:45', 1),
(10, 'product10.jpg', '1984', 'George Orwell', 1, 11.99, 25, 1, 'Dystopian masterpiece about totalitarianism', '2025-11-30 17:14:10', '2025-12-16 18:36:17', 1),
(11, 'product11.jpg', 'Chainsaw Man Vol. 12', 'Tatsuki Fujimoto', 2, 11.99, 92, 1, 'Latest volume of the hit dark action series', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(12, 'product12.jpg', 'Spy x Family Vol. 10', 'Tatsuya Endo', 2, 9.99, 98, 1, 'Anya and the Forger family adventures continue', '2025-11-30 17:14:10', '2025-12-15 10:40:45', 1),
(13, 'product13.jpg', 'Demon Slayer Complete Box Set', 'Koyoharu Gotouge', 2, 159.99, 7, 0, 'All 23 volumes in premium box', '2025-11-30 17:14:10', '2025-12-19 18:21:54', 1),
(14, 'product14.jpg', 'Your Name Official Visual Book', 'Makoto Shinkai', 3, 42.00, 18, 1, 'Complete movie art and production materials', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(15, 'product15.jpg', 'Violet Evergarden Gaiden Art Book', 'Kyoto Animation', 3, 68.00, 9, 0, 'Beautiful watercolor-style illustrations', '2025-11-30 17:14:10', '2025-12-19 18:21:54', 1),
(16, 'product16.jpg', 'Asuka Langley Soryu Figure', 'Kotobukiya', 4, 168.00, 6, 0, '1/6 scale figure from Rebuild of Evangelion', '2025-11-30 17:14:10', '2025-12-19 18:21:54', 1),
(17, 'product17.jpg', 'Zero Two Darling in the Franxx Figure', 'Good Smile Company', 4, 198.00, 4, 0, 'Iconic pink-haired waifu figure', '2025-11-30 17:14:10', '2025-12-19 18:21:54', 1),
(18, 'product18.jpg', 'The Alchemist', 'Paulo Coelho', 1, 16.99, 36, 1, 'Inspirational novel about following your dreams', '2025-11-30 17:14:10', '2025-12-20 16:35:50', 1),
(19, 'product19.jpg', 'Atomic Habits', 'James Clear', 8, 24.99, 51, 1, 'Practical guide to building good habits', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(20, 'product20.jpg', 'Dune', 'Frank Herbert', 1, 18.99, 27, 1, 'Epic science fiction masterpiece', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(21, 'product21.jpg', 'Blue Lock Vol. 22', 'Muneyuki Kaneshiro', 2, 10.99, 73, 1, 'Intense soccer manga about ego and victory', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(22, 'product22.jpg', 'Tokyo Revengers Vol. 31', 'Ken Wakui', 2, 11.99, 41, 1, 'Final volume of the time-leap delinquent saga', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(23, 'product23.jpg', 'My Dress-Up Darling Marin Kitagawa Figure', 'Aniplex', 4, 248.00, 2, 1, '1/7 scale swimsuit version', '2025-11-30 17:14:10', '2025-12-19 21:43:32', 1),
(24, 'product24.jpg', 'Rem Re:Zero Figure', 'Good Smile Company', 4, 188.00, 7, 0, 'Classic maid outfit version', '2025-11-30 17:14:10', '2025-12-19 18:21:54', 1),
(25, 'product25.jpg', 'The Little Prince', 'Antoine de Saint-Exupéry', 9, 8.99, 112, 1, 'Beloved children classic with beautiful illustrations', '2025-11-30 17:14:10', '2025-12-15 10:44:27', 1),
(26, 'product26.jpg', 'Harry Potter Complete Collection', 'J.K. Rowling', 1, 89.99, 19, 1, 'All 7 books in hardcover box set', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(27, 'product27.jpg', 'One Punch Man Vol. 28', 'ONE & Yusuke Murata', 2, 10.99, 58, 1, 'Saitama vs Garou reaches its climax', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(28, 'product28.jpg', 'Bocchi the Rock! Hitori Gotoh Figure', 'Good Smile Company', 4, 178.00, 11, 1, 'Shy guitarist in her iconic pink tracksuit', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(29, 'product29.jpg', 'Kubo Won\'t Let Me Be Invisible Vol. 12', 'Nene Yukimori', 2, 9.99, 64, 1, 'Wholesome romance comedy', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(30, 'product30.jpg', 'The Art of Spirited Away', 'Studio Ghibli', 3, 55.00, 14, 1, 'Official concept art and background paintings', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(31, 'product31.jpg', 'Nier Automata 2B YoRHa Figure', 'Square Enix', 4, 298.00, 3, 0, 'Deluxe version with multiple weapons', '2025-11-30 17:14:10', '2025-12-19 18:21:54', 1),
(32, 'product32.jpg', 'Pride and Prejudice', 'Jane Austen', 1, 9.99, 42, 1, 'Timeless romantic classic', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(33, 'product33.jpg', 'Oshi no Ko Vol. 11', 'Aka Akasaka', 2, 11.99, 89, 1, 'The entertainment industry dark mystery continues', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(34, 'product34.jpg', 'Kaguya-sama Love is War Complete Set', 'Aka Akasaka', 2, 98.00, 8, 0, 'All volumes of the romantic comedy masterpiece', '2025-11-30 17:14:10', '2025-12-19 18:21:54', 1),
(35, 'product35.jpg', 'Moriarty the Patriot Art Book', 'Ryosuke Takeuchi', 3, 48.00, 16, 1, 'Victorian era Sherlock Holmes rival story', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(36, 'product36.jpg', 'Sailor Moon Eternal Edition Vol. 1-10', 'Naoko Takeuchi', 2, 189.99, 5, 0, 'Remastered complete collection', '2025-11-30 17:14:10', '2025-12-19 18:21:54', 1),
(37, 'product37.jpg', 'The Hobbit', 'J.R.R. Tolkien', 1, 15.99, 31, 1, 'The classic prelude to Lord of the Rings', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(38, 'product38.jpg', 'Steins;Gate Visual Novel Art Book', '5pb. & Nitroplus', 3, 62.00, 9, 0, 'Complete illustration collection', '2025-11-30 17:14:10', '2025-12-19 18:21:54', 1),
(39, 'product39.jpg', 'Makima Chainsaw Man Figure', 'SH Figuarts', 4, 135.00, 12, 1, 'Highly detailed and posable figure', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(40, 'product40.jpg', 'Charlotte\'s Web', 'E.B. White', 9, 7.99, 87, 1, 'Heartwarming children classic about friendship', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(41, 'product41.jpg', 'Dr. STONE Complete Manga Set', 'Riichiro Inagaki', 2, 168.00, 6, 0, 'All 26 volumes of the science adventure', '2025-11-30 17:14:10', '2025-12-19 18:21:54', 1),
(42, 'product42.jpg', 'Weathering With You Art Book', 'Makoto Shinkai', 3, 52.00, 13, 1, 'Beautiful sky and cloud illustrations', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(43, 'product43.jpg', 'Anya Forger Spy x Family Figure', 'Banpresto', 4, 38.00, 42, 1, 'Cute chibi style with peanut accessory', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(44, 'product44.jpg', 'The Catcher in the Rye', 'J.D. Salinger', 1, 13.99, 36, 1, 'Iconic coming-of-age novel', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(45, 'product45.jpg', 'Solo Leveling Vol. 8', 'Chugong', 2, 19.99, 71, 1, 'Korean web novel masterpiece', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(46, 'product46.jpg', 'Lord of the Rings Trilogy', 'J.R.R. Tolkien', 1, 48.99, 14, 1, 'Complete epic fantasy saga', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(47, 'product47.jpg', 'Howl\'s Moving Castle Art Book', 'Studio Ghibli', 3, 68.00, 11, 1, 'Magical illustrations by Hayao Miyazaki', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(48, 'product48.jpg', 'Emilia Re:Zero Crystal Dress Figure', 'Shibuya Scramble', 4, 398.00, 2, 0, 'Stunning 1/7 scale luxury figure', '2025-11-30 17:14:10', '2025-12-19 18:21:54', 1),
(49, 'product49.jpg', 'Where the Wild Things Are', 'Maurice Sendak', 9, 18.99, 53, 1, 'Beloved children picture book classic', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(50, 'product50.jpg', 'The Complete Calvin and Hobbes', 'Bill Watterson', 2, 135.00, 4, 0, 'All strips in beautiful hardcover collection', '2025-11-30 17:14:10', '2025-12-19 18:21:54', 1);

-- --------------------------------------------------------

--
-- 表的结构 `token`
--

CREATE TABLE `token` (
  `Token_Id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `token_hash` varchar(255) NOT NULL,
  `token_type` enum('verify','reset','remember') NOT NULL,
  `type` enum('otp','link') NOT NULL DEFAULT 'link',
  `otp_code` varchar(6) DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `token`
--

INSERT INTO `token` (`Token_Id`, `customer_id`, `token_hash`, `token_type`, `type`, `otp_code`, `expires_at`, `admin_id`) VALUES
(2, NULL, 'af5d6b84763b14c979764b95f13b96ca50cc7a66', 'reset', 'link', NULL, '2025-12-14 22:13:15', 1);

--
-- 转储表的索引
--

--
-- 表的索引 `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `gmail` (`email`);

--
-- 表的索引 `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD UNIQUE KEY `customer_id` (`customer_id`);

--
-- 表的索引 `cart_item`
--
ALTER TABLE `cart_item`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD UNIQUE KEY `unique_cart_product` (`cart_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- 表的索引 `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_code` (`category_code`);

--
-- 表的索引 `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`);

--
-- 表的索引 `customer_address`
--
ALTER TABLE `customer_address`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `fk_address_customer` (`customer_id`);

--
-- 表的索引 `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`) USING BTREE;

--
-- 表的索引 `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- 表的索引 `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- 表的索引 `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_title` (`title`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `fk_product_admin` (`admin_id`);

--
-- 表的索引 `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`Token_Id`),
  ADD UNIQUE KEY `unique_token` (`token_hash`,`token_type`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- 使用表AUTO_INCREMENT `cart_item`
--
ALTER TABLE `cart_item`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- 使用表AUTO_INCREMENT `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- 使用表AUTO_INCREMENT `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- 使用表AUTO_INCREMENT `customer_address`
--
ALTER TABLE `customer_address`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- 使用表AUTO_INCREMENT `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2027;

--
-- 使用表AUTO_INCREMENT `order_item`
--
ALTER TABLE `order_item`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- 使用表AUTO_INCREMENT `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- 使用表AUTO_INCREMENT `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- 使用表AUTO_INCREMENT `token`
--
ALTER TABLE `token`
  MODIFY `Token_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- 限制导出的表
--

--
-- 限制表 `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE;

--
-- 限制表 `cart_item`
--
ALTER TABLE `cart_item`
  ADD CONSTRAINT `cart_item_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`);

--
-- 限制表 `customer_address`
--
ALTER TABLE `customer_address`
  ADD CONSTRAINT `fk_address_customer` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE;

--
-- 限制表 `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_customer` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`);

--
-- 限制表 `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `fk_order_item_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_item_product` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON UPDATE CASCADE;

--
-- 限制表 `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON UPDATE CASCADE;

--
-- 限制表 `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `fk_product_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`);

--
-- 限制表 `token`
--
ALTER TABLE `token`
  ADD CONSTRAINT `fk_token_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_token_customer` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
