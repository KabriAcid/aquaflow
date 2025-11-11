-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2025 at 02:34 PM
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
  `quantity` int(11) DEFAULT 0,
  `minimum_stock_level` int(11) NOT NULL DEFAULT 10,
  `reorder_point` int(11) DEFAULT 50,
  `last_restocked` date DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `product_id`, `current_stock`, `quantity`, `minimum_stock_level`, `reorder_point`, `last_restocked`, `last_updated`) VALUES
(1, 1, 500, 0, 100, 50, NULL, '2025-10-31 01:24:42'),
(2, 2, 400, 0, 80, 50, NULL, '2025-10-31 01:24:42'),
(3, 3, 300, 0, 60, 50, NULL, '2025-10-31 01:24:42'),
(4, 4, 200, 0, 40, 50, NULL, '2025-10-31 01:24:42'),
(5, 5, 200, 0, 40, 50, NULL, '2025-10-31 01:24:42'),
(6, 6, 150, 0, 30, 50, NULL, '2025-10-31 01:24:42'),
(7, 7, 100, 0, 20, 50, NULL, '2025-10-31 01:24:42'),
(8, 8, 80, 0, 15, 50, NULL, '2025-10-31 01:24:42'),
(9, 9, 300, 0, 50, 50, NULL, '2025-10-31 01:24:43'),
(10, 10, 250, 0, 50, 50, NULL, '2025-10-31 01:24:43'),
(11, 11, 120, 0, 30, 50, NULL, '2025-10-31 01:24:43'),
(12, 12, 600, 0, 100, 50, NULL, '2025-10-31 01:24:43'),
(13, 13, 200, 0, 40, 50, NULL, '2025-10-31 01:24:43'),
(14, 14, 220, 0, 40, 50, NULL, '2025-10-31 01:24:43'),
(15, 15, 180, 0, 30, 50, NULL, '2025-10-31 01:24:44'),
(16, 16, 60, 0, 10, 50, NULL, '2025-10-31 01:24:44'),
(17, 17, 80, 0, 10, 50, NULL, '2025-10-31 01:24:44'),
(18, 18, 30, 0, 5, 50, NULL, '2025-10-31 01:24:44');

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
  `delivery_date` date DEFAULT NULL,
  `special_instructions` text DEFAULT 'No special instructions',
  `subtotal` decimal(10,2) NOT NULL,
  `delivery_fee` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','out_for_delivery','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','paid','refunded') NOT NULL DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `customer_id`, `order_date`, `delivery_address`, `delivery_city`, `delivery_state`, `delivery_date`, `special_instructions`, `subtotal`, `delivery_fee`, `total_amount`, `status`, `payment_status`, `created_at`, `updated_at`) VALUES
(10, 'AF1762436426361', 4, '2025-11-06 13:40:26', '639 White Second Boulevard, Surulere, Lagos', 'Surulere', 'Lagos', '2025-11-19', 'No special instructions', 24780.00, 500.00, 25280.00, 'cancelled', 'unpaid', '2025-11-06 13:40:26', '2025-11-06 15:31:42'),
(11, 'AF1762438964726', 4, '2025-11-06 14:22:44', '639 White Second Boulevard, Bonny, Rivers', 'Bonny', 'Rivers', '2025-11-22', '', 36530.00, 500.00, 37030.00, 'delivered', 'unpaid', '2025-11-06 14:22:44', '2025-11-06 15:31:37'),
(12, 'AF1762439662896', 4, '2025-11-06 14:34:22', '639 White Second Boulevard, Surulere, Lagos', 'Surulere', 'Lagos', '2025-11-06', '', 9600.00, 500.00, 10100.00, 'delivered', 'unpaid', '2025-11-06 14:34:22', '2025-11-06 15:28:58'),
(13, 'AF1762858367856', 4, '2025-11-11 10:52:47', '639 White Second Boulevard, Ikeja, Lagos', 'Ikeja', 'Lagos', '2025-11-11', '', 9320.00, 500.00, 9820.00, 'out_for_delivery', 'paid', '2025-11-11 10:52:47', '2025-11-11 11:46:42'),
(14, 'AF1762859624775', 4, '2025-11-11 11:13:44', '639 White Second Boulevard, Nassarawa, Kano', 'Nassarawa', 'Kano', '2025-11-11', '', 11300.00, 500.00, 11800.00, 'delivered', 'paid', '2025-11-11 11:13:44', '2025-11-11 11:44:18'),
(15, 'AF1762859940441', 4, '2025-11-11 11:19:00', '382 South Milton Parkway, Zaria, Kaduna', 'Zaria', 'Kaduna', '2025-11-11', '', 4080.00, 500.00, 4580.00, 'delivered', 'unpaid', '2025-11-11 11:19:00', '2025-11-11 11:42:40'),
(16, 'AF1762860558363', 4, '2025-11-11 11:29:18', '639 White Second Boulevard, Gwale, Kano', 'Gwale', 'Kano', '2025-11-11', '', 3800.00, 500.00, 4300.00, 'delivered', 'unpaid', '2025-11-11 11:29:18', '2025-11-11 11:42:46');

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
(19, 9, 18, 'Party Pack - Mixed', 2, 3500.00, 7000.00),
(20, 9, 1, 'Pure Life Water', 50, 50.00, 2500.00),
(21, 9, 2, 'Pure Life Water', 40, 70.00, 2800.00),
(22, 9, 9, 'Sparkling Water - Lime', 24, 75.00, 1800.00),
(23, 9, 10, 'Sparkling Water - Berry', 24, 80.00, 1920.00),
(24, 9, 11, 'Mineral Water - Still', 12, 180.00, 2160.00),
(25, 9, 16, 'Family Mega Pack Water', 2, 1900.00, 3800.00),
(26, 9, 17, 'Office Starter Pack', 2, 1400.00, 2800.00),
(27, 10, 18, 'Party Pack - Mixed', 2, 3500.00, 7000.00),
(28, 10, 1, 'Pure Life Water', 50, 50.00, 2500.00),
(29, 10, 2, 'Pure Life Water', 40, 70.00, 2800.00),
(30, 10, 9, 'Sparkling Water - Lime', 24, 75.00, 1800.00),
(31, 10, 10, 'Sparkling Water - Berry', 24, 80.00, 1920.00),
(32, 10, 11, 'Mineral Water - Still', 12, 180.00, 2160.00),
(33, 10, 16, 'Family Mega Pack Water', 2, 1900.00, 3800.00),
(34, 10, 17, 'Office Starter Pack', 2, 1400.00, 2800.00),
(35, 11, 18, 'Party Pack - Mixed', 2, 3500.00, 7000.00),
(36, 11, 1, 'Pure Life Water', 50, 50.00, 2500.00),
(37, 11, 2, 'Pure Life Water', 40, 70.00, 2800.00),
(38, 11, 9, 'Sparkling Water - Lime', 24, 75.00, 1800.00),
(39, 11, 10, 'Sparkling Water - Berry', 24, 80.00, 1920.00),
(40, 11, 11, 'Mineral Water - Still', 12, 180.00, 2160.00),
(41, 11, 16, 'Family Mega Pack Water', 2, 1900.00, 3800.00),
(42, 11, 17, 'Office Starter Pack', 2, 1400.00, 2800.00),
(43, 11, 4, 'Fruit Juice - Orange', 20, 150.00, 3000.00),
(44, 11, 5, 'Fruit Juice - Apple', 20, 150.00, 3000.00),
(45, 11, 7, 'Family Pack Water', 5, 550.00, 2750.00),
(46, 11, 8, 'Office Pack Water', 3, 1000.00, 3000.00),
(47, 12, 13, 'Tropical Punch', 24, 160.00, 3840.00),
(48, 12, 14, 'Iced Tea - Lemon', 24, 140.00, 3360.00),
(49, 12, 12, 'Natural Spring Water', 60, 40.00, 2400.00),
(50, 13, 11, 'Mineral Water - Still', 12, 180.00, 2160.00),
(51, 13, 14, 'Iced Tea - Lemon', 24, 140.00, 3360.00),
(52, 13, 16, 'Family Mega Pack Water', 2, 1900.00, 3800.00),
(53, 14, 17, 'Office Starter Pack', 4, 1400.00, 5600.00),
(54, 14, 16, 'Family Mega Pack Water', 3, 1900.00, 5700.00),
(55, 15, 10, 'Sparkling Water - Berry', 24, 80.00, 1920.00),
(56, 15, 11, 'Mineral Water - Still', 12, 180.00, 2160.00),
(57, 16, 10, 'Sparkling Water - Berry', 25, 80.00, 2000.00),
(58, 16, 9, 'Sparkling Water - Lime', 24, 75.00, 1800.00);

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

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `payment_method`, `amount`, `transaction_reference`, `payment_status`, `payment_date`, `receipt_url`, `notes`) VALUES
(1, 12, 'cash_on_delivery', 10100.00, 'COD-12-1762439667', 'pending', '2025-11-06 14:34:27', NULL, '{\"note\":\"Cash on Delivery created via confirm_cod endpoint\",\"created_by\":4}'),
(2, 13, '', 9820.00, '9783307', 'completed', '2025-11-11 10:53:14', NULL, '{\"warning\":\"FLW_SECRET_KEY not set; skipping remote verification in dev mode.\"}'),
(3, 14, '', 11800.00, '9783365', 'completed', '2025-11-11 11:16:50', NULL, '{\"warning\":\"FLW_SECRET_KEY not set; skipping remote verification in dev mode.\"}'),
(4, 15, 'cash_on_delivery', 4580.00, 'COD-15-1762859943', 'pending', '2025-11-11 11:19:03', NULL, '{\"note\":\"Cash on Delivery created via confirm_cod endpoint\",\"created_by\":4}'),
(5, 16, 'cash_on_delivery', 4300.00, 'COD-16-1762860558', 'pending', '2025-11-11 11:29:18', NULL, '{\"note\":\"Cash on Delivery created via confirm_cod endpoint\",\"created_by\":4}');

-- --------------------------------------------------------

--
-- Table structure for table `production_logs`
--

CREATE TABLE `production_logs` (
  `id` int(11) NOT NULL,
  `production_date` date NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_type` enum('bottled_water','sparkling_beverages','other') NOT NULL DEFAULT 'bottled_water',
  `quantity_produced` int(11) NOT NULL DEFAULT 0,
  `shift` enum('morning','afternoon','night') DEFAULT NULL,
  `operator_id` int(11) DEFAULT NULL,
  `operator_name` varchar(100) DEFAULT NULL,
  `equipment_used` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('completed','in_progress','failed') NOT NULL DEFAULT 'completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `production_logs`
--

INSERT INTO `production_logs` (`id`, `production_date`, `product_id`, `product_name`, `product_type`, `quantity_produced`, `shift`, `operator_id`, `operator_name`, `equipment_used`, `notes`, `status`, `created_at`, `created_by`) VALUES
(1, '2025-11-11', 1, 'Pure Life Water', 'bottled_water', 2500, 'morning', 3, 'Francesca Douglas', 'Line A', NULL, 'completed', '2025-11-11 12:33:59', 3),
(2, '2025-11-11', 9, 'Sparkling Water - Lime', 'sparkling_beverages', 1800, 'morning', 3, 'Francesca Douglas', 'Line B', NULL, 'completed', '2025-11-11 12:33:59', 3),
(3, '2025-11-11', 2, 'Pure Life Water', 'bottled_water', 2200, 'afternoon', 3, 'Francesca Douglas', 'Line A', NULL, 'completed', '2025-11-11 12:33:59', 3),
(4, '2025-11-11', 10, 'Sparkling Water - Berry', 'sparkling_beverages', 1500, 'afternoon', 3, 'Francesca Douglas', 'Line B', NULL, 'completed', '2025-11-11 12:33:59', 3),
(5, '2025-11-10', 1, 'Pure Life Water', 'bottled_water', 2400, 'morning', 3, 'Francesca Douglas', 'Line A', NULL, 'completed', '2025-11-11 12:33:59', 3),
(6, '2025-11-10', 13, 'Tropical Punch', 'sparkling_beverages', 1900, 'morning', 3, 'Francesca Douglas', 'Line B', NULL, 'completed', '2025-11-11 12:33:59', 3),
(7, '2025-11-10', 3, 'Pure Life Water', 'bottled_water', 2100, 'afternoon', 3, 'Francesca Douglas', 'Line A', NULL, 'completed', '2025-11-11 12:33:59', 3),
(8, '2025-11-10', 14, 'Iced Tea - Lemon', 'sparkling_beverages', 1700, 'afternoon', 3, 'Francesca Douglas', 'Line B', NULL, 'completed', '2025-11-11 12:33:59', 3),
(9, '2025-11-09', 1, 'Pure Life Water', 'bottled_water', 2300, 'morning', 3, 'Francesca Douglas', 'Line A', NULL, 'completed', '2025-11-11 12:33:59', 3),
(10, '2025-11-09', 4, 'Fruit Juice - Orange', 'sparkling_beverages', 1600, 'morning', 3, 'Francesca Douglas', 'Line B', NULL, 'completed', '2025-11-11 12:33:59', 3),
(11, '2025-11-09', 2, 'Pure Life Water', 'bottled_water', 2200, 'afternoon', 3, 'Francesca Douglas', 'Line A', NULL, 'completed', '2025-11-11 12:33:59', 3),
(12, '2025-11-09', 5, 'Fruit Juice - Apple', 'sparkling_beverages', 1800, 'afternoon', 3, 'Francesca Douglas', 'Line B', NULL, 'completed', '2025-11-11 12:33:59', 3),
(13, '2025-11-08', 1, 'Pure Life Water', 'bottled_water', 2600, 'morning', 3, 'Francesca Douglas', 'Line A', NULL, 'completed', '2025-11-11 12:33:59', 3),
(14, '2025-11-08', 9, 'Sparkling Water - Lime', 'sparkling_beverages', 1900, 'morning', 3, 'Francesca Douglas', 'Line B', NULL, 'completed', '2025-11-11 12:33:59', 3),
(15, '2025-11-08', 3, 'Pure Life Water', 'bottled_water', 2400, 'afternoon', 3, 'Francesca Douglas', 'Line A', NULL, 'completed', '2025-11-11 12:33:59', 3),
(16, '2025-11-08', 10, 'Sparkling Water - Berry', 'sparkling_beverages', 1700, 'afternoon', 3, 'Francesca Douglas', 'Line B', NULL, 'completed', '2025-11-11 12:33:59', 3),
(17, '2025-11-07', 1, 'Pure Life Water', 'bottled_water', 2500, 'morning', 3, 'Francesca Douglas', 'Line A', NULL, 'completed', '2025-11-11 12:33:59', 3),
(18, '2025-11-07', 4, 'Fruit Juice - Orange', 'sparkling_beverages', 1650, 'morning', 3, 'Francesca Douglas', 'Line B', NULL, 'completed', '2025-11-11 12:33:59', 3),
(19, '2025-11-07', 2, 'Pure Life Water', 'bottled_water', 2300, 'afternoon', 3, 'Francesca Douglas', 'Line A', NULL, 'completed', '2025-11-11 12:33:59', 3),
(20, '2025-11-07', 13, 'Tropical Punch', 'sparkling_beverages', 1750, 'afternoon', 3, 'Francesca Douglas', 'Line B', NULL, 'completed', '2025-11-11 12:33:59', 3);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` enum('bottled_water','beverage','package') NOT NULL,
  `product_type` enum('bottled_water','sparkling_beverages','other') DEFAULT 'bottled_water',
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

INSERT INTO `products` (`id`, `name`, `category`, `product_type`, `size`, `volume`, `unit_price`, `minimum_order_quantity`, `description`, `image_url`, `status`, `created_at`, `updated_at`, `created_by`) VALUES
(1, 'Pure Life Water', 'bottled_water', 'bottled_water', 'Small', '500ml', 50.00, 50, 'Pure drinking water in 500ml bottles', 'default.png', 'active', '2025-10-31 01:24:42', '2025-10-31 01:37:10', NULL),
(2, 'Pure Life Water', 'bottled_water', 'bottled_water', 'Medium', '750ml', 70.00, 40, 'Pure drinking water in 750ml bottles', 'default.png', 'active', '2025-10-31 01:24:42', '2025-10-31 01:37:10', NULL),
(3, 'Pure Life Water', 'bottled_water', 'bottled_water', 'Large', '1.5L', 100.00, 30, 'Pure drinking water in 1.5L bottles', 'default.png', 'active', '2025-10-31 01:24:42', '2025-10-31 01:37:10', NULL),
(4, 'Fruit Juice - Orange', 'beverage', 'bottled_water', 'Regular', '500ml', 150.00, 20, 'Fresh orange juice', 'default.png', 'active', '2025-10-31 01:24:42', '2025-10-31 01:37:10', NULL),
(5, 'Fruit Juice - Apple', 'beverage', 'bottled_water', 'Regular', '500ml', 150.00, 20, 'Fresh apple juice', 'default.png', 'active', '2025-10-31 01:24:42', '2025-10-31 01:37:10', NULL),
(6, 'Energy Drink', 'beverage', 'bottled_water', 'Standard', '330ml', 200.00, 24, 'Energy boost drink', 'default.png', 'active', '2025-10-31 01:24:42', '2025-10-31 01:37:10', NULL),
(7, 'Family Pack Water', 'package', 'bottled_water', 'Pack of 12', '500ml x 12', 550.00, 5, 'Pack of 12 bottles - 500ml each', 'default.png', 'active', '2025-10-31 01:24:42', '2025-10-31 01:37:10', NULL),
(8, 'Office Pack Water', 'package', 'bottled_water', 'Pack of 24', '500ml x 24', 1000.00, 3, 'Pack of 24 bottles - 500ml each', 'default.png', 'active', '2025-10-31 01:24:42', '2025-10-31 01:37:10', NULL),
(9, 'Sparkling Water - Lime', 'bottled_water', 'bottled_water', 'Regular', '500ml', 75.00, 24, 'Sparkling water with a hint of lime', 'default.png', 'active', '2025-10-31 01:24:43', '2025-10-31 01:37:10', NULL),
(10, 'Sparkling Water - Berry', 'bottled_water', 'bottled_water', 'Regular', '500ml', 80.00, 24, 'Sparkling water with mixed berry flavor', 'default.png', 'active', '2025-10-31 01:24:43', '2025-10-31 01:37:10', NULL),
(11, 'Mineral Water - Still', 'bottled_water', 'bottled_water', 'Large', '2L', 180.00, 12, 'Mineral still water - 2L bottle', 'default.png', 'active', '2025-10-31 01:24:43', '2025-10-31 01:37:10', NULL),
(12, 'Natural Spring Water', 'bottled_water', 'bottled_water', 'Small', '330ml', 40.00, 60, 'Small 330ml natural spring water', 'default.png', 'active', '2025-10-31 01:24:43', '2025-10-31 01:37:10', NULL),
(13, 'Tropical Punch', 'beverage', 'bottled_water', 'Regular', '500ml', 160.00, 24, 'Tropical fruit punch drink', 'default.png', 'active', '2025-10-31 01:24:43', '2025-10-31 01:37:10', NULL),
(14, 'Iced Tea - Lemon', 'beverage', 'bottled_water', 'Regular', '500ml', 140.00, 24, 'Refreshing iced lemon tea', 'default.png', 'active', '2025-10-31 01:24:43', '2025-10-31 01:37:10', NULL),
(15, 'ElectroBoost - Mango', 'beverage', 'bottled_water', 'Standard', '330ml', 210.00, 24, 'Electrolyte beverage - mango flavor', 'default.png', 'active', '2025-10-31 01:24:43', '2025-10-31 01:37:10', NULL),
(16, 'Family Mega Pack Water', 'package', 'bottled_water', 'Pack of 48', '500ml x 48', 1900.00, 2, 'Bulk pack of 48 x 500ml bottles', 'default.png', 'active', '2025-10-31 01:24:43', '2025-10-31 01:37:10', NULL),
(17, 'Office Starter Pack', 'package', 'bottled_water', 'Pack of 36', '500ml x 36', 1400.00, 2, 'Office starter pack - 36 bottles', 'default.png', 'active', '2025-10-31 01:24:43', '2025-10-31 01:37:10', NULL),
(18, 'Party Pack - Mixed', 'package', 'bottled_water', 'Pack of 60', 'various', 3500.00, 1, 'Large party pack with mixed beverages', 'default.png', 'active', '2025-10-31 01:24:43', '2025-10-31 01:37:10', NULL);

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
(2, 'Cecilia Mercer', 'admin@aquaflow.com', '08037573455', '$2y$10$67i1QxLgA51VsAbKqpHex.4C.q5/Q4hCM6lO6HlkYRxnokV6jAche', 'admin', 'active', '10 Milton Avenue', 'Lorem incididunt pro', 'Laudantium quo pari', 'Et archite', '2025-10-31 00:22:56', '2025-10-31 19:13:42', NULL),
(3, 'Francesca Douglas', 'production@aquaflow.com', '08035587073', '$2y$10$gKB746O.txDM7HAF0VGNEeUHZwSZk1MytoGIZM3Cd93UTxMiczryW', 'production_manager', 'active', '36 Green New Court', 'kaduna_north', 'kaduna', '184520', '2025-10-31 00:42:52', '2025-11-06 17:56:23', NULL),
(4, 'Bevis Todd', 'customer@aquaflow.com', '08046816018', '$2y$10$e8o4dq11EeUIUPMHTFTKOO..vIpVxUQmv.oZJ3x4hEJXd9ejB7LNa', 'customer', 'active', '639 White Second Boulevard', 'zaria', 'kaduna', '206794', '2025-10-31 00:53:49', '2025-10-31 19:13:57', NULL),
(6, 'Kristen Fernandez', 'sales@aquaflow.com', '08053194908', '$2y$10$1IIT1pJqPpGuVTqt79skbuCtCE79OToURPld7H.R/pnNAFc1jAbY.', 'sales_manager', 'active', '432 North White New Avenue', 'gokana', 'rivers', 'Reprehende', '2025-10-31 02:28:04', '2025-10-31 19:13:10', NULL),
(7, 'Abdullahi Kabri', 'kabriacid01@gmail.com', '07037943396', '$2y$10$ysHgAVRPC.WahD00007RXuN/A52wY8Pd21i.EMa/wCCUOYEukvOo6', 'production_manager', 'active', NULL, 'gwale', 'kano', NULL, '2025-11-06 16:43:07', '2025-11-06 18:02:56', NULL),
(8, 'Jameson Mercado', 'nunuk@gmail.com', '08077324553', '$2y$10$fJqnYfl.YlP98v/jX.daTOSl/2R5jp7Ej5PdB6tjI0Kuh37W5kGHm', 'sales_manager', 'active', NULL, 'zaria', 'kaduna', NULL, '2025-11-06 18:01:24', '2025-11-06 18:01:24', NULL);

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
-- Indexes for table `production_logs`
--
ALTER TABLE `production_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_production_date` (`production_date`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_product_type` (`product_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_quantity` (`quantity_produced`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `production_logs`
--
ALTER TABLE `production_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
