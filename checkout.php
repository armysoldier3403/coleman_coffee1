<?php
require_once 'db_config.php';
session_start();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['cart']) || empty($data['orderID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing cart or PayPal order ID.']);
    exit;
}

$paypal_order_id = $data['orderID'];
$cart_items = $data['cart'];
$total_amount = $data['totalAmount'];
$customer_id = $_SESSION['customer_id'] ?? null; // Get customer ID from session if logged in

// Start a transaction for safety
$conn->begin_transaction();
$success = false;

try {
    // 1. Insert into orders table
    $stmt = $conn->prepare("INSERT INTO orders (customer_id, paypal_order_id, total_amount) VALUES (?, ?, ?)");
    $stmt->bind_param("isd", $customer_id, $paypal_order_id, $total_amount);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // 2. Insert into order_items and update product inventory
    foreach ($cart_items as $item) {
        $product_id = $item['id'];
        $quantity = $item['quantity'];
        $price = $item['price'];

        // Insert into order_items
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
        $stmt->execute();
        $stmt->close();

        // Update product inventory
        $stmt = $conn->prepare("UPDATE products SET inventory = inventory - ? WHERE product_id = ? AND inventory >= ?");
        $stmt->bind_param("iii", $quantity, $product_id, $quantity);
        $stmt->execute();
        if ($conn->affected_rows === 0) {
            throw new Exception("Insufficient inventory for product ID: $product_id");
        }
        $stmt->close();
    }

    $conn->commit();
    $success = true;

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn->close();

if ($success) {
    echo json_encode(['status' => 'success', 'orderId' => $order_id]);
}
?>
