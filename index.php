<?php
require('includes/init.php');
include('includes/header.php');
include('includes/navbar.php');

$query = "SELECT * FROM articles ORDER BY created_at DESC LIMIT 5";
$result = $conn->query($query);

if (!$result) {
    die("Query Error: " . $conn->error);
}

// Store results in an array to iterate multiple times
$articles = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="container">
    <h2>Latest News</h2>

    <div class="articles-list">
        <?php
        if (empty($articles)) {
            echo "<p>No articles available at this time.</p>";
        } else {
            foreach ($articles as $row) {
                echo "<div class='post'>";
                echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
                echo "<p>" . htmlspecialchars(substr($row['content'], 0, 200)) . "...</p>";
                echo "</div>";
            }
        }
        ?>
    </div>
</div>

<?php include('includes/footer.php'); ?>
