-- Create transactions table to log payment gateway responses
-- Run this on your development database: mysql -u root -p aquaflow < schema/transactions.sql

CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    -- link to user who made the payment (customer)
    customer_id INT DEFAULT NULL,
    customer_name VARCHAR(150) DEFAULT NULL,
    customer_email VARCHAR(150) DEFAULT NULL,
    customer_phone VARCHAR(50) DEFAULT NULL,
    transaction_id VARCHAR(255) NOT NULL,
    tx_ref VARCHAR(255) DEFAULT NULL,
    amount DECIMAL(12,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'NGN',
    status VARCHAR(50) DEFAULT 'pending',
    payment_method VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_order_id (order_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_transaction_id (transaction_id),
    INDEX idx_tx_ref (tx_ref)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
