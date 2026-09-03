<?php
/* ============================================
   DATABASE CONFIGURATION
   MySQL Connection Settings
   ============================================ */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'password'); // Change this to your MySQL password
define('DB_NAME', 'jsc_food_hub');
define('DB_PORT', 3306);

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

// Set charset to utf8
$conn->set_charset('utf8mb4');

// Return connection
return $conn;
?>