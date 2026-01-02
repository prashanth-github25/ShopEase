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
    
    // Keep existing image by default
    $image_name = $product['image'];
    $upload_error = false;
    
    // Check if new image is uploaded
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['product_image'];
        
        // Get file info
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_size = $file['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Allowed extensions
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        // Validation
        if (!in_array($file_ext, $allowed_extensions)) {
            show_message('Invalid image format. Only JPG, JPEG, PNG, GIF allowed.', 'error');
            $upload_error = true;
        }
        
        // Check file size (max 5MB)
        if ($file_size > 5 * 1024 * 1024) {
            show_message('Image size too large. Maximum 5MB allowed.', 'error');
            $upload_error = true;
        }
        
        if (!$upload_error) {
            // Generate unique filename
            $new_file_name = 'product_' . uniqid() . '.' . $file_ext;
            
            // Upload path
            $upload_path = '../images/products/' . $new_file_name;
            
            // Move uploaded file
            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Delete old image if exists
                if ($product['image'] && file_exists('../images/products/' . $product['image'])) {
                    unlink('../images/products/' . $product['image']);
                }
                
                $image_name = $new_file_name;
            } else {
                show_message('Failed to upload image. Check folder permissions.', 'error');
                $upload_error = true;
            }
        }
    }
    
    // Update product only if no upload errors
    if (!$upload_error) {
        $stmt = $conn->prepare("UPDATE products SET category_id = ?, name = ?, description = ?, price = ?, stock = ?, image = ?, featured = ? WHERE id = ?");
        $stmt->bind_param("issdissi", $category_id, $name, $description, $price, $stock, $image_name, $featured, $product_id);
        
        if ($stmt->execute()) {
            show_message('Product updated successfully', 'success');
            redirect('products.php');
        } else {
            show_message('Failed to update product', 'error');
        }
    }
}

$message = get_message();
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

        <?php if ($message): ?>
        <div class="alert alert-<?php echo $message['type']; ?>">
            <?php echo $message['message']; ?>
        </div>
        <?php endif; ?>

        <div class="admin-section">
            <form action="" method="POST" enctype="multipart/form-data" class="admin-form">
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

                <!-- CURRENT IMAGE PREVIEW -->
                <div class="form-group">
                    <label>Current Image</label>
                    <div style="margin: 1rem 0;">
                        <?php if ($product['image'] && file_exists('../images/products/' . $product['image'])): ?>
                            <img src="../images/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                 alt="Current product image" 
                                 style="max-width: 200px; border-radius: 8px; border: 2px solid #E0E0E0;">
                            <p style="color: #666; margin-top: 0.5rem; font-size: 0.9rem;">
                                Current: <?php echo htmlspecialchars($product['image']); ?>
                            </p>
                        <?php else: ?>
                            <img src="../images/placeholder.png" 
                                 alt="No image" 
                                 style="max-width: 200px; border-radius: 8px; border: 2px solid #E0E0E0;">
                            <p style="color: #999; margin-top: 0.5rem; font-size: 0.9rem;">No image uploaded</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- NEW IMAGE UPLOAD -->
                <div class="form-group">
                    <label for="product_image">Upload New Image (Optional)</label>
                    <input type="file" id="product_image" name="product_image" accept="image/jpeg,image/jpg,image/png,image/gif">
                    <small style="color: #666; display: block; margin-top: 0.5rem;">
                        Leave empty to keep current image | Allowed: JPG, JPEG, PNG, GIF | Max: 5MB
                    </small>
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