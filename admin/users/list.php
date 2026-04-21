<?php
require('../../includes/init.php');
require('../../includes/auth-middleware.php');
require('../../functions/helpers.php');

$result = $conn->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
if (!$result) die('Database error: ' . $conn->error);

$deleted = isset($_GET['deleted']) ? true : false;
$updated = isset($_GET['updated']) ? true : false;
?>

<h2>User Management</h2>

<?php if ($deleted): ?>
    <div class="success-message">User deleted successfully!</div>
<?php endif; ?>

<?php if ($updated): ?>
    <div class="success-message">User updated successfully!</div>
<?php endif; ?>

<table border="1" cellpadding="10">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo escape($row['name']); ?></td>
                <td><?php echo escape($row['email']); ?></td>
                <td>
                    <form method="POST" action="edit.php" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
                        <select name="role" onchange="this.form.submit();">
                            <option value="user" <?php echo $row['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                            <option value="admin" <?php echo $row['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </form>
                </td>
                <td><?php echo escape(date('M d, Y', strtotime($row['created_at']))); ?></td>
                <td>
                    <a href="delete.php?id=<?php echo (int)$row['id']; ?>">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
