<?php
// This script processes the AJAX poll vote and returns the updated results
$pollFile = 'poll-results.txt';
$results = [];

// Read existing results from the file
if (file_exists($pollFile)) {
    $fileContent = file_get_contents($pollFile);
    if (!empty($fileContent)) {
        $results = json_decode($fileContent, true);
    }
}

// Check if a vote was submitted
if (isset($_POST['vote'])) {
    $vote = $_POST['vote'];
    // Increment the vote count for the selected option
    if (isset($results[$vote])) {
        $results[$vote]++;
    } else {
        $results[$vote] = 1;
    }

    // Save the updated results back to the file
    file_put_contents($pollFile, json_encode($results));
}

// Calculate total votes for percentage
$totalVotes = array_sum($results);

// Display the updated results
echo '<h3>Current Poll Results</h3>';
if ($totalVotes > 0) {
    foreach ($results as $option => $count) {
        $percentage = ($count / $totalVotes) * 100;
        echo "<div><strong>$option:</strong> $count votes (" . round($percentage) . "%)</div>";
    }
} else {
    echo '<p>No votes yet.</p>';
}
?>
