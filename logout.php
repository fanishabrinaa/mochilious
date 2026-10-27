<?php
require_once 'config.php';

// Destroy session
session_destroy();

// Clear all session variables
$_SESSION = array();

// Delete session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Restart session for flash message
session_start();
setFlash('success', 'Anda telah berhasil logout.');

// Redirect to home
redirect('index.php');