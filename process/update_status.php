<?php
// Include the database connection file
require '../config/db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Get the form data (lead ID and new status ID)
$lead_id = $_POST['lead_id'];
$status_id = $_POST['status_id'];

// Validate if the required data is provided
if (empty($lead_id) || empty($status_id)) {
    header("Location: ../employee/my_leads.php?error=missing_data");
    exit();
}

// Prepare SQL query to update the status of the lead
$stmt = $pdo->prepare("UPDATE leads SET status_id = ?, updated_at = NOW() WHERE id = ?");

try {
    // Execute the prepared statement
    $stmt->execute([$status_id, $lead_id]);

    // Redirect to the leads page with a success message
    header("Location: ../employee/my_leads.php?success=status_updated");
    exit();

} catch (PDOException $e) {
    // If there's an error in the database, redirect with an error message
    header("Location: ../employee/my_leads.php?error=db_error");
    exit();
}
?>
