<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../../db.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
if (!$data)
    $data = $_POST;

$category_id = $data['category_id'] ?? '';
$name = trim($data['name'] ?? '');
$start_time = $data['start_time'] ?? '';
$end_time = $data['end_time'] ?? '';

if (!$category_id || !$name || !$start_time || !$end_time) {
    echo json_encode(["status" => 0, "message" => "All fields required"]);
    exit;
}

// 🔥 Optional: prevent duplicate bazar in same category
$stmt = $conn->prepare("SELECT id FROM game_markets WHERE category_id=? AND name=?");
$stmt->bind_param("is", $category_id, $name);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["status" => 0, "message" => "Market already exists"]);
    exit;
}

// insert
$stmt = $conn->prepare("INSERT INTO game_markets (category_id,name,start_time,end_time) VALUES (?,?,?,?)");
$stmt->bind_param("isss", $category_id, $name, $start_time, $end_time);

if ($stmt->execute()) {
    echo json_encode(["status" => 1, "message" => "Game added"]);
} else {
    echo json_encode(["status" => 0, "message" => "Database error"]);
}
?>