<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../../db.php";

header('Content-Type: application/json');

date_default_timezone_set('Asia/Kolkata');

$category_id = $_POST['category_id'] ?? '';

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
WHERE m.category_id = '$category_id'
ORDER BY m.id DESC
";

$result = $conn->query($sql);

$data = [];

$current_time = strtotime(date("H:i:s"));

while ($row = $result->fetch_assoc()) {

    $start_time = strtotime($row['start_time']);
    $end_time = strtotime($row['end_time']);

    // 🔥 status logic
    if ($current_time < $start_time) {

        $row['game_status'] = "UPCOMING";

    } elseif ($current_time >= $start_time && $current_time <= $end_time) {

        $row['game_status'] = "RUNNING";

    } else {

        $row['game_status'] = "CLOSED";
    }

    $row['time'] = $row['start_time'] . " - " . $row['end_time'];

    $data[] = $row;
}

echo json_encode([
    "status" => 1,
    "data" => $data
]);
?>