<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config.php';

/**
 * Generate CSRF token and store in session
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token from POST request or header
 */
function validate_csrf_token($token = null) {
    if ($token === null) {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    }
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Require admin authentication guard
 */
function require_admin_auth() {
    if (empty($_SESSION['admin_logged_in']) || empty($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Check if current user is logged in as admin
 */
function is_admin_logged_in() {
    return !empty($_SESSION['admin_logged_in']);
}

/**
 * Unified login: Checks admin_users first, then users table.
 * Automatically routes admins to dashboard and authors/users to their portal.
 */
function unified_authenticate($login_id, $password, $conn) {
    // 1. Check admin_users
    $stmt = $conn->prepare("SELECT id, username, email, password_hash, full_name, role FROM admin_users WHERE username = ? OR email = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("ss", $login_id, $login_id);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_name'] = $admin['full_name'] ?: $admin['username'];
            $_SESSION['admin_role'] = $admin['role'] ?: 'admin';
            return [
                'success' => true,
                'role' => 'admin',
                'redirect' => 'admin/dashboard.php',
                'message' => 'व्यवस्थापक लॉगिन सफल रहा!'
            ];
        }
    }

    // 2. Check users (authors/members)
    $stmt2 = $conn->prepare("SELECT id, name, email, password_hash, bio, profile_photo, phone, role FROM users WHERE email = ? OR name = ? LIMIT 1");
    if ($stmt2) {
        $stmt2->bind_param("ss", $login_id, $login_id);
        $stmt2->execute();
        $user = $stmt2->get_result()->fetch_assoc();
        $stmt2->close();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_photo'] = $user['profile_photo'] ?: 'images/authors/author-default.jpg';
            $_SESSION['user_bio'] = $user['bio'] ?: '';
            return [
                'success' => true,
                'role' => 'author',
                'redirect' => 'submit.php',
                'message' => 'रचनाकार लॉगिन सफल रहा!'
            ];
        }
    }

    return [
        'success' => false,
        'role' => 'none',
        'redirect' => '',
        'message' => 'गलत उपयोगकर्ता नाम/ईमेल या पासवर्ड।'
    ];
}

/**
 * Authenticate admin with username/email and password
 */
function authenticate_admin($username, $password, $conn) {
    $stmt = $conn->prepare("SELECT id, username, email, password_hash, full_name, role FROM admin_users WHERE username = ? OR email = ? LIMIT 1");
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password_hash'])) {
            // Re-generate session ID to prevent session fixation
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_name'] = $user['full_name'] ?: $user['username'];
            $_SESSION['admin_role'] = $user['role'] ?: 'admin';
            $stmt->close();
            return true;
        }
    }
    $stmt->close();
    return false;
}

/**
 * Secure logout for admin
 */
function logout_admin() {
    unset($_SESSION['admin_logged_in'], $_SESSION['admin_id'], $_SESSION['admin_username'], $_SESSION['admin_name'], $_SESSION['admin_role']);
}

/**
 * Check if a public member/author is logged in
 */
function is_user_logged_in() {
    return !empty($_SESSION['user_logged_in']) && !empty($_SESSION['user_id']);
}

/**
 * Get current logged in member's full profile details
 */
function get_logged_in_user($conn) {
    if (!is_user_logged_in()) {
        return null;
    }
    $user_id = intval($_SESSION['user_id']);
    $stmt = $conn->prepare("SELECT id, name, email, bio, profile_photo, phone, role, created_at FROM users WHERE id = ? LIMIT 1");
    if (!$stmt) return null;
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $user;
}

/**
 * Authenticate public member / author
 */
function authenticate_user($email, $password, $conn) {
    $stmt = $conn->prepare("SELECT id, name, email, password_hash, bio, profile_photo, phone, role FROM users WHERE email = ? LIMIT 1");
    if (!$stmt) return false;
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_photo'] = $user['profile_photo'] ?: 'images/authors/author-default.jpg';
        $_SESSION['user_bio'] = $user['bio'] ?: '';
        return true;
    }
    return false;
}

/**
 * Register a new public author / member
 */
function register_user($name, $email, $password, $bio = '', $profile_photo = 'images/authors/author-default.jpg', $phone = '', $conn = null) {
    if (!$conn) return ['success' => false, 'message' => 'डेटाबेस कनेक्शन त्रुटि'];
    
    // Check if email already exists
    $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        $stmt_check->close();
        return ['success' => false, 'message' => 'यह ईमेल पहले से पंजीकृत है। कृपया लॉग इन करें।'];
    }
    $stmt_check->close();

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, bio, profile_photo, phone) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) return ['success' => false, 'message' => 'सर्वर त्रुटि'];
    $stmt->bind_param("ssssss", $name, $email, $hash, $bio, $profile_photo, $phone);
    
    if ($stmt->execute()) {
        $new_id = $stmt->insert_id;
        $stmt->close();
        // Log in immediately
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = $new_id;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_photo'] = $profile_photo;
        $_SESSION['user_bio'] = $bio;
        return ['success' => true, 'message' => 'पंजीकरण सफल रहा!'];
    }
    $stmt->close();
    return ['success' => false, 'message' => 'पंजीकरण में त्रुटि: ' . $conn->error];
}

/**
 * Logout member
 */
function logout_user() {
    unset($_SESSION['user_logged_in'], $_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_email'], $_SESSION['user_photo'], $_SESSION['user_bio']);
}
?>
