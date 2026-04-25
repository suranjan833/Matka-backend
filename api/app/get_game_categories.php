<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../../db.php";

header('Content-Type: application/json');

// 🔥 only active categories
$result = $conn->query("
    SELECT id, name 
    FROM game_categories 
    WHERE status = 1 
    ORDER BY id DESC
");

$data = [];

if ($result && $result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode([
        "status" => 1,
        "data" => $data
    ]);

} else {

    echo json_encode([
        "status" => 0,
        "message" => "No categories found",
        "data" => []
    ]);
}
?>