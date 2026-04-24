<?php
include "../../db.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');

// 🔹 POST data
$phone = trim($_POST['phone'] ?? '');
$password = trim($_POST['password'] ?? '');

// 🔹 validation
if (empty($phone) || empty($password)) {
    echo json_encode([
        "status" => 400,
        "message" => "Phone and password are required"
    ]);
    exit;
}

// 🔹 query
$stmt = $conn->prepare("SELECT id,name,phone,password,status FROM users WHERE phone=? AND status=1");
$stmt->bind_param("s", $phone);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    echo json_encode([
        "status" => 404,
        "message" => "User not found"
    ]);
    exit;
}

// 🔹 fetch
$stmt->bind_result($id, $name, $phone_db, $password_db, $status);
$stmt->fetch();

// 🔹 password check
if (password_verify($password, $password_db)) {

    $token = md5(uniqid());

    echo json_encode([
        "status" => 200,
        "message" => "Login successful",
        "data" => [
            "id" => $id,
            "name" => $name,
            "phone" => $phone_db,
            "token" => $token
        ]
    ]);

} else {
    echo json_encode([
        "status" => 401,
        "message" => "Incorrect password"
    ]);
}
?>