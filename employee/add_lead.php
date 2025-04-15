<?php
require '../config/db.php';
session_start();

// Check if the employee is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Lead</title>
</head>
<body>
    <header>
        <h1>Add New Lead</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a> |
            <a href="my_leads.php">My Leads</a> |
            <a href="../auth/logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <form action="../process/add_lead.php" method="POST">
            <input type="hidden" name="created_by" value="<?= $_SESSION['user_id'] ?>">

            <label>Full Name: <input type="text" name="full_name" required></label><br><br>

            <label>Email: <input type="email" name="email"></label><br><br>

            <label>Phone: <input type="text" name="phone"></label><br><br>

            <label>Property Interest: <input type="text" name="property_interest"></label><br><br>

            <label>Budget: <input type="text" name="budget"></label><br><br>

            <label>Source: <input type="text" name="source"></label><br><br>

            <label>Notes:<br>
                <textarea name="notes" rows="4" cols="40"></textarea>
            </label><br><br>

            <input type="submit" value="Add Lead">
        </form>
    </main>
</body>
</html>
