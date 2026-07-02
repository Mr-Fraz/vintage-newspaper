<?php
class Helper
{
    // Escape output
    public static function escape($text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    // CSRF token field
    public static function csrfField()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;

        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }
    // Verify CSRF token
    public static function verifyCSRFToken($token)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['csrf_token']) &&
            hash_equals($_SESSION['csrf_token'], $token);
    }

    // Format date
    public static function formatDate($date, $format = 'F j, Y')
    {
        return date($format, strtotime($date));
    }

    // Truncate text
    public static function truncate($text, $length = 150)
    {
        if (strlen($text) > $length) {
            return substr($text, 0, $length) . '...';
        }
        return $text;
    }

    // Get excerpt
    public static function excerpt($content, $length = 200)
    {
        $content = strip_tags($content);
        return self::truncate($content, $length);
    }

    // Time ago
    public static function timeAgo($datetime)
    {
        $time = strtotime($datetime);
        $diff = time() - $time;

        if ($diff < 60) return 'just now';
        if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
        if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
        if ($diff < 604800) return floor($diff / 86400) . ' days ago';

        return date('M j, Y', $time);
    }

    // Upload image + generate thumb & medium variants
    public static function uploadImage($file, $altText = '', $userId = null)
    {
        $validate = Validate::image($file);
        if (!$validate['success']) return $validate;

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $base = uniqid();
        $filename       = $base . '.' . $ext;
        $filenameThumb  = 'thumb_'  . $base . '.jpg';
        $filenameMedium = 'medium_' . $base . '.jpg';

        if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);

        $dest = UPLOAD_DIR . $filename;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return ['success' => false, 'message' => 'Upload failed'];
        }

        // Load source image
        [$srcW, $srcH, $type] = getimagesize($dest);
        $src = match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($dest),
            IMAGETYPE_PNG  => imagecreatefrompng($dest),
            IMAGETYPE_GIF  => imagecreatefromgif($dest),
            default        => null
        };

        if (!$src) return ['success' => false, 'message' => 'GD could not read image'];

        // Helper: resize + crop to exact dimensions
        $resize = function ($src, $srcW, $srcH, $destW, $destH, $outPath) {
            $srcRatio  = $srcW / $srcH;
            $destRatio = $destW / $destH;

            if ($srcRatio > $destRatio) {
                // Crop width
                $cropH = $srcH;
                $cropW = (int)($srcH * $destRatio);
                $cropX = (int)(($srcW - $cropW) / 2);
                $cropY = 0;
            } else {
                // Crop height
                $cropW = $srcW;
                $cropH = (int)($srcW / $destRatio);
                $cropX = 0;
                $cropY = (int)(($srcH - $cropH) / 2);
            }

            $canvas = imagecreatetruecolor($destW, $destH);
            // Preserve transparency for PNG
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
            imagefill($canvas, 0, 0, $transparent);

            imagecopyresampled($canvas, $src, 0, 0, $cropX, $cropY, $destW, $destH, $cropW, $cropH);
            imagejpeg($canvas, $outPath, 85); // 85% quality
            imagedestroy($canvas);
        };

        // Generate thumb (300x200) and medium (800x500)
        $resize($src, $srcW, $srcH, 300,  200, UPLOAD_DIR . $filenameThumb);
        $resize($src, $srcW, $srcH, 800, 500, UPLOAD_DIR . $filenameMedium);
        imagedestroy($src);

        // Save to media table
        $mediaId = DB::insertMedia([
            'filename'        => $filename,
            'filename_thumb'  => $filenameThumb,
            'filename_medium' => $filenameMedium,
            'alt_text'        => $altText,
            'uploaded_by'     => $userId
        ]);

        return [
            'success'         => true,
            'media_id'        => $mediaId,
            'filename'        => $filename,
            'filename_thumb'  => $filenameThumb,
            'filename_medium' => $filenameMedium,
        ];
    }

    // Pagination
    public static function pagination($total, $perPage, $currentPage, $url)
    {
        $totalPages = ceil($total / $perPage);

        if ($totalPages <= 1) return '';

        // Support URLs that already carry a query string (e.g. author.php?id=5)
        $sep = (strpos($url, '?') !== false) ? '&' : '?';

        $html = '<div class="pagination">';

        // Previous
        if ($currentPage > 1) {
            $html .= '<a href="' . $url . $sep . 'page=' . ($currentPage - 1) . '">&laquo; Prev</a>';
        }

        // Pages
        for ($i = 1; $i <= $totalPages; $i++) {
            $active = ($i == $currentPage) ? 'active' : '';
            $html .= '<a href="' . $url . $sep . 'page=' . $i . '" class="' . $active . '">' . $i . '</a>';
        }

        // Next
        if ($currentPage < $totalPages) {
            $html .= '<a href="' . $url . $sep . 'page=' . ($currentPage + 1) . '">Next &raquo;</a>';
        }

        $html .= '</div>';
        return $html;
    }

    // Simple file-based rate limiter: returns true if the rate limit is exceeded
    public static function rateLimitExceeded($key, $limit = 60, $window = 60)
    {
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

    // Detect text direction (Arabic/Urdu/Persian block)
    public static function getTextDirection($text)
    {
        $rtlPattern = '/[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}\x{FB50}-\x{FDFF}\x{FE70}-\x{FEFF}]/u';
        return preg_match($rtlPattern, $text) ? 'rtl' : 'ltr';
    }
}
?>

<?php
// Global procedural wrappers for backward-compatible template calls
if (!function_exists('escape')) {
    function escape($text)
    {
        return Helper::escape($text);
    }
}

if (!function_exists('csrfField')) {
    function csrfField()
    {
        return Helper::csrfField();
    }
}

if (!function_exists('verifyCSRFToken')) {
    function verifyCSRFToken($token)
    {
        return Helper::verifyCSRFToken($token);
    }
}

if (!function_exists('getTextDirection')) {
    function getTextDirection($text)
    {
        return Helper::getTextDirection($text);
    }
}