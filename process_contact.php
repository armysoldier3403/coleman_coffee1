<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $salutation = $_POST['salutation'] ?? '';
    $firstName = $_POST['firstName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $comments = $_POST['comments'] ?? '';
    $receiveReply = isset($_POST['receiveReply']) ? 'Yes' : 'No';

    // Basic validation (more robust validation would be needed)
    if (empty($firstName) || empty($lastName) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields and provide a valid email.']);
        exit;
    }

    // Business Email Configuration
    $business_email = 'armysoldier3403@yahoo.com'; // **CHANGE THIS TO YOUR BUSINESS EMAIL**
    $business_subject = "New Contact Form Submission from The Daily Grind Website";
    $business_message = "
    New Contact Form Submission:

    Salutation: " . $salutation . "
    First Name: " . $firstName . "
    Last Name: " . $lastName . "
    Email: " . $email . "
    Phone: " . $phone . "
    Subject: " . $subject . "
    Comments: " . $comments . "
    Wishes to receive a reply: " . $receiveReply . "
    ";
    $business_headers = 'From: noreply@yourdomain.com' . "\r\n" .
                        'Reply-To: ' . $email . "\r\n" .
                        'X-Mailer: PHP/' . phpversion();

    // User Email Confirmation
    $user_subject = "Thank You for Contacting The Daily Grind";
    $user_message = "
    Dear " . $salutation . " " . $lastName . ",

    Thank you for your submission to The Daily Grind. We have received your message and will get back to you shortly if a reply was requested.

    Here's a copy of your message:
    Subject: " . $subject . "
    Comments: " . $comments . "

    Sincerely,
    The Daily Grind Team
    ";
    $user_headers = 'From: The Daily Grind <noreply@yourdomain.com>' . "\r\n" .
                    'Reply-To: ' . $business_email . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();

    // Send emails
    $business_mail_sent = mail($business_email, $business_subject, $business_message, $business_headers);
    $user_mail_sent = mail($email, $user_subject, $user_message, $user_headers);

    // Store feedback on server
    $feedback_dir = 'feedback/';
    if (!is_dir($feedback_dir)) {
        mkdir($feedback_dir, 0755, true);
    }
    $filename = $feedback_dir . date('Y-m-d_H-i-s') . '_' . uniqid() . '.txt';
    $feedback_content = "Date: " . date('Y-m-d H:i:s') . "\n" . $business_message;
    file_put_contents($filename, $feedback_content);

    if ($business_mail_sent && $user_mail_sent) {
        echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully! A confirmation email has been sent to you.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send one or more emails. Please try again later.']);
    }

} else {
    // Not a POST request
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
