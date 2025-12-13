-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2025-12-13 13:48:38
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
-- 表的结构 `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `address_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed') DEFAULT 'pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `address_id`, `total_amount`, `status`, `order_date`) VALUES
(27, 37, 5, 71.96, '', '2025-12-12 13:48:52'),
(35, 37, 5, 19.98, '', '2025-12-12 14:03:37'),
(36, 37, 5, 9.99, '', '2025-12-12 14:05:53'),
(38, 37, 6, 29.00, '', '2025-12-13 03:53:24'),
(39, 37, 6, 14.50, '', '2025-12-13 03:53:51'),
(40, 37, 6, 14.50, '', '2025-12-13 03:54:33'),
(41, 37, 6, 19.98, '', '2025-12-13 03:58:56'),
(42, 37, 6, 9.99, '', '2025-12-13 04:39:18'),
(43, 37, 6, 14.50, '', '2025-12-13 05:59:24'),
(44, 37, 5, 203.00, '', '2025-12-13 07:54:06'),
(45, 37, 6, 14.50, '', '2025-12-13 07:54:56'),
(46, 37, 6, 14.50, '', '2025-12-13 07:58:27'),
(47, 37, 6, 9.99, '', '2025-12-13 07:59:23'),
(48, 37, 5, 9.99, '', '2025-12-13 08:00:49'),
(49, 37, 6, 14.50, '', '2025-12-13 08:05:07'),
(50, 37, 6, 14.50, '', '2025-12-13 08:07:03'),
(51, 37, 6, 9.99, '', '2025-12-13 08:16:47'),
(52, 37, 6, 14.50, '', '2025-12-13 08:18:41'),
(54, 37, 6, 14.50, '', '2025-12-13 08:20:56'),
(56, 37, 6, 14.50, '', '2025-12-13 08:21:13'),
(57, 37, 6, 14.50, '', '2025-12-13 08:22:15'),
(58, 37, 6, 43.50, '', '2025-12-13 08:22:29'),
(59, 37, 6, 9.99, '', '2025-12-13 08:23:50'),
(60, 37, 6, 9.99, '', '2025-12-13 08:28:41'),
(61, 37, 6, 45.00, 'pending', '2025-12-13 08:47:20'),
(62, 37, 6, 14.50, 'pending', '2025-12-13 08:50:05'),
(63, 37, 6, 9.99, 'pending', '2025-12-13 08:56:17'),
(64, 37, 6, 14.50, 'pending', '2025-12-13 09:01:30'),
(65, 37, 6, 189.99, 'pending', '2025-12-13 09:03:22'),
(66, 37, 5, 9.99, 'pending', '2025-12-13 12:01:50'),
(67, 37, 6, 9.99, 'pending', '2025-12-13 12:03:38');

--
-- 转储表的索引
--

--
-- 表的索引 `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `address_id` (`address_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- 限制导出的表
--

--
-- 限制表 `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `customer_address` (`address_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
