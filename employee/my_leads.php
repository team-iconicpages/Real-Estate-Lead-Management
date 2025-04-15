<?php
require '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employee') {
    header("Location: ../auth/login.php");
    exit();
}

$employee_id = $_SESSION['user_id'];

// Fetch leads assigned to the employee
$assigned_stmt = $pdo->prepare("SELECT leads.id, leads.full_name, leads.phone, lead_status.status_name 
                                FROM leads 
                                JOIN lead_status ON leads.status_id = lead_status.id 
                                WHERE leads.assigned_to = ? 
                                ORDER BY leads.created_at DESC");
$assigned_stmt->execute([$employee_id]);
$assigned_leads = $assigned_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch leads created by the employee
$created_stmt = $pdo->prepare("SELECT leads.id, leads.full_name, leads.phone, lead_status.status_name 
                               FROM leads 
                               JOIN lead_status ON leads.status_id = lead_status.id 
                               WHERE leads.created_by = ? 
                               ORDER BY leads.created_at DESC");
$created_stmt->execute([$employee_id]);
$created_leads = $created_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle messages
$message = '';
if (isset($_GET['success']) && $_GET['success'] == 'status_updated') {
    $message = "Lead status updated successfully!";
}
if (isset($_GET['error']) && $_GET['error'] == 'lead_not_found') {
    $message = "Lead not found!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Leads</title>
</head>
<body>
    <header>
        <h1>My Leads</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a> |
            <a href="add_lead.php">Add Lead</a> |
            <a href="../auth/logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <?php if ($message): ?>
            <p style="color: green;"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <section>
            <h2>📌 Leads Assigned to Me</h2>
            <?php if (count($assigned_leads) > 0): ?>
                <table border="1" cellpadding="10">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assigned_leads as $lead): ?>
                            <tr>
                                <td><?= htmlspecialchars($lead['full_name']) ?></td>
                                <td><?= htmlspecialchars($lead['phone']) ?></td>
                                <td><?= htmlspecialchars($lead['status_name']) ?></td>
                                <td><a href="lead_detail.php?lead_id=<?= $lead['id'] ?>">View</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No leads have been assigned to you yet.</p>
            <?php endif; ?>
        </section>

        <section style="margin-top: 40px;">
            <h2>📝 Leads Created by Me</h2>
            <?php if (count($created_leads) > 0): ?>
                <table border="1" cellpadding="10">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($created_leads as $lead): ?>
                            <tr>
                                <td><?= htmlspecialchars($lead['full_name']) ?></td>
                                <td><?= htmlspecialchars($lead['phone']) ?></td>
                                <td><?= htmlspecialchars($lead['status_name']) ?></td>
                                <td><a href="lead_detail.php?lead_id=<?= $lead['id'] ?>">View</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>You haven’t created any leads yet.</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
