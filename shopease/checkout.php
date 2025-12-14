<?php
require_once 'php/config.php';
require_once 'php/functions.php';

if (!is_logged_in()) {
    show_message('Please login to checkout', 'error');
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$user = get_user_data($conn, $user_id);

// Get cart items
$stmt = $conn->prepare("SELECT c.*, p.name, p.price 
                       FROM cart c 
                       JOIN products p ON c.product_id = p.id 
                       WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if (empty($cart_items)) {
    show_message('Your cart is empty', 'error');
    redirect('cart.php');
}

$cart_count = count($cart_items);
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

$message = get_message();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - ShopEase</title>
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
                        <li><a href="orders.php">My Orders</a></li>
                        <li><a href="cart.php">Cart (<?php echo $cart_count; ?>)</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Checkout Section -->
    <section class="checkout-section">
        <div class="container">
            <h2>Checkout</h2>

            <?php if ($message): ?>
            <div class="alert alert-<?php echo $message['type']; ?>">
                <?php echo $message['message']; ?>
            </div>
            <?php endif; ?>

            <div class="checkout-content">
                <div class="checkout-form">
                    <h3>Shipping Address</h3>
                    <form action="php/order-handler.php" method="POST" id="checkout-form">
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea id="address" name="address" rows="3" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($user['city']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="state">State</label>
                                <input type="text" id="state" name="state" value="<?php echo htmlspecialchars($user['state']); ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="pincode">Pincode</label>
                            <input type="text" id="pincode" name="pincode" value="<?php echo htmlspecialchars($user['pincode']); ?>" required maxlength="6">
                        </div>

                        <div class="payment-method">
                            <h3>Payment Method</h3>
                            <div class="payment-option">
                                <input type="radio" id="cod" name="payment" value="cod" checked>
                                <label for="cod">Cash on Delivery</label>
                            </div>
                        </div>

                        <button type="submit" name="place_order" class="btn btn-large btn-block">Place Order</button>
                    </form>
                </div>

                <div class="order-summary">
                    <h3>Order Summary</h3>
                    <div class="order-items">
                        <?php foreach ($cart_items as $item): ?>
                        <div class="order-item">
                            <span><?php echo htmlspecialchars($item['name']); ?> × <?php echo $item['quantity']; ?></span>
                            <span><?php echo format_price($item['price'] * $item['quantity']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span><?php echo format_price($total); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span>Free</span>
                    </div>
                    <div class="summary-row summary-total">
                        <span>Total:</span>
                        <span><?php echo format_price($total); ?></span>
                    </div>
                </div>
            </div>
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