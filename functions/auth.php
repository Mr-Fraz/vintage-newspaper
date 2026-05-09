<?php
require_once __DIR__ . '/../config/config.php';

class Auth {
    // Register new user
    public static function register($username, $email, $password) {
        global $db;
        
        // Validate
        if (empty($username) || empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'All fields required'];
        }
        
        // Check if user exists
        $sql = "SELECT id FROM users WHERE email = :email OR username = :username";
        $stmt = $db->prepare($sql);
        $stmt->execute(['email' => $email, 'username' => $username]);
        
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'User already exists'];
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user
        $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $db->prepare($sql);
        
        try {
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword
            ]);
            return ['success' => true, 'message' => 'Registration successful'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Registration failed'];
        }
    }
    
    // Login user
    public static function login($email, $password) {
        global $db;
        
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $db->prepare($sql);
        $stmt->execute(['email' => $email]);
        
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in'] = true;
            
            return ['success' => true, 'message' => 'Login successful'];
        }
        
        return ['success' => false, 'message' => 'Invalid credentials'];
    }

    // Generate a simple JWT for API authentication (HS256)
    public static function generateJwt($user, $ttl = 3600) {
        if (!defined('JWT_SECRET')) return false;

        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $now = time();
        $payload = [
            'iss' => SITE_URL,
            'sub' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
            'iat' => $now,
            'exp' => $now + $ttl
        ];

        $b64h = rtrim(strtr(base64_encode(json_encode($header)), '+/', '-_'), '=');
        $b64p = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');
        $sig = hash_hmac('sha256', $b64h . '.' . $b64p, JWT_SECRET, true);
        $b64s = rtrim(strtr(base64_encode($sig), '+/', '-_'), '=');

        return $b64h . '.' . $b64p . '.' . $b64s;
    }
    
    // Logout
    public static function logout() {
        session_destroy();
        header('Location: ' . SITE_URL);
        exit;
    }
    
    // Check if user is logged in
    public static function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    // Check if user is admin
    public static function isAdmin() {
        return self::isLoggedIn() && $_SESSION['role'] === 'admin';
    }
    
    // Require login
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: ' . SITE_URL . '/pages/login.php');
            exit;
        }
    }
    
    // Require admin
    public static function requireAdmin() {
        if (!self::isAdmin()) {
            header('Location: ' . SITE_URL);
            exit;
        }
    }
}
?>
