<?php
/**
 * ShopEase - Authentication Handler
 * Handles user login and registration
 */

require_once 'config.php';
require_once 'functions.php';

// Handle user registration
if (isset($_POST['signup'])) {
    $name = clean_input($_POST['name']);
    $email = clean_input($_POST['email']);
    $password = $_POST['password'];
    $phone = clean_input($_POST['phone']);
    $address = clean_input($_POST['address']);
    $city = clean_input($_POST['city']);
    $state = clean_input($_POST['state']);
    $pincode = clean_input($_POST['pincode']);
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        show_message('Invalid email address', 'error');
        redirect('../signup.php');
    }
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        show_message('Email already registered', 'error');
        redirect('../signup.php');
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, address, city, state, pincode) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $name, $email, $hashed_password, $phone, $address, $city, $state, $pincode);
    
    if ($stmt->execute()) {
        show_message('Registration successful! Please login.', 'success');
        redirect('../login.php');
    } else {
        show_message('Registration failed. Please try again.', 'error');
        redirect('../signup.php');
    }
}

// Handle user login
if (isset($_POST['login'])) {
    $email = clean_input($_POST['email']);
    $password = $_POST['password'];
    
    // Fetch user
    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            redirect('../index.php');
        } else {
            show_message('Invalid email or password', 'error');
            redirect('../login.php');
        }
    } else {
        show_message('Invalid email or password', 'error');
        redirect('../login.php');
    }
}

// Handle admin login
if (isset($_POST['admin_login'])) {
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];
    
    // Fetch admin
    $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            redirect('../admin/index.php');
        } else {
            show_message('Invalid username or password', 'error');
            redirect('../admin/login.php');
        }
    } else {
        show_message('Invalid username or password', 'error');
        redirect('../admin/login.php');
    }
}
?>