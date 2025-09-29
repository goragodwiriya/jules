<?php
/**
 * Database configuration
 *
 * Please update these settings with your actual database credentials.
 * You also need to import the `backend/schema.sql` file into your MySQL database
 * for the application to work correctly.
 */

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'appointment_scheduler');

// Attempt to connect to MySQL database
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Set charset to utf8mb4
mysqli_set_charset($link, "utf8mb4");
?>