<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../../functions/db.php';

$pageTitle = 'Categories';

$categories = DB::getCategories();

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="admin-wrapper">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    
    <main class="admin-main">
        <header class="admin-page-header">
            <h1>Categories</h1>
            <a href="add.php" class="btn btn-primary">Add New Category</a>
        </header>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Description</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?php echo $cat['id']; ?></td>
                        <td><?php echo htmlspecialchars($cat['name']); ?></td>
                        <td><?php echo htmlspecialchars($cat['slug']); ?></td>
                        <td><?php echo htmlspecialchars($cat['description']); ?></td>
                        <td><?php echo date('M j, Y', strtotime($cat['created_at'])); ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $cat['id']; ?>" class="btn-sm">Edit</a>
                            <a href="delete.php?id=<?php echo $cat['id']; ?>" class="btn-sm btn-danger" onclick="return confirm('Delete category?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>

</body>
</html>
