<?php
session_start();

// If already logged in, redirect to the appropriate dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: ../admin/dashboard.php");
        exit();
    } elseif ($_SESSION['role'] === 'employee') {
        header("Location: ../employee/dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Real Estate Lead Management</title>
    <link rel="stylesheet" href="../public/css/style.css"> <!-- Include your CSS here -->
</head>
<body>

    <div class="login-container">
        <h2>Login to Your Account</h2>

        <!-- Check for login errors -->
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
                <p>Invalid credentials. Please try again.</p>
            </div>
        <?php endif; ?>

        <form action="../process/login_process.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required placeholder="Enter your email">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required placeholder="Enter your password">
            </div>

            <button type="submit" class="btn">Login</button>

            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </form>
    </div>

</body>
</html>
