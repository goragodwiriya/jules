<?php
include 'config.php';

// SQL to get orders and their items
$sql = "SELECT
            o.id AS order_id,
            o.total_price,
            o.payment_method,
            o.status,
            o.created_at,
            oi.product_id,
            oi.quantity,
            oi.price AS item_price,
            p.name AS product_name
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        ORDER BY o.created_at DESC, o.id DESC";

$result = $conn->query($sql);

$orders = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $order_id = $row['order_id'];

        // If the order is not yet in our array, add it
        if (!isset($orders[$order_id])) {
            $orders[$order_id] = [
                'id' => $order_id,
                'total_price' => $row['total_price'],
                'payment_method' => $row['payment_method'],
                'status' => $row['status'],
                'created_at' => $row['created_at'],
                'items' => []
            ];
        }

        // Add the item to the order
        $orders[$order_id]['items'][] = [
            'product_id' => $row['product_id'],
            'product_name' => $row['product_name'],
            'quantity' => $row['quantity'],
            'item_price' => $row['item_price']
        ];
    }
}

// Convert the associative array to a simple indexed array for JSON output
echo json_encode(array_values($orders));

$conn->close();
?>