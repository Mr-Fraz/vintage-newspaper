<?php
date_default_timezone_set('Asia/Karachi');
$now = date('Y-m-d H:i:s'); // Karachi time

$secret = $_GET['secret'] ?? '';
if ($secret !== 'Andkfe9sdf8sdf8sdf8sdf8sdf8') {
    http_response_code(403);
    die('Forbidden');
}
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../functions/db.php';

$db = DB::getConnection();

// Find articles that are scheduled to be published
$stmt = $db->prepare("SELECT id, title FROM articles 
    WHERE status IN ('scheduled','pending') 
    AND publish_at IS NOT NULL 
    AND publish_at <= :now");
$stmt->execute([':now' => $now]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!empty($rows)) {
    $ids = array_column($rows, 'id');
    $in = implode(',', array_map('intval', $ids));
    $upd = $db->prepare("UPDATE articles SET status = 'published', updated_at = NOW() WHERE id IN ($in)");
    $upd->execute();

    foreach ($rows as $r) {
        if (class_exists('DB') && method_exists('DB', 'logActivity')) {
            DB::logActivity(null, 'auto_publish', 'article', $r['id'], ['title' => $r['title']]);
        }
        echo "Published article: {$r['id']} - {$r['title']}\n";
    }
} else {
    echo "No scheduled articles to publish.\n";
}

return 0;