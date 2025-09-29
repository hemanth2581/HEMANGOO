<?php
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
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            throw new Exception('Invalid JSON input');
        }

        // Extract and validate input
        $name = trim($input['name'] ?? '');
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $mobile = trim($input['mobile'] ?? '');
        $role = trim($input['role'] ?? 'Farmer');

        // Validation
        if (empty($name) || empty($email) || empty($password) || empty($mobile)) {
            throw new Exception('All fields are required');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }

        if (strlen($password) < 6) {
            throw new Exception('Password must be at least 6 characters');
        }

        if (!preg_match('/^[0-9]{10}$/', $mobile)) {
            throw new Exception('Mobile number must be 10 digits');
        }

        if (!in_array($role, ['Farmer', 'Admin'])) {
            throw new Exception('Invalid role');
        }

        // Check if email already exists
        $checkStmt = $conn->prepare("SELECT s_no FROM auth WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            throw new Exception('Email already exists');
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user
        $stmt = $conn->prepare("INSERT INTO auth (full_name, email, mobile_number, password, user_role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $mobile, $hashedPassword, $role);

        if ($stmt->execute()) {
            $userId = $conn->insert_id;
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Registration successful!',
                'user' => [
                    'id' => $userId,
                    'name' => $name,
                    'email' => $email,
                    'mobile' => $mobile,
                    'role' => $role
                ]
            ]);
        } else {
            throw new Exception('Registration failed: ' . $conn->error);
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
