<?php
// PHP built-in server router for WordPress
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$file = __DIR__ . $uri;

// Serve static files directly
if ($uri !== '/' && file_exists($file) && !is_dir($file)) {
    return false;
}

// Route to WordPress
if (!file_exists(__DIR__ . '/index.php')) {
    // WordPress not yet installed
    echo "WordPress not found. Please wait for setup to complete.";
    exit;
}

require_once __DIR__ . '/index.php';
