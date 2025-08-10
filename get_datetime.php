<?php
// This is server-side PHP code.
// It must be placed on your web server and served with a .php extension.

// Set the timezone to match the business location
date_default_timezone_set('America/Chicago');

// Format the date and time string
// Example format: "Monday, March 1, 2099 at 10:30 AM"
$formattedDateTime = date('l, F j, Y \a\t g:i A');

// Print the result. This is what the AJAX call receives.
echo $formattedDateTime;
?>
