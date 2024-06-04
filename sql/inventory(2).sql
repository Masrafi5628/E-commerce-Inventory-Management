-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 04, 2024 at 07:36 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `Discounts`
--

CREATE TABLE `Discounts` (
  `discount_id` int(11) NOT NULL,
  `discount_name` varchar(100) NOT NULL,
  `discount_type` enum('percentage','buy_n_get_m') NOT NULL,
  `discount_value` decimal(5,2) NOT NULL,
  `buy_quantity` int(11) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Discounts`
--

INSERT INTO `Discounts` (`discount_id`, `discount_name`, `discount_type`, `discount_value`, `buy_quantity`, `tags`, `start_date`, `end_date`) VALUES
(1, 'Summer Sale', 'percentage', 20.50, NULL, 'summer', '2024-05-01', '2024-07-31'),
(3, 'Buy 3 get 2', 'buy_n_get_m', 2.00, 3, 'school', '2024-05-01', '2024-07-31'),
(4, 'Buy 1 Get 1', 'buy_n_get_m', 1.00, 1, 'b1g1', '2024-06-01', '2024-08-31'),
(5, 'Buy 5 Get 1', 'buy_n_get_m', 1.00, 5, 'b5g1', '2024-05-22', '2024-08-29');

-- --------------------------------------------------------

--
-- Table structure for table `Orders`
--

CREATE TABLE `Orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `product_ids` varchar(255) NOT NULL,
  `quantities` varchar(255) NOT NULL,
  `prices` varchar(255) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `free_products` varchar(255) DEFAULT NULL,
  `buy_offer` varchar(255) DEFAULT NULL,
  `get_offer` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Orders`
--

INSERT INTO `Orders` (`order_id`, `user_id`, `order_date`, `product_ids`, `quantities`, `prices`, `total_price`, `free_products`, `buy_offer`, `get_offer`) VALUES
(9, 1, '2024-06-02 14:04:34', '3', '3', '1000.00', 3300.00, '2', NULL, NULL),
(10, 1, '2024-06-02 14:04:50', '3', '3', '1000.00', 1000.00, '2', NULL, NULL),
(11, 1, '2024-06-02 14:07:30', '1,3', '1,4', '2300.00,1000.00', 3300.00, '0,0', NULL, NULL),
(12, 1, '2024-06-02 16:01:59', '3', '1', '1000.00', 1000.00, '0', NULL, NULL),
(13, 1, '2024-06-02 18:13:45', '3', '1', '1000.00', 1000.00, '0', NULL, NULL),
(14, 1, '2024-06-03 01:16:34', '3,2', '9,1', '1000.00,1100.00', 4400.00, '', '3,null,null', '2.00,20.50%,null'),
(15, 1, '2024-06-03 01:22:19', '3,1', '1,3', '1000.00,2300.00', 3300.00, '0,0', '3,null', '2.00,20.50%'),
(16, 1, '2024-06-03 01:23:19', '1', '3', '2300.00', 2300.00, NULL, 'null', '20.50%'),
(17, 1, '2024-06-03 01:25:37', '1', '1', '2300.00', 3300.00, '0', 'null', '20.50%'),
(18, 1, '2024-06-03 01:48:47', '3', '2', '1000.00', 1000.00, '0', '3', '2.00'),
(19, 1, '2024-06-03 02:57:43', '4,3,1', '2,2,3', '1200.00,1000.00,2300.00', 4500.00, '2,0', '1,3,null', '1.00,2.00,20.50%'),
(20, 1, '2024-06-03 03:01:34', '4,3', '1,2', '1200.00,1000.00', 2200.00, '1,0', '1,3', '1.00,2.00'),
(21, 1, '2024-06-03 03:03:24', '3', '2', '1000.00', 2200.00, '0', '3', '2.00'),
(22, 1, '2024-06-03 03:09:33', '', '', '', 2200.00, '', '', ''),
(23, 1, '2024-06-03 03:15:14', '3', '1', '1000.00', 1000.00, '0', '3', '2.00'),
(24, 1, '2024-06-03 03:18:34', '4', '7', '1200.00', 1200.00, '7', '1', '1.00'),
(25, 1, '2024-06-03 03:21:49', '4', '2', '1200.00', 1200.00, '2', '1', '1.00'),
(26, 1, '2024-06-03 03:26:20', '4', '5', '1200.00', 1200.00, NULL, '1', '1.00'),
(27, 1, '2024-06-03 03:26:40', '4,3', '6,5', '1200.00,1000.00', 2200.00, ',2', '1,3', '1.00,2.00'),
(28, 1, '2024-06-03 03:33:13', '4', '4', '1200.00', 1200.00, '4', '1', '1.00'),
(29, 3, '2024-06-03 04:58:36', '1,4,3', '3,4,5', '2300.00,1200.00,1000.00', 4500.00, '0,4,2', 'null,1,3', '20.50%,1.00,2.00'),
(30, 1, '2024-06-03 05:02:10', '4,3,1', '4,5,4', '1200.00,1000.00,2300.00', 4500.00, '4,2,0', '1,3,null', '1.00,2.00,20.50%'),
(31, 1, '2024-06-03 05:03:54', '3', '3', '1000.00', 1000.00, '2', '3', '2.00'),
(32, 1, '2024-06-03 05:04:08', '4', '3', '1200.00', 1200.00, '3', '1', '1.00'),
(33, 1, '2024-06-03 05:09:11', '4', '3', '1200.00', 1200.00, '3', '1', '1.00'),
(34, 1, '2024-06-03 05:09:41', '4', '3', '1200.00', 1200.00, '3', '1', '1.00'),
(35, 1, '2024-06-03 05:12:26', '4', '2', '1200.00', 1200.00, '2', '1', '1.00'),
(36, 1, '2024-06-03 05:12:57', '3,4', '1,1', '1000.00,1200.00', 2200.00, '0,1', '3,1', '2.00,1.00'),
(37, 2, '2024-06-03 05:42:39', '', '', '', 3000.00, '', '', ''),
(38, 1, '2024-06-03 05:45:51', '5', '3', '3000.00', 3000.00, '0', '5', '1.00'),
(39, 4, '2024-06-03 12:51:30', '1', '3', '2300.00', 2300.00, '0', 'null', '20.50%'),
(40, 1, '2024-06-04 04:20:16', '5,1,4', '3,4,4', '3000.00,2300.00,1200.00', 6500.00, '0,0,4', '5,null,1', '1.00,20.50%,1.00');

-- --------------------------------------------------------

--
-- Table structure for table `Products`
--

CREATE TABLE `Products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Products`
--

INSERT INTO `Products` (`product_id`, `name`, `description`, `price`, `tags`, `stock`) VALUES
(1, 'Venturini', 'Premium Quality Formal Shoe', 2300.00, 'shoe,summer', 168),
(2, 'Earth Shoes', 'Elegant looking best for regular use.', 1100.00, 'regular_use', 97),
(3, 'School Shoes', 'White shoes for school going students.', 1000.00, 'school,shoe', 82),
(4, 'Sandals', 'Good for regular and casual use.', 1200.00, 'regular_use,b1g1', 111),
(5, 'Army Boot', 'This product is very good for military personnel.', 3000.00, 'army,b5g1', 294);

-- --------------------------------------------------------

--
-- Table structure for table `Stock_Adjustments`
--

CREATE TABLE `Stock_Adjustments` (
  `stock_adjustment_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `adjustment_type` enum('addition','subtraction') NOT NULL,
  `quantity` int(11) NOT NULL,
  `adjustment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`user_id`, `name`, `email`, `password`, `is_admin`) VALUES
(1, 'Masrafi', 'masrafi@gmail.com', '$2y$10$hfeRZh2xmPZyIqX.hEvv7.a80TMvQnNemAuwVpEpPYahqiO8KPUQa', 0),
(2, 'Admin', 'admin@gmail.com', '$2y$10$rvhbAB5D4XPRJqXNQm428uTWWI2TNOYJ7uxf6kKbceFCAaD8qYSWO', 1),
(3, 'New User', 'new.user@gmail.com', '$2y$10$emh4SqHzLGvzC9eqrAWG/.ynxvQLaIfOa0pqeCc0K8oovmCZo8dEy', 0),
(4, 'hassan', 'has@gmail.con', '$2y$10$0Zw.LEz.0dDiqjzKrluHxOmYZ5FpQU40W./VU2fydxY18lBVMOqwW', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Discounts`
--
ALTER TABLE `Discounts`
  ADD PRIMARY KEY (`discount_id`);

--
-- Indexes for table `Orders`
--
ALTER TABLE `Orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `Products`
--
ALTER TABLE `Products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `Stock_Adjustments`
--
ALTER TABLE `Stock_Adjustments`
  ADD PRIMARY KEY (`stock_adjustment_id`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Discounts`
--
ALTER TABLE `Discounts`
  MODIFY `discount_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Orders`
--
ALTER TABLE `Orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `Products`
--
ALTER TABLE `Products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Stock_Adjustments`
--
ALTER TABLE `Stock_Adjustments`
  MODIFY `stock_adjustment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
