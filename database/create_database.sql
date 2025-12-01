CREATE DATABASE IF NOT EXISTS electro_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE electro_shop;

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    address TEXT NULL,
    city VARCHAR(255) NULL,
    postal_code VARCHAR(10) NULL,
    role ENUM('customer', 'employee', 'admin') DEFAULT 'customer',
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    image VARCHAR(255) NULL,
    parent_id BIGINT UNSIGNED NULL,
    `order` INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_slug (slug),
    INDEX idx_parent (parent_id),
    INDEX idx_active (is_active),
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    short_description TEXT NULL,
    price DECIMAL(10, 2) NOT NULL,
    old_price DECIMAL(10, 2) NULL,
    stock INT DEFAULT 0,
    sku VARCHAR(255) NOT NULL UNIQUE,
    image VARCHAR(255) NULL,
    images JSON NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    brand VARCHAR(255) NULL,
    specifications JSON NULL,
    is_active BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    views INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_slug (slug),
    INDEX idx_category (category_id),
    INDEX idx_sku (sku),
    INDEX idx_active (is_active),
    INDEX idx_featured (is_featured),
    INDEX idx_price (price),
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cart (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NULL,
    user_id BIGINT UNSIGNED NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY unique_session_product (session_id, product_id),
    UNIQUE KEY unique_user_product (user_id, product_id),
    INDEX idx_session (session_id),
    INDEX idx_user (user_id),
    INDEX idx_product (product_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(255) NOT NULL UNIQUE,
    user_id BIGINT UNSIGNED NULL,
    email VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    address TEXT NOT NULL,
    city VARCHAR(255) NOT NULL,
    postal_code VARCHAR(10) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    tax DECIMAL(10, 2) DEFAULT 0,
    shipping DECIMAL(10, 2) DEFAULT 0,
    total DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(255) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_order_number (order_number),
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status),
    INDEX idx_created (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_sku VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_order (order_id),
    INDEX idx_product (product_id),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS favorites (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY unique_user_product (user_id, product_id),
    INDEX idx_user (user_id),
    INDEX idx_product (product_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    INDEX idx_user (user_id),
    INDEX idx_last_activity (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE OR REPLACE VIEW product_stats AS
SELECT 
    p.id,
    p.name,
    p.price,
    p.stock,
    COUNT(DISTINCT oi.id) as total_orders,
    SUM(oi.quantity) as total_sold,
    SUM(oi.total) as total_revenue
FROM products p
LEFT JOIN order_items oi ON p.id = oi.product_id
GROUP BY p.id, p.name, p.price, p.stock;

CREATE OR REPLACE VIEW order_stats AS
SELECT 
    DATE(created_at) as order_date,
    COUNT(*) as total_orders,
    SUM(total) as total_revenue,
    AVG(total) as avg_order_value
FROM orders
GROUP BY DATE(created_at);


INSERT INTO users (name, email, password, role, created_at, updated_at) VALUES
('Administrator', 'admin@electroshop.pl', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW(), NOW()),
('Pracownik', 'employee@electroshop.pl', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee', NOW(), NOW()),
('Jan Kowalski', 'jan@example.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', NOW(), NOW());

INSERT INTO categories (name, slug, description, created_at, updated_at) VALUES
('Smartfony', 'smartfony', 'Najnowsze smartfony i telefony', NOW(), NOW()),
('Laptopy', 'laptopy', 'Laptopy i notebooki', NOW(), NOW()),
('Tablety', 'tablety', 'Tablety i iPady', NOW(), NOW()),
('Telewizory', 'telewizory', 'Telewizory i monitory', NOW(), NOW()),
('Słuchawki', 'sluchawki', 'Słuchawki i audio', NOW(), NOW()),
('Akcesoria', 'akcesoria', 'Akcesoria elektroniczne', NOW(), NOW());

INSERT INTO products (name, slug, description, short_description, price, old_price, stock, sku, category_id, brand, is_featured, created_at, updated_at) VALUES
('iPhone 15 Pro', 'iphone-15-pro', 'Najnowszy iPhone z procesorem A17 Pro', 'Najnowszy iPhone z procesorem A17 Pro', 4999.00, 5499.00, 15, 'IPH15PRO001', 1, 'Apple', TRUE, NOW(), NOW()),
('Samsung Galaxy S24', 'samsung-galaxy-s24', 'Flagowy smartfon Samsung', 'Flagowy smartfon Samsung', 3999.00, NULL, 20, 'SGS24001', 1, 'Samsung', TRUE, NOW(), NOW()),
('MacBook Pro 16"', 'macbook-pro-16', 'MacBook Pro z procesorem M3 Pro', 'MacBook Pro z procesorem M3 Pro', 12999.00, NULL, 8, 'MBP16M3001', 2, 'Apple', TRUE, NOW(), NOW()),
('Dell XPS 15', 'dell-xps-15', 'Laptop Dell XPS 15', 'Laptop Dell XPS 15', 8999.00, 9999.00, 12, 'DLLXPS15001', 2, 'Dell', FALSE, NOW(), NOW()),
('iPad Pro 12.9"', 'ipad-pro-12-9', 'iPad Pro z procesorem M2', 'iPad Pro z procesorem M2', 5999.00, NULL, 10, 'IPDPRO129001', 3, 'Apple', FALSE, NOW(), NOW()),
('Samsung Galaxy Tab S9', 'samsung-galaxy-tab-s9', 'Tablet Samsung z ekranem Super AMOLED', 'Tablet Samsung z ekranem Super AMOLED', 3499.00, NULL, 14, 'SGTS9001', 3, 'Samsung', FALSE, NOW(), NOW()),
('Samsung QLED 65"', 'samsung-qled-65', 'Telewizor Samsung QLED 65"', 'Telewizor Samsung QLED 65"', 6999.00, 7999.00, 6, 'SQLED65001', 4, 'Samsung', FALSE, NOW(), NOW()),
('Sony Bravia 55"', 'sony-bravia-55', 'Telewizor Sony Bravia 55"', 'Telewizor Sony Bravia 55"', 4999.00, NULL, 9, 'SNYBRV55001', 4, 'Sony', FALSE, NOW(), NOW()),
('AirPods Pro 2', 'airpods-pro-2', 'Słuchawki Apple AirPods Pro 2', 'Słuchawki Apple AirPods Pro 2', 1299.00, NULL, 25, 'APPPRO2001', 5, 'Apple', FALSE, NOW(), NOW()),
('Sony WH-1000XM5', 'sony-wh-1000xm5', 'Słuchawki Sony z redukcją szumów', 'Słuchawki Sony z redukcją szumów', 1599.00, NULL, 18, 'SNYWH1000XM5001', 5, 'Sony', FALSE, NOW(), NOW());

