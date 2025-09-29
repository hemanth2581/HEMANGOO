<?php
// Tell the browser/clients that we're returning JSON
header('Content-Type: application/json');

// Include your database connection
include "db.php";

/**
 * Support JSON body parsing:
 * If Content-Type is application/json, decode php://input
 * and populate $_POST so the rest of the code works normally.
 */
if (!empty($_SERVER["CONTENT_TYPE"]) && str_contains($_SERVER["CONTENT_TYPE"], "application/json")) {
    $input = json_decode(file_get_contents("php://input"), true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($input)) {
        $_POST = $input; // Overwrite $_POST with JSON body data
    }
}

// Only process POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if all required fields are present
    if (
        isset($_POST['full_name']) &&
        isset($_POST['mobile_number']) &&
        isset($_POST['email']) &&
        isset($_POST['password']) &&
        isset($_POST['user_role'])
    ) {
        $full_name     = trim($_POST['full_name']);
        $mobile_number = trim($_POST['mobile_number']);
        $email         = trim($_POST['email']);
        $password      = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $user_role     = trim($_POST['user_role']);

        // Check if email already exists
        $check = $conn->prepare("SELECT * FROM auth WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(["status" => "error", "message" => "Email already registered!"]);
        } else {
            // Insert new user
            $stmt = $conn->prepare(
                "INSERT INTO auth (full_name, mobile_number, email, password, user_role) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("sssss", $full_name, $mobile_number, $email, $password, $user_role);

            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Signup successful!"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Database error: " . $stmt->error]);
            }
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Missing required fields!"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method!"]);
}
