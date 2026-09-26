<?php
// google_auth.php - Unified Google OAuth 2.0 / Google Identity Authentication Handler
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';

$redirect = $_GET['redirect'] ?? $_POST['redirect'] ?? '';

/**
 * Decode Base64Url string to array/JSON
 */
function base64url_decode_json($data) {
    $b64 = str_replace(['-', '_'], ['+', '/'], $data);
    $remainder = strlen($b64) % 4;
    if ($remainder) {
        $b64 .= str_repeat('=', 4 - $remainder);
    }
    $json = base64_decode($b64);
    return json_decode($json, true);
}

/**
 * Perform Google Authentication and auto-registration
 */
function handle_google_login($credential_token, $conn) {
    if (empty($credential_token)) {
        return ['success' => false, 'message' => 'गूगल सुरक्षा टोकन प्राप्त नहीं हुआ।'];
    }

    $parts = explode('.', $credential_token);
    if (count($parts) !== 3) {
        return ['success' => false, 'message' => 'अमान्य गूगल टोकन संरचना।'];
    }

    $payload = base64url_decode_json($parts[1]);
    if (!$payload || empty($payload['email'])) {
        return ['success' => false, 'message' => 'गूगल टोकन से ईमेल पता प्राप्त करने में विफलता।'];
    }

    $email = strtolower(trim($payload['email']));
    $name = trim($payload['name'] ?? $payload['given_name'] ?? explode('@', $email)[0]);
    $picture = trim($payload['picture'] ?? 'images/authors/author-default.jpg');
    $google_id = trim($payload['sub'] ?? '');

    // 1. Check if email belongs to an Admin account in `admin_users`
    $stmt_admin = $conn->prepare("SELECT id, username, email, password_hash, full_name, role FROM admin_users WHERE email = ? LIMIT 1");
    if ($stmt_admin) {
        $stmt_admin->bind_param("s", $email);
        $stmt_admin->execute();
        $admin = $stmt_admin->get_result()->fetch_assoc();
        $stmt_admin->close();

        if ($admin) {
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
                'message' => 'गूगल द्वारा व्यवस्थापक लॉगिन सफल रहा!'
            ];
        }
    }

    // 2. Check if user already exists in `users` table
    $stmt_user = $conn->prepare("SELECT id, name, email, bio, profile_photo, phone, role FROM users WHERE email = ? LIMIT 1");
    if ($stmt_user) {
        $stmt_user->bind_param("s", $email);
        $stmt_user->execute();
        $user = $stmt_user->get_result()->fetch_assoc();
        $stmt_user->close();

        if ($user) {
            // Update profile photo if default or empty
            if (empty($user['profile_photo']) || $user['profile_photo'] === 'images/images/authors/author-default.jpg' || $user['profile_photo'] === 'images/authors/author-default.jpg') {
                $stmt_upd = $conn->prepare("UPDATE users SET profile_photo = ? WHERE id = ?");
                if ($stmt_upd) {
                    $stmt_upd->bind_param("si", $picture, $user['id']);
                    $stmt_upd->execute();
                    $stmt_upd->close();
                    $user['profile_photo'] = $picture;
                }
            }

            session_regenerate_id(true);
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_photo'] = $user['profile_photo'] ?: $picture;
            $_SESSION['user_bio'] = $user['bio'] ?: '';
            return [
                'success' => true,
                'role' => 'author',
                'redirect' => 'submit.php',
                'message' => 'गूगल द्वारा रचनाकार लॉगिन सफल रहा!'
            ];
        }
    }

    // 3. Register new User / Author automatically with Google Account info
    $random_pass = bin2hex(random_bytes(16));
    $hash = password_hash($random_pass, PASSWORD_BCRYPT);
    $bio = 'गूगल खाते से पंजीकृत रचनाकार';

    $stmt_ins = $conn->prepare("INSERT INTO users (name, email, password_hash, bio, profile_photo) VALUES (?, ?, ?, ?, ?)");
    if ($stmt_ins) {
        $stmt_ins->bind_param("sssss", $name, $email, $hash, $bio, $picture);
        if ($stmt_ins->execute()) {
            $new_id = $stmt_ins->insert_id;
            $stmt_ins->close();

            session_regenerate_id(true);
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $new_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_photo'] = $picture;
            $_SESSION['user_bio'] = $bio;

            return [
                'success' => true,
                'role' => 'author',
                'redirect' => 'submit.php',
                'message' => 'गूगल खाते से नया रचनाकार खाता पंजीकृत हुआ!'
            ];
        }
        $stmt_ins->close();
    }

    return ['success' => false, 'message' => 'डेटाबेस खाता निर्माण त्रुटि: ' . $conn->error];
}

// Request Entry Point
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $credential = $_POST['credential'] ?? '';

    if (empty($credential)) {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        if (is_array($data) && !empty($data['credential'])) {
            $credential = $data['credential'];
        }
    }

    $res = handle_google_login($credential, $conn);

    if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        send_json_response($res['success'], $res['message'], $res);
    } else {
        if ($res['success']) {
            $target = !empty($redirect) ? $redirect : $res['redirect'];
            header('Location: ' . $target);
            exit();
        } else {
            $_SESSION['login_error'] = $res['message'];
            header('Location: login.php?error=' . urlencode($res['message']));
            exit();
        }
    }
} else {
    header('Location: login.php');
    exit();
}
