<?php
require_once 'php/config.php';
require_once 'php/functions.php';

$categories = get_categories($conn);
$featured_products = get_featured_products($conn, 6);
$cart_count = is_logged_in() ? get_cart_count($conn, $_SESSION['user_id']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopEase - Your One-Stop Shopping Destination</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>ShopEase</h1>
                </div>
                <nav>
                    <ul>
                        <li><a href="index.php" class="active">Home</a></li>
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

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h2>Welcome to ShopEase</h2>
                <p>Discover amazing products at unbeatable prices</p>
                <a href="products.php" class="btn">Shop Now</a>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories">
        <div class="container">
            <h2 class="section-title">Shop by Category</h2>
            <div class="category-grid">
                <?php foreach ($categories as $category): ?>
                <a href="products.php?category=<?php echo $category['id']; ?>" class="category-card">
                    <div class="category-image">
                        <span class="category-icon">📦</span>
                    </div>
                    <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                    <p><?php echo htmlspecialchars($category['description']); ?></p>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="featured-products">
        <div class="container">
            <h2 class="section-title">Best Deals</h2>
            <div class="product-grid">
                <?php foreach ($featured_products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <span class="product-placeholder">🖼️</span>
                    </div>
                    <div class="product-info">
                        <span class="category-tag"><?php echo htmlspecialchars($product['category_name']); ?></span>
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-description"><?php echo htmlspecialchars(substr($product['description'], 0, 80)) . '...'; ?></p>
                        <div class="product-footer">
                            <span class="price"><?php echo format_price($product['price']); ?></span>
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn btn-sm">View Details</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
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