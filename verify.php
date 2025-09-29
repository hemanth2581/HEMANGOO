<?php
// forgot_password_reset.php

header("Content-Type: application/json");
include "db.php";

if (!empty($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
    $_POST = json_decode(file_get_contents("php://input"), true) ?? [];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Step 1: Validate input
    $mobile = $_POST["mobile_number"] ?? '';
    $otp    = $_POST["otp"] ?? '';
    $password = $_POST["new_password"] ?? '';
    $confirm = $_POST["confirm_password"] ?? '';

    if (!$mobile || !$otp || !$password || !$confirm) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit;
    }

    if ($password !== $confirm) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match."]);
        exit;
    }

    // Step 2: Find user and check OTP
    $stmt = $conn->prepare("SELECT s_no, otp_code, otp_expiry FROM auth WHERE mobile_number = ?");
    $stmt->bind_param("s", $mobile);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows !== 1) {
        echo json_encode(["status" => "error", "message" => "Mobile number not found."]);
        exit;
    }

    $row = $res->fetch_assoc();
    if (
        $row['otp_code'] !== $otp ||
        !$row['otp_code'] ||
        strtotime($row['otp_expiry']) < time()
    ) {
        echo json_encode(["status" => "error", "message" => "Invalid or expired OTP."]);
        exit;
    }

    // Step 3: Hash new password and update
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE auth SET password = ?, otp_code = NULL, otp_expiry = NULL WHERE s_no = ?");
    $stmt->bind_param("si", $hashed, $row['s_no']);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Password reset successful!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error."]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method!"]);
}
?>
