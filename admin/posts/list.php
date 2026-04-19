<?php
require('../../includes/init.php');
require('../../includes/auth-middleware.php');

$result = $conn->query("SELECT * FROM articles");
?>

<h2>All Articles</h2>

<a href="add.php">Add New</a>

<?php while ($row = $result->fetch_assoc()): ?>
    <p>
        <?php echo $row['title']; ?>
        <a href="edit.php?id=<?php echo $row['id']; ?>">Edit</a>
        <a href="delete.php?id=<?php echo $row['id']; ?>">Delete</a>
    </p>
<?php endwhile; ?>