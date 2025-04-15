<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real Estate Lead Management System</title>

    <!-- Link to your main stylesheet -->
    <link rel="stylesheet" href="../public/css/style.css">  <!-- You can replace this with Tailwind or other CSS frameworks -->

    <!-- Optional: Include other meta tags or external resources -->
    <meta name="description" content="Manage real estate leads, status updates, and more with this comprehensive management system.">
    <meta name="author" content="Your Name or Company Name">

    <!-- Include other libraries like jQuery, Bootstrap, or Tailwind CSS if necessary -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">  <!-- Example Google Font -->
    
    <!-- Add additional metadata if needed -->
</head>
<body>

<header>
    <div class="container">
        <nav>
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="../auth/login.php">Login</a></li>
                <?php if (isset($_SESSION['role'])): ?>
                    <li><a href="../auth/logout.php">Logout</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
