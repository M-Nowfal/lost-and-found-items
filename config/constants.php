<?php
/**
 * Application Constants
 * Lost & Found Portal
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Base URL calculation - more robust method using ROOT_PATH
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');
$rootPath = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/');
$webBase = str_replace($docRoot, '', $rootPath);
$webBase = rtrim(str_replace('\\', '/', $webBase), '/');
define('BASE_URL', $protocol . $host . $webBase . '/');

// Application paths
define('ROOT_PATH', dirname(__DIR__) . '/');
define('UPLOAD_PATH', ROOT_PATH . 'uploads/');
define('ASSETS_PATH', BASE_URL . 'assets/');

// Session configuration
define('SESSION_LIFETIME', 86400); // 24 hours
define('SESSION_NAME', 'lostfound_session');

// Security
define('CSRF_TOKEN_NAME', 'csrf_token');
define('HASH_COST', 12);

// Item categories
define('CATEGORIES', [
    'Electronics' => 'Electronics (Phone, Laptop, Tablet, etc.)',
    'Documents' => 'Documents (ID, Passport, License, etc.)',
    'Valuables' => 'Valuables (Wallet, Jewelry, Watch, etc.)',
    'Clothing' => 'Clothing (Jackets, Bags, Shoes, etc.)',
    'Keys' => 'Keys (Car, House, Office, etc.)',
    'Pets' => 'Pets (Collars, Tags, etc.)',
    'Others' => 'Others'
]);

// Item types
define('ITEM_TYPES', [
    'lost' => 'Lost Item',
    'found' => 'Found Item'
]);

// Item statuses
define('ITEM_STATUSES', [
    'pending' => 'Pending',
    'matched' => 'Matched',
    'claimed' => 'Claimed'
]);

// Match statuses
define('MATCH_STATUSES', [
    'pending' => 'Pending Review',
    'approved' => 'Approved',
    'rejected' => 'Rejected'
]);

// Upload configuration
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('UPLOAD_ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Matching configuration
define('MATCH_SIMILARITY_THRESHOLD', 50); // Minimum similarity percentage

// Pagination
define('ITEMS_PER_PAGE', 12);
define('USERS_PER_PAGE', 20);
define('NOTIFICATIONS_PER_PAGE', 20);

// Error reporting (disable in production)
ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('error_log', ROOT_PATH . 'logs/error.log');

// Timezone
date_default_timezone_set('UTC');

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isUser() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'user';
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUser() {
    return $_SESSION['user'] ?? null;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . 'views/auth/login.php');
        exit;
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ' . BASE_URL);
        exit;
    }
}

function generateCSRFToken() {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function validateCSRFToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

function sanitize($string) {
    return htmlspecialchars(strip_tags(trim($string)), ENT_QUOTES, 'UTF-8');
}

function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    
    return formatDate($datetime);
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}
