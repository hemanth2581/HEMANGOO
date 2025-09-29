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
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            throw new Exception('Invalid JSON input');
        }

        // Extract and validate input
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $role = trim($input['role'] ?? '');

        // Validation
        if (empty($email) || empty($password) || empty($role)) {
            throw new Exception('All fields are required');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }

        if (!in_array($role, ['Farmer', 'Admin'])) {
            throw new Exception('Invalid role');
        }

        // Check user credentials
        $stmt = $conn->prepare("SELECT s_no, full_name, email, mobile_number, password, user_role FROM auth WHERE email = ? AND user_role = ?");
        $stmt->bind_param("ss", $email, $role);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Store user session
                $_SESSION['user_id'] = $user['s_no'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['mobile'] = $user['mobile_number'];
                $_SESSION['role'] = $user['user_role'];

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Login successful!',
                    'user' => [
                        'id' => $user['s_no'],
                        'name' => $user['full_name'],
                        'email' => $user['email'],
                        'mobile' => $user['mobile_number'],
                        'role' => $user['user_role']
                    ]
                ]);
            } else {
                throw new Exception('Invalid password');
            }
        } else {
            throw new Exception('Invalid email or role');
        }

    } catch (Exception $e) {
        http_response_code(401);
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
