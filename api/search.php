<?php
require('../config/database.php');

$q = $_GET['q'];

$stmt = $conn->prepare("SELECT * FROM articles WHERE title LIKE ?");
$search = "%$q%";
$stmt->bind_param("s", $search);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "<p><a href='/pages/article.php?id={$row['id']}'>{$row['title']}</a></p>";
}