<?php
// reset.php

header("Content-Type: application/json");
session_start();
include "db.php";

// ✅ Support raw JSON body
if (!empty($_SERVER["CONTENT_TYPE"]) && stripos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
    $_POST = json_decode(file_get_contents("php://input"), true) ?? [];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Input values
    $mobile  = $_POST["mobile_number"] ?? '';
    $otp     = $_POST["otp"] ?? '';
    $password = $_POST["new_password"] ?? '';
    $confirm  = $_POST["confirm_password"] ?? '';

    // Validate input
    if (!$mobile || !$otp || !$password || !$confirm) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit;
    }

    if ($password !== $confirm) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match."]);
        exit;
    }

    // Find user by mobile number
    $stmt = $conn->prepare("SELECT s_no, otp_code, otp_expiry FROM auth WHERE mobile_number = ?");
    $stmt->bind_param("s", $mobile);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows !== 1) {
        echo json_encode(["status" => "error", "message" => "Mobile number not found."]);
        exit;
    }

    $row = $res->fetch_assoc();

    // OTP checks
    if (empty($row['otp_code']) || $row['otp_code'] !== $otp) {
        echo json_encode(["status" => "error", "message" => "Invalid OTP."]);
        exit;
    }

    if (strtotime($row['otp_expiry']) < time()) {
        echo json_encode(["status" => "error", "message" => "OTP has expired."]);
        exit;
    }

    // Update password and clear OTP
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $update = $conn->prepare("UPDATE auth SET password = ?, otp_code = NULL, otp_expiry = NULL WHERE s_no = ?");
    $update->bind_param("si", $hashed, $row['s_no']);

    if ($update->execute()) {
        echo json_encode(["status" => "success", "message" => "Password reset successful!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error."]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method!"]);
}
?>
