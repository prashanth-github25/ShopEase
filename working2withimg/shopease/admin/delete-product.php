<?php
/**
 * ShopEase - Delete Product
 * Handles product deletion
 */

require_once '../php/config.php';
require_once '../php/functions.php';

if (!is_admin_logged_in()) {
    redirect('login.php');
}

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id > 0) {
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    
    if ($stmt->execute()) {
        show_message('Product deleted successfully', 'success');
    } else {
        show_message('Failed to delete product', 'error');
    }
}

redirect('products.php');
?>