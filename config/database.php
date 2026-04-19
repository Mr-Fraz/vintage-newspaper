<?php
$conn = new mysqli("localhost", "root", ".mFraz@12", "vintage_news");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>