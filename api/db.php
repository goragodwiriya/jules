<?php
/**
 * Database Connection and Configuration
 *
 * Please update the database credentials to match your local setup.
 * The database and tables can be created by importing `database.sql`.
 */

// --- Database Configuration ---
// IMPORTANT: Replace with your actual database credentials.
define('DB_HOST', '127.0.0.1');      // Database host (e.g., 'localhost' or '127.0.0.1')
define('DB_NAME', 'form_builder_db'); // The name of the database created with database.sql
define('DB_USER', 'db_user');         // Your database username
define('DB_PASS', 'db_password');     // Your database password

// --- DSN (Data Source Name) for PDO ---
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";

// --- PDO Connection Options ---
$options = [
    // Throw a PDOException on error, so we can catch it.
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    // Fetch results as associative arrays.
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // Disable emulation of prepared statements for security and performance.
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// --- Global PDO variable ---
$pdo = null;

// --- Establish the connection ---
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // If the connection fails, stop the script and return a generic server error.
    // This prevents leaking sensitive connection details.
    http_response_code(500);
    // Use a generic error message in production.
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed. Please check server configuration.'
        // 'debug_message' => $e->getMessage() // Uncomment for development debugging only
    ]);
    exit; // Important: stop execution if the database connection is not available.
}

// The $pdo variable is now globally available for use in any script that includes this file.
