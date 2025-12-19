-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2025 at 03:52 PM
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
-- Database: `bookstore`
--

-- --------------------------------------------------------

--
-- Table structure for table `order_item`
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
-- Dumping data for table `order_item`
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
(55, 2018, 10, 3, 11.99, 35.97),
(56, 2019, 2, 1, 14.50, 14.50),
(57, 2020, 3, 1, 9.99, 9.99);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `order_item`
--
ALTER TABLE `order_item`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
