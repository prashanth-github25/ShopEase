<?php
require_once 'php/config.php';
require_once 'php/functions.php';

if (!is_logged_in()) {
    show_message('Please login to view orders', 'error');
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Get user orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$cart_count = get_cart_count($conn, $user_id);
$message = get_message();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - ShopEase</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1><a href="index.php">ShopEase</a></h1>
                </div>
                <nav>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="products.php">Products</a></li>
                        <li><a href="orders.php" class="active">My Orders</a></li>
                        <li><a href="cart.php">Cart (<?php echo $cart_count; ?>)</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Orders Section -->
    <section class="orders-section">
        <div class="container">
            <h2>My Orders</h2>

            <?php if ($message): ?>
            <div class="alert alert-<?php echo $message['type']; ?>">
                <?php echo $message['message']; ?>
            </div>
            <?php endif; ?>

            <?php if (empty($orders)): ?>
                <div class="no-orders">
                    <p>You haven't placed any orders yet.</p>
                    <a href="products.php" class="btn">Start Shopping</a>
                </div>
            <?php else: ?>
                <div class="orders-list">
                    <?php foreach ($orders as $order): ?>
                        <?php
                        // Get order items
                        $stmt = $conn->prepare("SELECT oi.*, p.name 
                                               FROM order_items oi 
                                               JOIN products p ON oi.product_id = p.id 
                                               WHERE oi.order_id = ?");
                        $stmt->bind_param("i", $order['id']);
                        $stmt->execute();
                        $order_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                        ?>
                        
                        <div class="order-card">
                            <div class="order-header">
                                <div class="order-info">
                                    <h3>Order #<?php echo $order['id']; ?></h3>
                                    <p class="order-date"><?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></p>
                                </div>
                                <div class="order-status">
                                    <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                        <?php echo $order['status']; ?>
                                    </span>
                                </div>
                            </div>

                            <div class="order-items">
                                <?php foreach ($order_items as $item): ?>
                                <div class="order-item">
                                    <span class="item-name"><?php echo htmlspecialchars($item['name']); ?></span>
                                    <span class="item-quantity">Qty: <?php echo $item['quantity']; ?></span>
                                    <span class="item-price"><?php echo format_price($item['price'] * $item['quantity']); ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="order-footer">
                                <div class="shipping-address">
                                    <strong>Shipping Address:</strong>
                                    <p><?php echo htmlspecialchars($order['shipping_address']); ?>, 
                                       <?php echo htmlspecialchars($order['shipping_city']); ?>, 
                                       <?php echo htmlspecialchars($order['shipping_state']); ?> - 
                                       <?php echo htmlspecialchars($order['shipping_pincode']); ?></p>
                                </div>
                                <div class="order-total">
                                    <strong>Total Amount:</strong>
                                    <span class="total-price"><?php echo format_price($order['total_amount']); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2024 ShopEase. All rights reserved.</p>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>