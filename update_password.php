<?php
require_once 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['login_username']);
    $new_password = $_POST['new_password'];

    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

    $sql = "UPDATE customers SET password_hash = ? WHERE login_username = ?";
    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("ss", $password_hash, $username);
        if($stmt->execute()){
            echo "SUCCESS: Password updated.";
        } else {
            echo "ERROR: Password update failed.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>
