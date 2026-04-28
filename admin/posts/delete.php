<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../../functions/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    DB::deleteArticle($id);
}

header('Location: list.php');
exit;
?>
