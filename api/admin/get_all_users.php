<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../../db.php";

header('Content-Type: application/json');

// 🔥 users fetch (latest first চাইলে DESC, না হলে ASC)
$result = $conn->query("
    SELECT id, name, email, phone 
    FROM users 
    ORDER BY id ASC
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
        "message" => "No users found",
        "data" => []
    ]);
}
?>