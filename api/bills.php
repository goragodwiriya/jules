<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include database and object files
include_once './config/database.php';
include_once './objects/bill.php';

$database = new Database();
$db = $database->getConnection();
$bill = new Bill($db);

// Query bills
$stmt = $bill->read();
$num = $stmt->rowCount();

if ($num > 0) {
    $bills_arr = array();
    $bills_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $bill_item = array(
            "id" => $id,
            "room_number" => $room_number,
            "tenant_name" => $tenant_name,
            "bill_date" => $bill_date,
            "due_date" => $due_date,
            "total_amount" => $total_amount,
            "status" => $status
        );
        array_push($bills_arr["records"], $bill_item);
    }

    http_response_code(200);
    echo json_encode($bills_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "No bills found."));
}
?>
