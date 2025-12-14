<?php
/**
 * ShopEase - User Logout
 * Destroys user session and redirects to homepage
 */

session_start();
session_destroy();
header("Location: index.php");
exit();
?>