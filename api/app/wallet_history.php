<?php
include "../../db.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');

// 🔹 Get RAW JSON
$data = json_decode(file_get_contents("php://input"), true);

// 🔹 Input
$user_id = intval($data['user_id'] ?? 0);

// 🔹 Validation
if ($user_id <= 0) {
    echo json_encode([
        "status" => 400,
        "message" => "User ID required"
    ]);
    exit;
}

// 🔹 Fetch data
$stmt = $conn->prepare("SELECT amount, status, created_at 
                        FROM add_money 
                        WHERE user_id=? 
                        ORDER BY id DESC");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$data_arr = [];

while ($row = $result->fetch_assoc()) {

    $status_text = "Pending";
    if ($row['status'] == 1) {
        $status_text = "Approved";
    } elseif ($row['status'] == 2) {
        $status_text = "Rejected";
    }

    $data_arr[] = [
        "amount" => (float) $row['amount'],
        "status" => (int) $row['status'],
        "status_text" => $status_text,
        "type" => ($row['status'] == 1) ? "credit" : "none",
        "created_at" => $row['created_at']
    ];
}

echo json_encode([
    "status" => 200,
    "message" => "Success",
    "data" => $data_arr
]);
?>