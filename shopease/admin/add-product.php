<?php
require_once '../php/config.php';
require_once '../php/functions.php';

if (!is_admin_logged_in()) {
    redirect('login.php');
}

// Get all products
$products = $conn->query("SELECT p.*, c.name as category_name 
                         FROM products p 
                         JOIN categories c ON p.category_id = c.id 
                         ORDER BY p.created_at DESC")->fetch_all(MYSQLI_ASSOC);

$message = get_message();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - ShopEase Admin</title>
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
            <h1>Manage Products</h1>
            <a href="add-product.php" class="btn">+ Add New Product</a>
        </div>

        <?php if ($message): ?>
        <div class="alert alert-<?php echo $message['type']; ?>">
            <?php echo $message['message']; ?>
        </div>
        <?php endif; ?>

        <div class="admin-section">
            <?php if (empty($products)): ?>
                <p>No products found.</p>
            <?php else: ?>
                <div class="admin-table">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Featured</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                <td><?php echo format_price($product['price']); ?></td>
                                <td><?php echo $product['stock']; ?></td>
                                <td><?php echo $product['featured'] ? '✓' : '✗'; ?></td>
                                <td class="action-buttons">
                                    <a href="edit-product.php?id=<?php echo $product['id']; ?>" class="btn-sm btn-edit">Edit</a>
                                    <a href="delete-product.php?id=<?php echo $product['id']; ?>" class="btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>