<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Get database connection
include_once '../config/database.php';
// Instantiate tenant object
include_once '../objects/tenant.php';

$database = new Database();
$db = $database->getConnection();
$tenant = new Tenant($db);

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Make sure data is not empty
if (!empty($data->name)) {
    // Set tenant property values
    $tenant->name = $data->name;
    $tenant->phone = $data->phone ?? null;
    $tenant->email = $data->email ?? null;
    $tenant->id_card = $data->id_card ?? null;
    $tenant->address = $data->address ?? null;

    // Create the tenant
    if($tenant->create()) {
        http_response_code(201);
        echo json_encode(array("message" => "Tenant was created."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to create tenant."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to create tenant. Data is incomplete. 'name' is required."));
}
?>
