<?php
/**
 * ShopEase - Database Configuration
 * Contains database connection settings and initialization
 */

// Database credentials
define('DB_HOST', 'localhost:3307');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'shopease');

// Site configuration
define('SITE_NAME', 'ShopEase');
define('SITE_URL', 'http://localhost/shopease');

// Create database connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8
    $conn->set_charset("utf8");
    
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>