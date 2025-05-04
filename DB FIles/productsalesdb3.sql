-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 04, 2025 at 12:50 AM
-- Server version: 5.7.40
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `productsalesdb3`
--
CREATE DATABASE IF NOT EXISTS `productsalesdb3` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `productsalesdb3`;

-- --------------------------------------------------------

--
-- Table structure for table `pos`
--

DROP TABLE IF EXISTS `pos`;
CREATE TABLE IF NOT EXISTS `pos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product` varchar(255) NOT NULL,
  `salesRefCode` varchar(50) NOT NULL,
  `userCode` varchar(50) NOT NULL,
  `compCode` varchar(50) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `date_sold` date NOT NULL,
  `addedby` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pos`
--

INSERT INTO `pos` (`id`, `product`, `salesRefCode`, `userCode`, `compCode`, `qty`, `price`, `total`, `date_sold`, `addedby`, `created_at`) VALUES
(1, 'External Hard Drive', 'REF-20250503-6136', '286595', '3456', 10, '129.99', '1299.90', '2025-05-03', 'admin@bizruntool.com', '2025-05-03 10:39:29'),
(2, 'Headphones', 'REF-20250503-6136', '286595', '3456', 20, '149.99', '2999.80', '2025-05-03', 'admin@bizruntool.com', '2025-05-03 10:39:29'),
(3, 'Keyboard', 'REF-20250503-6136', '286595', '3456', 30, '79.99', '2399.70', '2025-05-03', 'admin@bizruntool.com', '2025-05-03 10:39:29');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `price`) VALUES
(1, 'Laptop', '999.99'),
(2, 'Smartphone', '699.99'),
(3, 'Headphones', '149.99'),
(4, 'Monitor', '299.99'),
(5, 'Keyboard', '79.99'),
(6, 'Mouse', '39.99'),
(7, 'Tablet', '399.99'),
(8, 'Speaker', '89.99'),
(9, 'Printer', '199.99'),
(10, 'External Hard Drive', '129.99'),
(11, 'SSD Drive', '200.00');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
