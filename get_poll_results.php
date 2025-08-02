<?php
$file = 'poll_data.txt';
$votes = [];

if (file_exists($file)) {
    $content = file_get_contents($file);
    $votes = json_decode($content, true);
    if ($votes === null) {
        $votes = [];
    }
}

header('Content-Type: application/json');
echo json_encode($votes);
?>
