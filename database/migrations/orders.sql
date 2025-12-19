CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paypal_order_id VARCHAR(100) NOT NULL UNIQUE,
    paypal_capture_id VARCHAR(100),
    paypal_payer_id VARCHAR(100),
    paypal_vault_id VARCHAR(100), -- For stored payment methods
    customer_email VARCHAR(255) NOT NULL,
    customer_name VARCHAR(255),
    amount DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) NOT NULL,
    cart_data TEXT, -- JSON encoded cart items
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_paypal_order (paypal_order_id),
    INDEX idx_customer_email (customer_email),
    INDEX idx_vault_id (paypal_vault_id)
);