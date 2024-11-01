-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 01, 2024 at 06:46 PM
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
-- Database: `kizuno`
--

-- --------------------------------------------------------

--
-- Table structure for table `cooks`
--

CREATE TABLE `cooks` (
  `cook_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `specialty` varchar(255) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `total_reviews` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cooks`
--

INSERT INTO `cooks` (`cook_id`, `user_id`, `bio`, `specialty`, `rating`, `total_reviews`) VALUES
(1, 3, NULL, NULL, 0.00, 0),
(2, 4, 'hello im pathum', 'Chinese', 0.00, 0),
(4, 8, NULL, NULL, 0.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `user_id`, `address`) VALUES
(1, 1, NULL),
(2, 7, NULL),
(3, 22, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `delivery_personnel`
--

CREATE TABLE `delivery_personnel` (
  `driver_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vehicle_type` varchar(50) DEFAULT NULL,
  `vehicle_number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery_personnel`
--

INSERT INTO `delivery_personnel` (`driver_id`, `user_id`, `vehicle_type`, `vehicle_number`) VALUES
(1, 27, 'Bike', 'PK-4568');

-- --------------------------------------------------------

--
-- Table structure for table `meals`
--

CREATE TABLE `meals` (
  `meal_id` int(11) NOT NULL,
  `cook_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `available_date` date NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meals`
--

INSERT INTO `meals` (`meal_id`, `cook_id`, `name`, `description`, `price`, `available_date`, `image_url`, `created_at`) VALUES
(1, 4, 'fried rice', 'egg fried rice', 250.00, '2024-10-10', NULL, '2024-10-09 05:45:30'),
(3, 4, 'chicken rice', 'yellow rice with chicken curry', 400.00, '2024-10-11', NULL, '2024-10-11 05:45:30'),
(9, 4, 'polos baiya', 'asdf', 250.00, '2024-10-15', '1728887263_1_sDOCS6W0SxsNRS5KlQoYgQ.png', '2024-10-14 06:27:43'),
(10, 4, 'asdf', 'asdf', 999.99, '2024-10-14', NULL, '2024-10-13 05:40:55'),
(11, 1, 'spagetti', 'chcicken spagetti', 500.00, '2024-10-16', NULL, '2024-10-15 06:21:24'),
(12, 4, 'chicken noodles', 'Delicious chicken noodles', 400.00, '2024-11-01', '1730351806_Chicken-noodles.jpg', '2024-10-31 05:16:46'),
(13, 2, 'Pork noodles', 'Delicious pork noodles', 500.00, '2024-11-01', '1730351806_Chicken-noodles.jpg', '2024-10-31 06:16:46'),
(14, 2, 'Vegetarian Salad', 'Delicious vegetarian salad with avocado and many other vegies', 400.00, '2024-11-02', '1730437126_salad.jpg', '2024-11-01 04:58:46');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `delivery_address` varchar(255) DEFAULT NULL,
  `status` enum('pending','accepted','delivered','cancelled') DEFAULT 'pending',
  `driver_id` int(11) DEFAULT NULL,
  `driver_status` enum('unassigned','accepted','delivered') DEFAULT 'unassigned'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `total_amount`, `order_date`, `delivery_address`, `status`, `driver_id`, `driver_status`) VALUES
(1, 2, 550.00, '2024-10-14 18:30:00', '123 Main St, Cityville', 'delivered', 1, 'delivered'),
(2, 2, 250.00, '2024-10-13 18:30:00', '456 Oak St, Townsville', 'cancelled', NULL, 'unassigned'),
(3, 2, 400.00, '2024-10-31 06:36:09', '29/ s, mandawila road, piliyandala', 'pending', NULL, 'unassigned'),
(4, 2, 1000.00, '2024-10-31 07:03:30', '29/ s, mandawila road, piliyandala', 'pending', NULL, 'unassigned'),
(5, 2, 1200.00, '2024-10-31 18:30:00', '29/ s, mandawila road, piliyandala', 'pending', NULL, 'unassigned'),
(6, 2, 1500.00, '2024-10-31 18:30:00', '29/ s, mandawila road, piliyandala', 'pending', NULL, 'unassigned'),
(7, 2, 2000.00, '2024-10-31 18:30:00', '29/ s, mandawila road, piliyandala', 'pending', NULL, 'unassigned'),
(8, 2, 400.00, '2024-10-31 18:30:00', '29/ s, mandawila road, piliyandala', 'accepted', 1, 'accepted'),
(9, 2, 1000.00, '2024-11-01 18:30:00', '29/ s, mandawila road, piliyandala', 'delivered', 1, 'delivered');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `meal_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `meal_id`, `quantity`, `price`) VALUES
(1, 1, 9, 1, 0.00),
(2, 2, 10, 1, 0.00),
(3, 3, 12, 1, 0.00),
(4, 4, 13, 2, 500.00),
(5, 5, 12, 3, 400.00),
(6, 6, 13, 3, 500.00),
(7, 7, 13, 4, 500.00),
(8, 8, 12, 1, 400.00),
(9, 9, 14, 2, 400.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `payment_method` enum('credit_card','paypal','cash_on_delivery') DEFAULT NULL,
  `payment_status` enum('completed','pending','failed') DEFAULT 'pending',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `meal_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `review_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `subscription_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `subscription_plan` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('active','expired','cancelled') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `user_type` enum('cook','customer','driver') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `phone_number`, `password_hash`, `user_type`, `created_at`) VALUES
(1, 'Michelle Nikeetha Perera', 'michellenikeetha@gmail.com', '0718760054', '$2y$10$ZYoBtl9/CM/cDFQDI6G5rOSiL9y9O4du4BJoqvCDlKTLZ4KqI/rDa', 'customer', '2024-10-11 17:54:04'),
(3, 'Roshelle Nishita Perera', 'r@gmail.com', '0714324016', '$2y$10$1mbM3nn7TRc9k6GxXpwkveioavX3xmttAnYJQTbjhwbuvJ3ak5hCa', 'cook', '2024-10-11 17:57:13'),
(4, 'Pathum Lakshan', 'p@gmail.com', '0771234112', '$2y$10$PMiOYblwyzpFXzH3naW4HOpeeeJvcGF94arKzjHgCsojV/vpE7CBS', 'cook', '2024-10-11 17:58:24'),
(7, 'udara nishani', 'n@gmail.com', '0718760054', '$2y$10$qESBAdh9TCZd.z4G3oePHuz5IrNrhpmy1u3TkSP6XBaYWFTrwmUje', 'customer', '2024-10-11 18:35:03'),
(8, 'udara nishani', 'u@gmail.com', '0718760054', '$2y$10$GwX59TsjTxLgpiZWkphLBuNM0rXgw5rEZoV5JxHpupKxwWKxyaCsO', 'cook', '2024-10-11 18:36:09'),
(22, 'nikeetha perera', 'np@gmail.com', '0718760054', '$2y$10$QlUF0Gnohp6FoWRP.ez6TuPhvt5n7VKbxZWzo3uCjtEDkax2kUMqC', 'customer', '2024-10-14 07:17:39'),
(27, 'Maleesha Perera', 'mp@gmail.com', '0765301376', '$2y$10$tRkvSSgDDPmFp5HvBYxGDePLGVrf4ZJt2p6V4TcXZ2EFTkXLfVFqe', 'driver', '2024-10-29 17:57:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cooks`
--
ALTER TABLE `cooks`
  ADD PRIMARY KEY (`cook_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `delivery_personnel`
--
ALTER TABLE `delivery_personnel`
  ADD PRIMARY KEY (`driver_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `meals`
--
ALTER TABLE `meals`
  ADD PRIMARY KEY (`meal_id`),
  ADD KEY `cook_id` (`cook_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `meal_id` (`meal_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `meal_id` (`meal_id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`subscription_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cooks`
--
ALTER TABLE `cooks`
  MODIFY `cook_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `delivery_personnel`
--
ALTER TABLE `delivery_personnel`
  MODIFY `driver_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `meals`
--
ALTER TABLE `meals`
  MODIFY `meal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `subscription_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cooks`
--
ALTER TABLE `cooks`
  ADD CONSTRAINT `cooks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `delivery_personnel`
--
ALTER TABLE `delivery_personnel`
  ADD CONSTRAINT `delivery_personnel_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `meals`
--
ALTER TABLE `meals`
  ADD CONSTRAINT `meals_ibfk_1` FOREIGN KEY (`cook_id`) REFERENCES `cooks` (`cook_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`driver_id`) REFERENCES `delivery_personnel` (`driver_id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`meal_id`) REFERENCES `meals` (`meal_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`meal_id`) REFERENCES `meals` (`meal_id`) ON DELETE CASCADE;

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
