<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farmer') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

$farmer_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

// Validate and sanitize inputs here

$stmt = $pdo->prepare("INSERT INTO mango_quality (farmer_id, variety, harvest_date, estimated_quantity, ripeness_level, color, size_grade, bruising_level, pest)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->execute([
    $farmer_id,
    $data['variety'],
    $data['harvest_date'],
    $data['estimated_quantity'],
    $data['ripeness_level'],
    $data['color'],
    $data['size_grade'],
    $data['bruising_level'],
    $data['pest']
]);

echo json_encode(['success' => true, 'quality_id' => $pdo->lastInsertId()]);
?>
