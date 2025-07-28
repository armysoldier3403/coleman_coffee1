<?php
// reset_password.php - Handles initial forgot password request
session_start();
require_once 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_email = $_POST['username_email'] ?? '';
    $reset_method = $_POST['reset_method'] ?? '';

    if (empty($username_email)) {
        header("Location: forgot_password.html?status=user_not_found");
        exit();
    }

    // Find user by username or email
    $sql = "SELECT id, email, security_q1, security_q2 FROM users WHERE username = ? OR email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $username_email, $username_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            $user_id = $user['id'];
            $user_email = $user['email'];

            if ($reset_method == 'security_questions') {
                // Redirect to security questions page
                $_SESSION['reset_user_id'] = $user_id; // Store user ID in session
                header("Location: security_questions.php");
                exit();
            } elseif ($reset_method == 'email_reset') {
                // Generate and store reset token
                $token = bin2hex(random_bytes(32)); // Generate a random token
                $expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token valid for 1 hour

                $update_sql = "UPDATE users SET reset_token = ?, token_expiry = ? WHERE id = ?";
                if ($update_stmt = $conn->prepare($update_sql)) {
                    $update_stmt->bind_param("ssi", $token, $expiry, $user_id);
                    if ($update_stmt->execute()) {
                        // Send email with reset link
                        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/dailygrind/set_new_password.php?token=" . $token; // Adjust URL if needed
                        $subject = "The Daily Grind: Password Reset Request";
                        $message = "Dear " . htmlspecialchars($user['username']) . ",\n\n" .
                                   "You have requested a password reset for your Daily Grind account.\n" .
                                   "Please click on the following link to reset your password:\n" .
                                   $reset_link . "\n\n" .
                                   "This link will expire in 1 hour.\n" .
                                   "If you did not request this, please ignore this email.\n\n" .
                                   "Sincerely,\n" .
                                   "The Daily Grind Team";
                        $headers = "From: no-reply@thedailygrind.com\r\n" .
                                   "Reply-To: no-reply@thedailygrind.com\r\n" .
                                   "X-Mailer: PHP/" . phpversion();

                        if (mail($user_email, $subject, $message, $headers)) {
                            header("Location: forgot_password.html?status=email_sent");
                            exit();
                        } else {
                            header("Location: forgot_password.html?status=email_fail");
                            exit();
                        }
                    } else {
                        header("Location: forgot_password.html?status=password_update_fail"); // Error storing token
                        exit();
                    }
                    $update_stmt->close();
                } else {
                    header("Location: forgot_password.html?status=password_update_fail"); // Error preparing update
                    exit();
                }
            } else {
                header("Location: forgot_password.html?status=error"); // Invalid reset method
                exit();
            }
        } else {
            header("Location: forgot_password.html?status=user_not_found");
            exit();
        }
        $stmt->close();
    } else {
        header("Location: forgot_password.html?status=error"); // Database prepare error
        exit();
    }
    $conn->close();
} else {
    header("Location: forgot_password.html");
    exit();
}
?>
