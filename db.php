<?php
// $host = "localhost";
// $user = "root";
// $pass = "matka@@833400";
// $db = "saptahi1_quizmatka";

$host = "localhost";
$user = "root";
$pass = "";
$db = "matka_admin";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>