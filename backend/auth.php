<?php
require_once "config.php";

header("Content-Type: application/json; charset=UTF-8");

$response = ["status" => "error", "message" => "Invalid request"];
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'register') {
        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';

        if (empty(trim($username)) || empty(trim($password))) {
            $response['message'] = "Please enter username and password.";
        } else {
            $sql = "SELECT id FROM users WHERE username = ?";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "s", $param_username);
                $param_username = $username;
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_store_result($stmt);
                    if (mysqli_stmt_num_rows($stmt) == 1) {
                        $response['message'] = "This username is already taken.";
                    } else {
                        $sql_insert = "INSERT INTO users (username, password_hash) VALUES (?, ?)";
                        if ($stmt_insert = mysqli_prepare($link, $sql_insert)) {
                            mysqli_stmt_bind_param($stmt_insert, "ss", $param_username, $param_password);
                            $param_password = password_hash($password, PASSWORD_DEFAULT);
                            if (mysqli_stmt_execute($stmt_insert)) {
                                $response = ["status" => "success", "message" => "User registered successfully."];
                            } else {
                                $response['message'] = "Something went wrong. Please try again later.";
                            }
                            mysqli_stmt_close($stmt_insert);
                        }
                    }
                }
                mysqli_stmt_close($stmt);
            }
        }
    } elseif ($action === 'login') {
        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';

        if(empty(trim($username)) || empty(trim($password))){
            $response['message'] = "Please enter username and password.";
        } else {
            $sql = "SELECT id, username, password_hash FROM users WHERE username = ?";
            if($stmt = mysqli_prepare($link, $sql)){
                mysqli_stmt_bind_param($stmt, "s", $param_username);
                $param_username = $username;
                if(mysqli_stmt_execute($stmt)){
                    mysqli_stmt_store_result($stmt);
                    if(mysqli_stmt_num_rows($stmt) == 1){
                        mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                        if(mysqli_stmt_fetch($stmt)){
                            if(password_verify($password, $hashed_password)){
                                // Password is correct, start a new session
                                session_start();
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username;
                                $response = ["status" => "success", "message" => "Login successful."];
                            } else {
                                $response['message'] = "The password you entered was not valid.";
                            }
                        }
                    } else {
                        $response['message'] = "No account found with that username.";
                    }
                } else {
                    $response['message'] = "Oops! Something went wrong. Please try again later.";
                }
                mysqli_stmt_close($stmt);
            }
        }
    } elseif ($action === 'logout') {
        session_start();
        $_SESSION = array();
        session_destroy();
        $response = ["status" => "success", "message" => "Logged out successfully."];
    }
}

echo json_encode($response);
mysqli_close($link);
?>