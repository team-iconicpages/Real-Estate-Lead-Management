<?php
// Include the database connection file
require '../config/db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Get the form data
$full_name = $_POST['full_name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$property_interest = $_POST['property_interest'];
$budget = $_POST['budget'];
$source = $_POST['source'];
$notes = $_POST['notes'];
$assigned_to = $_POST['assigned_to'] ?? null;  // If no one is assigned, it will be NULL
$created_by = $_SESSION['user_id'];  // User ID of the person adding the lead

// Check if required fields are filled
if (empty($full_name) || empty($property_interest)) {
    header("Location: ../employee/add_lead.php?error=missing_data");
    exit();
}

// Prepare SQL query to insert the lead into the database
$stmt = $pdo->prepare("INSERT INTO leads (full_name, email, phone, property_interest, budget, source, notes, assigned_to, created_by) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

try {
    // Execute the prepared statement
    $stmt->execute([$full_name, $email, $phone, $property_interest, $budget, $source, $notes, $assigned_to, $created_by]);

    // Redirect to the employee's lead management page with a success message
    header("Location: ../employee/my_leads.php?success=lead_added");
    exit();

} catch (PDOException $e) {
    // If there's an error in the database, redirect with an error message
    header("Location: ../employee/add_lead.php?error=db_error");
    exit();
}
?>
