<?php
require_once __DIR__ . '/../includes/auth-check.php';       // → admin/includes/auth-check.php ✓
require_once __DIR__ . '/../../functions/db.php';            // → functions/db.php ✓
require_once __DIR__ . '/../../functions/helpers.php';       // → functions/helpers.php ✓

$pageTitle = 'Comments';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cid = (int)$_POST['comment_id'];
    if (isset($_POST['approve']))  DB::updateCommentStatus($cid, 'approved');
    if (isset($_POST['spam']))     DB::updateCommentStatus($cid, 'spam');
    if (isset($_POST['delete']))   DB::deleteComment($cid);
    header('Location: list.php');
    exit;
}

$comments = DB::getAllComments();
include __DIR__ . '/../includes/admin-header.php';
?>

<div class="admin-wrapper">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="admin-main">
        <header class="admin-page-header"><h1>Comments</h1></header>
        <div class="table-scroll">
        <table class="admin-table">
            <thead>
                <tr><th>Author</th><th>Comment</th><th>Article</th><th>Status</th><th>Date</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($comments as $c): ?>
                <tr>
                    <td><?php echo htmlspecialchars($c['username'] ?? $c['guest_name'] ?? 'Guest'); ?></td>
                    <td><?php echo htmlspecialchars(substr($c['body'], 0, 80)); ?>...</td>
                    <td><?php echo htmlspecialchars($c['article_title']); ?></td>
                    <td><span class="badge badge-<?php echo $c['status']; ?>"><?php echo $c['status']; ?></span></td>
                    <td><?php echo date('M j, Y', strtotime($c['created_at'])); ?></td>
                    <td>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="comment_id" value="<?php echo $c['id']; ?>">
                            <?php if ($c['status'] !== 'approved'): ?>
                                <button name="approve" class="btn btn-sm btn-success">Approve</button>
                            <?php endif; ?>
                            <button name="spam" class="btn btn-sm">Spam</button>
                            <button name="delete" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </main>
</div>
</body></html>