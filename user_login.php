<?php
// user_login.php - Member / Author Login and Registration Portal
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';

$error = '';
$success = '';
$redirect = $_GET['redirect'] ?? 'submit.php';

// Handle user logout action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    logout_user();
    header('Location: ' . ($redirect ?: 'index.php'));
    exit();
}

// If already logged in
if (is_user_logged_in() && !isset($_GET['action'])) {
    header('Location: ' . $redirect);
    exit();
}

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_type = $_POST['form_type'] ?? 'login';

    if ($form_type === 'login') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = 'कृपया ईमेल और पासवर्ड दोनों दर्ज करें।';
        } else {
            if (authenticate_user($email, $password, $conn)) {
                header('Location: ' . $redirect);
                exit();
            } else {
                $error = 'अमान्य ईमेल या पासवर्ड। कृपया पुनः प्रयास करें।';
            }
        }
    } elseif ($form_type === 'register') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $bio = trim($_POST['bio'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $photo_url = trim($_POST['profile_photo'] ?? '');

        // Handle uploaded profile picture if present
        $uploaded_photo = 'images/authors/author-default.jpg';
        if (!empty($_FILES['photo_file']['name'])) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['photo_file']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $upload_dir = __DIR__ . '/uploads/authors/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $filename = 'author_' . time() . '_' . rand(100, 999) . '.' . $ext;
                if (move_uploaded_file($_FILES['photo_file']['tmp_name'], $upload_dir . $filename)) {
                    $uploaded_photo = 'uploads/authors/' . $filename;
                }
            }
        } elseif (!empty($photo_url)) {
            $uploaded_photo = $photo_url;
        }

        if (empty($name) || empty($email) || empty($password)) {
            $error = 'कृपया नाम, ईमेल और पासवर्ड अवश्य भरें।';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'कृपया एक मान्य ईमेल पता दर्ज करें।';
        } elseif (strlen($password) < 6) {
            $error = 'पासवर्ड कम से कम 6 अक्षरों का होना चाहिए।';
        } else {
            $res = register_user($name, $email, $password, $bio, $uploaded_photo, $phone, $conn);
            if ($res['success']) {
                header('Location: ' . $redirect);
                exit();
            } else {
                $error = $res['message'];
            }
        }
    }
}

$page_title = "रचनाकार लॉगिन एवं पंजीकरण | शब्द संचय";
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Biryani:wght@400;600;700;800;900&family=Noto+Sans+Devanagari:wght@400;500;600;700;800&family=Noto+Serif+Devanagari:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        .auth-container-wrap {
            max-width: 520px;
            margin: 3rem auto;
            background: var(--card-bg, #ffffff);
            border: 1px solid rgba(200, 90, 23, 0.2);
            border-radius: 20px;
            padding: 2.2rem 2.5rem;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
        }
        [data-theme="dark"] .auth-container-wrap {
            background: #201a16;
            border-color: rgba(246, 173, 85, 0.25);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.45);
        }
        .auth-tab-switch {
            display: flex;
            background: rgba(200, 90, 23, 0.08);
            border-radius: 12px;
            padding: 4px;
            margin-bottom: 1.8rem;
        }
        [data-theme="dark"] .auth-tab-switch {
            background: rgba(255, 255, 255, 0.06);
        }
        .auth-tab-btn {
            flex: 1;
            padding: 0.65rem;
            text-align: center;
            border: none;
            background: transparent;
            font-weight: 700;
            font-size: 0.95rem;
            border-radius: 9px;
            color: var(--text);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .auth-tab-btn.active {
            background: #c85a17;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(200, 90, 23, 0.3);
        }
        [data-theme="dark"] .auth-tab-btn.active {
            background: #f6ad55;
            color: #1c1511;
        }
        .auth-form-group {
            margin-bottom: 1.25rem;
        }
        .auth-form-group label {
            display: block;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.4rem;
            color: var(--text);
        }
        .auth-form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid rgba(0,0,0,0.12);
            border-radius: 10px;
            font-family: inherit;
            font-size: 0.95rem;
            background: var(--bg);
            color: var(--text);
            box-sizing: border-box;
            transition: border-color 0.2s;
        }
        [data-theme="dark"] .auth-form-control {
            border-color: rgba(255,255,255,0.15);
            background: #181310;
        }
        .auth-form-control:focus {
            outline: none;
            border-color: #c85a17;
            box-shadow: 0 0 0 3px rgba(200, 90, 23, 0.15);
        }
        .auth-submit-btn {
            width: 100%;
            padding: 0.85rem;
            background: linear-gradient(135deg, #c85a17 0%, #a04000 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 14px rgba(200, 90, 23, 0.35);
            margin-top: 0.6rem;
        }
        [data-theme="dark"] .auth-submit-btn {
            background: linear-gradient(135deg, #f6ad55 0%, #dd6b20 100%);
            color: #1c1511;
        }
        .auth-submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(200, 90, 23, 0.45);
        }
        .auth-alert-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            margin-bottom: 1.2rem;
            border: 1px solid #fecaca;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="main-content">
        <div class="container" style="padding-top: 2rem; padding-bottom: 3rem;">
            <div class="auth-container-wrap">
                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <span style="color: #c85a17; font-weight: 800; font-size: 0.85rem; letter-spacing: 1px;">रचनाकार मंच • AUTHOR PORTAL</span>
                    <h2 style="font-family: 'Rozha One', 'Biryani', serif; margin: 0.4rem 0 0.5rem; font-size: 1.8rem;">शब्द संचय रचनाकार खाता</h2>
                    <p style="color: #64748b; font-size: 0.92rem; margin: 0;">लॉग इन करें ताकि आपकी रचनाएँ आपकी प्रोफ़ाइल व चित्र के साथ प्रकाशित हों।</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="auth-alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <div class="auth-tab-switch">
                    <button type="button" class="auth-tab-btn active" id="tab-login-btn" onclick="switchAuthTab('login')">लॉग इन करें</button>
                    <button type="button" class="auth-tab-btn" id="tab-register-btn" onclick="switchAuthTab('register')">नया खाता बनाएँ</button>
                </div>

                <!-- Login Form -->
                <form action="user_login.php?redirect=<?= urlencode($redirect) ?>" method="POST" id="form-login">
                    <input type="hidden" name="form_type" value="login">
                    
                    <div class="auth-form-group">
                        <label for="login-email">ईमेल पता *</label>
                        <input type="email" name="email" id="login-email" class="auth-form-control" required placeholder="अपना ईमेल दर्ज करें">
                    </div>

                    <div class="auth-form-group">
                        <label for="login-password">पासवर्ड *</label>
                        <input type="password" name="password" id="login-password" class="auth-form-control" required placeholder="••••••••">
                    </div>

                    <button type="submit" class="auth-submit-btn">लॉग इन करें</button>
                </form>

                <!-- Register Form -->
                <form action="user_login.php?redirect=<?= urlencode($redirect) ?>" method="POST" enctype="multipart/form-data" id="form-register" style="display: none;">
                    <input type="hidden" name="form_type" value="register">

                    <div class="auth-form-group">
                        <label for="reg-name">पूरा नाम (रचनाकार का नाम) *</label>
                        <input type="text" name="name" id="reg-name" class="auth-form-control" required placeholder="उदा. रामधारी सिंह 'दिनकर'">
                    </div>

                    <div class="auth-form-group">
                        <label for="reg-email">ईमेल पता *</label>
                        <input type="email" name="email" id="reg-email" class="auth-form-control" required placeholder="your.email@example.com">
                    </div>

                    <div class="auth-form-group">
                        <label for="reg-password">पासवर्ड (न्यूनतम 6 अक्षर) *</label>
                        <input type="password" name="password" id="reg-password" class="auth-form-control" required minlength="6" placeholder="••••••••">
                    </div>

                    <div class="auth-form-group">
                        <label for="reg-bio">संक्षिप्त परिचय (Author Bio)</label>
                        <textarea name="bio" id="reg-bio" class="auth-form-control" rows="2" placeholder="अपने साहित्यिक परिचय या अभिरुचि के बारे में 2 पंक्तियाँ..."></textarea>
                    </div>

                    <div class="auth-form-group">
                        <label for="reg-photo">प्रोफ़ाइल चित्र (Profile Photo)</label>
                        <input type="file" name="photo_file" id="reg-photo" class="auth-form-control" accept="image/*">
                        <small style="color: #64748b; font-size: 0.8rem; display: block; margin-top: 4px;">JPG, PNG या WEBP (वैकल्पिक)</small>
                    </div>

                    <div class="auth-form-group">
                        <label for="reg-phone">संपर्क सूत्र / फ़ोन (वैकल्पिक)</label>
                        <input type="tel" name="phone" id="reg-phone" class="auth-form-control" placeholder="+91 98765 43210">
                    </div>

                    <button type="submit" class="auth-submit-btn">पंजीकरण करें व आगे बढ़ें</button>
                </form>

                <div style="text-align: center; margin-top: 1.5rem; font-size: 0.88rem; color: #64748b;">
                    <a href="submit.php" style="color: #c85a17; text-decoration: none; font-weight: 600;">बिना लॉग इन किए रचना सबमिट करें →</a>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script>
    function switchAuthTab(tab) {
        const loginForm = document.getElementById('form-login');
        const regForm = document.getElementById('form-register');
        const loginBtn = document.getElementById('tab-login-btn');
        const regBtn = document.getElementById('tab-register-btn');

        if (tab === 'register') {
            loginForm.style.display = 'none';
            regForm.style.display = 'block';
            loginBtn.classList.remove('active');
            regBtn.classList.add('active');
        } else {
            regForm.style.display = 'none';
            loginForm.style.display = 'block';
            regBtn.classList.remove('active');
            loginBtn.classList.add('active');
        }
    }
    </script>
</body>
</html>
