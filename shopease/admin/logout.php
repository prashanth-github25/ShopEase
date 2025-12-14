<?php
/**
 * ShopEase - Admin Logout
 * Destroys admin session and redirects to admin login
 */

session_start();
session_destroy();
header("Location: login.php");
exit();
?>