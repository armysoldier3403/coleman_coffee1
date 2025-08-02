<?php
$file = 'poll_data.txt';
$votes = [];

// Read existing votes
if (file_exists($file)) {
    $content = file_get_contents($file);
    $votes = json_decode($content, true);
    if ($votes === null) { // Handle empty or invalid JSON
        $votes = [];
    }
}

$selected_coffee = isset($_POST['coffee']) ? $_POST['coffee'] : '';

if ($selected_coffee) {
    if (isset($votes[$selected_coffee])) {
        $votes[$selected_coffee]++;
    } else {
        $votes[$selected_coffee] = 1;
    }
    file_put_contents($file, json_encode($votes));
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'No coffee selected.']);
}
?>
