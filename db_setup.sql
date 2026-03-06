-- Database Setup for Warung Cafe
-- Run this SQL file in phpMyAdmin or via command line

-- Create database
CREATE DATABASE IF NOT EXISTS db_warung;
USE db_warung;

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) DEFAULT 'default.jpg',
    category VARCHAR(100) DEFAULT 'General',
    stock INT DEFAULT 100,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(50) NOT NULL,
    customer_address TEXT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Insert sample products
INSERT INTO products (name, description, price, image, category, stock) VALUES
('Kopi Hitam', 'Kopi hitam original yang kaya akan aroma dan rasa', 15000, 'kopi-hitam.jpg', 'Minuman', 100),
('Latte', 'Perpaduan espresso dengan susu yang creamy', 25000, 'latte.jpg', 'Minuman', 100),
('Cappuccino', 'Espresso denganbusa susu yang lembut', 22000, 'cappuccino.jpg', 'Minuman', 100),
('Teh Tarik', 'Teh tradisional dengan teknik tarik yang unik', 18000, 'teh-tarik.jpg', 'Minuman', 100),
('Americano', 'Espresso dengan air panas', 20000, 'americano.jpg', 'Minuman', 100),
('Croissant', 'Roti pastry khas Prancis yang renyah', 25000, 'croissant.jpg', 'Makanan', 50),
('Brownies', 'Kue cokelat lezat dengan tekstur moist', 20000, 'brownies.jpg', 'Makanan', 50),
('Cheesecake', 'Kue keju dengan rasa creamy', 30000, 'cheesecake.jpg', 'Makanan', 50),
('Donat', 'Donat lembut dengan berbagai topping', 12000, 'donat.jpg', 'Makanan', 50),
('Pasta Carbonara', 'Pasta dengan saus telur dan bacon', 35000, 'pasta.jpg', 'Makanan', 30);
