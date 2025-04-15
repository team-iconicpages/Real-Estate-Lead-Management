<?php
// Include database connection file
require '../config/db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Get the form data
$lead_id = $_POST['lead_id'];
$followup_note = $_POST['followup_note'];
$followup_date = $_POST['followup_date'];
$user_id = $_SESSION['user_id']; // User ID from session (the employee performing the follow-up)

// Check if all required data is provided
if (empty($lead_id) || empty($followup_note) || empty($followup_date)) {
    header("Location: ../employee/my_leads.php?error=missing_data");
    exit();
}

// Prepare SQL query to insert the follow-up into the database
$stmt = $pdo->prepare("INSERT INTO lead_followups (lead_id, user_id, note, followup_date) 
                       VALUES (?, ?, ?, ?)");

try {
    // Execute the prepared statement
    $stmt->execute([$lead_id, $user_id, $followup_note, $followup_date]);

    // Redirect back to the employee's lead management page with success
    header("Location: ../employee/my_leads.php?success=followup_added");
    exit();

} catch (PDOException $e) {
    // If there's a database error, redirect with an error message
    header("Location: ../employee/my_leads.php?error=db_error");
    exit();
}
?>
