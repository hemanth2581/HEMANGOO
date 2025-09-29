<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$booking_id = $data['booking_id'];
$status = $data['status']; // approved/rejected

if (!in_array($status, ['confirmed', 'rejected'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid status']);
    exit;
}

$stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
$stmt->execute([$status, $booking_id]);

echo json_encode(['success' => true]);
?>
