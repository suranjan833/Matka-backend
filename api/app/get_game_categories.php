<?php
include "../../db.php";

header('Content-Type: application/json');

$result = $conn->query("SELECT id, name FROM game_categories WHERE status=1");

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    "status" => 1,
    "data" => $data
]);
?>