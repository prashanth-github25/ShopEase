<?php
require_once 'php/config.php';
require_once 'php/functions.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect('index.php');
}

$message = get_message();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ShopEase</title>
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
                        <li><a href="login.php" class="active">Login</a></li>
                        <li><a href="signup.php">Sign Up</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Login Form -->
    <section class="auth-section">
        <div class="container">
            <div class="auth-box">
                <h2>Login to ShopEase</h2>
                
                <?php if ($message): ?>
                <div class="alert alert-<?php echo $message['type']; ?>">
                    <?php echo $message['message']; ?>
                </div>
                <?php endif; ?>

                <form action="php/auth.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <button type="submit" name="login" class="btn btn-block">Login</button>
                </form>

                <div class="auth-footer">
                    <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
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