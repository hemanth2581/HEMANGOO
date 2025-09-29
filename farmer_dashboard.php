<?php
session_start();
include "db.php";

header('Content-Type: application/json');

if (!isset($_SESSION['s_no']) || $_SESSION['user_role'] !== 'Farmer') {
    echo json_encode(['status' => 'error', 'message' => 'Access denied']);
    exit;
}

$farmer_id = $_SESSION['s_no'];
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

// Farmer Bookings Summary
$bookingsStmt = $conn->prepare("
    SELECT 
        b.booking_id,
        b.status,
        b.booking_time,
        b.quantity_kg,
        b.company_name,
        b.created_at
    FROM bookings b
    WHERE b.s_no = ? 
    ORDER BY b.created_at DESC 
    LIMIT 10
");
$bookingsStmt->bind_param("i", $farmer_id);
$bookingsStmt->execute();
$bookingsResult = $bookingsStmt->get_result();

$recent_bookings = [];
while ($row = $bookingsResult->fetch_assoc()) {
    $recent_bookings[] = [
        'booking_id' => $row['booking_id'],
        'status' => $row['status'],
        'booking_time' => $row['booking_time'],
        'quantity_kg' => $row['quantity_kg'],
        'company_name' => $row['company_name'],
        'created_at' => $row['created_at']
    ];
}

// Market Activity
$activityStmt = $conn->prepare("SELECT average_buyers, active_buyers, trend FROM market_activity WHERE date = ?");
$activityStmt->bind_param("s", $date);
$activityStmt->execute();
$activityResult = $activityStmt->get_result();

$market_activity = [
    'average_buyers' => 25,
    'active_buyers' => 18,
    'trend' => 'Stable'
];

if ($activityResult->num_rows > 0) {
    $activityRow = $activityResult->fetch_assoc();
    $market_activity = $activityRow;
}

echo json_encode([
    'status' => 'success',
    'market_price' => $market_price,
    'recent_bookings' => $recent_bookings,
    'market_activity' => $market_activity,
    'total_bookings' => count($recent_bookings)
]);
?>
