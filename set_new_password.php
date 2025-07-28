<?php
// set_new_password.php
session_start();
require_once 'db_config.php';

$user_id = null;
$token_valid = false;
$error_message = "";
$success_message = "";

// Check if coming from email link (with token)
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = $_GET['token'];
    $sql = "SELECT id, token_expiry FROM users WHERE reset_token = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            $expiry_time = strtotime($user['token_expiry']);
            if (time() < $expiry_time) {
                $user_id = $user['id'];
                $token_valid = true;
                $_SESSION['reset_user_id'] = $user_id; // Store in session for consistency
            } else {
                $error_message = "Password reset link has expired.";
            }
        } else {
            $error_message = "Invalid password reset link.";
        }
        $stmt->close();
    } else {
        $error_message = "Database error during token validation.";
    }
}
// Check if coming from security questions (user ID in session)
elseif (isset($_SESSION['reset_user_id'])) {
    $user_id = $_SESSION['reset_user_id'];
    $token_valid = true; // No token, but user is validated via security questions
}

// If no valid path to reset, redirect
if (!$token_valid) {
    // If an error message was set, display it via forgot_password.html
    if (!empty($error_message)) {
        header("Location: forgot_password.html?status=invalid_token&message=" . urlencode($error_message));
    } else {
        header("Location: forgot_password.html?status=invalid_token");
    }
    exit();
}

// Process new password submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_password'])) {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_new_password = $_POST['confirm_new_password'] ?? '';

    if (empty($new_password) || empty($confirm_new_password)) {
        $error_message = "Please enter and confirm your new password.";
    } elseif (strlen($new_password) < 8) {
        $error_message = "Password must be at least 8 characters long.";
    } elseif ($new_password !== $confirm_new_password) {
        $error_message = "New passwords do not match.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password and clear reset token/expiry
        $update_sql = "UPDATE users SET password_hash = ?, reset_token = NULL, token_expiry = NULL WHERE id = ?";
        if ($update_stmt = $conn->prepare($update_sql)) {
            $update_stmt->bind_param("si", $hashed_password, $user_id);
            if ($update_stmt->execute()) {
                $success_message = "Your password has been reset successfully. You can now log in.";
                unset($_SESSION['reset_user_id']); // Clear reset session variable
                // Redirect to login page after successful reset
                header("Location: index.html?login_status=password_reset_success");
                exit();
            } else {
                $error_message = "Failed to update password. Please try again.";
            }
            $update_stmt->close();
        } else {
            $error_message = "Database error during password update.";
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password - The Daily Grind</title>
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
        .reset-container input[type="password"] {
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
        .reset-container .success {
            color: green;
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
            <h2>Set New Password</h2>
            <?php if (!empty($error_message)): ?>
                <p class="message error"><?php echo $error_message; ?></p>
            <?php elseif (!empty($success_message)): ?>
                <p class="message success"><?php echo $success_message; ?></p>
            <?php endif; ?>

            <?php if ($token_valid && empty($success_message)): // Only show form if valid and not already successful ?>
                <form action="set_new_password.php<?php echo isset($_GET['token']) ? '?token=' . htmlspecialchars($_GET['token']) : ''; ?>" method="post">
                    <div class="form-group">
                        <label for="new_password">New Password:</label>
                        <input type="password" id="new_password" name="new_password" required><br>
                    </div>
                    <div class="form-group">
                        <label for="confirm_new_password">Confirm New Password:</label>
                        <input type="password" id="confirm_new_password" name="confirm_new_password" required><br>
                    </div>
                    <button type="submit">Reset Password</button>
                </form>
            <?php endif; ?>
            <p><a href="index.html">Back to Login</a></p>
        </div>
    </main>

    <footer>
        <p id="currentDate"></p>
    </footer>

    <script src="global.js"></script>
</body>
</html>
