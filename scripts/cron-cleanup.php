<?php
require_once __DIR__ . '/../includes/init.php';

$db = $db ?? ($GLOBALS['db'] ?? null);
if (!$db) {
    echo "Database connection not available\n";
    exit(1);
}

// Cleanup expired password reset tokens older than 7 days or used tokens older than 30 days
$del1 = $db->prepare("DELETE FROM password_resets WHERE expires_at < NOW() - INTERVAL 7 DAY");
$del1->execute();

$del2 = $db->prepare("DELETE FROM password_resets WHERE used = 1 AND created_at < NOW() - INTERVAL 30 DAY");
$del2->execute();

// Optionally prune activity logs older than 1 year
$del3 = $db->prepare("DELETE FROM activity_log WHERE created_at < NOW() - INTERVAL 1 YEAR");
$del3->execute();

echo "Cleanup completed.\n";

return 0;
