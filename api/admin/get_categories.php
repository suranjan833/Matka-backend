<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../../db.php";

header('Content-Type: application/json');

// 🔥 ALL categories (admin)
$result = $conn->query("
    SELECT id, name, status 
    FROM game_categories 
    ORDER BY id DESC
");

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    "status" => 1,
    "data" => $data
]);
?>