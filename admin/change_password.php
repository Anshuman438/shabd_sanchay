<?php
// admin/change_password.php - Change Admin Password Portal
$page_title = "पासवर्ड बदलें (Change Password)";
require_once __DIR__ . '/header.php';

$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "सुरक्षा टोकन अमान्य है। (Invalid CSRF token)";
    } else {
        $current_password = $_POST['current_password'] ?? '';
        $new_password     = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $admin_id         = intval($_SESSION['admin_id'] ?? 0);

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error = "कृपया सभी फ़ील्ड (वर्तमान और नया पासवर्ड) भरें।";
        } elseif (strlen($new_password) < 6) {
            $error = "नया पासवर्ड कम से कम 6 अक्षरों का होना चाहिए।";
        } elseif ($new_password !== $confirm_password) {
            $error = "नया पासवर्ड और पुष्टि पासवर्ड मेल नहीं खाते।";
        } else {
            // Fetch current password hash
            $stmt = $conn->prepare("SELECT password_hash FROM admin_users WHERE id = ? LIMIT 1");
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            $admin = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($admin && password_verify($current_password, $admin['password_hash'])) {
                $new_hash = password_hash($new_password, PASSWORD_BCRYPT);
                $upd = $conn->prepare("UPDATE admin_users SET password_hash = ? WHERE id = ?");
                $upd->bind_param("si", $new_hash, $admin_id);
                if ($upd->execute()) {
                    $msg = "आपका एडमिन पासवर्ड सफलतापूर्वक अपडेट कर दिया गया है!";
                } else {
                    $error = "अपडेट करने में त्रुटि: " . $conn->error;
                }
                $upd->close();
            } else {
                $error = "वर्तमान पासवर्ड गलत है। कृपया पुनः प्रयास करें।";
            }
        }
    }
}

$csrf_token = generate_csrf_token();
?>

<div style="max-width: 600px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 style="font-size: 22px; margin-bottom: 4px;">एडमिन पासवर्ड बदलें (Change Password)</h1>
            <p style="color: var(--admin-text-muted); font-size: 13px;">अपने एडमिन खाते का पासवर्ड सुरक्षित रूप से बदलें।</p>
        </div>
    </div>

    <?php if (!empty($msg)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="form-card">
        <form action="change_password.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="form-group">
                <label for="current_password">वर्तमान पासवर्ड (Current Password)</label>
                <input type="password" id="current_password" name="current_password" class="form-control" required placeholder="••••••••">
            </div>

            <div class="form-group">
                <label for="new_password">नया पासवर्ड (New Password)</label>
                <input type="password" id="new_password" name="new_password" class="form-control" required placeholder="कम से कम 6 अक्षर">
            </div>

            <div class="form-group">
                <label for="confirm_password">नया पासवर्ड पुनः दर्ज करें (Confirm New Password)</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required placeholder="पुनः दर्ज करें">
            </div>

            <div style="margin-top: 24px;">
                <button type="submit" class="btn-action btn-approve" style="padding: 10px 24px; font-size: 14px; font-weight: 700;">
                    🔒 पासवर्ड अपडेट करें (Update Password)
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
