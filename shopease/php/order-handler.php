<?php
/**
 * ShopEase - Order Handler
 * Handles order placement and processing
 */

require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    show_message('Please login to continue', 'error');
    redirect('../login.php');
}

$user_id = $_SESSION['user_id'];

// Place order
if (isset($_POST['place_order'])) {
    $address = clean_input($_POST['address']);
    $city = clean_input($_POST['city']);
    $state = clean_input($_POST['state']);
    $pincode = clean_input($_POST['pincode']);
    
    // Get cart items
    $stmt = $conn->prepare("SELECT c.*, p.name, p.price, p.stock 
                           FROM cart c 
                           JOIN products p ON c.product_id = p.id 
                           WHERE c.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    if (empty($cart_items)) {
        show_message('Your cart is empty', 'error');
        redirect('../cart.php');
    }
    
    // Calculate total and check stock
    $total_amount = 0;
    foreach ($cart_items as $item) {
        if ($item['quantity'] > $item['stock']) {
            show_message('Some items are out of stock', 'error');
            redirect('../cart.php');
        }
        $total_amount += $item['price'] * $item['quantity'];
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Create order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, shipping_city, shipping_state, shipping_pincode) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("idssss", $user_id, $total_amount, $address, $city, $state, $pincode);
        $stmt->execute();
        $order_id = $conn->insert_id;
        
        // Insert order items and update stock
        foreach ($cart_items as $item) {
            // Insert order item
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
            $stmt->execute();
            
            // Update product stock
            $new_stock = $item['stock'] - $item['quantity'];
            $stmt = $conn->prepare("UPDATE products SET stock = ? WHERE id = ?");
            $stmt->bind_param("ii", $new_stock, $item['product_id']);
            $stmt->execute();
        }
        
        // Clear cart
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        show_message('Order placed successfully! Order ID: #' . $order_id, 'success');
        redirect('../orders.php');
        
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        show_message('Order placement failed. Please try again.', 'error');
        redirect('../checkout.php');
    }
}
?>