<?php
// admin/manage_comments.php
$page_title = "टिप्पणी मॉडरेटर";
require_once __DIR__ . '/header.php';

$action = $_GET['action'] ?? '';
$id = intval($_GET['id'] ?? 0);
$msg = '';
$error = '';

if ($id > 0 && !empty($action)) {
    if (!validate_csrf_token($_GET['csrf'] ?? '')) {
        $error = "सुरक्षा टोकन अमान्य है।";
    } else {
        if ($action === 'approve') {
            $stmt = $conn->prepare("UPDATE comments SET status = 'approved' WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
            $msg = "टिप्पणी स्वीकृत (Approved) की गई।";
        } elseif ($action === 'reject') {
            $stmt = $conn->prepare("UPDATE comments SET status = 'rejected' WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
            $msg = "टिप्पणी अस्वीकृत (Rejected) की गई।";
        } elseif ($action === 'delete') {
            $stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
            $msg = "टिप्पणी हटा दी गई।";
        }
    }
}

$status_filter = $_GET['status'] ?? 'all';
$sql = "SELECT * FROM comments WHERE 1=1";
$params = [];
$types = "";

if ($status_filter !== 'all') {
    $sql .= " AND status = ?";
    $params[] = $status_filter;
    $types .= "s";
}
$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$comments = $stmt->get_result();

$csrf_token = generate_csrf_token();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 22px; margin-bottom: 4px;">पाठक टिप्पणी मॉडरेटर (Comments Moderation)</h1>
        <p style="color: #64748b; font-size: 13px;">रचनाओं पर प्राप्त पाठकों की प्रतिक्रियाओं की समीक्षा एवं स्वीकृति करें।</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="manage_comments.php?status=all" class="btn-action <?= $status_filter === 'all' ? 'btn-edit' : '' ?>">सभी</a>
        <a href="manage_comments.php?status=pending" class="btn-action <?= $status_filter === 'pending' ? 'btn-edit' : '' ?>">लंबित (Pending)</a>
        <a href="manage_comments.php?status=approved" class="btn-action <?= $status_filter === 'approved' ? 'btn-edit' : '' ?>">स्वीकृत (Approved)</a>
        <a href="manage_comments.php?status=rejected" class="btn-action <?= $status_filter === 'rejected' ? 'btn-edit' : '' ?>">अस्वीकृत</a>
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
                <th>पाठक</th>
                <th>विधा व ID</th>
                <th>टिप्पणी</th>
                <th>स्थिति</th>
                <th>दिनांक</th>
                <th style="text-align: right;">क्रियाएँ</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($comments && $comments->num_rows > 0): ?>
                <?php while ($c = $comments->fetch_assoc()): ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($c['name']) ?></strong><br>
                            <small style="color: #64748b;"><?= htmlspecialchars($c['email']) ?></small>
                        </td>
                        <td>
                            <span class="badge" style="background:#e0f2fe; color:#0369a1; text-transform: capitalize;">
                                <?= htmlspecialchars($c['content_type']) ?> #<?= $c['content_id'] ?>
                            </span>
                        </td>
                        <td style="max-width: 320px;">
                            <?= nl2br(htmlspecialchars($c['comment'])) ?>
                        </td>
                        <td>
                            <span class="badge" style="background: <?= $c['status'] == 'approved' ? '#dcfce7; color:#166534;' : ($c['status'] == 'pending' ? '#fef3c7; color:#92400e;' : '#fee2e2; color:#991b1b;') ?>">
                                <?= $c['status'] ?>
                            </span>
                        </td>
                        <td><small><?= format_hindi_date($c['created_at']) ?></small></td>
                        <td style="text-align: right; white-space: nowrap;">
                            <?php if ($c['status'] !== 'approved'): ?>
                                <a href="manage_comments.php?action=approve&id=<?= $c['id'] ?>&csrf=<?= $csrf_token ?>" class="btn-action btn-approve" title="स्वीकृत करें">स्वीकृत</a>
                            <?php endif; ?>
                            <?php if ($c['status'] !== 'rejected'): ?>
                                <a href="manage_comments.php?action=reject&id=<?= $c['id'] ?>&csrf=<?= $csrf_token ?>" class="btn-action" style="background:#fef3c7; color:#92400e;" title="अस्वीकृत करें">अस्वीकृत</a>
                            <?php endif; ?>
                            <a href="manage_comments.php?action=delete&id=<?= $c['id'] ?>&csrf=<?= $csrf_token ?>" class="btn-action btn-delete" onclick="return confirm('क्या आप वाकई इस टिप्पणी को हटाना चाहते हैं?')" title="हटाएँ">हटाएँ</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" style="text-align:center; padding: 24px;">कोई टिप्पणी नहीं मिली।</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
