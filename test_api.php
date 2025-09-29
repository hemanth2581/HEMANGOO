<?php
// Simple test script to verify the admin API endpoints
header('Content-Type: application/json');

echo "Testing Admin API Endpoints\n\n";

// Test 1: Get pending bookings
echo "1. Testing GET /api/v2/admin/pending_bookings.php\n";
echo "URL: http://localhost/Backend/api/v2/admin/pending_bookings.php\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/Backend/api/v2/admin/pending_bookings.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n\n";

// Test 2: Update booking status (approve)
echo "2. Testing POST /api/v2/admin/update_booking_status.php (approve)\n";
echo "URL: http://localhost/Backend/api/v2/admin/update_booking_status.php\n";

$data = json_encode([
    'booking_id' => 1,
    'action' => 'approve',
    'admin_notes' => 'Approved by test script',
    'rejection_reason' => '',
    'admin_id' => 1
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/Backend/api/v2/admin/update_booking_status.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n\n";

echo "Test completed!\n";
?>

