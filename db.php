<?php
// Database configuration - Unified for all APIs
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hemango";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Helper function to get database connection (for PDO-based APIs)
function getDatabaseConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=localhost;dbname=hemango;charset=utf8mb4",
            "root",
            "",
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ]
        );
        return $pdo;
    } catch(PDOException $exception) {
        error_log("Database connection error: " . $exception->getMessage());
        throw new Exception("Database connection failed");
    }
}
?>