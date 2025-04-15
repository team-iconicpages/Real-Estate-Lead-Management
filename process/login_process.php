<?php
// Include the database connection file
require '../config/db.php';
session_start();

// Check if the login form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get the form data (email and password)
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate the inputs
    if (empty($email) || empty($password)) {
        header("Location: ../auth/login.php?error=missing_data");
        exit();
    }

    // Prepare SQL query to check the user's credentials
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Check if the user exists and the password is correct
    if ($user && password_verify($password, $user['password'])) {
        // Create session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        // Redirect to the dashboard based on user role
        if ($user['role'] === 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: ../employee/dashboard.php");
        }
        exit();
    } else {
        // Redirect back to the login page with an error message
        header("Location: ../auth/login.php?error=invalid_credentials");
        exit();
    }
} else {
    // If the form is not submitted via POST, redirect to the login page
    header("Location: ../auth/login.php");
    exit();
}
?>
