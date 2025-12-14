<?php
/**
 * ShopEase - Update Order Status
 * Handles order status updates from admin
 */

require_once '../php/config.php';
require_once '../php/functions.php';

if (!is_admin_logged_in()) {
    redirect('login.php');
}

if (isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $status = clean_input($_POST['status']);
    
    $allowed_statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
    
    if (in_array($status, $allowed_statuses)) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
        
        if ($stmt->execute()) {
            show_message('Order status updated to ' . $status, 'success');
        } else {
            show_message('Failed to update order status', 'error');
        }
    }
}

redirect('orders.php');
?>