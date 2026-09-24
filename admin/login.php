<?php
// admin/login.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';

// Redirect if already logged in
if (is_admin_logged_in()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token()) {
        $error = "सत्र सुरक्षा टोकन अमान्य है। कृपया पुनः प्रयास करें। (Invalid CSRF Token)";
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            $error = "कृपया उपयोगकर्ता नाम और पासवर्ड दोनों दर्ज करें।";
        } else {
            if (authenticate_admin($username, $password, $conn)) {
                header('Location: dashboard.php');
                exit();
            } else {
                $error = "गलत उपयोगकर्ता नाम या पासवर्ड (Invalid Credentials)";
            }
        }
    }
}

$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>व्यवस्थापक लॉगिन (Admin Login) - शब्द संचय</title>
    <link href="https://fonts.googleapis.com/css2?family=Biryani:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Noto+Sans+Devanagari:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', 'Noto Sans Devanagari', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #312e81 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(10px);
            width: 100%;
            max-width: 420px;
            border-radius: 16px;
            padding: 36px 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
        }
        .login-header {
            text-align: center;
            margin-bottom: 28px;
        }
        .login-header h1 {
            font-family: 'Biryani', 'Noto Sans Devanagari', sans-serif;
            font-weight: 900;
            font-size: 26px;
            color: #1e3a8a;
            margin-bottom: 6px;
        }
        .login-header p {
            color: #64748b;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 13px;
            color: #334155;
        }
        .form-control {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
        }
        .btn-submit {
            width: 100%;
            padding: 12px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 8px;
        }
        .btn-submit:hover {
            background: #1d4ed8;
        }
        .alert {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            text-align: center;
        }
        .back-link {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
        }
        .back-link a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
        .demo-credentials {
            margin-top: 20px;
            padding: 10px;
            background: #f1f5f9;
            border-radius: 6px;
            font-size: 12px;
            color: #475569;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-header">
        <h1>शब्द संचय</h1>
        <p>व्यवस्थापक पोर्टल (Admin Portal)</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

        <div class="form-group">
            <label for="username">उपयोगकर्ता नाम या ईमेल (Username/Email)</label>
            <input type="text" id="username" name="username" class="form-control" required autofocus placeholder="admin">
        </div>

        <div class="form-group">
            <label for="password">पासवर्ड (Password)</label>
            <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
        </div>

        <button type="submit" class="btn-submit">लॉगिन करें</button>
    </form>

    <div class="demo-credentials">
        डिफ़ॉल्ट लॉगिन: <code>admin</code> / <code>admin123</code>
    </div>

    <div class="back-link">
        <a href="../index.php">← मुख्य वेबसाइट पर वापस जाएँ</a>
    </div>
</div>
</body>
</html>