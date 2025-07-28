<?php
// login.php
session_start(); // Start the session
require_once 'db_config.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_email = $_POST['username_email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Sanitize input
    $username_email = $conn->real_escape_string($username_email); // Use real_escape_string for basic sanitation

    // Prepare a select statement
    // Check for both username and email
    $sql = "SELECT id, username, password_hash FROM users WHERE username = ? OR email = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $username_email, $username_email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $db_username, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                // Password is correct, start a new session
                session_regenerate_id(true); // Regenerate session ID for security
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $db_username; // Store the actual username from DB

                header("Location: index.html"); // Redirect to home page
                exit();
            } else {
                // Password is not valid
                header("Location: index.html?login_status=failed");
                exit();
            }
        } else {
            // No user found with that username/email
            header("Location: index.html?login_status=failed");
            exit();
        }
        $stmt->close();
    } else {
        // Database prepare error
        header("Location: index.html?login_status=failed"); // Generic error for security
        exit();
    }
    $conn->close();
} else {
    // If not a POST request, redirect to home page
    header("Location: index.html");
    exit();
}
?>
