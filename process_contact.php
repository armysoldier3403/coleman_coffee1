<?php
// process_contact.php
// Handles server-side processing for the contact form.

// Include database configuration
require_once 'config.php';

// Set response header to JSON
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'An unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $salutation = filter_input(INPUT_POST, 'salutation', FILTER_SANITIZE_STRING);
    $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
    $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
    $comments = filter_input(INPUT_POST, 'comments', FILTER_SANITIZE_STRING);
    $receive_reply = isset($_POST['receive_reply']) ? 1 : 0; // Convert checkbox to boolean (1 or 0)

    // Server-side validation
    if (empty($first_name) || empty($last_name) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Invalid input: First name, last name, and a valid email are required.';
        echo json_encode($response);
        exit();
    }

    try {
        // 1. Store a dated copy of the feedback on the server (database)
        $stmt = $pdo->prepare("INSERT INTO feedback (salutation, first_name, last_name, email, phone, subject, comments, receive_reply) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$salutation, $first_name, $last_name, $email, $phone, $subject, $comments, $receive_reply]);

        // 2. Send email to the business
        $business_email = ‘armysoldier3403@yahoo.com'; // 
        $business_subject = "New Contact Form Submission from " . $first_name . " " . $last_name;
        $business_body = "A new message has been submitted via the contact form:\n\n" .
                         "Salutation: " . ($salutation ?: 'N/A') . "\n" .
                         "First Name: " . $first_name . "\n" .
                         "Last Name: " . $last_name . "\n" .
                         "Email: " . $email . "\n" .
                         "Phone: " . ($phone ?: 'N/A') . "\n" .
                         "Subject: " . ($subject ?: 'N/A') . "\n" .
                         "Comments:\n" . $comments . "\n\n" .
                         "Reply requested: " . ($receive_reply ? 'Yes' : 'No') . "\n" .
                         "Submitted On: " . date('Y-m-d H:i:s');

        $headers = "From: armysoldier3403@yahoo.com\r\n" . // 
                   "Reply-To: " . $email . "\r\n" .
                   "X-Mailer: PHP/" . phpversion();

        // Use @mail to suppress errors, handle with if condition
        if (@mail($business_email, $business_subject, $business_body, $headers)) {
            // Business email sent successfully
        } else {
            error_log("Failed to send email to business: " . error_get_last()['message']);
            // You might still proceed or log this as a non-critical error
        }

        // 3. Send email confirmation to the user
        if ($receive_reply) {
            $user_email = $email;
            $user_subject = "The Daily Grind - Confirmation of Your Feedback Submission";
            $user_body = "Dear " . $first_name . ",\n\n" .
                         "Thank you for contacting The Daily Grind. We have received your feedback and will get back to you shortly if a reply was requested.\n\n" .
                         "Here is a copy of your submission:\n\n" .
                         "Subject: " . ($subject ?: 'N/A') . "\n" .
                         "Comments:\n" . $comments . "\n\n" .
                         "Sincerely,\n" .
                         "The Daily Grind Team";

            $user_headers = "From: armysoldier3403@yahoo.com\r\n" . // 
                            "Reply-To: " . $business_email . "\r\n" .
                            "X-Mailer: PHP/" . phpversion();

            if (@mail($user_email, $user_subject, $user_body, $user_headers)) {
                // User confirmation email sent successfully
            } else {
                error_log("Failed to send confirmation email to user: " . error_get_last()['message']);
            }
        }
        
        // 4. User gets an immediate browser display confirming the feedback submission.
        $response['success'] = true;
        $response['message'] = 'Thank you for your feedback! Your message has been sent.';

    } catch (PDOException $e) {
        error_log("Database error in contact form: " . $e->getMessage());
        $response['message'] = 'There was a problem submitting your feedback. Please try again later.';
    } catch (Exception $e) {
        error_log("General error in contact form: " . $e->getMessage());
        $response['message'] = 'An unexpected error occurred. Please try again.';
    }

} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
