<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// include database and object files
include_once '../config/database.php';
include_once '../objects/bill.php';

$database = new Database();
$db = $database->getConnection();
$bill = new Bill($db);

$data = json_decode(file_get_contents("php://input"));

// set bill property values
$bill->id = $data->id;
$bill->status = $data->status;

// update the bill status
if ($bill->updateStatus()) {
    http_response_code(200);
    echo json_encode(array("message" => "Bill status was updated."));
} else {
    http_response_code(503);
    echo json_encode(array("message" => "Unable to update bill status."));
}
?>
