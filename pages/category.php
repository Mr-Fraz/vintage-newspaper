<?php
require('../includes/init.php');
require('../functions/helpers.php');
include('../includes/header.php');
include('../includes/navbar.php');

if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    die('Category not specified');
}

$slug = sanitizeInput($_GET['slug']);

// Get category
$stmt = $conn->prepare("SELECT id, name, description FROM categories WHERE slug = ?");
if (!$stmt) die('Database error');
$stmt->bind_param("s", $slug);
$stmt->execute();
$category = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$category) die('Category not found');

// Get articles in this category
$query = "SELECT a.id, a.title, a.content, a.created_at, u.name as author_name 
          FROM articles a 
          LEFT JOIN users u ON a.author_id = u.id 
          WHERE a.category_id = ? AND a.status = 'published'
          ORDER BY a.created_at DESC";

$stmt = $conn->prepare($query);
if (!$stmt) die('Database error');
$stmt->bind_param("i", $category['id']);
$stmt->execute();
$result = $stmt->get_result();
$articles = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="container">
    <h1><?php echo escape($category['name']); ?></h1>
    <p><?php echo escape($category['description']); ?></p>
    
    <div class="articles-list">
        <?php
        if (empty($articles)) {
            echo "<p>No articles in this category yet.</p>";
        } else {
            foreach ($articles as $article) {
                echo "<div class='post'>";
                echo "<h3><a href='article.php?id=" . (int)$article['id'] . "'>" . escape($article['title']) . "</a></h3>";
                echo "<p>" . escape(substr($article['content'], 0, 200)) . "...</p>";
                echo "<small>";
                echo escape(date('M d, Y', strtotime($article['created_at'])));
                if ($article['author_name']) {
                    echo " by " . escape($article['author_name']);
                }
                echo "</small>";
                echo "</div>";
            }
        }
        ?>
    </div>
</div>

<?php include('../includes/footer.php'); ?> 
