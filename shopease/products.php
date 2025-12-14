<?php
require_once 'php/config.php';
require_once 'php/functions.php';

$categories = get_categories($conn);
$cart_count = is_logged_in() ? get_cart_count($conn, $_SESSION['user_id']) : 0;

// Get products based on category filter
$category_id = isset($_GET['category']) ? intval($_GET['category']) : null;

if ($category_id) {
    $products = get_products_by_category($conn, $category_id);
    $stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $category_info = $result->fetch_assoc();
    $page_title = $category_info['name'] . ' Products';
} else {
    $result = $conn->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC");
    $products = $result->fetch_all(MYSQLI_ASSOC);
    $page_title = 'All Products';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - ShopEase</title>
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
                        <li><a href="products.php" class="active">Products</a></li>
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

    <!-- Products Section -->
    <section class="products-section">
        <div class="container">
            <div class="products-header">
                <h2><?php echo $page_title; ?></h2>
                <div class="category-filter">
                    <a href="products.php" class="filter-btn <?php echo !$category_id ? 'active' : ''; ?>">All</a>
                    <?php foreach ($categories as $cat): ?>
                    <a href="products.php?category=<?php echo $cat['id']; ?>" class="filter-btn <?php echo $category_id == $cat['id'] ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if (empty($products)): ?>
                <div class="no-products">
                    <p>No products found in this category.</p>
                </div>
            <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <span class="product-placeholder">🖼️</span>
                    </div>
                    <div class="product-info">
                        <span class="category-tag"><?php echo htmlspecialchars($product['category_name']); ?></span>
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-description"><?php echo htmlspecialchars(substr($product['description'], 0, 80)) . '...'; ?></p>
                        <div class="stock-info">
                            <?php if ($product['stock'] > 0): ?>
                                <span class="in-stock">In Stock: <?php echo $product['stock']; ?></span>
                            <?php else: ?>
                                <span class="out-of-stock">Out of Stock</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-footer">
                            <span class="price"><?php echo format_price($product['price']); ?></span>
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn btn-sm">View Details</a>
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