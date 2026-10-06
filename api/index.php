<?php
// Vercel Serverless Entrypoint Router for Shabd Sanchay

// Enable automatic gzip compression for HTML/PHP responses
if (!ob_get_level() && !headers_sent() && extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
    @ob_start('ob_gzhandler');
}

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

            // Cache-Control: long cache for static assets
            $immutable_exts = ['png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'woff', 'woff2', 'ttf'];
            $long_cache_exts = ['css', 'js'];

            if (in_array($ext, $immutable_exts)) {
                // Images & fonts: 1 year cache
                header('Cache-Control: public, max-age=31536000, immutable');
                header('Vary: Accept-Encoding');
            } elseif (in_array($ext, $long_cache_exts)) {
                // CSS/JS: 1 week cache (versioned via ?v= query param)
                header('Cache-Control: public, max-age=604800, stale-while-revalidate=86400');
                header('Vary: Accept-Encoding');
            }

            // ETag support for conditional requests
            $mtime = filemtime($target);
            $etag = '"' . dechex($mtime) . '-' . dechex(filesize($target)) . '"';
            header('ETag: ' . $etag);
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $mtime) . ' GMT');

            if (
                (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === $etag) ||
                (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $mtime)
            ) {
                http_response_code(304);
                exit;
            }
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
