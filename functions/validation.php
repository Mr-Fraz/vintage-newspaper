<?php
class Validate {
    // Sanitize input
    public static function sanitize($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
    
    // Validate email
    public static function email($email) {
        return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
    }
    
    // Validate URL
    public static function url($url) {
        return filter_var($url, FILTER_VALIDATE_URL);
    }
    
    // Generate slug
    public static function slug($text) {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9-]/', '-', $text);
        $text = preg_replace('/-+/', '-', $text);
        $text = trim($text, '-');
        return $text;
    }
    
    // Validate image upload
    public static function image($file) {
        $allowed = ALLOWED_EXTENSIONS;
        $maxSize = MAX_FILE_SIZE;
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Upload error'];
        }
        
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'File too large (max 5MB)'];
        }
        
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            return ['success' => false, 'message' => 'Invalid file type'];
        }
        
        return ['success' => true];
    }
    
    // Generate CSRF token
    public static function generateToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    // Verify CSRF token
    public static function verifyToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
?>
