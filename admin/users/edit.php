<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../../functions/helpers.php';

$error = '';
$success = '';

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    die('Invalid user ID');
}

$id = (int)$_POST['id'];

// Check if user exists
$stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    $stmt->close();
    die('User not found');
}
$stmt->close();

// Validate role
$role = isset($_POST['role']) && in_array($_POST['role'], ['user', 'admin']) ? $_POST['role'] : 'user';

// Cannot demote yourself
if ($_SESSION['user']['id'] == $id && $role != 'admin' && $_SESSION['user']['role'] == 'admin') {
    die('You cannot demote yourself');
}

$stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
$stmt->bind_param("si", $role, $id);
if ($stmt->execute()) {
    header("Location: list.php?updated=1");
} else {
    echo 'Error: ' . $stmt->error;
}
$stmt->close();
