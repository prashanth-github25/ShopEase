<?php
/**
 * ShopEase - Cart Handler
 * Handles add to cart, update quantity, and remove from cart
 */

require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    show_message('Please login to continue', 'error');
    redirect('../login.php');
}

$user_id = $_SESSION['user_id'];

// Add to cart
if (isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']) ?? 1;
    
    // Check if product exists and has stock
    $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        show_message('Product not found', 'error');
        redirect('../index.php');
    }
    
    $product = $result->fetch_assoc();
    
    if ($product['stock'] < $quantity) {
        show_message('Insufficient stock', 'error');
        redirect('../product-detail.php?id=' . $product_id);
    }
    
    // Check if product already in cart
    $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update quantity
        $cart_item = $result->fetch_assoc();
        $new_quantity = $cart_item['quantity'] + $quantity;
        
        if ($new_quantity > $product['stock']) {
            show_message('Cannot add more than available stock', 'error');
            redirect('../product-detail.php?id=' . $product_id);
        }
        
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_quantity, $cart_item['id']);
        $stmt->execute();
    } else {
        // Insert new cart item
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $stmt->execute();
    }
    
    show_message('Product added to cart', 'success');
    redirect('../cart.php');
}

// Update cart quantity
if (isset($_POST['update_cart'])) {
    $cart_id = intval($_POST['cart_id']);
    $quantity = intval($_POST['quantity']);
    
    if ($quantity < 1) {
        show_message('Quantity must be at least 1', 'error');
        redirect('../cart.php');
    }
    
    // Check stock availability
    $stmt = $conn->prepare("SELECT p.stock FROM cart c JOIN products p ON c.product_id = p.id WHERE c.id = ? AND c.user_id = ?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        show_message('Cart item not found', 'error');
        redirect('../cart.php');
    }
    
    $item = $result->fetch_assoc();
    
    if ($quantity > $item['stock']) {
        show_message('Quantity exceeds available stock', 'error');
        redirect('../cart.php');
    }
    
    // Update quantity
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("iii", $quantity, $cart_id, $user_id);
    $stmt->execute();
    
    show_message('Cart updated', 'success');
    redirect('../cart.php');
}

// Remove from cart
if (isset($_GET['remove'])) {
    $cart_id = intval($_GET['remove']);
    
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();
    
    show_message('Item removed from cart', 'success');
    redirect('../cart.php');
}
?>