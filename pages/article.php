 
<?php
require('../includes/init.php');

$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM articles WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$article = $result->fetch_assoc();

include('../includes/header.php');
?>

<h2><?php echo $article['title']; ?></h2>
<p><?php echo $article['content']; ?></p>

<?php include('../includes/footer.php'); ?>