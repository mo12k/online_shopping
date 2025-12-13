-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2025-12-13 13:48:41
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
(1, 27, 1, 1, 12.99, 0.00),
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
(40, 67, 3, 1, 9.99, 9.99);

--
-- 转储表的索引
--

--
-- 表的索引 `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `order_item`
--
ALTER TABLE `order_item`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- 限制导出的表
--

--
-- 限制表 `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `order_item_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
