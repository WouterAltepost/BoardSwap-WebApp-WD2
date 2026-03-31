
-- Schema with Sample Data for BoardSwap

-- Drop existing tables if needed
DROP TABLE IF EXISTS favourites;
DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS users;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cart items table
CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Favourites table
CREATE TABLE favourites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert sample users
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@boardswap.com', '$2y$12$2vEzwlFv7nxSgj4Y8h5VBuTbuB0Coj4VUndyBXWWjFCmOKTKndJ.O', 'admin'),
('johndoe', 'john@example.com', '$2y$12$5BYkxn6bzwbqf/UYquJVB.otDvR0V8R.o6/CJwbA6jb2GCu.xmP8S', 'user');


-- Insert sample products
INSERT INTO products (name, price, description, image_url, stock) VALUES
('Classic Longboard', 599.99, 'Perfect for beginners and mellow rides.', 'https://popupsurfshop.eu/cdn/shop/files/TQ-TC-BF-BLACK-FCS-II-PLUGS.webp?v=1742305918&width=540', 10),
('Performance Shortboard', 749.99, 'Ideal for experienced surfers seeking performance.', 'https://popupsurfshop.eu/cdn/shop/files/RHFH-TMFutures.webp?v=1739445803&width=540', 5),
('Fish Surfboard', 499.99, 'Great for small waves and fast turns.', 'https://popupsurfshop.eu/cdn/shop/files/GLFP-PR0610-FU1_74150c67-e606-4cb3-a4a2-a62d9fdf550e.webp?v=1740400305&width=540', 8),
('Gun Surfboard', 849.99, 'Built for big waves and high performance.', 'https://popupsurfshop.eu/cdn/shop/files/GLNF-GT-251GliderTwinNFTFutures.webp?v=1739444270&width=540', 2),
('Salt Gypsy', 450.00, 'Fish tail shortboard for speedy rides and snapy turns.', 'https://popupsurfshop.eu/cdn/shop/files/SP-SHOPU-Pale-Apricot_ae9fdfee-43d0-442f-b9a0-d672458f7557.webp?v=1739782926&width=540', 2),
('Torq Bigfish 6´1', 800., 'High volumn board for easy to catch waves.', 'https://popupsurfshop.eu/cdn/shop/files/MFPT-DM-ART_a742e389-f039-4700-b191-2e0255246169.webp?v=1740400330&width=540', 2),
('Softech 5´11', 600.00, 'SoftTech classic with all round attributes for anyone.', 'https://popupsurfshop.eu/cdn/shop/files/MD-FALCPU-CLR.webp?v=1739449617&width=540', 2),
('Hayden Shapes 7´1', 849.99, 'Built for big waves and high performance.', 'https://popupsurfshop.eu/cdn/shop/files/CS-ALLPU-Straw.webp?v=1742219618&width=540', 2);

