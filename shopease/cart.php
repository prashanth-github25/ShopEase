<?php
require_once 'php/config.php';
require_once 'php/functions.php';

if (!is_logged_in()) {
    show_message('Please login to view your cart', 'error');
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Get cart items
$stmt = $conn->prepare("SELECT c.*, p.name, p.price, p.stock, p.image 
                       FROM cart c 
                       JOIN products p ON c.product_id = p.id 
                       WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

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
    <title>Shopping Cart - ShopEase</title>
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
                        <li><a href="cart.php" class="active">Cart (<?php echo $cart_count; ?>)</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Cart Section -->
    <section class="cart-section">
        <div class="container">
            <h2>Shopping Cart</h2>

            <?php if ($message): ?>
            <div class="alert alert-<?php echo $message['type']; ?>">
                <?php echo $message['message']; ?>
            </div>
            <?php endif; ?>

            <?php if (empty($cart_items)): ?>
                <div class="empty-cart">
                    <p>Your cart is empty</p>
                    <a href="products.php" class="btn">Continue Shopping</a>
                </div>
            <?php else: ?>
                <div class="cart-content">
                    <div class="cart-items">
                        <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item">
                            <div class="cart-item-image">
                                <span class="product-placeholder">🖼️</span>
                            </div>
                            <div class="cart-item-details">
                                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="item-price"><?php echo format_price($item['price']); ?> each</p>
                                <form action="php/cart-handler.php" method="POST" class="quantity-form">
                                    <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                    <div class="quantity-control">
                                        <label>Quantity:</label>
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>">
                                        <button type="submit" name="update_cart" class="btn-sm">Update</button>
                                    </div>
                                </form>
                            </div>
                            <div class="cart-item-total">
                                <p class="item-subtotal"><?php echo format_price($item['price'] * $item['quantity']); ?></p>
                                <a href="php/cart-handler.php?remove=<?php echo $item['id']; ?>" class="remove-btn" onclick="return confirm('Remove this item from cart?')">Remove</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="cart-summary">
                        <h3>Order Summary</h3>
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
                        <a href="checkout.php" class="btn btn-block">Proceed to Checkout</a>
                        <a href="products.php" class="btn-secondary btn-block">Continue Shopping</a>
                    </div>
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

    <script src="js/cart.js"></script>
</body>
</html>