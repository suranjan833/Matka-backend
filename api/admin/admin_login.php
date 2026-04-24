<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../../db.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
if (!$data)
    $data = $_POST;

$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(["status" => 0, "message" => "All fields required"]);
    exit;
}

$stmt = $conn->prepare("SELECT id, password FROM admins WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    echo json_encode(["status" => 0, "message" => "Invalid email"]);
    exit;
}

$stmt->bind_result($id, $db_password);
$stmt->fetch();

// 🔥 SIMPLE MATCH (no hashing)
if ($password != $db_password) {
    echo json_encode(["status" => 0, "message" => "Wrong password"]);
    exit;
}

echo json_encode([
    "status" => 1,
    "message" => "Login success",
    "admin_id" => $id
]);
?>