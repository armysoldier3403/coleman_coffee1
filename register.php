<?php
session_start();
require_once 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['user_email']);
    $username = trim($_POST['login_username']);
    $password = $_POST['password'];
    $securityQ1 = trim($_POST['security_question_1']);
    $securityA1 = trim($_POST['security_answer_1']);
    $securityQ2 = trim($_POST['security_question_2']);
    $securityA2 = trim($_POST['security_answer_2']);

    // Check if username or email already exists
    $sql = "SELECT customer_id FROM customers WHERE login_username = ? OR email = ?";
    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("ss", $param_username, $param_email);
        $param_username = $username;
        $param_email = $email;

        if($stmt->execute()){
            $stmt->store_result();
            if($stmt->num_rows > 0){
                echo "ERROR: Username or email already exists.";
                exit();
            }
        }
        $stmt->close();
    }

    // Hash the password for security
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert new customer into the database
    $sql = "INSERT INTO customers (first_name, last_name, email, login_username, password_hash, security_question_1, security_answer_1, security_question_2, security_answer_2) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("sssssssss", $firstName, $lastName, $email, $username, $password_hash, $securityQ1, $securityA1, $securityQ2, $securityA2);
        if($stmt->execute()){
            echo "SUCCESS: Registration complete!";
        } else {
            echo "ERROR: Could not complete registration.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>
