<?php
require_once 'db_config.php';
session_start();

header('Content-Type: application/json');

$order_id = $_GET['orderId'] ?? null;
if (!$order_id) {
    echo json_encode(['status' => 'error', 'message' => 'No order ID provided.']);
    exit;
}

// Fetch order details
$order_sql = "SELECT order_id, order_date, total_amount FROM orders WHERE order_id = ?";
$stmt = $conn->prepare($order_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();
$order = $order_result->fetch_assoc();
$stmt->close();

if (!$order) {
    echo json_encode(['status' => 'error', 'message' => 'Order not found.']);
    exit;
}

// Fetch order items
$items_sql = "SELECT p.name, oi.quantity, oi.price FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = ?";
$stmt = $conn->prepare($items_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();

$items = [];
while($item_row = $items_result->fetch_assoc()) {
    $items[] = $item_row;
}
$stmt->close();

$conn->close();

echo json_encode(['status' => 'success', 'order' => $order, 'items' => $items]);
?>
