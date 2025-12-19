<?php
/**
 * Database and Application Configuration
 * ArtShop Inc. - Dynamic E-commerce System
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'artshop');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

// Site Configuration
define('SITE_NAME', 'ArtShop Inc.');
define('SITE_URL', 'http://localhost/artShop');
define('SITE_EMAIL', 'pferguson@mycolard-art.com');

// Email Configuration
define('FROM_EMAIL', 'pferguson@mycolard-art.com');
define('FROM_NAME', 'ArtShop Inc.');

// SMTP Configuration (for email)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'pferguson8030@gmail.com');
define('SMTP_PASS', 'rjtwlfmtfbsnmhjh');
define('SMTP_SECURE', 'tls');

// Security
define('ADMIN_EMAIL', 'pferguson@mycolard-art.com');
define('SESSION_LIFETIME', 3600); // 1 hour

// Create database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

// Helper Functions
function redirect($url) {
    header("Location: $url");
    exit();
}

function setFlashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        setFlashMessage('Please log in to continue', 'warning');
        redirect('login.php');
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        setFlashMessage('Access denied. Admin privileges required.', 'danger');
        redirect('index.php');
    }
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

?>
