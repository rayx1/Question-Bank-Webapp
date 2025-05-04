<?php
// config.php
$host = 'localhost';
$user = 'root';       // Change as needed
$password = '';       // Change as needed
$database = 'question_bank';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
