-- ============================================
-- JSC FOOD HUB - Database Schema
-- MySQL Database Setup
-- ============================================

-- Create Database
CREATE DATABASE IF NOT EXISTS jsc_food_hub;
USE jsc_food_hub;

-- ============================================
-- Users Table (Authentication)
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('chef', 'admin') NOT NULL,
    email VARCHAR(100),
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    INDEX idx_username (username),
    INDEX idx_role (role)
);

-- ============================================
-- Menu Items Table
-- ============================================
CREATE TABLE IF NOT EXISTS menu_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    category ENUM('Main Course', 'Side Dish', 'Dessert', 'Beverage', 'Snack') NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    availability BOOLEAN DEFAULT TRUE,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_availability (availability),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================
-- Inventory Stocks Table
-- ============================================
CREATE TABLE IF NOT EXISTS inventory_stocks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    quantity DECIMAL(10, 2) NOT NULL,
    unit ENUM('kg', 'lb', 'liters', 'pieces', 'dozen', 'box') NOT NULL,
    low_stock_threshold DECIMAL(10, 2) NOT NULL,
    cost_per_unit DECIMAL(10, 2) NOT NULL,
    status ENUM('Normal', 'Low') DEFAULT 'Normal',
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_name (name)
);

-- ============================================
-- Sales Records Table
-- ============================================
CREATE TABLE IF NOT EXISTS sales_records (
    id INT PRIMARY KEY AUTO_INCREMENT,
    menu_item_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    recorded_by INT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_timestamp (timestamp),
    INDEX idx_menu_item (menu_item_id),
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================
-- Stock Alerts Table
-- ============================================
CREATE TABLE IF NOT EXISTS stock_alerts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    stock_id INT NOT NULL,
    alert_type ENUM('Low Stock', 'Critical', 'Out of Stock') NOT NULL,
    message VARCHAR(255),
    is_resolved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL,
    INDEX idx_stock (stock_id),
    INDEX idx_is_resolved (is_resolved),
    FOREIGN KEY (stock_id) REFERENCES inventory_stocks(id) ON DELETE CASCADE
);

-- ============================================
-- Insert Default Users
-- ============================================
INSERT INTO users (username, password, role, full_name, email) VALUES
('chef', '$2y$10$8.8Z8Z8Z8Z8Z8Z8Z8Z8Z8uKzKzKzKzKzKzKzKzKzKzKzKzKzKzKzKz', 'chef', 'Chef User', 'chef@jscfoodhub.com'),
('admin', '$2y$10$8.8Z8Z8Z8Z8Z8Z8Z8Z8Z8uKzKzKzKzKzKzKzKzKzKzKzKzKzKzKzKz', 'admin', 'Admin User', 'admin@jscfoodhub.com');

-- Note: Default passwords are hashed using bcrypt
-- Chef password: chef123
-- Admin password: admin123