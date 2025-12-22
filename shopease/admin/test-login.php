<?php
/**
 * Admin Login Debug Page
 * Access: http://localhost/shopease/admin/test-login.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../php/config.php';
require_once '../php/functions.php';

echo "<h1>Admin Login Diagnostic</h1>";
echo "<pre>";

// Test 1: Check admin table
echo "=== Test 1: Check Admin Table ===\n";
$result = $conn->query("SELECT id, username, email, LEFT(password, 20) as pass_preview FROM admins");
if ($result->num_rows > 0) {
    echo "✅ Found " . $result->num_rows . " admin(s)\n\n";
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . "\n";
        echo "Username: " . $row['username'] . "\n";
        echo "Email: " . $row['email'] . "\n";
        echo "Password (first 20 chars): " . $row['pass_preview'] . "...\n\n";
    }
} else {
    echo "❌ No admins found in database\n\n";
}

echo "=== Test 2: Test Password Hash ===\n";
$test_password = 'admin123';
$test_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

echo "Test Password: $test_password\n";
echo "Expected Hash: $test_hash\n";

if (password_verify($test_password, $test_hash)) {
    echo "✅ Password verification working correctly\n\n";
} else {
    echo "❌ Password verification failed\n\n";
}

// Test 3: Check actual admin password
echo "=== Test 3: Check Database Admin Password ===\n";
$stmt = $conn->prepare("SELECT username, password FROM admins WHERE username = 'admin'");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    echo "Username: " . $admin['username'] . "\n";
    echo "Stored Hash: " . $admin['password'] . "\n\n";
    
    if (password_verify('admin123', $admin['password'])) {
        echo "✅ Password 'admin123' matches stored hash\n\n";
    } else {
        echo "❌ Password 'admin123' does NOT match stored hash\n";
        echo "⚠️ You need to reset the password\n\n";
    }
} else {
    echo "❌ Admin user 'admin' not found\n\n";
}

// Test 4: Session check
echo "=== Test 4: Session Check ===\n";
echo "Session ID: " . session_id() . "\n";
echo "Admin logged in: " . (is_admin_logged_in() ? 'Yes' : 'No') . "\n";
if (isset($_SESSION['admin_id'])) {
    echo "Admin ID: " . $_SESSION['admin_id'] . "\n";
    echo "Admin Username: " . $_SESSION['admin_username'] . "\n";
}
echo "\n";

// Test 5: Manual login test
echo "=== Test 5: Manual Login Test ===\n";
if (isset($_POST['test_login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    echo "Attempting login with:\n";
    echo "Username: $username\n";
    echo "Password: $password\n\n";
    
    $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        echo "✅ Admin user found\n";
        $admin = $result->fetch_assoc();
        
        if (password_verify($password, $admin['password'])) {
            echo "✅ Password verified successfully\n";
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            echo "✅ Session variables set\n";
            echo "<a href='index.php'>Go to Admin Dashboard</a>\n";
        } else {
            echo "❌ Password verification failed\n";
            echo "Expected: admin123\n";
            echo "You entered: $password\n";
        }
    } else {
        echo "❌ Admin user not found\n";
    }
    echo "\n";
}

echo "</pre>";

// Test form
?>
<h2>Test Admin Login</h2>
<form method="POST">
    <div style="margin-bottom: 10px;">
        <label>Username:</label><br>
        <input type="text" name="username" value="admin" required>
    </div>
    <div style="margin-bottom: 10px;">
        <label>Password:</label><br>
        <input type="text" name="password" value="admin123" required>
    </div>
    <button type="submit" name="test_login">Test Login</button>
</form>

<hr>
<h2>Reset Admin Password</h2>
<form method="POST" action="reset-admin-password.php">
    <p>Click to reset admin password to: <strong>admin123</strong></p>
    <button type="submit" name="reset_password">Reset Password</button>
</form>

<hr>
<p><a href="login.php">Back to Admin Login</a></p>