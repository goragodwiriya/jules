<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include database and object files
include_once './config/database.php';
include_once './objects/contract.php';

// Instantiate database and contract object
$database = new Database();
$db = $database->getConnection();
$contract = new Contract($db);

// Query contracts
$stmt = $contract->read();
$num = $stmt->rowCount();

if ($num > 0) {
    $contracts_arr = array();
    $contracts_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $contract_item = array(
            "id" => $id,
            "room_number" => $room_number,
            "tenant_name" => $tenant_name,
            "start_date" => $start_date,
            "is_active" => $is_active
        );
        array_push($contracts_arr["records"], $contract_item);
    }

    http_response_code(200);
    echo json_encode($contracts_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "No contracts found."));
}
?>
