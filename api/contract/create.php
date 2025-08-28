<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-control-allow-headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Get database connection
include_once '../config/database.php';
// Instantiate contract object
include_once '../objects/contract.php';

$database = new Database();
$db = $database->getConnection();
$contract = new Contract($db);

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Make sure data is not empty
if (
    !empty($data->room_id) &&
    !empty($data->tenant_id) &&
    !empty($data->start_date)
) {
    // Set contract property values
    $contract->room_id = $data->room_id;
    $contract->tenant_id = $data->tenant_id;
    $contract->start_date = $data->start_date;
    $contract->deposit = $data->deposit ?? 0;

    // Create the contract
    if($contract->create()) {
        http_response_code(201);
        echo json_encode(array("message" => "Contract was created."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to create contract."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to create contract. Data is incomplete."));
}
?>
