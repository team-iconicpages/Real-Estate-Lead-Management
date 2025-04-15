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
    header("Location: ../leads/list.php");
    exit();
}

$lead_id = $_GET['id'];

// Fetch lead details
$stmt = $pdo->prepare("SELECT leads.id, leads.full_name, leads.email, leads.phone, leads.property_interest, leads.budget, leads.source, leads.notes, 
                              leads.status_id, lead_status.status_name, users.name AS assigned_to 
                       FROM leads 
                       LEFT JOIN lead_status ON leads.status_id = lead_status.id
                       LEFT JOIN users ON leads.assigned_to = users.id
                       WHERE leads.id = ?");
$stmt->execute([$lead_id]);
$lead = $stmt->fetch(PDO::FETCH_ASSOC);

// If lead doesn't exist
if (!$lead) {
    header("Location: ../leads/list.php");
    exit();
}

// Fetch all follow-ups related to this lead
$stmt = $pdo->prepare("SELECT lead_followups.id, lead_followups.note, lead_followups.followup_date, users.name AS followup_by 
                       FROM lead_followups 
                       LEFT JOIN users ON lead_followups.user_id = users.id
                       WHERE lead_followups.lead_id = ?");
$stmt->execute([$lead_id]);
$followups = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Lead - <?php echo htmlspecialchars($lead['full_name']); ?></title>
</head>
<body>
    <header>
        <h1>Lead Details</h1>
        <nav>
            <a href="../dashboard.php">Dashboard</a> |
            <a href="list.php">Leads List</a> |
            <a href="../manage_users.php">Manage Users</a> |
            <a href="../../auth/logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <section>
            <h2>Lead Information</h2>
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($lead['full_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($lead['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($lead['phone']); ?></p>
            <p><strong>Property Interest:</strong> <?php echo htmlspecialchars($lead['property_interest']); ?></p>
            <p><strong>Budget:</strong> <?php echo htmlspecialchars($lead['budget']); ?></p>
            <p><strong>Source:</strong> <?php echo htmlspecialchars($lead['source']); ?></p>
            <p><strong>Notes:</strong> <?php echo nl2br(htmlspecialchars($lead['notes'])); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($lead['status_name']); ?></p>
            <p><strong>Assigned To:</strong> <?php echo $lead['assigned_to'] ? htmlspecialchars($lead['assigned_to']) : 'Not Assigned'; ?></p>
        </section>

        <section>
            <h2>Follow-ups</h2>
            <?php if (count($followups) > 0): ?>
                <table border="1" cellpadding="10" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Follow-up Date</th>
                            <th>Follow-up By</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($followups as $followup): ?>
                            <tr>
                                <td><?php echo $followup['followup_date']; ?></td>
                                <td><?php echo htmlspecialchars($followup['followup_by']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($followup['note'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No follow-ups for this lead.</p>
            <?php endif; ?>
        </section>

        <section>
            <a href="edit.php?id=<?php echo $lead['id']; ?>">Edit Lead</a> | 
            <a href="assign.php?id=<?php echo $lead['id']; ?>">Assign Lead</a>
        </section>
    </main>
</body>
</html>
