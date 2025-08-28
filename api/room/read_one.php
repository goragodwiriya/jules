<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json; charset=UTF-8');

// include database and object files
include_once '../config/database.php';
include_once '../objects/room.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// prepare room object
$room = new Room($db);

// get id of room to be edited
$room->id = isset($_GET['id']) ? $_GET['id'] : die();

// read the details of room to be edited
$room->readOne();

if ($room->room_number != null) {
    // create array
    $room_arr = array(
        "id" =>  $room->id,
        "room_number" => $room->room_number,
        "room_type" => $room->room_type,
        "price" => $room->price,
        "status" => $room->status
    );

    // set response code - 200 OK
    http_response_code(200);

    // make it json format
    echo json_encode($room_arr);
} else {
    // set response code - 404 Not found
    http_response_code(404);

    // tell the user room does not exist
    echo json_encode(array("message" => "Room does not exist."));
}
?>
