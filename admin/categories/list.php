<?php
require('../../includes/init.php');
require('../../includes/auth-middleware.php');
require('../../functions/helpers.php');

$result = $conn->query("SELECT id, name, slug, description, created_at FROM categories ORDER BY created_at DESC");
if (!$result) die('Database error: ' . $conn->error);

$deleted = isset($_GET['deleted']) ? true : false;
$updated = isset($_GET['updated']) ? true : false;
?>

<h2>Categories Management</h2>

<?php if ($deleted): ?>
    <div class="success-message">Category deleted successfully!</div>
<?php endif; ?>

<?php if ($updated): ?>
    <div class="success-message">Category updated successfully!</div>
<?php endif; ?>

<a href="add.php" class="btn btn-primary">Add New Category</a>

<?php if ($result->num_rows === 0): ?>
    <p>No categories found.</p>
<?php else: ?>
    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Description</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo escape($row['name']); ?></td>
                    <td><?php echo escape($row['slug']); ?></td>
                    <td><?php echo escape(substr($row['description'], 0, 50)); ?></td>
                    <td><?php echo escape(date('M d, Y', strtotime($row['created_at']))); ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo (int)$row['id']; ?>">Edit</a> | 
                        <a href="delete.php?id=<?php echo (int)$row['id']; ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>
