<?php
header('Content-Type: application/json');

// You need to register for your own API key and App ID at [https://developer.edamam.com/](https://developer.edamam.com/)
$appId = 'YOUR_EDAMAM_APP_ID';
$appKey = 'YOUR_EDAMAM_APP_KEY';

$query = urlencode($_GET['q'] ?? '');

if (empty($query)) {
    echo json_encode(['status' => 'error', 'message' => 'Query parameter "q" is required.']);
    exit;
}

$apiUrl = "[https://api.edamam.com/api/nutrition-data?app_id=](https://api.edamam.com/api/nutrition-data?app_id=){$appId}&app_key={$appKey}&ingr={$query}";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpcode !== 200) {
    echo json_encode(['status' => 'error', 'message' => 'API request failed with status code: ' . $httpcode]);
    exit;
}

$data = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON response from API.']);
    exit;
}

// Extract relevant nutrition data
$nutrients = [
    'calories' => $data['totalNutrients']['ENERC_KCAL'] ?? ['label' => 'Calories', 'quantity' => 0, 'unit' => 'kcal'],
    'fat' => $data['totalNutrients']['FAT'] ?? ['label' => 'Total Fat', 'quantity' => 0, 'unit' => 'g'],
    'carbs' => $data['totalNutrients']['CHOCDF'] ?? ['label' => 'Total Carbohydrate', 'quantity' => 0, 'unit' => 'g'],
    'protein' => $data['totalNutrients']['PROCNT'] ?? ['label' => 'Protein', 'quantity' => 0, 'unit' => 'g']
];

echo json_encode(['status' => 'success', 'query' => urldecode($query), 'nutrients' => $nutrients]);
?>
