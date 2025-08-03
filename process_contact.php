<?php
// This script processes the contact form, sends emails, and logs feedback

// Business and user email addresses for testing
// IMPORTANT: Replace these with your actual email addresses
$businessEmail = 'armysoldier3403@yahoo.com';
$userEmail = $_POST['email'];

// Form validation
if (empty($_POST['first-name']) || empty($_POST['last-name']) || empty($_POST['email']) || empty($_POST['message'])) {
    // In a real-world scenario, you might redirect with an error message
    die('Error: All form fields are required.');
}

// Sanitize inputs
$firstName = htmlspecialchars($_POST['first-name']);
$lastName = htmlspecialchars($_POST['last-name']);
$email = htmlspecialchars($_POST['email']);
$message = htmlspecialchars($_POST['message']);

// --- 1. Send email to the business ---
$businessSubject = "New Feedback from The Daily Grind Website";
$businessMessage = "You have received new feedback from the website.\n\n"
                 . "Name: $firstName $lastName\n"
                 . "Email: $email\n"
                 . "Message:\n$message";
$businessHeaders = "From: webmaster@thedailygrind.com\r\n"
                 . "Reply-To: $email\r\n"
                 . "X-Mailer: PHP/" . phpversion();

// Use a simple check for mail() success; it's not foolproof but sufficient for this example
if (!mail($businessEmail, $businessSubject, $businessMessage, $businessHeaders)) {
    // You could log this error or handle it more gracefully
    error_log("Failed to send email to business.");
}

// --- 2. Send email confirmation to the user ---
$userSubject = "Confirmation of Your Feedback to The Daily Grind";
$userMessage = "Dear $firstName,\n\n"
             . "Thank you for contacting us. We have received your message and will get back to you shortly.\n\n"
             . "Here is a copy of your message:\n\n"
             . "$message\n\n"
             . "Sincerely,\n"
             . "The Daily Grind Team";
$userHeaders = "From: The Daily Grind <noreply@thedailygrind.com>\r\n"
             . "Reply-To: $businessEmail\r\n"
             . "X-Mailer: PHP/" . phpversion();

if (!mail($userEmail, $userSubject, $userMessage, $userHeaders)) {
    error_log("Failed to send confirmation email to user.");
}

// --- 3. Store a dated copy of the feedback on the server ---
$feedbackFile = 'feedback-log.txt';
$feedback = date('Y-m-d H:i:s') . " | Name: $firstName $lastName | Email: $email | Message: $message\n";
file_put_contents($feedbackFile, $feedback, FILE_APPEND | LOCK_EX);

// --- 4. Redirect the user with an immediate browser display confirmation ---
header("Location: contact-us.php?status=success");
exit();
?>
