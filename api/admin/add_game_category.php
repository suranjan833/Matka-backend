<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../../db.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
if (!$data)
    $data = $_POST;

$name = trim($data['name'] ?? '');

if (!$name) {
    echo json_encode(["status" => 0, "message" => "Category required"]);
    exit;
}

// check duplicate
$stmt = $conn->prepare("SELECT id FROM game_categories WHERE name=?");
$stmt->bind_param("s", $name);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["status" => 0, "message" => "Category already exists"]);
    exit;
}

// insert
$stmt = $conn->prepare("INSERT INTO game_categories (name) VALUES (?)");
$stmt->bind_param("s", $name);

if ($stmt->execute()) {
    echo json_encode(["status" => 1, "message" => "Category added"]);
} else {
    echo json_encode(["status" => 0, "message" => "Error"]);
}
?>