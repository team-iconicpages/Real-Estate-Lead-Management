<?php
// Include the database connection file
require '../config/db.php';
session_start();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];

    // Validate form fields
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $error = "All fields are required!";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert user into the database
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hashed_password, $phone, $role]);

            // Redirect to login page after successful registration
            header("Location: login.php?success=registration");
            exit();
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <header>
        <h1>Register</h1>
        <nav>
            <a href="login.php">Already have an account? Login</a>
        </nav>
    </header>

    <main>
        <section class="register-form">
            <h2>Create a New Account</h2>

            <?php if (isset($error)): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <label for="name">Name:</label>
                <input type="text" name="name" required>

                <label for="email">Email:</label>
                <input type="email" name="email" required>

                <label for="password">Password:</label>
                <input type="password" name="password" required>

                <label for="phone">Phone (Optional):</label>
                <input type="text" name="phone">

                <label for="role">Role:</label>
                <select name="role" required>
                    <option value="admin">Admin</option>
                    <option value="employee">Employee</option>
                </select>

                <button type="submit" name="register">Register</button>
            </form>
        </section>
    </main>
</body>
</html>
