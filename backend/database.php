<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Change this to your database password
define('DB_NAME', 'login_system');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === FALSE) {
    die(json_encode(['success' => false, 'message' => 'Error creating database']));
}

// Select the database
$conn->select_db(DB_NAME);

// Create users table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255),
    google_id VARCHAR(255) UNIQUE,
    profile_picture VARCHAR(500),
    full_name VARCHAR(255),
    signup_method VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_verified INT DEFAULT 0,
    is_active INT DEFAULT 1,
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_google_id (google_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) === FALSE) {
    die(json_encode(['success' => false, 'message' => 'Error creating users table']));
}

// Set charset to utf8
$conn->set_charset("utf8mb4");

// Security: Disable warnings from being displayed
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

?>
