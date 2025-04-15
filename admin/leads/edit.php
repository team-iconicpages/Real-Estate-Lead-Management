<?php
// Include the database connection file
require '../../config/db.php';
session_start();

// Check if the user is logged in and has the 'admin' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}

// Check if the lead ID is provided
if (!isset($_GET['id'])) {
    header("Location: ../my_leads.php");
    exit();
}

$lead_id = $_GET['id'];

// Fetch lead details
$stmt = $pdo->prepare("SELECT * FROM leads WHERE id = ?");
$stmt->execute([$lead_id]);
$lead = $stmt->fetch(PDO::FETCH_ASSOC);

// If lead doesn't exist
if (!$lead) {
    header("Location: ../my_leads.php");
    exit();
}

// Fetch all lead statuses for dropdown
$stmt = $pdo->prepare("SELECT * FROM lead_status");
$stmt->execute();
$statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all employees to assign leads
$stmt = $pdo->prepare("SELECT id, name FROM users WHERE role = 'employee'");
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $property_interest = $_POST['property_interest'];
    $budget = $_POST['budget'];
    $source = $_POST['source'];
    $notes = $_POST['notes'];
    $status_id = $_POST['status_id'];
    $assigned_to = $_POST['assigned_to'];

    // Update the lead details in the database
    $stmt = $pdo->prepare("UPDATE leads SET full_name = ?, email = ?, phone = ?, property_interest = ?, budget = ?, source = ?, notes = ?, status_id = ?, assigned_to = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->execute([$full_name, $email, $phone, $property_interest, $budget, $source, $notes, $status_id, $assigned_to, $lead_id]);

    // Redirect to the leads list page after successful update
    header("Location: ../my_leads.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lead</title>
</head>
<body>
    <header>
        <h1>Edit Lead</h1>
        <nav>
            <a href="../dashboard.php">Dashboard</a> |
            <a href="my_leads.php">Leads</a> |
            <a href="../manage_users.php">Manage Users</a> |
            <a href="../../auth/logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <section>
            <h2>Edit Lead Information</h2>
            <form method="POST" action="">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($lead['full_name']); ?>" required><br><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($lead['email']); ?>"><br><br>

                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($lead['phone']); ?>"><br><br>

                <label for="property_interest">Property Interest:</label>
                <input type="text" id="property_interest" name="property_interest" value="<?php echo htmlspecialchars($lead['property_interest']); ?>"><br><br>

                <label for="budget">Budget:</label>
                <input type="text" id="budget" name="budget" value="<?php echo htmlspecialchars($lead['budget']); ?>"><br><br>

                <label for="source">Source:</label>
                <input type="text" id="source" name="source" value="<?php echo htmlspecialchars($lead['source']); ?>"><br><br>

                <label for="notes">Notes:</label><br>
                <textarea id="notes" name="notes" rows="4" cols="50"><?php echo htmlspecialchars($lead['notes']); ?></textarea><br><br>

                <label for="status_id">Status:</label>
                <select id="status_id" name="status_id" required>
                    <?php foreach ($statuses as $status): ?>
                        <option value="<?php echo $status['id']; ?>" <?php echo ($lead['status_id'] == $status['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($status['status_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select><br><br>

                <label for="assigned_to">Assigned To:</label>
                <select id="assigned_to" name="assigned_to" required>
                    <option value="">Select Employee</option>
                    <?php foreach ($employees as $employee): ?>
                        <option value="<?php echo $employee['id']; ?>" <?php echo ($lead['assigned_to'] == $employee['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($employee['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select><br><br>

                <button type="submit">Update Lead</button>
            </form>
        </section>
    </main>
</body>
</html>
