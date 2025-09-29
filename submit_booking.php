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
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('User not logged in');
        }

        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            throw new Exception('Invalid JSON input');
        }

        // Extract and validate input
        $companyName = trim($input['company_name'] ?? '');
        $bookingTime = trim($input['booking_time'] ?? '');
        $quantityKg = intval($input['quantity_kg'] ?? 0);
        $mangoVariety = trim($input['mango_variety'] ?? '');
        $harvestDate = trim($input['harvest_date'] ?? '');
        $ripenessLevel = trim($input['ripeness_level'] ?? 'fully_ripe');
        $colour = trim($input['colour'] ?? 'yellow');
        $size = trim($input['size'] ?? 'medium');
        $bruisingLevel = trim($input['bruising_level'] ?? 'none');
        $pestPresence = trim($input['pest_presence'] ?? 'no');
        $comments = trim($input['comments'] ?? '');
        $photo1 = trim($input['photo_1'] ?? '');
        $photo2 = trim($input['photo_2'] ?? '');
        $photo3 = trim($input['photo_3'] ?? '');

        // Validation
        if (empty($companyName) || empty($bookingTime) || $quantityKg <= 0) {
            throw new Exception('Company name, booking time, and quantity are required');
        }

        $userId = $_SESSION['user_id'];

        // Insert booking
        $stmt = $conn->prepare("
            INSERT INTO bookings (s_no, company_name, booking_time, quantity_kg, status) 
            VALUES (?, ?, ?, ?, 'Pending')
        ");
        $stmt->bind_param("issi", $userId, $companyName, $bookingTime, $quantityKg);

        if ($stmt->execute()) {
            $bookingId = $conn->insert_id;
            
            // Insert quality details if provided
            if (!empty($mangoVariety) || !empty($harvestDate)) {
                $qualityStmt = $conn->prepare("
                    INSERT INTO mango_quality (farmer_id, variety, harvest_date, estimated_quantity, 
                                             ripeness_level, color, size_grade, bruising_level, pest_presence, images) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $images = json_encode([$photo1, $photo2, $photo3]);
                $qualityStmt->bind_param("ississssss", $userId, $mangoVariety, $harvestDate, $quantityKg,
                                       $ripenessLevel, $colour, $size, $bruisingLevel, $pestPresence, $images);
                $qualityStmt->execute();
            }
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Booking submitted successfully!',
                'booking_id' => $bookingId
            ]);
        } else {
            throw new Exception('Failed to create booking: ' . $conn->error);
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