<?php
include "db.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT s_no, full_name, mobile_number, email, password, user_role FROM auth WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['s_no'] = $row['s_no'];
            $_SESSION['full_name'] = $row['full_name'];
            $_SESSION['user_role'] = $row['user_role'];
            echo "Login successful!";
            // header("Location: dashboard.php");
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "Email not found!";
    }
}
?>
