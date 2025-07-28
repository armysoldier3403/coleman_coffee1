<?php
// submit_vote.php
// Handles submission of a poll vote.

session_start(); // Start session to prevent multiple votes from same user (optional, but good for simple polls)

// Include database configuration (assuming config.php is in the same directory or accessible)
$host = 'localhost';
$db   = 'daily_grind_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    error_log("Database connection failed in submit_vote.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'An unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $coffee_type = filter_input(INPUT_POST, 'coffee_type', FILTER_SANITIZE_STRING);

    if (empty($coffee_type)) {
        $response['message'] = 'No coffee type selected.';
        echo json_encode($response);
        exit();
    }

    // Optional: Prevent multiple votes from the same session
    if (isset($_SESSION['voted_poll'])) {
        $response['message'] = 'You have already voted in this poll!';
        echo json_encode($response);
        exit();
    }

    try {
        // Check if the coffee_type exists in the poll_votes table
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM poll_votes WHERE coffee_type = ?");
        $stmt_check->execute([$coffee_type]);
        if ($stmt_check->fetchColumn() == 0) {
            $response['message'] = 'Invalid coffee type selected.';
            echo json_encode($response);
            exit();
        }

        // Increment the vote count for the selected coffee type
        $stmt = $pdo->prepare("UPDATE poll_votes SET vote_count = vote_count + 1 WHERE coffee_type = ?");
        $stmt->execute([$coffee_type]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['voted_poll'] = true; // Mark session as voted
            $response['success'] = true;
            $response['message'] = 'Your vote has been recorded!';
        } else {
            $response['message'] = 'Failed to record your vote. Please try again.';
        }

    } catch (PDOException $e) {
        error_log("Database error in poll submission: " . $e->getMessage());
        $response['message'] = 'Database error. Please try again later.';
    } catch (Exception $e) {
        error_log("General error in poll submission: " . $e->getMessage());
        $response['message'] = 'An unexpected error occurred.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
