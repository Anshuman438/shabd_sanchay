<?php
// admin/manage_plays.php
$page_title = "नाटक प्रबंधन";
require_once __DIR__ . '/header.php';

$action = $_GET['action'] ?? 'list';
$id = intval($_GET['id'] ?? 0);
$msg = '';
$error = '';

// Handle Delete
if ($action === 'delete' && $id > 0) {
    if (!validate_csrf_token($_GET['csrf'] ?? '')) {
        $error = "सुरक्षा टोकन अमान्य है।";
    } else {
        $stmt = $conn->prepare("DELETE FROM plays WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $msg = "नाटक सफलतापूर्वक हटा दिया गया।";
        } else {
            $error = "हटाने में त्रुटि: " . $stmt->error;
        }
        $stmt->close();
    }
    $action = 'list';
}

// Handle Add / Edit POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token()) {
        $error = "सुरक्षा टोकन अमान्य है।";
    } else {
        $title = trim($_POST['title'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $author_name = trim($_POST['author_name'] ?? '');
        $category = trim($_POST['category'] ?? 'ऐतिहासिक');
        $acts_count = intval($_POST['acts_count'] ?? 3);
        $image_url = trim($_POST['image_url'] ?? 'images/play-default.jpg');
        $edit_id = intval($_POST['edit_id'] ?? 0);

        if (empty($excerpt)) {
            $excerpt = make_excerpt($content, 180);
        }

        if (empty($title) || empty($content) || empty($author_name)) {
            $error = "कृपया शीर्षक, नाटक की पटकथा/संवाद और नाटककार का नाम अवश्य भरें।";
        } else {
            if ($edit_id > 0) {
                $stmt = $conn->prepare("UPDATE plays SET title = ?, excerpt = ?, content = ?, author_name = ?, category = ?, acts_count = ?, image_url = ? WHERE id = ?");
                $stmt->bind_param("sssssisi", $title, $excerpt, $content, $author_name, $category, $acts_count, $image_url, $edit_id);
                if ($stmt->execute()) {
                    $msg = "नाटक सफलतापूर्वक अपडेट किया गया!";
                    $action = 'list';
                } else {
                    $error = "अपडेट त्रुटि: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $stmt = $conn->prepare("INSERT INTO plays (title, excerpt, content, author_name, category, acts_count, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssis", $title, $excerpt, $content, $author_name, $category, $acts_count, $image_url);
                if ($stmt->execute()) {
                    $msg = "नया नाटक सफलतापूर्वक जोड़ा गया!";
                    $action = 'list';
                } else {
                    $error = "जोड़ने में त्रुटि: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}

// Fetch edit data if needed
$edit_data = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $conn->prepare("SELECT * FROM plays WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$csrf_token = generate_csrf_token();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 22px; margin-bottom: 4px;">नाटक प्रबंधन (Manage Plays & Dramas)</h1>
        <p style="color: #64748b; font-size: 13px;">हिंदी नाटक, एकांकी और संवाद रचनाओं का प्रबंधन करें।</p>
    </div>
    <?php if ($action === 'list'): ?>
        <a href="manage_plays.php?action=create" class="btn-action btn-approve" style="padding: 10px 18px; font-size: 14px;">+ नया नाटक जोड़ें</a>
    <?php else: ?>
        <a href="manage_plays.php" class="btn-action btn-secondary" style="padding: 10px 18px;">← सूची पर वापस जाएँ</a>
    <?php endif; ?>
</div>

<?php if (!empty($msg)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($action === 'create' || $action === 'edit'): ?>
    <div class="form-card">
        <h2 style="font-size: 18px; margin-bottom: 20px;"><?= $action === 'edit' ? 'नाटक संपादित करें' : 'नया नाटक जोड़ें' ?></h2>
        <form method="POST" action="manage_plays.php">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="edit_id" value="<?= $edit_data['id'] ?? 0 ?>">

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>नाटक का शीर्षक (Title) *</label>
                    <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($edit_data['title'] ?? '') ?>" placeholder="उदा. आषाढ़ का एक दिन">
                </div>
                <div class="form-group">
                    <label>नाटककार (Playwright/Author) *</label>
                    <input type="text" name="author_name" class="form-control" required value="<?= htmlspecialchars($edit_data['author_name'] ?? '') ?>" placeholder="उदा. मोहन राकेश">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>श्रेणी (Category)</label>
                    <select name="category" class="form-control">
                        <?php
                        $cats = ['ऐतिहासिक नाटक', 'गीतिनाट्य', 'एकांकी', 'सामाजिक नाटक', 'व्यंग्य', 'पौराणिक', 'सामान्य'];
                        $cur_cat = $edit_data['category'] ?? 'ऐतिहासिक नाटक';
                        foreach ($cats as $c): ?>
                            <option value="<?= $c ?>" <?= $cur_cat === $c ? 'selected' : '' ?>><?= $c ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>अंक / दृश्य संख्या (Acts Count)</label>
                    <input type="number" name="acts_count" class="form-control" value="<?= intval($edit_data['acts_count'] ?? 3) ?>" min="1">
                </div>
                <div class="form-group">
                    <label>कवर छवि यूआरएल (Image URL)</label>
                    <input type="text" name="image_url" class="form-control" value="<?= htmlspecialchars($edit_data['image_url'] ?? 'images/play-default.jpg') ?>">
                </div>
            </div>

            <div class="form-group">
                <label>संक्षिप्त सारांश (Excerpt/Plot Summary)</label>
                <textarea name="excerpt" class="form-control" rows="2" placeholder="कथानक का संक्षिप्त परिचय"><?= htmlspecialchars($edit_data['excerpt'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>नाटक के संवाद एवं दृश्य (Script/Dialogue Content) *</label>
                <textarea name="content" class="form-control" rows="16" required placeholder="पात्र, दृश्य निर्देश और संवाद यहाँ लिखें..."><?= htmlspecialchars($edit_data['content'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn-action btn-approve" style="padding: 12px 24px; font-size: 15px;">
                <?= $action === 'edit' ? 'अपडेट सहेजें' : 'प्रकाशित करें' ?>
            </button>
        </form>
    </div>
<?php else: ?>
    <?php
    $plays = $conn->query("SELECT * FROM plays ORDER BY created_at DESC");
    ?>
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th>शीर्षक</th>
                    <th>नाटककार</th>
                    <th>श्रेणी</th>
                    <th>अंक संख्या</th>
                    <th>पसंद / दर्शन</th>
                    <th>दिनांक</th>
                    <th style="text-align: right;">क्रियाएँ</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($plays && $plays->num_rows > 0): ?>
                    <?php while ($row = $plays->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><strong><?= htmlspecialchars($row['title']) ?></strong></td>
                            <td><?= htmlspecialchars($row['author_name']) ?></td>
                            <td><span class="badge" style="background:#f3e8ff; color:#6b21a8;"><?= htmlspecialchars($row['category'] ?? 'सामान्य') ?></span></td>
                            <td><?= $row['acts_count'] ?> अंक</td>
                            <td><?= $row['likes'] ?> पसंद &nbsp;|&nbsp; <?= $row['views'] ?> दर्शन</td>
                            <td><?= format_hindi_date($row['created_at']) ?></td>
                            <td style="text-align: right;">
                                <a href="../play_detail.php?id=<?= $row['id'] ?>" target="_blank" class="btn-action" style="background:#f1f5f9; color:#475569;" title="देखें">देखें</a>
                                <a href="manage_plays.php?action=edit&id=<?= $row['id'] ?>" class="btn-action btn-edit">संपादित करें</a>
                                <a href="manage_plays.php?action=delete&id=<?= $row['id'] ?>&csrf=<?= $csrf_token ?>" class="btn-action btn-delete" onclick="return confirm('क्या आप वाकई इस नाटक को हटाना चाहते हैं?')">हटाएँ</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="8" style="text-align:center; padding: 24px;">कोई नाटक उपलब्ध नहीं है।</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/footer.php'; ?>
