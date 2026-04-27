<?php
include "../../db.php";

header('Content-Type: application/json; charset=utf-8');

// 🔹 Get input (JSON বা form-data দুটোই)
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    $data = $_POST;
}

// 🔹 Get values
$phone = trim($data['phone'] ?? '');
$password = trim($data['password'] ?? '');

// 🔹 Validation
if ($phone == '' || $password == '') {
    echo json_encode([
        "status" => 400,
        "message" => "Phone and password are required"
    ]);
    exit;
}

// 🔹 Query
$stmt = $conn->prepare("SELECT id, name, phone, password, status FROM users WHERE phone=? LIMIT 1");
$stmt->bind_param("s", $phone);
$stmt->execute();
$stmt->store_result();

// 🔹 User check
if ($stmt->num_rows == 0) {
    echo json_encode([
        "status" => 404,
        "message" => "User not found"
    ]);
    exit;
}

// 🔹 Fetch
$stmt->bind_result($id, $name, $phone_db, $password_db, $status);
$stmt->fetch();

// 🔹 Status check
if ($status != 1) {
    echo json_encode([
        "status" => 403,
        "message" => "Account inactive"
    ]);
    exit;
}

// 🔹 Password check
if (!password_verify($password, $password_db)) {
    echo json_encode([
        "status" => 401,
        "message" => "Incorrect password"
    ]);
    exit;
}

// 🔹 Success
echo json_encode([
    "status" => 200,
    "message" => "Login successful",
    "data" => [
        "id" => $id,
        "name" => $name,
        "phone" => $phone_db
    ]
]);

$stmt->close();
$conn->close();
?>