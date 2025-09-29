<?php
require 'db.php';

$lat = floatval($_GET['lat'] ?? 0);
$lng = floatval($_GET['lng'] ?? 0);

if (!$lat || !$lng) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing location']);
    exit;
}

$stmt = $pdo->prepare("SELECT id, name, location, latitude, longitude,
    ( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance
    FROM factories
    HAVING distance < 50 ORDER BY distance LIMIT 20");

$stmt->execute([$lat, $lng, $lat]);
$factories = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($factories);
?>
