<?php
/**
 * ShopEase - Helper Functions
 * Common utility functions used throughout the application
 */

/**
 * Sanitize user input to prevent XSS attacks
 */
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Redirect to a specific page
 */
function redirect($page) {
    header("Location: " . $page);
    exit();
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if admin is logged in
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin_id']);
}

/**
 * Get current user data
 */
function get_user_data($conn, $user_id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Get cart count for user
 */
function get_cart_count($conn, $user_id) {
    $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}

/**
 * Format price in Indian Rupees
 */
function format_price($price) {
    return '₹' . number_format($price, 2);
}

/**
 * Get all categories
 */
function get_categories($conn) {
    $result = $conn->query("SELECT * FROM categories ORDER BY name");
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get featured products
 */
function get_featured_products($conn, $limit = 6) {
    $stmt = $conn->prepare("SELECT p.*, c.name as category_name 
                           FROM products p 
                           JOIN categories c ON p.category_id = c.id 
                           WHERE p.featured = 1 
                           ORDER BY p.created_at DESC 
                           LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get products by category
 */
function get_products_by_category($conn, $category_id) {
    $stmt = $conn->prepare("SELECT p.*, c.name as category_name 
                           FROM products p 
                           JOIN categories c ON p.category_id = c.id 
                           WHERE p.category_id = ? 
                           ORDER BY p.created_at DESC");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get single product details
 */
function get_product($conn, $product_id) {
    $stmt = $conn->prepare("SELECT p.*, c.name as category_name 
                           FROM products p 
                           JOIN categories c ON p.category_id = c.id 
                           WHERE p.id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Display success message
 */
function show_message($message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

/**
 * Get and clear message
 */
function get_message() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $type = $_SESSION['message_type'] ?? 'success';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}
?>