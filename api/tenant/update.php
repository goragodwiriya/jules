<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// include database and object files
include_once '../config/database.php';
include_once '../objects/tenant.php';

$database = new Database();
$db = $database->getConnection();
$tenant = new Tenant($db);

$data = json_decode(file_get_contents("php://input"));

// set ID property of tenant to be edited
$tenant->id = $data->id;

// set tenant property values
$tenant->name = $data->name;
$tenant->phone = $data->phone;
$tenant->email = $data->email;
$tenant->id_card = $data->id_card;
$tenant->address = $data->address;

// update the tenant
if ($tenant->update()) {
    http_response_code(200);
    echo json_encode(array("message" => "Tenant was updated."));
} else {
    http_response_code(503);
    echo json_encode(array("message" => "Unable to update tenant."));
}
?>
