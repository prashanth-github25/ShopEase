<?php
/**
 * Reset Admin Password
 * Creates/Updates admin with password: admin123
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../php/config.php';

echo "<h1>Admin Password Reset</h1>";

if (isset($_POST['reset_password'])) {
    
    $username = 'admin';
    $password = 'admin123';
    $email = 'admin@shopease.com';
    
    // Generate new password hash
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    echo "<p>Generated password hash: <code>$hashed_password</code></p>";
    
    // Check if admin exists
    $stmt = $conn->prepare("SELECT id FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update existing admin
        echo "<p>Updating existing admin...</p>";
        $stmt = $conn->prepare("UPDATE admins SET password = ?, email = ? WHERE username = ?");
        $stmt->bind_param("sss", $hashed_password, $email, $username);
        
        if ($stmt->execute()) {
            echo "<p style='color: green;'>✅ Admin password updated successfully!</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to update: " . $conn->error . "</p>";
        }
    } else {
        // Create new admin
        echo "<p>Creating new admin...</p>";
        $stmt = $conn->prepare("INSERT INTO admins (username, password, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashed_password, $email);
        
        if ($stmt->execute()) {
            echo "<p style='color: green;'>✅ Admin created successfully!</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create: " . $conn->error . "</p>";
        }
    }
    
    echo "<h3>Login Credentials:</h3>";
    echo "<ul>";
    echo "<li><strong>Username:</strong> admin</li>";
    echo "<li><strong>Password:</strong> admin123</li>";
    echo "</ul>";
    
    echo "<p><a href='login.php' style='padding: 10px 20px; background: #1E88E5; color: white; text-decoration: none; border-radius: 5px;'>Go to Admin Login</a></p>";
    
} else {
    echo "<p>Access this page from the test-login.php page</p>";
    echo "<p><a href='test-login.php'>Go to Test Login Page</a></p>";
}
?>