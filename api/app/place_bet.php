<?php
include "../../db.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    $data = $_POST;
}

$user_id = $data['user_id'] ?? '';
$market_id = $data['market_id'] ?? '';
$number = $data['number'] ?? '';
$amount = $data['amount'] ?? '';

if (!$user_id || !$market_id || $number === '' || !$amount) {
    echo json_encode(["status" => 0, "message" => "All fields required"]);
    exit;
}


$stmt = $conn->prepare("SELECT start_time, end_time FROM game_markets WHERE id=?");
$stmt->bind_param("i", $market_id);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

$current_time = date("H:i:s");

if ($current_time < $res['start_time'] || $current_time > $res['end_time']) {
    echo json_encode(["status" => 0, "message" => "Game closed"]);
    exit;
}

// ✅ Insert bet
$stmt = $conn->prepare("INSERT INTO game_bets (user_id, market_id, number, amount) VALUES (?,?,?,?)");
$stmt->bind_param("iiid", $user_id, $market_id, $number, $amount);

if ($stmt->execute()) {
    echo json_encode(["status" => 1, "message" => "Bet placed"]);
} else {
    echo json_encode(["status" => 0, "message" => "Error"]);
}
?>