<?php
include 'config.php';

// Get the posted data
$data = json_decode(file_get_contents("php://input"));

if (isset($data->items) && isset($data->total) && isset($data->paymentMethod)) {
    $items = $data->items;
    $total = $data->total;
    $paymentMethod = $data->paymentMethod;
    $status = 'pending'; // Default status

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert into orders table
        $stmt = $conn->prepare("INSERT INTO orders (total_price, payment_method, status) VALUES (?, ?, ?)");
        $stmt->bind_param("dss", $total, $paymentMethod, $status);
        $stmt->execute();
        $order_id = $stmt->insert_id;
        $stmt->close();

        // Insert into order_items table
        $stmt_items = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($items as $item) {
            $stmt_items->bind_param("iiid", $order_id, $item->id, $item->quantity, $item->price);
            $stmt_items->execute();
        }
        $stmt_items->close();

        // Commit transaction
        $conn->commit();

        echo json_encode(['success' => true, 'order_id' => $order_id]);

    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => $exception->getMessage()]);
    }

} else {
    echo json_encode(['success' => false, 'error' => 'Invalid order data']);
}

$conn->close();
?>