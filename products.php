<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "hemango");

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed"]));
}

$data = json_decode(file_get_contents("php://input"), true);
<?php
header("Content-Type: application/json");

// DB Connection
$conn = new mysqli("localhost", "root", "", "hemango");

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Read and decode JSON
$data = json_decode(file_get_contents("php://input"), true);

// Check if JSON is parsed correctly
if (!$data) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON input"]);
    exit;
}

// Validate required fields
$required_fields = ["name", "location", "mango_variety", "harvest_date", "quantity", "ripeness_level", "colour", "size", "bruising_level", "pest", "photo_1", "photo_2", "photo_3", "comments", "slot_timing", "slot_date", "market_price"];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || trim($data[$field]) === "") {
        echo json_encode(["status" => "error", "message" => "Missing or empty field: $field"]);
        exit;
    }
}

// Prepare statement
$stmt = $conn->prepare("INSERT INTO products (
    name, location, mango_variety, harvest_date, quantity, ripeness_level, colour, size,
    bruising_level, pest, photo_1, photo_2, photo_3, comments, slot_timing, slot_date, market_price
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

// Bind parameters (s = string, i = integer)
$stmt->bind_param(
    "ssssissssssssssss",
    $data["name"],
    $data["location"],
    $data["mango_variety"],
    $data["harvest_date"],
    $data["quantity"],
    $data["ripeness_level"],
    $data["colour"],
    $data["size"],
    $data["bruising_level"],
    $data["pest"],
    $data["photo_1"],
    $data["photo_2"],
    $data["photo_3"],
    $data["comments"],
    $data["slot_timing"],
    $data["slot_date"],
    $data["market_price"]
);

// Execute
if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Product added"]);
} else {
    echo json_encode(["status" => "error", "message" => "Execute failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>

// Validate input
$required_fields = ["name", "location", "mango_variety", "harvest_date", "quantity", "ripeness_level", "colour", "size", "bruising_level", "pest", "photo_1", "photo_2", "photo_3", "comments", "slot_timing", "slot_date", "market_price"];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || $data[$field] === "") {
        echo json_encode(["status" => "error", "message" => "Missing or empty field: $field"]);
        exit;
    }
}

$stmt = $conn->prepare("INSERT INTO products (name, location, mango_variety, harvest_date, quantity, ripeness_level, colour, size, bruising_level, pest, photo_1, photo_2, photo_3, comments, slot_timing, slot_date, market_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
    "ssssissssssssssss",
    $data["name"],
    $data["location"],
    $data["mango_variety"],
    $data["harvest_date"],
    $data["quantity"],
    $data["ripeness_level"],
    $data["colour"],
    $data["size"],
    $data["bruising_level"],
    $data["pest"],
    $data["photo_1"],
    $data["photo_2"],
    $data["photo_3"],
    $data["comments"],
    $data["slot_timing"],
    $data["slot_date"],
    $data["market_price"]
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Product added"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
