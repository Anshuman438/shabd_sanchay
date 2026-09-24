<?php
// admin/manage_contacts.php
$page_title = "संपर्क संदेश";
require_once __DIR__ . '/header.php';

$action = $_GET['action'] ?? '';
$id = intval($_GET['id'] ?? 0);
$msg = '';
$error = '';

if ($action === 'delete' && $id > 0) {
    if (!validate_csrf_token($_GET['csrf'] ?? '')) {
        $error = "सुरक्षा टोकन अमान्य है।";
    } else {
        $stmt = $conn->prepare("DELETE FROM contacts WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $msg = "संदेश सफलतापूर्वक हटा दिया गया।";
        } else {
            $error = "हटाने में त्रुटि: " . $stmt->error;
        }
        $stmt->close();
    }
}

$contacts = $conn->query("SELECT * FROM contacts ORDER BY created_at DESC");
$csrf_token = generate_csrf_token();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 22px; margin-bottom: 4px;">संपर्क पूछताछ एवं संदेश (Contact Inquiries)</h1>
        <p style="color: #64748b; font-size: 13px;">पाठकों और लेखकों द्वारा संपर्क फॉर्म के जरिए भेजे गए संदेश।</p>
    </div>
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
                <th>प्रेषक (Sender)</th>
                <th>विषय (Subject)</th>
                <th>संदेश (Message)</th>
                <th>दिनांक</th>
                <th style="text-align: right;">क्रियाएँ</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($contacts && $contacts->num_rows > 0): ?>
                <?php while ($row = $contacts->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($row['name']) ?></strong><br>
                            <a href="mailto:<?= htmlspecialchars($row['email']) ?>" style="color: #2563eb; text-decoration: none; font-size: 13px;"><?= htmlspecialchars($row['email']) ?></a>
                        </td>
                        <td><strong><?= htmlspecialchars($row['subject']) ?></strong></td>
                        <td style="max-width: 380px;">
                            <?= nl2br(htmlspecialchars($row['message'])) ?>
                        </td>
                        <td><small><?= format_hindi_date($row['created_at']) ?></small></td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="mailto:<?= htmlspecialchars($row['email']) ?>?subject=Re: <?= urlencode($row['subject']) ?>" class="btn-action btn-edit">उत्तर दें</a>
                            <a href="manage_contacts.php?action=delete&id=<?= $row['id'] ?>&csrf=<?= $csrf_token ?>" class="btn-action btn-delete" onclick="return confirm('क्या आप वाकई इस संदेश को हटाना चाहते हैं?')">हटाएँ</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align:center; padding: 24px;">कोई संपर्क संदेश नहीं मिला।</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
