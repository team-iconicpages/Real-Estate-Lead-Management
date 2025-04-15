<?php
// Include the database connection file
require '../../config/db.php';
session_start();

// Check if the user is logged in and has the 'admin' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}

// Fetch all leads
$stmt = $pdo->prepare("SELECT id, full_name, email, phone, property_interest, assigned_to FROM leads WHERE assigned_to IS NULL ORDER BY created_at DESC");
$stmt->execute();
$leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all employees
$stmt = $pdo->prepare("SELECT id, name FROM users WHERE role = 'employee'");
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lead_id = $_POST['lead_id'];
    $assigned_to = $_POST['assigned_to'];

    // Update the lead's assigned employee
    $stmt = $pdo->prepare("UPDATE leads SET assigned_to = ? WHERE id = ?");
    $stmt->execute([$assigned_to, $lead_id]);

    // Redirect to the leads list page after successful assignment
    header("Location: ../my_leads.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Lead</title>
</head>
<body>
    <header>
        <h1>Assign Lead</h1>
        <nav>
            <a href="../dashboard.php">Dashboard</a> |
            <a href="my_leads.php">Leads</a> |
            <a href="../manage_users.php">Manage Users</a> |
            <a href="../../auth/logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <section>
            <h2>Select Lead and Assign to Employee</h2>
            <form method="POST" action="">
                <label for="lead_id">Lead:</label>
                <select id="lead_id" name="lead_id" required>
                    <option value="">Select Lead</option>
                    <?php foreach ($leads as $lead): ?>
                        <option value="<?php echo $lead['id']; ?>"><?php echo htmlspecialchars($lead['full_name']); ?> (<?php echo htmlspecialchars($lead['property_interest']); ?>)</option>
                    <?php endforeach; ?>
                </select><br><br>

                <label for="assigned_to">Assign To:</label>
                <select id="assigned_to" name="assigned_to" required>
                    <option value="">Select Employee</option>
                    <?php foreach ($employees as $employee): ?>
                        <option value="<?php echo $employee['id']; ?>"><?php echo htmlspecialchars($employee['name']); ?></option>
                    <?php endforeach; ?>
                </select><br><br>

                <button type="submit">Assign Lead</button>
            </form>
        </section>
    </main>
</body>
</html>
