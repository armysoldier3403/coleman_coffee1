<?php
// security_questions.php
session_start();
require_once 'db_config.php';

// Check if user ID is set in session from reset_password.php
if (!isset($_SESSION['reset_user_id'])) {
    header("Location: forgot_password.html");
    exit();
}

$user_id = $_SESSION['reset_user_id'];
$security_q1 = $security_q2 = "";
$error_message = "";

// Fetch security questions for the user
$sql = "SELECT security_q1, security_q2, security_a1_hash, security_a2_hash FROM users WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $security_q1 = htmlspecialchars($user['security_q1']);
        $security_q2 = htmlspecialchars($user['security_q2']);
        $security_a1_hash = $user['security_a1_hash'];
        $security_a2_hash = $user['security_a2_hash'];
    } else {
        // User not found, clear session and redirect
        unset($_SESSION['reset_user_id']);
        header("Location: forgot_password.html?status=user_not_found");
        exit();
    }
    $stmt->close();
} else {
    $error_message = "Database error. Please try again.";
}

// Process submitted answers
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $answer1 = $_POST['answer1'] ?? '';
    $answer2 = $_POST['answer2'] ?? '';

    // Verify answers
    if (password_verify($answer1, $security_a1_hash) && password_verify($answer2, $security_a2_hash)) {
        // Answers are correct, allow user to set new password
        // Redirect to set_new_password.php, keeping user ID in session
        header("Location: set_new_password.php");
        exit();
    } else {
        $error_message = "Incorrect answer(s). Please try again.";
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Questions - The Daily Grind</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .reset-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        .reset-container input[type="text"] {
            width: calc(100% - 22px);
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .reset-container button {
            background-color: #A0522D;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }
        .reset-container button:hover {
            background-color: #8B4513;
        }
        .reset-container .message {
            margin-top: 20px;
            font-weight: bold;
        }
        .reset-container .error {
            color: red;
        }
    </style>
</head>
<body>

    <div class="top-bar">
        <img src="logo.jpg" alt="The Daily Grind Logo">
        <div class="address-info">
            605 Washington St<br>
            Fayette, IA 52142<br>
            Tel: 888-555-5555
        </div>
    </div>

    <nav>
        <ul>
            <li><a href="index.html">Home</a></li>
            <li><a href="houseblend.html">House Blend</a></li>
            <li><a href="contact.html">Contact Us</a></li>
            <li><a href="sitemap.html">Site Map</a></li>
            <li><a href="register.html">Register</a></li>
        </ul>
    </nav>

    <main>
        <div class="reset-container">
            <h2>Security Questions</h2>
            <p>Please answer your security questions to reset your password.</p>

            <?php if (!empty($error_message)): ?>
                <p class="message error"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <form action="security_questions.php" method="post">
                <div class="form-group">
                    <label><?php echo $security_q1; ?></label>
                    <input type="text" name="answer1" required><br>
                </div>
                <div class="form-group">
                    <label><?php echo $security_q2; ?></label>
                    <input type="text" name="answer2" required><br>
                </div>
                <button type="submit">Verify Answers</button>
            </form>
            <p><a href="forgot_password.html">Back to Forgot Password</a></p>
        </div>
    </main>

    <footer>
        <p id="currentDate"></p>
    </footer>

    <script src="global.js"></script>
</body>
</html>
