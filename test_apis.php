<?php
/**
 * API Test Script
 * Test all the APIs to ensure they're working correctly
 */

echo "Testing Hemango APIs...\n\n";

// Test configuration
$baseUrl = "http://localhost/Backend/api/v2";
$testUserId = 2; // Farmer user ID
$testFactoryId = 1;
$testDate = date('Y-m-d');

// Helper function to make HTTP requests
function makeRequest($url, $method = 'GET', $data = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    if ($method === 'POST' && $data) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'response' => json_decode($response, true)
    ];
}

// Test 1: Get Factories
echo "1. Testing Get Factories API...\n";
$result = makeRequest("$baseUrl/factories/list.php");
if ($result['code'] === 200 && $result['response']['status'] === 'success') {
    echo "✅ Factories API working - Found " . count($result['response']['factories']) . " factories\n";
} else {
    echo "❌ Factories API failed: " . ($result['response']['message'] ?? 'Unknown error') . "\n";
}

// Test 2: Get Mango Varieties
echo "\n2. Testing Get Mango Varieties API...\n";
$result = makeRequest("$baseUrl/mango/varieties.php");
if ($result['code'] === 200 && $result['response']['status'] === 'success') {
    echo "✅ Mango Varieties API working - Found " . count($result['response']['varieties']) . " varieties\n";
} else {
    echo "❌ Mango Varieties API failed: " . ($result['response']['message'] ?? 'Unknown error') . "\n";
}

// Test 3: Get Available Slots
echo "\n3. Testing Get Available Slots API...\n";
$result = makeRequest("$baseUrl/slots/get_available.php?factory_id=$testFactoryId&date=$testDate");
if ($result['code'] === 200 && $result['response']['status'] === 'success') {
    echo "✅ Available Slots API working - Found " . count($result['response']['slots']) . " slots\n";
} else {
    echo "❌ Available Slots API failed: " . ($result['response']['message'] ?? 'Unknown error') . "\n";
}

// Test 4: Book a Slot (if slots are available)
echo "\n4. Testing Book Slot API...\n";
$slotsResult = makeRequest("$baseUrl/slots/get_available.php?factory_id=$testFactoryId&date=$testDate");
if ($slotsResult['code'] === 200 && $slotsResult['response']['status'] === 'success' && count($slotsResult['response']['slots']) > 0) {
    $slot = $slotsResult['response']['slots'][0];
    $bookingData = [
        'user_id' => $testUserId,
        'factory_id' => $testFactoryId,
        'slot_id' => $slot['id'],
        'mango_type' => 'Mango',
        'mango_variety' => 'Alphonso',
        'quantity' => 100,
        'unit' => 'kg'
    ];
    
    $result = makeRequest("$baseUrl/slots/book_slot.php", 'POST', $bookingData);
    if ($result['code'] === 200 && $result['response']['status'] === 'success') {
        echo "✅ Book Slot API working - Booking ID: " . $result['response']['booking_id'] . "\n";
        $bookingId = $result['response']['booking_id'];
    } else {
        echo "❌ Book Slot API failed: " . ($result['response']['message'] ?? 'Unknown error') . "\n";
    }
} else {
    echo "⚠️  No available slots to test booking\n";
}

// Test 5: Get Pending Bookings (Admin)
echo "\n5. Testing Get Pending Bookings API...\n";
$result = makeRequest("$baseUrl/admin/pending_bookings.php");
if ($result['code'] === 200 && $result['response']['status'] === 'success') {
    echo "✅ Pending Bookings API working - Found " . count($result['response']['bookings']) . " pending bookings\n";
} else {
    echo "❌ Pending Bookings API failed: " . ($result['response']['message'] ?? 'Unknown error') . "\n";
}

// Test 6: Get Farmer Bookings
echo "\n6. Testing Get Farmer Bookings API...\n";
$result = makeRequest("$baseUrl/bookings/my.php?user_id=$testUserId");
if ($result['code'] === 200 && $result['response']['status'] === 'success') {
    echo "✅ Farmer Bookings API working - Found " . count($result['response']['bookings']) . " bookings\n";
} else {
    echo "❌ Farmer Bookings API failed: " . ($result['response']['message'] ?? 'Unknown error') . "\n";
}

// Test 7: Update Booking Status (if we have a booking)
if (isset($bookingId)) {
    echo "\n7. Testing Update Booking Status API...\n";
    $updateData = [
        'booking_id' => $bookingId,
        'action' => 'approve',
        'admin_notes' => 'Test approval',
        'admin_id' => 1
    ];
    
    $result = makeRequest("$baseUrl/admin/update_booking_status.php", 'POST', $updateData);
    if ($result['code'] === 200 && $result['response']['status'] === 'success') {
        echo "✅ Update Booking Status API working - Status updated to: " . $result['response']['new_status'] . "\n";
    } else {
        echo "❌ Update Booking Status API failed: " . ($result['response']['message'] ?? 'Unknown error') . "\n";
    }
} else {
    echo "\n7. ⚠️  Skipping Update Booking Status test (no booking created)\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "API Testing completed!\n";
echo "If all tests passed, your booking system is ready to use.\n";
echo "\nNext steps:\n";
echo "1. Test the Android app with these APIs\n";
echo "2. Verify the complete booking flow works\n";
echo "3. Check that admin can approve/reject bookings\n";
?>
