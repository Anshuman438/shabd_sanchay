<?php
// login.php - Unified Login & Registration for Both Admin and Users/Authors
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';

$error = '';
$success = '';
$redirect = $_GET['redirect'] ?? '';

// Handle Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    logout_admin();
    logout_user();
    header('Location: ' . ($redirect ?: 'index.php'));
    exit();
}

// Redirect if already logged in as Admin
if (is_admin_logged_in()) {
    header('Location: admin/dashboard.php');
    exit();
}

// Redirect if already logged in as User/Author
if (is_user_logged_in()) {
    header('Location: ' . ($redirect ?: 'submit.php'));
    exit();
}

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_type = $_POST['form_type'] ?? 'login';

    if ($form_type === 'login') {
        $login_id = trim($_POST['login_id'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($login_id) || empty($password)) {
            $error = 'कृपया उपयोगकर्ता नाम/ईमेल और पासवर्ड दोनों दर्ज करें।';
        } else {
            $res = unified_authenticate($login_id, $password, $conn);
            if ($res['success']) {
                $target = !empty($redirect) ? $redirect : $res['redirect'];
                header('Location: ' . $target);
                exit();
            } else {
                $error = 'अमान्य क्रेडेंशियल्स! कृपया उपयोगकर्ता नाम/ईमेल या पासवर्ड की जाँच करें।';
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
            $reg_res = register_user($name, $email, $password, $bio, $uploaded_photo, $phone, $conn);
            if ($reg_res['success']) {
                header('Location: ' . ($redirect ?: 'submit.php'));
                exit();
            } else {
                $error = $reg_res['message'];
            }
        }
    }
}

$page_title = "लॉग इन एवं रचनाकार खाता | शब्द संचय";
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Biryani:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700&family=Noto+Sans+Devanagari:wght@400;500;600;700;800&family=Noto+Serif+Devanagari:wght@400;600;700&family=Rozha+One&display=swap" rel="stylesheet">
    <style>
        .unified-auth-container {
            max-width: 480px;
            margin: 3.5rem auto 4rem;
            background: var(--card-bg, #ffffff);
            border: 1px solid rgba(200, 90, 23, 0.22);
            border-radius: 24px;
            padding: 2.4rem 2.2rem;
            box-shadow: 0 16px 45px rgba(0, 0, 0, 0.08);
            position: relative;
        }
        [data-theme="dark"] .unified-auth-container {
            background: #201a16;
            border-color: rgba(246, 173, 85, 0.25);
            box-shadow: 0 16px 45px rgba(0, 0, 0, 0.45);
        }
        .auth-portal-header {
            text-align: center;
            margin-bottom: 1.8rem;
        }
        .auth-brand-badge {
            display: inline-block;
            font-size: 0.8rem;
            font-weight: 800;
            color: #c85a17;
            letter-spacing: 1px;
            background: rgba(200, 90, 23, 0.09);
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            margin-bottom: 0.5rem;
        }
        [data-theme="dark"] .auth-brand-badge {
            color: #f6ad55;
            background: rgba(246, 173, 85, 0.12);
        }
        .auth-tab-row {
            display: flex;
            background: rgba(200, 90, 23, 0.08);
            border-radius: 14px;
            padding: 4px;
            margin-bottom: 1.8rem;
        }
        [data-theme="dark"] .auth-tab-row {
            background: rgba(255, 255, 255, 0.06);
        }
        .auth-tab-pill {
            flex: 1;
            padding: 0.7rem 0.5rem;
            text-align: center;
            border: none;
            background: transparent;
            font-weight: 700;
            font-size: 0.95rem;
            border-radius: 10px;
            color: var(--text);
            cursor: pointer;
            transition: all 0.22s ease;
            font-family: inherit;
        }
        .auth-tab-pill.active {
            background: #c85a17;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(200, 90, 23, 0.32);
        }
        [data-theme="dark"] .auth-tab-pill.active {
            background: #f6ad55;
            color: #1c1511;
        }
        .u-form-group {
            margin-bottom: 1.3rem;
        }
        .u-form-group label {
            display: block;
            font-weight: 700;
            font-size: 0.88rem;
            margin-bottom: 0.45rem;
            color: var(--text);
        }
        .u-form-control {
            width: 100%;
            padding: 0.8rem 1.1rem;
            border: 1.5px solid rgba(0, 0, 0, 0.12);
            border-radius: 12px;
            font-family: inherit;
            font-size: 0.96rem;
            background: var(--bg);
            color: var(--text);
            box-sizing: border-box;
            transition: all 0.2s ease;
        }
        [data-theme="dark"] .u-form-control {
            border-color: rgba(255, 255, 255, 0.14);
            background: #181310;
        }
        .u-form-control:focus {
            outline: none;
            border-color: #c85a17;
            box-shadow: 0 0 0 3px rgba(200, 90, 23, 0.14);
        }
        [data-theme="dark"] .u-form-control:focus {
            border-color: #f6ad55;
            box-shadow: 0 0 0 3px rgba(246, 173, 85, 0.14);
        }
        .u-submit-btn {
            width: 100%;
            padding: 0.9rem;
            background: linear-gradient(135deg, #c85a17 0%, #a04000 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.02rem;
            font-weight: 800;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.22s ease;
            box-shadow: 0 5px 16px rgba(200, 90, 23, 0.35);
            margin-top: 0.6rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        [data-theme="dark"] .u-submit-btn {
            background: linear-gradient(135deg, #f6ad55 0%, #dd6b20 100%);
            color: #1c1511;
            box-shadow: 0 5px 16px rgba(246, 173, 85, 0.25);
        }
        .u-submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(200, 90, 23, 0.45);
        }
        .auth-msg-alert {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.8rem 1rem;
            border-radius: 10px;
            font-size: 0.9rem;
            margin-bottom: 1.4rem;
            border: 1px solid #fecaca;
            text-align: center;
        }
        .auth-helper-card {
            margin-top: 1.8rem;
            padding: 1rem;
            background: rgba(200, 90, 23, 0.05);
            border-radius: 12px;
            font-size: 0.84rem;
            color: #64748b;
            text-align: center;
            border: 1px dashed rgba(200, 90, 23, 0.2);
        }
        [data-theme="dark"] .auth-helper-card {
            background: rgba(255, 255, 255, 0.04);
            border-color: rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="main-content">
        <div class="container">
            <div class="unified-auth-container">
                
                <div class="auth-portal-header">
                    <span class="auth-brand-badge">🌸 शब्द संचय पोर्टल</span>
                    <h1 style="font-family: 'Rozha One', 'Biryani', serif; font-size: 1.9rem; margin: 0.3rem 0 0.4rem;">लॉग इन / खाता</h1>
                    <p style="color: #64748b; font-size: 0.92rem; margin: 0;">व्यवस्थापक एवं रचनाकार दोनों के लिए साझा प्रवेश द्वार</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="auth-msg-alert"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <!-- Tab switcher -->
                <div class="auth-tab-row">
                    <button type="button" class="auth-tab-pill active" id="tab-login-btn" onclick="switchLoginTab('login')">लॉग इन करें</button>
                    <button type="button" class="auth-tab-pill" id="tab-register-btn" onclick="switchLoginTab('register')">रचनाकार पंजीकरण</button>
                </div>

                <!-- Unified Login Form -->
                <form action="login.php<?= !empty($redirect) ? '?redirect=' . urlencode($redirect) : '' ?>" method="POST" id="form-login-pane">
                    <input type="hidden" name="form_type" value="login">

                    <div class="u-form-group">
                        <label for="login_id">उपयोगकर्ता नाम या ईमेल (Username / Email) *</label>
                        <input type="text" name="login_id" id="login_id" class="u-form-control" required autofocus placeholder="उदा. admin या your.email@example.com">
                    </div>

                    <div class="u-form-group">
                        <label for="password">पासवर्ड (Password) *</label>
                        <input type="password" name="password" id="password" class="u-form-control" required placeholder="••••••••">
                    </div>

                    <button type="submit" class="u-submit-btn">
                        <span>लॉग इन करें</span>
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </button>
                </form>

                <!-- New Author Registration Form -->
                <form action="login.php<?= !empty($redirect) ? '?redirect=' . urlencode($redirect) : '' ?>" method="POST" enctype="multipart/form-data" id="form-register-pane" style="display: none;">
                    <input type="hidden" name="form_type" value="register">

                    <div class="u-form-group">
                        <label for="reg-name">रचनाकार का पूरा नाम *</label>
                        <input type="text" name="name" id="reg-name" class="u-form-control" required placeholder="उदा. रामधारी सिंह दिनकर">
                    </div>

                    <div class="u-form-group">
                        <label for="reg-email">ईमेल पता *</label>
                        <input type="email" name="email" id="reg-email" class="u-form-control" required placeholder="your.email@example.com">
                    </div>

                    <div class="u-form-group">
                        <label for="reg-password">पासवर्ड (न्यूनतम 6 अक्षर) *</label>
                        <input type="password" name="password" id="reg-password" class="u-form-control" required minlength="6" placeholder="••••••••">
                    </div>

                    <div class="u-form-group">
                        <label for="reg-bio">संक्षिप्त साहित्यिक परिचय (Author Bio)</label>
                        <input type="text" name="bio" id="reg-bio" class="u-form-control" placeholder="उदा. कवि, निबंधकार, साहित्य प्रेमी...">
                    </div>

                    <div class="u-form-group">
                        <label for="reg-photo">प्रोफ़ाइल चित्र (Profile Photo)</label>
                        <input type="file" name="photo_file" id="reg-photo" class="u-form-control" accept="image/*">
                        <small style="color: #64748b; font-size: 0.78rem; display: block; margin-top: 3px;">JPG, PNG, WEBP</small>
                    </div>

                    <button type="submit" class="u-submit-btn">
                        <span>खाता बनाएँ व आगे बढ़ें</span>
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </button>
                </form>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script>
    function switchLoginTab(tab) {
        const loginPane = document.getElementById('form-login-pane');
        const regPane = document.getElementById('form-register-pane');
        const loginBtn = document.getElementById('tab-login-btn');
        const regBtn = document.getElementById('tab-register-btn');

        if (tab === 'register') {
            loginPane.style.display = 'none';
            regPane.style.display = 'block';
            loginBtn.classList.remove('active');
            regBtn.classList.add('active');
        } else {
            regPane.style.display = 'none';
            loginPane.style.display = 'block';
            regBtn.classList.remove('active');
            loginBtn.classList.add('active');
        }
    }
    </script>
</body>
</html>
