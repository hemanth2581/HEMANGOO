<?php
include "db.php";

header("Content-Type: application/json");

// Retrieve POST variables safely
$warehouseId = $_POST['warehouse_id'] ?? '';
$slottime = $_POST['slot_timing'] ?? '';
$slotprice = $_POST['slot_price'] ?? '';

// Validate input
if (empty($warehouseId) || empty($slottime) || empty($slotprice)) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}

// Prepare statement
$stmt = $conn->prepare("INSERT INTO slots (warehouse_id, slot_timing, slot_price) VALUES (?, ?, ?)");

if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
    exit;
}

// Bind parameters
$stmt->bind_param("sss", $warehouseId, $slottime, $slotprice);

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
