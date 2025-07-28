<?php
// logout.php
session_start(); // Start the session
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

header("Location: index.html?login_status=logged_out"); // Redirect to home page with logout status
exit();
?>
