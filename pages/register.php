<?php
require('../includes/init.php');
require('../functions/auth.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    registerUser($conn, $_POST['name'], $_POST['email'], $_POST['password']);
    header("Location: login.php");
}
?>

<h2>Register</h2>

<form method="POST">
    <input type="text" name="name" placeholder="Name" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Register</button>
</form> 
