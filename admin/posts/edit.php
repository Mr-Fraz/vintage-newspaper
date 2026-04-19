<?php
require('../../includes/init.php');
require('../../includes/auth-middleware.php');

$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM articles WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$article = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $conn->prepare("UPDATE articles SET title=?, content=? WHERE id=?");
    $stmt->bind_param("ssi", $_POST['title'], $_POST['content'], $id);
    $stmt->execute();

    header("Location: list.php");
}
?>

<form method="POST">
    <input type="text" name="title" value="<?php echo $article['title']; ?>"><br>
    <textarea name="content"><?php echo $article['content']; ?></textarea><br>
    <button>Update</button>
</form>