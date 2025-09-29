<?php
session_start();
require_once "config.php";

header("Content-Type: application/json; charset=UTF-8");

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("HTTP/1.1 401 Unauthorized");
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$response = ["status" => "error", "message" => "Invalid request"];
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
$userId = $_SESSION["id"];

switch ($method) {
    case 'GET':
        $sql = "SELECT id, title, appointment_time FROM appointments WHERE user_id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $userId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $appointments = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $response = ["status" => "success", "data" => $appointments];
            mysqli_stmt_close($stmt);
        } else {
            $response['message'] = "Database query failed.";
        }
        break;

    case 'POST':
        $title = $input['title'] ?? null;
        $time = $input['appointment_time'] ?? null;

        if ($title && $time) {
            $sql = "INSERT INTO appointments (user_id, title, appointment_time) VALUES (?, ?, ?)";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "iss", $userId, $title, $time);
                if (mysqli_stmt_execute($stmt)) {
                    $response = ["status" => "success", "message" => "Appointment created successfully.", "id" => mysqli_insert_id($link)];
                } else {
                    $response['message'] = "Failed to create appointment.";
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            $response['message'] = "Missing title or appointment_time.";
        }
        break;

    // DELETE and PUT cases can be added here later
    default:
        header("HTTP/1.0 405 Method Not Allowed");
        $response['message'] = 'Method Not Allowed';
        break;
}

echo json_encode($response);
mysqli_close($link);
?>