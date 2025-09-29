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
        // Check if user is logged in and is admin
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
            throw new Exception('Admin access required');
        }

        // Get pending booking requests
        $stmt = $conn->prepare("
            SELECT b.booking_id, b.company_name, b.booking_time, b.quantity_kg, b.status, b.created_at,
                   a.full_name as farmer_name
            FROM bookings b 
            JOIN auth a ON b.s_no = a.s_no
            WHERE b.status = 'Pending' 
            ORDER BY b.created_at DESC
        ");
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
                'created_at' => $row['created_at'],
                'farmer_name' => $row['farmer_name']
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