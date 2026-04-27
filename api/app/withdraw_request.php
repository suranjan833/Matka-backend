<?php
include "../../db.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');

/// 🔥 HANDLE BOTH JSON + FORM DATA
$data = json_decode(file_get_contents("php://input"), true);

if (!empty($_POST)) {
    $user_id = intval($_POST['user_id'] ?? 0);
    $amount = floatval($_POST['amount'] ?? 0);

    $bank_name = trim($_POST['bank_name'] ?? '');
    $account_number = trim($_POST['account_no'] ?? '');
    $ifsc_code = trim($_POST['ifsc'] ?? '');
    $account_holder = trim($_POST['name'] ?? '');

    $upi_id = trim($_POST['upi_id'] ?? '');
} else {
    $user_id = intval($data['user_id'] ?? 0);
    $amount = floatval($data['amount'] ?? 0);

    $bank_name = trim($data['bank_name'] ?? '');
    $account_number = trim($data['account_no'] ?? '');
    $ifsc_code = trim($data['ifsc'] ?? '');
    $account_holder = trim($data['name'] ?? '');

    $upi_id = trim($data['upi_id'] ?? '');
}

/// 🔹 Validation
if ($user_id <= 0 || $amount <= 0) {
    echo json_encode([
        "status" => 400,
        "message" => "User ID and amount required"
    ]);
    exit;
}

/// 🔹 At least one method
if (empty($upi_id) && (empty($bank_name) || empty($account_number) || empty($ifsc_code))) {
    echo json_encode([
        "status" => 400,
        "message" => "Provide bank details or UPI ID"
    ]);
    exit;
}

/// 🔹 Check wallet
$stmt = $conn->prepare("SELECT balance FROM wallet WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows == 0) {
    echo json_encode([
        "status" => 404,
        "message" => "Wallet not found"
    ]);
    exit;
}

$row = $res->fetch_assoc();

if ($amount > $row['balance']) {
    echo json_encode([
        "status" => 400,
        "message" => "Insufficient balance"
    ]);
    exit;
}

/// 🔹 Insert request
$stmt = $conn->prepare("INSERT INTO withdraw_requests 
(user_id, amount, bank_name, account_number, ifsc_code, account_holder_name, upi_id) 
VALUES (?,?,?,?,?,?,?)");

$stmt->bind_param("idsssss", $user_id, $amount, $bank_name, $account_number, $ifsc_code, $account_holder, $upi_id);

if ($stmt->execute()) {
    echo json_encode([
        "status" => 200,
        "message" => "Withdraw request submitted"
    ]);
} else {
    echo json_encode([
        "status" => 500,
        "message" => "Database error",
        "error" => $stmt->error
    ]);
}
?>