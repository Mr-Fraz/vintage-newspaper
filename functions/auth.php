<?php

function registerUser($conn, $name, $email, $password) {
    $password = md5($password);

    $stmt = $conn->prepare("INSERT INTO users (name,email,password) VALUES (?,?,?)");
    $stmt->bind_param("sss", $name, $email, $password);

    return $stmt->execute();
}

function loginUser($conn, $email, $password) {
    $password = md5($password);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? AND password=?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();

    $result = $stmt->get_result();
    return $result->fetch_assoc();
} 
