<?php
// config.php
// This file contains database connection details.

// Database connection parameters
$host = 'localhost'; // Your database host
$db   = 'daily_grind_db'; // The database name you created
$user = 'root'; // Your MySQL username
$pass = ''; // Your MySQL password (leave empty if no password)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Log the error in a real application
    error_log("Database connection failed: " . $e->getMessage());
    // In a production environment, you would show a generic error message to the user.
    // For development, we can show the actual error.
    die("Database connection failed. Please try again later.");
}
?>
