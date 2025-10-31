-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 31, 2025 at 05:44 AM
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
-- Database: `aquaflow`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `current_stock` int(11) NOT NULL DEFAULT 0,
  `minimum_stock_level` int(11) NOT NULL DEFAULT 10,
  `last_restocked` date DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `product_id`, `current_stock`, `minimum_stock_level`, `last_restocked`, `last_updated`) VALUES
(1, 1, 500, 100, NULL, '2025-10-31 01:24:42'),
(2, 2, 400, 80, NULL, '2025-10-31 01:24:42'),
(3, 3, 300, 60, NULL, '2025-10-31 01:24:42'),
(4, 4, 200, 40, NULL, '2025-10-31 01:24:42'),
(5, 5, 200, 40, NULL, '2025-10-31 01:24:42'),
(6, 6, 150, 30, NULL, '2025-10-31 01:24:42'),
(7, 7, 100, 20, NULL, '2025-10-31 01:24:42'),
(8, 8, 80, 15, NULL, '2025-10-31 01:24:42'),
(9, 9, 300, 50, NULL, '2025-10-31 01:24:43'),
(10, 10, 250, 50, NULL, '2025-10-31 01:24:43'),
(11, 11, 120, 30, NULL, '2025-10-31 01:24:43'),
(12, 12, 600, 100, NULL, '2025-10-31 01:24:43'),
(13, 13, 200, 40, NULL, '2025-10-31 01:24:43'),
(14, 14, 220, 40, NULL, '2025-10-31 01:24:43'),
(15, 15, 180, 30, NULL, '2025-10-31 01:24:44'),
(16, 16, 60, 10, NULL, '2025-10-31 01:24:44'),
(17, 17, 80, 10, NULL, '2025-10-31 01:24:44'),
(18, 18, 30, 5, NULL, '2025-10-31 01:24:44');

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

CREATE TABLE `materials` (
  `id` int(11) NOT NULL,
  `material_name` varchar(100) NOT NULL,
  `unit` varchar(20) NOT NULL,
  `current_stock` decimal(10,2) NOT NULL DEFAULT 0.00,
  `reorder_level` decimal(10,2) NOT NULL,
  `unit_cost` decimal(10,2) DEFAULT NULL,
  `supplier` varchar(100) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `materials`
--

INSERT INTO `materials` (`id`, `material_name`, `unit`, `current_stock`, `reorder_level`, `unit_cost`, `supplier`, `last_updated`) VALUES
(1, 'Purified Water', 'Liters', 10000.00, 2000.00, 0.50, 'Water Treatment Plant', '2025-10-31 01:24:42'),
(2, 'PET Bottles 500ml', 'Pieces', 5000.00, 1000.00, 5.00, 'Bottle Suppliers Ltd', '2025-10-31 01:24:42'),
(3, 'PET Bottles 750ml', 'Pieces', 3000.00, 800.00, 7.00, 'Bottle Suppliers Ltd', '2025-10-31 01:24:42'),
(4, 'PET Bottles 1.5L', 'Pieces', 2000.00, 500.00, 10.00, 'Bottle Suppliers Ltd', '2025-10-31 01:24:42'),
(5, 'Bottle Caps', 'Pieces', 8000.00, 1500.00, 2.00, 'Cap Industries', '2025-10-31 01:24:42'),
(6, 'Labels', 'Pieces', 6000.00, 1200.00, 1.50, 'Printing Services', '2025-10-31 01:24:42'),
(7, 'Orange Concentrate', 'Liters', 500.00, 100.00, 150.00, 'Fruit Suppliers Co', '2025-10-31 01:24:42'),
(8, 'Apple Concentrate', 'Liters', 500.00, 100.00, 150.00, 'Fruit Suppliers Co', '2025-10-31 01:24:42'),
(9, 'Sugar', 'Kilograms', 1000.00, 200.00, 80.00, 'Sugar Mills', '2025-10-31 01:24:42'),
(10, 'Preservatives', 'Kilograms', 100.00, 20.00, 500.00, 'Chemical Suppliers', '2025-10-31 01:24:42');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','warning','success','error') NOT NULL DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `delivery_address` text NOT NULL,
  `delivery_city` varchar(50) DEFAULT NULL,
  `delivery_state` varchar(50) DEFAULT NULL,
  `delivery_postal_code` varchar(10) DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `special_instructions` text DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `delivery_fee` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','out_for_delivery','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','paid','refunded') NOT NULL DEFAULT 'unpaid',
  `assigned_to` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `customer_id`, `order_date`, `delivery_address`, `delivery_city`, `delivery_state`, `delivery_postal_code`, `delivery_date`, `special_instructions`, `subtotal`, `delivery_fee`, `total_amount`, `status`, `payment_status`, `assigned_to`, `created_at`, `updated_at`) VALUES
(5, 'AF1761876884517', 4, '2025-10-31 02:14:44', '285 Second Street, Karaye, Kano', 'Karaye', 'Kano', '', '2025-10-31', 'Dignissimos deserunt', 8280.00, 500.00, 8780.00, 'pending', 'unpaid', NULL, '2025-10-31 02:14:44', '2025-10-31 02:14:44'),
(6, 'AF1761877708218', 6, '2025-10-31 02:28:28', '330 North Rocky Old Lane, Karin-Lamido, Taraba', 'Karin-Lamido', 'Taraba', '', '2025-10-31', 'Mollit officia volup', 260.00, 500.00, 760.00, 'pending', 'unpaid', NULL, '2025-10-31 02:28:28', '2025-10-31 02:28:28'),
(7, 'AF1761877736724', 6, '2025-10-31 02:28:56', '772 Milton Road, Karin-Lamido, Taraba', 'Karin-Lamido', 'Taraba', '', '2025-10-31', 'Explicabo Enim fugi', 260.00, 500.00, 760.00, 'pending', 'unpaid', NULL, '2025-10-31 02:28:56', '2025-10-31 02:28:56'),
(8, 'AF1761879092740', 6, '2025-10-31 02:51:32', '294 West Nobel Court, Oshodi, Lagos', 'Oshodi', 'Lagos', '', '2025-10-31', 'Est quam molestiae n', 4080.00, 500.00, 4580.00, 'pending', 'unpaid', NULL, '2025-10-31 02:51:32', '2025-10-31 02:51:32');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `quantity`, `unit_price`, `subtotal`) VALUES
(1, 1, 10, 'Sparkling Water - Berry', 24, 80.00, 1920.00),
(2, 1, 11, 'Mineral Water - Still', 13, 180.00, 2340.00),
(3, 2, 10, 'Sparkling Water - Berry', 24, 80.00, 1920.00),
(4, 2, 11, 'Mineral Water - Still', 13, 180.00, 2340.00),
(5, 3, 10, 'Sparkling Water - Berry', 24, 80.00, 1920.00),
(6, 3, 11, 'Mineral Water - Still', 13, 180.00, 2340.00),
(7, 4, 10, 'Sparkling Water - Berry', 24, 80.00, 1920.00),
(8, 4, 11, 'Mineral Water - Still', 13, 180.00, 2340.00),
(9, 5, 9, 'Sparkling Water - Lime', 24, 75.00, 1800.00),
(10, 5, 11, 'Mineral Water - Still', 12, 180.00, 2160.00),
(11, 5, 10, 'Sparkling Water - Berry', 24, 80.00, 1920.00),
(12, 5, 12, 'Natural Spring Water', 60, 40.00, 2400.00),
(13, 6, 10, 'Sparkling Water - Berry', 1, 80.00, 80.00),
(14, 6, 11, 'Mineral Water - Still', 1, 180.00, 180.00),
(15, 7, 10, 'Sparkling Water - Berry', 1, 80.00, 80.00),
(16, 7, 11, 'Mineral Water - Still', 1, 180.00, 180.00),
(17, 8, 10, 'Sparkling Water - Berry', 24, 80.00, 1920.00),
(18, 8, 11, 'Mineral Water - Still', 12, 180.00, 2160.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_method` enum('credit_card','debit_card','bank_transfer','cash_on_delivery') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_reference` varchar(100) DEFAULT NULL,
  `payment_status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `receipt_url` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `production`
--

CREATE TABLE `production` (
  `id` int(11) NOT NULL,
  `production_date` date NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `shift` enum('morning','afternoon','night') NOT NULL,
  `quantity_produced` int(11) NOT NULL,
  `equipment_used` varchar(255) DEFAULT NULL,
  `operator_id` int(11) DEFAULT NULL,
  `operator_name` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('completed','in_progress','failed') NOT NULL DEFAULT 'completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` enum('bottled_water','beverage','package') NOT NULL,
  `size` varchar(50) DEFAULT NULL,
  `volume` varchar(50) DEFAULT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `minimum_order_quantity` int(11) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT 'default.png',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `size`, `volume`, `unit_price`, `minimum_order_quantity`, `description`, `image_url`, `status`, `created_at`, `updated_at`, `created_by`) VALUES
(1, 'Pure Life Water', 'bottled_water', 'Small', '500ml', 50.00, 50, 'Pure drinking water in 500ml bottles', 'default.png', 'active', '2025-10-31 01:24:42', '2025-10-31 01:37:10', NULL),
(2, 'Pure Life Water', 'bottled_water', 'Medium', '750ml', 70.00, 40, 'Pure drinking water in 750ml bottles', 'default.png', 'active', '2025-10-31 01:24:42', '2025-10-31 01:37:10', NULL),
(3, 'Pure Life Water', 'bottled_water', 'Large', '1.5L', 100.00, 30, 'Pure drinking water in 1.5L bottles', 'default.png', 'active', '2025-10-31 01:24:42', '2025-10-31 01:37:10', NULL),
(4, 'Fruit Juice - Orange', 'beverage', 'Regular', '500ml', 150.00, 20, 'Fresh orange juice', 'default.png', 'active', '2025-10-31 01:24:42', '2025-10-31 01:37:10', NULL),
(5, 'Fruit Juice - Apple', 'beverage', 'Regular', '500ml', 150.00, 20, 'Fresh apple juice', 'default.png', 'active', '2025-10-31 01:24:42', '2025-10-31 01:37:10', NULL),
(6, 'Energy Drink', 'beverage', 'Standard', '330ml', 200.00, 24, 'Energy boost drink', 'default.png', 'active', '2025-10-31 01:24:42', '2025-10-31 01:37:10', NULL),
(7, 'Family Pack Water', 'package', 'Pack of 12', '500ml x 12', 550.00, 5, 'Pack of 12 bottles - 500ml each', 'default.png', 'active', '2025-10-31 01:24:42', '2025-10-31 01:37:10', NULL),
(8, 'Office Pack Water', 'package', 'Pack of 24', '500ml x 24', 1000.00, 3, 'Pack of 24 bottles - 500ml each', 'default.png', 'active', '2025-10-31 01:24:42', '2025-10-31 01:37:10', NULL),
(9, 'Sparkling Water - Lime', 'bottled_water', 'Regular', '500ml', 75.00, 24, 'Sparkling water with a hint of lime', 'default.png', 'active', '2025-10-31 01:24:43', '2025-10-31 01:37:10', NULL),
(10, 'Sparkling Water - Berry', 'bottled_water', 'Regular', '500ml', 80.00, 24, 'Sparkling water with mixed berry flavor', 'default.png', 'active', '2025-10-31 01:24:43', '2025-10-31 01:37:10', NULL),
(11, 'Mineral Water - Still', 'bottled_water', 'Large', '2L', 180.00, 12, 'Mineral still water - 2L bottle', 'default.png', 'active', '2025-10-31 01:24:43', '2025-10-31 01:37:10', NULL),
(12, 'Natural Spring Water', 'bottled_water', 'Small', '330ml', 40.00, 60, 'Small 330ml natural spring water', 'default.png', 'active', '2025-10-31 01:24:43', '2025-10-31 01:37:10', NULL),
(13, 'Tropical Punch', 'beverage', 'Regular', '500ml', 160.00, 24, 'Tropical fruit punch drink', 'default.png', 'active', '2025-10-31 01:24:43', '2025-10-31 01:37:10', NULL),
(14, 'Iced Tea - Lemon', 'beverage', 'Regular', '500ml', 140.00, 24, 'Refreshing iced lemon tea', 'default.png', 'active', '2025-10-31 01:24:43', '2025-10-31 01:37:10', NULL),
(15, 'ElectroBoost - Mango', 'beverage', 'Standard', '330ml', 210.00, 24, 'Electrolyte beverage - mango flavor', 'default.png', 'active', '2025-10-31 01:24:43', '2025-10-31 01:37:10', NULL),
(16, 'Family Mega Pack Water', 'package', 'Pack of 48', '500ml x 48', 1900.00, 2, 'Bulk pack of 48 x 500ml bottles', 'default.png', 'active', '2025-10-31 01:24:43', '2025-10-31 01:37:10', NULL),
(17, 'Office Starter Pack', 'package', 'Pack of 36', '500ml x 36', 1400.00, 2, 'Office starter pack - 36 bottles', 'default.png', 'active', '2025-10-31 01:24:43', '2025-10-31 01:37:10', NULL),
(18, 'Party Pack - Mixed', 'package', 'Pack of 60', 'various', 3500.00, 1, 'Large party pack with mixed beverages', 'default.png', 'active', '2025-10-31 01:24:43', '2025-10-31 01:37:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('general','payment','delivery','notification') NOT NULL DEFAULT 'general',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `updated_at`, `updated_by`) VALUES
(1, 'company_name', 'Water & Beverage Factory', 'general', '2025-10-31 01:24:42', NULL),
(2, 'company_email', 'info@aquaflow.com', 'general', '2025-10-31 01:24:42', NULL),
(3, 'company_phone', '+2348000000000', 'general', '2025-10-31 01:24:42', NULL),
(4, 'company_address', 'Port Harcourt, Rivers State, Nigeria', 'general', '2025-10-31 01:24:42', NULL),
(5, 'delivery_fee', '500', 'delivery', '2025-10-31 01:24:42', NULL),
(6, 'minimum_order_amount', '1000', 'general', '2025-10-31 01:24:42', NULL),
(7, 'payment_methods', 'credit_card,debit_card,bank_transfer,cash_on_delivery', 'payment', '2025-10-31 01:24:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `customer_name` varchar(150) DEFAULT NULL,
  `customer_email` varchar(150) DEFAULT NULL,
  `customer_phone` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `tx_ref` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `currency` varchar(10) DEFAULT 'NGN',
  `status` varchar(50) DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('customer','sales_manager','production_manager','admin') NOT NULL DEFAULT 'customer',
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password_hash`, `role`, `status`, `address`, `city`, `state`, `postal_code`, `created_at`, `updated_at`, `last_login`) VALUES
(1, 'System Administrator', 'admin@wbfms.com', '+2348000000000', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', NULL, NULL, NULL, NULL, '2025-10-30 22:31:36', '2025-10-30 22:31:36', NULL),
(2, 'Cecilia Mercer', 'gynyzi@gmail.com', '08037573455', '$2y$10$67i1QxLgA51VsAbKqpHex.4C.q5/Q4hCM6lO6HlkYRxnokV6jAche', 'customer', 'active', '10 Milton Avenue', 'Lorem incididunt pro', 'Laudantium quo pari', 'Et archite', '2025-10-31 00:22:56', '2025-10-31 00:22:56', NULL),
(3, 'Francesca Douglas', 'cowozeguv@gmail.com', '08035587073', '$2y$10$gKB746O.txDM7HAF0VGNEeUHZwSZk1MytoGIZM3Cd93UTxMiczryW', 'customer', 'active', '36 Green New Court', 'Adamawa', 'Yola', '184520', '2025-10-31 00:42:52', '2025-10-31 00:42:52', NULL),
(4, 'Bevis Todd', 'vytu@gmail.com', '08046816018', '$2y$10$e8o4dq11EeUIUPMHTFTKOO..vIpVxUQmv.oZJ3x4hEJXd9ejB7LNa', 'customer', 'active', '639 White Second Boulevard', 'zaria', 'kaduna', '206794', '2025-10-31 00:53:49', '2025-10-31 00:53:49', NULL),
(5, 'System Administrator', 'admin@aquaflow.com', '+2348000000000', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', NULL, NULL, NULL, NULL, '2025-10-31 01:24:42', '2025-10-31 01:24:42', NULL),
(6, 'Kristen Fernandez', 'kafi@gmail.com', '08053194908', '$2y$10$1IIT1pJqPpGuVTqt79skbuCtCE79OToURPld7H.R/pnNAFc1jAbY.', 'sales_manager', 'active', '432 North White New Avenue', 'gokana', 'rivers', 'Reprehende', '2025-10-31 02:28:04', '2025-10-31 03:35:45', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_entity_type` (`entity_type`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_product` (`product_id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_stock_level` (`current_stock`);

--
-- Indexes for table `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_material_name` (`material_name`),
  ADD KEY `idx_current_stock` (`current_stock`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_type` (`type`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `idx_customer_id` (`customer_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_order_date` (`order_date`),
  ADD KEY `idx_order_number` (`order_number`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_product_id` (`product_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_transaction_reference` (`transaction_reference`);

--
-- Indexes for table `production`
--
ALTER TABLE `production`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_production_date` (`production_date`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_operator_id` (`operator_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_setting_key` (`setting_key`),
  ADD KEY `idx_setting_type` (`setting_type`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_customer_id` (`customer_id`),
  ADD KEY `idx_transaction_id` (`transaction_id`),
  ADD KEY `idx_tx_ref` (`tx_ref`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_status` (`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `materials`
--
ALTER TABLE `materials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `production`
--
ALTER TABLE `production`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
