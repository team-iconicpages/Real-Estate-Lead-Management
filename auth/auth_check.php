<?php
session_start();

// Check if the user is logged in and has a valid session
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    // Redirect to login page if no valid session exists
    header("Location: login.php");
    exit();
}

// Optional: Check if the user has the correct role to access specific pages (Admin or Employee)
// Example for admin-only pages:
if ($_SESSION['role'] !== 'admin') {
    // Redirect to employee dashboard if user is not an admin
    header("Location: ../employee/dashboard.php");
    exit();
}
?>
