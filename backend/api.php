<?php
// Main API Router

// Determine the requested resource
$resource = $_GET['resource'] ?? '';

switch ($resource) {
    case 'auth':
        require 'auth.php';
        break;

    case 'appointments':
        require 'appointments.php';
        break;

    case 'ai':
        require 'ai_processor.php';
        break;

    default:
        header("HTTP/1.0 404 Not Found");
        echo json_encode([
            "status" => "error",
            "message" => "API endpoint not found."
        ]);
        break;
}
?>