-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 03, 2024 at 03:00 AM
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
(3, 'Buy 3 get 2', 'buy_n_get_m', 2.00, 3, 'school', '2024-05-01', '2024-07-31');

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
  `free_products` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Orders`
--

INSERT INTO `Orders` (`order_id`, `user_id`, `order_date`, `product_ids`, `quantities`, `prices`, `total_price`, `free_products`) VALUES
(9, 1, '2024-06-02 14:04:34', '3', '3', '1000.00', 3300.00, '2'),
(10, 1, '2024-06-02 14:04:50', '3', '3', '1000.00', 1000.00, '2'),
(11, 1, '2024-06-02 14:07:30', '1,3', '1,4', '2300.00,1000.00', 3300.00, '0,0'),
(12, 1, '2024-06-02 16:01:59', '3', '1', '1000.00', 1000.00, '0'),
(13, 1, '2024-06-02 18:13:45', '3', '1', '1000.00', 1000.00, '0');

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
(1, 'Venturini', 'Premium Quality Formal Shoe', 2300.00, 'shoe,summer', 192),
(2, 'Earth Shoes', 'Elegant looking best for regular use.', 1100.00, 'regular_use', 98),
(3, 'School Shoes', 'White shoes for school going students.', 1000.00, 'school,shoe', 140);

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
(2, 'Admin', 'admin@gmail.com', '$2y$10$rvhbAB5D4XPRJqXNQm428uTWWI2TNOYJ7uxf6kKbceFCAaD8qYSWO', 1);

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
  MODIFY `discount_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Orders`
--
ALTER TABLE `Orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `Products`
--
ALTER TABLE `Products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Stock_Adjustments`
--
ALTER TABLE `Stock_Adjustments`
  MODIFY `stock_adjustment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
