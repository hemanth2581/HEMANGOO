<?php
session_start();
include "db.php";

header('Content-Type: application/json');

if (!isset($_SESSION['s_no']) || $_SESSION['user_role'] !== 'Admin') {
    echo json_encode(['status' => 'error', 'message' => 'Access denied']);
    exit;
}

$date = date('Y-m-d');

// Market Prices
$pricesStmt = $conn->prepare("SELECT price FROM market_prices WHERE date = ?");
$pricesStmt->bind_param("s", $date);
$pricesStmt->execute();
$pricesResult = $pricesStmt->get_result();
$market_price = 150; // Default price
if ($pricesResult->num_rows > 0) {
    $priceRow = $pricesResult->fetch_assoc();
    $market_price = $priceRow['price'];
}

// Recent Bookings
$bookingsStmt = $conn->prepare("
    SELECT 
        b.booking_id,
        a.full_name AS farmer_name,
        b.booking_time,
        b.quantity_kg,
        b.status,
        b.company_name,
        b.created_at
    FROM bookings b
    JOIN auth a ON b.s_no = a.s_no
    ORDER BY b.created_at DESC 
    LIMIT 20
");
$bookingsStmt->execute();
$bookingsResult = $bookingsStmt->get_result();

$recent_bookings = [];
while ($row = $bookingsResult->fetch_assoc()) {
    $recent_bookings[] = [
        'booking_id' => $row['booking_id'],
        'farmer_name' => $row['farmer_name'],
        'booking_time' => $row['booking_time'],
        'quantity_kg' => $row['quantity_kg'],
        'status' => $row['status'],
        'company_name' => $row['company_name'],
        'created_at' => $row['created_at']
    ];
}

// Pending Requests
$pendingStmt = $conn->prepare("
    SELECT 
        b.booking_id,
        a.full_name AS farmer_name,
        b.booking_time,
        b.quantity_kg,
        b.company_name,
        b.created_at
    FROM bookings b
    JOIN auth a ON b.s_no = a.s_no
    WHERE b.status = 'Pending'
    ORDER BY b.created_at DESC
");
$pendingStmt->execute();
$pendingResult = $pendingStmt->get_result();

$pending_requests = [];
while ($row = $pendingResult->fetch_assoc()) {
    $pending_requests[] = [
        'booking_id' => $row['booking_id'],
        'farmer_name' => $row['farmer_name'],
        'booking_time' => $row['booking_time'],
        'quantity_kg' => $row['quantity_kg'],
        'company_name' => $row['company_name'],
        'created_at' => $row['created_at']
    ];
}

// Statistics
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
$stats = $statsResult->fetch_assoc();

echo json_encode([
    'status' => 'success',
    'market_price' => $market_price,
    'recent_bookings' => $recent_bookings,
    'pending_requests' => $pending_requests,
    'statistics' => $stats
]);
?>
