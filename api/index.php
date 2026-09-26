<?php
// Vercel Serverless Entrypoint Router for Shabd Sanchay

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri = strtok($uri, '?');

$root_dir = dirname(__DIR__);

// Default route to home
if ($uri === '/' || $uri === '' || $uri === '/index.php') {
    if (!headers_sent()) {
        header('Content-Type: text/html; charset=UTF-8');
    }
    chdir($root_dir);
    require $root_dir . '/index.php';
    exit;
}

$target = $root_dir . $uri;

// Handle static files and PHP execution
if (file_exists($target) && is_file($target)) {
    $ext = strtolower(pathinfo($target, PATHINFO_EXTENSION));
    $mime_types = [
        'css'   => 'text/css; charset=UTF-8',
        'js'    => 'application/javascript; charset=UTF-8',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'svg'   => 'image/svg+xml',
        'ico'   => 'image/x-icon',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'json'  => 'application/json'
    ];

    if (isset($mime_types[$ext])) {
        if (!headers_sent()) {
            header('Content-Type: ' . $mime_types[$ext]);
        }
        readfile($target);
        exit;
    }

    // Standard PHP execution
    if (!headers_sent()) {
        header('Content-Type: text/html; charset=UTF-8');
    }
    chdir(dirname($target));
    require $target;
    exit;
}

// Fallback to home page
if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}
chdir($root_dir);
require $root_dir . '/index.php';
