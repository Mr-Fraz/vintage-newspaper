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
    <h1>📰 Vintage Daily</h1>

    <div class="article">
        <?php
        foreach ($articles as $row) {
            echo "<h3>{$row['title']}</h3>";
            echo "<p>" . substr($row['content'], 0, 150) . "...</p>";
        }
        ?>
    </div>
</div>

<div class="container">
    <h2>Latest News</h2>

    <div class="article">
        <?php
        foreach ($articles as $row) {
            echo "<div class='post'>";
            echo "<h3>{$row['title']}</h3>";
            echo "<p>" . substr($row['content'], 0, 200) . "...</p>";
            echo "</div>";
        }
        ?>
    </div>
</div>

<?php include('includes/footer.php'); ?>
