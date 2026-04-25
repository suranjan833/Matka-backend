<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../../db.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
if (!$data)
    $data = $_POST;

$id = $data['id'] ?? '';

if (!$id) {
    echo json_encode(["status" => 0, "message" => "Market ID required"]);
    exit;
}

// check exist
$stmt = $conn->prepare("SELECT id FROM game_markets WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    echo json_encode(["status" => 0, "message" => "Market not found"]);
    exit;
}

// delete
$stmt = $conn->prepare("DELETE FROM game_markets WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["status" => 1, "message" => "Market deleted"]);
} else {
    echo json_encode(["status" => 0, "message" => "Delete failed"]);
}
?>