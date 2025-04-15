<?php
// Include the database connection file
require '../config/db.php';
session_start();

// Check if the user is logged in and has the 'employee' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employee') {
    header("Location: ../auth/login.php");
    exit();
}

// Get the lead ID from the URL (this will be passed as a query parameter)
$lead_id = isset($_GET['lead_id']) ? $_GET['lead_id'] : null;

if (empty($lead_id)) {
    header("Location: ../employee/my_leads.php?error=invalid_lead");
    exit();
}

// Get employee ID from session
$employee_id = $_SESSION['user_id'];

// Fetch the lead details from the database
$stmt = $pdo->prepare("SELECT * FROM leads WHERE id = ? AND assigned_to = ?");
$stmt->execute([$lead_id, $employee_id]);
$lead = $stmt->fetch();

if (!$lead) {
    header("Location: ../employee/my_leads.php?error=lead_not_found");
    exit();
}

// Fetch all statuses from the lead_status table
$stmt = $pdo->prepare("SELECT * FROM lead_status");
$stmt->execute();
$statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle status update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status_id'];

    // Update lead status
    $stmt = $pdo->prepare("UPDATE leads SET status_id = ? WHERE id = ?");
    $stmt->execute([$new_status, $lead_id]);

    // Redirect back to the lead details page
    header("Location: lead_detail.php?lead_id=$lead_id&success=status_updated");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Lead Status</title>
</head>
<body>
    <header>
        <h1>Update Lead Status: <?php echo htmlspecialchars($lead['full_name']); ?></h1>
        <nav>
            <a href="my_leads.php">Back to My Leads</a> |
            <a href="../auth/logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <section class="update-status">
            <h2>Update Lead Status</h2>
            <form action="update_status.php?lead_id=<?php echo $lead_id; ?>" method="POST">
                <label for="status_id">Change Status:</label>
                <select name="status_id" required>
                    <?php
                    // Display all statuses in a dropdown
                    foreach ($statuses as $status) {
                        echo "<option value='" . $status['id'] . "'" . ($status['id'] == $lead['status_id'] ? " selected" : "") . ">" . htmlspecialchars($status['status_name']) . "</option>";
                    }
                    ?>
                </select>
                <button type="submit" name="update_status">Update Status</button>
            </form>

            <?php
            // Success message for status update
            if (isset($_GET['success']) && $_GET['success'] == 'status_updated') {
                echo "<p style='color: green;'>Lead status updated successfully!</p>";
            }
            ?>
        </section>
    </main>
</body>
</html>
