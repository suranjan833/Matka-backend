<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../../db.php";

header('Content-Type: application/json; charset=utf-8');


$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    $data = $_POST;
}

// ✅ Get user_id
$user_id = $data['user_id'] ?? '';

if (empty($user_id)) {
    echo json_encode([
        "status" => 400,
        "message" => "User ID required"
    ]);
    exit;
}

// ✅ Fetch user
$stmt = $conn->prepare("SELECT id, name, email, phone FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    echo json_encode([
        "status" => 200,
        "message" => "Profile fetched successfully",
        "data" => $user
    ]);
} else {
    echo json_encode([
        "status" => 404,
        "message" => "User not found"
    ]);
}
?>