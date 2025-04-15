<?php
require '../config/db.php';
session_start();

// Ensure the employee is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: ../auth/login.php");
    exit();
}

$employee_id = $_SESSION['user_id'];
$lead_id = isset($_GET['lead_id']) ? intval($_GET['lead_id']) : 0;

if (!$lead_id) {
    header("Location: my_leads.php?error=invalid_lead");
    exit();
}

// Fetch lead and verify it's assigned to or created by the employee
$stmt = $pdo->prepare("
    SELECT l.*, ls.status_name 
    FROM leads l 
    JOIN lead_status ls ON l.status_id = ls.id 
    WHERE l.id = ? AND (l.assigned_to = ? OR l.created_by = ?)
");
$stmt->execute([$lead_id, $employee_id, $employee_id]);
$lead = $stmt->fetch();

if (!$lead) {
    header("Location: my_leads.php?error=lead_not_found");
    exit();
}

// Update lead status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = intval($_POST['status_id']);
    $update = $pdo->prepare("UPDATE leads SET status_id = ?, updated_at = NOW() WHERE id = ?");
    $update->execute([$new_status, $lead_id]);

    header("Location: lead_detail.php?lead_id=$lead_id&success=status_updated");
    exit();
}

// Fetch follow-ups with user info
$followups = $pdo->prepare("
    SELECT lf.*, u.name AS employee_name 
    FROM lead_followups lf 
    JOIN users u ON lf.user_id = u.id 
    WHERE lf.lead_id = ? 
    ORDER BY lf.followup_date DESC
");
$followups->execute([$lead_id]);
$followupData = $followups->fetchAll(PDO::FETCH_ASSOC);

// Fetch all statuses
$statusStmt = $pdo->query("SELECT * FROM lead_status");
$allStatuses = $statusStmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lead Detail - <?= htmlspecialchars($lead['full_name']) ?></title>
</head>
<body>
    <header>
        <h1>Lead Detail: <?= htmlspecialchars($lead['full_name']) ?></h1>
        <nav>
            <a href="my_leads.php">Back to My Leads</a> |
            <a href="../auth/logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <?php if (isset($_GET['success']) && $_GET['success'] === 'status_updated'): ?>
            <p style="color: green;">✅ Lead status updated successfully!</p>
        <?php endif; ?>

        <section class="lead-info">
            <h2>Lead Information</h2>
            <p><strong>Email:</strong> <?= htmlspecialchars($lead['email']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($lead['phone']) ?></p>
            <p><strong>Property Interest:</strong> <?= htmlspecialchars($lead['property_interest']) ?></p>
            <p><strong>Budget:</strong> <?= htmlspecialchars($lead['budget']) ?></p>
            <p><strong>Source:</strong> <?= htmlspecialchars($lead['source']) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($lead['status_name']) ?></p>
        </section>

        <section class="update-status">
            <h2>Update Lead Status</h2>
            <form method="POST">
                <label for="status_id">Change Status:</label>
                <select name="status_id" required>
                    <?php foreach ($allStatuses as $status): ?>
                        <option value="<?= $status['id'] ?>" <?= $status['id'] == $lead['status_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($status['status_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="update_status">Update</button>
            </form>
        </section>

        <section class="followup-history">
            <h2>Follow-up History</h2>
            <?php if ($followupData): ?>
                <ul>
                    <?php foreach ($followupData as $f): ?>
                        <li>
                            <strong><?= date('d M Y', strtotime($f['followup_date'])) ?> by <?= htmlspecialchars($f['employee_name']) ?>:</strong><br>
                            <?= nl2br(htmlspecialchars($f['note'])) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No follow-ups yet for this lead.</p>
            <?php endif; ?>
        </section>

        <section class="add-followup">
            <h2>Add Follow-up</h2>
            <form action="../employee/followup.php?lead_id=<?= $lead_id ?>" method="POST">
                <label for="followup_date">Follow-up Date:</label>
                <input type="date" name="followup_date" required>

                <label for="note">Note:</label>
                <textarea name="note" rows="4" required></textarea>

                <button type="submit">Add Follow-up</button>
            </form>
        </section>
    </main>
</body>
</html>
