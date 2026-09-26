<?php
// Vercel Serverless Entrypoint Router for Shabd Sanchay

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// If root path or empty, serve main index.php
if ($uri === '/' || $uri === '' || $uri === '/index.php') {
    chdir(__DIR__ . '/..');
    require __DIR__ . '/../index.php';
    exit;
}

// Build target file path relative to project root
$target = realpath(__DIR__ . '/..' . $uri);
$project_root = realpath(__DIR__ . '/..');

// Ensure target exists, is a file, and stays inside project root (security check)
if ($target && is_file($target) && strpos($target, $project_root) === 0) {
    chdir(dirname($target));
    require $target;
    exit;
}

// Fallback to main index.php
chdir(__DIR__ . '/..');
require __DIR__ . '/../index.php';
