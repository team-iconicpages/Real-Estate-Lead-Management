<?php
session_start();

// If user is already logged in, redirect to their dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
        exit();
    } elseif ($_SESSION['role'] === 'employee') {
        header("Location: employee/dashboard.php");
        exit();
    }
}

// If not logged in, redirect to login page
header("Location: auth/login.php");
exit();
?>
