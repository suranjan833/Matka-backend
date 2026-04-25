<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../../db.php";

header('Content-Type: application/json');

// 🔥 get all markets + category name
$sql = "
SELECT 
    m.id,
    m.name,
    m.start_time,
    m.end_time,
    m.status,
    c.name AS category_name
FROM game_markets m
LEFT JOIN game_categories c ON c.id = m.category_id
ORDER BY m.id DESC
";

$result = $conn->query($sql);

$data = [];
$current_time = date("H:i:s");

while ($row = $result->fetch_assoc()) {

    // 🟢 open/close logic
    if ($current_time >= $row['start_time'] && $current_time <= $row['end_time']) {
        $row['is_open'] = 1;
    } else {
        $row['is_open'] = 0;
    }

    // 🟡 optional readable text
    $row['time'] = $row['start_time'] . " - " . $row['end_time'];

    $data[] = $row;
}

echo json_encode([
    "status" => 1,
    "data" => $data
]);
?>