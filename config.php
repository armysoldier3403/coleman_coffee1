?php
    // This file contains your database connection details.
    // Replace the placeholders with your actual database credentials.
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'your_username');
    define('DB_PASSWORD', 'your_password');
    define('DB_NAME', 'daily_grind_db');

    // Attempt to connect to the MySQL database
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Check connection
    if($conn === false){
        die("ERROR: Could not connect. " . $conn->connect_error);
    }
?>
