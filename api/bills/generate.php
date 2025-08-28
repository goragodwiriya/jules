<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// Include database and object files
include_once '../config/database.php';
include_once '../objects/bill.php';

$database = new Database();
$db = $database->getConnection();
$bill = new Bill($db);

// Generate bills
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bills_created = $bill->generate();

    if ($bills_created !== false) {
        http_response_code(200);
        echo json_encode(array("message" => "Bill generation process completed. {$bills_created} new bills were created."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to generate bills."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed."));
}
?>
