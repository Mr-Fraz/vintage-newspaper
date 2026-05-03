<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../../functions/helpers.php';
require_once __DIR__ . '/../../functions/db.php';

$error = '';
$success = '';

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    die('Invalid user ID');
}

$id = (int)$_POST['id'];

// Check if user exists
global $db;
$stmt = $db->prepare("SELECT id FROM users WHERE id = ?");
$stmt->execute([$id]);
if ($stmt->rowCount() === 0) {
    die('User not found');
}

// Validate role
$role = isset($_POST['role']) && in_array($_POST['role'], ['user', 'admin']) ? $_POST['role'] : 'user';

// Cannot demote yourself
if ($_SESSION['user_id'] == $id && $role != 'admin' && $_SESSION['role'] == 'admin') {
    die('You cannot demote yourself');
}

$stmt = $db->prepare("UPDATE users SET role = ? WHERE id = ?");
if ($stmt->execute([$role, $id])) {
    header("Location: manage.php?id=$id&updated=1");
    exit;
} else {
    echo 'Error: Database update failed';
}
