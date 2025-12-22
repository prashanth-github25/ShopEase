<?php
require_once '../php/config.php';
require_once '../php/functions.php';

// Redirect if already logged in
if (is_admin_logged_in()) {
    redirect('index.php');
}

$message = get_message();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - ShopEase</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="admin-body">
    <div class="admin-login-container">
        <div class="admin-login-box">
            <h1>ShopEase Admin</h1>
            <h2>Login to Dashboard</h2>

            <?php if ($message): ?>
            <div class="alert alert-<?php echo $message['type']; ?>">
                <?php echo $message['message']; ?>
            </div>
            <?php endif; ?>

            <form action="../php/auth.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autofocus autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="off">
                </div>

                <button type="submit" name="admin_login" class="btn btn-block">Login</button>
            </form>

            <div class="admin-footer">
                <p><a href="../index.php">← Back to Website</a></p>
            </div>
        </div>
    </div>
</body>
</html>