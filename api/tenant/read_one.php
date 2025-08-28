<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json; charset=UTF-8');

// include database and object files
include_once '../config/database.php';
include_once '../objects/tenant.php';

$database = new Database();
$db = $database->getConnection();
$tenant = new Tenant($db);

$tenant->id = isset($_GET['id']) ? $_GET['id'] : die();

if ($tenant->readOne()) {
    $tenant_arr = array(
        "id" =>  $tenant->id,
        "name" => $tenant->name,
        "phone" => $tenant->phone,
        "email" => $tenant->email,
        "id_card" => $tenant->id_card,
        "address" => $tenant->address
    );
    http_response_code(200);
    echo json_encode($tenant_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Tenant does not exist."));
}
?>
