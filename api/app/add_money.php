<?php
include "../../db.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');

// 🔹 Input
$user_id = intval($_POST['user_id'] ?? 0);
$user_txn = trim($_POST['transaction_id'] ?? '');
$amount = floatval($_POST['amount'] ?? 0);

// 🔹 Validation
if ($user_id <= 0 || empty($user_txn) || $amount <= 0) {
    echo json_encode([
        "status" => 400,
        "message" => "All fields are required"
    ]);
    exit;
}

// 🔹 Image Upload
$image_name = "";

if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {

    // 🔥 absolute path (IMPORTANT)
    $upload_dir = __DIR__ . "/uploads/";

    // 🔥 create folder if not exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // 🔥 generate unique name
    $ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
    $image_name = time() . "_" . rand(1000, 9999) . "." . $ext;

    $target_file = $upload_dir . $image_name;

    // 🔥 move file
    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        echo json_encode([
            "status" => 500,
            "message" => "Image upload failed",
            "debug" => $_FILES['image']
        ]);
        exit;
    }
}

// 🔹 Generate system txn id
$system_txn = "TXN" . time() . rand(1000, 9999);

// 🔹 Insert
$stmt = $conn->prepare("INSERT INTO add_money (user_id,user_transaction_id,system_transaction_id,amount,image) VALUES (?,?,?,?,?)");
$stmt->bind_param("issds", $user_id, $user_txn, $system_txn, $amount, $image_name);

if ($stmt->execute()) {
    echo json_encode([
        "status" => 200,
        "message" => "Request submitted successfully",
        "data" => [
            "system_transaction_id" => $system_txn,
            "image" => $image_name
        ]
    ]);
} else {
    echo json_encode([
        "status" => 500,
        "message" => "Database error",
        "error" => $stmt->error
    ]);
}
?>