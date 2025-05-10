-- Create the database
CREATE DATABASE IF NOT EXISTS tea_gap_db;
USE tea_gap_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (username),
    UNIQUE KEY (email)
);

-- Create products table
CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category ENUM('milk_tea', 'pizza', 'coffee') NOT NULL,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create ratings table
CREATE TABLE IF NOT EXISTS ratings (
    rating_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    UNIQUE KEY (user_id, product_id)
);

-- Create comments table
CREATE TABLE IF NOT EXISTS comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

-- Insert sample products (you can expand this with your actual products)
INSERT INTO products (name, description, price, category, image_path) VALUES
('Okinawa', 'Delicious Okinawa milk tea', 39.00, 'milk_tea', 'sample_product.jpg'),
('Red Velvet', 'Sweet Red Velvet milk tea', 39.00, 'milk_tea', 'sample_product.jpg'),
('Taro', 'Classic Taro milk tea', 39.00, 'milk_tea', 'sample_product.jpg'),
('Classic Milk Tea', 'Traditional milk tea recipe', 49.00, 'milk_tea', 'sample_product.jpg'),
('Meat Lovers', 'Pizza loaded with various meats', 110.00, 'pizza', 'sample_product.jpg'),
('Mushroom Pizza', 'Pizza topped with mushrooms', 90.00, 'pizza', 'sample_product.jpg'),
('Cheese Overload', 'Extra cheesy pizza', 115.00, 'pizza', 'sample_product.jpg'),
('Iced Caramel Macchiato', 'Refreshing coffee with caramel', 49.00, 'coffee', 'sample_product.jpg'),
('Blueberry Iced Latte', 'Coffee with blueberry flavor', 49.00, 'coffee', 'sample_product.jpg'),
('Iced Matcha Latte', 'Green tea latte served cold', 49.00, 'coffee', 'sample_product.jpg');
