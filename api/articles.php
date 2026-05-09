<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/db.php';

// Minimal JWT verification helper (mirrors Auth::generateJwt)
function api_verify_jwt($jwt) {
    if (empty($jwt) || !defined('JWT_SECRET')) return false;
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) return false;
    list($h64, $p64, $s64) = $parts;
    $rawSig = hash_hmac('sha256', $h64 . '.' . $p64, JWT_SECRET, true);
    $calcSig = rtrim(strtr(base64_encode($rawSig), '+/', '-_'), '=');
    if (!hash_equals($calcSig, $s64)) return false;
    $payload = json_decode(base64_decode(strtr($p64, '-_', '+/')));
    if (!$payload) return false;
    // check expiry
    if (isset($payload->exp) && time() > $payload->exp) return false;
    return $payload;
}

// If this endpoint is used for state-changing operations later, check for Authorization header
// For GET listing we allow public access; protected writes should be implemented separately.

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

$articles = DB::getArticles($page, $limit);
$total = DB::countArticles();

echo json_encode([
    'success' => true,
    'page' => $page,
    'limit' => $limit,
    'total' => $total,
    'articles' => $articles
]);
?>
