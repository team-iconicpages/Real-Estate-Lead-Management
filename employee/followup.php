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

// Fetch the follow-up history for this lead
$stmt = $pdo->prepare("SELECT * FROM lead_followups WHERE lead_id = ? ORDER BY followup_date DESC");
$stmt->execute([$lead_id]);
$followups = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle new follow-up form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note = $_POST['note'];
    $followup_date = $_POST['followup_date'];

    // Validate the inputs
    if (empty($note) || empty($followup_date)) {
        header("Location: followup.php?lead_id=$lead_id&error=missing_data");
        exit();
    }

    // Insert the new follow-up into the database
    $stmt = $pdo->prepare("INSERT INTO lead_followups (lead_id, user_id, note, followup_date) VALUES (?, ?, ?, ?)");
    try {
        $stmt->execute([$lead_id, $employee_id, $note, $followup_date]);
        header("Location: followup.php?lead_id=$lead_id&success=followup_added");
        exit();
    } catch (PDOException $e) {
        header("Location: followup.php?lead_id=$lead_id&error=db_error");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Follow-up for Lead: <?php echo htmlspecialchars($lead['full_name']); ?></title>
</head>
<body>
    <header>
        <h1>Follow-up for Lead: <?php echo htmlspecialchars($lead['full_name']); ?></h1>
        <nav>
            <a href="my_leads.php">Back to My Leads</a> |
            <a href="../auth/logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <section class="followup-history">
            <h2>Follow-up History</h2>
            <?php if (!empty($followups)): ?>
                <ul>
                    <?php foreach ($followups as $followup): ?>
                        <li>
                            <strong><?php echo date('d M Y', strtotime($followup['followup_date'])); ?></strong>: 
                            <?php echo htmlspecialchars($followup['note']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No follow-ups yet for this lead.</p>
            <?php endif; ?>
        </section>

        <section class="add-followup">
            <h2>Add Follow-up</h2>
            <form action="followup.php?lead_id=<?php echo $lead_id; ?>" method="POST">
                <label for="followup_date">Follow-up Date:</label>
                <input type="date" name="followup_date" required>
                
                <label for="note">Note:</label>
                <textarea name="note" rows="4" required></textarea>

                <button type="submit">Add Follow-up</button>
            </form>

            <?php
            // Display error/success messages if any
            if (isset($_GET['error'])) {
                if ($_GET['error'] == 'missing_data') {
                    echo "<p style='color: red;'>Please fill in all fields.</p>";
                } elseif ($_GET['error'] == 'db_error') {
                    echo "<p style='color: red;'>There was an error saving the follow-up. Please try again.</p>";
                }
            }

            if (isset($_GET['success']) && $_GET['success'] == 'followup_added') {
                echo "<p style='color: green;'>Follow-up added successfully!</p>";
            }
            ?>
        </section>
    </main>
</body>
</html>
