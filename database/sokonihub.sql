-- =============================================
-- Sokoni Hub - Smart E-Commerce Database
-- BIT3208 Capstone Project
-- =============================================

CREATE DATABASE IF NOT EXISTS sokonihub;
USE sokonihub;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(200),
    phone VARCHAR(20),
    address TEXT,
    role ENUM('admin', 'customer') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Cart Table
CREATE TABLE IF NOT EXISTS cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- =============================================
-- Seed Data
-- =============================================

-- Admin User (password: admin123)
INSERT INTO users (username, email, password, full_name, role) VALUES
('admin', 'admin@sokonihub.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 'admin');

-- Sample Customer (password: customer123)
INSERT INTO users (username, email, password, full_name, role) VALUES
('john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', 'customer');

-- Categories
INSERT INTO categories (name, description) VALUES
('Electronics', 'Phones, laptops, tablets, and accessories'),
('Fashion', 'Clothing, shoes, bags, and accessories'),
('Home & Kitchen', 'Furniture, appliances, and home decor'),
('Sports & Fitness', 'Exercise equipment and sportswear'),
('Books & Education', 'Textbooks, novels, and stationery');

-- Products
INSERT INTO products (category_id, name, description, price, stock, image) VALUES
(1, 'Samsung Galaxy A54', '6.4" Super AMOLED, 128GB, 5000mAh battery', 42999.00, 15, 'phone1.jpg'),
(1, 'HP Pavilion Laptop', '15.6" FHD, Intel i5, 8GB RAM, 512GB SSD', 89999.00, 8, 'laptop1.jpg'),
(1, 'JBL Bluetooth Earbuds', 'True wireless, 6hr battery, IPX5 water resistant', 5499.00, 30, 'earbuds.jpg'),
(2, 'Men\'s Formal Shirt', 'Slim fit, 100% cotton, available in multiple colors', 1899.00, 50, 'shirt1.jpg'),
(2, 'Ladies Floral Dress', 'Casual summer dress, breathable fabric', 2499.00, 35, 'dress1.jpg'),
(2, 'Leather Handbag', 'Genuine leather, multiple compartments', 3999.00, 20, 'bag1.jpg'),
(3, 'Non-Stick Cookware Set', '5-piece set, PFOA-free coating, induction compatible', 4599.00, 12, 'cookware.jpg'),
(3, 'LED Desk Lamp', 'Adjustable brightness, USB charging port, eye-care', 1299.00, 25, 'lamp1.jpg'),
(4, 'Yoga Mat', 'Anti-slip, 6mm thick, carry strap included', 1499.00, 40, 'yogamat.jpg'),
(4, 'Dumbbell Set', '2x5kg adjustable dumbbells, chrome finish', 3299.00, 18, 'dumbbells.jpg'),
(5, 'Web Development Textbook', 'PHP, MySQL & JavaScript – Complete Guide', 2199.00, 60, 'book1.jpg'),
(5, 'Scientific Calculator', 'Casio FX-991EX, 552 functions', 1799.00, 45, 'calc1.jpg');
