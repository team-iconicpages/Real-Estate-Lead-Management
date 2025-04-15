<?php
// Database connection settings
$host = 'localhost';         // Database host
$db   = 'real_estate_leads'; // Database name
$user = 'root';              // Database username
$pass = '';                  // Database password (leave blank for local environment with no password)
$charset = 'utf8mb4';        // Character set for UTF-8

// Data Source Name (DSN) to connect to the database
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Create PDO instance (PHP Data Objects) to interact with the database
try {
    $pdo = new PDO($dsn, $user, $pass);
    // Set the PDO error mode to exception for debugging
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    // If connection fails, display an error message
    die("Database connection failed: " . $e->getMessage());
}
?>
