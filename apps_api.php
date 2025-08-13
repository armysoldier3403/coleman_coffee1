<?php
header('Content-Type: application/json');

$apps = [
    [
        'name' => 'Grind Rewards',
        'price' => 'Free',
        'description' => 'A loyalty program app for Daily Grind customers. Earn points with every purchase and redeem them for free drinks and exclusive merchandise.',
    ],
    [
        'name' => 'Brew Master',
        'price' => 'Free with in-app purchases',
        'description' => 'A coffee brewing guide app. Learn about different brewing methods, bean types, and flavor profiles to make the perfect cup at home.',
    ],
    [
        'name' => 'Daily Grind Radio',
        'price' => 'Free',
        'description' => 'An app to stream the curated music playlist from our coffee shop. Enjoy our chill, jazzy vibes wherever you are.',
    ],
];

echo json_encode($apps);
?>
