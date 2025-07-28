<?php
// get_datetime.php
// This script returns the current date and time from the server.

// Set the content type to plain text
header('Content-Type: text/plain');

// Set the timezone to your business location (e.g., Central Daylight Time)
// You might need to adjust this based on your actual business location
date_default_timezone_set('America/Chicago'); // Example: Fort Johnson South, Louisiana is in Central Time

// Format the date and time as "Monday, March 1, 2099 03:30 PM"
echo date('l, F j, Y h:i A');
?>
