<?php
/**
 * Database Initialization Script
 * Run this once to create all necessary tables
 * Access: http://localhost/jsc-food-hub/backend/config/init_database.php
 */

$host = 'localhost';
$user = 'root';
$password = '';
$db_name = 'jsc_food_hub';

try {
    // Create database connection
    $conn = new PDO('mysql:host=' . $host, $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if not exists
    $conn->exec("CREATE DATABASE IF NOT EXISTS $db_name");
    $conn->exec("USE $db_name");

    // Create Users Table
    $conn->exec("CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('chef', 'admin') NOT NULL,
        email VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    // Create Menu Items Table
    $conn->exec("CREATE TABLE IF NOT EXISTS menu_items (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        category VARCHAR(50) NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        description TEXT,
        is_available BOOLEAN DEFAULT TRUE,
        created_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (created_by) REFERENCES users(id)
    )");

    // Create Inventory/Stock Table
    $conn->exec("CREATE TABLE IF NOT EXISTS inventory (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        quantity DECIMAL(10, 2) NOT NULL,
        unit VARCHAR(20) NOT NULL,
        low_stock_threshold DECIMAL(10, 2) NOT NULL,
        cost_per_unit DECIMAL(10, 2) NOT NULL,
        status ENUM('normal', 'low') DEFAULT 'normal',
        last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    // Create Sales Records Table
    $conn->exec("CREATE TABLE IF NOT EXISTS sales_records (
        id INT PRIMARY KEY AUTO_INCREMENT,
        menu_item_id INT NOT NULL,
        quantity INT NOT NULL,
        unit_price DECIMAL(10, 2) NOT NULL,
        total_price DECIMAL(10, 2) NOT NULL,
        sold_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (menu_item_id) REFERENCES menu_items(id)
    )");

    // Create Stock Alerts Table
    $conn->exec("CREATE TABLE IF NOT EXISTS stock_alerts (
        id INT PRIMARY KEY AUTO_INCREMENT,
        inventory_id INT NOT NULL,
        alert_type VARCHAR(50) NOT NULL,
        message TEXT,
        is_resolved BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (inventory_id) REFERENCES inventory(id)
    )");

    // Insert demo users with hashed passwords
    $admin_password = password_hash('admin123', PASSWORD_BCRYPT);
    $chef_password = password_hash('chef123', PASSWORD_BCRYPT);

    $conn->exec("INSERT IGNORE INTO users (username, password, role, email) VALUES 
        ('admin', '$admin_password', 'admin', 'admin@foodhub.com'),
        ('chef', '$chef_password', 'chef', 'chef@foodhub.com')");

    echo json_encode([
        'success' => true,
        'message' => 'Database initialized successfully!',
        'tables' => [
            'users',
            'menu_items',
            'inventory',
            'sales_records',
            'stock_alerts'
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>