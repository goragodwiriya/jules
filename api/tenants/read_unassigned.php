<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include database and object files
include_once '../config/database.php';
include_once '../objects/tenant.php';

// Instantiate database and tenant object
$database = new Database();
$db = $database->getConnection();
$tenant = new Tenant($db);

// Query unassigned tenants
$stmt = $tenant->readUnassigned();
$num = $stmt->rowCount();

if ($num > 0) {
    $tenants_arr = array();
    $tenants_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $tenant_item = array(
            "id" => $id,
            "name" => $name
        );
        array_push($tenants_arr["records"], $tenant_item);
    }

    http_response_code(200);
    echo json_encode($tenants_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "No unassigned tenants found."));
}
?>
