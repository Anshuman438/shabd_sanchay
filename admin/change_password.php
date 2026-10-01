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
        $admin_username   = trim($_SESSION['admin_username'] ?? 'superadmin');

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error = "कृपया सभी फ़ील्ड (वर्तमान और नया पासवर्ड) भरें।";
        } elseif (strlen($new_password) < 6) {
            $error = "नया पासवर्ड कम से कम 6 अक्षरों का होना चाहिए।";
        } elseif ($new_password !== $confirm_password) {
            $error = "नया पासवर्ड और पुष्टि पासवर्ड मेल नहीं खाते।";
        } else {
            // Fetch current password hash by admin_id or username
            if ($admin_id > 0) {
                $stmt = $conn->prepare("SELECT id, username, password_hash FROM admin_users WHERE id = ? LIMIT 1");
                $stmt->bind_param("i", $admin_id);
            } else {
                $stmt = $conn->prepare("SELECT id, username, password_hash FROM admin_users WHERE username = ? LIMIT 1");
                $stmt->bind_param("s", $admin_username);
            }
            
            $stmt->execute();
            $admin = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($admin && password_verify($current_password, $admin['password_hash'])) {
                $target_id = $admin['id'];
                $new_hash = password_hash($new_password, PASSWORD_BCRYPT);
                $upd = $conn->prepare("UPDATE admin_users SET password_hash = ? WHERE id = ?");
                $upd->bind_param("si", $new_hash, $target_id);
                if ($upd->execute()) {
                    $msg = "✓ आपका एडमिन पासवर्ड सफलतापूर्वक अपडेट कर दिया गया है! यह स्थायी रूप से सेव हो गया है।";
                } else {
                    $error = "डेटाबेस त्रुटि: " . $conn->error;
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
                <div style="position: relative;">
                    <input type="password" id="current_password" name="current_password" class="form-control" required placeholder="••••••••" style="padding-right: 40px;">
                    <button type="button" onclick="togglePass('current_password')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--admin-text-muted);">👁</button>
                </div>
            </div>

            <div class="form-group">
                <label for="new_password">नया पासवर्ड (New Password)</label>
                <div style="position: relative;">
                    <input type="password" id="new_password" name="new_password" class="form-control" required placeholder="कम से कम 6 अक्षर" style="padding-right: 40px;">
                    <button type="button" onclick="togglePass('new_password')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--admin-text-muted);">👁</button>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">नया पासवर्ड पुनः दर्ज करें (Confirm New Password)</label>
                <div style="position: relative;">
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required placeholder="पुनः दर्ज करें" style="padding-right: 40px;">
                    <button type="button" onclick="togglePass('confirm_password')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--admin-text-muted);">👁</button>
                </div>
            </div>

            <div style="margin-top: 24px;">
                <button type="submit" class="btn-action btn-approve" style="padding: 12px 28px; font-size: 14px; font-weight: 700; width: 100%; justify-content: center;">
                    🔒 पासवर्ड अपडेट करें (Update Password)
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePass(id) {
    const input = document.getElementById(id);
    if (input) {
        input.type = input.type === 'password' ? 'text' : 'password';
    }
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
