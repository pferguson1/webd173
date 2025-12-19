-- ArtShop Inc. Database Setup
-- Complete schema for dynamic e-commerce system

-- Drop existing tables if they exist
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS users;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(50),
    zip VARCHAR(20),
    country VARCHAR(100) DEFAULT 'USA',
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255),
    category VARCHAR(100),
    featured TINYINT(1) DEFAULT 0,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    user_id INT,
    customer_email VARCHAR(255) NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20),
    shipping_address TEXT,
    shipping_city VARCHAR(100),
    shipping_state VARCHAR(50),
    shipping_zip VARCHAR(20),
    payment_method VARCHAR(50) NOT NULL,
    payment_status VARCHAR(50) DEFAULT 'pending',
    paypal_order_id VARCHAR(100),
    paypal_vault_id VARCHAR(100),
    subtotal DECIMAL(10, 2) NOT NULL,
    tax DECIMAL(10, 2) DEFAULT 0,
    shipping DECIMAL(10, 2) DEFAULT 0,
    total DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Order Items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    sku VARCHAR(50) NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample products (Art pieces)
INSERT INTO products (sku, name, description, price, stock, image, category, featured) VALUES
('ART-001', 'An American Patriot', 'A stunning patriotic artwork celebrating American heritage and values.', 100.00, 15, './website_files/patriot.jpg', 'Patriotic Art', 1),
('ART-002', 'The Railway Worker', 'A powerful depiction of the hardworking men who built America\'s railways.', 100.00, 12, './website_files/railway.jpg', 'Historical Art', 1),
('ART-003', 'Urban Landscape', 'Modern cityscape capturing the essence of urban life.', 85.00, 20, './website_files/urban.jpg', 'Contemporary Art', 0),
('ART-004', 'Sunset Dreams', 'Beautiful sunset painting with vibrant colors and serene atmosphere.', 95.00, 18, './website_files/sunset.jpg', 'Nature Art', 1),
('ART-005', 'Abstract Expression', 'Bold abstract piece exploring color and form.', 120.00, 8, './website_files/abstract.jpg', 'Abstract Art', 0),
('ART-006', 'Mountain Majesty', 'Majestic mountain landscape with breathtaking detail.', 110.00, 10, './website_files/mountain.jpg', 'Nature Art', 1),
('ART-007', 'Ocean Waves', 'Dynamic seascape capturing the power and beauty of the ocean.', 90.00, 14, './website_files/ocean.jpg', 'Nature Art', 0),
('ART-008', 'Portrait Study', 'Classical portrait showcasing masterful technique.', 150.00, 5, './website_files/portrait.jpg', 'Portrait Art', 1);

-- Insert admin user (password: admin123 - hashed with password_hash)
-- Note: This is a sample hash. In production, use password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO users (email, password, first_name, last_name, is_admin) VALUES
('admin@artshop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 1);

-- Create indexes for better performance
CREATE INDEX idx_products_sku ON products(sku);
CREATE INDEX idx_products_category ON products(category);
CREATE INDEX idx_products_featured ON products(featured);
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_orders_number ON orders(order_number);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_order_items_order ON order_items(order_id);
CREATE INDEX idx_order_items_product ON order_items(product_id);
