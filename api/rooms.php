<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include database and object files
include_once './config/database.php';
include_once './objects/room.php';

// Instantiate database and room object
$database = new Database();
$db = $database->getConnection();
$room = new Room($db);

// Handle HTTP GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Query rooms
    $stmt = $room->read();
    $num = $stmt->rowCount();

    // Check if more than 0 record found
    if ($num > 0) {
        $rooms_arr = array();
        $rooms_arr["records"] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $room_item = array(
                "id" => $id,
                "room_number" => $room_number,
                "room_type" => $room_type,
                "price" => $price,
                "status" => $status
            );
            array_push($rooms_arr["records"], $room_item);
        }

        http_response_code(200);
        echo json_encode($rooms_arr);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No rooms found."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed."));
}
?>
