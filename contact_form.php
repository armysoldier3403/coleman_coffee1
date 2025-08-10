<?php
    // Step 3: Server-side PHP for contact form processing
    // This is for demonstration purposes and will not run in this environment.

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $firstName = htmlspecialchars($_POST['first_name']);
        $lastName = htmlspecialchars($_POST['last_name']);
        $email = htmlspecialchars($_POST['user_email']);
        $message = htmlspecialchars($_POST['user_message']);
        $datetime = date('Y-m-d H:i:s');

        // Business email details
        $to_business = "armysoldier3403@yahoo.com"; // Replace with your business email
        $subject_business = "New Contact Form Submission from The Daily Grind";
        $message_business = "A new message has been submitted:\n\n";
        $message_business .= "Name: $firstName $lastName\n";
        $message_business .= "Email: $email\n";
        $message_business .= "Message:\n$message\n\n";
        $message_business .= "Submitted on: $datetime";
        $headers_business = "From: webmaster@thedailygrind.com";

        // User confirmation email details
        $to_user = $email;
        $subject_user = "Confirmation: The Daily Grind Contact Form";
        $message_user = "Hi $firstName,\n\n";
        $message_user .= "Thank you for contacting The Daily Grind. We have received your message and will get back to you shortly.\n\n";
        $message_user .= "Your message:\n$message\n\n";
        $message_user .= "Sincerely,\nThe Daily Grind Team";
        $headers_user = "From: webmaster@thedailygrind.com";

        // Store a dated copy of the feedback on the server
        // This is a simple example; a database would be better for a real application.
        $feedback_file = "feedback_log.txt";
        $feedback_entry = "--- New Feedback ---\n";
        $feedback_entry .= "Date: $datetime\n";
        $feedback_entry .= "Name: $firstName $lastName\n";
        $feedback_entry .= "Email: $email\n";
        $feedback_entry .= "Message: $message\n\n";
        file_put_contents($feedback_file, $feedback_entry, FILE_APPEND | LOCK_EX);

        // Send emails
        // mail($to_business, $subject_business, $message_business, $headers_business);
        // mail($to_user, $subject_user, $message_user, $headers_user);

        // Send a success response back to the browser
        http_response_code(200);
        echo "Success";
    } else {
        http_response_code(405);
        echo "Method Not Allowed";
    }
?>
