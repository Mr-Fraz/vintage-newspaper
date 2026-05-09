<?php
require_once __DIR__ . '/../includes/init.php';

// Publish scheduled articles whose publish_at <= now
$db = $db ?? null;
if (!$db) {
    // try to get from global in database.php
    $db = $GLOBALS['db'] ?? null;
}

if (!$db) {
    echo "Database connection not available\n";
    exit(1);
}

// Move scheduled or pending articles to published when publish_at has arrived
$stmt = $db->prepare("SELECT id, title FROM articles WHERE status IN ('scheduled','pending') AND publish_at IS NOT NULL AND publish_at <= NOW()");
$stmt->execute();
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
