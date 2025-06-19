<?php
// Include your database connection
include "db.php";

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
        $full_name = $_POST['full_name'];
        $mobile_number = $_POST['mobile_number'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $user_role = $_POST['user_role'];

        // Check if email already exists
        $check = $conn->prepare("SELECT * FROM auth WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(["status" => "error", "message" => "Email already registered!"]);
        } else {
            $stmt = $conn->prepare("INSERT INTO auth (full_name, mobile_number, email, password, user_role) VALUES (?, ?, ?, ?, ?)");
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
?>
