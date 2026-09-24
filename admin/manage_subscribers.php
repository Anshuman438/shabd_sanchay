<?php
// admin/manage_subscribers.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_admin_auth();

// Handle CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=shabd_sanchay_subscribers_' . date('Y-m-d') . '.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Email', 'Subscribed Date']);
    $res = $conn->query("SELECT id, email, subscribed_at FROM newsletter_subscribers ORDER BY subscribed_at DESC");
    while ($row = $res->fetch_assoc()) {
        fputcsv($output, [$row['id'], $row['email'], $row['subscribed_at']]);
    }
    fclose($output);
    exit();
}

$page_title = "न्यूज़लेटर ग्राहक";
require_once __DIR__ . '/header.php';

$action = $_GET['action'] ?? '';
$id = intval($_GET['id'] ?? 0);
$msg = '';
$error = '';

if ($action === 'delete' && $id > 0) {
    if (!validate_csrf_token($_GET['csrf'] ?? '')) {
        $error = "सुरक्षा टोकन अमान्य है।";
    } else {
        $stmt = $conn->prepare("DELETE FROM newsletter_subscribers WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $msg = "ईमेल सफलतापूर्वक हटा दी गई।";
        } else {
            $error = "हटाने में त्रुटि: " . $stmt->error;
        }
        $stmt->close();
    }
}

$subscribers = $conn->query("SELECT * FROM newsletter_subscribers ORDER BY subscribed_at DESC");
$csrf_token = generate_csrf_token();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 22px; margin-bottom: 4px;">पत्रिका ग्राहक (Newsletter Subscribers)</h1>
        <p style="color: #64748b; font-size: 13px;">साहित्यिक पत्रिका और दैनिक शब्दकोश के पंजीकृत पाठक।</p>
    </div>
    <a href="manage_subscribers.php?export=csv" class="btn-action btn-approve" style="padding: 10px 18px; font-size: 14px;">CSV डाउनलोड करें</a>
</div>

<?php if (!empty($msg)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th style="width: 50px;">ID</th>
                <th>ईमेल पता (Email Address)</th>
                <th>सदस्यता तिथि (Subscribed At)</th>
                <th style="text-align: right;">क्रियाएँ</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($subscribers && $subscribers->num_rows > 0): ?>
                <?php while ($sub = $subscribers->fetch_assoc()): ?>
                    <tr>
                        <td><?= $sub['id'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($sub['email']) ?></strong>
                        </td>
                        <td><?= format_hindi_date($sub['subscribed_at']) ?></td>
                        <td style="text-align: right;">
                            <a href="manage_subscribers.php?action=delete&id=<?= $sub['id'] ?>&csrf=<?= $csrf_token ?>" class="btn-action btn-delete" onclick="return confirm('क्या आप वाकई इस ईमेल को हटाना चाहते हैं?')">हटाएँ</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" style="text-align:center; padding: 24px;">कोई ग्राहक पंजीकृत नहीं है।</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
