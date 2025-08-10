<?php
    // Step 2: Server-side PHP for an AJAX poll
    // This is for demonstration purposes and will not run in this environment.

    // A simple file to store poll results
    $resultsFile = 'poll_results.json';
    $votes = [];

    // Initialize or load existing votes
    if (file_exists($resultsFile)) {
        $votes = json_decode(file_get_contents($resultsFile), true);
    } else {
        // Initialize with zero votes
        $votes = ['latte' => 0, 'cappuccino' => 0, 'espresso' => 0, 'cold_brew' => 0];
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['vote'])) {
        $vote = htmlspecialchars($_POST['vote']);
        if (array_key_exists($vote, $votes)) {
            $votes[$vote]++;
            file_put_contents($resultsFile, json_encode($votes));
        }
    }

    // Display the poll results
    $totalVotes = array_sum($votes);
    echo "<h3>Poll Results:</h3>";
    echo "<ul>";
    foreach ($votes as $option => $count) {
        $percentage = ($totalVotes > 0) ? round(($count / $totalVotes) * 100, 2) : 0;
        echo "<li>" . ucfirst(str_replace('_', ' ', $option)) . ": $count votes ($percentage%)</li>";
    }
    echo "</ul>";
?>
