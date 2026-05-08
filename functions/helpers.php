<?php
class Helper {
    // Escape output
    public static function escape($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    // CSRF token field
    public static function csrfField() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;

    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }
    // Verify CSRF token
    public static function verifyCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    return isset($_SESSION['csrf_token']) &&
           hash_equals($_SESSION['csrf_token'], $token);
    }

    // Format date
    public static function formatDate($date, $format = 'F j, Y') {
        return date($format, strtotime($date));
    }
    
    // Truncate text
    public static function truncate($text, $length = 150) {
        if (strlen($text) > $length) {
            return substr($text, 0, $length) . '...';
        }
        return $text;
    }
    
    // Get excerpt
    public static function excerpt($content, $length = 200) {
        $content = strip_tags($content);
        return self::truncate($content, $length);
    }
    
    // Time ago
    public static function timeAgo($datetime) {
        $time = strtotime($datetime);
        $diff = time() - $time;
        
        if ($diff < 60) return 'just now';
        if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
        if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
        if ($diff < 604800) return floor($diff / 86400) . ' days ago';
        
        return date('M j, Y', $time);
    }
    
    // Upload image
    public static function uploadImage($file) {
        $validate = Validate::image($file);
        if (!$validate['success']) {
            return $validate;
        }
        
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = uniqid() . '.' . $ext;
        $destination = UPLOAD_DIR . $filename;
        
        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => true, 'filename' => $filename];
        }
        
        return ['success' => false, 'message' => 'Upload failed'];
    }
    
    // Pagination
    public static function pagination($total, $perPage, $currentPage, $url) {
        $totalPages = ceil($total / $perPage);
        
        if ($totalPages <= 1) return '';
        
        $html = '<div class="pagination">';
        
        // Previous
        if ($currentPage > 1) {
            $html .= '<a href="' . $url . '?page=' . ($currentPage - 1) . '">&laquo; Prev</a>';
        }
        
        // Pages
        for ($i = 1; $i <= $totalPages; $i++) {
            $active = ($i == $currentPage) ? 'active' : '';
            $html .= '<a href="' . $url . '?page=' . $i . '" class="' . $active . '">' . $i . '</a>';
        }
        
        // Next
        if ($currentPage < $totalPages) {
            $html .= '<a href="' . $url . '?page=' . ($currentPage + 1) . '">Next &raquo;</a>';
        }
        
        $html .= '</div>';
        return $html;
    }

    // Simple file-based rate limiter: returns true if the rate limit is exceeded
    public static function rateLimitExceeded($key, $limit = 60, $window = 60) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $hash = md5($key . '|' . $ip);
        $file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'vintage_rate_' . $hash . '.json';
        $now = time();

        $data = ['count' => 0, 'start' => $now];
        if (file_exists($file)) {
            $json = @file_get_contents($file);
            $d = @json_decode($json, true);
            if (is_array($d) && isset($d['count']) && isset($d['start'])) {
                $data = $d;
            }
        }

        if ($now - $data['start'] > $window) {
            $data = ['count' => 1, 'start' => $now];
        } else {
            $data['count'] = ($data['count'] ?? 0) + 1;
        }

        // best-effort write (no locking complexity)
        @file_put_contents($file, json_encode($data));

        return ($data['count'] > $limit);
    }
}
?>

<?php
// Global procedural wrappers for backward-compatible template calls
if (!function_exists('escape')) {
    function escape($text) {
        return Helper::escape($text);
    }
}

if (!function_exists('csrfField')) {
    function csrfField() {
        return Helper::csrfField();
    }
}

if (!function_exists('verifyCSRFToken')) {
    function verifyCSRFToken($token) {
        return Helper::verifyCSRFToken($token);
    }
}

