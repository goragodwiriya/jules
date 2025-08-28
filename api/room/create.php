<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Get database connection
include_once '../config/database.php';

// Instantiate room object
include_once '../objects/room.php';

$database = new Database();
$db = $database->getConnection();

$room = new Room($db);

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Make sure data is not empty
if (
    !empty($data->room_number) &&
    !empty($data->price) &&
    !empty($data->status)
) {
    // Set room property values
    $room->room_number = $data->room_number;
    $room->price = $data->price;
    $room->status = $data->status;
    $room->room_type = !empty($data->room_type) ? $data->room_type : null;

    // Create the room
    if($room->create()) {
        // Set response code - 201 created
        http_response_code(201);

        // Tell the user
        echo json_encode(array("message" => "Room was created."));
    } else {
        // If unable to create the room, tell the user
        // Set response code - 503 service unavailable
        http_response_code(503);

        // Tell the user
        echo json_encode(array("message" => "Unable to create room."));
    }
} else {
    // Tell the user data is incomplete
    // Set response code - 400 bad request
    http_response_code(400);

    // Tell the user
    echo json_encode(array("message" => "Unable to create room. Data is incomplete."));
}
?>
