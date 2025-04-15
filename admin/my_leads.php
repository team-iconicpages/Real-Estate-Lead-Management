<?php
require '../config/db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];

// Fetch leads created by the current admin
$stmt = $pdo->prepare("SELECT leads.*, lead_status.status_name, users.name AS assigned_to_name 
                       FROM leads 
                       LEFT JOIN lead_status ON leads.status_id = lead_status.id
                       LEFT JOIN users ON leads.assigned_to = users.id
                       WHERE leads.created_by = ? 
                       ORDER BY leads.created_at DESC");
$stmt->execute([$admin_id]);
$leads = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Leads</title>
</head>
<body>
    <header>
        <h1>My Created Leads</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a> |
            <a href="leads/list.php">All Leads</a> |
            <a href="leads/add.php">Add Lead</a> |
            <a href="../auth/logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <?php if (count($leads) > 0): ?>
            <table border="1" cellpadding="10" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Phone</th>
                        <th>Budget</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leads as $lead): ?>
                        <tr>
                            <td><?= $lead['id'] ?></td>
                            <td><?= htmlspecialchars($lead['full_name']) ?></td>
                            <td><?= htmlspecialchars($lead['phone']) ?></td>
                            <td><?= htmlspecialchars($lead['budget']) ?></td>
                            <td><?= htmlspecialchars($lead['status_name']) ?></td>
                            <td><?= $lead['assigned_to_name'] ?? 'Unassigned' ?></td>
                            <td><?= $lead['created_at'] ?></td>
                            <td>
                                <a href="leads/view.php?id=<?= $lead['id'] ?>">View</a> | 
                                <a href="leads/edit.php?id=<?= $lead['id'] ?>">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No leads created by you yet.</p>
        <?php endif; ?>
    </main>
</body>
</html>
