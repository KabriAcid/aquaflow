-- =============================================
-- Water and Beverage Factory Management System
-- Database Schema (No Foreign Keys)
-- =============================================

-- Create Database
CREATE DATABASE IF NOT EXISTS wbfms_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE wbfms_db;

-- =============================================
-- 1. Users Table
-- =============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('customer', 'sales_manager', 'production_manager', 'admin') NOT NULL DEFAULT 'customer',
    status ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    postal_code VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- =============================================
-- 2. Products Table
-- =============================================
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category ENUM('bottled_water', 'beverage', 'package') NOT NULL,
    size VARCHAR(50),
    volume VARCHAR(50),
    unit_price DECIMAL(10, 2) NOT NULL,
    minimum_order_quantity INT NOT NULL DEFAULT 1,
    description TEXT,
    image_url VARCHAR(255),
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_name (name)
) ENGINE=InnoDB;

-- =============================================
-- 3. Inventory Table
-- =============================================
CREATE TABLE inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    current_stock INT NOT NULL DEFAULT 0,
    minimum_stock_level INT NOT NULL DEFAULT 10,
    last_restocked DATE,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_product (product_id),
    INDEX idx_product_id (product_id),
    INDEX idx_stock_level (current_stock)
) ENGINE=InnoDB;

-- =============================================
-- 4. Orders Table
-- =============================================
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    customer_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    delivery_address TEXT NOT NULL,
    delivery_city VARCHAR(50),
    delivery_state VARCHAR(50),
    delivery_postal_code VARCHAR(10),
    delivery_date DATE,
    special_instructions TEXT,
    subtotal DECIMAL(10, 2) NOT NULL,
    delivery_fee DECIMAL(10, 2) DEFAULT 0.00,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'out_for_delivery', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
    payment_status ENUM('unpaid', 'paid', 'refunded') NOT NULL DEFAULT 'unpaid',
    assigned_to INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_customer_id (customer_id),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status),
    INDEX idx_order_date (order_date),
    INDEX idx_order_number (order_number)
) ENGINE=InnoDB;

-- =============================================
-- 5. Order Items Table
-- =============================================
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    INDEX idx_order_id (order_id),
    INDEX idx_product_id (product_id)
) ENGINE=InnoDB;

-- =============================================
-- 6. Payments Table
-- =============================================
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    payment_method ENUM('credit_card', 'debit_card', 'bank_transfer', 'cash_on_delivery') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    transaction_reference VARCHAR(100),
    payment_status ENUM('pending', 'completed', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    receipt_url VARCHAR(255),
    notes TEXT,
    INDEX idx_order_id (order_id),
    INDEX idx_payment_status (payment_status),
    INDEX idx_transaction_reference (transaction_reference)
) ENGINE=InnoDB;

-- =============================================
-- 7. Production Table
-- =============================================
CREATE TABLE production (
    id INT AUTO_INCREMENT PRIMARY KEY,
    production_date DATE NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(100) NOT NULL,
    shift ENUM('morning', 'afternoon', 'night') NOT NULL,
    quantity_produced INT NOT NULL,
    equipment_used VARCHAR(255),
    operator_id INT,
    operator_name VARCHAR(100),
    notes TEXT,
    status ENUM('completed', 'in_progress', 'failed') NOT NULL DEFAULT 'completed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    INDEX idx_production_date (production_date),
    INDEX idx_product_id (product_id),
    INDEX idx_status (status),
    INDEX idx_operator_id (operator_id)
) ENGINE=InnoDB;

-- =============================================
-- 8. Materials Table (Raw Materials)
-- =============================================
CREATE TABLE materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    material_name VARCHAR(100) NOT NULL,
    unit VARCHAR(20) NOT NULL,
    current_stock DECIMAL(10, 2) NOT NULL DEFAULT 0,
    reorder_level DECIMAL(10, 2) NOT NULL,
    unit_cost DECIMAL(10, 2),
    supplier VARCHAR(100),
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_material_name (material_name),
    INDEX idx_current_stock (current_stock)
) ENGINE=InnoDB;

-- =============================================
-- 9. Material Usage Table
-- =============================================
CREATE TABLE material_usage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    production_id INT NOT NULL,
    material_id INT NOT NULL,
    material_name VARCHAR(100) NOT NULL,
    quantity_used DECIMAL(10, 2) NOT NULL,
    unit VARCHAR(20),
    INDEX idx_production_id (production_id),
    INDEX idx_material_id (material_id)
) ENGINE=InnoDB;

-- =============================================
-- 10. Production Schedule Table
-- =============================================
CREATE TABLE production_schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    scheduled_date DATE NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(100) NOT NULL,
    planned_quantity INT NOT NULL,
    shift ENUM('morning', 'afternoon', 'night') NOT NULL,
    assigned_to INT,
    assigned_to_name VARCHAR(100),
    status ENUM('scheduled', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'scheduled',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_scheduled_date (scheduled_date),
    INDEX idx_product_id (product_id),
    INDEX idx_status (status),
    INDEX idx_assigned_to (assigned_to)
) ENGINE=InnoDB;

-- =============================================
-- 11. System Settings Table
-- =============================================
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('general', 'payment', 'delivery', 'notification') NOT NULL DEFAULT 'general',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT,
    INDEX idx_setting_key (setting_key),
    INDEX idx_setting_type (setting_type)
) ENGINE=InnoDB;

-- =============================================
-- 12. Activity Logs Table (Audit Trail)
-- =============================================
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    user_name VARCHAR(100),
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_entity_type (entity_type),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB;

-- =============================================
-- 13. Notifications Table
-- =============================================
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'warning', 'success', 'error') NOT NULL DEFAULT 'info',
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read),
    INDEX idx_type (type)
) ENGINE=InnoDB;

-- =============================================
-- Insert Default Admin User
-- Password: admin123 (hashed with bcrypt cost 10)
-- =============================================
INSERT INTO users (full_name, email, phone, password_hash, role, status) VALUES
('System Administrator', 'admin@wbfms.com', '+2348000000000', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active');

-- =============================================
-- Insert Default System Settings
-- =============================================
INSERT INTO settings (setting_key, setting_value, setting_type) VALUES
('company_name', 'Water & Beverage Factory', 'general'),
('company_email', 'info@wbfms.com', 'general'),
('company_phone', '+2348000000000', 'general'),
('company_address', 'Port Harcourt, Rivers State, Nigeria', 'general'),
('delivery_fee', '500', 'delivery'),
('minimum_order_amount', '1000', 'general'),
('payment_methods', 'credit_card,debit_card,bank_transfer,cash_on_delivery', 'payment');

-- =============================================
-- Insert Sample Products
-- =============================================
INSERT INTO products (name, category, size, volume, unit_price, minimum_order_quantity, description, status) VALUES
('Pure Life Water', 'bottled_water', 'Small', '500ml', 50.00, 50, 'Pure drinking water in 500ml bottles', 'active'),
('Pure Life Water', 'bottled_water', 'Medium', '750ml', 70.00, 40, 'Pure drinking water in 750ml bottles', 'active'),
('Pure Life Water', 'bottled_water', 'Large', '1.5L', 100.00, 30, 'Pure drinking water in 1.5L bottles', 'active'),
('Fruit Juice - Orange', 'beverage', 'Regular', '500ml', 150.00, 20, 'Fresh orange juice', 'active'),
('Fruit Juice - Apple', 'beverage', 'Regular', '500ml', 150.00, 20, 'Fresh apple juice', 'active'),
('Energy Drink', 'beverage', 'Standard', '330ml', 200.00, 24, 'Energy boost drink', 'active'),
('Family Pack Water', 'package', 'Pack of 12', '500ml x 12', 550.00, 5, 'Pack of 12 bottles - 500ml each', 'active'),
('Office Pack Water', 'package', 'Pack of 24', '500ml x 24', 1000.00, 3, 'Pack of 24 bottles - 500ml each', 'active');

-- =============================================
-- Initialize Inventory for Products
-- =============================================
INSERT INTO inventory (product_id, current_stock, minimum_stock_level) VALUES
(1, 500, 100),
(2, 400, 80),
(3, 300, 60),
(4, 200, 40),
(5, 200, 40),
(6, 150, 30),
(7, 100, 20),
(8, 80, 15);

-- =============================================
-- Insert Sample Raw Materials
-- =============================================
INSERT INTO materials (material_name, unit, current_stock, reorder_level, unit_cost, supplier) VALUES
('Purified Water', 'Liters', 10000.00, 2000.00, 0.50, 'Water Treatment Plant'),
('PET Bottles 500ml', 'Pieces', 5000.00, 1000.00, 5.00, 'Bottle Suppliers Ltd'),
('PET Bottles 750ml', 'Pieces', 3000.00, 800.00, 7.00, 'Bottle Suppliers Ltd'),
('PET Bottles 1.5L', 'Pieces', 2000.00, 500.00, 10.00, 'Bottle Suppliers Ltd'),
('Bottle Caps', 'Pieces', 8000.00, 1500.00, 2.00, 'Cap Industries'),
('Labels', 'Pieces', 6000.00, 1200.00, 1.50, 'Printing Services'),
('Orange Concentrate', 'Liters', 500.00, 100.00, 150.00, 'Fruit Suppliers Co'),
('Apple Concentrate', 'Liters', 500.00, 100.00, 150.00, 'Fruit Suppliers Co'),
('Sugar', 'Kilograms', 1000.00, 200.00, 80.00, 'Sugar Mills'),
('Preservatives', 'Kilograms', 100.00, 20.00, 500.00, 'Chemical Suppliers');

-- =============================================
-- End of Schema
-- =============================================