<?php
// logout.php - Universal Clean Logout for Admin & Users
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

logout_admin();
logout_user();

$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

if (!headers_sent()) {
    setcookie('ss_session_data', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

@session_destroy();

$redirect = $_GET['redirect'] ?? 'index.php';
header('Location: ' . $redirect);
exit();
