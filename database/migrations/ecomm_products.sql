-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Nov 14, 2025 at 01:29 AM
-- Server version: 8.0.35
-- PHP Version: 8.2.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecommerce`
--

-- --------------------------------------------------------

--
-- Table structure for table `ecomm_products`
--

CREATE TABLE `ecomm_products` (
  `id` int NOT NULL,
  `prodname` varchar(255) NOT NULL,
  `prodprice` decimal(5,2) NOT NULL,
  `proddesc` text NOT NULL,
  `options` text NOT NULL,
  `catid` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ecomm_products`
--

INSERT INTO `ecomm_products` (`id`, `prodname`, `prodprice`, `proddesc`, `options`, `catid`) VALUES
(1, 'Triple Lindy', 12.99, '', '  {\r\n        \"bread\": [\r\n            \"Italian\",\r\n            \"Rye\",\r\n            \"Wheat\"\r\n        ]\r\n    },\r\n    {\r\n        \"cheese\": [\r\n            \"swiss\",\r\n            \"american\",\r\n            \"cheddar\"\r\n        ]\r\n    }\r\n', 1),
(2, 'Secor\'s Reuben', 14.99, 'Great stuff...makes you go bald', '[\r\n    {\r\n        \"bread\": \"[\'Italian\',\'Rye\',\'Wheat\']\"\r\n    },\r\n    {\r\n        \"cheese\": \"[\'swiss\',\'american\',\'cheddar\']\"\r\n    }\r\n]', 1),
(3, 'Meatloaf', 15.99, 'It\'s great', '[\r\n    {\r\n        \"bread\": \"[\'Italian\',\'Rye\',\'Wheat\']\"\r\n    },\r\n    {\r\n        \"cheese\": \"[\'swiss\',\'american\',\'cheddar\']\"\r\n    }\r\n]', 2),
(4, 'Chevy Bolt', 12.99, 'Great EV 300 Mile Range. Doesn\'t cause too many fires.', 'EV', 1),
(5, 'Chevy Volt', 20.00, '', 'Hybrid', 1),
(6, 'Chevy Camaro', 14.00, '', 'Gas', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ecomm_products`
--
ALTER TABLE `ecomm_products`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ecomm_products`
--
ALTER TABLE `ecomm_products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
