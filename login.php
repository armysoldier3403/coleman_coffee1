<?php
session_start();
require_once 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['login_username']);
    $password = $_POST['password'];

    // Prepare a select statement
    $sql = "SELECT customer_id, login_username, password_hash FROM customers WHERE login_username = ?";
    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("s", $username);

        if($stmt->execute()){
            $stmt->store_result();
            if($stmt->num_rows == 1){
                $stmt->bind_result($id, $username, $hashed_password);
                if($stmt->fetch()){
                    if(password_verify($password, $hashed_password)){
                        // Password is correct, start a new session
                        $_SESSION["loggedin"] = true;
                        $_SESSION["customer_id"] = $id;
                        $_SESSION["login_username"] = $username;
                        echo "SUCCESS: Login successful!";
                        exit();
                    }
                }
            }
        }
        $stmt->close();
    }
    echo "ERROR: Invalid username or password.";
    $conn->close();
}
?>
