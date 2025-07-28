<?php
// process_registration.php
require_once 'db_config.php'; // Include database connection

// Initialize variables
$first_name = $last_name = $email = $username = $password = $dob = $country = $gender = "";
$security_q1 = $security_a1 = $security_q2 = $security_a2 = "";
$newsletter = 0; // Default to not subscribed

$errors = [];

// Function to sanitize and validate input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $first_name = sanitize_input($_POST['regFirstName'] ?? '');
    $last_name = sanitize_input($_POST['regLastName'] ?? '');
    $email = sanitize_input($_POST['regEmail'] ?? '');
    $username = sanitize_input($_POST['regUsername'] ?? '');
    $password = $_POST['regPassword'] ?? ''; // Password not sanitized with htmlspecialchars for hashing
    $confirm_password = $_POST['confirmPassword'] ?? '';
    $dob = sanitize_input($_POST['dob'] ?? '');
    $country = sanitize_input($_POST['country'] ?? '');
    $gender = sanitize_input($_POST['gender'] ?? '');
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;
    $security_q1 = sanitize_input($_POST['securityQ1'] ?? '');
    $security_a1 = sanitize_input($_POST['securityA1'] ?? '');
    $security_q2 = sanitize_input($_POST['securityQ2'] ?? '');
    $security_a2 = sanitize_input($_POST['securityA2'] ?? '');

    // Server-side validation
    if (empty($first_name)) { $errors[] = "First Name is required."; }
    if (empty($last_name)) { $errors[] = "Last Name is required."; }
    if (empty($email)) { $errors[] = "Email is required."; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "Invalid email format."; }
    if (empty($username)) { $errors[] = "Username is required."; }
    if (empty($password)) { $errors[] = "Password is required."; }
    if (strlen($password) < 8) { $errors[] = "Password must be at least 8 characters."; }
    if ($password !== $confirm_password) { $errors[] = "Passwords do not match."; }
    if (empty($security_q1)) { $errors[] = "Security Question 1 is required."; }
    if (empty($security_a1)) { $errors[] = "Answer 1 is required."; }
    if (empty($security_q2)) { $errors[] = "Security Question 2 is required."; }
    if (empty($security_a2)) { $errors[] = "Answer 2 is required."; }

    // Check if email or username already exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Email or Username already exists.";
        }
        $stmt->close();
    }

    if (empty($errors)) {
        // Hash the password and security answers
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $security_a1_hash = password_hash($security_a1, PASSWORD_DEFAULT);
        $security_a2_hash = password_hash($security_a2, PASSWORD_DEFAULT);

        // Prepare an insert statement
        $sql = "INSERT INTO users (first_name, last_name, email, username, password_hash, dob, country, gender, newsletter, security_q1, security_a1_hash, security_q2, security_a2_hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssssssssisss",
                $first_name,
                $last_name,
                $email,
                $username,
                $password_hash,
                $dob,
                $country,
                $gender,
                $newsletter,
                $security_q1,
                $security_a1_hash,
                $security_q2,
                $security_a2_hash
            );

            if ($stmt->execute()) {
                header("Location: register.html?status=success");
            } else {
                $errors[] = "Error: " . $stmt->error;
                header("Location: register.html?status=error&message=" . urlencode(implode(", ", $errors)));
            }
            $stmt->close();
        } else {
            $errors[] = "Database prepare error: " . $conn->error;
            header("Location: register.html?status=error&message=" . urlencode(implode(", ", $errors)));
        }
    } else {
        header("Location: register.html?status=error&message=" . urlencode(implode(", ", $errors)));
    }

    $conn->close();
} else {
    // If not a POST request, redirect to registration page
    header("Location: register.html");
    exit();
}
?>
