<?php
// Include the database connection file
require '../config/db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Get the form data (lead ID and updated details)
$lead_id = $_POST['lead_id'];
$full_name = $_POST['full_name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$property_interest = $_POST['property_interest'];
$budget = $_POST['budget'];
$source = $_POST['source'];
$notes = $_POST['notes'];
$assigned_to = $_POST['assigned_to'] ?? null;  // If no one is assigned, it will be NULL
$status_id = $_POST['status_id']; // Lead status

// Validate if required fields are filled
if (empty($lead_id) || empty($full_name) || empty($property_interest)) {
    header("Location: ../employee/edit_lead.php?lead_id=$lead_id&error=missing_data");
    exit();
}

// Prepare SQL query to update the lead details in the database
$stmt = $pdo->prepare("UPDATE leads 
                       SET full_name = ?, email = ?, phone = ?, property_interest = ?, 
                           budget = ?, source = ?, notes = ?, assigned_to = ?, status_id = ?, updated_at = NOW()
                       WHERE id = ?");

try {
    // Execute the prepared statement
    $stmt->execute([$full_name, $email, $phone, $property_interest, $budget, $source, $notes, $assigned_to, $status_id, $lead_id]);

    // Redirect to the leads page with a success message
    header("Location: ../employee/my_leads.php?success=lead_updated");
    exit();

} catch (PDOException $e) {
    // If there's an error in the database, redirect with an error message
    header("Location: ../employee/edit_lead.php?lead_id=$lead_id&error=db_error");
    exit();
}
?>
