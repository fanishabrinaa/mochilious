<?php
/**
 * Database Configuration
 * File: config.php
 * Simpan file ini di root folder TokoOnline/
 */

// Prevent direct access
if (!defined('DB_CONFIG')) {
    define('DB_CONFIG', true);
}

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'mochilicious');
define('DB_CHARSET', 'utf8mb4');
// Site Configuration
define('SITE_NAME', 'MochiLicious');
define('SITE_URL', 'http://localhost/TokoOnline');
define('WHATSAPP_NUMBER', '6212345678910');

// Cadangan kalau ekstensi mbstring tidak aktif di hosting
if (!function_exists('mb_substr')) {
    function mb_substr($s, $start, $length = null) {
        return $length === null ? substr($s, $start) : substr($s, $start, $length);
    }
}
if (!function_exists('mb_strimwidth')) {
    function mb_strimwidth($s, $start, $width, $trim = '') {
        return strlen($s) > $width ? substr($s, $start, $width - strlen($trim)) . $trim : $s;
    }
}

// Session Configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Database Connection Class
 */
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("DB connection failed: " . $e->getMessage());
            throw $e;
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Prevent cloning
    private function __clone() {}
    
    // Prevent unserializing
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

/**
 * Get database connection
 */
function getDB() {
    return Database::getInstance()->getConnection();
}

/**
 * Helper Functions
 */

// Sanitize input
function clean($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Redirect helper
function redirect($url) {
    header("Location: $url");
    exit();
}

// Flash message
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Format currency
function formatPrice($price) {
    return 'Rp ' . number_format($price, 0, ',', '.');
}

// Get cart count
function getCartCount() {
    if (isLoggedIn()) {
        $db = getDB();
        $stmt = $db->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    } else {
        return isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
    }
}

// Generate order number
function generateOrderNumber() {
    return 'MO' . date('Ymd') . strtoupper(substr(uniqid(), -6));
}

// Password hash
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Password verify
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Error Handler
 */
function handleError($message, $redirect_url = null) {
    error_log($message);
    setFlash('error', 'Terjadi kesalahan. Silakan coba lagi.');
    if ($redirect_url) {
        redirect($redirect_url);
    }
}

/**
 * AJAX Response Helper
 */
function jsonResponse($success, $data = [], $message = '') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'message' => $message
    ]);
    exit();
}

/**
 * Check if request is AJAX
 */
function isAjax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

// Auto-load protection
if (!defined('DB_CONFIG')) {
    die('Direct access not permitted');
}