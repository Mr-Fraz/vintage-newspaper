<?php
require('../../includes/init.php');
require('../../includes/auth-middleware.php');

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM articles WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: list.php");