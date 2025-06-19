<?php

include "db.php";

$farmerId = $_POST['farmer_id'];
$name = $_POST['name'];
$email = $_POST['email'];
$mobileNumber = $_POST['mobile_number'];
$password = $_POST['password'];

if (empty($farmerId) || empty($name) || empty($email) || empty($mobileNumber) || empty($password)) {
    echo "All Fields are Required";
} 

$stmt = $conn->prepare("INSERT INTO farmer (farmer_id , name , email , mobile_number , password) VALUES (? , ? , ? , ? , ?)");
$stmt->bind_param(("issss") , $farmerId , $name , $email , $mobileNumber , $password);
$stmt->execute();

if ($stmt -> num_rows > 0) {
    echo json_encode(["status" => "success" , "message" => "Fields Inserted Successfully"]);
} else {
    echo json_encode(["status" => "failure" , "message" => "Fields Inserted Successfully"]) ;
}

$conn->close()

?>