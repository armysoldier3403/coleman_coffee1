<?php
require_once 'db_config.php';

header('Content-Type: application/json');

$sql = "SELECT * FROM products ORDER BY category, name";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
echo json_encode($products);

$conn->close();
?>
