<?php
// --- Main API Router ---

// Set global headers for JSON response and CORS
header("Content-Type: application/json");
// In a production environment, you should restrict this to your frontend's domain
// for security. Example: header("Access-Control-Allow-Origin: https://yourdomain.com");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// PHP's built-in server doesn't handle OPTIONS requests well, so we do it manually.
// This is for pre-flight requests from browsers.
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); // No Content
    exit;
}

// Include the database connection. The script will exit if connection fails.
require_once 'db.php';

// --- Helper function to send a standardized JSON response and exit ---
function send_json_response($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// --- API Routing Logic ---
$method = $_SERVER['REQUEST_METHOD'];

// Get the request path from the 'path' query parameter set by .htaccess
$path_info = isset($_GET['path']) ? $_GET['path'] : '';

// Sanitize and split the path into parts
$path_parts = explode('/', trim(filter_var($path_info, FILTER_SANITIZE_URL), '/'));

$resource = array_shift($path_parts) ?? null;
$resource_id = array_shift($path_parts) ?? null;
$sub_resource = array_shift($path_parts) ?? null;

// --- Main try-catch block for handling all requests ---
try {
    // Route based on the main resource (e.g., 'forms')
    switch ($resource) {
        case 'forms':
            // Handle requests to /api/forms
            if ($method === 'POST' && $resource_id === null) {
                // --- CREATE a new form ---
                // Route: POST /api/forms
                $payload = json_decode(file_get_contents('php://input'), true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    send_json_response(['message' => 'Invalid JSON payload provided'], 400);
                }

                $formName = $payload['name'] ?? null;
                $formStructure = $payload['structure'] ?? null;

                if (empty($formName) || empty($formStructure)) {
                    send_json_response(['message' => 'Form name and structure are required'], 400);
                }

                $stmt = $pdo->prepare("INSERT INTO forms (name, structure) VALUES (?, ?)");
                // We store the structure as a JSON string
                $stmt->execute([$formName, json_encode($formStructure)]);

                $newFormId = $pdo->lastInsertId();

                send_json_response(['id' => $newFormId, 'message' => 'Form created successfully'], 201);
            }
            // Handle requests to /api/forms/{id}
            elseif ($method === 'GET' && is_numeric($resource_id) && $sub_resource === null) {
                // --- READ a specific form's structure ---
                // Route: GET /api/forms/{id}
                $stmt = $pdo->prepare("SELECT id, name, structure, created_at FROM forms WHERE id = ?");
                $stmt->execute([$resource_id]);
                $form = $stmt->fetch();

                if ($form) {
                    // Decode the JSON string from the DB back into an array/object for the client
                    $form['structure'] = json_decode($form['structure'], true);
                    send_json_response($form);
                } else {
                    send_json_response(['message' => "Form with ID {$resource_id} not found"], 404);
                }
            }
            // Handle requests to /api/forms/{id}/submissions
            elseif ($method === 'POST' && is_numeric($resource_id) && $sub_resource === 'submissions') {
                // --- CREATE a new submission for a form ---
                // Route: POST /api/forms/{id}/submissions

                // First, check if the form exists
                $stmt = $pdo->prepare("SELECT id FROM forms WHERE id = ?");
                $stmt->execute([$resource_id]);
                if ($stmt->fetch() === false) {
                    send_json_response(['message' => "Form with ID {$resource_id} not found"], 404);
                }

                // The submitted data from a standard form post is in $_POST
                $submission_data = $_POST;

                if (empty($submission_data)) {
                    // This could also happen if the request body is JSON, so let's check that too.
                    $json_payload = json_decode(file_get_contents('php://input'), true);
                    if (!empty($json_payload)) {
                        $submission_data = $json_payload;
                    } else {
                        send_json_response(['message' => 'No submission data received'], 400);
                    }
                }

                // Store the entire submission data as a single JSON object
                $stmt = $pdo->prepare("INSERT INTO submissions (form_id, data) VALUES (?, ?)");
                $stmt->execute([$resource_id, json_encode($submission_data)]);

                // For an API, returning JSON is standard. The frontend will handle the redirect or success message.
                send_json_response(['message' => 'Form submitted successfully'], 201);
            }
            // Handle requests to /api/forms/{id}/submissions
            elseif ($method === 'GET' && is_numeric($resource_id) && $sub_resource === 'submissions') {
                // --- READ all submissions for a specific form ---
                // Route: GET /api/forms/{id}/submissions

                // First, get the form details (especially the structure for table headers)
                $stmt = $pdo->prepare("SELECT id, name, structure FROM forms WHERE id = ?");
                $stmt->execute([$resource_id]);
                $form = $stmt->fetch();

                if ($form === false) {
                    send_json_response(['message' => "Form with ID {$resource_id} not found"], 404);
                }
                $form['structure'] = json_decode($form['structure'], true);

                // Now, get all submissions for that form
                $stmt = $pdo->prepare("SELECT id, data, submitted_at FROM submissions WHERE form_id = ? ORDER BY submitted_at DESC");
                $stmt->execute([$resource_id]);
                $submissions = $stmt->fetchAll();

                // Decode the 'data' field for each submission
                $submissions = array_map(function($sub) {
                    $sub['data'] = json_decode($sub['data'], true);
                    return $sub;
                }, $submissions);

                // Combine into a single response object
                $response_data = [
                    'form' => $form,
                    'submissions' => $submissions
                ];

                send_json_response($response_data);
            }
            // Handle other requests related to forms (like submissions) in future steps
            else {
                send_json_response(['message' => 'Invalid method or endpoint for forms resource'], 405); // 405 Method Not Allowed
            }
            break;

        // Add other resources like 'submissions' here in the future
        // case 'submissions':
        //     ...
        //     break;

        default:
            // Handle unknown resources
            send_json_response(['message' => 'API resource not found'], 404);
            break;
    }
} catch (PDOException $e) {
    // Catch any database errors that occur during query execution
    // In production, you should log this error instead of echoing it.
    send_json_response(['message' => "Database query failed: " . $e->getMessage()], 500);
} catch (Exception $e) {
    // Catch any other unexpected errors
    send_json_response(['message' => "An unexpected error occurred: " . $e->getMessage()], 500);
}
