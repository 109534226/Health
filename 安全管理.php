<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'your_database_name';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}
?>

