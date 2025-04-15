<?php
// Include the database connection file
require '../config/db.php';
session_start();

// Check if the user is logged in and has the 'admin' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch lead data for reporting
$stmt = $pdo->prepare("SELECT l.id, l.full_name, l.email, l.phone, l.property_interest, l.budget, l.source, ls.status_name, u.name AS assigned_to, l.created_at FROM leads l JOIN lead_status ls ON l.status_id = ls.id LEFT JOIN users u ON l.assigned_to = u.id ORDER BY l.created_at DESC");
$stmt->execute();
$leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user data for reporting
$stmt = $pdo->prepare("SELECT id, name, email, phone, role, created_at FROM users ORDER BY created_at DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
</head>
<body>
    <header>
        <h1>Admin Reports</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a> |
            <a href="my_leads.php">Leads</a> |
            <a href="manage_users.php">Manage Users</a> |
            <a href="../auth/logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <section class="reports">
            <h2>Leads Report</h2>
            <table>
                <thead>
                    <tr>
                        <th>Lead ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Property Interest</th>
                        <th>Budget</th>
                        <th>Source</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leads as $lead): ?>
                    <tr>
                        <td><?php echo $lead['id']; ?></td>
                        <td><?php echo htmlspecialchars($lead['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($lead['email']); ?></td>
                        <td><?php echo htmlspecialchars($lead['phone']); ?></td>
                        <td><?php echo htmlspecialchars($lead['property_interest']); ?></td>
                        <td><?php echo htmlspecialchars($lead['budget']); ?></td>
                        <td><?php echo htmlspecialchars($lead['source']); ?></td>
                        <td><?php echo htmlspecialchars($lead['status_name']); ?></td>
                        <td><?php echo htmlspecialchars($lead['assigned_to']); ?></td>
                        <td><?php echo date("Y-m-d H:i:s", strtotime($lead['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h2>User Report</h2>
            <table>
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td><?php echo date("Y-m-d H:i:s", strtotime($user['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
