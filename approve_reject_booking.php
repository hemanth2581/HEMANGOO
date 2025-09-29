<?php
session_start();
include "db.php";

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Check if user is logged in and is admin
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
            throw new Exception('Admin access required');
        }

        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            throw new Exception('Invalid JSON input');
        }

        $bookingId = intval($input['booking_id'] ?? 0);
        $action = trim($input['action'] ?? '');
        $comments = trim($input['comments'] ?? '');

        // Validation
        if ($bookingId <= 0) {
            throw new Exception('Invalid booking ID');
        }

        if (!in_array($action, ['approve', 'reject'])) {
            throw new Exception('Invalid action. Must be approve or reject');
        }

        // Check if booking exists and is pending
        $checkStmt = $conn->prepare("SELECT booking_id FROM bookings WHERE booking_id = ? AND status = 'Pending'");
        $checkStmt->bind_param("i", $bookingId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception('Booking not found or already processed');
        }

        // Update booking status
        $newStatus = $action === 'approve' ? 'Confirmed' : 'Cancelled';
        $updateStmt = $conn->prepare("UPDATE bookings SET status = ? WHERE booking_id = ?");
        $updateStmt->bind_param("si", $newStatus, $bookingId);

        if ($updateStmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => "Booking $action" . ($action === 'approve' ? 'd' : 'ed') . ' successfully!'
            ]);
        } else {
            throw new Exception('Failed to update booking status');
        }

    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed'
    ]);
}
?>