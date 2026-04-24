<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../../db.php";

header('Content-Type: application/json; charset=utf-8');

// 🔹 Get JSON input
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {

    $data = $_POST;

}

// 🔹 Get fields
$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');
$password = trim($data['password'] ?? '');

// 🔹 Validation
if (empty($name) || empty($email) || empty($phone) || empty($password)) {
    echo json_encode([
        "status" => 400,
        "message" => "All fields are required"
    ]);
    exit;
}

// 🔹 Email format check
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "status" => 400,
        "message" => "Invalid email format"
    ]);
    exit;
}

// 🔹 Password hash
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// 🔹 Check email exists
$checkEmail = $conn->prepare("SELECT id FROM users WHERE email=?");
$checkEmail->bind_param("s", $email);
$checkEmail->execute();
$resEmail = $checkEmail->get_result();

if ($resEmail->num_rows > 0) {
    echo json_encode([
        "status" => 409,
        "message" => "Email already exists"
    ]);
    exit;
}

// 🔹 Check phone exists (recommended)
$checkPhone = $conn->prepare("SELECT id FROM users WHERE phone=?");
$checkPhone->bind_param("s", $phone);
$checkPhone->execute();
$resPhone = $checkPhone->get_result();

if ($resPhone->num_rows > 0) {
    echo json_encode([
        "status" => 409,
        "message" => "Phone already exists"
    ]);
    exit;
}

// 🔹 Insert user
$stmt = $conn->prepare("INSERT INTO users (name,email,password,phone) VALUES (?,?,?,?)");
$stmt->bind_param("ssss", $name, $email, $hashedPassword, $phone);

if ($stmt->execute()) {
    echo json_encode([
        "status" => 200,
        "message" => "Registration successful"
    ]);
} else {
    echo json_encode([
        "status" => 500,
        "message" => "Database error",
        "error" => $stmt->error
    ]);
}
?>