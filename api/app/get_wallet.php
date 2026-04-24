<?php
include "../../db.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');

// 🔹 Input
$data = json_decode(file_get_contents("php://input"), true);

$user_id = intval($data['user_id'] ?? 0);

// 🔹 Validation
if ($user_id <= 0) {
    echo json_encode([
        "status" => 400,
        "message" => "User ID required"
    ]);
    exit;
}

// 🔹 Check wallet
$stmt = $conn->prepare("SELECT balance FROM wallet WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {

    // 🔥 create wallet if not exists
    $insert = $conn->prepare("INSERT INTO wallet (user_id, balance) VALUES (?, 0)");
    $insert->bind_param("i", $user_id);
    $insert->execute();

    echo json_encode([
        "status" => 200,
        "message" => "Wallet created",
        "data" => [
            "balance" => 0
        ]
    ]);
    exit;
}

// 🔹 Fetch balance
$stmt->bind_result($balance);
$stmt->fetch();

echo json_encode([
    "status" => 200,
    "message" => "Success",
    "data" => [
        "balance" => (float) $balance
    ]
]);
?>