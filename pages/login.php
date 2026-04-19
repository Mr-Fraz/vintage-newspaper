<?php
require('../includes/init.php');
require('../functions/auth.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = loginUser($conn, $_POST['email'], $_POST['password']);

    if ($user) {
        $_SESSION['user'] = $user;

        if ($user['role'] == 'admin') {
            header("Location: ../admin/index.php");
        } else {
            header("Location: ../index.php");
        }
    } else {
        echo "Invalid credentials";
    }
}
?>

<h2>Login</h2>

<form method="POST">
    <input type="email" name="email" placeholder="Email"><br>
    <input type="password" name="password" placeholder="Password"><br>
    <button type="submit">Login</button>
</form> 
