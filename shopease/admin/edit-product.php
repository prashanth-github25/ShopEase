<?php
require_once '../php/config.php';
require_once '../php/functions.php';

if (!is_admin_logged_in()) {
    redirect('login.php');
}

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = get_product($conn, $product_id);

if (!$product) {
    show_message('Product not found', 'error');
    redirect('products.php');
}

$categories = get_categories($conn);

// Handle form submission
if (isset($_POST['update_product'])) {
    $name = clean_input($_POST['name']);
    $category_id = intval($_POST['category_id']);
    $description = clean_input($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    // Update product
    $stmt = $conn->prepare("UPDATE products SET category_id = ?, name = ?, description = ?, price = ?, stock = ?, featured = ? WHERE id = ?");
    $stmt->bind_param("issdiis", $category_id, $name, $description, $price, $stock, $featured, $product_id);
    
    if ($stmt->execute()) {
        show_message('Product updated successfully', 'success');
        redirect('products.php');
    } else {
        show_message('Failed to update product', 'error');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - ShopEase Admin</title>
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
            <a href="products.php" class="active">Products</a>
            <a href="orders.php">Orders</a>
            <a href="logout.php">Logout</a>
        </nav>
    </div>

    <!-- Admin Content -->
    <div class="admin-content">
        <div class="admin-header">
            <h1>Edit Product</h1>
            <a href="products.php" class="btn-secondary">← Back to Products</a>
        </div>

        <div class="admin-section">
            <form action="" method="POST" class="admin-form">
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id" required>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Price (₹)</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo $product['price']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="stock">Stock Quantity</label>
                        <input type="number" id="stock" name="stock" min="0" value="<?php echo $product['stock']; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="featured" <?php echo $product['featured'] ? 'checked' : ''; ?>>
                        Mark as Featured Product
                    </label>
                </div>

                <button type="submit" name="update_product" class="btn btn-large">Update Product</button>
            </form>
        </div>
    </div>
</body>
</html>