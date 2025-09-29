<?php
// generate.php — Generate OTP for password reset
header("Content-Type: application/json");
include "db.php";

// Accept JSON or form data
if (!empty($_SERVER["CONTENT_TYPE"]) && stripos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
    $_POST = json_decode(file_get_contents("php://input"), true) ?? [];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mobile = $_POST["mobile_number"] ?? '';

    if (!$mobile) {
        echo json_encode(["status" => "error", "message" => "Mobile number is required"]);
        exit;
    }

    // Check if user exists
    $stmt = $conn->prepare("SELECT s_no FROM auth WHERE mobile_number = ?");
    $stmt->bind_param("s", $mobile);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows !== 1) {
        echo json_encode(["status" => "error", "message" => "Mobile number not found"]);
        exit;
    }

    // Generate OTP and expiry
    $otp = strval(rand(1000, 9999));
    $expiry = date('Y-m-d H:i:s', time() + 10 * 60); // 10 minutes from now

    // Update DB
    $update = $conn->prepare("UPDATE auth SET otp_code = ?, otp_expiry = ? WHERE mobile_number = ?");
    $update->bind_param("sss", $otp, $expiry, $mobile);
    if ($update->execute()) {
        // In production: send $otp via SMS here
        echo json_encode([
            "status" => "success",
            "message" => "OTP generated successfully",
            "otp_debug" => $otp // REMOVE this in production!
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error"]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
