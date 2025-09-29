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
        $userRole = $_SESSION['role'];

        // Get today's market price
        $priceStmt = $conn->prepare("SELECT price FROM market_prices WHERE date = CURDATE() ORDER BY id DESC LIMIT 1");
        $priceStmt->execute();
        $priceResult = $priceStmt->get_result();
        $marketPrice = $priceResult->num_rows > 0 ? $priceResult->fetch_assoc()['price'] : 0;

        // Get market activity
        $activityStmt = $conn->prepare("SELECT average_buyers, active_buyers, trend FROM market_activity WHERE date = CURDATE() ORDER BY id DESC LIMIT 1");
        $activityStmt->execute();
        $activityResult = $activityStmt->get_result();
        $marketActivity = $activityResult->num_rows > 0 ? $activityResult->fetch_assoc() : [
            'average_buyers' => 0,
            'active_buyers' => 0,
            'trend' => 'stable'
        ];

        if ($userRole === 'Farmer') {
            // Get farmer's bookings
            $bookingsStmt = $conn->prepare("
                SELECT b.booking_id, b.company_name, b.booking_time, b.quantity_kg, b.status, b.created_at
                FROM bookings b 
                WHERE b.s_no = ? 
                ORDER BY b.created_at DESC 
                LIMIT 10
            ");
            $bookingsStmt->bind_param("i", $userId);
            $bookingsStmt->execute();
            $bookingsResult = $bookingsStmt->get_result();
            
            $recentBookings = [];
            while ($row = $bookingsResult->fetch_assoc()) {
                $recentBookings[] = [
                    'id' => $row['booking_id'],
                    'company_name' => $row['company_name'],
                    'booking_time' => $row['booking_time'],
                    'quantity_kg' => $row['quantity_kg'],
                    'status' => $row['status'],
                    'created_at' => $row['created_at']
                ];
            }

            // Get total bookings count
            $totalStmt = $conn->prepare("SELECT COUNT(*) as total FROM bookings WHERE s_no = ?");
            $totalStmt->bind_param("i", $userId);
            $totalStmt->execute();
            $totalResult = $totalStmt->get_result();
            $totalBookings = $totalResult->fetch_assoc()['total'];

            echo json_encode([
                'status' => 'success',
                'market_price' => $marketPrice,
                'market_activity' => $marketActivity,
                'recent_bookings' => $recentBookings,
                'total_bookings' => $totalBookings
            ]);

        } else if ($userRole === 'Admin') {
            // Get pending requests
            $pendingStmt = $conn->prepare("
                SELECT b.booking_id, b.company_name, b.booking_time, b.quantity_kg, b.status, b.created_at,
                       a.full_name as farmer_name
                FROM bookings b 
                JOIN auth a ON b.s_no = a.s_no
                WHERE b.status = 'Pending' 
                ORDER BY b.created_at DESC 
                LIMIT 10
            ");
            $pendingStmt->execute();
            $pendingResult = $pendingStmt->get_result();
            
            $pendingRequests = [];
            while ($row = $pendingResult->fetch_assoc()) {
                $pendingRequests[] = [
                    'id' => $row['booking_id'],
                    'company_name' => $row['company_name'],
                    'booking_time' => $row['booking_time'],
                    'quantity_kg' => $row['quantity_kg'],
                    'status' => $row['status'],
                    'created_at' => $row['created_at'],
                    'farmer_name' => $row['farmer_name']
                ];
            }

            // Get statistics
            $statsStmt = $conn->prepare("
                SELECT 
                    COUNT(*) as total_bookings,
                    SUM(CASE WHEN status = 'Confirmed' THEN 1 ELSE 0 END) as confirmed_bookings,
                    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending_bookings,
                    SUM(CASE WHEN status = 'Cancelled' THEN 1 ELSE 0 END) as cancelled_bookings
                FROM bookings
            ");
            $statsStmt->execute();
            $statsResult = $statsStmt->get_result();
            $statistics = $statsResult->fetch_assoc();

            echo json_encode([
                'status' => 'success',
                'market_price' => $marketPrice,
                'market_activity' => $marketActivity,
                'pending_requests' => $pendingRequests,
                'statistics' => $statistics
            ]);
        }

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