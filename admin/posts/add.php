<?php
require('../../includes/init.php');
require('../../includes/auth-middleware.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $conn->prepare("INSERT INTO articles (title, content) VALUES (?,?)");
    $stmt->bind_param("ss", $title, $content);
    $stmt->execute();

    header("Location: list.php");
}
?>

<h2>Add Article</h2>
<form method="POST">
    <input type="text" name="title" placeholder="Title"><br>
    <textarea name="content" placeholder="Content"></textarea><br>
    <button>Add</button>
</form>