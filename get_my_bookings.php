<?php
session_start();
include "db.php";

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('User not logged in');
        }

        $userId = $_SESSION['user_id'];

        // Get user's bookings
        $stmt = $conn->prepare("
            SELECT booking_id, company_name, booking_time, quantity_kg, status, created_at
            FROM bookings 
            WHERE s_no = ? 
            ORDER BY created_at DESC
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $bookings = [];
        while ($row = $result->fetch_assoc()) {
            $bookings[] = [
                'id' => $row['booking_id'],
                'company_name' => $row['company_name'],
                'booking_time' => $row['booking_time'],
                'quantity_kg' => $row['quantity_kg'],
                'status' => $row['status'],
                'created_at' => $row['created_at']
            ];
        }

        echo json_encode([
            'status' => 'success',
            'bookings' => $bookings
        ]);

    } catch (Exception $e) {
        http_response_code(500);
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