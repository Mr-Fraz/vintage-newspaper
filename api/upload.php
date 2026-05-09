<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/helpers.php';
require_once __DIR__ . '/../config/config.php';

// Simple JWT verification helper (HS256). Uses JWT_SECRET from config if available.
function verify_jwt_token($jwt) {
    if (empty($jwt)) return false;
    if (!defined('JWT_SECRET')) return false;

    $parts = explode('.', $jwt);
    if (count($parts) !== 3) return false;

    list($h64, $p64, $s64) = $parts;

    $header = json_decode(base64_decode(strtr($h64, '-_', '+/')));
    $payload = json_decode(base64_decode(strtr($p64, '-_', '+/')));
    $sig = $s64;

    $rawSig = hash_hmac('sha256', $h64 . '.' . $p64, JWT_SECRET, true);
    $calcSig = rtrim(strtr(base64_encode($rawSig), '+/', '-_'), '=');

    if (!hash_equals($calcSig, $sig)) return false;

    return $payload;
}

// Basic rate limiting: 10 uploads per hour per IP
if (Helper::rateLimitExceeded('api_upload', 10, 3600)) {
    http_response_code(429);
    echo json_encode(['error' => 'Too many upload requests, try again later']);
    exit;
}

header('Content-Type: application/json');

// Verify CSRF token
if (empty($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}

// Verify authentication: accept Bearer JWT or fallback to session-based auth
$userId = null;
$authHeader = '';
if (!empty($_SERVER['HTTP_AUTHORIZATION'])) $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
elseif (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];

if ($authHeader && stripos($authHeader, 'Bearer ') === 0) {
    $token = trim(str_ireplace('Bearer', '', $authHeader));
    $payload = verify_jwt_token($token);
    if ($payload && isset($payload->sub)) {
        $userId = $payload->sub;
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid token']);
        exit;
    }
} else {
    session_start();
    if (empty($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
    $userId = $_SESSION['user']['id'] ?? null;
}

// Validate file upload
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'File upload failed']);
    exit;
}

// Prepare upload destination (structured by year/month)
$file = $_FILES['file'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowed = ALLOWED_EXTENSIONS;
$maxSize = MAX_FILE_SIZE;

$year = date('Y');
$month = date('m');
$targetDir = rtrim(UPLOAD_DIR, '/') . "/{$year}/{$month}/";

// Ensure web-accessible uploads root has .htaccess to prevent script execution
$uploadsRoot = dirname(UPLOAD_DIR) . '/';
$htaccessPath = $uploadsRoot . '.htaccess';
if (!file_exists($htaccessPath)) {
    @file_put_contents($htaccessPath, "<FilesMatch \"\\.(php|phtml|php3|php4|php5|phar)\">\n    Deny from all\n</FilesMatch>\n");
}

// Validate file
if (!in_array($ext, $allowed)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file type. Only jpg, jpeg, png, gif allowed']);
    exit;
}

if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(['error' => 'File too large. Max size 5MB']);
    exit;
}

// Verify file is actually an image
// Verify file content MIME
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

// Normalize jpeg mime
if ($mime === 'image/pjpeg') $mime = 'image/jpeg';
$allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($mime, $allowedMimes)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file content']);
    exit;
}

// Create filename and ensure directory exists
$fileName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}
$targetFile = $targetDir . $fileName;

if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save file']);
    exit;
}

// Re-encode image to strip EXIF and metadata (GD fallback)
try {
    if ($mime === 'image/jpeg' && function_exists('imagecreatefromjpeg')) {
        $img = imagecreatefromjpeg($targetFile);
        if ($img !== false) {
            imagejpeg($img, $targetFile, 90);
            imagedestroy($img);
        }
    } elseif ($mime === 'image/png' && function_exists('imagecreatefrompng')) {
        $img = imagecreatefrompng($targetFile);
        if ($img !== false) {
            imagepng($img, $targetFile);
            imagedestroy($img);
        }
    } elseif ($mime === 'image/gif' && function_exists('imagecreatefromgif')) {
        $img = imagecreatefromgif($targetFile);
        if ($img !== false) {
            imagegif($img, $targetFile);
            imagedestroy($img);
        }
    }
} catch (Exception $e) {
    // If re-encoding fails, keep original but log activity
    @error_log('Image re-encode failed: ' . $e->getMessage());
}

// Build public path
$publicPath = '/uploads/articles/' . $year . '/' . $month . '/' . $fileName;

// Optionally log upload activity
if (method_exists('DB', 'logActivity')) {
    DB::logActivity($userId, 'upload_image', 'upload', null, ['path' => $publicPath], $_SERVER['REMOTE_ADDR'] ?? null);
}

echo json_encode([
    'success' => true,
    'file' => $publicPath,
    'message' => 'File uploaded successfully'
]);
