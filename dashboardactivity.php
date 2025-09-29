<?php
header("Content-Type: application/json");
session_start();
include "db.php";
$s_no = $_GET['s_no'] ?? 1;

$q = $conn->prepare("SELECT company_name, booking_time, quantity_kg, status FROM bookings WHERE s_no=? ORDER BY booking_time DESC LIMIT 5");
$q->bind_param("i", $s_no);
$q->execute();
$res = $q->get_result();
$list = [];
while ($row = $res->fetch_assoc()) $list[] = $row;
echo json_encode($list);
