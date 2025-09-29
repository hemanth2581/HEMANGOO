<?php
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
        // Get available slots with warehouse information
        $stmt = $conn->prepare("
            SELECT s.s_no as id, w.name as warehouse_name, w.email as warehouse_email, 
                   w.mobile_number as warehouse_mobile, s.slot_timing, s.slot_price
            FROM slots s
            JOIN warehouse w ON s.warehouse_id = w.warehouse_id
            ORDER BY s.slot_timing
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        
        $slots = [];
        while ($row = $result->fetch_assoc()) {
            $slots[] = [
                'id' => $row['id'],
                'warehouse_name' => $row['warehouse_name'],
                'warehouse_email' => $row['warehouse_email'],
                'warehouse_mobile' => $row['warehouse_mobile'],
                'slot_timing' => $row['slot_timing'],
                'slot_price' => $row['slot_price']
            ];
        }

        echo json_encode([
            'status' => 'success',
            'slots' => $slots
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