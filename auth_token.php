<?php
// Auth token helper for Hemangoo mobile API
// Provides simple bearer-token generation & validation using user_sessions table

include_once __DIR__ . "/db.php";

if (!function_exists('generateToken')) {
    /**
     * Generate (or update) a long-lived token for a user and store in user_sessions
     *
     * @param int $userId
     * @param int $validDays validity period in days (default 30)
     * @return string Bearer token
     */
    function generateToken(int $userId, int $validDays = 30): string
    {
        global $conn;
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$validDays} days"));

        // Insert or update existing session row
        $stmt = $conn->prepare("INSERT INTO user_sessions (user_id, token, expires_at) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE token = VALUES(token), expires_at = VALUES(expires_at)");
        $stmt->bind_param("iss", $userId, $token, $expiresAt);
        $stmt->execute();
        return $token;
    }
}

if (!function_exists('verifyToken')) {
    /**
     * Validate a bearer token and return the associated user_id or null
     * @param string $token
     * @return int|null
     */
    function verifyToken(string $token): ?int
    {
        global $conn;
        $stmt = $conn->prepare("SELECT user_id FROM user_sessions WHERE token = ? AND expires_at > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return intval($row['user_id']);
        }
        return null;
    }
}
?>