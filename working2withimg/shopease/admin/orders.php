<?php
require_once '../php/config.php';
require_once '../php/functions.php';

if (!is_admin_logged_in()) {
    redirect('login.php');
}

// Get all orders
$orders = $conn->query("SELECT o.*, u.name as user_name, u.email 
                       FROM orders o 
                       JOIN users u ON o.user_id = u.id 
                       ORDER BY o.order_date DESC")->fetch_all(MYSQLI_ASSOC);

$message = get_message();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - ShopEase Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="admin-body">
    <!-- Admin Sidebar -->
    <div class="admin-sidebar">
        <div class="admin-logo">
            <h2>ShopEase Admin</h2>
        </div>
        <nav class="admin-nav">
            <a href="index.php">Dashboard</a>
            <a href="products.php">Products</a>
            <a href="orders.php" class="active">Orders</a>
            <a href="logout.php">Logout</a>
        </nav>
    </div>

    <!-- Admin Content -->
    <div class="admin-content">
        <div class="admin-header">
            <h1>Manage Orders</h1>
        </div>

        <?php if ($message): ?>
        <div class="alert alert-<?php echo $message['type']; ?>">
            <?php echo $message['message']; ?>
        </div>
        <?php endif; ?>

        <div class="admin-section">
            <?php if (empty($orders)): ?>
                <p>No orders found.</p>
            <?php else: ?>
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
                    
                    <div class="admin-order-card">
                        <div class="order-card-header">
                            <div>
                                <h3>Order #<?php echo $order['id']; ?></h3>
                                <p class="order-customer">Customer: <?php echo htmlspecialchars($order['user_name']); ?> (<?php echo htmlspecialchars($order['email']); ?>)</p>
                                <p class="order-date">Date: <?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></p>
                            </div>
                            <div class="order-status-update">
                                <form action="update-order.php" method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="Pending" <?php echo $order['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Processing" <?php echo $order['status'] == 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                        <option value="Shipped" <?php echo $order['status'] == 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                        <option value="Delivered" <?php echo $order['status'] == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                        <option value="Cancelled" <?php echo $order['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </form>
                            </div>
                        </div>

                        <div class="order-items-list">
                            <h4>Order Items:</h4>
                            <table class="order-items-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order_items as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td><?php echo format_price($item['price']); ?></td>
                                        <td><?php echo format_price($item['price'] * $item['quantity']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="order-card-footer">
                            <div class="shipping-details">
                                <strong>Shipping Address:</strong>
                                <p><?php echo htmlspecialchars($order['shipping_address']); ?>, 
                                   <?php echo htmlspecialchars($order['shipping_city']); ?>, 
                                   <?php echo htmlspecialchars($order['shipping_state']); ?> - 
                                   <?php echo htmlspecialchars($order['shipping_pincode']); ?></p>
                            </div>
                            <div class="order-total-amount">
                                <strong>Total Amount:</strong>
                                <span class="total-price"><?php echo format_price($order['total_amount']); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>