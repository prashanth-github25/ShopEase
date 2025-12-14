<?php
require_once 'php/config.php';
require_once 'php/functions.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = get_product($conn, $product_id);

if (!$product) {
    redirect('products.php');
}

$cart_count = is_logged_in() ? get_cart_count($conn, $_SESSION['user_id']) : 0;
$message = get_message();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - ShopEase</title>
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
                        <?php if (is_logged_in()): ?>
                            <li><a href="orders.php">My Orders</a></li>
                            <li><a href="cart.php">Cart (<?php echo $cart_count; ?>)</a></li>
                            <li><a href="logout.php">Logout</a></li>
                        <?php else: ?>
                            <li><a href="login.php">Login</a></li>
                            <li><a href="signup.php">Sign Up</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Product Detail Section -->
    <section class="product-detail-section">
        <div class="container">
            <?php if ($message): ?>
            <div class="alert alert-<?php echo $message['type']; ?>">
                <?php echo $message['message']; ?>
            </div>
            <?php endif; ?>

            <div class="product-detail">
                <div class="product-detail-image">
                    <span class="product-placeholder-large">🖼️</span>
                </div>

                <div class="product-detail-info">
                    <span class="category-tag"><?php echo htmlspecialchars($product['category_name']); ?></span>
                    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                    <p class="price-large"><?php echo format_price($product['price']); ?></p>
                    
                    <div class="stock-status">
                        <?php if ($product['stock'] > 0): ?>
                            <span class="in-stock">✓ In Stock (<?php echo $product['stock']; ?> available)</span>
                        <?php else: ?>
                            <span class="out-of-stock">✗ Out of Stock</span>
                        <?php endif; ?>
                    </div>

                    <div class="product-description">
                        <h3>Description</h3>
                        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    </div>

                    <?php if ($product['stock'] > 0): ?>
                    <form action="php/cart-handler.php" method="POST" class="add-to-cart-form">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        
                        <div class="quantity-selector">
                            <label for="quantity">Quantity:</label>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                        </div>

                        <?php if (is_logged_in()): ?>
                            <button type="submit" name="add_to_cart" class="btn btn-large">Add to Cart</button>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-large">Login to Add to Cart</a>
                        <?php endif; ?>
                    </form>
                    <?php endif; ?>
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