<?php
include "../../db.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    $data = $_POST;
}

$category_id = $data['category_id'] ?? '';

if (empty($category_id)) {
    echo json_encode([
        "status" => 0,
        "message" => "Category ID required"
    ]);
    exit;
}

$stmt = $conn->prepare("
    SELECT id, name, start_time, end_time 
    FROM game_markets 
    WHERE category_id=? AND status=1
    ORDER BY start_time ASC
");
$stmt->bind_param("i", $category_id);
$stmt->execute();

$result = $stmt->get_result();

$response = [];
$current_time = date("H:i:s");

while ($row = $result->fetch_assoc()) {

    // 🔥 GAME STATUS LOGIC
    if ($current_time < $row['start_time']) {
        $row['game_status'] = "UPCOMING";
    } elseif ($current_time >= $row['start_time'] && $current_time <= $row['end_time']) {
        $row['game_status'] = "OPEN";
    } else {
        $row['game_status'] = "CLOSED";
    }

    // 🔥 formatted time
    $row['time'] = $row['start_time'] . " - " . $row['end_time'];

    $response[] = $row;
}

echo json_encode([
    "status" => 1,
    "data" => $response
]);
?>