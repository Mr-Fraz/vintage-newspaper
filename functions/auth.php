<?php //auth.php

function registerUser($conn, $name, $email, $password) {
    // sanitize inputs
    $name = trim($name);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $stmt->close();
        throw new Exception("Email already registered.");
    }
    $stmt->close();

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (name,email,password) VALUES (?,?,?)");
    $stmt->bind_param("sss", $name, $email, $password);
    if (!$stmt->execute()) {
        $stmt->close();
        throw new Exception("Registration Failed.");
    }
    $stmt->close();
    return true;
}

function loginUser($conn, $email, $password) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $stmt->close();

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }

    return false;
}
