<?php
header("Content-Type: application/json");
include "db.php";

// Average price (today)
$avg = $conn->query("SELECT AVG(price) as avg_price FROM market_prices WHERE date = CURDATE()")->fetch_assoc();
$avg_price = $avg ? floatval($avg['avg_price']) : 0;

// Active buyers count
$buyers = $conn->query("SELECT COUNT(*) as actives FROM buyers WHERE is_active=1")->fetch_assoc();
$active_buyers = $buyers ? intval($buyers['actives']) : 0;

// Market trend (simple: price % difference compared to yesterday)
$today = $conn->query("SELECT price FROM market_prices WHERE date=CURDATE() ORDER BY id DESC LIMIT 1")->fetch_assoc();
$yest = $conn->query("SELECT price FROM market_prices WHERE date=DATE_SUB(CURDATE(), INTERVAL 1 DAY) ORDER BY id DESC LIMIT 1")->fetch_assoc();
$trend = ($yest && $yest['price'] != 0)
    ? round(100*($today['price'] - $yest['price'])/$yest['price'], 2)
    : 0;

echo json_encode([
    "average_price" => $avg_price,
    "active_buyers" => $active_buyers,
    "market_trend_percent" => $trend
]);
