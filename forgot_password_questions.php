<?php
require_once 'db_config.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['login_username']);
    $answer1 = trim($_POST['security_answer_1']);
    $answer2 = trim($_POST['security_answer_2']);

    $sql = "SELECT customer_id FROM customers WHERE login_username = ? AND security_answer_1 = ? AND security_answer_2 = ?";
    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("sss", $username, $answer1, $answer2);
        if($stmt->execute()){
            $stmt->store_result();
            if($stmt->num_rows == 1){
                echo "SUCCESS";
            } else {
                echo "ERROR: Answers do not match.";
            }
        } else {
            echo "ERROR: Something went wrong.";
        }
        $stmt->close();
    }
    $conn->close();
} else {
    // Return security questions for a given username
    $username = $_GET['login_username'];
    $sql = "SELECT security_question_1, security_question_2 FROM customers WHERE login_username = ?";
    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows == 1){
            $row = $result->fetch_assoc();
            echo json_encode($row);
        } else {
            echo "ERROR: User not found.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>
