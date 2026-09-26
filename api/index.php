<?php
// Vercel Serverless Entrypoint Router for Shabd Sanchay

if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri = strtok($uri, '?');

$root_dir = dirname(__DIR__);

// Default route to home
if ($uri === '/' || $uri === '' || $uri === '/index.php') {
    chdir($root_dir);
    require $root_dir . '/index.php';
    exit;
}

$target = $root_dir . $uri;

// If specific file requested exists
if (file_exists($target) && is_file($target)) {
    chdir(dirname($target));
    require $target;
    exit;
}

// Fallback to home page
chdir($root_dir);
require $root_dir . '/index.php';
