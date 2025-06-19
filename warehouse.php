<?php
include "db.php";

header("Content-Type: application/json");

// Retrieve POST variables safely
$warehouseId   = $_POST['warehouse_id'] ?? '';
$name          = $_POST['name'] ?? '';
$email         = $_POST['email'] ?? '';
$mobileNumber  = $_POST['mobile_number'] ?? '';
$password      = $_POST['password'] ?? '';

// Validate input
if (empty($warehouseId) || empty($name) || empty($email) || empty($mobileNumber) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}

// Prepare statement
$stmt = $conn->prepare("INSERT INTO warehouse (warehouse_id, name, email, mobile_number, password) VALUES (?, ?, ?, ?, ?)");

if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
    exit;
}

// Bind parameters
$stmt->bind_param("sssss", $warehouseId, $name, $email, $mobileNumber, $password);

// Execute and check success
if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Values inserted successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Insert failed: " . $stmt->error]);
}

// Cleanup
$stmt->close();
$conn->close();
?>
