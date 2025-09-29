<?php
header("Content-Type: application/json");
session_start();
include "db.php";

// Accept JSON request
if ($_SERVER["CONTENT_TYPE"] == "application/json") {
    $_POST = json_decode(file_get_contents("php://input"), true) ?? [];
}
$s_no = $_POST["s_no"] ?? 1;
$company_name = $_POST["company_name"] ?? "";
$booking_time = $_POST["booking_time"] ?? "";
$quantity_kg = $_POST["quantity_kg"] ?? 0;

// You might want to check for duplicate bookings etc.

if (!$company_name || !$booking_time || !$quantity_kg) {
    echo json_encode(["status" => "error", "message" => "Missing required fields."]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO bookings (s_no, company_name, booking_time, quantity_kg, status) VALUES (?, ?, ?, ?, 'Confirmed')");
$stmt->bind_param("issi", $s_no, $company_name, $booking_time, $quantity_kg);

if ($stmt->execute())
    echo json_encode(["status" => "success"]);
else
    echo json_encode(["status" => "error", "message" => $stmt->error]);
