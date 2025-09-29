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
$slot_id = $data['slot_id'];
$quality_id = $data['quality_id'];

$stmt = $pdo->prepare("INSERT INTO bookings (farmer_id, slot_id, quality_id, status, created_at)
                       VALUES (?, ?, ?, 'pending', NOW())");

$stmt->execute([$farmer_id, $slot_id, $quality_id]);

echo json_encode(['success' => true]);
?>
