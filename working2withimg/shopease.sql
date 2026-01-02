-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Dec 30, 2025 at 03:56 PM
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
-- Database: `shopease`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `email`, `created_at`) VALUES
(2, 'admin', '$2y$10$1udjQtwFGCZ9F2jOgYs8PuLYyIkYzt/jGVSd02cvofxW.6WgEd55i', 'admin@shopease.com', '2025-12-22 04:27:49');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `added_at`) VALUES
(10, 1, 8, 2, '2025-12-21 11:53:21'),
(11, 1, 3, 1, '2025-12-21 11:54:03'),
(17, 3, 10, 2, '2025-12-29 18:07:33');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `image`, `created_at`) VALUES
(1, 'Electronics', 'Latest gadgets and electronic devices', 'electronics.jpg', '2025-12-06 18:59:41'),
(2, 'Fashion', 'Trendy clothing and accessories', 'fashion.jpg', '2025-12-06 18:59:41'),
(3, 'Home & Kitchen', 'Everything for your home', 'home.jpg', '2025-12-06 18:59:41'),
(4, 'Books', 'Wide range of books and magazines', 'books.jpg', '2025-12-06 18:59:41'),
(5, 'Sports', 'Sports equipment and fitness gear', 'sports.jpg', '2025-12-06 18:59:41');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT 'Cash on Delivery',
  `status` enum('Pending','Processing','Shipped','Delivered','Cancelled') DEFAULT 'Pending',
  `shipping_address` text NOT NULL,
  `shipping_city` varchar(50) DEFAULT NULL,
  `shipping_state` varchar(50) DEFAULT NULL,
  `shipping_pincode` varchar(10) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `payment_method`, `status`, `shipping_address`, `shipping_city`, `shipping_state`, `shipping_pincode`, `order_date`) VALUES
(1, 1, 3096.00, 'Cash on Delivery', 'Delivered', 'user1address', 'bengaluru', 'karnataka', '560001', '2025-12-07 18:30:50'),
(2, 2, 5196.00, 'Cash on Delivery', 'Pending', 'user2 address', 'mysore', 'karnataka', '570001', '2025-12-07 18:35:20'),
(3, 1, 5998.00, 'Cash on Delivery', 'Delivered', 'user1address', 'bengaluru', 'karnataka', '560001', '2025-12-14 17:46:42'),
(4, 3, 17895.00, 'Cash on Delivery', 'Pending', 'user3  address', 'hospet', 'karnataka', '583204', '2025-12-22 04:41:49'),
(5, 3, 12795.00, 'Cash on Delivery', 'Delivered', 'user3  address', 'hospet', 'karnataka', '583204', '2025-12-22 09:21:54');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 7, 3, 899.00),
(2, 1, 8, 1, 399.00),
(3, 2, 9, 2, 799.00),
(4, 2, 3, 2, 1799.00),
(5, 3, 1, 2, 2999.00),
(6, 4, 5, 2, 2199.00),
(7, 4, 2, 3, 4499.00),
(8, 5, 1, 4, 2999.00),
(9, 5, 9, 1, 799.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `stock`, `image`, `featured`, `created_at`) VALUES
(1, 1, 'Wireless Headphones', 'Premium noise-cancelling headphones with 30-hour battery life', 2999.00, 44, 'product_6953dd56eca5b.png', 1, '2025-12-06 18:59:41'),
(2, 1, 'Smart Watch', 'Fitness tracker with heart rate monitor and GPS', 4499.00, 27, 'product_6953dda9b51e8.png', 1, '2025-12-06 18:59:41'),
(3, 1, 'Bluetooth Speaker', 'Portable speaker with crystal clear sound', 1799.00, 38, 'product_6953de0c9ad80.png', 1, '2025-12-06 18:59:41'),
(4, 2, 'Denim Jacket', 'Classic blue denim jacket for all seasons', 1299.00, 25, 'product_6953de676026c.png', 1, '2025-12-06 18:59:41'),
(5, 5, 'Running Shoes', 'Comfortable sports shoes for daily wear', 2199.00, 58, 'product_6953deb33e916.png', 1, '2025-12-06 18:59:41'),
(6, 3, 'Non-Stick Cookware Set', '5-piece cookware set with glass lids', 3499.00, 20, 'product_6953def87a60e.png', 0, '2025-12-06 18:59:41'),
(7, 3, 'Electric Kettle', 'Fast boiling 1.8L electric kettle', 899.00, 42, 'product_6953df2fe0f98.png', 0, '2025-12-06 18:59:41'),
(8, 4, 'IKIGAI', 'The Japanese Secret to a Long Happy Life', 399.00, 99, 'product_6953dfc31edbf.png', 1, '2025-12-06 18:59:41'),
(9, 5, 'Yoga Mat', 'Eco-friendly non-slip yoga mat', 799.00, 32, 'product_6953e02feb0fa.png', 1, '2025-12-06 18:59:41'),
(10, 2, 'Puma Flip-Flops', 'Mens slides', 1280.00, 20, 'product_6953dca1bd403.png', 1, '2025-12-29 18:07:12'),
(11, 3, 'Air-Fryer', 'Air-Fryer', 2500.00, 10, 'product_6953da65609c6.png', 1, '2025-12-29 18:17:08'),
(12, 4, 'The Monk Who Sold His Ferrari', 'A great novel written by Robin Sharma', 249.00, 20, 'product_6953e07e5ee14.png', 1, '2025-12-30 13:48:23');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `city`, `state`, `pincode`, `created_at`) VALUES
(1, 'user1', 'user1@gmail.com', '$2y$10$jprAK56DLyN0EFmFjJdi6eiASiP./Y8hO6LVbr/K3U8dtGZ8WIrgS', '9111111111', 'user1address', 'bengaluru', 'karnataka', '560001', '2025-12-06 19:16:52'),
(2, 'user2', 'user2@gmail.com', '$2y$10$8AcSnLahrbCVsPKGB/1mIuOiOQcg1uCM027vEpvCn2KJZ2iXuAhyG', '9222222222', 'user2 address', 'mysore', 'karnataka', '570001', '2025-12-07 18:33:52'),
(3, 'user3', 'user3@gmail.com', '$2y$10$ngX0ujArSxkvQbBkhcTJr.H7nyvfqmrrR2y5LxjX9x9DOkJkKRfBq', '9333333333', 'user3  address', 'hospet', 'karnataka', '583204', '2025-12-22 04:40:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
