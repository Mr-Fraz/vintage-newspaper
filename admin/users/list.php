<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../../functions/helpers.php';

$pageTitle = 'Users';

global $db;
$userStatement = $db->prepare("SELECT * FROM users ORDER BY created_at DESC");
$userStatement->execute();
$users = $userStatement->fetchAll();

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="admin-wrapper">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    
    <main class="admin-main">
        <header class="admin-page-header">
            <h1>Users</h1>
        </header>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><span class="badge badge-<?php echo $user['role']; ?>"><?php echo $user['role']; ?></span></td>
                        <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <a href="manage.php?id=<?php echo $user['id']; ?>" class="btn-sm">Manage</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>
