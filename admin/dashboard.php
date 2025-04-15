<?php
// Include the database connection file
require '../config/db.php';
session_start();

// Check if the user is logged in and has the 'admin' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch total number of leads
$stmt = $pdo->prepare("SELECT COUNT(*) AS total_leads FROM leads");
$stmt->execute();
$total_leads = $stmt->fetch()['total_leads'];

// Fetch total number of users
$stmt = $pdo->prepare("SELECT COUNT(*) AS total_users FROM users");
$stmt->execute();
$total_users = $stmt->fetch()['total_users'];

// Fetch total number of leads by status
$stmt = $pdo->prepare("SELECT ls.status_name, COUNT(l.id) AS total FROM leads l JOIN lead_status ls ON l.status_id = ls.id GROUP BY ls.status_name");
$stmt->execute();
$status_counts = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <header>
        <h1>Welcome, Admin</h1>
        <nav>
            <a href="my_leads.php">Leads</a> |
            <a href="manage_users.php">Manage Users</a> |
            <a href="../auth/logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <section class="dashboard-stats">
            <h2>Dashboard Overview</h2>
            <div class="stats">
                <div class="stat">
                    <h3>Total Leads</h3>
                    <p><?php echo $total_leads; ?></p>
                </div>
                <div class="stat">
                    <h3>Total Users</h3>
                    <p><?php echo $total_users; ?></p>
                </div>
            </div>
            <section class="status-summary">
                <h3>Lead Status Summary</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Total Leads</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($status_counts as $status): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($status['status_name']); ?></td>
                            <td><?php echo $status['total']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </section>
    </main>
</body>
</html>
