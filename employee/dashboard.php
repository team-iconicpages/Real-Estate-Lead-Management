<?php
// Include the database connection file
require '../config/db.php';
session_start();

// Check if the user is logged in and has the 'employee' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employee') {
    header("Location: ../auth/login.php");
    exit();
}

// Get the employee ID from the session
$employee_id = $_SESSION['user_id'];

// Fetch the total number of leads assigned to the employee
$stmt = $pdo->prepare("SELECT COUNT(*) AS total_leads FROM leads WHERE assigned_to = ?");
$stmt->execute([$employee_id]);
$total_leads = $stmt->fetchColumn();

// Fetch the number of leads grouped by status
$stmt = $pdo->prepare("SELECT ls.status_name, COUNT(l.id) AS total
                       FROM leads l
                       JOIN lead_status ls ON l.status_id = ls.id
                       WHERE l.assigned_to = ?
                       GROUP BY ls.status_name");
$stmt->execute([$employee_id]);
$leads_by_status = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch the most recent leads assigned to the employee
$stmt = $pdo->prepare("SELECT * FROM leads WHERE assigned_to = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$employee_id]);
$recent_leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <!-- Add your CSS/JS files here -->
</head>
<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
        <nav>
            <a href="my_leads.php">My Leads</a> |
            <a href="../auth/logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <section class="dashboard-stats">
            <h2>Dashboard Overview</h2>
            <div class="stats">
                <div class="stat-item">
                    <h3>Total Leads</h3>
                    <p><?php echo $total_leads; ?></p>
                </div>
                
                <?php foreach ($leads_by_status as $status): ?>
                    <div class="stat-item">
                        <h3><?php echo htmlspecialchars($status['status_name']); ?></h3>
                        <p><?php echo $status['total']; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="recent-leads">
            <h2>Recent Leads</h2>
            <table>
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_leads as $lead): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($lead['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($lead['email']); ?></td>
                            <td><?php echo htmlspecialchars($lead['status_id']); ?></td>
                            <td><?php echo date('d M Y', strtotime($lead['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
