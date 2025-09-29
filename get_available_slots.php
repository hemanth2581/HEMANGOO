<?php
session_start();
include "db.php";

header('Content-Type: application/json');

// Get available slots for booking
$stmt = $conn->prepare("
    SELECT 
        s.s_no,
        s.warehouse_id,
        s.slot_timing,
        s.slot_price,
        w.name as warehouse_name,
        w.email as warehouse_email,
        w.mobile_number as warehouse_mobile
    FROM slots s
    JOIN warehouse w ON s.warehouse_id = w.warehouse_id
    ORDER BY s.slot_timing ASC
");

$stmt->execute();
$result = $stmt->get_result();

$slots = [];
while ($row = $result->fetch_assoc()) {
    $slots[] = [
        'slot_id' => $row['s_no'],
        'warehouse_id' => $row['warehouse_id'],
        'slot_timing' => $row['slot_timing'],
        'slot_price' => $row['slot_price'],
        'warehouse_name' => $row['warehouse_name'],
        'warehouse_email' => $row['warehouse_email'],
        'warehouse_mobile' => $row['warehouse_mobile']
    ];
}

echo json_encode([
    'status' => 'success',
    'slots' => $slots
]);
?>
