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
    echo json_encode(["status" => 0, "message" => "Category ID required"]);
    exit;
}

// 🔥 check if markets exist
$stmt = $conn->prepare("SELECT id FROM game_markets WHERE category_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode([
        "status" => 0,
        "message" => "Cannot delete. Markets exist under this category"
    ]);
    exit;
}

// delete category
$stmt = $conn->prepare("DELETE FROM game_categories WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["status" => 1, "message" => "Category deleted"]);
} else {
    echo json_encode(["status" => 0, "message" => "Delete failed"]);
}
?>